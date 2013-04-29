
window.fbAsyncInit = function () {
    //api init
    FB.init({

        appId: '161632220670499', // App ID
        channelUrl: 'http://aimya.com', // Channel File
        status: true, // check login status
        cookie: true, // enable cookies to allow the server to access the session
        xfbml: true  // parse XFBML

    });

    //is logged on facebook
    FB.getLoginStatus(function (response) {

        if (response.status === 'connected') {

            FB.api('/me', function (response) {

                //whatever

            });


        }

    });


};


// Load the SDK Asynchronously
(function (d) {
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement('script');
    js.id = id;
    js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));


function wallPost(picture, review, name, link) {

    FB.ui(
        {
            method: 'feed',
            name: name,
            link: link,
            picture: picture,
            description: review
        },

        function (response) {

            if (response && response.post_id) {

                //

            }
        }
    );
}

//facebook logout API
function fb_logout() {

    FB.logout(function (response) {
    });
    return false;

}

//facebook login API
function fb_login() {

    FB.login(function (response) {

        if (response.authResponse) {

            FB.api('/me', function (response) {
                var baseUrl = $('#current_url').val();
                $.ajax({
                        url: baseUrl + "/user/fb",
                        type: "post",
                        data: {
                            data: response
                        },
                        success: function (result) {
                            if( result.success == 1){
                                window.location.href = baseUrl + "/account/features";
                            }
                        }
                    }
                );

            });

        } else {
            //user hit cancel button
            console.log('User cancelled login or did not fully authorize.');

        }
    }, {scope: 'email'});

    return false;
}