<?php get_header(); ?>

<div id="content" class="wrap clearfix">

    <div id="main" class="eightcol first clearfix" role="main">

        <h1 class="archive-title h2">
            <?php

            post_type_archive_title();

            $remuneration = get_query_var('usc_jobs_remuneration');

            if( ! empty($remuneration) ) {
                echo ': ' . ucfirst($remuneration);
            }
            ?>
        </h1>

        <div class="filterjs">
            <div class="filterjs__filter">
                <div class="filterjs__filter__search__wrapper">
                    <h4>Search with filter.js</h4>
                    <input type="text" id="search_box" class="searchbox" placeholder="Type here...."/>
                </div>
                <div class="filterjs__filter__checkbox__wrapper">
                    <h4>Filter by Money</h4>
                    <ul id="remuneration">
                        <li>
                            <input id="paid" value="paid" type="checkbox">
                            <span>paid</span>
                        </li>
                        <!--li>
                            <input id="volunteer" value="volunteer" type="checkbox">
                            <span>volunteer</span>
                        </li-->
                        <li>
                            <input id="internship" value="internship" type="checkbox">
                            <span>internship</span>
                        </li>
                    </ul>
                </div>
                <div class="filterjs__filter__checkbox__wrapper">
                    <h4>Filter by Dept</h4>
                    <ul id="taxonomy_departments">
                        <li>
                            <input id="finance" value="finance" type="checkbox">
                            <span>finance</span>
                        </li>
                        <!--li>
                            <input id="volunteer" value="volunteer" type="checkbox">
                            <span>volunteer</span>
                        </li-->
                        <li>
                            <input id="chrw" value="chrw" type="checkbox">
                            <span>chrw</span>
                        </li>
                    </ul>
                </div>
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
        </div>

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
