var gulp = require('gulp')
    , sass = require('gulp-sass')
    , sourcemaps = require('gulp-sourcemaps')
    , concat = require('gulp-concat')
    , uglifycss = require('gulp-uglifycss')
    , uglify = require('gulp-uglify')
    , util = require('gulp-util')
    , notify = require("gulp-notify")
    , plumber = require('gulp-plumber')
    , rev = require('gulp-rev')
    , spritesmith = require('gulp.spritesmith')
    , runSequence = require('run-sequence').use(gulp)
    , merge = require('merge-stream')
;

var array_values = function(object) {
    var values = [];
    for( var key in object ) {
        values.push(object[key]);
    }

    return values;
};

gulp.jsFiles = {
    'lodash': 'node_modules/lodash/lodash.js'
};

gulp.task('js', function generateJs(done) {
    return gulp.src(array_values(gulp.jsFiles))
        .pipe(concat('app.js'))
        // .pipe(uglify())
        .on('error', done)
        .pipe(gulp.dest('./web/assets/js'))
    ;
});

gulp.cssFiles = {
};

gulp.scssFiles = {
    'app': './app/sass/screen.scss'
};

gulp.task('css', function generateCss(done) {
    var cssStream = gulp.src(array_values(gulp.cssFiles))
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(concat('vendors.css'))
        // .pipe(uglifycss())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./web/assets/css'))
    ;

    var scssStream = gulp.src(array_values(gulp.scssFiles))
        .pipe(sourcemaps.init())
        .pipe(concat('styles.css'))
        .pipe(sass({
            // outputStyle: 'compressed'
        }))
        .on('error', done)
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./web/assets/css'))
    ;

    return merge(cssStream, scssStream);
});

gulp.watchedJsFiles = {
    'js': './web/assets/js/*.js'
};

gulp.watchedScssFiles = {
    'scss': './app/sass/**/*.scss'
};

gulp.task('default', ['js'], function runAsSequence() {
    runSequence(['css'])
});

gulp.task('watch', ['js', 'css'], function watchAssets() {
    gulp.watch(array_values(gulp.watchedJsFiles), ['js']);
    gulp.watch(array_values(gulp.watchedScssFiles), ['scss']);
});
