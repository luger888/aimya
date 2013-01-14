$(document).ready(function () {

});

function removeCat(e, id) {


    var answer = confirm("Delete category ?");
    var baseUrl = $('#current_url').val();

    if (answer) {

        $.ajax({
                url:baseUrl + "/admin/index",
                type:"post",
                data:{'deleteCat':id},
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
    $('.categories .formRow:last').after('<div class="formRow clearfix"><input type="text" class="newCat floatLeft"><div class ="delete floatLeft"><input type="button" class = "button-2 delete"  onclick="removeNewCat(this);return false;"></div></div>');
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


function addDur(e) {
    $('#saveDur').removeClass('disable');
    $('.duration .formRow:last').after('<div class="formRow clearfix"><input type="text" class="newCat floatLeft"><div class ="delete floatLeft"><input type="button" class = "button-2 delete"  onclick="removeNewDur(this);return false;"></div></div>');
}

function removeNewDur(e) {
    $(e).parents('.formRow').remove();
}

function saveDur(e) {
    var durations = new Array();
    $('.newCat').each(function (index) {
        if($(this).val()!=''){
            durations.push($(this).val());
        }

    });
    if (durations) {
        var baseUrl = $('#current_url').val();
        $.ajax({
            url:baseUrl + "/admin/index",
            type:"post",
            data:{'durations':durations},
            success:function (response) {
                window.location.reload();

            }
        })
    }

}

function removeDur(e, id) {


    var answer = confirm("Delete period ?");
    var baseUrl = $('#current_url').val();

    if (answer) {

        $.ajax({
                url:baseUrl + "/admin/index",
                type:"post",
                data:{'deleteDur':id},
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
function suspension(e, status){
    if(status == 1){
        var answer = confirm("Suspend user?");
    }
}
