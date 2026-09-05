const fs = require('fs-extra');
const glob = require('glob');
const path = require('path');

const filesToCopy = [
  "./LICENSE",
  "./*theme.json",
  "./**/*.php",
  "./**/*.txt",
  "./**/*.css",
  "./**/*.png",
  "./**/*.jpg",
  "./inc/**",
  "./languages/**",
  "./vendor/**",
  "./_g2/inc/**",
  "./_g2/assets/**",
  "./_g2/library/**",
  "./_g3/inc/**",
  "./_g3/assets/**",
];

// 配布物に含めないファイル。WordPress.org のアップロード時に走る自動レビューが
// 読めない形式のファイルを含んでいると弾かれるため、コピー段階で落とす。
// glob は dot ファイルを列挙せず、ディレクトリ自身が列挙されて丸ごとコピーされる経路が
// あるため、excludedPaths では止まらない。fs.copy 側の filter で除外する。

// macOS の Finder が作るファイル。
const excludedFileNames = [".DS_Store", "._.DS_Store"];

// 開発専用の設定ファイル。vendor 配下のライブラリが同梱してくるもので、実行には不要。
const excludedDevFileNames = ["phpunit.xml.dist", "phpunit.xml", "composer.lock"];

// 開発専用のディレクトリ。配下ごと除外する。
const excludedDirNames = [".github", ".vscode", ".circleci"];

const isExcluded = (src) => {
  const base = path.basename(src);

  if (excludedFileNames.includes(base)) {
    return true;
  }

  // ディレクトリ自身と、その配下のファイルの両方を落とす。
  const segments = src.split(path.sep);
  if (segments.some((segment) => excludedDirNames.includes(segment))) {
    return true;
  }

  // vendor 配下に限り、開発用の dot ファイル（.gitignore / .phpcs.xml / .eslintrc.js 等）と
  // 開発専用の設定ファイルを除外する。テーマ本体側の dot ファイルは対象にしない。
  const isInVendor = segments.includes("vendor");
  if (isInVendor && (base.startsWith(".") || excludedDevFileNames.includes(base))) {
    return true;
  }

  return false;
};

const copyFilter = (src) => !isExcluded(src);

const excludedPaths = [
  "./_g2/assets/css/map/**",
  "./_g3/node_modules/**/*.*",
  "./_g3/assets/css/map/**",
  "./.vscode/**",
  "./bin/**",
  "./dist/**",
  "./node_modules/**/*.*",
  "./tests/**",
];

const destination = "dist/lightning";

async function copyFiles() {
  try {
    // distディレクトリが存在しない場合は作成
    await fs.ensureDir(destination);

    for (const file of filesToCopy) {
      const files = glob.sync(file, { ignore: excludedPaths });
      for (const src of files) {
        const dest = path.join(destination, path.relative('.', src));
        await fs.copy(src, dest, { filter: copyFilter });
        console.log(`Copied ${src} to ${dest}`);
      }
    }
  } catch (err) {
    console.error(err);
  }
}

copyFiles();
