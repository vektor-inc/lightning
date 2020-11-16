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

let error_stop = true

function src(list) {
  if(error_stop) {
    return gulp.src(list)
  }else{
    return gulp.src(list).pipe(plumber())
  }
}

gulp.task('text-domain', function (done) {
	gulp.src(['./inc/font-awesome/package/*.php'])
	.pipe(replace(', \'vk_font_awesome_version_textdomain\'', ', \'lightning\''))
	.pipe(gulp.dest('./inc/font-awesome/package/'));
	gulp.src(['./inc/vk-components/package/*.php'])
	.pipe(replace(', \'vk_components_textdomain\'', ', \'lightning\''))
	.pipe(gulp.dest('./inc/vk-components/package/'));
	gulp.src(['./inc/vk-mobile-nav/package/*.php'])
	.pipe(replace('vk_mobile_nav_textdomain', 'lightning'))
	.pipe(gulp.dest('./inc/vk-mobile-nav/package/'));
	gulp.src(['./inc/term-color/package/*'])
	.pipe(replace('vk_term_color_textdomain', 'lightning'))
  .pipe(gulp.dest('./inc/term-color/package/'));
	gulp.src(['./inc/term-color/package/*'])
	.pipe(replace('vk_term_color_textdomain', 'lightning'))
  .pipe(gulp.dest('./inc/term-color/package/'));
	gulp.src(["./inc/vk-css-optimize/package/*"])
		.pipe(replace("css_optimize_textdomain", "lightning"))
		.pipe(gulp.dest("./inc/vk-css-optimize/package/"));
	done();
});

gulp.task('sass_common_dev', function (done) {
  src(['./assets/_scss/**/*.scss'])
    .pipe(sourcemaps.init())
    .pipe(
      sass({
        includePaths: [
          './assets/scss',
          './inc/vk-components/package/_scss'
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
    .pipe(gulp.dest('./assets/css'))
  done()
});
gulp.task('sass_common', function (done) {
  src(['./assets/_scss/**/*.scss'])
    .pipe(
      sass({
        includePaths: [
          './assets/scss',
          './inc/vk-components/package/_scss'
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
    .pipe(gulp.dest('./assets/css'))
  done()
});

gulp.task('sass_bs4', function (done) {
  src(['./library/bootstrap-4/scss/**.scss'])
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
    .pipe(gulp.dest('./library/bootstrap-4/css/'))
    .pipe(gulp.dest('../lightning-pro/library/bootstrap-4/css/'));
  done();
});

gulp.task('sass_skin', function (done) {
  src(['design-skin/origin/_scss/**/*.scss'])
    .pipe(sass())
    .pipe(cmq({
      log: true
    }))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./design-skin/origin/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin/css'));
  done();
});

gulp.task('sass_skin2_dev', function (done) {
  src(['design-skin/origin2/_scss/**/*.scss'])
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./map/'))
    .pipe(gulp.dest('./design-skin/origin2/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin2/css'));
  done();
});
gulp.task('sass_skin2', function (done) {
  src(['design-skin/origin2/_scss/**/*.scss'])
    .pipe(sass())
    .pipe(cmq(
      {
        log: true
      }
    ))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./design-skin/origin2/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin2/css'));
  done();
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
    .pipe(gulp.dest('../lightning-pro/plugin-support/woocommerce/css/'));
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
	  .pipe(gulp.dest('../lightning-pro/plugin-support/bbpress/css/'));
  });

gulp.task('components_copy', function (done) {
  return gulp.src(['inc/components/*.php'])
    .pipe(gulp.dest('./inc/components'))
    .pipe(gulp.dest('../lightning-pro/inc/components'));
});

gulp.task('dist_foundation', function (done) {
  gulp.src(['design-skin/foundation/**'])
    .pipe(gulp.dest('../../plugins/lightning-skin-charm/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-variety/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-fort/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-pale/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-jpnstyle/bs4/_scss/foundation'))
    .pipe(gulp.dest('../../plugins/lightning-skin-sample/assets/foundation'));
    done();
});

gulp.task('watch_pro', function (done) {
	error_stop = false
	gulp.watch([
		'./assets/_js/**',
		// './assets/_scss/**',
		'./inc/**'
	], gulp.series('dist_pro_dev'));
	done();
});
gulp.task('dist_pro_dev', function () {
	const files = gulp.src(
		[
			'./assets/_js/**',
			// './assets/_scss/**',
			'./inc/**',
			"!./inc/tgm-plugin-activation/**",
		], {
			base: './'
		}
	)
	return files.pipe(gulp.dest('../lightning-pro/'))
});

// Watch
gulp.task('watch', function (done) {
  error_stop = false
  gulp.watch(['./assets/_scss/**','./inc/vk-mobile-nav/package/css/**','./inc/vk-components/**/*.css'], gulp.series('sass_common_dev'));
  gulp.watch(['./plugin-support/woocommerce/_scss/**'], gulp.series('sass_woo'));
  gulp.watch(['./plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress'));
  gulp.watch(['./library/bootstrap-4/scss/**.scss'], gulp.series('sass_bs4'));
  gulp.watch(['./design-skin/origin/_scss/**/*.scss'], gulp.series('sass_skin'));
  gulp.watch(['./design-skin/origin2/_scss/**/*.scss'], gulp.series('sass_skin2_dev'));
  gulp.watch(['./design-skin/foundation/_scss/**/*.scss'], gulp.series('sass_skin2', 'dist_foundation'));
  done();
});

// copy dist ////////////////////////////////////////////////

gulp.task('copy_dist', function (done) {
  const files = gulp.src(
    [
      './**/*.php',
      './**/*.txt',
      './**/*.css',
      './**/*.png',
      './assets/**',
      '!./assets/css/map/**',
      './design-skin/**',
      './inc/**',
      './plugin-support/**',
      './library/**',
      './template-parts/**',
      './languages/**',
      "!./vendor/**",
      "!./.vscode/**",
      "!./bin/**",
      "!./dist/**",
      "!./node_modules/**/*.*",
      "!./tests/**",
	  "!./dist/**",
    ], {
      base: './'
    }
  )
  files.pipe(gulp.dest("dist/lightning"));
  done();
});

gulp.task('dist_pro', function () {
  const files = gulp.src(
    [
      './**/*.php',
      './**/*.txt',
      './**/*.css',
      './**/*.png',
      './assets/**',
      './design-skin/**',
      './inc/**',
      './plugin-support/**',
      './library/**',
      './template-parts/**',
      './languages/**',
      "!./vendor/**",
      "!./assets/css/map/**",
      "!./design-skin/origin2/css/map/**",
      "!./.vscode/**",
      "!./bin/**",
      "!./dist/**",
      "!./node_modules/**/*.*",
      "!./tests/**",
      "!./dist/**",
      "!./readme.txt",
      "!./style.css",
      "!./screenshot.png",
      "!./inc/tgm-plugin-activation/**",
    ], {
      base: './'
    }
  )

  if (process.env.COPY_TO) {
    return files.pipe(gulp.dest(path.resolve(__dirname, process.env.COPY_TO)))
  }

  return files.pipe(gulp.dest('../lightning-pro/'))
});

gulp.task('dist', gulp.series('text-domain','sass_common','sass_skin2','copy_dist'));
gulp.task('default',  gulp.series('text-domain', 'watch'));



gulp.task('watch_kuru', function (done) {
  gulp.parallel('watch');
  gulp.watch(['./**.php','! ./dist/**'],gulp.series ('copy_dist','dist_kuru'));
  done();
});

gulp.task('dist_kuru', function (done) {

  const files = gulp.src(
    [
      "./dist/lightning/**",
    ], {
      base: './dist/'
    }
  )

  // if (process.env.COPY_TO) {
  //   return files.pipe(gulp.dest(path.resolve(__dirname, process.env.COPY_TO)))
  // }

  files.pipe(gulp.dest('../../../../../../../Local Sites/themecheck/app/public/wp-content/themes/'));
  done();
});
gulp.task('watch_theme_check_kuru',  gulp.parallel('watch_kuru' ));

