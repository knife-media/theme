const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');
const cleanCss = require('gulp-clean-css');
const sassGlob = require('gulp-sass-glob');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const prefix = require('gulp-autoprefixer');
const workboxBuild = require('workbox-build');
const babel = require('gulp-babel');

// Process theme styles
gulp.task('styles', (done) => {
  gulp.src('src/styles/app.scss')
    .pipe(plumber())
    .pipe(sassGlob())
    .pipe(sass({
      errLogToConsole: true
    }))
    .pipe(prefix())
    .pipe(concat('styles.min.css'))
    .pipe(cleanCss({
      compatibility: 'ie9'
    }))
    .pipe(gulp.dest('app/assets/'))

  done();
});

// Process theme scripts
gulp.task('scripts', (done) => {
  gulp.src('src/scripts/*.js')
    .pipe(plumber())
    .pipe(babel({
      presets: ['@babel/env']
    }))
    .pipe(uglify())
    .pipe(concat('scripts.min.js'))
    .pipe(gulp.dest('app/assets/'))

  done();
});

// Add vendor files
gulp.task('vendor', (done) => {
  gulp.src('node_modules/workbox-sw/build/workbox-sw.js.*')
    .pipe(gulp.dest('app/assets/vendor/'));

  done();
});

// Move images
gulp.task('images', (done) => {
  gulp.src('src/images/**/*')
    .pipe(gulp.dest('app/assets/images/'));

  done();
});

// Move video
gulp.task('video', (done) => {
  gulp.src('src/video/**/*')
    .pipe(gulp.dest('app/assets/video/'));

  done();
});

// Move fonts
gulp.task('fonts', (done) => {
  gulp.src('src/fonts/**/*.{ttf,woff,woff2}')
    .pipe(gulp.dest('app/assets/fonts/'));

  done();
});

// Set service-worker
gulp.task('worker', (done) => {
  const theme = '/wp-content/themes/knife/assets/';

  workboxBuild.generateSW({
    globDirectory: 'app/assets',
    importScripts: [
      theme + 'vendor/workbox-sw.js'
    ],
    sourcemap: false,
    modifyURLPrefix: {
      '': theme
    },
    inlineWorkboxRuntime: true,
    globPatterns: [
      'scripts.min.js',
      'styles.min.css',
      'images/**/*.{png,jpg,svg}',
      'vendor/*.{js,css}',
      'fonts/**/*.{ttf,woff2,woff}'
    ],
    swDest: 'app/assets/service-worker.js',
  });

  done();
});

// Watch theme assets
gulp.task('watch', () => {
  gulp.watch('src/styles/**/*', gulp.series('styles'));
  gulp.watch('src/scripts/**/*', gulp.series('scripts'));
});

// Build task
// Don't forget to execute gulp worker after building
gulp.task('build', gulp.parallel(
  'styles',
  'scripts',
  'images',
  'fonts',
  'video',
  'vendor'
));

// Set default task
gulp.task('default', gulp.series('build', 'watch'));
