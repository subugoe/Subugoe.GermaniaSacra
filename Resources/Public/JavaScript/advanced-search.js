$(function() {
  return $('#advancedSearch').submit(function(e) {
    var json, search;
    e.preventDefault();
    json = JSON.stringify($('#advancedSearch').serializeArray());
    search = $.post('/search', json);
    search.done(function(data) {
      return console.dir(data);
    });
    return search.fail(function(data) {
      return console.dir(data);
    });
  });
});
