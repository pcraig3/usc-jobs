<?php


get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>

    <div id="main-content">

        <?php if ( ! $is_page_builder_used ) : ?>

        <div class="et_pb_section et_pb_fullwidth_section et_section_regular">

            <section class="et_pb_fullwidth_header et_pb_bg_layout_dark et_pb_text_align_left">
                <div class="et_pb_row">
                    <h1><?php the_title(); ?></h1>
                </div>
            </section>

        </div>

        <div id="news-post" class="container">
            <div id="content-area" class="clearfix et_pb_row">
                <div class="et_pb_column et_pb_column_2_3">

                    <div class="breadcrumbs">
                        <?php if(function_exists('bcn_display'))
                        {
                            bcn_display();
                        }?>
                    </div>

                    <?php endif; ?>

                    <?php while ( have_posts() ) : the_post(); ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                            <?php if ( ! $is_page_builder_used ) : ?>
                                <div class="news-article-info">
                                    <!--<span class="news-date"><?php the_time('F jS, Y') ?> </span>
				    <span class="news-author"><a href="<?php et_get_the_author_posts_link();?>"> Posted by <?php the_author() ?></a></span>-->
                                    <?php //et_divi_post_meta();

                                    //a href="http://westernusc.org/blog/author/uscadmin/" title="Posts by USCAdmin" rel="author">USCAdmin</a> | Aug 8, 2014 |  | </p>

                                    $html_string    = "";

                                    $array_of_departments = get_the_terms( get_the_ID(), 'departments' );

                                    //if no departments are assigned, get_the_terms() returns 'false'
                                    if( false !== $array_of_departments ) {

                                        $html_string .= '<p class="post-meta">';

                                        foreach( $array_of_departments as &$department) {

                                            //var_dump($department);

                                            $department_archive_url = get_site_url( get_current_blog_id(), trailingslashit( $department->taxonomy . '/' . $department->slug ) );

                                            $html_string .= '<a title="Find more ' . esc_attr($department->name) . ' jobs!"';
                                            $html_string .= ' href="' . esc_url($department_archive_url) . '" >';
                                            $html_string .= $department->name . "</a>, ";

                                        }
                                        unset($department);

                                        $html_string = trim($html_string, ", ");
                                        $html_string .= ' | ' . get_the_date( );
                                        $html_string .= '</p>';
                                    }

                                    echo $html_string;

                                    ?>
                                </div>

                            <?php endif; ?>

                            <div class="news-entry-content news-entry-content__usc_jobs">
                                <?php

                                $html_string = '';

                                $array_of_meta_values =  get_post_meta( get_the_ID() );

                                //var_dump($array_of_meta_values);

                                $html_string .= '<p><span class="subheading">' . __( 'Compensation', 'usc-jobs' ) . '</span>  '
                                                    . ucfirst( array_shift( $array_of_meta_values['remuneration'] ) ) . "</p>";
                                $html_string .= '<p><span class="subheading">' . __( 'Apply By Date', 'usc-jobs' ) . '</span>  '
                                                    . date( 'F j, Y', strtotime( array_shift( $array_of_meta_values['apply_by_date'] ) ) ) . "</p>";
                                $html_string .= '<p><span class="subheading">' . __( 'Start Date', 'usc-jobs' ) . '</span> '
                                                    . "September 13, 2014" . "</p>";

                                $html_string .= '<br>';

                                $html_string .= '<p><span class="subheading">' . __( 'Description', 'usc-jobs' ) . '</span></p>';

                                echo $html_string;

                                the_content();

                                //button area

                                $job_description_file   = array_shift($array_of_meta_values['job_description_file']);;
                                $job_posting_file       = array_shift($array_of_meta_values['job_posting_file']);
                                $application_link       = array_shift($array_of_meta_values['application_link']);;


                                //application_link',
                 			    //job_posting_file',
               		            //job_description_file',

                                $html_string =  '<div class="button_area_at_the_bottom_of_a_single_usc_job btn-menu">';
                                $html_string .=     '<ul>';
                                if( !empty( $job_description_file ) )
                                    $html_string .=     '<li><a class="job_description_file" target="_blank" href="' . esc_url( $job_description_file ) . '">' . __( 'Job Description', 'usc-jobs' ) .'</a></li>';

                                if( !empty( $job_posting_file ) )
                                    $html_string .=     '<li><a class="job_posting_file" target="_blank" href="' . esc_url( $job_posting_file ) . '">' . __( 'Job Posting', 'usc-jobs' ) . '</a></li>';

                                if( !empty( $application_link ) )
                                    $html_string .=     '<li><a class="application_link" target="_blank" href="' . esc_url( $application_link ) . '">' . __( 'Application Link', 'usc-jobs' ) . '</a></li>';

                                $html_string .=     '</ul>';
                                $html_string .= '</div>';

                                echo $html_string;


                                if ( ! $is_page_builder_used )
                                    wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
                                ?>
                            </div> <!-- .entry-content -->

                        </article> <!-- .et_pb_post -->

                    <?php endwhile; ?>

                    <?php if ( ! $is_page_builder_used ) : ?>

                </div> <!-- #left-area -->

                <?php if ( is_active_sidebar( 'usc_jobs_single_sidebar' ) ) : ?>
                    <div class="et_pb_column et_pb_column_1_3">
                        <div class="et_pb_widget_area et_pb_widget_arzea_right clearfix et_pb_bg_layout_light btn-menu">
                            <?php dynamic_sidebar( 'usc_jobs_single_sidebar' ); ?>
                        </div><!-- .et_pb_widget_area .btn-menu -->
                    </div><!-- .et_pb_column -->
                <?php endif; ?>


            </div> <!-- #content-area -->

        </div> <!-- .container -->
    <!--Comments section-->
        <div id="news-comments" class="et_pb_section">
            <div class="et_pb_row">
                <?php
                if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'divi_show_postcomments', 'on' ) )
                    comments_template( '', true );
                ?>
            </div>
        </div>
        <!--comments section ends--->

    <?php endif; ?>

    </div> <!-- #main-content -->

<?php get_footer(); ?>