$(function() {
<<<<<<< HEAD
  $('#search, #advancedSearch').submit(function() {
    var json, search;
    json = $(this).serializeArray();
    search = $.post('/search', json);
    search.success(function(data) {
      data = $.parseJSON(data);
      if (data.length) {
        return $('#uuidFilter').val(data.join('|')).change();
      } else {
        return $('#uuidFilter').val('```').change();
      }
    });
    return search.fail(function(data) {
      return alert('Suche fehlgeschlagen');
=======
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
>>>>>>> ffaa0ae67eaef52c208947233fa7edca2568c0ec
    });
  });
  return $('#search .reset').click(function() {
    return $('#uuidFilter').val('').change();
  });
});
