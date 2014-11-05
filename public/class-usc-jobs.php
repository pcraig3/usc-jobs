<?php
/**
 * USC Jobs is more or less a standard Custom Post Type. It creates the 'usc_jobs' Post Type, meant to act as a
 * scaffolding around jobs postings.  What it does is provides a consistent location for USC jobs to be posted, as well
 * as a standardized format for posting them.
 *
 * Uses the AdminPageFramework to create the admin page, and then uses filter.js to create a better archive page.
 *
 * @package   USC_Jobs
 * @author    Paul Craig <pcraig3@uwo.ca>
 * @license   GPL-2.0+
 * @copyright 2014
 */

/**
 * @package USC_Jobs
 * @author    Paul Craig <pcraig3@uwo.ca>
 */
class USC_Jobs {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.1.1
     *
     * @var     string
     */
    const VERSION = '1.1.1';

    /**
     *
     * Unique identifier for your plugin.
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    0.1.0
     *
     * @var      string
     */
    protected $plugin_slug = 'usc-jobs';

    /**
     * Instance of this class.
     *
     * @since    0.1.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * @since    0.8.0
     *
     * @var string      variable used so that the template-finding function works
     */
    protected $usc_jobs_dir =  '';

    /**
     * How many jobs to return.  I've set this absurdly high so that there's never a chance of needing to paginate.
     *
     * @since    0.8.1
     *
     * @var int
     */
    protected $number_of_usc_jobs_to_return_at_once = null;

    /**
     *  Customize the order by which for both the query and the JSON request return jobs.
     *  Default is to return by soonest 'apply_by_date'
     *
     * @since    0.8.1
     *
     * @var int
     */
    protected $order_by_usc_jobs = array();

    /**
     * @var  used for saving the default $wp_using_ext_object_cache so as not to bugger up our plugin
     *
     * @since    0.8.3
     */
    private $wp_using_ext_object_cache_status;

    /**
     * @var used for saving the default server timezone so that nothing odd happens with our time calculations
     *
     * @since    0.8.3
     */
    private $date_default_timezone_get_status;


    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since    0.9.0
     */
    private function __construct() {

        //exactly one up from this directory is the home directory of the plugin
        $this->usc_jobs_dir = trailingslashit( dirname( __DIR__ ) );

        //I've set this absurdly high so that there's never a chance of needing to paginate.
        $this->number_of_usc_jobs_to_return_at_once = 150;

        //we're returning by the earliest date by default
        //@see: http://codex.wordpress.org/Class_Reference/WP_Query
        $this->order_by_usc_jobs = array(
            'query' => array(
                'orderby',
                'meta_key',
                'order',
            ),
            'JSON' => array(
                'order_by',
                'meta_key',
                'order'
            ),
            'values' => array(
                'meta_value',
                'apply_by_date',
                'ASC',
            )
        );

        // Load plugin text domain
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        //department taxonomy doesn't load on 'init'
        //add_action( 'init', array( $this, 'add_jobs_post_type' ) );
        $this->add_jobs_post_type();

        //set our own template instead of 'index.php'
        add_filter( 'template_include', array( $this, 'usc_jobs_set_template' ) ) ;

        //register sidebars for jobs single and archive (even though the archive ones will get overwritten)
        add_action( 'widgets_init', array( $this, 'usc_jobs_register_sidebars' ) );

        //define the rewrite tag and a url pattern that triggers it
        add_action( 'init', array( $this, 'usc_jobs_rewrite_rules' ) );
        //add 'usc_jobs_remuneration' to our query variables
        add_action( 'init', array( $this, 'usc_jobs_add_remuneration' ) );

        //define what our query looks like when we call our custom url
        add_action( 'pre_get_posts', array( $this, 'usc_jobs_get_meta_remuneration' ) );

        //call it LATER than the other one so that you overwrite its value
        add_action( 'pre_get_posts', array( $this, 'usc_jobs_increase_posts_per_page_order_by_apply_date' ), 24);

        //add filter_js scripts if post_archive of usc jobs.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_filter_js_scripts' ) );

        /*
         * set an initial value to this variable, in case we call the method to overwrite the global value before we
         * call the method to store it
         */
        global $_wp_using_ext_object_cache;

        $this->wp_using_ext_object_cache_status = $_wp_using_ext_object_cache;

        //set a default timezone so that we don't accidentally call the wrong method first and overwrite anything
        $this->date_default_timezone_get_status = date_default_timezone_get();
    }

    /**
     * one of two functions created to get around a bug with the APC backend object-caching plugin
     * Basically, our APC caching backend plugin was setting $_wp_using_ext_object_cache to true, with the
     * unintended side-effect that any time we saved a transient, it wouldn't persist through the next pageload.
     *
     * So this function sets $wp_using_ext_object_cache_status to false so that setting a transient will work
     *
     * More detailed discussion here:
     * @see: https://github.com/michaeluno/admin-page-framework/issues/118
     *
     * @since   0.8.3
     */
    public function turn_off_object_cache_so_our_bloody_plugin_works() {
        global $_wp_using_ext_object_cache;

        $this->wp_using_ext_object_cache_status = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;
    }

    /**
     * one of two functions created to get around a bug with the APC backend object-caching plugin
     * Basically, our APC caching backend plugin was setting $_wp_using_ext_object_cache to true, with the
     * unintended side-effect that any time we saved a transient, it wouldn't persist through the next pageload.
     *
     * So this function assumes the 'turn_off_object_cache_so_our_bloody_plugin_works' was called first,
     * sets the $wp_using_ext_object_cache_status back to its original value
     *
     * More detailed discussion here:
     * @see: https://github.com/michaeluno/admin-page-framework/issues/118
     *
     * @since   0.8.3
     */
    public function turn_object_caching_back_on_for_the_next_poor_sod() {
        global $_wp_using_ext_object_cache;

        $_wp_using_ext_object_cache = $this->wp_using_ext_object_cache_status;
    }

    /**
     * function sets the default timezone to America/Toronto because that's where #westernu is.
     *
     * @since 0.8.3
     */
    public function set_server_to_local_time() {

        $this->date_default_timezone_get_status = date_default_timezone_get();
        date_default_timezone_set("America/Toronto");
    }

    /**
     * function resets the server timezone back to whatever it was before calling 'set_server_to_local_time'
     *
     * @since 0.8.3
     */
    public function set_server_back_to_default_time() {

        date_default_timezone_set($this->date_default_timezone_get_status);
    }

    /**
     * This function formats job posts
     * returned from the Query instead of formatting Job posts returned by an API.
     *
     * Sets up an array to return to filterjs so that we can create our dynamic archive page.
     * Also sets a sequential id to all jobs.
     *
     * @since   0.8.3
     *
     * @param null $posts   the usc_jobs posts returned by the main query.
     * @return array|null   array of jobs formatted in a way filterjs understands
     */
    private function filter_js_format_query_response( $posts = null ) {

        $posts_for_filterjs = array();

        if ( null === ( $posts ) || empty( $posts ) ) {

            return $posts;

        } else {

            /**
             * Here's the simplified version of a post
            ID=>                int
            post_author=>       string
            post_date=>         string
            post_date_gmt=>     string
            post_content=>      string
            post_title=>        string
            post_excerpt=>      string
            post_status=>       string
            comment_status=>    string
            ping_status=>       string
            post_password=>     string
            post_name=>         string
            to_ping=>           string
            pinged=>            string
            post_modified=>     string
            post_modified_gmt=> string
            post_content_filtered=> string
            post_parent=>       int
            guid=>              string
            menu_order=>        int
            post_type=>         string
            post_mime_type=>    string
            comment_count=>     string
            filter=>            string
             */

            $temp_post = array();

            $author_fields = array(
                'id' => 'ID',
                'slug' => 'user_nicename',
                'name' => 'user_login',
                'first_name' => 'first_name',
                'last_name' => 'last_name',
                'nickname' => 'nickname',
                'url' => 'user_url',
                'description' => 'description'
            );

            $custom_fields = array(
                'apply_by_date',
                'remuneration',
                'position',
                'application_link',
                'job_posting_file',
                'job_description_file',
                'contact_information'
            );

            foreach( $posts as $num => $post ) {

                if('publish' === $post->post_status) {

                    $temp_post['id'] = $num + 1; //filter_js needs sequential id numbers
                    $temp_post['wp_id'] = $post->ID;

                    //type
                    $temp_post['type'] = $post->post_type;
                    //slug
                    $temp_post['slug'] = $post->post_name;
                    //url
                    $temp_post['url'] = $post->guid;
                    //title
                    $temp_post['title'] = $post->post_title;
                    //title plain
                    $temp_post['title_plain'] = $post->post_title;
                    //date
                    $temp_post['date'] = $post->post_date;
                    //modified
                    $temp_post['modified'] = $post->post_modified;
                    //author
                    $author  = $post->post_author;

                    foreach($author_fields as $author_field_for_filter_js => $wp_query_author_field)
                        $temp_post['author'][$author_field_for_filter_js] = get_the_author_meta( $wp_query_author_field, $author );


                    //custom_fields
                    $custom_fields = get_post_meta($post->ID);

                    foreach( $custom_fields as $key => $value ) {

                        if( substr( $key, 0, 1 ) !== '_'){

                            $temp_post['custom_fields'][$key] = array_shift($value);
                        }
                    }

                    //now the taxonomies.
                    $temp_post['taxonomy_departments'] =  json_decode(json_encode(wp_get_post_terms($post->ID, 'departments')), true);

                    foreach($temp_post['taxonomy_departments'] as &$taxonomy_department) {

                        $taxonomy_department['title'] = $taxonomy_department['name'];
                        $taxonomy_department['id'] = $taxonomy_department['term_id'];
                    }

                    $posts[$num] = $temp_post;

                }
                else
                    //if not published, remove the post
                    unset($posts[$num]);
            }

            //array_values in case any posts were removed.
            $posts_for_filterjs = array_values($posts);

        } // end if/else

        return ( is_null($posts_for_filterjs) ) ? $posts : $posts_for_filterjs;
    }

    /**
     * function hijacks the main query if we're on the usc_jobs post archive
     *
     * 1. Changes Posts per Page to an impossibly high number.
     * 2. Orders posts by their apply-by-date
     * 3. Removes all posts whose apply-by-dates are before today at 11pm
     *
     * @influence Bill Erickson
     * @see: http://www.billerickson.net/customize-the-wordpress-query/
     *
     * @since   1.1.1
     *
     * @param object $query data    the query object just before it hits the WordPress database
     */
    public function usc_jobs_increase_posts_per_page_order_by_apply_date( $query ) {

        if( $query->is_main_query() && !$query->is_feed() && !is_admin()
            && ( is_post_type_archive( 'usc_jobs' )  || ( is_tax('departments') ) ) ) {

            $query->set( 'posts_per_page', $this->number_of_usc_jobs_to_return_at_once );

            /* order by apply_by_date */
            foreach($this->order_by_usc_jobs['query'] as $key => $value) {

                $query->set($value, $this->order_by_usc_jobs['values'][$key]);
            }

            //first, get 11:00pm today
            $this->set_server_to_local_time();
            //this is what these strings look like : "2014-08-22 05:00"

            $one_minute_past_midnight_today = date( 'Y-m-d' ) . ' 00:01';

            $this->set_server_back_to_default_time();

            /* remove jobs whose apply_by_dates are equal or greater than today at 1 minute past midnight */
            $query->set('meta_query', array(
                array(
                    'key'     => 'apply_by_date',
                    'value'   => $one_minute_past_midnight_today, // A value must exist due to https://core.trac.wordpress.org/ticket/23268
                    'compare' => '>',
                    )
                )
            );

        }
    }

    /**
     * Looks for the 'usc_jobs_remuneration' query_var, and, if found, adds a meta query to the main query.
     *
     * @influence Bill Erickson
     * @see: http://www.billerickson.net/customize-the-wordpress-query/
     *
     * @since    0.4.5
     *
     * @param object $query data
     */
    public function usc_jobs_get_meta_remuneration( $query ) {

        $remuneration = get_query_var('usc_jobs_remuneration');

        if( !empty($remuneration) && $query->is_main_query() && !$query->is_feed()
            && !is_admin() && is_post_type_archive( 'usc_jobs' ) ) {
            $meta_query = array(
                array(
                    'key' => 'remuneration',
                    'value' => $remuneration,
                    'compare' => '='
                )
            );

            $query->set( 'meta_query', $meta_query );
        }
    }

    /**
     * Function adds the 'usc_jobs_remuneration' parameter to the query variables, as WordPress calls them.
     * If the function below this one describes the pattern in which 'usc_jobs_remuneration' should be used,
     * this is the function that registers the name of the variable with WordPress
       * more information here:
     * @see: http://wordpress.stackexchange.com/questions/71305/when-should-add-rewrite-tag-be-used
     *
     * @since    0.4.5
     */
    public function usc_jobs_add_remuneration() {

        global $wp;

        $wp->add_query_var('usc_jobs_remuneration');
    }

    /**
     * Static function sets the rewrite rules for archives of USC Jobs based on the 'remuneration' meta value.
     * The idea is that we should be able to generate a specific archive if the url contains a useful 'remuneration' value
     * Function is static so that it can be called on plugin activation.
     *
     * @since    0.6.1
     */
    public static function usc_jobs_rewrite_rules() {

        // Custom tag we will be using to recognize page requests
        add_rewrite_tag('%usc_jobs_remuneration%','([^/]+)');

        // Custom rewrite rule to hijack page generation
        add_rewrite_rule('jobs/remuneration/([^/]+)/?$','index.php?post_type=usc_jobs&usc_jobs_remuneration=$matches[1]','top');
    }

    /**
     * Register Sidebars.  Creates one for the single jobs and one for the jobs archive.
     *
     * http://devotepress.com/wordpress-coding/how-to-register-sidebars-in-wordpress/#.U-MXWxa-MUY
     */
    public function usc_jobs_register_sidebars() {

        /* Register the usc jobs archive sidebar. */
        register_sidebar(
            array(
                'id' => 'usc_jobs_archive_sidebar',
                'name' => __( 'USC Jobs Archive Sidebar', 'usc-jobs' ),
                'description' => __( 'Only found on USC Jobs archives.', 'usc-jobs' ),
                'before_widget' => '<aside id="%1$s" class="et_pb_widget %2$s">',
                'after_widget' => '</aside>',
                'before_title' => '<h4 class="widgettitle">',
                'after_title' => '</h4>'
            )
        );

        /* Register the usc jobs single sidebar. */
        register_sidebar(
            array(
                'id' => 'usc_jobs_single_sidebar',
                'name' => __( 'USC Job Single Sidebar', 'usc-jobs' ),
                'description' => __( 'Widgets only meant for individual USC Jobs Posts.', 'usc-jobs' ),
                'before_widget' => '<aside id="%1$s" class="et_pb_widget %2$s">',
                'after_widget' => '</aside>',
                'before_title' => '<h4 class="widgettitle">',
                'after_title' => '</h4>'
            )
        );
    }

    /**
     * Creates a new Job Post Type.  You should apply.
     *
     * Needs the AdminPageFramework library in order to work.
     *
     * @since 0.4.0
     */
    public function add_jobs_post_type() {

        if ( ! class_exists( 'AdminPageFramework' ) )
            include_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin-page-framework/library/admin-page-framework.min.php' );

        include_once('USC_Job_PostType.php');
        new USCJob_PostType( 'usc_jobs' );
        /* Look in admin-usc-jobs for the rest of the USC_Jobs stuff */

    }

    /**
     * Checks if provided template path points to a 'usc_jobs' template recognised by our humble little plugin.
     * If no usc_jobs-archive template is present the plug-in will pick the most appropriate
     * option, first from the theme/child-theme directory then the plugin.
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L153
     * @author  Stephen Harris
     *
     * @since    0.8.1
     *
     * @param string    $templatePath absolute path to template or filename (with .php extension)
     * @param string    $context What the template is for ('usc_jobs','archive-usc_jobs', etc).
     * @return bool     return true if template is recognised as an 'event' template. False otherwise.
     */
    private function usc_jobs_is_job_template($templatePath,$context=''){

        $template = basename($templatePath);

        switch($context):
            case 'usc_jobs';
                return $template === 'single-usc_jobs.php';

            case 'archive':
                return $template === 'archive-usc_jobs.php';

            case 'departments':
                return $template === 'taxonomy-departments.php';

        endswitch;

        return false;
    }

    /**
     * Checks to see if appropriate templates are present in active template directory.
     * Otherwise uses templates present in plugin's template directory.
     * Hooked onto template_include
     *
     * **THIS MEANS THAT IF YOU WANT A CHANGE TO A TEMPLATE TO PROPAGATE, MAKE THE CHANGE TO THE TEMPLATE IN THE
     * THEMES FOLDER, NOT THE TEMPLATE FILE IN THE FOLDER FOR THIS PLUGIN**
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L192
     * @author  Stephen Harris
     *
     * @since    0.8.1
     *
     * @param string $template Absolute path to template
     * @return string Absolute path to template
     */
    public function usc_jobs_set_template( $template ) {

        //If WordPress couldn't find a 'usc_jobs' archive template use plug-in instead:

        if( is_post_type_archive( 'usc_jobs' ) && ! $this->usc_jobs_is_job_template( $template, 'archive' ) )
            $template = $this->usc_jobs_dir . 'templates/archive-usc_jobs_westernusc.php';

        if( ( is_tax('departments') ) && ! $this->usc_jobs_is_job_template( $template,'departments' ) )
            $template = $this->usc_jobs_dir . 'templates/archive-usc_jobs_westernusc.php';

        /*
        * In view of theme compatibility, if an event template isn't found
        * rather than using our own (hypothetical) single-usc_jobs.php, we use ordinary single.php and
        * add content in via the_content
        */
        if( is_singular( 'usc_jobs' ) && ! $this->usc_jobs_is_job_template( $template,'usc_jobs' ) ){
            //Viewing a single usc_jobs

            //Hide next/previous post link
            add_filter("next_post_link",'__return_false');
            add_filter("previous_post_link",'__return_false');

            //Prepend our event details
            add_filter('the_content', array( $this, '_usc_jobs_single_job_content' ) );
            //$template = $this->usc_jobs_dir . 'templates/single-usc_jobs.php';
        }

        return $template;
    }

    /**
     * Function triggered by the the_content filter for our usc_jobs single post type.
     * The idea here is that you can just inject whatever you want into the single-usc_jobs.php template that the theme uses
     * and that way not muck everything up.
     *
     * **NOT USING THIS FUNCTION CURRENTLY**
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L243
     * @author  Stephen Harris
     *
     * @since 0.4.3
     *
     * @param $content
     * @return string
     */
    public function _usc_jobs_single_job_content( $content ){

        //Sanity check!
        if( !is_singular('usc_jobs') )
            return $content;

        //Object buffering
        ob_start();
        $this->usc_jobs_get_template_part('usc_jobs-meta','usc_jobs-single');

        $usc_jobs_content = ob_get_contents();
        ob_end_clean();

        //filter that someone someday might latch on to.  Probably not though.
        $usc_jobs_content = apply_filters('usc_jobs_pre_usc_jobs_content', $usc_jobs_content, $content);

        return $usc_jobs_content.$content;
    }

    /**
     * Load a template part into a template
     *
     * Identical to {@see `get_template_part()`} except that it uses {@see `usc_jobs_locate_template()`}
     * instead of {@see `locate_template()`}.
     *
     * Makes it easy for a theme to reuse sections of code in a easy to overload way
     * for child themes. Looks for and includes templates {$slug}-{$name}.php
     *
     * You may include the same template part multiple times.
     *
     * **NOT USING THIS FUNCTION CURRENTLY**
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L7
     * @author  Stephen Harris
     *
     * @since 0.4.3
     *
     * @uses usc_jobs_locate_template()
     * @uses do_action() Calls `get_template_part_{$slug}` action.
     *
     * @param string $slug The slug name for the generic template.
     * @param string $name The name of the specialised template.
     */
    private function usc_jobs_get_template_part( $slug, $name = null ) {
        do_action( "get_template_part_{$slug}", $slug, $name );

        $templates = array();
        if ( isset($name) )
            $templates[] = "{$slug}-{$name}.php";

        $templates[] = "{$slug}.php";

        $this->usc_jobs_locate_template($templates, true, false);
    }

    /**
     * Retrieve the name of the highest priority template file that exists.
     *
     * Searches the child theme first, then the parent theme before checking the plug-in templates folder.
     * So parent themes can override the default plug-in templates, and child themes can over-ride both.
     *
     * Behaves almost identically to {@see locate_template()}
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L38
     * @author  Stephen Harris
     *
     * @since 0.4.3
     *
     * @param string|array $template_names Template file(s) to search for, in order.
     * @param bool $load If true the template file will be loaded if it is found.
     * @param bool $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
     * @return string The template filename if one is located.
     */
    private function usc_jobs_locate_template($template_names, $load = false, $require_once = true ) {
        $located = '';

        $template_dir = get_stylesheet_directory(); //child theme
        $parent_template_dir = get_template_directory(); //parent theme

        $stack = apply_filters( 'usc_jobs_template_stack', array( $template_dir, $parent_template_dir, $this->usc_jobs_dir . 'templates' ) );

        foreach ( (array) $template_names as $template_name ) {
            if ( !$template_name )
                continue;

            foreach ( $stack as $template_stack ){

                if ( file_exists( trailingslashit( $template_stack ) . $template_name ) ) {
                    $located = trailingslashit( $template_stack ) . $template_name;
                    break;
                }
            }
        }


        if ( $load && '' !== $located )
            load_template( $located, $require_once );

        return $located;
    }

    /**
     * Return the plugin slug.
     *
     * @since    0.1.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.1.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide  ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_activate();

                    restore_current_blog();
                }

            } else {
                self::single_activate();
            }

        } else {
            self::single_activate();
        }

    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_deactivate();

                    restore_current_blog();

                }

            } else {
                self::single_deactivate();
            }

        } else {
            self::single_deactivate();
        }

    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    0.1.0
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site( $blog_id ) {

        if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
            return;
        }

        switch_to_blog( $blog_id );
        self::single_activate();
        restore_current_blog();

    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    0.1.0
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

        return $wpdb->get_col( $sql );

    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    0.4.5
     */
    private static function single_activate() {

        self::usc_jobs_rewrite_rules();

        // flush rewrite rules - only do this on activation as anything more frequent is bad!
        flush_rewrite_rules();
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    0.4.5
     */
    private static function single_deactivate() {

        // flush rules on deactivate as well so they're not left hanging around uselessly
        flush_rewrite_rules();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.1.0
     */
    public function load_plugin_textdomain() {

        $domain = $this->plugin_slug;
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    0.1.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
        wp_enqueue_script( 'jquery-ui-datepicker' );

    }

    /**
     * Register and enqueues public-facing JavaScript files relating to filter_js
     * Also, get the usc_jobs data from the main query and feed it to filter_js
     *
     * @since   0.8.3
     */
    public function enqueue_filter_js_scripts() {

        global $wp_query;

        if( $wp_query->is_main_query() && ( is_post_type_archive( 'usc_jobs' ) || is_tax( 'departments' ) ) ) {

            /* This would get an array of usc_jobs from our backend JSON API.
            However, it's quicker and more up-to-date (though significantly less elegant), to get jobs from the wp_query object,
            so unfortunately this clever little programming construct was abandoned */
            //$usc_jobs_as_json_array = $this->filter_js_format_API_response( $this->HTTP_GET_usc_jobs() );

            $posts = $wp_query->posts;
            $usc_jobs_as_json_array = $this->filter_js_format_query_response($posts);

            wp_enqueue_script( 'tinysort', plugins_url( '/bower_components/tinysort/dist/jquery.tinysort.min.js', __DIR__ ), array( 'jquery' ), self::VERSION );

            //<soops h4ck> disable jQuery.noConflict for the length of filter.js
            wp_enqueue_script( 'jquery_no_conflict_disable', plugins_url( '/assets/js/jquery-no-conflict-disable.js', __FILE__ ), array( 'jquery', 'tinysort' ), self::VERSION );
            wp_enqueue_script( 'filterjs', plugins_url( '/assets/js/filter.js', __FILE__ ), array( 'jquery', 'tinysort', 'jquery-ui-core', 'jquery_no_conflict_disable' ), self::VERSION );
            //wp_enqueue_script( 'filterjs', "https://raw.githubusercontent.com/jiren/filter.js/master/filter.js", array( 'jquery', 'tinysort', 'jquery-ui-core', 'jquery_no_conflict_disable' ), self::VERSION );

            wp_enqueue_script( 'public_filterjs', plugins_url( '/assets/js/public-filter.js', __FILE__ ), array( 'jquery', 'tinysort', 'jquery-ui-core', 'filterjs' ), self::VERSION );

            // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
            wp_localize_script( 'public_filterjs', "options", array(
                'jobs'  => json_encode($usc_jobs_as_json_array),
            ) );

        }
    }

    /**
     * NOTE:  Actions are points in the execution of a page or process
     *        lifecycle that WordPress fires.
     *
     *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    0.1.0
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:  Filters are points of execution in which WordPress modifies data
     *        before saving it or sending it to the browser.
     *
     *        Filters: http://codex.wordpress.org/Plugin_API#Filters
     *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    0.1.0
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
    }
}
