<?php


get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>

    <div id="main-content">

        <div class="entry-content">
            <div class="et_pb_section et_pb_fullwidth_section et_section_regular">



                <section class="et_pb_fullwidth_header et_pb_bg_layout_dark et_pb_text_align_left">
                    <div class="et_pb_row">
                        <h1>
                            Jobs
                        </h1>
                        <p class="et_pb_fullwidth_header_subhead">
                            <?php

                            $html_string = 'The best ';

                            $is_departments     = is_tax( 'departments' );
                            $is_usc_jobs        = is_post_type_archive( 'usc_jobs' );
                            $is_remuneration    = false;

                            if( $is_departments ) {

                                //get the slug of the taxonomy term (ie, "finance")
                                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

                                //get the taxonomy object
                                //$taxonomy = get_taxonomy( $term->taxonomy );

                                $html_string .= $term->name;
                            }
                            if( $is_usc_jobs ) {

                                //prints the archive title.  This isn't echoed, this is returned.
                                //ob_start();
                                //post_type_archive_title();
                                //$html_string .= ob_get_clean();

                                $remuneration = get_query_var( 'usc_jobs_remuneration' );

                                if( ! empty( $remuneration ) ) {

                                    $is_remuneration = true;
                                    $html_string .= $remuneration;
                                }
                            }

                            echo $html_string . ' opportunities on campus.';

                            ?>
                        </p>
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

                            <div class="filterjs hidden">
                                <div class="filterjs__filter">
                                    <aside  id="nav_menu-search-1000" class="filterjs__filter__search__wrapper et_pb_widget widget_nav_menu">
                                        <h4 class="widgettitle">Search Jobs</h4>
                                        <input type="text" id="search_box" class="searchbox" placeholder="^.^"/>
                                    </aside>
                                    <aside id="nav_menu-remuneration-1000" class="filterjs__filter__checkbox__wrapper et_pb_widget widget_nav_menu" <?php echo ( $is_remuneration ) ? 'style="display:none"' : ''; ?> >
                                        <h4 class="widgettitle">Remuneration</h4>
                                        <ul id="remuneration">
                                            <?php

                                            $remuneration_values = array(
                                                'paid',
                                                'volunteer'
                                            );

                                            foreach( $remuneration_values as &$remuneration_value ) {

                                                $checked_by_default = ( ! $is_remuneration ) ? "checked" : ( $remuneration === $remuneration_value ) ? "checked" : "" ;

                                                echo '<li><label class="' . $checked_by_default . '">'
                                                        .   '<input id="' . $remuneration_value . '" value="' . $remuneration_value . '" type="checkbox">';
                                                echo ucfirst($remuneration_value) . '</label>';
                                                echo '</li>';

                                            }
                                            unset( $remuneration_value );

                                            ?>

                                        </ul>
                                    </aside>
                                    <aside id="nav_menu-departments-1000" class="filterjs__filter__checkbox__wrapper et_pb_widget widget_nav_menu" <?php echo ( $is_departments ) ? 'style="display:none"' : ''; ?> >
                                        <h4 class="widgettitle">Departments</h4>
                                        <ul id="taxonomy_departments">
                                            <?php

                                            echo '<li><label class="' .  (( ! $is_departments ) ? "checked" : '') . '">'
                                                .'<input id="departments_all" value="all" type="checkbox">All</label></li>';

                                            $departments = get_terms( 'departments' );

                                            foreach( $departments as &$department ) {

                                                $checked_by_default = ( ! $is_departments ) ? "checked" : ( $term->slug === $department->slug ) ? "checked" : "" ;

                                                /** @TODO: I mean, really we just want the departments of the current jobs */
                                                if( $department->count > 0 ) {

                                                    echo '<li><label class="' . $checked_by_default . '">'
                                                            .'<input id="' . $department->slug . '" value="' . $department->slug . '" type="checkbox">';
                                                    echo    $department->name . '</label>';
                                                    echo '</li>';
                                                }

                                            }
                                            unset( $department );

                                            ?>
                                        </ul>
                                    </aside>
                                </div>
                                <br>
                                <div class="filterjs__list__wrapper">
                                    <div class="filterjs__loading filterjs__loading--ajax">
                                        <img class="filterjs__loading__img" title="go mustangs!"
                                             src="<?php echo plugins_url( 'assets/horse.gif', __DIR__ ); ?>" alt="Loading" height="91" width="160">
                                        <p class="filterjs__loading__status">
                                            * Loading *
                                        </p>
                                    </div>

                                    <!--div class="filterjs__list__crop"-->
                                    <div class="filterjs__list" id="usc_jobs_list"></div>
                                    <!--/div-->
                                </div>
                                <div class="clearfix cf"></div>
                                <?php /* we're putting this in to ensure that the collapseomatic js file is included on the page */
                                /* the collapseomatic rules are set on the filter fields using JS in public-filer.js */
                                /* It's just two lines of JS. */
                                do_shortcode('[expand title="placeholder"][\expand]'); ?>
                            </div>


                            <?php

                            global $wp_query;

                            $jobs_string = ( $wp_query->found_posts === 0 )
                                ? 'No Jobs' :
                                ( $wp_query->found_posts === 1 )
                                    ? '1 Job':
                                    $wp_query->found_posts . ' Jobs';
                            ?>
                            <h4 class="usc_jobs--count"><span id="counter"><?php echo $jobs_string; ?></span> Available</h4>

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

                                        $html_string .= '<p><span class="subheading">' . __( 'Remuneration', 'usc-jobs' ) . '</span>  '
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