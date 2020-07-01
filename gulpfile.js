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
	done();
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
    .pipe(gulp.dest('../lightning-pro/assets/css'))
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
  return src(['./inc/woocommerce/_scss/**.scss'])
    .pipe(sass())
    .pipe(cmq({
      log: true
    }))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./inc/woocommerce/css/'))
    .pipe(gulp.dest('../lightning-pro/inc/woocommerce/css/'));
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

// Watch
gulp.task('watch', function (done) {
  error_stop = false
  gulp.watch(['./assets/_scss/**','./inc/vk-mobile-nav/package/css/**','./inc/vk-components/**/*.css'], gulp.series('sass_common'));
  gulp.watch(['./inc/woocommerce/_scss/**'], gulp.series('sass_woo'));
  gulp.watch(['./library/bootstrap-4/scss/**.scss'], gulp.series('sass_bs4'));
  gulp.watch(['./design-skin/origin/_scss/**/*.scss'], gulp.series('sass_skin'));
  gulp.watch(['./design-skin/origin2/_scss/**/*.scss'], gulp.series('sass_skin2'));
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
      './design-skin/**',
      './inc/**',
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
  return files.pipe(gulp.dest('../../../../../../updatetestfree/app/public/wp-content/themes/lightning'))
  .pipe(gulp.dest("dist/lightning"));
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

gulp.task('dist', gulp.series('text-domain','sass_common','copy_dist'));
gulp.task('default',  gulp.series('text-domain', 'watch'));

