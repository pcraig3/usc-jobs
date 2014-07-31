/**
 * Created by Paul on 31/07/14.
 */


jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

    $(document).ready(function() {

        checked_paid();
        // Code here will be executed on document ready. Use $ as normal.
    });

    $('#field-renumeration__0').on('click', function() {

        checked_paid();
    });


    function checked_paid() {

        var paid_is_checked = $('#renumeration__0_paid').is(":checked");

        if(paid_is_checked) {

            $('#fieldrow-position').show();
        }
        else {

            $('#fieldrow-position').hide().find('input').prop('checked', false);
        }
    }
});