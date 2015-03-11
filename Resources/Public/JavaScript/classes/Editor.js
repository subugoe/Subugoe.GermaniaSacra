// Generated by CoffeeScript 1.7.1
var __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

germaniaSacra.Editor = (function() {
  function Editor(type) {
    var self;
    this.type = type;
    this.scope = $('#edit');
    self = this;
    this.scope.hide();
    $('input[type=url]', this.scope).keyup(function() {
      return $(this).parent().next(".link").html($(this).val() ? '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' : '');
    });
    $('fieldset .multiple .remove', this.scope).click();
    $(':input:not([name=uUID])', this.scope).change(function() {
      $(this).closest("label").addClass("dirty");
      $('body').addClass('dirty');
      return $('[type=submit]', self.scope).prop('disabled', false);
    });
    $('.close', this.scope).click(function(e) {
      if (!$('.dirty', self.scope).length || confirm(germaniaSacra.messages.askUnsavedChanges)) {
        $(this).parent().closest('section[id]').slideUp();
        $('#search, #list').slideDown();
        $('.dirty', self.scope).removeClass('dirty');
        return e.preventDefault();
      }
    });
    $('form', this.scope).submit(function(e) {
      e.preventDefault();
      $(':disabled', this.scope).prop('disabled', false).addClass('disabled');
      if ($(this).find(':input[name=uUID]').first().val().length) {
        self.update();
      } else {
        self.create();
      }
      return $('.disabled', self.scope).prop('disabled', true);
    });
    $('.coordinates').geopicker();
  }

  Editor.prototype["new"] = function() {
    var $form;
    $form = $('form', this.scope);
    $form.clearForm();
    $('#search, #list').slideUp();
    $(this.scope).slideDown();
    $form.find('select[name=personallistenstatus] option:contains("Erfassung")').prop('selected', true);
    $('select', this.scope).autocomplete();
    $form.find('input[type=url]').keyup();
    return $form.find('textarea').autosize();
  };

  Editor.prototype.create = function(data) {
    var $form;
    $form = $('form', this.scope);
    return $.post(this.type + '/create', $form.serialize()).done(function(respond, status, jqXHR) {
      germaniaSacra.message('entryCreated');
      $form.find('.dirty').removeClass('dirty');
      return $('body').removeClass('dirty');
    }).fail(function() {
      return germaniaSacra.message('entryCreateError');
    });
  };

  Editor.prototype.edit = function(id) {
    var $form, type;
    type = this.type;
    $form = $('form', this.scope);
    $form.clearForm();
    $('#search, #list').slideUp();
    germaniaSacra.message('loading', false);
    return $.getJSON("" + type + "/edit/" + id).done((function(_this) {
      return function(obj) {
        var $fieldset, $input, name, value;
        for (name in obj) {
          value = obj[name];
          $input = $form.find(":input[data-type=" + name + "], :input[name='" + name + "']").first();
          if ($input.is(':checkbox')) {
            $input.val(1);
            if (value) {
              $input.prop('checked', true);
            }
          } else if ($input.is('select.ajax')) {
            $input.html($('<option />', {
              value: value.uUID,
              text: value.name
            }).attr('selected', true));
          } else {
            $input.val(value);
          }
        }
        $fieldset = $('#klosterdaten', _this.scope);
        if ($fieldset.length) {
          $fieldset.find('label :input').each(function() {
            var val;
            name = $(this).attr('name');
            if (name) {
              name = name.replace('[]', '');
            }
            val = obj[name];
            return $(this).val(val);
          });
        }
        $fieldset = $('#klosterorden', _this.scope);
        if ($fieldset.length && (obj.klosterorden != null)) {
          $.each(obj.klosterorden, function(index, value) {
            if (index > 0) {
              $fieldset.find('.multiple:last()').addInputs(0);
            }
            return $fieldset.find('.multiple:last() label :input').each(function() {
              name = $(this).attr('name');
              if (typeof name === 'undefined') {
                return;
              }
              name = name.replace('[]', '');
              return $(this).val(value[name]);
            });
          });
        }
        $fieldset = $('#klosterstandorte', _this.scope);
        if ($fieldset.length && (obj.klosterstandorte != null)) {
          $fieldset.find('.multiple:eq(0)').removeInputs(0);
          $.each(obj.klosterstandorte, function(index, value) {
            if (index > 0) {
              $fieldset.find('.multiple:last()').addInputs(0);
            }
            return $fieldset.find('.multiple:last() label :input').each(function() {
              var checkedCondition, text, val;
              name = $(this).attr('name');
              if (typeof name === 'undefined') {
                return;
              }
              name = name.replace('[]', '');
              val = value[name];
              if (name === 'wuestung') {
                checkedCondition = value[name] === 1;
                return $(this).prop('checked', checkedCondition);
              } else if (name === 'ort') {
                $(this).html($('<option />', {
                  value: value.uUID,
                  text: value.ort
                }).attr('selected', true));
                return $(this).change(function() {
                  var bistum;
                  bistum = $(this).closest('.multiple').find('[name="bistum[]"]');
                  return $.get("" + type + "/searchBistum/" + ($(this).val()), function(uUID) {
                    var text;
                    bistum.val(uUID).change();
                    text = bistum.find(':selected').text();
                    return bistum.prop('disabled', (__indexOf.call(germaniaSacra.notSpecifiedValues, text) < 0)).trigger('refresh');
                  });
                });
              } else if (name === 'bistum') {
                $(this).val(value[name]);
                text = $(this).find(':selected').text();
                return $(this).prop('disabled', (__indexOf.call(germaniaSacra.notSpecifiedValues, text) < 0));
              } else {
                return $(this).val(value[name]);
              }
            });
          });
        }
        $fieldset = $('#links', _this.scope);
        if ($fieldset.length && (obj.url != null)) {
          $fieldset.find('.multiple:eq(0)').removeInputs(0);
          $.each(obj.url, function(index, value) {
            if (value.url_typ_name === 'GND') {
              $fieldset.find('[name=gnd]').val(value.url);
              return $fieldset.find('[name=gnd_label]').val(value.url_label);
            } else if (value.url_typ_name === 'Wikipedia') {
              $fieldset.find('[name=wikipedia]').val(value.url);
              return $fieldset.find('[name=wikipedia_label]').val(value.url_label);
            } else {
              $fieldset.find('.multiple:last()').addInputs(0);
              return $fieldset.find('.multiple:last() label :input').each(function() {
                name = $(this).attr('name');
                if (typeof name === 'undefined') {
                  return;
                }
                name = name.replace('[]', '');
                return $(this).val(value[name]);
              });
            }
          });
        }
        $fieldset = $('#literatur', _this.scope);
        if ($fieldset.length && (obj.literatur != null)) {
          $.each(obj.literatur, function(index, value) {
            if (index > 0) {
              $fieldset.find('.multiple:last()').addInputs(0);
            }
            return $fieldset.find('.multiple:last() label :input').each(function() {
              name = $(this).attr('name');
              if (typeof name === 'undefined') {
                return;
              }
              name = name.replace('[]', '');
              return $(this).val(value[name]);
            });
          });
        }
        _this.scope.slideDown();
        germaniaSacra.hideMessage();
        $form.find('select').autocomplete();
        $form.find('input[type=url]').keyup();
        $form.find('textarea').autosize();
        return $('[type=submit]', _this.scope).prop('disabled', true);
      };
    })(this)).fail(function() {
      return germaniaSacra.message('dataLoadError');
    });
  };

  Editor.prototype.update = function() {
    var $form, uuid;
    $form = $('form', this.scope);
    uuid = $form.find(':input[name=uUID]:first').val();
    return $.post("" + this.type + "/update/" + uuid, $form.serialize()).done((function(_this) {
      return function(respond, status, jqXHR) {
        germaniaSacra.keepSelectOptions = false;
        germaniaSacra.message('changesSaved');
        $form.find('.dirty').removeClass('dirty');
        $('body').removeClass('dirty');
        $('[type=submit]', _this.scope).prop('disabled', true);
        $('.close', _this.scope).click();
        return germaniaSacra.list.reload();
      };
    })(this)).fail(function() {
      return germaniaSacra.message('changesSaveError');
    });
  };

  return Editor;

})();
