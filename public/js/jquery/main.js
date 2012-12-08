$(document).ready(function() {

    //$('#wrap').css('background-position', $('.accountContent').offset().left + 4 + 'px 0');

    $('.leftBg').css('min-height', $('#wrap').height());

    $(window).resize(function(){
        $('.leftBg').css('min-height', $('#wrap').height());
        //$('#wrap').css('background-position', $('.accountContent').offset().left + 4 + 'px 0');
    });

    //---Home page member buttons--------------------------------------------------------------------------------

    $('.memberButtons input:first').attr('checked', 'checked');


    //---Styling radio and checkbox buttons--------------------------------------------------------------------------------

    $('#remember').checkRadio({
        wrapperClass: 'checkboxWrapper',
        chekedClass: 'checked'
    });

    $('input[type="radio"]').checkRadio({
        wrapperClass: 'radioBoxWrapper',
        chekedClass: 'checked'
    });

    $('.memberButtons label').each(function(){
        $(this).find( '.txt' ).appendTo($(this).find('.radioBoxWrapper'));
    });
    



    $('#uploadAvatar').click(function(){
        $('#avatar').click();
    });

    $('#removeAvatar').click(function(){//deleting avatar from profile
        $.post(

            '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'deleteAvatar': 1},
            function(response){
                window.location.reload();
            }

        );

    });
    

    /* END Gender bar(radio)*/
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

    /*  ACCOUNT SYSTEM   */

    /*$('#singleactions').change(function () {
     id =
     window.location.href = '/message/' . $('#singleactions').val();
     });*/
    /*  ACCOUNT SYSTEM   */

    setInterval(imStillAlive, 60000);
    function imStillAlive() {
        activity = jQuery("#user_activity");
        if(activity.val() == 1) {
            jQuery.ajax({
                url: "/account/offline",
                type: "get",
                success: function(result) {
                    activity.val(0);
                }
            });
            return false;
        }
    }

    jQuery(window).unload( function () {
        activity = jQuery("#user_activity");
        if(activity.val() == 1) {
            jQuery.ajax({
                url: "/account/offline",
                type: "get",
                success: function(result) {
                    activity.val(0);
                }
            });
            return false;
        }
    })

    setInterval(getNewMessagesCount, 60000);
    getNewMessagesCount();
    function getNewMessagesCount() {
        jQuery.ajax({
            url: "/message/count",
            type: "get",
            success: function(result) {
                messagesCount = parseInt(result.messageCount.id);

                if(messagesCount > 0) {
                    if($(".newMessagesCount").length) $(".newMessagesCount").remove();
                    inboxLi = $('.leftNavigation').find($('a[href="/message/inbox"]')).parent();
                    inboxLi.append('<span class="newMessagesCount">' + messagesCount + '</span>')
                }
            }
        });
        return false;
    }

    setInterval(getNewBookingCount, 60000);
    getNewBookingCount();
    function getNewBookingCount() {
        jQuery.ajax({
            url: "/booking/count",
            type: "get",
            success: function(result) {
                bookingCount = parseInt(result.bookingCount.id);
                if(bookingCount > 0) {
                    if($(".newBookingCount").length) $(".newBookingCount").remove();
                    inboxLi = $('.leftNavigation').find($('a[href="/booking"]')).parent();
                    inboxLi.append('<span class="newBookingCount">' + bookingCount + '</span>')
                }
            }
        });
        return false;
    }

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

function messageAction(element_id, action) {
    tmpArr = element_id.split('_');
    message_id = tmpArr[1];
    message = $('#'+ element_id);
    selected = message.val();
    if(selected == "default"){
        return false;
    } else if(selected == "delete"){
        var answer = confirm("Do you realy want remove this message?");
        if (answer) {
            window.location.href = "/message/" + selected + "/message_id/" + message_id + "/current_action/" + action;
        }
    } else {
        window.location.href = "/message/" + selected + "/message_id/" + message_id + "/current_action/" + action;
    }


    return false;
}

/*---PIE - add css3 to ie 7 and ie8 -----------------------------------------------------*/

$(function() {
    if (window.PIE) {
      $('#username-login, #password-login, .button').each(function() {
      PIE.attach(this);
    });//each
  }//if
});

