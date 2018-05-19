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
  ftp = require('vinyl-ftp'),
  log= require('fancy-log'),
  runsequence = require('run-sequence');

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

gulp.task('libs', function()
{
  return gulp.src(['jvlm/external/jquery/jquery-3.2.1.min.js',
  'jvlm/external/jquery-ui/jquery-ui.js','jvlm/external/bootstrap-master/js/bootstrap.js',
  'jvlm/external/jquery.csv.js','jvlm/external/bootstrap-colorpicker-master/js/bootstrap-colorpicker.js',
  'jvlm/external/footable-bootstrap/js/footable.js','jvlm/jquery.ui.touch-punch.js',
  'jvlm/external/store/store.min.js',
  'jvlm/external/verimail/verimail.jquery.min.js','jvlm/external/PasswordStrength/jquery.pstrength-min.1.2.js',
  'jvlm/external/moments/moment.min.js','jvlm/externals/fullcalendar/fullcalendar.min.js',
  'jvlm/externals/fullcalendar/locale-all.js','jvlm/external/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'
])
    //.pipe(jshint('.jshintrc'))
    //.pipe(jshint.reporter('default'))
    .pipe(concat('jvlm_libs.js'))
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
      message: 'Minify Libs task complete'
    }));
});




gulp.task('deploy', function()
{

  var conn = ftp.create(
  {
    host: 'vlm-dev.ddns.net',
    user: 'vlm',
    password: 'vlm',
    parallel: 1,
    reload: true,
    log: log
  });

  var globs = [
    'jvlm/dist/*'
  ];

  // using base = '.' will transfer everything to /public_html correctly
  // turn off buffering in gulp.src for best performance

  return gulp.src(globs,
    {
      cwd: '/home/vlm/vlmcode',
      buffer: false
    })
    .pipe(conn.newerOrDifferentSize('jvlm/dist')) // only upload newer files
    .pipe(conn.dest('jvlm/dist')
    .pipe(notify(
      {
        message: 'Upload task complete'
      })));

});

gulp.task('default', function()
{
  return runsequence('scripts', 'deploy');
});