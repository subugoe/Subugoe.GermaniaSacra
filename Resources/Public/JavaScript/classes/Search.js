// Generated by CoffeeScript 1.7.1
var Search;

Search = (function() {
  function Search() {
    this.scope = $('#simple-search, #advanced-search');
    this.scope.submit(function() {
      var json, search;
      json = $(this).serializeArray();
      search = $.post('/search', json);
      search.success(function(data) {
        data = $.parseJSON(data);
        if (data.length) {
          return $('#uuid-filter').val(data.join('|')).change();
        } else {
          return $('#uuid-filter').val('```').change();
        }
      });
      return search.fail(function(data) {
        return alert('Suche fehlgeschlagen');
      });
    });
    $('.reset', this.scope).click(function(e) {
      e.preventDefault();
      $('#uuid-filter').val('').change();
      return $(this).parents('form').clearForm();
    });
  }

  return Search;

})();
