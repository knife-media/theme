var gulp     = require('gulp');
var sass     = require('gulp-sass');
var concat   = require('gulp-concat');
var minifyCss  = require('gulp-minify-css');
var uglify   = require('gulp-uglify');
var plumber  = require('gulp-plumber');
var prefix   = require('gulp-autoprefixer');

var path = {
  source: 'src/',
  assets: 'app/assets/'
}

gulp.task('styles', function() {
  gulp.src([path.source + '/styles/custom/app.scss'])
    .pipe(plumber())
    .pipe(sass({errLogToConsole: true}))
    .pipe(prefix({browsers: ['ie >= 10', 'ff >= 30', 'chrome >= 34', 'safari >= 7', 'opera >= 23', 'ios >= 7', 'android >= 4.4']}))
    .pipe(concat('styles.min.css'))
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest(path.assets))

  gulp.src([path.source + '/styles/vendor/*.min.css'])
    .pipe(gulp.dest(path.assets + '/styles/'));
})

gulp.task('scripts', function() {
  gulp.src([path.source + '/scripts/custom/*.js'])
    .pipe(plumber())
    .pipe(uglify())
    .pipe(concat('scripts.min.js'))
    .pipe(gulp.dest(path.assets))

  gulp.src([path.source + '/scripts/vendor/*.min.js'])
    .pipe(gulp.dest(path.assets + '/scripts/'));
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
  gulp.src([path.source + '/fonts/**/*.{ttf,woff,eot,svg,woff2}'])
    .pipe(gulp.dest(path.assets + '/fonts/'));
})

gulp.task('watch', function() {
  gulp.watch(path.source + '/**/*', ['styles', 'scripts']);
})

gulp.task('default', ['styles', 'scripts', 'images', 'fonts', 'watch', 'video']);
