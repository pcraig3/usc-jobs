<?php
class USC_Department_TaxonomyField extends AdminPageFramework_TaxonomyField {

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(

                'field_id'		=> 'text_field',
                'type'			=> 'hidden',
                //'title'			=> __( 'Text Input', 'admin-page-framework-demo' ),
                //'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
                //'help'			=> 'This is help text.',
                //'help_aside'	=> 'This is additional help text which goes to the side bar of the help pane.',
            )
        );

    }

    public function columns_USC_Department_TaxonomyField( $aColumn ) {	// column_{instantiated class name}

        unset( $aColumn['description'] );
        return array(
            'cb' => $aColumn['cb'],
        )
        + $aColumn;

    }

}