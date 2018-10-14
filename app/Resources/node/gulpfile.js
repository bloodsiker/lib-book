'use strict';

const gulp = require('gulp');
// const stylus = require('gulp-stylus');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const minify = require('gulp-minify');
const cleanCSS = require('gulp-clean-css');
const del = require('del');
const newer = require('gulp-newer');
const autoprefixer = require('gulp-autoprefixer');
const remember = require('gulp-remember');
const path = require('path');
const cached = require('gulp-cached');
const debug = require('gulp-debug');

gulp.task('styles', function () {
    return gulp.src('../../../src/AppBundle/Resources/public/css/**/*.css')
    // return gulp.src('frontend/css/**/*.css')
        .pipe(cached('styles'))
        .pipe(sourcemaps.init())
        .pipe(autoprefixer())
        .pipe(remember('styles'))
        .pipe(sourcemaps.write())
        .pipe(cleanCSS())
        .pipe(concat('all.css'))
        .pipe(gulp.dest('../../../web/public/css'));
});

gulp.task('assets', function () {
    return gulp.src('../../../src/AppBundle/Resources/public/assets/**', {since: gulp.lastRun('assets')})
        .pipe(newer('../../../web/public/assets'))
        // .pipe(debug())
        .pipe(gulp.dest('../../../web/public/assets'))
});

gulp.task('clean', function () {
    return del('../../../web/public', {force: true});
});

gulp.task('build', gulp.series('clean', gulp.parallel('styles', 'assets')));

gulp.task('watch', function () {
    gulp.watch('../../../src/AppBundle/Resources/public/css/**/*.*', gulp.series('styles')).on('unlink', function (filepath) {
        remember.forget('styles', path.resolve(filepath));
        delete cached.caches.styles[path.resolve(filepath)];
    });
});

gulp.task('dev', gulp.series('build', 'watch'));