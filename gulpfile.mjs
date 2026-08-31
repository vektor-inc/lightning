import gulp from 'gulp';
import replace from 'gulp-replace';
// ファイルリネーム（.min作成用）
import rename from 'gulp-rename';
// エラーでも監視を続行させる
import plumber from 'gulp-plumber';
import sassModule from 'gulp-sass';
import autoprefixer from 'gulp-autoprefixer';
import CleanCSS from 'clean-css';
import through from 'through2';
import applySourceMap from 'vinyl-sourcemaps-apply';
import path from 'path';
// メディアクエリの統合・並べ替え
import postcss from 'postcss';
import { Transform } from 'stream';
import sourcemaps from 'gulp-sourcemaps';
import aliases from 'gulp-style-aliases';
import nodeSass from 'sass';

const sass = sassModule(nodeSass);

let error_stop = true

// メディアクエリの並び順グループ。数値が小さいほどファイルの先頭側に出力する
const MQ_GROUP = { other: 0, all: 1, minWidth: 2, minHeight: 3, maxWidth: 4, maxHeight: 5, print: 6 };

// メディアクエリを「下限を持つもの」「上限を持つもの」などに分類し、並べ替え用の値を返す。
// `min-width` / `max-width` に加えて `768px < width` / `width <= 768px` のような範囲構文も判定する。
function classifyMediaQuery(params) {
	const lower = params.match(/min-width\s*:\s*([\d.]+)px/) ||
		params.match(/([\d.]+)px\s*<=?\s*width/) ||
		params.match(/width\s*>=?\s*([\d.]+)px/);
	if (lower) {
		return { group: MQ_GROUP.minWidth, value: parseFloat(lower[1]), descending: false };
	}
	const upper = params.match(/max-width\s*:\s*([\d.]+)px/) ||
		params.match(/width\s*<=?\s*([\d.]+)px/) ||
		params.match(/([\d.]+)px\s*>=?\s*width/);
	if (upper) {
		return { group: MQ_GROUP.maxWidth, value: parseFloat(upper[1]), descending: true };
	}
	const minHeight = params.match(/min-height\s*:\s*([\d.]+)px/);
	if (minHeight) {
		return { group: MQ_GROUP.minHeight, value: parseFloat(minHeight[1]), descending: false };
	}
	const maxHeight = params.match(/max-height\s*:\s*([\d.]+)px/);
	if (maxHeight) {
		return { group: MQ_GROUP.maxHeight, value: parseFloat(maxHeight[1]), descending: true };
	}
	if (/print/.test(params)) {
		return { group: MQ_GROUP.print, value: 0, descending: false };
	}
	if (/all/.test(params)) {
		return { group: MQ_GROUP.all, value: 0, descending: false };
	}
	return { group: MQ_GROUP.other, value: 0, descending: false };
}

// 同じ条件のメディアクエリを1つにまとめ、下限指定は小さい順・上限指定は大きい順に並べ替える PostCSS プラグイン
function sortMediaQueries() {
	return {
		postcssPlugin: 'lightning-sort-media-queries',
		OnceExit: function (root, helpers) {
			const merged = new Map();
			root.each(function (node) {
				if (node.type !== 'atrule' || node.name !== 'media') {
					return;
				}
				if (!merged.has(node.params)) {
					merged.set(node.params, new helpers.AtRule({ name: node.name, params: node.params, source: node.source }));
				}
				node.each(function (child) {
					merged.get(node.params).append(child.clone());
				});
				node.remove();
			});
			const entries = [...merged.keys()].map(function (params, index) {
				const info = classifyMediaQuery(params);
				info.params = params;
				info.index = index;
				return info;
			});
			entries.sort(function (a, b) {
				if (a.group !== b.group) {
					return a.group - b.group;
				}
				if (a.value !== b.value) {
					return a.descending ? b.value - a.value : a.value - b.value;
				}
				return a.index - b.index;
			});
			entries.forEach(function (entry) {
				root.append(merged.get(entry.params));
			});
		},
	};
}
sortMediaQueries.postcss = true;

// 上記プラグインを gulp のストリームに適用する
function mergeMediaQueries() {
	return new Transform({
		objectMode: true,
		transform: function (file, enc, cb) {
			postcss([sortMediaQueries()])
				.process(file.contents.toString(), { from: undefined })
				.then(function (result) {
					file.contents = Buffer.from(result.css);
					cb(null, file);
				})
				.catch(cb);
		},
	});
}

// gulp-clean-css is pinned to clean-css@4.2.3, which has a bug that strips the
// descendant combinator (space) inside :not() selectors, so this replaces it
// with a custom gulp plugin that calls clean-css@5 directly.
// gulp-clean-css は clean-css@4.2.3 に固定されており、
// :not() 内の子孫結合子（半角スペース）を誤って除去するバグがあるため、
// clean-css@5 を直接叩く自前の gulp プラグインに置き換えている。
// https://github.com/vektor-inc/Lightning/pull/1401
function cleanCss(options) {
  return through.obj(function (file, enc, cb) {
    const _options = Object.assign({}, options || {});

    if (file.isNull()) {
      return cb(null, file);
    }
    if (file.isStream()) {
      this.emit('error', new Error('cleanCss: Streaming not supported!'));
      return cb(null, file);
    }

    if (file.sourceMap) {
      _options.sourceMap = JSON.parse(JSON.stringify(file.sourceMap));
    }

    const content = {
      [file.path]: { styles: file.contents ? file.contents.toString() : '' }
    };
    if (!_options.rebaseTo && _options.rebase !== false) {
      _options.rebaseTo = path.dirname(file.path);
    }

    new CleanCSS(_options).minify(content, (errors, css) => {
      if (errors) {
        return cb(errors.join(' '));
      }

      file.contents = Buffer.from(css.styles);

      if (css.sourceMap) {
        const iMap = JSON.parse(css.sourceMap);
        const oMap = Object.assign({}, iMap, {
          file: path.relative(file.base, file.path),
          sources: iMap.sources.map(mapSrc => path.relative(file.base, mapSrc))
        });
        applySourceMap(file, oMap);
      }

      cb(null, file);
    });
  });
}

function src(list) {
  if(error_stop) {
    return gulp.src(list)
  }else{
    return gulp.src(list).pipe(plumber())
  }
}

gulp.task('text-domain', function (done) {
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
	// vk-footer-customize.
	gulp.src(['./_g2/inc/vk-footer-customize/package/*.php'])
		.pipe(replace("vk-footer-customize-textdomain",'lightning'))
		.pipe(gulp.dest('./_g2/inc/vk-footer-customize/package/'));
	done();
});

gulp.task('sass_common_g2', function (done) {
  src(['./_g2/assets/_scss/**/*.scss'])
    .pipe(
      sass({
        includePaths: [
          './_g2/assets/scss',
          './vendor/vektor-inc/vk-component/src/assets/scss'
        ]
      }
    ))
    .pipe(mergeMediaQueries())
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/assets/css'))
  done()
});

gulp.task('sass_bs4_g2', function (done) {
  src(['./_g2/library/bootstrap-4/scss/**.scss'])
    .pipe(aliases({
      "@bootstrap": "./node_modules/bootstrap/scss"
    }))
    .pipe(sass())
    .pipe(mergeMediaQueries())
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(rename(
      {
        suffix: '.min'
      }
    ))
    .pipe(gulp.dest('./_g2/library/bootstrap-4/css/'))
    .pipe(gulp.dest('../lightning-pro/library/bootstrap-4/css/'));
  done();
});

gulp.task('sass_skin_g1', function (done) {
  src(['_g2/design-skin/origin/_scss/**/*.scss'])
    .pipe(sass())
    .pipe(mergeMediaQueries())
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/design-skin/origin/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin/css'));
  done();
});

gulp.task('sass_skin_g2', function (done) {
  src(['_g2/design-skin/origin2/_scss/**/*.scss'])
    .pipe(sass())
    .pipe(mergeMediaQueries())
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/design-skin/origin2/css'))
    .pipe(gulp.dest('../lightning-pro/design-skin/origin2/css'));
  done();
});

gulp.task('sass_woo_g2', function (done) {
  return src(['./_g2/plugin-support/woocommerce/_scss/**.scss'])
    .pipe(sass())
    .pipe(mergeMediaQueries())
    .pipe(autoprefixer())
    .pipe(cleanCss())
    .pipe(gulp.dest('./_g2/plugin-support/woocommerce/css/'))
    .pipe(gulp.dest('../lightning-pro/plugin-support/woocommerce/css/'));
});

gulp.task('sass_booking_package_g2', function (done) {
	return src(['./_g2/plugin-support/booking-package/_scss/**.scss'])
	  .pipe(sass())
	  .pipe(mergeMediaQueries())
	  .pipe(autoprefixer())
	  .pipe(cleanCss())
	  .pipe(gulp.dest('./_g2/plugin-support/booking-package/css/'))
	  .pipe(gulp.dest('../lightning-pro/plugin-support/booking-package/css/'));
  });

gulp.task('sass_bbpress_g2', function (done) {
	return src(['./_g2/plugin-support/bbpress/_scss/**.scss'])
	  .pipe(sass())
	  .pipe(mergeMediaQueries())
	  .pipe(autoprefixer())
	  .pipe(cleanCss())
	  .pipe(gulp.dest('./_g2/plugin-support/bbpress/css/'))
	  .pipe(gulp.dest('../lightning-pro/plugin-support/bbpress/css/'));
  });
gulp.task('sass_bbpress_g3', function (done) {
return src(['./_g3/plugin-support/bbpress/_scss/**.scss'])
	.pipe(sass())
	.pipe(mergeMediaQueries())
	.pipe(autoprefixer())
	.pipe(cleanCss())
	.pipe(gulp.dest('./_g3/plugin-support/bbpress/css/'));
});

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

// Watch
gulp.task('watch_g2', function (done) {
  error_stop = false
  gulp.watch(['./_g2/assets/_scss/**','./_g2/inc/vk-mobile-nav/package/css/**','/vendor/vektor-inc/vk-component/**/*.css'] );
  gulp.watch(['./_g2/plugin-support/woocommerce/_scss/**'], gulp.series('sass_woo_g2'));
  gulp.watch(['./_g2/plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress_g2'));
  gulp.watch(['./_g2/plugin-support/booking-package/_scss/**'], gulp.series('sass_booking_package_g2'));
  gulp.watch(['./_g2//library/bootstrap-4/scss/**.scss'], gulp.series('sass_bs4_g2'));
  gulp.watch(['./_g2/design-skin/origin/_scss/**/*.scss'], gulp.series('sass_skin_g1'));
  gulp.watch(['./_g2/design-skin/foundation/_scss/**/*.scss'], gulp.series('sass_skin2', 'dist_foundation'));
  done();
});

gulp.task('default_g2',  gulp.series('text-domain', 'watch_g2'));
gulp.task('sass_g2',  gulp.series( 'sass_common_g2', 'sass_bs4_g2', 'sass_skin_g1', 'sass_skin_g2', 'sass_woo_g2', 'sass_bbpress_g2', 'sass_booking_package_g2' ));
gulp.task('dist_g2',  gulp.series( 'text-domain', 'sass_g2', ));

// G3 ////////////////////////////////////////////////

gulp.task('sass_common_g3', function (done) {
	src(['./_g3/assets/_scss/**/*.scss'])
    .pipe(aliases({
		"@bootstrap": "./node_modules/bootstrap/scss"
	  }))
	.pipe(
	  sass({
		includePaths: [
		  './_g3/assets/scss',
		]
	  }
	))
	  .pipe(sourcemaps.init())
	  .pipe(mergeMediaQueries())
	  .pipe(autoprefixer())
	  .pipe(cleanCss())
	  .pipe(gulp.dest('./_g3/assets/css'))
	done()
  });
    
  gulp.task('sass_skin_g3', function (done) {
	src(['./_g3/design-skin/origin3/_scss/**/*.scss'])
	.pipe(aliases({
	  "@bootstrap": "./node_modules/bootstrap/scss"
	}))
	.pipe(
	  sass({
		includePaths: [
		  './_g3/design-skin/origin3/_scss',
		]
	  }
	))
	  .pipe(sourcemaps.init())
	  .pipe(mergeMediaQueries())
	  .pipe(autoprefixer())
	  .pipe(cleanCss())
	  .pipe(gulp.dest('./_g3/design-skin/origin3/css'))
	done()
  });
  
  gulp.task('sass_woo_g3', function (done) {
	return src(['./_g3/plugin-support/woocommerce/_scss/**.scss'])
	  .pipe(sass())
	  .pipe(mergeMediaQueries())
	  .pipe(autoprefixer())
	  .pipe(cleanCss())
	  .pipe(gulp.dest('./_g3/plugin-support/woocommerce/css/'))
  });
  
  gulp.task('sass_bbpress_g3', function (done) {
	  return src(['./_g3/plugin-support/bbpress/_scss/**.scss'])
		.pipe(sass())
		.pipe(mergeMediaQueries())
		.pipe(autoprefixer())
		.pipe(cleanCss())
		.pipe(gulp.dest('./_g3/plugin-support/bbpress/css/'))
	});
  
	gulp.task('sass_the_event_calendar_g3', function (done) {
		return src(['./_g3/plugin-support/the-events-calendar/_scss/**.scss'])
		  .pipe(sass())
		  .pipe(mergeMediaQueries())
		  .pipe(autoprefixer())
		  .pipe(cleanCss())
		  .pipe(gulp.dest('./_g3/plugin-support/the-events-calendar/css/'))
	  });
	
	gulp.task('sass_booking_package_g3', function (done) {
		return src(['./_g3/plugin-support/booking-package/_scss/**.scss'])
		  .pipe(sass())
		  .pipe(mergeMediaQueries())
		  .pipe(autoprefixer())
		  .pipe(cleanCss())
		  .pipe(gulp.dest('./_g3/plugin-support/booking-package/css/'))
	  });

	gulp.task('sass_snow_monkey_forms_g3', function (done) {
		return src(['./_g3/plugin-support/snow-monkey-forms/_scss/**.scss'])
		  .pipe(sass())
		  .pipe(mergeMediaQueries())
		  .pipe(autoprefixer())
		  .pipe(cleanCss())
		  .pipe(gulp.dest('./_g3/plugin-support/snow-monkey-forms/css/'))
	  });

  // Watch
gulp.task('watch_g3', function (done) {
	error_stop = false
	gulp.watch(['./_g3/assets/_scss/**','./_g3/inc/vk-mobile-nav/package/css/**','/vendor/vektor-inc/vk-component/**/*.css'], gulp.series('sass_g3'));
	gulp.watch(['./_g3/design-skin/origin3/_scss/**'], gulp.series('sass_skin_g3'));
	gulp.watch(['./_g3/plugin-support/woocommerce/_scss/**'], gulp.series('sass_woo_g3'));
	gulp.watch(['./_g3/plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress_g3'));
	gulp.watch(['./_g3/plugin-support/the-events-calendar/_scss/**'], gulp.series('sass_the_event_calendar_g3'));
	gulp.watch(['./_g3/plugin-support/booking-package/_scss/**'], gulp.series('sass_booking_package_g3'));
	gulp.watch(['./_g3/plugin-support/snow-monkey-forms/_scss/**'], gulp.series('sass_snow_monkey_forms_g3'));
	done();
});

gulp.task('watch', function (done) {
	error_stop = false
	gulp.watch(['./_g2/assets/_scss/**','./_g2/inc/vk-mobile-nav/package/css/**','/vendor/vektor-inc/vk-component/**/*.css'] );
	gulp.watch(['./_g2/plugin-support/woocommerce/_scss/**'], gulp.series('sass_woo_g2'));
	gulp.watch(['./_g2/plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress_g2'));
	gulp.watch(['./_g2//library/bootstrap-4/scss/**.scss'], gulp.series('sass_bs4_g2'));
	gulp.watch(['./_g2/design-skin/origin/_scss/**/*.scss'], gulp.series('sass_skin_g1'));
	gulp.watch(['./_g3/assets/_scss/**','./_g3/inc/vk-mobile-nav/package/css/**','/vendor/vektor-inc/vk-component/**/*.css'], gulp.series('sass_common_g3'));
	gulp.watch(['./_g3/design-skin/origin3/_scss/**'], gulp.series('sass_skin_g3'));
	gulp.watch(['./_g3/plugin-support/woocommerce/_scss/**'], gulp.series('sass_woo_g3'));
	gulp.watch(['./_g3/plugin-support/bbpress/_scss/**'], gulp.series('sass_bbpress_g3'));
	gulp.watch(['./_g3/plugin-support/the-events-calendar/_scss/**'], gulp.series('sass_the_event_calendar_g3'));
	gulp.watch(['./_g3/plugin-support/booking-package/_scss/**'], gulp.series('sass_booking_package_g3'));
	gulp.watch(['./_g3/plugin-support/snow-monkey-forms/_scss/**'], gulp.series('sass_snow_monkey_forms_g3'));
	done();
});

gulp.task('default',  gulp.series( 'watch'));
// _g3/assets/_scss/style-theme-json.sass が _g3/assets/css/を読み込んでいるため２回まわしている.
gulp.task('sass_g3',  gulp.series( 'sass_common_g3', 'sass_common_g3', 'sass_skin_g3', 'sass_woo_g3', 'sass_bbpress_g3', 'sass_the_event_calendar_g3', 'sass_booking_package_g3', 'sass_snow_monkey_forms_g3' ));
gulp.task('dist_g3',  gulp.series( 'text-domain', 'sass_g3' ));

gulp.task('sass',  gulp.series( 'sass_g2', 'sass_g3' ));

// copy dist ////////////////////////////////////////////////

// change to copy-files.js