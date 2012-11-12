$(document).ready(function () {
    /*  FILTER SYSTEM   */
    $.urlParam = function (name) {
        var results = new RegExp('[\\?\&]' + name + '=([^\&#]*)').exec(window.location.href);//getting params from url
        if (results)
            return results[1] || 0;
    };
    var user = $.urlParam('user');
    var category = $.urlParam('category');
    $('#author').val(user);
    $('#lesson_category').val(category);
    $('#author, #lesson_category').change(function () {
        window.location.href = 'features?user=' + $('#author').val() + '&category=' + $('#lesson_category').val();//reload url after selecting category

    });
    /*  END -- FILTER SYSTEM    */
});