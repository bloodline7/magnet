const Auth = {

    init: function () {

        $("#signout").click(function () {
            Auth.showSignOut();
        });
    },

    showSignOut: function () {

        modalControl.showModal({title: 'Admin Sign Out', content: 'Do you wanna sign out?', button: 'Sign Out'},
            function () {

              var url = '/'+window.prefix+'/logout';
                $.get(url).done(function () {
                    window.location.href = '/'+window.prefix+'/login';
                }).fail(function () {

                })
            })
    }

}

$(function () {
    Auth.init();
})
