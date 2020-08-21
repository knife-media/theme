const gulp = require('gulp');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const cleanCss = require('gulp-clean-css');
const sassGlob = require('gulp-sass-glob');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const prefix = require('gulp-autoprefixer');
const workboxBuild = require('workbox-build');
const babel = require('gulp-babel');
const path = require('path');
const rename = require('gulp-rename');


gulp.task('styles', (done) => {
  // Process common theme styles
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

  // Process custom styles
  gulp.src('src/styles/custom/*.scss')
    .pipe(plumber())
    .pipe(sass({
      errLogToConsole: true
    }))
    .pipe(prefix())
    .pipe(cleanCss({
      compatibility: 'ie9'
    }))
    .pipe(rename((file) => {
      file.dirname = file.basename;
      file.basename = 'styles';
      file.extname = '.css';
    }))
    .pipe(gulp.dest('app/core/custom/'))

  // Process special styles
  gulp.src('src/styles/special/*.scss')
    .pipe(plumber())
    .pipe(sass({
      errLogToConsole: true
    }))
    .pipe(prefix())
    .pipe(cleanCss({
      compatibility: 'ie9'
    }))
    .pipe(rename((file) => {
      file.dirname = file.basename;
      file.basename = 'styles';
      file.extname = '.css';
    }))
    .pipe(gulp.dest('app/core/special/'))

  done();
})


gulp.task('scripts', (done) => {
  // Process common theme scripts
  gulp.src('src/scripts/bundle/*.js')
    .pipe(plumber())
    .pipe(babel({
      presets: ['@babel/env']
    }))
    .pipe(uglify())
    .pipe(concat('scripts.min.js'))
    .pipe(gulp.dest('app/assets/'))

  // Process custom scripts
  gulp.src('src/scripts/custom/*.js')
    .pipe(plumber())
    .pipe(babel({
      presets: ['@babel/env']
    }))
    .pipe(uglify())
    .pipe(rename((file) => {
      file.dirname = file.basename;
      file.basename = 'scripts';
      file.extname = '.js';
    }))
    .pipe(gulp.dest('app/core/custom/'))

  // Process special scripts
  gulp.src('src/scripts/special/*.js')
    .pipe(plumber())
    .pipe(babel({
      presets: ['@babel/env']
    }))
    .pipe(uglify())
    .pipe(rename((file) => {
      file.dirname = file.basename;
      file.basename = 'scripts';
      file.extname = '.js';
    }))
    .pipe(gulp.dest('app/core/special/'))

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


gulp.task('watch', function (done) {
  gulp.watch('src/**/*', gulp.series('styles', 'scripts'));

  done();
})


gulp.task('default', gulp.series('styles', 'scripts', 'images', 'fonts', 'video', 'vendor', 'workbox', 'watch'));