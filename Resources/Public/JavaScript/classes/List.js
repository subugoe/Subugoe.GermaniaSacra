// Generated by CoffeeScript 1.7.1
germaniaSacra.List = (function() {
  function List(type) {
    var self;
    this.type = type;
    this.scope = $('#list');
    self = this;
    this.dataTable = null;
    this.editList();
    $('.new', this.scope).click(function(e) {
      e.preventDefault();
      return germaniaSacra.editor["new"]();
    });
    $('form', this.scope).submit(function(e) {
      e.preventDefault();
      if ($(this).find('input[name=uUID]:checked').length === 0) {
        germaniaSacra.message('Wählen Sie bitte mindestens einen Eintrag aus.');
        return false;
      } else {
        self.updateList();
        return true;
      }
    });
  }

  List.prototype.editList = function() {
    var $table, $ths, ajaxSuccess, columns, orderBy, self;
    self = this;
    $('#search, #list').hide();
    germaniaSacra.message(germaniaSacra.messages.loading, false);
    $table = this.scope.find('table:eq(0)');
    $table.find('thead th').not(':first').not(':last').each(function() {
      return $(this).append('<div><input type="text"></div>');
    });
    $ths = $table.find('th');
    columns = [];
    $ths.each(function() {
      if ($(this).data('name') != null) {
        return columns.push({
          data: $(this).data('name')
        });
      }
    });
    columns.push({
      "class": 'no-wrap show-only-on-hover',
      data: null,
      defaultContent: $ths.last().data('html')
    });
    orderBy = $table.find('th.order-by').index();
    if (orderBy < 0) {
      orderBy = 1;
    }
    this.dataTable = $table.DataTable({
      sAjaxSource: '/entity/' + this.type,
      columns: columns,
      autoWidth: false,
      pageLength: 100,
      columnDefs: [
        {
          bSortable: false,
          aTargets: ['not-sortable']
        }
      ],
      dom: 'lipt',
      language: {
        url: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'
      },
      order: [[orderBy, 'asc']],
      fnServerData: function(sSource, aoData, fnCallback, oSettings) {
        return oSettings.jqXHR = $.ajax({
          cache: false,
          dataType: 'json',
          type: 'GET',
          url: sSource,
          data: aoData,
          success: [ajaxSuccess, fnCallback],
          error: function() {
            return germaniaSacra.message('Fehler: Daten konnten nicht geladen werden.');
          }
        });
      },
      fnDrawCallback: function() {
        var $tr;
        $tr = $table.find('tbody tr:not(.processed)');
        $tr.children().each(function() {
          var $input, $td, $th, dataInput, name, option, optionUuid, text, uuid, value, _ref, _ref1, _ref2;
          $td = $(this);
          $th = $table.find('th[data-name]').eq($td.index());
          value = $td.text().trim();
          if ($th.length) {
            dataInput = $th.data('input');
            name = $th.data('name');
            if (dataInput === 'checkbox') {
              $input = $('<input type="checkbox"/>');
              if (value === '1') {
                $input.prop('checked', true);
              }
              if (name !== 'uUID') {
                value = 1;
              }
            } else {
              $input = $("<" + dataInput + "/>");
            }
            if (dataInput.indexOf('select') === 0) {
              if (germaniaSacra.selectOptions[name] != null) {
                _ref = germaniaSacra.selectOptions[name];
                for (uuid in _ref) {
                  text = _ref[uuid];
                  $input.append($('<option/>').text(text).attr('value', uuid));
                }
                _ref1 = germaniaSacra.selectOptions[name];
                for (optionUuid in _ref1) {
                  option = _ref1[optionUuid];
                  if (option === value) {
                    value = optionUuid;
                    break;
                  }
                }
              } else {
                _ref2 = value.trim().split(':', 2), uuid = _ref2[0], text = _ref2[1];
                if (uuid) {
                  $input.append($('<option/>').text(text).attr('value', uuid));
                  value = uuid;
                } else {
                  value = '';
                }
              }
            }
            return $(this).html($input.attr('name', name).val(value));
          }
        });
        $tr.each(function() {
          var uuid;
          uuid = $(this).find(':input[name=uUID]').val();
          $(this).find('textarea').autosize();
          return $(this).find(':input:not([name=uUID])').change(function() {
            $(this).closest('td').addClass('dirty').closest('tr').find(':checkbox:eq(0)').prop('checked', true);
            $('body').addClass('dirty');
            return $(':submit[type=submit]', self.scope).prop('disabled', false);
          });
        });
        $tr.find('select').autocomplete();
        return $tr.addClass('processed');
      }
    }, ajaxSuccess = function(json) {
      var entity, index, key, value, _ref, _results;
      $('#search, #list').slideDown();
      germaniaSacra.hideMessage();
      _ref = json.data;
      _results = [];
      for (index in _ref) {
        entity = _ref[index];
        json.data[index].bearbeitungsstatus = germaniaSacra.selectOptions.bearbeitungsstatus[entity.bearbeitungsstatus];
        _results.push((function() {
          var _results1;
          _results1 = [];
          for (key in entity) {
            value = entity[key];
            if (!value) {
              _results1.push(json.data[index][key] = ' ');
            } else {
              _results1.push(void 0);
            }
          }
          return _results1;
        })());
      }
      return _results;
    });
    $table.on('click', '.edit', function(e) {
      var uuid;
      e.preventDefault();
      uuid = $(this).closest('tr').find(':input[name=uUID]').first().val();
      return germaniaSacra.editor.edit(uuid);
    });
    $table.on('click', '.delete', function(e) {
      var uuid;
      e.preventDefault();
      uuid = $(this).closest('tr').find(':input[name=uUID]').first().val();
      return self["delete"](uuid);
    });
    this.dataTable.columns().eq(0).each(function(colIdx) {
      return $('input', self.dataTable.column(colIdx).header()).click(function(e) {
        return e.stopPropagation();
      }).on('keyup change', function() {
        return self.dataTable.column(colIdx).search(this.value).draw();
      });
    });
    $('#uuid-filter').change(function() {
      return self.dataTable.column(0).search(this.value, true, false).draw();
    });
  };

  List.prototype.updateList = function() {
    var $form, $rows, formData;
    $form = $('form', this.scope);
    $rows = this.dataTable.$('tr').has('td:first input:checked');
    formData = {};
    formData.data = {};
    $rows.each(function(i, row) {
      var uuid;
      uuid = $(row).find(':input[name=uUID]').first().val();
      formData.data[uuid] = {};
      return $(row).find(':input:not([name=uUID])').each(function(i, input) {
        if (!$(input).is(':checkbox') || $(input).prop('checked')) {
          if (input.name) {
            formData.data[uuid][input.name] = input.value;
          }
        }
      });
    });
    formData.__csrfToken = $('#csrf').val();
    $.post(this.type + '/updateList', formData).done((function(_this) {
      return function(respond, status, jqXHR) {
        germaniaSacra.message('Ihre Änderungen wurden gespeichert.');
        $form.find('.dirty').removeClass('dirty');
        $form.find('input[name=uUID]').prop('checked', false);
        $('body').removeClass('dirty');
        return $(':submit[type=submit]', _this.scope).prop('disabled', true);
      };
    })(this)).fail(function(jqXHR, textStatus) {
      return germaniaSacra.message('Fehler: Daten konnten nicht gespeichert werden.');
    });
  };

  List.prototype["delete"] = function(uuid) {
    var check, csrf;
    check = confirm(germaniaSacra.messages.askDelete);
    if (check === true) {
      csrf = $('#csrf').val();
      $.post(this.type + '/delete/' + uuid, {
        __csrfToken: csrf
      }).done((function(_this) {
        return function(respond, status, jqXHR) {
          if (status === 'success') {
            _this.dataTable.row($('tr').has("td:first input[value='" + uuid + "']")).remove().draw();
            return germaniaSacra.message('Der Eintrag wurde gelöscht.');
          }
        };
      })(this)).fail(function(jqXHR, textStatus) {
        return germaniaSacra.message('Fehler: Eintrag konnte nicht gelöscht werden.');
      });
    }
  };

  List.prototype.reload = function() {
    return this.dataTable.ajax.reload();
  };

  return List;

})();
