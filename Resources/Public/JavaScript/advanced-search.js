$(function() {
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
    });
  });
  return $('#search .reset').click(function() {
    return $('#uuidFilter').val('').change();
  });
});
