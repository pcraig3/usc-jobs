/**
 * Created by Paul on 31/07/14.
 */


jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

    var $renumeration_row           = $('#fieldrow-renumeration');
    var $renumeration_radio_buttons = $renumeration_row.find('#field-renumeration__0');
    var $radio_button_paid          = $renumeration_radio_buttons.find('#renumeration__0_paid');
    var $position_row               = $renumeration_row.next();

    $(document).ready(function() {

        check_paid();
    });

    //one method checks if the field is checked

    /**
     * OnClick function that calls 'check_paid' every time a radio button in the 'renumeration section is clicked'
     */
    $renumeration_radio_buttons.on('click', function() {

        check_paid();
    });


    /**
     * Pretty straightforward function.
     * If, at the time the method runs, 'paid' is clicked, then reveal the following pane full of input buttons.
     * If 'paid' is not clicked, hide the pane and uncheck all of its inputs.
     */
    function check_paid() {

        var paid_is_checked = $radio_button_paid.is(":checked");

        if(paid_is_checked) {

            $position_row.removeClass('hidden');
        }
        else {

            $position_row.addClass('hidden').find('input').prop('checked', false);
        }
    }

});