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
                'description'	=> __( 'Is this a paid or a volunteer position?', 'usc-jobs' ),
                'help'	        => __( 'Is this a paid or a volunteer position?', 'usc-jobs' ),
                'label' => array(
                    'volunteer' => __( 'Volunteer', 'usc-jobs' ),
                    'paid' => __( 'Paid', 'usc-jobs' ),
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
                'field_id'		=>	'pdf_description',
                'title'			=>	__( 'PDF Job Description', 'usc-jobs' ),
                'type'			=>	'file',
                'description'	=>	__( 'Upload the job description PDF file (if any)', 'usc-jobs' ),
                'description'	=>	__( 'Upload the job description PDF file (if any)', 'usc-jobs' ),
            )
        );

        $this->addSettingFields(
            array (
                'field_id'		=> 'taxonomy_checklist',
                'type'			=> 'taxonomy',
                'title'			=> __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
                'taxonomy_slugs' => get_taxonomies( '', 'names' ),
            ),
            array()
        );
    }

    public function validation_USC_Job_MetaBox( $aInput, $aOldInput ) {	// validation_{instantiated class name}

        $_fIsValid = true;
        $_aErrors = array();

        $non_empty_fields = array(

            'job_description'   => 'Sorry, but Job Description cannot be empty.',
            'apply_by_date'     => 'Yikes!  You forgot to put in an apply-by date.',
            'application_link'  => 'Oops, forgot to link to the application form'
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

/*
        if ( strlen( trim( $aInput['metabox_text_field'] ) ) < 3 ) {

            $_aErrors['metabox_text_field'] = __( 'The entered text is too short! Type more than 2 characters.', 'admin-page-framework-demo' ) . ': ' . $aInput['metabox_text_field'];
            $_fIsValid = false;

        }
        if ( empty( $aInput['metabox_password'] ) ) {

            $_aErrors['metabox_password'] = __( 'The password cannot be empty.', 'admin-page-framework-demo' );
            $_fIsValid = false;

        }

  */      if ( ! $_fIsValid ) {

            $this->setFieldErrors( $_aErrors );
            $this->setSettingNotice( __( 'There was an error in your input in meta box form fields', 'admin-page-framework-demo' ) );
            return $aOldInput;

        }

        return $aInput;

    }

}