<?php
/**
 * USC Jobs.
 *
 * @package   USC_Jobs
 * @author    Paul Craig <pcraig3@uwo.ca>
 * @license   GPL-2.0+
 * @copyright 2014
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-usc-jobs-admin.php`
 *
 * @package USC_Jobs
 * @author    Paul Craig <pcraig3@uwo.ca>
 */
class USC_Jobs {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.3.0
     *
     * @var     string
     */
    const VERSION = '0.3.0';

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
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     0.2.0
     */
    private function __construct() {

        // Load plugin text domain
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        /* Define custom functionality.
         * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         */
        //add_action( '@TODO', array( $this, 'action_method_name' ) );
        //add_filter( '@TODO', array( $this, 'filter_method_name' ) );

        $this->add_jobs_post_type();

        add_filter( 'template_include', array( $this, 'usc_jobs_set_template' ) ) ;

    }

    /**
     * Creates a new Job Post Type.  You should apply.
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
     * If no usc_jobs-archive tempate is present the plug-in will pick the most appropriate
     * option, first from the theme/child-theme directory then the plugin.
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L153
     * @author  Stephen Harris
     *
     * @since 0.4.2
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

        endswitch;

        return false;
    }

    /**
     * Checks to see if appropriate templates are present in active template directory.
     * Otherwises uses templates present in plugin's template directory.
     * Hooked onto template_include'
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L192
     * @author  Stephen Harris
     *
     * @since 0.4.3
     *
     * @param string $template Absolute path to template
     * @return string Absolute path to template
     */
    public function usc_jobs_set_template( $template ){

        $plugin_dir = trailingslashit( dirname( __DIR__ ) );

        //If WordPress couldn't find a 'usc_jobs' archive template use plug-in instead:

        if( is_post_type_archive( 'usc_jobs' ) && ! $this->usc_jobs_is_job_template( $template, 'archive' ) )
            $template = $plugin_dir . 'templates/archive-usc_jobs.php';

        /*
        * In view of theme compatibility, if an event template isn't found
        * rather than using our own single-usc_jobs.php, we use ordinary single.php and
        * add content in via the_content
        */
        if( is_singular( 'usc_jobs' ) && ! $this->usc_jobs_is_job_template( $template,'usc_jobs' ) ){
            //Viewing a single usc_jobs

            //Hide next/previous post link
            add_filter("next_post_link",'__return_false');
            add_filter("previous_post_link",'__return_false');

            //Prepend our event details
            add_filter('the_content', array( $this, '_usc_jobs_single_event_content' ) );
        }

        return $template;
    }

    /**
     * Function triggered by the the_content filter for our usc_jobs single post type.
     * The idea here is that you can just inject whatever you want into the single.php template that the theme uses
     * and that way not muck everything up.
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L243
     * @author  Stephen Harris
     *
     * @since 0.4.3
     *
     * @param $content
     * @return string
     */
    public function _usc_jobs_single_event_content( $content ){

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

        $stack = apply_filters( 'usc_jobs_template_stack', array( $template_dir, $parent_template_dir, trailingslashit( dirname( __DIR__ ) ) . 'templates' ) );

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
     * @since    0.1.0
     */
    private static function single_activate() {
        // @TODO: Define activation functionality here
        flush_rewrite_rules();
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    0.1.0
     */
    private static function single_deactivate() {
        // @TODO: Define deactivation functionality here
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
