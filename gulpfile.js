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
// http://blog.e-riverstyle.com/2014/02/gulpspritesmithcss-spritegulp.html
// 同期的に処理してくれる
var runSequence = require('run-sequence');

gulp.task( 'copy', function() {
    gulp.src( './library/bootstrap/css/bootstrap.min.css'  )
    .pipe(rename({prefix: "_",extname: ".scss"})) // 拡張子をscssに
    .pipe( gulp.dest( './design_skin/origin/_scss/' ) ); // _scss ディレクトリに保存
    gulp.src( './library/bootstrap/fonts/**'  )
    .pipe( gulp.dest( './design_skin/origin/fonts/' ) ); // _scss ディレクトリに保存
} );

// ファイル結合
gulp.task('scripts', function() {
  return gulp.src(['./library/bootstrap/js/bootstrap.min.js','./js/_master.js'])
    .pipe(concat('all.js'))
    .pipe(gulp.dest('./js/'));

});
gulp.task('scripts_header_fixed', function() {
  return gulp.src(['./library/bootstrap/js/bootstrap.min.js','./js/_master.js','./js/_header_fixed.js'])
    .pipe(concat('all_in_header_fixed.js'))
    .pipe(gulp.dest('./js/'));
});

// js最小化
gulp.task('jsmin', function () {
  gulp.src(['./js/all.js','./js/all_in_header_fixed.js'])
  .pipe(plumber()) // エラーでも監視を続行
  .pipe(jsmin())
  .pipe(rename({suffix: '.min'}))
  .pipe(gulp.dest('./js'));
});

// Watch
gulp.task('watch', function() {
    gulp.watch('js/_master.js', ['scripts','scripts_header_fixed']);
    gulp.watch('js/_header_fixed.js', ['scripts','scripts_header_fixed']);
    gulp.watch('js/all.js', ['jsmin']);
});

gulp.task('default', ['copy','scripts','jsmin','watch']);
gulp.task('compile', ['copy','scripts','jsmin']);

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
            ],
            { base: './' }
        )
        .pipe( gulp.dest( 'dist' ) ); // distディレクトリに出力
} );
// gulp.task('build:dist',function(){
//     /* ここで、CSS とか JS をコンパイルする */
// });

gulp.task('dist', function(cb){
    // return runSequence( 'build:dist', 'copy', cb );
    // return runSequence( 'build:dist', 'copy_dist', cb );
    return runSequence( 'copy_dist', cb );
});
