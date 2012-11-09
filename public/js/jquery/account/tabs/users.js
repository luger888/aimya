$(document).ready(function() {
});
    /* USER RELATIONS STATUSES: */
var blockedUser = 2;
var removedUser = 3;

    function blockUser(e) {
        var id = $(e).nextAll('input[type=hidden]:first').val();
        var answer = confirm("Block user?");

        if (answer) {
            $.post(

                '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'updateUserId':id, 'status': blockedUser}

            );

        }

        return false;

    }

    function deleteUser(e) {
        var id = $(e).nextAll('input[type=hidden]:first').val();
        var answer = confirm("Warning!" +
            "By deleting User it will be erased" +
            "from your account permanently!" +
            "Are you sure you want to proceed?");

        if (answer) {
            $.post(

                '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'updateUserId': id, 'status': removedUser}

            );

        }

        return false;

    }


