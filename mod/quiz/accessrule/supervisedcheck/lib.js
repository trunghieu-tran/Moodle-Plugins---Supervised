/**
 * Created by AndreyU on 24.11.13.
 */


$(document).ready(function(){
    $('#fgroup_id_lessontypesgroup :input').attr('disabled', true);

    $('#fgroup_id_radioar :input').change(
        function(){
            if ($('#id_supervisedcheckrequired_2').is(':checked')) {
                $('#fgroup_id_lessontypesgroup :input').removeAttr('disabled');
            } else {
                $('#fgroup_id_lessontypesgroup :input').attr('disabled', true);
            }
        }
    );
});