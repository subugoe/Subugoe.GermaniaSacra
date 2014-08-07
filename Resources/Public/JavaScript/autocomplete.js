
/*

Autocomplete for select fields

Overlaying input field, data AJAXed on type
Requires returned JSON to contain $uuid and $name for each item
 */
var delay;

$.fn.extend({
  autocomplete: function() {
    return this.each(function() {
      var $button, $input, $list, $overlay, $select, $spinner;
      $select = $(this).css({
        opacity: 0
      });
      $input = $('<input type="text">').val($select.find(':selected').text());
      $input.click(function() {
        return this.select();
      });
      $button = $('<button>&#9660;</button>');
      $spinner = $('<i class="spinner-icon"/>');
      $spinner.hide();
      $button.click(function() {
        if (ol.is(":visible")) {
          $input.blur();
        } else {
          $input.focus();
        }
        return false;
      });
      $list = $('<ol/>');
      $list.css({
        top: $select.outerHeight()
      });
      $overlay = $('<div class="autocomplete"/>').append($input, $button, $spinner, $list);
      $overlay.css({
        width: $select.outerWidth(),
        height: $select.outerHeight(),
        position: 'absolute',
        right: 0,
        top: 0
      });
      $overlay.insertAfter($select);
      $input.on('input', function() {
        if ($input.val().length > 0) {
          return delay((function() {
            $spinner.show();
            return $.ajax({
              url: '/searchOrt?searchString=' + encodeURIComponent($input.val()),
              type: 'GET',
              complete: function() {
                return $spinner.hide();
              },
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
                $list.slideDown().scrollTop(0).find('li').first().addClass('current');
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
      $input.on('blur', function() {
        $list.slideUp();
        return $input.val($select.find(':selected').text());
      });
      return $input.on('keydown', function(e) {
        var $current, $lis, index, li_height;
        if ($list.is(':visible')) {
          $lis = $list.children();
          li_height = $list.children(':eq(0)').outerHeight();
          $current = $list.find('.current');
          index = $list.children('.current').siblings().addBack().index($list.children('.current'));
          switch (e.which) {
            case 13:
              e.preventDefault();
              $input.val($current.text());
              $select.setSelected($current);
              return $list.slideUp();
            case 38:
              if (--index < 0) {
                index = $lis.length - 1;
              }
              $lis.removeClass('current').eq(index).addClass('current');
              $list.scrollTop(index * li_height - ($list.height() - li_height) / 2);
              return false;
            case 9:
            case 40:
              if (++index >= $lis.length) {
                index = 0;
              }
              $lis.removeClass('current').eq(index).addClass('current');
              $list.scrollTop(index * li_height - ($list.height() - li_height) / 2);
              return false;
            case 35:
            case 36:
            case 27:
              return $input.blur();
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