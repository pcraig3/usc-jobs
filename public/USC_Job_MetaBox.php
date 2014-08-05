<?php
class USC_Job_MetaBox extends AdminPageFramework_MetaBox {

    /**
     * Framework method that registers all of the DateTime fields we need
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.3.0
     */
    public function start_USC_Job_MetaBox() { // start_{extended class name} - this method gets automatically triggered at the end of the class constructor.

        /*
         * Register custom field types.
         */

        /* 1. Include the file that defines the custom field type. */
        $aFiles = array(

            dirname( __FILE__ ) . '/custom-fields/event-modify-custom-field-type/EventModifyCustomFieldType.php',
            dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/DateCustomFieldType.php',
            dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/TimeCustomFieldType.php',
            dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
        );

        foreach( $aFiles as $sFilePath )
            if ( file_exists( $sFilePath ) ) include_once( $sFilePath );

        /* 2. Instantiate the classes  */
        $sClassName = get_class( $this );
        new EventModifyCustomFieldType( $sClassName );
        new DateCustomFieldType( $sClassName );
        new TimeCustomFieldType( $sClassName );
        new DateTimeCustomFieldType( $sClassName );

    }

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds a contextual help pane at the top right of the page that the meta box resides.
         */
        $this->addHelpText(
            __( 'This text will DANCE in the contextual help pane.', 'admin-page-framework-demo' ),
            __( 'This description LAZES in the sidebar of the help pane.', 'admin-page-framework-demo' )
        );

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'		=> 'job_description',
                'type'			=> 'textarea',
                'title'			=> __( 'Job Description', 'usc-jobs' ),
                'description'	=> __( 'Write a short description of the job here.', 'usc-jobs' ),
                'help'			=> __( 'Write a short description of the job here.', 'usc-jobs' ),
                //'default'		=> __( 'This is a default text value.', 'admin-page-framework-demo' ),
                'attributes'	=>	array(
                    'cols'	=>	40,
                ),
            ),
            array(	// date picker
                'field_id'	    =>	'apply_by_date',
                'title'	        =>	__( 'Apply-by Date', 'usc-jobs'),
                'description'	=>	__( 'Candidates must have their applications in by this date.', 'usc-jobs' ),
                'description'	=>	__( 'Candidates must have their applications in by this date.', 'usc-jobs' ),
                'type'          =>  'date_time',
                'date_format'	=>	'yy-mm-dd',
                'time_format'	=>  'HH:mm',
                'size'          =>  '40',
            ),
            array (
                'field_id'		=> 'renumeration',
                'type'			=> 'radio',
                'title'			=> __( 'Renumeration Expected', 'usc-jobs' ),
                'description'	=> __( 'Is this a paid position, a volunteer position, or an internship?', 'usc-jobs' ),
                'help'	        => __( 'Is this a paid position, a volunteer position, or an internship?', 'usc-jobs' ),
                'label' => array(
                    'volunteer' => __( 'Volunteer', 'usc-jobs' ),
                    'paid' => __( 'Paid', 'usc-jobs' ),
                    'internship' => __( 'Internship', 'usc-jobs' ),
                ),
                'default' => 'volunteer',
            ),
            array (
                'field_id'		=> 'position',
                'type'			=> 'radio',
                'title'			=> __( 'Position', 'usc-jobs' ),
                'description'	=> __( 'What kind of position this job is for.', 'usc-jobs' ),
                'help'	        => __( 'What kind of position this job is for.', 'usc-jobs' ),
                'label' => array(
                    'ft_permanent'  => __( 'Full-Time Permanent', 'usc-jobs' ),
                    'ft_contract'   => __( 'Full-Time Contract', 'usc-jobs' ),
                    'pt_permanent'  => __( 'Part-Time Permanent', 'usc-jobs' ),
                    'pt_contract'   => __( 'Part-Time Contract', 'usc-jobs' ),
                    'honourarium'   => __( 'Honourarium', 'usc-jobs' ),
                ),
                'default' => 'volunteer',
            ),
            array(
                'field_id'		=> 'application_link',
                'type'			=> 'text',
                'title'			=> __( 'Application Link', 'usc-jobs' ),
                'description'	=> __( 'Link to the application form.', 'usc-jobs' ),
                'help'			=> __( 'Link to the application form (offsite).', 'usc-jobs' ),
            ),
            array( // Single File Upload Field
                'field_id'		=>	'pdf_posting',
                'title'			=>	__( 'Job Posting (PDF File)', 'usc-jobs' ),
                'type'			=>	'file',
                'description'	=>	__( 'Upload the job posting PDF file.', 'usc-jobs' ),
                'help'	        =>	__( 'Upload the job posting PDF file.', 'usc-jobs' ),
            ),
            array(
                'field_id'		=>	'pdf_description',
                'title'			=>	__( 'Job Description (PDF File)', 'usc-jobs' ),
                'type'			=>	'file',
                'description'	=>	__( 'Upload the job description PDF file (optional).', 'usc-jobs' ),
                'help'	        =>	__( 'Upload the job description PDF file (optional).', 'usc-jobs' ),
                'attributes'	=>	array(
                    'data-nonce'	=>	wp_create_nonce('pdf_description_nonce'),
                    //'style'	=>	'background-color: #C8AEFF;',
                ),
            ),
            array(
                'field_id'		=> 'contact_information',
                'type'			=> 'textarea',
                'title'			=> __( 'Contact Information Description', 'usc-jobs' ),
                'description'	=> __( 'Who to contact for more information.  Can be just an email, or a name and phone number, etc. ', 'usc-jobs' ),
                'help'	        => __( 'Who to contact for more information.  Can be just an email, or a name and phone number, etc. ', 'usc-jobs' ),
                'default'		=> __( 'usc.jobs@westernusc.ca', 'usc-jobs' ),
                'attributes'	=>	array(
                    'cols'	=>	40,
                ),
            )
        );

        $this->addSettingFields(
            array (
                'field_id'		=> 'taxonomy_checklist',
                'type'			=> 'taxonomy',
                'title'			=> __( 'Departments', 'usc-jobs' ),
                'taxonomy_slugs'	=>	array( 'departments' )
            ),
            array()
        );

        http://testwestern.com//js/debug-bar.js?ver=20111209'

        $this->enqueueScript(
            '/wp-content/plugins/usc-jobs/public/test.js',   // source url or path /* @TODO: This sucks */
            array( 'usc_jobs' ),
            array(
                'handle_id' => 'test',     // this handle ID also is used as the object name for the translation array below.
                'dependencies ' => array('jquery'),
                'in_footer' => true
            )
        );
    }

    /** @TODO: draft if errors found in validation: http://stackoverflow.com/questions/5007748/modifying-wordpress-post-status-on-publish */
    public function validation_USC_Job_MetaBox( $aInput, $aOldInput ) {	// validation_{instantiated class name}

        $_fIsValid = true;
        $_aErrors = array();

        $non_empty_fields = array(

            'job_description'   => 'Sorry, but Job Description cannot be empty.',
            'apply_by_date'     => 'Yikes!  You forgot to put in an apply-by date.',
            'application_link'  => 'Oops, forgot to link to the application form.'
        );

        // You can check the passed values and correct the data by modifying them.
        //echo $this->oDebug->logArray( $aInput );

        // Validate the submitted data.
        foreach( $non_empty_fields as $key => $value ) {

            if ( empty( $aInput[$key] ) ) {

                $_aErrors[$key] = __( $value, 'usc-jobs' );
                $_fIsValid = false;

            }

        }

        //get only the file extension
        $pdf_description_extension = pathinfo($aInput['pdf_description'], PATHINFO_EXTENSION);

        /*
         * http://stackoverflow.com/questions/7563658/php-check-file-extension
         * empty string "" is for files then end with .. NULL is for no file extension.
        */

        if ( $pdf_description_extension !== "pdf" ) {

            $_aErrors['pdf_description'] = __( 'The selected is not a valid "pdf" file.  Please fix.', 'usc-jobs' ) . ': ' . $aInput['pdf_description'];
            $_fIsValid = false;

            //wp_die( 'The file you elected to upload sucks.' );

            //okay, so wp_delete_post( $postid, $force_delete ); right away?  It's hacky, but it might work.

        }

        /*
        if ( empty( $aInput['metabox_password'] ) ) {

            $_aErrors['metabox_password'] = __( 'The password cannot be empty.', 'admin-page-framework-demo' );
            $_fIsValid = false;

        }

    @TODO: Validate URLs please.

  */      if ( ! $_fIsValid ) {

            //global $post;

            add_filter('wp_insert_post_data', 'my_post_data_validator', '99');

            $this->setFieldErrors( $_aErrors );
            $this->setSettingNotice( __( '<pre>someone ' . print_r($aInput, true) . '</pre>', 'admin-page-framework-demo' ) );
            //$this->setSettingNotice( __( '<pre>' . print_r($post, true) . '</pre><pre>publish ' . $_POST['publish'] . '</pre><pre>save ' . $_POST['save'] . '</pre>
            //<pre>status ' . $_POST['post_status'] . '</pre><pre>' . print_r($wpdb, true) . '</pre>', 'admin-page-framework-demo' ) );

            return $aOldInput;

        }

        return $aInput;

    }

}

function my_post_data_validator( $data ) {
    //if ($data['post_type'] == 'post') {
        // If post data is invalid then
        $data['post_status'] = 'pending';
    //}
    return $data;
}
