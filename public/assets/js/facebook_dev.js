(() => {
    if (pageConfig.system_settings.facebook_dev) {
        let fb_params = pageConfig.system_settings.facebook_dev.children;
        window.fbAsyncInit = function() {
            FB.init({
                appId: fb_params.fb_colon_app_id.value,
                cookie: true,
                xfbml: true,
                version: 'v8.0'
            });

            FB.AppEvents.logPageView();
        };
        const __LANGUAGE__ = navigator.language || navigator.userLanguage;
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) { return; }
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/" + __LANGUAGE__ + "/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    };
})();
