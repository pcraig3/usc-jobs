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
                'title'			=> __( 'Job Description*', 'usc-jobs' ),
                'description'	=> __( '(required)', 'usc-jobs' ),
                'help'			=> __( 'Write a short description for the job here.', 'usc-jobs' ),
                'attributes'	=>	array(
                    'cols'	=>	40,
                )
            ),
            array(	// date picker
                'field_id'	    =>	'apply_by_date',
                'title'	        =>	__( 'Apply-by Date*', 'usc-jobs'),
                'description'	=>	__( 'Candidates must have their applications in by this date. (required)', 'usc-jobs' ),
                'help'	        =>	__( 'Candidates must have their applications in by this date.', 'usc-jobs' ),
                'type'          =>  'date_time',
                'date_format'	=>	'yy-mm-dd',
                'time_format'	=>  'HH:mm',
                'size'          =>  '40',
            ),
            array (
                'field_id'		=> 'renumeration',
                'type'			=> 'radio',
                'title'			=> __( 'Renumeration Expected', 'usc-jobs' ),
                //'description'	=> __( 'If this is a paid position, the following fields', 'usc-jobs' ),
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
                'title'			=> __( 'Position*', 'usc-jobs' ),
                'description'	=> __( '(required if position is paid)', 'usc-jobs' ),
                'help'	        => __( 'What kind of position this job is for.', 'usc-jobs' ),
                'label' => array(
                    'ft_permanent'  => __( 'Full-Time Permanent', 'usc-jobs' ),
                    'ft_contract'   => __( 'Full-Time Contract', 'usc-jobs' ),
                    'pt_permanent'  => __( 'Part-Time Permanent', 'usc-jobs' ),
                    'pt_contract'   => __( 'Part-Time Contract', 'usc-jobs' ),
                    'honourarium'   => __( 'Honourarium', 'usc-jobs' ),
                ),
                'default' => 'volunteer',
                'attributes'	=>	array(
                    'class'	=>	'hidden',
                ),
            ),
            array(
                'field_id'		=> 'application_link',
                'type'			=> 'text',
                'title'			=> __( 'Application Link', 'usc-jobs' ),
                'description'	=> __( 'Link to an offsite application form.', 'usc-jobs' ),
                'help'			=> __( 'Link to an offsite application form.', 'usc-jobs' ),
            ),
            array( // Media File (which we are constraining to PDFs.)
                'field_id'		=>	'job_posting_file',
                'title'			=>	__( 'Job Posting File', 'usc-jobs' ),
                'type'			=>	'media',
                'description'	=>	__( 'Upload the job posting file.', 'usc-jobs' ),
                'help'	        =>	__( 'Upload the job posting file.', 'usc-jobs' ),
                'allow_external_source'	=>	true,
            ),
            array(
                'field_id'		=>	'job_description_file',
                'title'			=>	__( 'Job Description File*', 'usc-jobs' ),
                'type'			=>	'media',
                'description'	=>	__( 'Only PDF and Word documents are accepted. (required)', 'usc-jobs' ),
                'help'	        =>	__( 'Upload the job description file (required).', 'usc-jobs' ),
                'allow_external_source'	=>	false,
                'attributes'	=>	array(
                    'data-nonce'	=>	wp_create_nonce('job_description_file_nonce'),
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
        /* ,
        array (
            'field_id'		=> 'taxonomy_checklist',
            'type'			=> 'taxonomy',
            'title'			=> __( 'Departments', 'usc-jobs' ),
            'taxonomy_slugs'	=>	array( 'departments' )
        )
        */
        );

        http://testwestern.com//js/debug-bar.js?ver=20111209'

        $this->enqueueScript(
            plugins_url('assets/js/reveal_job_pane.js', __FILE__ ),   // source url or path
            array( 'usc_jobs' ),
            array(
                'handle_id' => 'reveal_job_pane',     // this handle ID also is used as the object name for the translation array below.
                'dependencies ' => array('jquery'),
                'in_footer' => true
            )
        );
    }

    /** Draft if errors found in validation: http://stackoverflow.com/questions/5007748/modifying-wordpress-post-status-on-publish */
    public function validation_USC_Job_MetaBox( $aInput, $aOldInput ) {	// validation_{instantiated class name}

        $_fIsValid = true;
        $_aErrors = array();

        // You can check the passed values and correct the data by modifying them.
        //echo $this->oDebug->logArray( $aInput );

        $non_empty_fields = array(

            'job_description'   => 'Sorry, but Job Description cannot be empty.',
            'apply_by_date'     => 'Yikes!  You forgot to put in an apply-by date.',
            'job_description_file'  => 'Oh no! Please upload and select a job description file.'
        );

        // Validate the submitted data.
        foreach( $non_empty_fields as $key => $value ) {

            if ( empty( $aInput[$key] ) ) {

                $_aErrors[$key] = __( $value, 'usc-jobs' );
                $_fIsValid = false;
            }
        }

        if( ! isset( $_aErrors['job_description_file'] ) ) {

            //get only the file extension
            $job_description_file_extension = pathinfo($aInput['job_description_file'], PATHINFO_EXTENSION);

            $allowed_extensions = array(
                'pdf',
                'doc',
                'docx'
            );

            if ( ! in_array($job_description_file_extension, $allowed_extensions) ) {

                $_aErrors['job_description_file'] = __( 'Not an acceptable file type.  Please upload a PDF or a Word Document.', 'usc-jobs' );
                $_fIsValid = false;
            }
            ///http://stackoverflow.com/questions/7952977/php-check-if-url-and-a-file-exists
            elseif ( ! $this->web_item_exists( $aInput['job_description_file'] ) ){

                $_aErrors['job_description_file'] = __( 'Sorry, but your URL doesn\'t appear to exist. Try uploading and selecting your file again.', 'usc-jobs' );
                $_fIsValid = false;

            }
        }

        if( ! filter_var( $aInput['application_link'], FILTER_VALIDATE_URL )  ) {

            $_aErrors['application_link'] = __( 'Sorry, can you try a properly formatted URL?', 'usc-jobs' );
            $_fIsValid = false;

        }

        if ( ! $_fIsValid ) {

            $this->setFieldErrors( $_aErrors );
            $this->setSettingNotice( __( '<pre>' . print_r($aInput, true) . '</pre><p>' . 'nothing' . '</p>', 'usc-jobs' ) );

            //hacky, but fun!
            add_filter( 'wp_insert_post_data', function( $data ) { //use ( $status ) {

                $data['post_status'] = 'pending';

                return $data;
            });

            return $aInput;

        }

        return $aInput;

    }

    /**
     * Check if an item exists out there in the "ether".
     *
     * @param string $url - preferably a fully qualified URL
     * @return boolean - true if it is out there somewhere
     */
    private function web_item_exists( $url ) {
        if ( !isset( $url ) || empty( $url ) )
            return false;

        $response = wp_remote_head( $url, array( 'timeout' => 5 ) );

        $accepted_status_codes = array( 200, 301, 302 );

        if ( ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), $accepted_status_codes ) ) {
            return true;
        }
        return false;
    }
}


