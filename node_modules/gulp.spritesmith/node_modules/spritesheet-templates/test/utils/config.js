exports.singleItem = {
  items: [{
    name: 'sprite-dash-case', x: 0, y: 0, width: 10, height: 20
  }],
  spritesheet: {
    width: 10, height: 20, image: 'nested/dir/spritesheet.png'
  }
};

exports.multipleItems = {
  items: [{
    name: 'sprite-dash-case', x: 0, y: 0, width: 10, height: 20
  }, {
    name: 'sprite_snake_case', x: 10, y: 20, width: 20, height: 30
  }, {
    name: 'spriteCamelCase', x: 30, y: 50, width: 50, height: 50
  }],
  spritesheet: {
    width: 80, height: 100, image: 'nested/dir/spritesheet.png'
  }
};
