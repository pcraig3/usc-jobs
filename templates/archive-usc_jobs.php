<?php get_header(); ?>

<div id="content" class="wrap clearfix">

    <div id="main" class="eightcol first clearfix" role="main">

        <h1 class="archive-title h2">
            <?php post_type_archive_title(); ?>
        </h1>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

                <header class="article-header">

                    <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
                    <p class="byline vcard"><?php
                        printf(__('by <span class="author">%3$s</span>, <time class="updated" datetime="%1$s" pubdate>%2$s</time>', 'serena'), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), serena_get_the_author_posts_link());
                        ?></p>


                </header> <!-- end article header -->

                <section class="entry-content clearfix">
                    <p>Application Deadline: <?php echo date('g a, \o\n F d', strtotime( get_post_meta( get_the_ID(), "apply_by_date", true ) ) ); ?></p>
                    <p>Whether Paid: <?php echo ucwords(get_post_meta( get_the_ID(), "renumeration", true )); ?></p>
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

</div> <!-- end #content -->

<?php get_footer(); ?>
