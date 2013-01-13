$(document).ready(function () {

    $('.alert-block .close').click(function(){
        $(this).parents('.alert-block').remove();
    });

    //timepicker

    $(function(){

        $('#started_at_time').scroller({ preset: 'time', theme: 'android-ics', ampm: false, timeFormat: 'HH:ii' });
    });
//    $.ajaxSetup({
//        beforeSend: function(jqXHR, settings) {
//            jQuery("body").append('<div class="loadingIcon"></div>');
//        },
//        complete:function(jqXHR, settings) {
//            jQuery('.loadingIcon').remove();
//        }
//    });
//---Main navigation--------------------------------------------------------------------------------
    $("nav ul li >ul").hover(function () {
        $('.last>a').addClass('liHover');
    });
    $("nav ul li >ul").mouseleave(function () {
        $('.last>a').removeClass('liHover');
    });
    //---Home page member buttons--------------------------------------------------------------------------------

    $('.memberButtons input:first').attr('checked', 'checked');


    //---Styling radio and checkbox buttons--------------------------------------------------------------------------------

    $('input[type="checkbox"]').checkRadio({
        wrapperClass:'checkboxWrapper',
        chekedClass:'checked'
    });

    $('input[type="radio"]').checkRadio({
        wrapperClass:'radioBoxWrapper',
        chekedClass:'checked'
    });

    $('.memberButtons label').each(function () {
        $(this).find('.txt').appendTo($(this).find('.radioBoxWrapper'));
    });

    jQuery.fn.shorten = function(settings) {
        var config = {
            showChars : 100,
            ellipsesText : "...",
            moreText : "more",
            lessText : "less"
        };

        if (settings) {
            $.extend(config, settings);
        }

        $('.morelink').live('click', function() {
            var $this = $(this);
            if ($this.hasClass('less')) {
                $this.removeClass('less');
                $this.html(config.moreText);
            } else {
                $this.addClass('less');
                $this.html(config.lessText);
            }
            $this.parent().prev().toggle();
            $this.prev().toggle();
            return false;
        });

        return this.each(function() {
            var $this = $(this);

            var content = $this.html();
            if (content.length > config.showChars) {
                var c = content.substr(0, config.showChars);
                var h = content.substr(config.showChars , content.length - config.showChars);
                var html = c + '<span class="moreellipses">' + config.ellipsesText + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="morelink">' + config.moreText + '</a></span>';
                $this.html(html);
                $(".morecontent span").hide();
            }
        });
    };
//---Styling uploads--------------------------------------------------------------------------------
    $('#uploadAvatar').click(function () {
        $('#avatar').click();
    });


    $('#removeAvatar').click(function () {//deleting avatar from profile
        pathName = $('#current_url').val();
        $.post(
            pathName + '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'deleteAvatar':1},
            function (response) {
                window.location.reload();
            }

        );

    });


    /* END Gender bar(radio)*/
    /* DatePicker jquery UI */
    $('#birthday').datepicker({ dateFormat:'yy-mm-dd',
        changeMonth:true,
        changeYear:true,
        yearRange:"1910:2012" }).val();
    $('#birthday').datepicker({
        onSelect:function (dateText, inst) {
            $("input[name='birthday']").val(dateText);
        }
    });

    $('#fromPeriod').datepicker({ dateFormat:'yy-mm-dd',
        changeMonth:true,
        changeYear:true,
        yearRange:"1910:2012" }).val();
    $('#toPeriod').datepicker({ dateFormat:'yy-mm-dd',
        changeMonth:true,
        changeYear:true,
        yearRange:"1910:2012" }).val();
    /* END DatePicker jquery UI */
    $('#starsBlock').raty();//stars rating
    $('.stars').raty({
        score: function() {
            return $(this).attr('data-rating');
        },
        readOnly:true
    });
    /* TABS with COOKIES jquery UI */
    $(function () {
        var cookieName, $tabs, stickyTab;

        cookieName = 'stickyTab';
        $tabs = $('#tabs');

        $tabs.tabs(
            {cache:true,
                load:function (e, ui) {
                    $(ui.panel).find(".tab-loading").remove();
//                    uploadify();
                },
                select:function (e, ui) {
                    var $panel = $(ui.panel);
                    if ($panel.is(":empty")) {
                        $panel.append("<div class='tab-loading'>Loading...</div>")
                    }
                    $.cookies.set(cookieName, ui.index);
                }
            });

        $leftTabs = $('#left_tabs');

        $("#left_tabs").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
        $("#left_tabs li").removeClass("ui-corner-top").addClass("ui-corner-left");

        $leftTabs.tabs(
            {cache:true,
                load:function (e, ui) {
                    $(ui.panel).find(".tab-loading").remove();
                },
                select:function (e, ui) {
                    var $panel = $(ui.panel);
                    if ($panel.is(":empty")) {
                        $panel.append("<div class='tab-loading'>Loading...</div>")
                    }
                }
            });

        stickyTab = $.cookies.get(cookieName);
        if (!isNaN(stickyTab)) {
            $tabs.tabs('select', stickyTab);
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
        pathName = $('#current_url').val();
        if (activity.val() == 1) {
            jQuery.ajax({
                url:pathName + "/account/offline",
                type:"get",
                success:function (result) {
                    //console.log(result.userStatus);
                }
            });
            return false;
        }
    }

    jQuery(window).unload(function () {
        pathName = $('#current_url').val();
        activity = jQuery("#user_activity");
        if (activity.val() == 1) {
            jQuery.ajax({
                url:pathName + "/account/offline",
                type:"get",
                success:function (result) {
                    activity.val(0);
                }
            });
            return false;
        }
    })

    setInterval(getNewMessagesCount, 60000);
    getNewMessagesCount();
    function getNewMessagesCount() {
        pathName = $('#current_url').val();
        jQuery.ajax({
            url:pathName + "/message/count",
            type:"get",
            success:function (result) {
                messagesCount = parseInt(result.messageCount.id);

                if (messagesCount > 0) {
                    if ($(".newMessagesCount").length) $(".newMessagesCount").remove();
                    inboxLi = $('.leftNavigation').find($('a[href="' + pathName + '/message/inbox"]')).parent();
                    inboxLi.append('<span class="newMessagesCount">' + messagesCount + '</span>')
                }
            }
        });
        return false;
    }

    setInterval(getNewBookingCount, 30000);
    getNewBookingCount();
    function getNewBookingCount() {
        var pathName = $('#current_url').val();

        jQuery.ajax({
            url:pathName + "/booking/count",
            type:"get",
            success:function (result) {
                var bookingCount = parseInt(result.bookingCount.id);

                if (bookingCount > 0) {
                    if ($(".newBookingCount").length) $(".newBookingCount").remove();
                    var inboxLi = $('.leftNavigation').find($('a[href="' + pathName + '/booking"]')).parent();
                    inboxLi.append('<span class="newBookingCount">' + bookingCount + '</span>')
                }

                if (result.bookingPaymentStatus.booking) {
                    var paymentTd = $('td.payment');
                    var lessonLi = $('.leftNavigation').find($('a[href="' + pathName + '/lesson"]')).parent();
                    lessonLi.append('<span class="newBookingCount">' + $(result.bookingPaymentStatus.booking).length + '</span>');
                    var scheduled = $('.messageTabs').find($('a[href="' + pathName + '/lesson"]')).parent();
                    scheduled.append('<span class="newBookingCount">' + $(result.bookingPaymentStatus.booking).length + '</span>');
                    var lesson = $('.userRelationsTab.lesson .table .username').find($('input[value="' + result.bookingPaymentStatus.booking.id + '"]')).parent();
                    if (result.bookingPaymentStatus.booking.pay) {
                        var payButton = result.bookingPaymentStatus.booking.pay;
                        for (var i = 0; i < $(payButton).length; i++) {

                            lesson.append('<span class="newBookingCount">!</span>');

                            var payButtonCode = '<form method="POST" id="pay_55' /*+ result.bookingPaymentStatus.booking.id */+
                                '" action="' + pathName + '/payment/pay/">' +
                                '<input name="teacher_id" type="hidden" value="' + result.bookingPaymentStatus.userdata.id + '">' +
                                '<input name="booking_id" type="hidden" value="' + result.bookingPaymentStatus.booking.id + '">' +
                                '<a href="#" id="' + result.bookingPaymentStatus.userdata.id + '"' +
                                'onclick=payMoney(this)><span class="button play">Pay</span></a>' +
                                '</form>';
                            lesson.parents('tr:first').find(paymentTd).html(payButtonCode);

                        }
                    }
                    if (result.bookingPaymentStatus.booking.send) {
                        for (var d = 0; d < $(result.bookingPaymentStatus.booking.send).length; d++) {
                            var sendButtonCode = '<input type="button" class="button send"' +
                                ' onclick="payRequest(' + result.bookingPaymentStatus.userdata.id + ',' + result.bookingPaymentStatus.booking.id + ', this);" value = "send">';
                            lesson.parents('tr:first').find(paymentTd).html(sendButtonCode);
                        }
                    }
                    if (result.bookingPaymentStatus.booking.paid) {
                        for (var v = 0; v < $(result.bookingPaymentStatus.booking.paid).length; v++) {
                            var  paidCode = '<span class="result success">Paid</span>';
                            lesson.parents('tr:first').find(paymentTd).html(paidCode);
                        }
                    }
                    if (result.bookingPaymentStatus.booking.start) {
                        var actionTd = $('td.action');
                        for (var s = 0; s < $(result.bookingPaymentStatus.booking.start).length; s++) {
                            var startButtonCode = '<form method="POST"'+
                            '" action="' + pathName + '/lesson/setup/">' +
                                '<input name="student_id" type="hidden" value="' + result.bookingPaymentStatus.userdata.id + '">'+
                                    '<input name="booking_id" type="hidden" value="' + result.bookingPaymentStatus.booking.id + '">'+
                                        '<a href="#" id="' + result.bookingPaymentStatus.userdata.id + '"'+
                                        'onclick=startLesson(this)><span class="button-2 play">Start</span></a>'+
                                    '</form>';
                            lesson.parents('tr:first').find(actionTd).html(startButtonCode);
                        }
                    }

                    if (result.bookingPaymentStatus.booking.join && window.location.pathname == pathName + "/lesson") {

                        window.location.href =  pathName + '/lesson/join/';
                    }
                }
            }
        });
        return false;
    }

//LESSON DETAILS//
    $("#notes-dialog").dialog({
        autoOpen:false,
        height: 'auto',
        width:450,
        modal:true,
        resizable:false,
        close:function () {

        }
    });

    $("#details-dialog").dialog({
        autoOpen:false,
        height: 'auto',
        width:450,
        modal:true,
        resizable:false,
        close:function () {

        }
    });

    // END LESSON DETAILS//
});
//function uploadify(){
//    //$(function() {
//    $('#file_upload').uploadifive({
//        'auto'         : false,
//        'formData'     : {'experienceUpload' : 'certificate'},
//        'queueID'      : 'queue',
//        'uploadScript' : '/resume/upload',
//        'onUploadComplete' : function(file, data) {
//        }
//    });
//}
function payMoney(e) {

    $(e).parent().submit();
}
function startLesson(e) {

    $(e).parent().submit();
}
//LESSONS
function payRequest(id, booking, e) {
    var element = $(e);
    var baseUrl = $('#current_url').val();
    jQuery("body").append('<div class="loadingIcon"></div>');
    $.post(

        baseUrl + "/lesson/pay/0/controller%3D%3Elesson/1/action%3D%3Epay", {

            "friend_id":id,
            "booking_id":booking

        },

        function (response) {
            jQuery('.loadingIcon').remove();
            $(e).parent().html('<span class="result pending">Pending</span>');

        })
}
function messageAction(element_id, action) {
    tmpArr = element_id.split('_');
    message_id = tmpArr[1];
    message = $('#' + element_id);
    selected = message.val();
    if (selected == "default") {
        return false;
    } else if (selected == "delete") {
        var answer = confirm("Do you realy want remove this message?");
        if (answer) {
            window.location.href = "/message/" + selected + "/message_id/" + message_id + "/current_action/" + action;
        }
    } else {
        window.location.href = "/message/" + selected + "/message_id/" + message_id + "/current_action/" + action;
    }


    return false;
}

function massTrash(current_action, url) {
    jQuery("body").append('<div class="loadingIcon"></div>');
    ids = [];
    $('.messageCheckboxes:checkbox:checked').each(function () {
        ids.push($(this).val());
    });

    idsString = ids.toString();
    $.ajax({
        url:url + "/message/masstrash",
        type:"post",
        data:{
            'message_ids':idsString,
            'current_action':current_action
        },
        success:function (result) {
            jQuery('.loadingIcon').remove();
            window.location.href = url + "/message/" + current_action + "/current_action/" + current_action;
        }
    });

}

function massDelete(current_action, url) {
    jQuery("body").append('<div class="loadingIcon"></div>');
    ids = [];
    $('.messageCheckboxes:checkbox:checked').each(function () {
        ids.push($(this).val());
    });

    idsString = ids.toString();
    $.ajax({
        url:url + "/message/massdelete",
        type:"post",
        data:{
            'message_ids':idsString,
            'current_action':current_action
        },
        success:function (result) {
            jQuery('.loadingIcon').remove();
            window.location.href = url + "/message/" + current_action + "/current_action/" + current_action;
        }
    });

}

function massArchive(current_action, url) {
    jQuery("body").append('<div class="loadingIcon"></div>');
    ids = [];
    $('.messageCheckboxes:checkbox:checked').each(function () {
        ids.push($(this).val());
    });

    idsString = ids.toString();
    $.ajax({
        url:url + "/message/massarchive",
        type:"post",
        data:{
            'message_ids':idsString,
            'current_action':current_action
        },
        success:function (result) {
            jQuery('.loadingIcon').remove();
            window.location.href = url + "/message/" + current_action + "/current_action/" + current_action;
        }
    });
}

function setDefaultTimezone() {
    var baseUrl = $('#current_url').val();
    var d=new Date(Date.now()); // sets your date to variable d

    function repeat(str,count) { // EXTENSION
        return new Array(count+1).join(str);
    };

    function padLeft(str,length,char) { // EXTENSION
        return length<=str.length ? str.substr(0,length) : repeat(String(char||" ").substr(0,1),length-str.length)+str;
    };

    var str=padLeft(String(d.getFullYear()),4,"0")+"-"+
        padLeft(String(d.getMonth()),2,"0")+"-"+
        padLeft(String(d.getDate()),2,"0")+"T"+
        padLeft(String(d.getHours()),2,"0")+":"+
        padLeft(String(d.getMinutes()),2,"0")+":"+
        padLeft(String(d.getSeconds()),2,"0")+"."+
        d.getMilliseconds();
    //str+=" GMT";
    var o=d.getTimezoneOffset(),s=o<0?"+":"-",h,m;
    h=Math.floor(Math.abs(o)/60);
    m=Math.abs(o)-h*60;

    str+=" "+s+padLeft(String(h),2,"0")+padLeft(String(m),2,"0");
    var str = str.substr(str.length - 5);
    var first = str.slice(0, 3);
    var second = str.slice(3, 5);
    var newTimeZone = first + ':' + second;

    $.ajax({
        'url':baseUrl + '/user/timezone/',
        'data':({timezone: newTimeZone}),
        'dataType':'json',
        'type':'post',
        success:function (data) {
            if(data.status == 'success') {
                $("#timezone").val(newTimeZone);
            }
        }
    });
}



function getNotes(e,id){
    var pathName = $('#current_url').val();
    jQuery("body").append('<div class="loadingIcon"></div>');
    $.ajax({
        url:pathName + "/lesson/correspondence",
        type:"post",
        data:{
            'lesson_id':id
        },
        success:function (result) {
            jQuery('.loadingIcon').remove();
            $("#notes-dialog").dialog("open");
            $('.notesWindow').html(result.notes);
            var parent = $(e).parents('tr');
            var id = parent.find('input[type=hidden]').val();

            var focusName = parent.find('.focus');
            var dateLesson = parent.find('.date');
            $('.focusDialog').html(focusName.text());
            $('.dateDialog').html(dateLesson.text());
            $('.note:nth-child(even)').append('<div class ="smallSeparatorBot"></div>');
            $('.note:nth-child(even)').prepend('<div class ="smallSeparatorTop"></div>');
            $('.dialogFooter').prepend('<input type="hidden" name="les_id" value = "'+id+'">');
            $('.timeLeftSpan').html(result.date + ' ');
            if(result.review){
                $('#sendRating').remove();
                $('.timeLeft').remove();
                $('.comment').html('Comment: '+ result.review);
                $('#starsBlock').raty({
                    readOnly : true,
                    score    : result.rate
                });
            }
        }
    });
}

function openFeedback(e, lessonId){

    var pathName = $('#current_url').val();
    jQuery("body").append('<div class="loadingIcon"></div>');
    $.ajax({
        url:pathName + "/feedback/view",
        data: {
            'lessonId': lessonId
        },
        type:"post",
        success:function (result) {
            if(result.feedback.content){

                element = $(result.html);


                element.find('#starsBlockFeedback').raty();
                var parent = $(e).parents('tr');
                var focusName = parent.find('.focus');
                var dateLesson = parent.find('.date');
                element.find('.notesWindow').html(result.feedback.content);
                var id = parent.find('input[type=hidden]').val();
                element.find('.focusDialog').html(focusName.text());
                element.find('.dateDialog').html(result.feedback.created_at);

                jQuery('.loadingIcon').remove();
                //element.find('#feedbackDiv').html(result.feedback.content);
                //alert(result.feedback.content);
                $("#details-dialog").html(element);
                $("#details-dialog").dialog("open");
                element.find('.dialogFooter').prepend('<input type="hidden" name="les_id" value = "'+id+'">');
                element.find('.timeLeftSpan').html(result.date + ' ');
                if(result.review){
                    element.find('#sendRating').remove();
                    element.find('.timeLeft').remove();
                    element.find('.comment').html('Comment: '+ result.review);
                    element.find('#starsBlockFeedback').raty({
                        readOnly : true,
                        score    : result.rate
                    });
                }
                return false;
            }
        }
    });
}

function getFeedbackForm(e, lessonId){

    var pathName = $('#current_url').val();
    jQuery("body").append('<div class="loadingIcon"></div>');
    $.ajax({
        url:pathName + "/feedback/form",
        data: {
            'lessonId': lessonId
        },
        type:"post",
        success:function (result) {
            jQuery('.loadingIcon').remove();
            if(result.html){
                element = $(result.html)
                jQuery('.loadingIcon').remove();
                $("#details-dialog").html(element)
                $("#details-dialog").dialog("open");
                return false;
            }
        }
    });
}

function sendRating(e){
    var pathName = $('#current_url').val();
    var parent = $(e).parents('.dialogFooter');
    var id = parent.find('input[name=les_id]').val();
    var rating = parent.find('input[name=score]').val();
    var review = parent.find('input.rateInput').val();
    jQuery("body").append('<div class="loadingIcon"></div>');
    $.ajax({
        url:pathName + "/lesson/review",
        type:"post",
        data:{
            'lesson_id':id,
            'rating':rating,
            'review':review
        },
        success:function (result) {
            jQuery('.loadingIcon').remove();
                if(result.success){
                    $('.comment').html('Comment: '+ review);
                    $('#starsBlock').raty({
                        readOnly : true,
                        score    : rating
                    });
                    $(e).remove();
                }
        }
    });
}