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

    /* DatePicker jquery UI */
    $('#birthday').datepicker({ dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "1910:2012" }).val();
    $('#birthday').datepicker({
        onSelect: function(dateText, inst) {
            $("input[name='birthday']").val(dateText);
        }
    });
    /* END DatePicker jquery UI */

    /* TABS with COOKIES jquery UI */
    $(function() {
        var cookieName, $tabs, stickyTab;

        cookieName = 'stickyTab';
        $tabs = $( '#tabs' );

        $tabs.tabs(
            {cache:true,
            load: function (e, ui) {
                $(ui.panel).find(".tab-loading").remove();
                uploadify();
            },
            select: function( e, ui )
            {
                var $panel = $(ui.panel);
                if ($panel.is(":empty")) {
                    $panel.append("<div class='tab-loading'>Loading...</div>")
                }
                $.cookies.set( cookieName, ui.index );
            }
        });

        $leftTabs = $( '#left_tabs' );

        $( "#left_tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#left_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

        $leftTabs.tabs(
            {cache:true,
                load: function (e, ui) {
                    $(ui.panel).find(".tab-loading").remove();
                },
                select: function( e, ui )
                {
                    var $panel = $(ui.panel);
                    if ($panel.is(":empty")) {
                        $panel.append("<div class='tab-loading'>Loading...</div>")
                    }
                }
            });

        stickyTab = $.cookies.get( cookieName );
        if( ! isNaN( stickyTab )  )
        {
            $tabs.tabs( 'select', stickyTab );
        }
    });
    /* END TABS with COOKIES jquery UI */



});
function uploadify(){
    //$(function() {
    $('#file_upload').uploadifive({
        'auto'         : false,
        'formData'     : {'experienceUpload' : 'certificate'},
        'queueID'      : 'queue',
        'folder'        : '/img/uploads/' + $(this).attr('id'),
        'uploadScript' : '/resume/upload',
        'onUploadComplete' : function(file, data) {
        }
    });
}
