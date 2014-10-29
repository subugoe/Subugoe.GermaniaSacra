// Generated by CoffeeScript 1.7.1
var dataTable, deleteAction, editListAction, initList, updateListAction;

dataTable = null;

initList = function(type) {
  editListAction(type);
  return $("#list form").submit(function(e) {
    e.preventDefault();
    if ($(this).find("input[name=uuid]:checked, input[name=uUID]:checked").length === 0) {
      message("Wählen Sie bitte mindestens einen Eintrag aus.");
      return false;
    } else {
      updateListAction(type);
      return true;
    }
  });
};

editListAction = function(type) {
  var $table, $this, $ths, ajaxSuccess, columns, orderBy, selectOptions;
  $this = $('#list');
  if (!$this.length) {
    alert('There has to be a <section> whose id equals type');
    return;
  }
  $('#search, #list').hide();
  message(s_loading, false);
  $table = $this.find("table:eq(0)");
  $table.find("thead th").not(":first").not(":last").each(function() {
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
  selectOptions = {};
  dataTable = $table.DataTable({
    sAjaxSource: '/entity/' + type,
    columns: columns,
    autoWidth: false,
    pageLength: 10,
    columnDefs: [
      {
        bSortable: false,
        aTargets: ["not-sortable"]
      }
    ],
    dom: "lipt",
    language: {
      url: "/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json"
    },
    order: [[orderBy, "asc"]],
    fnServerData: function(sSource, aoData, fnCallback, oSettings) {
      return oSettings.jqXHR = $.ajax({
        cache: false,
        dataType: 'json',
        type: 'GET',
        url: sSource,
        data: aoData,
        success: [ajaxSuccess, fnCallback]
      });
    },
    fnDrawCallback: function() {
      var $tr;
      $tr = $table.find('tbody tr:not(.processed)');
      $tr.children().each(function() {
        var $input, $th, obj, selectName, _i, _len, _ref;
        $th = $table.find('th[data-name]').eq($(this).index());
        if ($th.length) {
          if ($th.data('input') === 'checkbox') {
            $input = $('<input type="checkbox"/>');
          } else {
            $input = $("<" + ($th.data('input')) + "/>");
          }
          $input.attr('name', $th.data('name'));
          if ($th.data('input') === 'select') {
            selectName = $th.data('name');
            if (selectOptions[selectName] != null) {
              _ref = selectOptions[selectName];
              for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                obj = _ref[_i];
                $input.append($('<option/>').text(obj.name).attr('value', obj.uuid));
              }
            }
          } else if ($th.data('input') === 'checkbox') {
            if ($(this).text() === '1') {
              $input.prop('checked', true);
            }
          }
          return $(this).html($input.val($(this).text()));
        }
      });
      $tr.each(function() {
        var uuid;
        uuid = $(this).find(':input[name=uuid]').val();
        $(this).find("textarea").autosize();
        return $(this).find(":input:not([name=uuid]):not([name=uUID])").change(function() {
          return $(this).closest("td").addClass("dirty").closest("tr").find(":checkbox:eq(0)").prop("checked", true);
        });
      });
      return $tr.addClass('processed');
    }
  }, ajaxSuccess = function(json) {
    $('#search, #list').slideDown();
    $('#message').slideUp();
    return selectOptions.bearbeitungsstatus = json.bearbeitungsstatus;
  });
  $table.on("click", ".edit", function(e) {
    var uuid;
    e.preventDefault();
    uuid = $(this).closest('tr').find(':input[name=uuid], :input[name=uUID]').first().val();
    return editAction(type, uuid);
  });
  $table.on("click", ".delete", function(e) {
    var uuid;
    e.preventDefault();
    uuid = $(this).closest('tr').find(':input[name=uuid], :input[name=uUID]').first().val();
    return deleteAction(type, uuid);
  });
  dataTable.columns().eq(0).each(function(colIdx) {
    return $("input", dataTable.column(colIdx).header()).click(function(e) {
      return e.stopPropagation();
    }).on("keyup change", function() {
      return dataTable.column(colIdx).search(this.value).draw();
    });
  });
  $("body").append('<input id="uuid-filter" type="hidden">');
  $("#uuid-filter").change(function() {
    return dataTable.column(0).search(this.value, true, false).draw();
  });
};

updateListAction = function(type) {
  var $form, $rows, formData;
  $form = $('#list form');
  $rows = dataTable.$('tr').has('td:first input:checked');
  formData = {};
  formData.data = {};
  $rows.each(function() {
    var uuid;
    uuid = $form.find('input[name=uuid], input[name=uUID]').first().val();
    formData.data[uuid] = {};
    return $form.find(':input:not([name=uuid]):not([name=uUID])').each(function(i, input) {
      if (input.name) {
        formData.data[uuid][input.name] = input.value;
      }
    });
  });
  formData.__csrfToken = $('#csrf').val();
  $.post(type + '/updateList', formData).done(function(respond, status, jqXHR) {
    if (type === 'kloster') {
      $.post("updateSolrAfterListUpdate", {
        uuids: respond
      });
    }
    message('Ihre Änderungen wurden gespeichert.');
    return $form.find('.dirty').removeClass('.dirty');
  }).fail(function(jqXHR, textStatus) {
    return message('Fehler: Daten konnten nicht gespeichert werden.');
  });
};

deleteAction = function(type, id) {
  var $this, check, csrf;
  $this = $(this);
  check = confirm('Wollen Sie diesen Eintrag wirklich löschen?');
  if (check === true) {
    csrf = $('#csrf').val();
    $.post(type + '/delete/' + id, {
      __csrfToken: csrf
    }).done(function(respond, status, jqXHR) {
      if (status === 'success') {
        return message('Der Eintrag wurde gelöscht.');
      }
    }).fail(function(jqXHR, textStatus) {
      return message('Fehler: Eintrag konnte nicht gelöscht werden.');
    });
  }
};
