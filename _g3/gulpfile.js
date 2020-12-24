const plumber = require('gulp-plumber')
const gulp = require('gulp')
const sass = require('gulp-sass')
const autoprefixer = require('gulp-autoprefixer')
const cleanCss = require('gulp-clean-css')
const cmq = require('gulp-merge-media-queries')
var sourcemaps = require('gulp-sourcemaps');
const aliases = require('gulp-style-aliases')

let error_stop = true

function src(list) {
  if(error_stop) {
    return gulp.src(list)
  }else{
    return gulp.src(list).pipe(plumber())
  }
}

gulp.task('sass', function (done) {
  src(['./assets/_scss/**/*.scss'])
  .pipe(aliases({
    "@bootstrap": "./node_modules/bootstrap/scss"
  }))
  .pipe(
    sass({
      includePaths: [
        './assets/scss',
        // './inc/vk-components/package/_scss'
      ]
    }
  ))
    .pipe(sourcemaps.init())
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./assets/css'))
  done()
});

gulp.task('sass_woo', function (done) {
  return src(['./plugin-support/woocommerce/_scss/**.scss'])
    .pipe(sass())
    .pipe(cmq({
      log: true
    }))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./plugin-support/woocommerce/css/'))
});

gulp.task('sass_bbpress', function (done) {
	return src(['./plugin-support/bbpress/_scss/**.scss'])
	  .pipe(sass())
	  .pipe(cmq({
		log: true
	  }))
	  .pipe(autoprefixer())
	  .pipe(cleanCss())
	  .pipe(gulp.dest('./plugin-support/bbpress/css/'))
  });

// Watch
gulp.task('watch', function (done) {
  error_stop = false
  gulp.watch(['./assets/_scss/**','./inc/vk-mobile-nav/package/css/**','./inc/vk-components/**/*.css'], gulp.series('sass'));
  gulp.watch(['./plugin-support/woocommerce/_scss/**'], gulp.series('sass_woo'));
  gulp.watch(['./plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress'));
  done();
});

gulp.task('default',  gulp.series('watch'));