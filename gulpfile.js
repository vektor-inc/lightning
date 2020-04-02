var path = require('path')
var fs = require('fs')
var gulp = require('gulp');
var replace = require('gulp-replace');
// ファイル結合
var concat = require('gulp-concat');
// js最小化
var jsmin = require('gulp-uglify');
// ファイルリネーム（.min作成用）
var rename = require('gulp-rename');
// エラーでも監視を続行させる
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var cleanCss = require('gulp-clean-css');
var cssmin = require('gulp-cssmin');
var cmq = require('gulp-merge-media-queries');
var babel = require('gulp-babel');
let error_stop = true

var theme_name = 'lightning'


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
  gulp.src(['./inc/vk-mobile-nav/package/*.php'])
    .pipe(replace('vk_mobile_nav_textdomain', 'lightning'))
    .pipe(gulp.dest('./inc/vk-mobile-nav/package/'));
  gulp.src(['./inc/term-color/package/*'])
    .pipe(replace('lightning-pro', 'lightning'))
    .pipe(gulp.dest('./inc/term-color/package/'));
  done()
})

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

// ファイル結合
gulp.task('js_build', function () {
  return gulp.src([
    './assets/_js/_common.js',
    './assets/_js/_master.js',
    './assets/_js/_header_fixed.js',
    './assets/_js/_sidebar-fixed.js',
    './assets/_js/_vk-prlx.min.js',
    './inc/vk-mobile-nav/package/js/vk-mobile-nav.js',
  ])
    .pipe(concat('lightning.min.js'))
    .pipe(babel({
      presets: ['@babel/env']
    }))
    .pipe(jsmin())
    .pipe(gulp.dest('./assets/js/'));
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
  gulp.watch(['./inc/vk-components/*.php'], gulp.series('components_copy'));
  gulp.watch(['./assets/_scss/**','./inc/vk-mobile-nav/package/css/**','./inc/vk-components/**/*.css'], gulp.series('sass_common'));
  gulp.watch(['./assets/js/_*.js', './inc/vk-mobile-nav/package/js/**'], gulp.series('js_build'));
  gulp.watch(['./inc/woocommerce/_scss/**'], gulp.series('sass_woo'));
  gulp.watch(['./library/bootstrap-4/scss/**.scss'], gulp.series('sass_bs4'));
  gulp.watch(['./design-skin/origin/_scss/**/*.scss'], gulp.series('sass_skin'));
  gulp.watch(['./design-skin/origin2/_scss/**/*.scss'], gulp.series('sass_skin2'));
  gulp.watch(['./design-skin/foundation/_scss/**/*.scss'], gulp.series('sass_skin2', 'dist_foundation'));
  done();
});

// copy dist ////////////////////////////////////////////////
gulp.task('copy_dist', function () {
  var test_dir = '../../../../../../update/app/public/wp-content/themes/lightning'
  var dist_dir = path.join(process.cwd(), 'dist', theme_name)

  // reset dist dir
  fs.rmdir(dist_dir, (e) => {})
  var g = gulp.src(
    [
      '*.php',
      'style.css',
      'readme.txt',
      'screenshot.png',
      'assets/css/*.css',
      'assets/js/*.js',
      'assets/images/*',
      'design-skin/**/css/*',
      'design-skin/**/fonts/*',
      'design-skin/**/*.php',
      'inc/**/*.php',
      'inc/font-awesome/**/*',
      'inc/vk-mobile-nav/**/*',
      'library/**/css/*',
      'library/**/fonts/*',
      'library/**/js/*',
      'template-parts/**',
      '!**/*.scss',
      '!.*'
    ], {
      base: process.cwd()
    }
  ).pipe(gulp.dest(dist_dir))

  fs.access(test_dir, fs.constants.W_OK, (e) => {
    if (!e) {
      g.pipe(gulp.dest(test_dir))
    }
  });

  return g
});

gulp.task('dist_pro', function () {
  return gulp.src(
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
      base: process.cwd()
    }
  )
    .pipe(gulp.dest('../lightning-pro/')); // dist/lightningディレクトリに出力
});

gulp.task('build', gulp.series(
  'sass_common',
  'sass_bs4',
  'sass_skin',
  'sass_skin2',
  'sass_woo',
  'components_copy',
  'js_build',
));
gulp.task('dist', gulp.series('text-domain','copy_dist'))
gulp.task('default',  gulp.series('text-domain', 'watch'))
