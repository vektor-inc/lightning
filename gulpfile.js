var gulp = require('gulp');

var cssmin = require('gulp-cssmin');
// ファイルリネーム（.min作成用）
var rename = require('gulp-rename');
// ファイル結合
var concat = require('gulp-concat');
// js最小化
var jsmin = require('gulp-jsmin');
// エラーでも監視を続行させる
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var cmq = require('gulp-merge-media-queries');
// add vender prifix
var autoprefixer = require('gulp-autoprefixer');
var cleanCss = require('gulp-clean-css');

// http://blog.e-riverstyle.com/2014/02/gulpspritesmithcss-spritegulp.html
// 同期的に処理してくれる
var runSequence = require('run-sequence');

gulp.task('sass', function() {
  gulp.src(['design_skin/origin/_scss/**/*.scss'])
    .pipe(plumber({
      handleError: function(err) {
        console.log(err);
        this.emit('end');
      }
    }))
    .pipe(plumber())
    .pipe(sass())
    .pipe(cmq({
      log: true
    }))
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./design_skin/origin/css'))
  // .pipe(rename({
  //     suffix: '.min'
  // }))

  // .pipe(cleanCss())
  // .pipe(gulp.dest('design_skin/origin/css'))
});






gulp.task('copy', function() {
  gulp.src('./library/bootstrap/css/bootstrap.min.css')
    .pipe(rename({
      prefix: "_",
      extname: ".scss"
    })) // 拡張子をscssに
    .pipe(gulp.dest('./design_skin/origin/_scss/')); // _scss ディレクトリに保存
  gulp.src('./library/bootstrap/fonts/**')
    .pipe(gulp.dest('./design_skin/origin/fonts/')); // _scss ディレクトリに保存
});

// // ファイル結合
// gulp.task('concat', function() {
//   return gulp.src(['./library/bootstrap/js/bootstrap.min.js', './js/_master.js', './js/_header_fixed.js', './js/vk-prlx.min.js'])
//     .pipe(concat('lightning.js'))
//     .pipe(gulp.dest('./js/'));
// });
//
// // js最小化
// gulp.task('jsmin', function() {
//   gulp.src(['./js/lightning.js'])
//     .pipe(plumber()) // エラーでも監視を続行
//     .pipe(jsmin())
//     .pipe(rename({
//       suffix: '.min'
//     }))
//     .pipe(gulp.dest('./js/'));
// });


// ファイル結合
gulp.task('js_build', function() {
  return gulp.src(['./library/bootstrap/js/bootstrap.min.js', './js/_master.js', './js/_header_fixed.js', './js/vk-prlx.min.js'])
    // return gulp.src(['./library/bootstrap/js/bootstrap.min.js', './js/_master.js', './js/_header_fixed.js'])
    .pipe(concat('lightning.js'))
    .pipe(gulp.dest('./js/'))
    .pipe(jsmin())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest('./js/'));
});

// Watch
gulp.task('watch', function() {
  gulp.watch('js/_master.js', ['js_build']);
  gulp.watch('js/_header_fixed.js', ['js_build']);
  gulp.watch('js/vk-prlx.min.js', ['js_build']);
  // gulp.watch('js/lightning.js', ['jsmin']);
  gulp.watch('design_skin/origin/_scss/**/*.scss', ['sass']);
});

gulp.task('default', ['copy', 'js_build', 'watch']);
gulp.task('compile', ['copy', 'js_build']);

// copy dist ////////////////////////////////////////////////

gulp.task('copy_dist', function() {
  return gulp.src(
      [
        './**/*.php',
        './**/*.txt',
        './**/*.css',
        './**/*.png',
        './design_skin/**',
        './images/**',
        './inc/**',
        './js/**',
        './languages/**',
        './library/**',
        './template-parts/**',
        "!./tests/**",
        "!./dist/**",
        "!./node_modules/**/*.*"
      ], {
        base: './'
      }
    )
    .pipe(gulp.dest('dist/lightning')); // dist/lightningディレクトリに出力
});
// gulp.task('build:dist',function(){
//     /* ここで、CSS とか JS をコンパイルする */
// });

gulp.task('dist', function(cb) {
  // return runSequence( 'build:dist', 'copy', cb );
  // return runSequence( 'build:dist', 'copy_dist', cb );
  return runSequence('copy_dist', cb);
});