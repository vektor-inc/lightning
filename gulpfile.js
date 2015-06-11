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
// sudo npm install gulp.spritesmith --save-dev
var spritesmith = require('gulp.spritesmith');
// http://blog.e-riverstyle.com/2014/02/gulpspritesmithcss-spritegulp.html

// // Task
// gulp.task('cssmin', function () {
//   gulp.src('css/**/*.css')
//   .pipe(plumber()) // エラーでも監視を続行
//   .pipe(cssmin())
//   .pipe(rename({suffix: '.min'}))
//   .pipe(gulp.dest('css'));
// });

gulp.task( 'copy', function() {
    gulp.src( './bootstrap/css/bootstrap.min.css'  )
    .pipe(rename({prefix: "_",extname: ".scss"})) // 拡張子をscssに
    .pipe( gulp.dest( './_scss/' ) ); // _scss ディレクトリに保存
} );

// ファイル結合
gulp.task('scripts', function() {
  return gulp.src(['./bootstrap/js/bootstrap.min.js','./js/master.js'])
    .pipe(concat('all.js'))
    .pipe(gulp.dest('./js/'));
});

// js最小化
gulp.task('jsmin', function () {
  gulp.src('./js/all.js')
  .pipe(plumber()) // エラーでも監視を続行
  .pipe(jsmin())
  .pipe(rename({suffix: '.min'}))
  .pipe(gulp.dest('./js'));
});

// // 画像のCSSスプライト
// gulp.task('sprite', function () {
//   //スプライトにする愉快な画像達
//   var spriteData = gulp.src('./images/_sprite/*.png') 
//   .pipe(spritesmith({
//     imgName: 'sprite.png', //スプライトの画像
//     cssName: '_sprite.scss', //生成されるscss
//     imgPath: '../images/sprite.png', //生成されるscssに記載されるパス
//     cssFormat: 'scss', //フォーマット
//     cssVarMap: function (sprite) {
//       sprite.name = 'sprite-' + sprite.name; //VarMap(生成されるScssにいろいろな変数の一覧を生成)
//     }
//   }));
//   spriteData.img.pipe(gulp.dest('./images/')); //imgNameで指定したスプライト画像の保存先
//   spriteData.css.pipe(gulp.dest('./_scss/')); //cssNameで指定したcssの保存先
// });

// Watch
gulp.task('watch', function() {
    // gulp.watch('css/*.css', ['cssmin'])
    // gulp.watch('js/*.js', ['scripts']);
    gulp.watch('js/master.js', ['scripts']);
    gulp.watch('js/all.js', ['jsmin']);
    gulp.watch('_scss/style.scss', ['copy']);
});

// gulp.task('default', ['scripts','watch','sprite']);
gulp.task('default', ['scripts','jsmin','watch']);