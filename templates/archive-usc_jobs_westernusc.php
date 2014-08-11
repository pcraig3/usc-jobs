<?php


get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>

    <div id="main-content">

        <div class="entry-content">
            <div class="et_pb_section et_pb_fullwidth_section et_section_regular">



                <section id="fake_usc_jobs" class="et_pb_fullwidth_header et_pb_bg_layout_dark et_pb_text_align_left">
                    <div class="et_pb_row">
                        <h1>
                            <?php

                            $html_string = '';

                            $is_departments     = is_tax( 'departments' );
                            $is_usc_jobs        = is_post_type_archive( 'usc_jobs' );
                            $is_remuneration    = false;

                            if( $is_departments ) {

                                //get the slug of the taxonomy term (ie, "finance")
                                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

                                //get the taxonomy object
                                $taxonomy = get_taxonomy( $term->taxonomy );

                                $html_string .= $taxonomy->labels->singular_name . ': ' . $term->name;
                            }
                            if( $is_usc_jobs ) {

                                //prints the archive title.  This isn't echoed, this is returned.
                                ob_start();
                                post_type_archive_title();

                                $html_string .= ob_get_clean();

                                $remuneration = get_query_var( 'usc_jobs_remuneration' );

                                if( ! empty( $remuneration ) ) {

                                    $is_remuneration = true;
                                    $html_string .= ': ' . ucfirst( $remuneration );
                                }
                            }

                            echo $html_string;

                            ?>
                        </h1>

                    </div>
                </section>

            </div> <!-- .et_pb_section -->

            <div class="et_pb_section usc-breadcrumbs et_section_regular" style="background-color:#fbfbfb;">

                <div class="et_pb_row">
                    <div class="et_pb_column et_pb_column_4_4">
                        <div class="et_pb_text et_pb_bg_layout_light et_pb_text_align_left">


                            <div class="breadcrumbs">
                                <?php if(function_exists('bcn_display'))
                                {
                                    bcn_display();
                                }?>
                            </div>


                        </div> <!-- .et_pb_text -->
                    </div> <!-- .et_pb_column -->
                </div> <!-- .et_pb_row -->

            </div>

            <div class="et_pb_section et_section_regular">

                <div class="et_pb_row">

                    <?php if ( is_active_sidebar( 'usc_jobs_archive_sidebar' ) ) : ?>
                        <div class="et_pb_column et_pb_column_1_3">
                            <div class="et_pb_widget_area et_pb_widget_arzea_right clearfix et_pb_bg_layout_light btn-menu">
                                <?php dynamic_sidebar( 'usc_jobs_archive_sidebar' ); ?>
                            </div><!-- .et_pb_widget_area .btn-menu -->
                        </div><!-- .et_pb_column -->
                    <?php endif; ?>


                    <div class="et_pb_column et_pb_column_2_3">
                        <div class="et_pb_text et_pb_bg_layout_light et_pb_text_align_left">

                            <?php

                            global $wp_query;

                            ?>

                            <h4 class="usc_jobs--count"><?php echo $wp_query->found_posts; ?> Jobs Available</h4>

                            <?php

                            if ( have_posts() ) :
                                while ( have_posts() ) : the_post();
                                    $post_format = get_post_format(); ?>

                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>

                                        <?php
                                        $thumb = '';

                                        $width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

                                        $height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
                                        $classtext = 'et_pb_post_main_image';
                                        $titletext = get_the_title();
                                        $thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
                                        $thumb = $thumbnail["thumb"];

                                        et_divi_post_format_content();
                                        ?>

                                        <a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>

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
                                            //$html_string .= ' | ' . get_the_date( );
                                            $html_string .= '</p>';
                                        }

                                        echo $html_string;

                                        $html_string = '';

                                        $array_of_meta_values =  get_post_meta( get_the_ID() );

                                        //var_dump($array_of_meta_values);

                                        $html_string .= '<p><span class="subheading">' . __( 'Compensation', 'usc-jobs' ) . '</span>  '
                                            . ucfirst( array_shift( $array_of_meta_values['remuneration'] ) ) . "</p>";
                                        $html_string .= '<p><span class="subheading">' . __( 'Apply By Date', 'usc-jobs' ) . '</span>  '
                                            . date( 'F j, Y', strtotime( array_shift( $array_of_meta_values['apply_by_date'] ) ) ) . "</p>";
                                        /*
                                         $html_string .= '<p><span class="subheading">' . __( 'Start Date', 'usc-jobs' ) . '</span> '
                                            . "September 13, 2014" . "</p>";
                                        */

                                        echo $html_string;

                                        ?>

                                    </article> <!-- .et_pb_post -->
                                <?php
                                endwhile;

                                if ( function_exists( 'wp_pagenavi' ) )
                                    wp_pagenavi();
                                else
                                    get_template_part( 'includes/navigation', 'index' );
                            else :
                                get_template_part( 'includes/no-results', 'index' );
                            endif;
                            ?>
                        </div> <!-- .et_pb_text -->
                    </div> <!-- .et_pb_column -->
                </div> <!-- .et_pb_row -->

            </div> <!-- .et_pb_section -->
        </div> <!-- .entry-content -->

    </div> <!-- #main-content -->

<?php get_footer(); ?>