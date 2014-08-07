jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

    AjaxEvents.ajax_jobs_gotten = function( jobs, limit ) {

        $('.filterjs__loading').addClass('hidden');

        //I don't think we need limits.
        //events = AjaxEvents.limit_events( jobs, limit );

        $('#remuneration :checkbox').prop('checked', true);

        fJS = filterInit( jobs );

        $('#usc_jobs_list').trigger( "change" );
    };

    /**
     * Function sets up all of our filtering.
     * Works now, but seems a bit brittle.
     *
     * @param jobs    a list of jobs. Data is pulled from the USC Jobs Custom Post Type in our database.
     *
     * @since   0.5.1
     *
     * @returns {*} A list of searchable jobs in the backend.
     */
    function filterInit( jobs ) {

        var view = function( job ){

            var html_string = '';

            //at this point we have ONE JOB.  This sets up the loop.

            html_string +=  '<article id="post-' + job.wp_id + '" class="post-' + job.wp_id + ' ' + job.type + ' type-' + job.type + ' status-publish hentry clearfix" role="article">';
            html_string +=      '<header class="article-header">';

            html_string +=          '<h3>';
            html_string +=              '<a href="' + job.url + '" rel="bookmark" title="' + job.title + '">' + job.title + '</a>';
            html_string +=          '</h3>';

            html_string +=          '<p class="byline vcard">by <span class="author"><a href="http://testwestern.com/author/' + job.author.slug + '/" title="Posts by '
                                        + job.author.name  + '" rel="author">' + job.author.name + '</a></span>, ';

            html_string +=          '<time class="updated" datetime="' + job.modified.substring(0, 10) + '" pubdate="">' + job.modified.substring(0, 10) + '</time></p>';

            html_string +=      '</header> <!-- end article header -->';

            html_string +=      '<section class="entry-content clearfix">';

            html_string +=          '<p>Application Deadline: ' + job.custom_fields.apply_by_date + '</p>';
            html_string +=          '<p>Whether Paid: ' + job.custom_fields.remuneration.charAt(0).toUpperCase() + job.custom_fields.remuneration.slice(1) + '</p>';

            html_string +=          '<span class="float-right">';
            html_string +=              '<a href="' + job.url + '" title="Check out this job!">Full Details »</a>';
            html_string +=          '</span>';

            html_string +=      '</section> <!-- end article section -->';

            html_string +=      '<footer class="article-footer">';
            html_string +=      '</footer> <!-- end article footer -->';
            html_string +=   '</article> <!-- end article -->';

            return html_string;
        }

        var settings = {
            filter_criteria: {
             remuneration: ['#remuneration input:checkbox', 'custom_fields.remuneration']
             },
            search: {input: '#search_box' },
            and_filter_on: true,
            id_field: 'id' //Default is id. This is only for usecase
        };

        return FilterJS(jobs, "#usc_jobs_list", view, settings);
    }

    $(document).ready(function($) {

        var usc_jobs_as_json = JSON.parse(options.jobs);

        console.log( usc_jobs_as_json[0] );

        AjaxEvents.ajax_jobs_gotten( usc_jobs_as_json, 0 );

    });

});
