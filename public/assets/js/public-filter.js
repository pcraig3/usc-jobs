jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

    var fJS;


    var AjaxUSCJobs = {

        /** Remove something that's supposed to be there an put in something that's not. */
        remove_wordpress_jobs_for_filterjs_jobs: function() {

            var $jobs_column = $('.post-type-archive-usc_jobs .et_pb_text, .tax-departments #main');
            var $to_detach = $jobs_column.find('.filterjs__list__wrapper');

            //http://bugs.jquery.com/ticket/13400

            //old.replaceWith( new ); //can be changed to:
            //old.before( new ).detach();

            $articles = $jobs_column.find('article');

            $articles.first().before( $to_detach );
            $articles.remove();

        },

        remove_wordpress_widgets_for_filterjs_imposter_widgets : function() {

            var $widgets_column = $('.post-type-archive-usc_jobs .et_pb_widget_area, .tax-departments #main');
            var $filterjs       = $('.filterjs.hidden');


            //old.before( new ).detach();

            $widgets_column.find('aside').each(function( index ) {


                var found = (  $( this ).find( '[class*=remuneration]' ).length > 0 );

                if( found )
                    $( this ).replaceWith( $filterjs.find( '#nav_menu-remuneration-1000' ) );

                /*else {
                    found = ( $( this ).find( '[class*=departments]' ).length > 0 );

                    if( found )
                        $( this ).replaceWith( $filterjs.find( '#nav_menu-departments-1000' ) );

                }*/

                if( !found )
                    $( this ).remove();

            });

            //now, put in the search bar.
            $widgets_column.prepend( $filterjs.find('#nav_menu-search-1000').detach() );

            //if there are more asides in the filter colunm, add them.
            if( $filterjs.find('aside').length > 0 ) {

                $filterjs.find('aside').each(function( index ) {

                    $widgets_column.append( $(this) );

                });

            }

            $widgets_column.find('#search_box').on('keyup', function() {

                AjaxUSCJobs.typewatch(function () {
                    AjaxUSCJobs.update_visible_jobs()
                }, 50);

            });

            $widgets_column.find('#taxonomy_departments, #remuneration').delegate('input', 'click', function() {

                AjaxUSCJobs.update_visible_jobs();
                console.log( $('#usc_jobs_list').find('article:visible').length );
            });

        },

        typewatch: (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            }
        })(),

        jobs_gotten: function( jobs ) {

        $('.filterjs__loading').addClass('hidden');

        $('#remuneration :checkbox.check_me, #taxonomy_departments :checkbox.check_me').prop('checked', true);

        fJS = filterInit( jobs );

        $('#usc_jobs_list').trigger( "change" );
        },

        date_format: function( date_string ) {

            var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

            //returned date strings look like this
            // "2014-08-22 23:59"
            //  0123456789012345

            //making a date new Date(year, month, day, hours, minutes, seconds, milliseconds);
            var d = new Date(date_string.slice(0,4), (date_string.slice(5,7) - 1), date_string.slice(8,10), date_string.slice(11,13), 0, 0);

            //Friday, August 22
            //return  days[ d.getDay() ] + ', ' + months[ d.getMonth() ] + " " + d.getDate();

            //August 22, 2014
            return  months[ d.getMonth() ] + " " + d.getDate() + ", " + d.getFullYear();
        },

        update_visible_jobs: function() {

            var $jobs_column = $('.et_pb_text');

            $jobs_column.find('#counter').text( $jobs_column.find('article:visible').length );
        }

    };

    /**
     * Function sets up all of our filtering.
     * Works now, but seems a bit brittle.
     *
     * @param jobs    a list of jobs. Data is pulled from the USC Jobs Custom Post Type in our database.
     *
     * @since   0.6.0
     *
     * @returns {*} A list of searchable jobs in the backend.
     */
    function filterInit( jobs ) {

        var view = function( job ) {

            var html_string = '';

            //at this point we have ONE JOB.  This sets up the loop.

            /*

             <p class="post-meta"><a title="Find more Creative Services jobs!" href="http://westernusc.org/departments/creative-services-2/">Creative Services</a></p><p><span class="subheading">Compensation</span>  Paid</p><p><span class="subheading">Apply By Date</span>  August 22, 2014</p>
             </article>

             */

            html_string +=  '<article id="post-' + job.wp_id + '" class="post-' + job.wp_id + ' ' + job.type + ' type-' + job.type + ' status-publish hentry et_pb_post">';

            html_string +=          '<a href="' + job.url + '" title="' + job.title + '"><h2>' + job.title + '</h2></a>';

            html_string +=          '<p class="post-meta">';

            var departments = job.taxonomy_departments;
            var total = departments.length;
            for (var i = 0; i < total; i++) {

                html_string +=       '<a title="Find more ' + departments[i].title + ' jobs!" '
                                        + 'href="http://westernusc.org/departments/' + departments[i].slug + '/">' + departments[i].title + '</a>, ';

            }

            //cut off the last comma and space
            html_string =           html_string.slice(0, (html_string.length - 2));

            html_string +=          '</p>';

            html_string +=          '<p>';
            html_string +=              '<span class="subheading">Compensation</span> ' +  job.custom_fields.remuneration.charAt(0).toUpperCase() + job.custom_fields.remuneration.slice(1);
            html_string +=          '</p>';
            html_string +=          '<p>';
            html_string +=              '<span class="subheading">Apply By Date</span> ' +  AjaxUSCJobs.date_format(job.custom_fields.apply_by_date);
            html_string +=          '</p>';

            html_string +=  '</article>';

            return html_string;
        }

        var settings = {
            filter_criteria: {
             remuneration: ['#remuneration input:checkbox', 'custom_fields.remuneration'],
             taxonomy_departments: ['#taxonomy_departments input:checkbox', 'taxonomy_departments.ARRAY.slug']
             },
            search: {input: '#search_box' },
            and_filter_on: true,
            id_field: 'id' //Default is id. This is only for usecase
        };

        return FilterJS(jobs, "#usc_jobs_list", view, settings);
    }

    $(document).ready(function() {

        var usc_jobs_as_json = JSON.parse(options.jobs);

        console.log( usc_jobs_as_json[0] );

        AjaxUSCJobs.jobs_gotten( usc_jobs_as_json );

    });

    //call this right away.  don't wait for $(document).ready
    //this one removes the jobs and puts in my new jobs.
    AjaxUSCJobs.remove_wordpress_jobs_for_filterjs_jobs();

    //this one removes the widgets and puts in my widgets
    AjaxUSCJobs.remove_wordpress_widgets_for_filterjs_imposter_widgets();


});
