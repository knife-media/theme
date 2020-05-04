const gulp = require('gulp');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const cleanCss = require('gulp-clean-css');
const sassGlob = require('gulp-sass-glob');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const prefix = require('gulp-autoprefixer');
const workboxBuild = require('workbox-build');


gulp.task('styles',  (done) => {
  gulp.src('src/styles/app.scss')
    .pipe(plumber())
    .pipe(sassGlob())
    .pipe(sass({errLogToConsole: true}))
    .pipe(prefix())
    .pipe(concat('styles.min.css'))
    .pipe(cleanCss({compatibility: 'ie9'}))
    .pipe(gulp.dest('app/assets/'))

  done();
})


gulp.task('scripts', (done) => {
  gulp.src('src/scripts/*.js')
    .pipe(plumber())
    .pipe(uglify())
    .pipe(concat('scripts.min.js'))
    .pipe(gulp.dest('app/assets/'))

  done();
})


gulp.task('vendor', (done) => {
  gulp.src('node_modules/workbox-sw/build/workbox-sw.js.*')
    .pipe(gulp.dest('app/assets/vendor/'));

  gulp.src('node_modules/@glidejs/glide/dist/glide.min.js')
    .pipe(gulp.dest('app/assets/vendor/'));

  done();
})


gulp.task('images', (done) => {
  gulp.src('src/images/**/*')
    .pipe(gulp.dest('app/assets/images/'));

  done();
})


gulp.task('video', (done) => {
  gulp.src('src/video/**/*')
    .pipe(gulp.dest('app/assets/video/'));

  done();
})


gulp.task('fonts', (done) => {
  gulp.src('src/fonts/**/*.{ttf,woff,woff2}')
    .pipe(gulp.dest('app/assets/fonts/'));

  done();
})


gulp.task('workbox', (done) => {
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


gulp.task('watch', function() {
  gulp.watch('src/**/*', gulp.series('styles', 'scripts'));
})


gulp.task('default', gulp.series('styles', 'scripts', 'images', 'fonts', 'video', 'vendor', 'workbox', 'watch'));
