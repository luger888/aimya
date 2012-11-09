$(document).ready(function () {
    /*  FILTER SYSTEM   */
    $.urlParam = function (name) {
        var results = new RegExp('[\\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
        if (results)
            return results[1] || 0;
    };
    $('#author').val($.urlParam('user'));
    $('#lesson_category').val($.urlParam('category'));
    $('#author, #lesson_category').change(function () {
        window.location.href = 'features?user=' + $('#author').val() + '&category=' + $('#lesson_category').val();

    });
    /*  END -- FILTER SYSTEM    */
});