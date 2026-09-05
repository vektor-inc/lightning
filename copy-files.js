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

// macOS の Finder が作るファイル。配布物に混ざると WordPress.org へのアップロードで弾かれる。
// glob は dot ファイルを列挙しないため excludedPaths では止まらず、ディレクトリごとコピーする
// fs.copy 側の filter で除外する。
const excludedFileNames = [".DS_Store", "._.DS_Store"];

const copyFilter = (src) => !excludedFileNames.includes(path.basename(src));

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
