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
  log = require('fancy-log'),
  //debug = require('gulp-debug'),
  inject = require('gulp-inject-string'),
  htmlmin = require('gulp-htmlmin'),
  runsequence = require('run-sequence'),
  gulpif = require('gulp-if'),
  babel = require('gulp-babel');

const VLMVersion = 19.2;
var BuildTypeProd = false; // This is automatically set to true in prod build chains


gulp.task('scripts', function()
{
  return gulp.src(['jvlm/*.js', '!jvlm/external/*', '!jvlm/config.js'])
    .pipe(jshint('.jshintrc'))
    .pipe(jshint.reporter('default'))
    .pipe(concat('jvlm_main.js'))
    .pipe(babel(
    {
      presets: ['@babel/env']
    }))
    .pipe(gulpif(BuildTypeProd, uglify()))
    .pipe(gulp.dest('jvlm/dist'))
    .pipe(rename(
    {
      suffix: '.min'
    }))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    })
    //.pipe(gulpDeployFtp('./vlmcode', 'vlm-dev.ddns.net', 21, 'vlm', 'vlm'))
    .pipe(gulp.dest('jvlm/dist'));
});

gulp.task('html', function()
{
  return gulp.src(['jvlm/index.htm'])
    .pipe(rename('index.html'))
    .pipe(inject.prepend("<!-- AUTO GENERATED FILE DO NOT MODIFY YOUR CHANGES WILL GET LOST-->"))
    .pipe(inject.replace('@@JVLMVERSION@@', 'V' + VLMVersion))
    .pipe(inject.replace('@@VLMBUILDATE@@', Date()))
    .pipe(inject.replace('//JVLMBUILD', "= '" + new Date().toUTCString() + "'"))
    .pipe(inject.replace('@@BUILD_TYPE@@', 'Dev'))
    .pipe(gulp.dest('jvlm'))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    });
});

gulp.task('html_prod', function()
{
  return gulp.src(['jvlm/index.htm'])
    .pipe(rename('index.html'))
    .pipe(inject.prepend("<!-- AUTO GENERATED FILE DO NOT MODIFY YOUR CHANGES WILL GET LOST-->"))
    .pipe(inject.replace('@@JVLMVERSION@@', 'V' + VLMVersion))
    .pipe(inject.replace('@@VLMBUILDATE@@', Date()))
    .pipe(inject.replace('//JVLMBUILD', "= '" + new Date().toUTCString() + "'"))
    .pipe(inject.replace('dist/jvlm_main.js', 'dist/jvlm_main.min.js'))
    .pipe(inject.replace('dist/jvlm_main.js', 'dist/jvlm_main.min.js'))
    .pipe(inject.replace('@@BUILD_TYPE@@', 'Prod'))
    .pipe(htmlmin(
    {
      collapseWhitespace: true,
      removeComments: true,
      removeCommentsFromCDATA: true
    }))
    .pipe(gulp.dest('jvlm'))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    });
});

gulp.task('guest_map', function()
{
  return gulp.src(['guest_map/index.htm'])
    .pipe(rename('index.html'))
    .pipe(inject.prepend("<!-- AUTO GENERATED FILE DO NOT MODIFY YOUR CHANGES WILL GET LOST-->"))
    .pipe(inject.replace('@@JVLMVERSION@@', 'V' + VLMVersion))
    .pipe(inject.replace('@@VLMBUILDATE@@', Date()))
    .pipe(inject.replace('//JVLMBUILD', "= '" + new Date().toUTCString() + "'"))
    .pipe(inject.replace('@@BUILD_TYPE@@', 'Prod'))
    .pipe(gulpif(BuildTypeProd,inject.replace('dist/guest_map_babel.js', 'dist/guest_map_babel.min.js')))
    .pipe(gulpif(BuildTypeProd, htmlmin(
    {
      collapseWhitespace: true,
      removeComments: true,
      removeCommentsFromCDATA: true
    })))
    .pipe(gulp.dest('guest_map'))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    });
});

gulp.task('libs_std', function()
{
  return gulp.src(['jvlm/external/jquery/jquery-3.2.1.min.js',
      'jvlm/external/jquery-ui/jquery-ui.js', 'jvlm/external/bootstrap-master/js/bootstrap.js',
      'jvlm/external/jquery.csv.js', 'jvlm/external/bootstrap-colorpicker-master/js/bootstrap-colorpicker.js',
      'jvlm/jquery.ui.touch-punch.js', 'jvlm/external/store/store.min.js',
      'jvlm/external/moments/moment-with-locales.min.js'
    ])
    //.pipe(jshint('.jshintrc'))
    //.pipe(jshint.reporter('default'))
    .pipe(concat('jvlm_libs_std.js'))
    .pipe(gulp.dest('jvlm/dist'))
    .pipe(rename(
    {
      suffix: '.min'
    }))
    .pipe(gulpif(BuildTypeProd, uglify()))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    })
    //.pipe(gulpDeployFtp('./vlmcode', 'vlm-dev.ddns.net', 21, 'vlm', 'vlm'))
    .pipe(gulp.dest('jvlm/dist'));
});

gulp.task('guest_map_js', function()
{
  return gulp.src(['guest_map/js/config.js',
      'guest_map/js/guest_map.js'
    ])
    //.pipe(jshint('.jshintrc'))
    //.pipe(jshint.reporter('default'))
    .pipe(concat('guest_map_babel.js'))
    .pipe(babel(
    {
      presets: ['@babel/env']
    }))
    .pipe(gulpif(BuildTypeProd, uglify()))
    .pipe(gulp.dest('guest_map/dist'))
    .pipe(rename(
    {
      suffix: '.min'
    }))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    })
    //.pipe(gulpDeployFtp('./vlmcode', 'vlm-dev.ddns.net', 21, 'vlm', 'vlm'))
    .pipe(gulp.dest('guest_map/dist'));
});

gulp.task('libs_babel', function()
{
  return gulp.src(['jvlm/external/footable-bootstrap/js/footable.js',
      'jvlm/external/verimail/verimail.jquery.min.js', 'jvlm/external/PasswordStrength/jquery.pstrength-min.1.2.js',
      'externals/fullcalendar/fullcalendar.min.js',
      'externals/fullcalendar/locale-all.js', 'jvlm/external/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'
    ])
    //.pipe(jshint('.jshintrc'))
    //.pipe(jshint.reporter('default'))
    .pipe(concat('jvlm_libs_babel.js'))
    .pipe(babel(
    {
      presets: ['@babel/env']
    }))
    .pipe(gulpif(BuildTypeProd, uglify()))
    .pipe(gulp.dest('jvlm/dist'))
    .pipe(rename(
    {
      suffix: '.min'
    }))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    })
    //.pipe(gulpDeployFtp('./vlmcode', 'vlm-dev.ddns.net', 21, 'vlm', 'vlm'))
    .pipe(gulp.dest('jvlm/dist'));
});

gulp.task('libs_concat', function()
{
  return gulp.src(['jvlm/dist/jvlm_libs_std.min.js', 'jvlm/dist/jvlm_libs_babel.min.js'])
    //.pipe(jshint('.jshintrc'))
    //.pipe(jshint.reporter('default'))
    .pipe(concat('jvlm_libs.min.js'))
    .on('error', function(err)
    {
      gutil.log(gutil.colors.red('[Error]'), err.toString());
    })
    //.pipe(gulpDeployFtp('./vlmcode', 'vlm-dev.ddns.net', 21, 'vlm', 'vlm'))
    .pipe(gulp.dest('jvlm/dist'));
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
    log: log //,
    //debug:log
  });

  var globs = [
    'guest_map/index.html',
    'guest_map/dist/*',
    'jvlm/dist/*',
    'jvlm/index.html',
    'jvlm/*.css'
  ];

  // using base = '.' will transfer everything to /public_html correctly
  // turn off buffering in gulp.src for best performance

  return gulp.src(globs,
    {
      base: '.'
      //cwd: '/home/vlm/vlmcode',
      //buffer: true
    })
    .pipe(conn.newerOrDifferentSize('/home/vlm/vlmcode/')) // only upload newer files
    .pipe(conn.dest('/home/vlm/vlmcode'));

});

gulp.task('default', gulp.series('html', 'scripts', 'guest_map','guest_map_js', 'deploy'),function(done){done();});

gulp.task('BuildAll', gulp.series('libs_std', 'libs_babel', 'libs_concat', 'guest_map','guest_map_js', 'html', 'scripts', 'deploy'),
  function(done){ done();});


gulp.task('BuildProd', 
            gulp.series(function(done){ BuildTypeProd = true; done();}, 'libs_std', 'libs_babel', 'libs_concat', 'guest_map','guest_map_js', 'html_prod', 'scripts', 'deploy'),
            function(done){ done();});