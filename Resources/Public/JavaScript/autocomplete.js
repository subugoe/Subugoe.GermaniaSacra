
/*

Autocomplete for select fields

Overlaying input field, data AJAXed on type
Requires returned JSON to contain $uuid and $name for each item
 */
var delay;

$.fn.extend({
  autocomplete: function() {
    return this.each(function() {
      var $input, $list, $overlay, $select;
      $select = $(this);
      $input = $('<input type="text">').val($select.find(':selected').text());
      $input.click(function() {
        return this.select();
      });
      $list = $('<ul class="dropdown-list"/>');
      $list.css({
        top: $select.outerHeight()
      });
      $overlay = $('<div class="autocomplete"/>').append($input, $list);
      $overlay.css({
        width: $select.outerWidth(),
        height: $select.outerHeight(),
        position: 'absolute',
        right: 0,
        top: 0
      });
      $overlay.insertAfter($select);
      $input.on('input', function() {
        if ($input.val().length > 1) {
          return delay((function() {
            return $.ajax({
              url: '/searchOrt?searchString=' + encodeURIComponent($input.val()),
              type: 'GET',
              error: function() {
                return console.log('autocomplete ajax error');
              },
              success: function(data) {
                var json;
                json = $.parseJSON(data);
                $list.empty();
                $.each(json, function(index, element) {
                  return $list.append('<li data-uuid="' + element.uuid + '">' + element.name + '</li>');
                });
                $list.slideDown().find('li').first().addClass('current');
                return $list.find('li').click(function() {
                  $input.val($(this).text());
                  $select.setSelected($(this));
                  return $list.slideUp();
                });
              }
            });
          }), 500);
        }
      });
      return $input.on('keydown', function(e) {
        var $current;
        if ($list.is(':visible')) {
          $current = $list.find('.current');
          switch (e.which) {
            case 13:
              e.preventDefault();
              $input.val($current.text());
              $select.setSelected($current);
              return $list.slideUp();
            case 38:
              e.preventDefault();
              return $current.removeClass('current').prev().addClass('current');
            case 9:
            case 40:
              e.preventDefault();
              return $current.removeClass('current').next().addClass('current');
          }
        }
      });
    });
  },
  setSelected: function($data) {
    return this.each(function() {
      return $(this).empty().append('<option value="' + $data.data('uuid') + '" selected>' + $data.text() + '</option>');
    });
  }
});

delay = (function() {
  var timer;
  timer = 0;
  return function(callback, ms) {
    clearTimeout(timer);
    return timer = setTimeout(callback, ms);
  };
})();
