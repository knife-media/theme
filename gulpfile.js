var gulp      = require('gulp');
var sass      = require('gulp-sass');
var concat    = require('gulp-concat');
var cleanCss  = require('gulp-clean-css');
var sassGlob  = require('gulp-sass-glob');
var uglify    = require('gulp-uglify');
var plumber   = require('gulp-plumber');
var prefix    = require('gulp-autoprefixer');

var path = {
  source: 'src/',
  assets: 'app/assets/'
}

gulp.task('styles', function(done) {
  gulp.src([path.source + '/styles/app.scss'])
    .pipe(plumber())
    .pipe(sassGlob())
    .pipe(sass({errLogToConsole: true}))
    .pipe(prefix())
    .pipe(concat('styles.min.css'))
    .pipe(cleanCss({compatibility: 'ie9'}))
    .pipe(gulp.dest(path.assets))

  done();
})

gulp.task('scripts', function(done) {
  gulp.src([path.source + '/scripts/*.js'])
    .pipe(plumber())
    .pipe(uglify())
    .pipe(concat('scripts.min.js'))
    .pipe(gulp.dest(path.assets))

  done();
})

gulp.task('vendor', function() {
  gulp.src([path.source + '/vendor/*.min.js'])
    .pipe(gulp.dest(path.assets + '/vendor/'));
})

gulp.task('images', function() {
  gulp.src([path.source + '/images/**/*'])
    .pipe(gulp.dest(path.assets + '/images/'));
})

gulp.task('video', function() {
  gulp.src([path.source + '/video/**/*'])
    .pipe(gulp.dest(path.assets + '/video/'));
})

gulp.task('fonts', function() {
  gulp.src([path.source + '/fonts/**/*.{ttf,woff,woff2}'])
    .pipe(gulp.dest(path.assets + '/fonts/'));
})

gulp.task('watch', function() {
  gulp.watch('./src/**/*', gulp.series('styles', 'scripts'));
})

gulp.task('default', gulp.parallel('styles', 'scripts', 'vendor', 'images', 'fonts', 'video', 'watch'));
