<?php
/**
 * USCJob_PostType extends the AdminPageFramework_PostType class and creates a 'usc_jobs' as a WordPress Custom Post Type
 * Also associates the 'Departments' taxonomy with usc_jobs.
 */

class USCJob_PostType extends AdminPageFramework_PostType {

    /**
     * This method is called at the end of the constructor.
     *
     * Alternatively, you may use the start_{extended class name} method, which also is called at the end of the constructor.
     */
    public function start() {

        $this->setAutoSave( false );
        $this->setAuthorTableFilter( true );

        $this->setPostTypeArgs(
            array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels' => array(
                    'name'			=>	'Jobs',
                    'all_items' 	=>	__( 'All Jobs', 'usc-jobs' ),
                    'singular_name' =>	'Job',
                    'add_new'		=>	__( 'Add New', 'usc-jobs' ),
                    'add_new_item'	=>	__( 'Add New Job', 'usc-jobs' ),
                    'edit'			=>	__( 'Edit', 'usc-jobs' ),
                    'edit_item'		=>	__( 'Edit Job', 'usc-jobs' ),
                    'new_item'		=>	__( 'New Job', 'usc-jobs' ),
                    'view'			=>	__( 'View', 'usc-jobs' ),
                    'view_item'		=>	__( 'View Job', 'usc-jobs' ),
                    'search_items'	=>	__( 'Search Jobs', 'usc-jobs' ),
                    'not_found'		=>	__( 'No Jobs found', 'usc-jobs' ),
                    'not_found_in_trash' => __( 'No Jobs found in Trash', 'usc-jobs' ),
                    //'parent'		=>	__( 'Parent APF Post', 'usc-jobs' ),
                    'plugin_listing_table_title_cell_link'	=>	__( 'Jobs', 'usc-jobs' ),		// framework specific key. [3.0.6+]
                ),
                'public'			=>	true,
                'description'       =>  'USC Job postings. Yay!',
                'menu_position' 	=>	5,  //below Posts
                'supports'			=>	array( 'title', 'editor' ), // 'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),	// 'custom-fields'
                'taxonomies'		=>	array( '' ),
                'has_archive'		=>	true,
                'show_admin_column' =>	true,	// this is for custom taxonomies to automatically add the column in the listing table.
                'menu_icon'			=>	'dashicons-hammer',
                'rewrite'           => array( 'slug' => 'jobs/list','with_front' => false ),
                // ( framework specific key ) this sets the screen icon for the post type for WordPress v3.7.1 or below.
                'screen_icon'		=>	'http://testwestern.com/wp-content/plugins/admin-page-framework/asset/image/wp_logo_bw_32x32.png'
            )
        );

        // the setUp() method is too late to add taxonomies. So we use start_{class name} action hook.

        $this->addTaxonomy(
            'departments', // taxonomy slug
            array(			// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
                'labels' => array(
                    'name' => 'Departments',
                    'singular_name' => 'Department',
                    'add_new_item' => 'Add New Department',
                    'new_item_name' => 'New Department'
                ),
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'rewrite' => array( 'with_front' => false ),
                'show_table_filter' => true,	// framework specific key
                'show_in_sidebar_menus' => true,	// framework specific key
            )
        );

        add_filter( 'the_content', array( $this, 'replyToPrintOptionValues' ) );

        //add_filter( 'request', array( $this, 'replyToSortCustomColumn' ) );

    }

    /*
     * Method I've largely tried to circumvent.  Not really using it.
     *
     */
    public function columns_usc_jobs( $aHeaderColumns ) {	// columns_{post type slug}

        return array_merge(
            $aHeaderColumns,
            array(
                'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions.
                'title'			=> __( 'Title', 'usc-jobs' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
                'author'		=> __( 'Author', 'usc-jobs' ),		// Post author.
                // 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to.
                // 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post.
                'date'			=> __( 'Date', 'usc-jobs' ), 	// The date and publish status of the post.
                //'samplecolumn'			=> __( 'Sample Column' ),
            )
        );

    }


    /*public function sortable_columns_usc_jobs( $aSortableHeaderColumns ) {	// sortable_columns_{post type slug}
        return $aSortableHeaderColumns + array(
            'samplecolumn' => 'samplecolumn',
        );
    }*/
    /*public function cell_usc_jobs_samplecolumn( $sCell, $iPostID ) {	// cell_{post type}_{column key}

        return sprintf( __( 'Post ID: %1$s', 'usc-jobs' ), $iPostID ) . "<br />"
        . __( 'Text', 'usc-jobs' ) . ': ' . get_post_meta( $iPostID, 'metabox_text_field', true );

    }*/

    /**
     * Custom callback methods
     */

    /**
     * Modifies the way how the sample column is sorted. This makes it sorted by post ID.
     *
     * @see			http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
     *
    public function replyToSortCustomColumn( $aVars ){

    if ( isset( $aVars['orderby'] ) && 'samplecolumn' == $aVars['orderby'] ){
    $aVars = array_merge(
    $aVars,
    array(
    'meta_key'	=>	'metabox_text_field',
    'orderby'	=>	'meta_value',
    )
    );
    }
    return $aVars;
    }

    /**
     * If we wanted, we could define the 'usc_jobs' template here instead of having a template file.
     *
     * But I didn't.  I've come up with a template file called usc_jobs-single, and so that's where to make the changes
     * to the layout of a USC Job.
     */
    public function replyToPrintOptionValues( $sContent ) {

        return $sContent;

        if ( ! isset( $GLOBALS['post']->ID ) || get_post_type() !== 'usc_jobs' ) return $sContent;

        // 1. To retrieve the meta box data	- get_post_meta( $post->ID ) will return an array of all the meta field values.
        // or if you know the field id of the value you want, you can do $value = get_post_meta( $post->ID, $field_id, true );
        /*
         *
         Possible Meta Values (useless ones are indented)

                [_edit_last] => 1
            [apply_by_date] => 2014-08-13 12:00
            [remuneration] => volunteer
            [application_link] => http://33.media.tumblr.com/2d95777547966a733ccdfb3e34afaacc/tumblr_n55qheEABg1qlka8ko1_400.gif
            [job_posting_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
            [job_description_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
            [contact_information] => email@westernusc.ca
                [_edit_lock] => 1407311587:1
         */

        $iPostID = $GLOBALS['post']->ID;

        $aPostData = get_post_custom($iPostID);

        $html_string = "";

        $subhead = "";

        foreach( $aPostData as $key => $value ) {

            //anything starting with an underscore we don't want.
            if( ! (substr($key, 0, 1) === '_') ) {

                //get the first item of the array
                //var_dump($key . ' => '. $value);
                $value = (string) array_shift($value);

                //get a better title
                $subhead = ucwords(str_replace("_", " ", $key));

                if( filter_var( $value, FILTER_VALIDATE_URL )  ) { //test for a url

                    $html_string .= '<a href="' . esc_url($value) . '" title="click me!"><h3>' . __( $subhead , 'usc-jobs') . '</h3></a>';
                }
                else {

                    $html_string .= '<h3>' . __( $subhead , 'usc-jobs') . '</h3>'
                        . '<p>' . $value . '</p>';
                }
            }
        }

        return $html_string;
    }
}