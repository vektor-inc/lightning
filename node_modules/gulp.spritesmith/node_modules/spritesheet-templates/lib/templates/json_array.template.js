function jsonArrayTemplate(params) {
  // Stringify the array of itemsitemObj
  var items = params.items;
  var retStr = JSON.stringify(items, null, 4);

  // Return the stringified JSON
  return retStr;
}

// Export our JSON template
module.exports = jsonArrayTemplate;
