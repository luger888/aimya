$(document).ready(function() {

});
    /* USER RELATIONS STATUSES: */
var blockedUser = 2;
var removedUser = 3;

    function blockUser(e, url) {
        var id = $(e).nextAll('input[type=hidden]:first').val();
        $(function() {
            $( "#block-confirm" ).dialog({
                resizable: false,
                height:140,
                width:360,
                modal: true,
                buttons: {
                    "Yes": function() {
                        $( this ).dialog( "close" );
                        $.ajax({
                            url: url,
                            type: "post",
                            data: {
                                'updateUserId':id, 'status': blockedUser
                            },
                            success: function(response){
                                //window.location.reload();
                            }
                        });
                    },
                    No: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            $('.ui-icon-alert').remove();
        });






        return false;

    }

    function deleteUser(e, url) {

        var id = $(e).nextAll('input[type=hidden]:first').val();
            $( "#delete-confirm" ).dialog({
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "Yes": function() {
                        $( this ).dialog( "close" );
                        $.ajax({
                            url: url,
                            type: "post",
                            data: {
                                'updateUserId': id,
                                'status': removedUser
                            },
                            success: function(response){
                                window.location.reload();
                            }
                        });
                    },
                    No: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            $('.ui-icon-alert').remove();


        return false;

    }


