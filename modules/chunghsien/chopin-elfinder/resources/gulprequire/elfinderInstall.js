module.exports = function () {
    gulp.task('elfinderInstall', async function () {
        
        var sourceFolder = 'vendor/studio-42/elfinder';
        var destFolder = 'public/packages/elfinder/';
        
        gulp.src([sourceFolder + '/**[!php]/*{.css,.js,.png}', sourceFolder + '/**[!php]/**/*{.css,.js,.png}']).pipe(gulp.dest(destFolder));
        
        var themeSource = 'modules/chunghsien/chopin-elfinder/resources/packages/elfinder';
        //var themeDest = 'public/packages/elfinder';
        return gulp.src([
            themeSource + '/themes/windows-10/**/*.{css,png}',
            themeSource + '/themes/windows-10/**/**/*.{css,png}',
        ]).pipe(gulp.dest(destFolder+'/themes/windows-10'));
    });
}