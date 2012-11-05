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
    $('#birthday').datepicker({ dateFormat: 'yy-mm-dd' }).val();
    $('#birthday').datepicker({
        onSelect: function(dateText, inst) {
            $("input[name='birthday']").val(dateText);
        }
    });

    $(function() {
        var cookieName, $tabs, stickyTab;

        cookieName = 'stickyTab';
        $tabs = $( '#tabs' );

        $tabs.tabs( {
            select: function( e, ui )
            {
                $.cookies.set( cookieName, ui.index );
            }
        } );

        stickyTab = $.cookies.get( cookieName );
        if( ! isNaN( stickyTab )  )
        {
            $tabs.tabs( 'select', stickyTab );
        }
    });




});

function addService(){

    $("#addService").css('display', 'block');
}

