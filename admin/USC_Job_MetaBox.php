<?php
/**
 * Class USC_Job_MetaBox
 *
 * This class is later associated with the USCJob_PostType.  (since we're just creating a metabox, we can put it
 * wherever we like.)
 *
 * Anyway, this class defines a bunch of custom fields to associate with the USC_Jobs custom post type.
 */
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

            dirname( dirname( dirname( __FILE__ ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/DateCustomFieldType.php',
            dirname( dirname( dirname( __FILE__ ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/TimeCustomFieldType.php',
            dirname( dirname( dirname( __FILE__ ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
        );

        foreach( $aFiles as $sFilePath )
            if ( file_exists( $sFilePath ) ) include_once( $sFilePath );

        /* 2. Instantiate the classes  */
        $sClassName = get_class( $this );
        new DateCustomFieldType( $sClassName );
        new TimeCustomFieldType( $sClassName );
        new DateTimeCustomFieldType( $sClassName );

    }

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    /**
     * Framework method sets up all of the custom fields for a USC Job and then enqueues some JS
     *
     * Fields added are:
     * 1. apply_by_date:        the date applications for this job must be in by
     * 2. remuneration:         whether this is a paid position or a volunteer opportunity
     * 3. position:             the type of paid job (only if this is a paid job).  FT, PT, contract, intern, whatever.
     * 4. application_link:     link to the actual application (usually a form to fill out or something)
     * 5. job_posting_file:     the posting advertising this job
     * 6. job_description_file: the description of the position (hint we usually don't need both)
     * 7. contact_information:  a phone number or email or description or something.  I mean, usually an email
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.4.0
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
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
                'field_id'		=> 'remuneration',
                'type'			=> 'radio',
                'title'			=> __( 'Remuneration Expected', 'usc-jobs' ),
                //'description'	=> __( 'If this is a paid position, the following fields', 'usc-jobs' ),
                'help'	        => __( 'Is this a paid position or a volunteer position?', 'usc-jobs' ),
                'label' => array(
                    'volunteer' => __( 'Volunteer', 'usc-jobs' ),
                    'paid' => __( 'Paid', 'usc-jobs' ),
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
                    'full-time_permanent'   => __( 'Full-Time Permanent', 'usc-jobs' ),
                    'full-time_contract'    => __( 'Full-Time Contract', 'usc-jobs' ),
                    'part-time_permanent'   => __( 'Part-Time Permanent', 'usc-jobs' ),
                    'part-time_contract'    => __( 'Part-Time Contract', 'usc-jobs' ),
                    'honourarium'           => __( 'Honourarium', 'usc-jobs' ),
                    'internship'            => __( 'Internship', 'usc-jobs' ),

                ),
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
        );

        /* enqueue a javascript file with a very specialized function directly relating to this metabox */
        $this->enqueueScript(
            plugins_url('assets/js/reveal-job-pane.js', __FILE__ ),   // source url or path
            array( 'usc_jobs' ),
            array(
                'handle_id' => 'reveal_job_pane',     // this handle ID also is used as the object name for the translation array below.
                'dependencies ' => array('jquery'),
                'in_footer' => true
            )
        );

        USC_Jobs::get_instance()->turn_object_caching_back_on_for_the_next_poor_sod();
    }

    /**
     * Function that validates values for jobs.
     *
     * Used to do a lot more, but it wasn't really working so well.
     * Currently, the only error-checking I have is that this sets publish status to 'draft' if the apply_by_date is empty
     *
     * @seE: http://stackoverflow.com/questions/5007748/modifying-wordpress-post-status-on-publish
     *
     * @param array $aInput     values in each of the input fields at the time of submitting the form
     * @param array $aOldInput  old values saved from before inputting the form.
     * @return mixed            either the $aInput values if everything checks out, or the error array if an error is found
     */
    public function validation_USC_Job_MetaBox( $aInput, $aOldInput ) {	// validation_{instantiated class name}

        USC_Jobs::get_instance()->turn_off_object_cache_so_our_bloody_plugin_works();

        $_fIsValid = true;
        $_aErrors = array();

        // You can check the passed values and correct the data by modifying them.
        //echo $this->oDebug->logArray( $aInput );

        $non_empty_fields = array(

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

        /*

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

            elseif ( ! $this->web_item_exists( $aInput['job_description_file'] ) ){

                $_aErrors['job_description_file'] = __( 'Sorry, but your URL doesn\'t appear to exist. Try uploading and selecting your file again.', 'usc-jobs' );
                $_fIsValid = false;

            }
        }

        if( ! filter_var( $aInput['application_link'], FILTER_VALIDATE_URL )  ) {

            $_aErrors['application_link'] = __( 'Sorry, can you try a properly formatted URL?', 'usc-jobs' );
            $_fIsValid = false;

        }
        */

        if ( ! $_fIsValid ) {

            $this->setFieldErrors( $_aErrors );

            $admin_error_message = implode("<br>", array_values($_aErrors))
                . "<br><br><em>Job marked as <strong>Pending Review</strong> until errors have been resolved.</em>";

            $this->setSettingNotice( __( $admin_error_message, 'usc-jobs' ) );

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
     * Ripped off of stackoverflow
     * http://stackoverflow.com/questions/7952977/php-check-if-url-and-a-file-exists
     *
     * @author Charleston Software Associates
     *
     * @since    0.4.0
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


