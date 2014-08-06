<?php

/**
 1. To retrieve the meta box data	- get_post_meta( $post->ID ) will return an array of all the meta field values.
 or if you know the field id of the value you want, you can do $value = get_post_meta( $post->ID, $field_id, true );

 * Possible Meta Values (useless ones are indented)
        [_edit_last] => 1
    [job_description] => Make bread cats can't remove.
    [apply_by_date] => 2014-08-13 12:00
    [renumeration] => volunteer
    [application_link] => http://33.media.tumblr.com/2d95777547966a733ccdfb3e34afaacc/tumblr_n55qheEABg1qlka8ko1_400.gif
    [job_posting_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
    [job_description_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
    [contact_information] => email@westernusc.ca
        [_edit_lock] => 1407311587:1
 */

?>

<?php

    $array_of_meta_values =  get_post_meta( get_the_ID() );

    $html_string = "";

    /** @TODO: get the departments */

    $subhead = "";

foreach( $array_of_meta_values as $key => $value ) {

    //anything starting with an underscore we don't want.
    if( ! (substr($key, 0, 1) === '_') ) {

        //get the first item of the array
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

echo $html_string;


?>