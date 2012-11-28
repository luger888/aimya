$(document).ready(function() {
});
    /* USER RELATIONS STATUSES: */
var blockedUser = 2;
var removedUser = 3;

    function blockUser(e) {
        var id = $(e).nextAll('input[type=hidden]:first').val();
        var answer = $(function() {
            $( "#block-confirm" ).dialog({
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "Yes": function() {
                        $( this ).dialog( "close" );
                    },
                    No: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            $('.ui-icon-alert').remove();
        });
alert(answer)
        if (answer) {
            $.post(

                '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'updateUserId':id, 'status': blockedUser}

            );

        }

        return false;

    }

    function deleteUser(e) {
        var id = $(e).nextAll('input[type=hidden]:first').val();
        var answer = $(function() {
            $( "#delete-confirm" ).dialog({
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "Yes": function() {
                        $( this ).dialog( "close" );
                    },
                    No: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            $('.ui-icon-alert').remove();
        });

        if (answer) {
            $.post(

                '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'updateUserId': id, 'status': removedUser},
                function(response){
                    window.location.reload();
                }

            );

        }

        return false;

    }


