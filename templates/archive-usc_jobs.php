<?php get_header(); ?>

<div id="content" class="wrap clearfix">

    <div id="main" class="eightcol first clearfix" role="main">

        <h1 class="archive-title h2">
            <?php

            $is_departments = is_tax( 'departments' );
            $is_usc_jobs    = is_post_type_archive( 'usc_jobs' );

            if( $is_departments ) {

                //get the slug of the taxonomy term (ie, "finance")
                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

                //get the taxonomy object
                $taxonomy = get_taxonomy($term->taxonomy);

                echo $taxonomy->labels->singular_name . ': ' . $term->name;
            }
            if( $is_usc_jobs ) {

                post_type_archive_title();

                $remuneration = get_query_var('usc_jobs_remuneration');

                if( ! empty($remuneration) ) {
                    echo ': ' . ucfirst($remuneration);
                }
            }

            ?>
        </h1>



        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

                <header class="article-header">

                    <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
                    <p class="byline vcard"><?php
                        printf(__('by <span class="author">%3$s</span>, <time class="updated" datetime="%1$s" pubdate>%2$s</time>', 'serena'), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), "http://not_the_real_link.com");
                        ?></p>


                </header> <!-- end article header -->

                <section class="entry-content clearfix">
                    <p>Application Deadline: <?php echo date('g a, \o\n F d', strtotime( get_post_meta( get_the_ID(), "apply_by_date", true ) ) ); ?></p>
                    <p>Whether Paid: <?php echo ucwords(get_post_meta( get_the_ID(), "remuneration", true )); ?></p>
                    <span class="float-right"><?php echo(' <a href="'. get_permalink($post->ID) . '" title="Check out this job!">Full Details &raquo;</a>') ?></span>
                </section> <!-- end article section -->

                <footer class="article-footer">

                </footer> <!-- end article footer -->

            </article> <!-- end article -->

        <?php endwhile; ?>

            <nav class="wp-prev-next">
                <ul>
                    <li class="prev-link"><?php next_posts_link(__('&laquo; Older', "serena")) ?></li>
                    <li class="next-link"><?php previous_posts_link(__('Newer &raquo;', "serena")) ?></li>
                </ul>
            </nav>

        <?php else : ?>

            <article id="post-not-found" class="hentry clearfix">
                <header class="article-header">
                    <h1><?php _e("Article Missing", "serena"); ?></h1>
                </header>
                <section class="entry-content">
                    <p><?php _e("Sorry, but something is missing. Please try again!", "serena"); ?></p>
                </section>
                <footer class="article-footer">
                </footer>
            </article>

        <?php endif; ?>

    </div> <!-- end #main -->

    <?php get_sidebar(); ?>

    <?php if ( is_active_sidebar( 'usc_jobs_archive_sidebar' ) ) : ?>
        <div id="secondary" class="sidebar-container fourcol" role="complementary">
            <div class="widget-area">
                <?php dynamic_sidebar( 'usc_jobs_archive_sidebar' ); ?>
            </div><!-- .widget-area -->
        </div><!-- #secondary -->
    <?php endif; ?>

</div> <!-- end #content -->

<?php get_footer(); ?>
