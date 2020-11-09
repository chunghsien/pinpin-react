//webpack plugin

const fs = require('fs');

class AfterDonePlugin {
    apply(compiler) {
        //stats is passed as an argument when done hook is tapped.
        compiler.hooks.done.tap('AfterDonePlugin', (stats) => {
            const ADMIN_SOURCE = 'public/admin-default.html.twig';
            const ADMIN_DESC = 'resources/templates/app/admin-default.html.twig';
            fs.rename(
                ADMIN_SOURCE,
                ADMIN_DESC,
                function(err) {
                    if(!err) {
                        console.log(ADMIN_DESC);
                    }
                }
            );
            const SITE_SOURCE = 'public/site-default.html.twig';
            const SITE_DESC = 'resources/templates/app/site-default.html.twig';
            fs.rename(
                SITE_SOURCE,
                SITE_DESC,
                function(err) {
                    if(!err) {
                        console.log(SITE_DESC);
                    }
                });

        });
    }
}
module.exports = AfterDonePlugin;