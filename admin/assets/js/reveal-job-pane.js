/**
 * The only thing this file exists to do is reveal or hide a pane in the USC_Job_MetaBox depending on a prior value that
 * was selected.
 *
 * If the position is paid, reveal the 'position' pane which presents all of the types of paid positions.
 * Else, if it's a volunteer position, we don't need to be any more specific
 */
jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

    var $remuneration_row           = $('#fieldrow-remuneration');
    var $remuneration_radio_buttons = $remuneration_row.find('#field-remuneration__0');
    var $radio_button_paid          = $remuneration_radio_buttons.find('#remuneration__0_paid');
    var $position_row               = $remuneration_row.next();

    $(document).ready(function() {

        check_paid();
    });

    //one method checks if the field is checked

    /**
     * OnClick function that calls 'check_paid' every time a radio button in the 'remuneration section is clicked'
     */
    $remuneration_radio_buttons.on('click', function() {

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