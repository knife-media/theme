var gulp       = require('gulp');
var sass       = require('gulp-sass');
var concat     = require('gulp-concat');
var minifyCss  = require('gulp-minify-css');
var uglify     = require('gulp-uglify');
var plumber    = require('gulp-plumber');
var flatten    = require('gulp-flatten');
var prefix     = require('gulp-autoprefixer');
var order      = require('gulp-order');

var path = {
	source: 'src/',
	assets: 'app/assets/'
}

gulp.task('scss', function() {
	gulp.src([path.source + 'scss/app.scss'])
		.pipe(plumber())
		.pipe(sass({errLogToConsole: true}))
		.pipe(prefix({browsers: ['ie >= 10', 'ff >= 30', 'chrome >= 34', 'safari >= 7', 'opera >= 23', 'ios >= 7', 'android >= 4.4']}))
		.pipe(concat('styles.min.css'))
		.pipe(minifyCss({compatibility: 'ie8'}))
		.pipe(gulp.dest(path.assets))
})

gulp.task('js', function() {
	gulp.src([path.source + '/js/**/*.js'])
		.pipe(plumber())
		.pipe(uglify())
		.pipe(concat('scripts.min.js'))
		.pipe(gulp.dest(path.assets))
})

gulp.task('images', function() {
	gulp.src([path.source + '/images/**/*'])
		.pipe(gulp.dest(path.assets + '/images/'));
})

gulp.task('fonts', function() {
	gulp.src([path.source + '/fonts/**/*.{ttf,woff,eot,svg,woff2}'])
		.pipe(gulp.dest(path.assets + '/fonts/'));
})

gulp.task('watch', function() {
	gulp.watch(path.source + '/**/*', ['scss', 'js', 'images']);
})

gulp.task('default', ['scss', 'js', 'images', 'fonts', 'watch']);
