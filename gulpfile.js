const gulp = require('gulp'),
  sass = require('gulp-ruby-sass'),
  autoprefixer = require('gulp-autoprefixer'),
  cssnano = require('gulp-cssnano'),
  jshint = require('gulp-jshint'),
  imagemin = require('gulp-imagemin'),
  rename = require('gulp-rename'),
  concat = require('gulp-concat'),
  notify = require('gulp-notify'),
  cache = require('gulp-cache'),
  livereload = require('gulp-livereload'),
  gutil = require('gulp-util'),
  del = require('del'),
  uglifyes = require('uglify-es'),
  composer = require('gulp-uglify/composer'),
  uglify = composer(uglifyes, console),
  gulpDeployFtp = require('gulp-deploy-ftp');

gulp.task('scripts', function()
{
  return gulp.src(['jvlm/*.js', '!jvlm/external/*'])
    .pipe(jshint('.jshintrc'))
    .pipe(jshint.reporter('default'))
    .pipe(concat('jvlm_main.js'))
    .pipe(gulp.dest('jvlm/dist'))
    .pipe(rename(
    {
      suffix: '.min'
    }))
    .pipe(uglify())
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    })
    //.pipe(gulpDeployFtp('./vlmcode', 'vlm-dev.ddns.net', 21, 'vlm', 'vlm'))
    .pipe(gulp.dest('jvlm/dist'))
    .pipe(notify(
    {
      message: 'Scripts task complete'
    }));
});