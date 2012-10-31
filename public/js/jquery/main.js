$(document).ready(function() {

/* Button bar(radio)*/
    $('.buttonBar label').first().addClass('checked');
    $('.buttonBar input').first().prop('checked', true);
    $('.buttonBar input[type="radio"]').change(function(){
        if($(this).is(":checked")){
            $('.buttonBar label').removeClass('checked');
            $(this).parent().addClass('checked');
        }
    });
    /* END Button bar(radio)*/

    $('#birthday').datepicker({
        onSelect: function(dateText, inst) {
            $("input[name='birthday']").val(dateText);
        }
    });

    $(function() {
        $( "#tabs" ).tabs();
    });
});