const gulp = require('gulp')

const replace = require('gulp-replace')

// ファイル結合
const concat = require('gulp-concat')
// ファイルリネーム（.min作成用）
const rename = require('gulp-rename')

// エラーでも監視を続行させる
const plumber = require('gulp-plumber')
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer')
const cleanCss = require('gulp-clean-css')
const cssmin = require('gulp-cssmin')
const cmq = require('gulp-merge-media-queries')
const path = require('path')
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

gulp.task('text-domain', function (done) {
	// font-awesome
	gulp.src(['./_g2/inc/font-awesome/package/*.php'])
		.pipe(replace(', \'vk_font_awesome_version_textdomain\'', ', \'lightning\''))
		.pipe(gulp.dest('./_g2/inc/font-awesome/package/'));
	gulp.src(['./_g3/inc/font-awesome/package/*.php'])
		.pipe(replace(', \'vk_font_awesome_version_textdomain\'', ', \'lightning\''))
		.pipe(gulp.dest('./_g3/inc/font-awesome/package/'));
	// vk-components
	gulp.src(['./_g2/inc/vk-components/package/*.php'])
		.pipe(replace(', \'vk_components_textdomain\'', ', \'lightning\''))
		.pipe(gulp.dest('./_g2/inc/vk-components/package/'));
	gulp.src(['./_g3/inc/vk-components/package/*.php'])
		.pipe(replace(', \'vk_components_textdomain\'', ', \'lightning\''))
		.pipe(gulp.dest('./_g3/inc/vk-components/package/'));
	// vk-mobile-nav
	gulp.src(['./_g2/inc/vk-mobile-nav/package/*.php'])
		.pipe(replace('vk_mobile_nav_textdomain', 'lightning'))
		.pipe(gulp.dest('./_g2/inc/vk-mobile-nav/package/'));
	gulp.src(['./_g3/inc/vk-mobile-nav/package/*.php'])
		.pipe(replace('vk_mobile_nav_textdomain', 'lightning'))
		.pipe(gulp.dest('./_g3/inc/vk-mobile-nav/package/'));
	// term-color
	gulp.src(['./_g2/inc/term-color/package/*'])
		.pipe(replace('vk_term_color_textdomain', 'lightning'))
		.pipe(gulp.dest('./_g2/inc/term-color/package/'));
	gulp.src(['./_g3/inc/term-color/package/*'])
		.pipe(replace('vk_term_color_textdomain', 'lightning'))
		.pipe(gulp.dest('./_g3/inc/term-color/package/'));
	// vk-css-optimize
	gulp.src(['./_g2/inc/vk-css-optimize/package/*'])
		.pipe(replace("css_optimize_textdomain", 'lightning'))
		.pipe(gulp.dest('./_g2/inc/vk-css-optimize/package/'));
	gulp.src(['./_g3/inc/vk-css-optimize/package/*'])
		.pipe(replace("css_optimize_textdomain", 'lightning'))
		.pipe(gulp.dest('./_g3/inc/vk-css-optimize/package/'));
	// vk-footer-customize.
	gulp.src(['./_g2/inc/vk-footer-customize/package/*.php'])
		.pipe(replace("vk-footer-customize-textdomain",'lightning'))
		.pipe(gulp.dest('./_g2/inc/vk-footer-customize/package/'));
	gulp.src(['./_g3/inc/vk-footer-customize/package/*.php'])
		.pipe(replace("vk-footer-customize-textdomain",'lightning'))
		.pipe(gulp.dest('./_g3/inc/vk-footer-customize/package/'));
	done();
});

gulp.task('sass_common_dev', function (done) {
  src(['./_g2/assets/_scss/**/*.scss'])
    .pipe(sourcemaps.init())
    .pipe(
      sass({
        includePaths: [
          './_g2/assets/scss',
          './_g2/inc/vk-components/package/_scss'
        ]
      }
    ))
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./map/'))
    .pipe(gulp.dest('../lightning-pro/assets/css'))
    .pipe(gulp.dest('./_g2/assets/css'))
  done()
});
gulp.task('sass_common', function (done) {
  src(['./_g2/assets/_scss/**/*.scss'])
    .pipe(
      sass({
        includePaths: [
          './_g2/assets/scss',
          './_g2/inc/vk-components/package/_scss'
        ]
      }
    ))
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/assets/css'))
  done()
});

gulp.task('sass_bs4', function (done) {
  src(['./_g2/library/bootstrap-4/scss/**.scss'])
    .pipe(aliases({
      "@bootstrap": "./node_modules/bootstrap/scss"
    }))
    .pipe(sass())
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(rename(
      {
        suffix: '.min'
      }
    ))
    .pipe(gulp.dest('./_g2/library/bootstrap-4/css/'))
    // .pipe(gulp.dest('../lightning-pro/library/bootstrap-4/css/'));
  done();
});

gulp.task('sass_skin', function (done) {
  src(['_g2/design-skin/origin/_scss/**/*.scss'])
    .pipe(sass())
    .pipe(cmq({
      log: true
    }))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/design-skin/origin/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin/css'));
  done();
});

gulp.task('sass_skin2_dev', function (done) {
  src(['_g2/design-skin/origin2/_scss/**/*.scss'])
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./map/'))
    .pipe(gulp.dest('./_g2/design-skin/origin2/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin2/css'));
  done();
});
gulp.task('sass_skin2', function (done) {
  src(['_g2/design-skin/origin2/_scss/**/*.scss'])
    .pipe(sass())
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/design-skin/origin2/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin2/css'));
  done();
});

gulp.task('sass_woo', function (done) {
  return src(['./_g2/plugin-support/woocommerce/_scss/**.scss'])
    .pipe(sass())
    .pipe(cmq({
      log: true
    }))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/plugin-support/woocommerce/css/'))
    .pipe(gulp.dest('../lightning-pro/plugin-support/woocommerce/css/'));
});

gulp.task('sass_bbpress', function (done) {
	return src(['./_g2/plugin-support/bbpress/_scss/**.scss'])
	  .pipe(sass())
	  .pipe(cmq({
		log: true
	  }))
	  .pipe(autoprefixer())
	  .pipe(cleanCss())
	  .pipe(gulp.dest('./_g2/plugin-support/bbpress/css/'))
	  .pipe(gulp.dest('../lightning-pro/plugin-support/bbpress/css/'));
  });
  gulp.task('sass_bbpress_g3', function (done) {
    return src(['./_g3/plugin-support/bbpress/_scss/**.scss'])
      .pipe(sass())
      .pipe(cmq({
      log: true
      }))
      .pipe(autoprefixer())
      .pipe(cleanCss())
      .pipe(gulp.dest('./_g3/plugin-support/bbpress/css/'));
    });

// gulp.task('components_copy', function (done) {
//   return gulp.src(['inc/components/*.php'])
//     .pipe(gulp.dest('./_g2/inc/components'))
//     .pipe(gulp.dest('../lightning-pro/inc/components'));
// });

gulp.task('dist_foundation', function (done) {
  gulp.src(['_g2/design-skin/foundation/**'])
    .pipe(gulp.dest('../../plugins/lightning-skin-charm/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-variety/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-fort/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-pale/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-jpnstyle/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-sample/assets/foundation'));
    done();
});

// gulp.task('watch_pro', function (done) {
// 	error_stop = false
// 	gulp.watch([
// 		'./assets/_js/**',
// 		// './assets/_scss/**',
// 		'./_g2/inc/**'
// 	], gulp.series('dist_pro_dev'));
// 	done();
// });
// gulp.task('dist_pro_dev', function () {
// 	const files = gulp.src(
// 		[
// 			'./assets/_js/**',
// 			// './assets/_scss/**',
// 			'./_g2/inc/**',
// 			"!./inc/tgm-plugin-activation/**",
// 		], {
// 			base: './'
// 		}
// 	)
// 	return files.pipe(gulp.dest('../lightning-pro/'))
// });

// Watch
gulp.task('watch', function (done) {
  error_stop = false
  gulp.watch(['./_g2/assets/_scss/**','./_g2/inc/vk-mobile-nav/package/css/**','./_g2/inc/vk-components/**/*.css'], gulp.series('sass_common_dev'));
  gulp.watch(['./_g2/plugin-support/woocommerce/_scss/**'], gulp.series('sass_woo'));
  gulp.watch(['./_g2/plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress'));
  gulp.watch(['./_g2//library/bootstrap-4/scss/**.scss'], gulp.series('sass_bs4'));
  gulp.watch(['./_g2/design-skin/origin/_scss/**/*.scss'], gulp.series('sass_skin'));
  gulp.watch(['./_g2/design-skin/origin2/_scss/**/*.scss'], gulp.series('sass_skin2_dev'));
  gulp.watch(['./_g2/design-skin/foundation/_scss/**/*.scss'], gulp.series('sass_skin2', 'dist_foundation'));
  gulp.watch(['./_g3/plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress_g3'));
  done();
});

// copy dist ////////////////////////////////////////////////

/* 実際には階層指定はあんまり効いてなくて拡張子だけでコピーされてる */
gulp.task('copy_dist', function (done) {
  const files = gulp.src(
    [
      "./**/*.php",
      "./**/*.txt",
      "./**/*.css",
      "./**/*.png",
      "./**/*.jpg",
      "./inc/**",
      "./languages/**",
      "./_g2/inc/**",
      "./_g2/assets/**",
      "./_g2/library/**",
      "./_g3/inc/**",
      "./_g3/assets/**",
      "!./_g2/assets/css/map/**",
      "!./_g3/node_modules/**/*.*",
      "!./_g3/assets/css/map/**",
      "!./vendor/**",
      "!./.vscode/**",
      "!./bin/**",
      "!./dist/**",
      "!./node_modules/**/*.*",
      "!./tests/**",
    ], {
      base: './'
    }
  )
  files.pipe(gulp.dest("dist/lightning"));
  done();
});

gulp.task('dist', gulp.series('text-domain','sass_common','sass_skin2','copy_dist'));
gulp.task('default',  gulp.series('text-domain', 'watch'));