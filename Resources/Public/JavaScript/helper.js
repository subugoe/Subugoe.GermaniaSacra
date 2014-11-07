// Generated by CoffeeScript 1.7.1
var confirmDiscardChanges, message, ucfirst;

confirmDiscardChanges = function() {
  return confirm("Sind Sie sicher, dass Sie diese Seite verlassen wollen? Ihre Änderungen wurden nicht gespeichert.");
};

message = function(text, withTimestampAndCloseButton) {
  var $close, $message, date, timestamp;
  if (withTimestampAndCloseButton == null) {
    withTimestampAndCloseButton = true;
  }
  $message = $('#message');
  date = new Date();
  timestamp = withTimestampAndCloseButton ? "<span class='timestamp'>" + (date.toLocaleString()) + "</span>" : '';
  text = "<span class='text'>" + text + "</span>";
  $message.hide().html(timestamp + text).slideDown();
  if (withTimestampAndCloseButton) {
    $close = $("<i class='hover close icon-close right'>&times;</i>");
    $close.appendTo($message).click(function(e) {
      e.preventDefault();
      return $message.slideUp();
    });
  }
  return $('html, body').animate({
    scrollTop: $message.offset().top
  });
};

ucfirst = function(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
};

$.fn.addInputs = function(slideTime) {
  if (slideTime == null) {
    slideTime = 0;
  }
  return this.each(function() {
    var $clone, $fieldset;
    $fieldset = $(this).closest("fieldset");
    $clone = $(this).clone(true);
    $clone.clearForm();
    $clone.find("select").autocomplete();
    $clone.insertAfter($(this)).hide().slideDown(slideTime);
    return $fieldset.find("button.remove").prop("disabled", $fieldset.find(".multiple:not(.dying)").length === 1);
  });
};

$.fn.removeInputs = function(slideTime) {
  if (slideTime == null) {
    slideTime = 0;
  }
  return this.each(function() {
    var $fieldset;
    $fieldset = $(this).closest("fieldset");
    $fieldset.find(".multiple").length > 1 && $(this).addClass("dying").slideUp(slideTime, this.remove);
    return $fieldset.find("button.remove").prop("disabled", $fieldset.find(".multiple:not(.dying)").length === 1);
  });
};

$.fn.clearForm = function() {
  return this.each(function() {
    var $form;
    $form = $(this);
    $form.find("label").removeClass('dirty');
    $form.find(":input").prop('disabled', false);
    $form.find(":input:not([name=__csrfToken]):not(:checkbox):not(:submit)").val('');
    $form.find(":checkbox, :radio").prop('checked', false);
    $form.find('select option:contains("keine Angabe"), select option:contains("unbekannt")').prop('selected', true);
    $form.find(".multiple:gt(0)").removeInputs();
    return $form.find("button.remove").prop('disabled', true);
  });
};
