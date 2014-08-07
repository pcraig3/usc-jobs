jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

    AjaxEvents.ajax_events_gotten = function( jobs, limit ) {

        $('.filterjs__loading').addClass('hidden');

        //ideally, you cut down on the event array before processing it, but the API is making that harder.
        //events = AjaxEvents.limit_events( jobs, limit );

        fJS = filterInit( jobs );

        $('#event_list').trigger( "change" );
    };

    /**
     * Function sets up all of our filtering.
     * Works now, but seems a bit brittle.
     *
     * @param events    a list of events. Data from FB is merged with information from our database.
     *
     * @since   0.4.0
     *
     * @returns {*} A list of searchable events in the backend.
     */
    function filterInit( jobs ) {

        var view = function( job ){

            //at this point we have ONE JOB.  This sets up the loop.
            var html_string = '<p>' + job.title + '</p>';

            return html_string;
        }

        var settings = {
            /*filter_criteria: {
             removed: ['#removed :checkbox', 'removed']
             },*/
            search: {input: '#search_box' },
            //and_filter_on: true,
            id_field: 'id' //Default is id. This is only for usecase
        };

        return FilterJS(jobs, "#event_list", view, settings);
    }

    $(document).ready(function($) {

        var usc_jobs_as_json = JSON.parse(options.jobs);

        console.log( usc_jobs_as_json[0] );
        console.log( usc_jobs_as_json[0]['id'] );

        AjaxEvents.ajax_events_gotten( usc_jobs_as_json, 0 );
    });

});

