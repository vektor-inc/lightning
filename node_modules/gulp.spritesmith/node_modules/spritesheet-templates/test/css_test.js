var configUtils = require('./utils/config');
var testUtils = require('./utils/test');

describe('An array of image positions, dimensions, and names', function () {
  testUtils.setInfo(configUtils.multipleItems);

  describe('processed by `spritesheet-templates` into CSS', function () {
    testUtils.runTemplater(null);
    testUtils.assertOutputMatches(__dirname + '/expected_files/css.css');

    testUtils.runFakeJigsaw();
    it('is valid CSS', function (done) {
      var css = this.result;
      testUtils._assertValidCss(css, done);
    });
  });
});

// Edge case test for filepaths with quotes
describe('An array of image positions, dimensions, and names', function () {
  testUtils.setInfo({
    items: configUtils.multipleItems.items,
    spritesheet: {
      width: configUtils.multipleItems.spritesheet.width,
      height: configUtils.multipleItems.spritesheet.height,
      image: 'nested/dir/( \'")/spritesheet.png'
    }
  });

  describe('processed by `spritesheet-templates` into CSS with an escapable selector', function () {
    testUtils.runTemplater(null);
    testUtils.assertOutputMatches(__dirname + '/expected_files/css-quote-filepath.css');

    testUtils.runFakeJigsaw();
    it('is valid CSS', function (done) {
      var css = this.result;
      testUtils._assertValidCss(css, done);
    });
  });
});

// Edge case test for https://github.com/Ensighten/grunt-spritesmith/issues/104
describe('An array of image positions, dimensions, and names', function () {
  testUtils.setInfo(configUtils.multipleItems);

  describe('processed by `spritesheet-templates` into CSS with an escapable selector', function () {
    testUtils.runTemplater({
      formatOpts: {
        cssSelector: function (item) {
          return '.hello > .icon-' + item.name;
        }
      }
    });
    testUtils.assertOutputMatches(__dirname + '/expected_files/css-html-selector.css');

    testUtils.runFakeJigsaw();
    it('is valid CSS', function (done) {
      var css = this.result;
      testUtils._assertValidCss(css, done);
    });
  });
});
