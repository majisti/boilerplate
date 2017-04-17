let gulp = require('gulp');
let path = require('path');
let webpack = require('webpack');
let webpackStream = require('webpack-stream');
let configuration = require('../webpack/webpack.config');

let root = __dirname + '/../..';

gulp.task('webpack', () => {
    return gulp.src(`${root}/src/Frontend/index.tsx`)
        .pipe(webpackStream(configuration, webpack))
        .pipe(gulp.dest(`${root}/web/assets/js/`))
});