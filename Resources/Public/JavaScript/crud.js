// Save the Kloster list
$.fn.update_list = function(url) {

	$.post(url, $("#UpdateList").serialize())
		.done(function(respond, status, jqXHR) {
			if (status == "success") {
				$('#confirm').modal({
					closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
					position: ["20%"],
					overlayId: 'confirm-overlay',
					containerId: 'confirm-container',
					onShow: function(dialog) {
						$('.message').append("Der Eintrag wurde erfolgreich bearbeitet.")
					}
				})
				setTimeout(function() {
					window.location.href = ''
				}, 1000)
			}
		})
		.fail(function(jqXHR, textStatus) {
			$('#confirm').modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
				position: ["20%", ],
				overlayId: 'confirm-overlay',
				containerId: 'confirm-container',
				onShow: function(dialog) {
					$('.message').append(jqXHR.responseText)
				}
			})
		})
}

// Fill the Kloster list
$.fn.populate_liste = function(page) {

	var $this = $(this);

	$.getJSON("klosterListAll", function(response) {

		// Fill "Status" select fields
		var bearbeitungsstatusArray = response[1]
		var $inputBearbeitungsstatus = $("select[name='bearbeitungsstatus']")
		$inputBearbeitungsstatus.empty()
		$.each(bearbeitungsstatusArray, function(k, v) {
			$.each(v, function(k1, v1) {
				$inputBearbeitungsstatus.append($("<option>", { value: v1, html: k1 }))
			})
		})

		var klosters = response[0];

		var $trTemplate = $('#list tbody tr:first')

		// Add a text input to each header cell used for search
		$("#list thead th").not(':first').not(':last').each( function () {
			$(this).append( '<div><input type="text"></div>' )
		});

		var table = $('#list').DataTable({
			autoWidth: false,
			columnDefs: [
				{ bSortable : false, aTargets : [ 'no-sorting' ] },
				{ "width": "10%", "targets": 1 },
			],
			dom: 'lipt', // 'l' - Length changing, 'f' - Filtering input, 't' - The table, 'i' - Information, 'p' - Pagination, 'r' - pRocessing
			language: {
				"url": "/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json"
			},
			order: [[ 3, "asc" ]],
			fnDrawCallback: function() {
				// Since only visible textareas can be autosized, this has to be called after every page render
				$('#list textarea').autosize()
				// Mark row as dirty when changed
				$('#list :input:not(:checkbox)').change( function() {
					$(this)
						.closest('td').addClass('dirty')
							.closest('tr').find(':checkbox:eq(0)').prop('checked', true)
				})
			}
		})

		// Apply the search
		table.columns().eq( 0 ).each( function ( colIdx ) {
			$( 'input', table.column( colIdx ).header() )
				.click( function(e) {
					e.stopPropagation()
				})
				.on( 'keyup change', function () {
					table
						.column( colIdx )
						.search( this.value )
						.draw()
				})
		})

		// Filter table by "search all" return values
		$('body').append('<input id="uuidFilter" type="hidden">');
		$('#uuidFilter').change( function() {
			table
				.column(0)
				.search( this.value, true, false ) // enable regex, disable smart search (enabling both will not work)
				.draw()
		})

		// Fill the table
		$.each(klosters, function(index, kloster) {

			// Clone with triggers for edit and delete
			var $tr = $trTemplate.clone(true)

			$tr.find(':input').each(function() {
				var name = $(this).attr('name')
				if (typeof name === 'undefined') return;
				var val = kloster[name]
				if ($(this).is('[type=checkbox]')) {
					if (name == "auswahl") {
						$(this).val(kloster.uuid)
					}
				} else
					if ($(this).is('select')) {
						if (name == "bearbeitungsstatus") {
							$tr.find("select[name=bearbeitungsstatus] option").each(function(i, opt) {
								if (opt.value == val) {
									$(opt).attr('selected', 'selected');
								}
							});
						}
						else {
							$(this).append('<option>' + val + '</option>')
						}
					} else {
						if (name !== "__csrfToken") {
							$(this).val(val)
						}
					}
				if (name !== "__csrfToken" && name !== "auswahl") {
					$(this).attr('name', name + '[' + kloster.uuid + ']')
					// WORKAROUND: DataTables 1.10.1 has a bug that prevents sorting of :input elements, so we use plain text for sorting
					$('<span class="val"/>').text( $(this).is('select') ? $(this).find(':selected').text() : $(this).val() ).hide().insertBefore($(this))
				}

			});

			$tr.find('.edit').attr('href', "edit/" + kloster.uuid)
			$tr.find('.delete').attr('href', "delete/" + kloster.uuid)

			$tr.find('input.csrf').attr('id', "csrf" + index)

			table.row.add($tr)

		})

		// Remove template row and draw table
		table.row( $trTemplate ).remove().draw()

	})

}

// Fill the select fields with options
$.fn.populate_selects = function() {

	var url = 'getOptions'

	$.getJSON(url, function(response) {

		// Fill select fields with available options
		// TODO: Fill "URL Typ" selects
		var options = {}
		options.bearbeitungsstatus = response[0]
		options.personallistenstatus = response[1]
		options.band = response[2]
		options.literatur = response[3]
		options.bistum = response[4]
		options.orden = response[5]
		options.klosterstatus = response[6]
		options.bearbeiter = response[7]

		$.each( options, function(name, values) {
			var $select = $('select[name="' + name + '"], select[name="' + name + '[]"]')
			$select.empty().append( $("<option>", { value: '', text: '' }) )
			$.each(values, function(index, object) {
				$.each(object, function(value, uuid) {
					$select.append( $("<option>", { value: uuid, text: value }) )
				})
			})
		})

	})

}

$.fn.new_kloster = function() {

	$('#edit').clear_form()

	$("#browse").slideUp()
	$('#edit').slideDown()
	$('#edit .autocomplete').autocomplete()
	$('#edit textarea').trigger('autosize.resize');
	$('#edit input[type=url]').keyup()

}

// Load a single Kloster into the edit form
$.fn.populate_kloster = function(url) {

	var $this = $(this)
	$('#edit').clear_form()

	$.getJSON(url, function(kloster) {

		var uuid = kloster.uuid
		var update_url = "update/" + uuid
		$('#EditKloster').attr("action", update_url)

		var $fieldset = $('#kloster')
		$fieldset.find('label :input').each(function() {
			var name = $(this).attr('name')
			if (typeof name === 'undefined') return
			name = name.replace('[]', '')
			var val = kloster[name]
			$(this).val(val)
		})
		$fieldset.find('.bearbeiter').text(kloster.bearbeiter || '?')
		$fieldset.find('.changeddate').text(kloster.changeddate ? kloster.changeddate.date.substr(0, kloster.changeddate.date.indexOf('.')) : '?')

		var $fieldset = $('#klosterorden')
		$.each(kloster.klosterorden, function(index, value) {
			if (index > 0) $fieldset.find('.multiple:last()').addInputs(0)
			$fieldset.find('.multiple:last() label :input').each(function() {
				var name = $(this).attr('name')
				if (typeof name === 'undefined') return;
				name = name.replace('[]', '')
				$(this).val( value[name] )
			})
		})

		var $fieldset = $('#klosterstandorte')
		$.each(kloster.klosterstandorte, function(index, value) {
			if (index > 0) $fieldset.find('.multiple:last()').addInputs(0)
			$fieldset.find('.multiple:last() label :input').each(function() {
				var name = $(this).attr('name')
				if (typeof name === 'undefined') return;
				name = name.replace('[]', '')
				var val = value[name];
				if ( name == "wuestung" ) {
					if ( name == "wuestung" ) {
						$(this).prop('checked', value[name] == 1)
					}
				} else if ( name ==  "ort" ) {
					$(this).html( $("<option />", { value: value['uuid'], text: value['ort'] }).attr('selected', true) )
				} else if ( name == "bistum" ) {
					$(this).val( value[name] ).prop('disabled', typeof value[name] !== 'undefined')
				} else {
					$(this).val( value[name] )
				}
			})
		})

		var $fieldset = $('#links')
		$.each(kloster.url, function(index, value) {
			if (value.url_typ == 'GND') {
				$('#gnd').val(value.url)
			} else if (value.url_typ == 'Wikipedia') {
				$('#wikipedia').val(value.url)
			} else {
				$fieldset.find('.multiple:last()').addInputs(0)
				$fieldset.find('.multiple:last() label :input').each(function() {
					var name = $(this).attr('name')
					if (typeof name === 'undefined') return;
					name = name.replace('[]', '')
					$(this).val( value[name] )
				})
			}
		})
		$fieldset.find('.multiple:eq(0)').removeInputs(0)

		var $fieldset = $('#literatur')
		$.each(kloster.literatur, function(index, value) {
			if (index > 0) $fieldset.addInputs(0)
			$fieldset.find('.multiple:last() label :input').each(function() {
				var name = $(this).attr('name')
				if (typeof name === 'undefined') return;
				name = name.replace('[]', '')
				$(this).val( value )
			})
		})

		$("#browse").slideUp()
		$('#edit').slideDown()
		$('#edit .autocomplete').autocomplete()
		$('#edit textarea').trigger('autosize.resize');
		$('#edit input[type=url]').keyup()

	})
}

// Update a single Kloster
$.fn.update_kloster = function(url) {

	$.post(url, $("#EditKloster").serialize())
		.done(function(respond, status, jqXHR) {
			if (status == "success") {
				$('#confirm').modal({
					closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
					position: ["20%", ],
					overlayId: 'confirm-overlay',
					containerId: 'confirm-container',
					onShow: function(dialog) {
						$('.message').append("Der Eintrag wurde erfolgreich bearbeitet.")
					}
				})
				setTimeout(function() {
					window.location.href = ''
				}, 1000)
			}
		})
		.fail(function(jqXHR, textStatus) {
			$('#confirm').modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
				position: ["20%", ],
				overlayId: 'confirm-overlay',
				containerId: 'confirm-container',
				onShow: function(dialog) {
					$('.message').append(jqXHR.responseText)
				}
			})
		})

}

// Create a new Kloster
$.fn.create_kloster = function() {

	$.post('create', $("#NewKloster").serialize())
		.done(function(respond, status, jqXHR) {
			if (status == "success") {
				var dataArray = $.parseJSON(respond)
				var uuid = dataArray[0];
				var addKlosterId_url = "addKlosterId";
				$.get(addKlosterId_url, { uuid: uuid})
				$('#confirm').modal({
					closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
					position: ["20%", ],
					overlayId: 'confirm-overlay',
					containerId: 'confirm-container',
					onShow: function(dialog) {
						$('.message').append("Der Eintrag wurde erfolgreich gespeichert.")
					}
				})
			}
		})
		.fail(function(jqXHR, textStatus) {
			alert(jqXHR.responseText)
		})

}

// Delete a single Kloster
$.fn.delete_kloster = function(url, csrf) {

	check = confirm("Wollen Sie diesen Eintrag wirklich löschen?")

	if (check == true) {
		$.post(url, { __csrfToken: csrf })
			.done(function(respond, status, jqXHR) {
					if (status == "success") {
						$('#confirm').modal({
							closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
							position: ["20%", ],
							overlayId: 'confirm-overlay',
							containerId: 'confirm-container',
							onShow: function(dialog) {
								$('.message').append("Der Eintrag wurde erfolgreich gelöscht.")
							}
						})
						setTimeout(function() {
							window.location.href = ''
						}, 1000)
					}
				})
			.fail(function(jqXHR, textStatus) {
				alert(jqXHR.responseText)
			})
	}
}

$.fn.clear_form = function() {
	$(this).find('.multiple:gt(0)').removeInputs(0)
	$(this).find(':input:not([type=submit]):not([type=hidden])').val('')
	$(this).find('.bearbeiter, .changeddate').text('?')
}