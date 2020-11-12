
const uglify = require('gulp-uglify-es').default;

module.exports = function () {
    gulp.task('elfinderCompile', function () {
        gulp.src([
            'modules/chunghsien/chopin-elfinder/resources/packages/elfinder/css/*.css']
        ).pipe(cleanCSS()).pipe(gulp.dest('public/packages/elfinder/css'));
        
        return gulp.src([
            'modules/chunghsien/chopin-elfinder/resources/packages/elfinder/js/*.js'
        ]).pipe(uglify()).pipe(gulp.dest('public/packages/elfinder/js'));
        
    });
};