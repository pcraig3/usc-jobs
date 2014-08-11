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

            </div> <!-- .et_pb_section --><div class="et_pb_section et_section_regular">

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

                                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                                            <?php
                                            et_divi_post_meta();

                                            truncate_post( 270 );
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