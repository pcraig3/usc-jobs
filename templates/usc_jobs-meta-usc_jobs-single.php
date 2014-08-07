<?php

$html_string    = "";
$subhead        = "";


/**
echo '<pre>';
echo var_dump( get_post_meta( get_the_ID() ) );
echo '</pre>';
**/

$array_of_departments = get_the_terms( get_the_ID(), 'departments' );

$html_string .= '<h3>' . __( 'Departments' , 'usc-jobs') . '</h3>';

//if no departments are assigned, get_the_terms() returns 'false'
if( false === $array_of_departments ) {

    $html_string .= '<p>This Job does not fall under a Department.</p>';
}
else {

    $html_string .= '<p>';

    foreach( $array_of_departments as &$department) {

        $department_archive_url = get_site_url( get_current_blog_id(), trailingslashit( $department->taxonomy . '/' . $department->slug ) );

        $html_string .= '<a title="Find more Jobs in ' . $department->name . '" ';
        $html_string .= ' href="' . $department_archive_url . '" >';
        $html_string .= $department->name . "</a>, ";

    }
    unset($department);

    $html_string = trim($html_string, ", ");
    $html_string .= '</p>';

}

/**
1. To retrieve the meta box data	- get_post_meta( $post->ID ) will return an array of all the meta field values.
or if you know the field id of the value you want, you can do $value = get_post_meta( $post->ID, $field_id, true );

 * Possible Meta Values (useless ones are indented)
[_edit_last] => 1
[job_description] => Make bread cats can't remove.
[apply_by_date] => 2014-08-13 12:00
[remuneration] => volunteer
[application_link] => http://33.media.tumblr.com/2d95777547966a733ccdfb3e34afaacc/tumblr_n55qheEABg1qlka8ko1_400.gif
[job_posting_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
[job_description_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
[contact_information] => email@westernusc.ca
[_edit_lock] => 1407311587:1
 */



    $array_of_meta_values =  get_post_meta( get_the_ID() );

$html_string .= '<br>';


foreach( $array_of_meta_values as $key => $value ) {

    //anything starting with an underscore we don't want.
    if( ! (substr($key, 0, 1) === '_') ) {

        //get the first item of the array
        $value = (string) array_shift($value);

        //get a better title
        $subhead = ucwords(str_replace("_", " ", $key));

        if( filter_var( $value, FILTER_VALIDATE_URL )  ) { //test for a url

            $html_string .= '<a href="' . esc_url($value) . '" title="click me!"><h3>' . __( $subhead , 'usc-jobs') . '</h3></a><br>';
        }
        else {

            $html_string .= '<h3>' . __( $subhead , 'usc-jobs') . '</h3>'
                . '<p>' . $value . '</p><br>';
        }
    }
}

echo $html_string;
