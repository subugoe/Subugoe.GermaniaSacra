// Generated by CoffeeScript 1.7.1

/*
Autocomplete for select fields

Overlaying input field, data AJAXed on type
Requires returned JSON to contain $uuid and $name for each item
 */
var delay;

$.fn.autocomplete = function() {
  return this.each(function() {
    var $input, $list, $overlay, $select, $spinner;
    $(this).siblings('.autocomplete').remove();
    $select = $(this).hide();
    $input = $('<input type="text">').val($select.find(':selected').text());
    $spinner = $('<i class="spinner spinner-icon"/>');
    $spinner.hide();
    $list = $('<ol class="list"/>');
    $list.css({
      top: $('select:eq(0)').outerHeight()
    });
    $overlay = $('<div class="overlay autocomplete"/>').append($input, $spinner, $list);
    $overlay.insertAfter($select);
    $input.click(function() {
      this.select();
      return $list.slideDown().scrollTop(0).find('li:eq(0)').addClass('current');
    });
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
    $input.blur(function() {
      $list.slideUp();
      return $select.find(':selected').text();
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
};

$.fn.setSelected = function($el) {
  return this.each(function() {
    return $(this).empty().append('<option value="' + $el.data('uuid') + '" selected>' + $el.text() + '</option>');
  });
};

delay = (function() {
  var timer;
  timer = 0;
  return function(callback, ms) {
    clearTimeout(timer);
    return timer = setTimeout(callback, ms);
  };
})();
