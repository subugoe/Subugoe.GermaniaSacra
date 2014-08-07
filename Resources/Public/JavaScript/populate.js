$.fn.extend({

	// Die Kloster-Liste aktualisieren
	update_list: function(url) {

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
	},

	//Eintragsliste anzeigen
	populate_liste: function(page) {

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
					$("#list textarea").autosize()
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

	},

// Das Formular zum Editiren mit Daten des jeweiligen Eintrages ausfüllen
	populate_kloster: function(url) {

		var $this = $(this)

		$.getJSON(url, function(response) {

			// Fill select fields with available options
			// TODO: Fill "URL Typ" selects
			var options = {}
			options.bearbeitungsstatus = response[1]
			options.personallistenstatus = response[2]
			options.band = response[3]
			options.literatur = response[4]
			options.bistum = response[5]
			options.orden = response[6]
			options.klosterstatus = response[7]
			options.bearbeiter = response[8]

			$.each( options, function(name, values) {
				var $select = $('select[name="' + name + '"], select[name="' + name + '[]"]')
				$select.empty().append( $("<option>", { value: '', text: '' }) )
				$.each(values, function(index, object) {
					$.each(object, function(value, uuid) {
						$select.append( $("<option>", { value: uuid, text: value }) )
					})
				})
				$select.val( response[0][name] )
			})


			var kloster = response[0]
			var klosterstandorte = kloster.klosterstandorte
			var klosterorden = kloster.klosterorden
			var klosterurl = kloster.url
			var klosterliteratur = kloster.literatur

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
			$fieldset.find('.changeddate').text(kloster.changeddate || '?')

			var $fieldset = $('#klosterorden')
			$fieldset.find('.multiple:gt(0)').removeInputs(0)
			$.each(klosterorden, function(index, value) {
				if (index > 0) $fieldset.find('.multiple:last()').addInputs(0)
				$fieldset.find('.multiple:last() label :input').each(function() {
					var name = $(this).attr('name')
					if (typeof name === 'undefined') return;
					name = name.replace('[]', '')
					$(this).val( value[name] )
				})
			})

			var $fieldset = $('#klosterstandorte')
			$fieldset.find('.multiple:gt(0)').removeInputs(0)
			$.each(klosterstandorte, function(index, value) {
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
					} else {
						$(this).val( value[name] )
					}
				})
			})

			var $fieldset = $('#links')
			$fieldset.find('.multiple:gt(0)').removeInputs(0)
			$.each(klosterurl, function(index, value) {
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
			$fieldset.find('.multiple:gt(0)').remove()
			$.each(klosterliteratur, function(index, value) {
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
	},

	// Den bearbeiteten Eintrag aktualisieren
	update_kloster: function(url) {
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
	},

	// Das Formular zum Eintragen mit den Stammdaten ausfüllen
	new_kloster: function() {
		var url = "new";
		$.getJSON(url, function(response) {

			var bearbeitungsstatusArray = response[0];
			var $inputBearbeitungsstatus = $("select[name='new_bearbeitungsstatus']")
			$inputBearbeitungsstatus.empty()
			$.each(bearbeitungsstatusArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBearbeitungsstatus.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var personallistenstatusArray = response[1];
			var $inputPersonallistenstatus = $("select[name='new_personallistenstatus']")
			$inputPersonallistenstatus.empty()
			$.each(personallistenstatusArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputPersonallistenstatus.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var bandArray = response[2];
			var $inputBand = $("select[name='new_band']")
			$inputBand.empty()
			$inputBand.append($("<option>", { value: '', html: 'Kein Band' }))
			$.each(bandArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBand.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var literaturArray = response[3];
			var $inputLiteratur = $("select[name='literatur[]']")
			$inputLiteratur.empty()
			$.each(literaturArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputLiteratur.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var bistumArray = response[4];
			var $inputBistum = $("select[name='new_bistum[]']")
			$inputBistum.empty()
			$.each(bistumArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBistum.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var ordenArray = response[5];
			var $inputOrden = $("select[name='new_orden[]']")
			$inputOrden.empty()
			$.each(ordenArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputOrden.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var klosterstatusArray = response[6];
			var $inputKlosterstatus = $("select[name='new_klosterstatus[]']")
			$inputKlosterstatus.empty()
			$.each(klosterstatusArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputKlosterstatus.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var bearbeiterArray = response[7];
			var $inputBearbeiter = $("select[name='new_bearbeiter']")
			$inputBearbeiter.empty()
			$.each(bearbeiterArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBearbeiter.append($("<option>", { value: v1, html: k1 }))
				})
			})

		})
	},

	// Ein neues Kloster-Object wird eingetragen
	create_kloster: function() {
		var url = "create";
		$.post(url, $("#NewKloster").serialize())
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
					setTimeout(function() {
						window.location.href = ''
					}, 1000)
				}
			})
			.fail(function(jqXHR, textStatus) {
				alert(jqXHR.responseText)
			})

	},

	delete_kloster: function(url, csrf) {

		Check = confirm("Wollen Sie diesen Eintrag wirklich löschen?")

		if (Check == true) {
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
	},

})
