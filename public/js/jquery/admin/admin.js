$(document).ready(function () {

});

function removeCat(e, id) {


    var answer = confirm("Delete category ?");
    var baseUrl = $('#current_url').val();

    if (answer) {

        $.ajax({
                url:baseUrl + "/admin/index",
                type:"post",
                data:{'deleteId':id},
                success:function (response) {
                    if (response.result) {
                        $(e).parents('.formRow').remove();
                    }

                }
            }
        );
    }
    return false;
}

function addCat(e) {
    $('#saveCat').removeClass('disable');
    $('.formRow:last').after('<div class="formRow clearfix"><input type="text" class="newCat floatLeft"><div class ="delete floatLeft"><input type="button" class = "button-2 delete"  onclick="removeNewCat(this);return false;"></div></div>');
}

function removeNewCat(e) {
    $(e).parents('.formRow').remove();
}

function saveCat(e) {
    var categories = new Array();
    $('.newCat').each(function (index) {
        if($(this).val()!=''){
            categories.push($(this).val());
        }

    });
    if (categories) {
        var baseUrl = $('#current_url').val();
        $.ajax({
            url:baseUrl + "/admin/index",
            type:"post",
            data:{'categories':categories},
            success:function (response) {
                    window.location.reload();

            }
        })
    }

}