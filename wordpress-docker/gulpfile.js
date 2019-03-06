var project_location    = 'plugins/openwebconcept';
var function_vers_var   = 'OWC_PLUGIN_VERSION';

require('es6-promise').polyfill();

var gulp          = require('gulp'),
    sass          = require('gulp-sass'),
    less          = require('gulp-less'),
    rtlcss        = require('gulp-rtlcss'),
    autoprefixer  = require('gulp-autoprefixer'),
    plumber       = require('gulp-plumber'),
    gutil         = require('gulp-util'),
    rename        = require('gulp-rename'),
    concat        = require('gulp-concat'),
    jshint        = require('gulp-jshint'),
    uglify        = require('gulp-uglify'),
    svgSprite     = require('gulp-svg-sprite'),
    imagemin      = require('gulp-imagemin'),
    browserSync   = require('browser-sync').create(),
    zip           = require('gulp-zip'),
    header        = require('gulp-header'),
    del           = require('del');

var svgConfig = {
  mode: {
    symbol: {
      inline: true,
      prefix: ".svg %s-svg",
    }
  },
  shape: {
    id: {
      generator: function(name, file) {
        var svg_id = 'svg-' + name;
        return svg_id.replace(/\.[^/.]+$/, "");
      }
    }
  }
};

var onError = function( err ) {
  console.log('An error occurred:', gutil.colors.magenta(err.message));
  gutil.beep();
  // this.emit('end');
};

// Grab the package.json file for the version
var getPackageJson = function () {
  var fs = require('fs');

  return JSON.parse(fs.readFileSync( 'package.json', 'utf8'));
};

// Sass
gulp.task('sass', function() {
  return gulp.src(
    [
      './wp-content/'+project_location+'/assets/sass/**/style.scss',
      './wp-content/'+project_location+'/assets/sass/**/admin/style.scss'
    ]
  )
  .pipe(plumber({ errorHandler: onError }))
  .pipe(sass())
  .pipe(autoprefixer())
  .pipe(gulp.dest('./wp-content/'+project_location+'/assets/css/'))
  .pipe(rtlcss())                     // Convert to RTL
  .pipe(rename({ basename: 'rtl' }))  // Rename to rtl.css
  .pipe(gulp.dest('./wp-content/'+project_location+'/assets/css/'));             // Output RTL stylesheets (rtl.css)
});

// Less
gulp.task('less', function(){
    return gulp.src('./wp-content/'+project_location+'/assets/less/**/style.less')
        .pipe(less())
        .pipe(autoprefixer())
        .pipe(gulp.dest('./wp-content/'+project_location+'/assets/css/'))
        .pipe(rtlcss())                     // Convert to RTL
        .pipe(rename({ basename: 'rtl' }))  // Rename to rtl.css
        .pipe(gulp.dest('./wp-content/'+project_location+'/assets/css/'));  
});

// JavaScript
gulp.task('js', function() {
  return gulp.src(['./wp-content/'+project_location+'/assets/js/*.js'])
  .pipe(jshint())
  .pipe(jshint.reporter('default'))
  .pipe(concat('app.js'))
  .pipe(rename({suffix: '.min'}))
  .pipe(uglify())
  .pipe(gulp.dest('./wp-content/'+project_location+'/assets/js'));
});

// Images
gulp.task('images', function() {
  return gulp.src('./wp-content/'+project_location+'/assets/images/src/*')
  .pipe(plumber({ errorHandler: onError }))
  .pipe(imagemin({ optimizationLevel: 7, progressive: true }))
  .pipe(gulp.dest('./wp-content/'+project_location+'/assets/images/dist'));
});

gulp.task('svg', function() {

  gulp.src('./wp-content/'+project_location+'/assets/svg/*.svg')
  .pipe(svgSprite(svgConfig))
  .pipe(gulp.dest('./wp-content/'+project_location+'/assets'));

});

// Watch
gulp.task('watch', function() {
  browserSync.init({
    files: ['{lib,templates}/**/*.php', '*.php'],
    proxy: {
      target: '192.168.99.100',
      ws: true,
      proxyReq: [
        function(proxyReq) {
          proxyReq.setHeader('Access-Control-Allow-Origin', '*');
        }
      ]
    },
    snippetOptions: {
      whitelist: ['/wp-admin/admin-ajax.php'],
      blacklist: ['/wp-admin/**']
    }
  });
  gulp.watch('./wp-content/'+project_location+'/assets/sass/**/*.scss', ['sass']);
  gulp.watch('./wp-content/'+project_location+'/assets/less/**/*.less', ['less']);
  // gulp.watch('./wp-content/'+project_location+'/assets/js/*.js', ['js']);
  gulp.watch('./wp-content/'+project_location+'/assets/images/src/*', ['images']);
  gulp.watch('./wp-content/'+project_location+'/assets/svg/*', ['svg']);
});


// Our versioning
gulp.task('release', ['build-release'], function(){
  gulp.start('zip-release');
});

// Our versioning
gulp.task('build-release', function(){
  // get the version which we want to bump up to.
  var argv      = require('yargs').argv;
  
  var version   = argv.type;
  var noupdate  = argv.noupdate; // --noupdate
  
  if(typeof(argv.type) === 'undefined') {
    version = 'patch'; // major.minor.patch (https://semver.org/)
  }

  if( typeof(argv.noupdate) === 'undefined' ) { 
    noupdate = false;
  } else {
    noupdate = true;
  }

  // require some packages that we need
  var bump          = require('gulp-bump');
  var semver        = require('semver');
  var replace       = require('gulp-replace');

  // reget package
  var pkg = getPackageJson();
  
  if( noupdate ) {
    var newVer = pkg.version;
  } else {
    // increment version
    var newVer = semver.inc(pkg.version, version);
  }

  var src_path = 'wp-content/' + project_location;

  // files we want to transfer for release
  gulp.src( 'package.json' )
      .pipe(bump({
        version: newVer,
        type:version
      }))
      .pipe(gulp.dest('./releases/' + newVer + '/'+pkg.name))
      .pipe(gulp.dest('./'));

  // Move the styles.css into the new folder
  gulp.src( src_path + '/style.css')
  // perform the version up in the style.css file
      .pipe(replace(/Version:[ ]+[0-9].+/g, 'Version: '+newVer))
      .pipe(gulp.dest('./releases/'+newVer + '/'+pkg.name))
      .pipe(gulp.dest( src_path + '/' ));

  var regex = new RegExp("\'" + function_vers_var + "\', \'+[0-9].+\'", "g");

  // Replace the functions.php version
  gulp.src( src_path + '/functions.php')
  // perform the version up in the style.css file
      .pipe(replace(regex, '\''+function_vers_var+'\', \''+newVer+'\''))
      .pipe(gulp.dest('./releases/'+newVer + '/'+pkg.name))
      .pipe(gulp.dest( src_path + '/' ));

  gulp.src('CHANGELOG.md')
    .pipe(header(
      "# " + pkg.description + "\n\n" +
      "**Requires at least:** WordPress 4.4\n" +
      "**Tested up to:** WordPress 4.7\n" +
      "**Stable tag:** " + newVer + "\n" +
      "**Version:** " + newVer + "\n\n" +
      "## Changelog\n\n"
    ))
    .pipe(rename("readme.txt"))
    .pipe(gulp.dest('./'));

  // move package files for deployment
  return gulp.src([
      src_path + '/screenshot.*',
      src_path + '/**/*.php',
      src_path + '/**/*.mo',
      src_path + '/**/*.css',
      src_path + '/**/*.js',
      src_path + '/**/*.svg',
      src_path + '/**/*.png',
      src_path + '/**/*.jpg',
      src_path + '/**/*.jpeg',
      src_path + '/**/*.gif',
      '!' + src_path + '/assets/svg/*.svg',
      '!' + src_path + '/assets/images/src/*',
      '!' + src_path + '/assets/less/*',
      '!' + src_path + '/assets/sass/*',
      '!' + src_path + '/functions.php',
    ], {base: './' + src_path}
  ).pipe(gulp.dest('./releases/' + newVer + '/'+pkg.name));

});

gulp.task('zip-release', function(){

  // reget package
  var pkg = getPackageJson();

  // zip the release
  gulp.src('./releases/' + pkg.version + '/**').pipe(zip('master.zip')).pipe(gulp.dest('dist'));

});

gulp.task('build', ['svg', 'sass', 'less', 'images']);
gulp.task('default', ['build', 'watch']);