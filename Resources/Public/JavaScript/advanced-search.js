$(function() {
  $('#advancedSearch').submit(function(e) {
    var json, search;
    e.preventDefault();
    json = $('#advancedSearch').serializeArray();

    search = $.post('/search', json);
    search.done(function(data) {
	    alert(data);
      console.dir(data);
    });
    search.fail(function(data) {
	    alert($.parseJSON(data.status));
      console.dir(data);
    });
  });
});
