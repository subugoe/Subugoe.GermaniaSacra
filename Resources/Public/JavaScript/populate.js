$.fn.extend({

	// Die Kloster-Liste aktualisieren
	update_list: function(url) {

		$.post(url, $("#UpdateList").serialize())
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
			$.each(klosters, function(index, kloster) {

				// Clone with triggers for edit and delete
				var $tr = $this.find('tbody tr:first').clone(true)

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
						// WORKAROUND: DataTables 1.10.0 has a bug that prevents sorting of :input elements, so we use plain text for sorting
						$('<span class="val">' + $(this).val() + '</span>').hide().insertBefore($(this))
					}

				});

				//$tr.find('input#searchOrt:eq(' + index + ')').attr('tabindex', index + 1)
				//$tr.find('input#searchOrt:eq(' + (index + 1) + ')').attr('data-uuid', kloster.uuid)

				$tr.find('.edit').attr('href', "edit/" + kloster.uuid)
				$tr.find('.delete').attr('href', "delete/" + kloster.uuid)

				$tr.find('input.csrf').attr('id', "csrf" + index)

				$this.append($tr)

			})

			$this.find("tbody tr:first").remove()

			$('#list').dataTable({
				'dom': 'lifpt', // 'l' - Length changing, 'f' - Filtering input, 't' - The table, 'i' - Information, 'p' - Pagination, 'r' - pRocessing
				"language": {
					"url": "/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/German.json"
				},
				'fnDrawCallback': function() {
					// Since only visible textareas can be autosized, this has to be called after every page render
					$("#list textarea").autosize()
				}
			})

		})

	},

// Das Formular zum Editiren mit Daten des jeweiligen Eintrages ausfüllen
	populate_kloster: function(url) {

		var $this = $(this)

		var ort1 = $('select[tabindex=20]')
		ort1.replaceWith('<input id="searchOrtEdit" type="text" name="ort[]" tabindex="20">')

		$.getJSON(url, function(response) {

			var bearbeitungsstatusArray = response[1]
			var $inputBearbeitungsstatus = $("select[name='bearbeitungsstatus']")
			$inputBearbeitungsstatus.empty()
			$.each(bearbeitungsstatusArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBearbeitungsstatus.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var personallistenstatusArray = response[2]
			var $inputPersonallistenstatus = $("select[name='personallistenstatus']")
			$inputPersonallistenstatus.empty()
			$.each(personallistenstatusArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputPersonallistenstatus.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var bandArray = response[3]
			var $inputBand = $("select[name='band']")
			$inputBand.empty()
			$inputBand.append($("<option>", { value: '', html: 'Kein Band' }))
			$.each(bandArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBand.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var literaturArray = response[4]
			var $inputLiteratur = $("select[name='literatur[]']")
			$inputLiteratur.empty()

			$inputLiteratur.append($("<option>", { value: "", html: "Keine Literatur" }))

			$.each(literaturArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputLiteratur.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var bistumArray = response[5]
			var $inputBistum = $("select[name='bistum[]']")
			$inputBistum.empty()
			$.each(bistumArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBistum.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var ordenArray = response[6]
			var $inputOrden = $("select[name='orden[]']")
			$inputOrden.empty()
			$.each(ordenArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputOrden.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var klosterstatusArray = response[7]
			var $inputKlosterstatus = $("select[name='klosterstatus[]']")
			$inputKlosterstatus.empty()
			$.each(klosterstatusArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputKlosterstatus.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var bearbeiterArray = response[8]
			var $inputBearbeiter = $("select[name='bearbeiter']")
			$inputBearbeiter.empty()
			$.each(bearbeiterArray, function(k, v) {
				$.each(v, function(k1, v1) {
					$inputBearbeiter.append($("<option>", { value: v1, html: k1 }))
				})
			})

			var klosterstandorte = response[0].klosterstandorte
			var klosterorden = response[0].klosterorden
			var klosterurl = response[0].url
			var klosterliteratur = response[0].literatur

			var uuid = response[0].uuid
			var update_url = "update/" + uuid
			$('#EditKloster').attr("action", update_url)

			var fieldset = $this.find('fieldset:eq(0)')
			fieldset.find('label :input').each(function() {
				var name = $(this).attr('name')
				if (typeof name === 'undefined') return
				name = name.replace('[]', '')
				var val = response[0][name]
				if ($(this).is('[type=checkbox]')) {
//					if (name == "wuestung" && val==1) {
//						$(this).prop('checked', true)
//					}
//					else {
					return
//					}
				}
				else
					if ($(this).is('select')) {

						if (name == "bearbeitungsstatus") {
							fieldset.find("select[name='bearbeitungsstatus'] option").each(function(i, opt) {
								if (opt.value == val) {
									$(opt).attr('selected', 'selected')
								}
							})
						}
						else
							if (name == "bearbeiter") {
								fieldset.find("select[name='bearbeiter'] option").each(function(i, opt) {
									if (opt.value == val) {
										$(opt).attr('selected', 'selected')
									}
								})
							}
							else
								if (name == "personallistenstatus") {
									fieldset.find("select[name='personallistenstatus'] option").each(function(i, opt) {
										if (opt.value == val) {
											$(opt).attr('selected', 'selected')
										}
									})
								}
								else
									if (name == "band") {
										fieldset.find("select[name='band'] option").each(function(i, opt) {
											if (opt.value == val) {
												$(opt).attr('selected', 'selected')
											}
										})
									}
									else {
										$(this).find('option').remove()
										if (val) $(this).append('<option>' + val + '</option>')
									}
					}
					else {
						$(this).val(val)
					}
			})

			$this.find("input[type=url]").keyup()

			$.each(klosterorden, function(key, value) {

				if (key == 0) {
					var fieldset = $this.find('fieldset:eq(2)')
				}
				else {
					var fieldset = $this.find('div.multiple:eq(1)').clone(true)
				}

				fieldset.find('label :input').each(function() {
					var name = $(this).attr('name')
					if (typeof name === 'undefined') return;
					name = name.replace('[]', '')
					var val = value[name];
					if ($(this).is('[type=checkbox]')) {
						return;
					}
					else
						if ($(this).is('select')) {

							if (name == "orden") {
								$this.find("select[name='orden[]'] option").each(function(i, opt) {
									if (opt.value == val) {
										$(opt).attr('selected', true)
									}
								})
							}
							else
								if (name == "klosterstatus") {
									$this.find("select[name='klosterstatus[]'] option").each(function(i, opt) {
										if (opt.value == val) {
											$(opt).attr('selected', 'selected')
										}
									})
								}

						} else {
							$(this).val(val)
						}
				})

				if (key > 0) {
					$this.find('fieldset:eq(2)').append(fieldset)
				}
				$this.find("textarea").autosize()
				$this.find("input[type=url]").keyup()
			})

			$.each(klosterstandorte, function(key, value) {
				if (key == 0) {
					var fieldset = $this.find('fieldset:eq(1)')
				}
				else {
					var fieldset = $this.find('div.multiple:eq(0)').clone(true)
				}

				fieldset.find('label :input').each(function() {
					var name = $(this).attr('name')
					if (typeof name === 'undefined') return;
					name = name.replace('[]', '')
					var val = value[name];
					if ($(this).is('[type=checkbox]')) {
						if (name == "wuestung" && val == 1) {
							$(this).prop('checked', true)
						}
						if (name == "wuestung" && val == 0) {
							$(this).prop('checked', false)
						}
						else {
							return;
						}
					} else
						if ($(this).is('select')) {
							if (name == "bistum") {
								$this.find("select[name='bistum[]'] option").each(function(i, opt) {
									if (opt.value == val) {
										$(opt).attr('selected', 'selected')
									}
								})
							}

						} else {
							$(this).val(val)
						}
				})

				if (key > 0) {
					$this.find('div.multiple:eq(0)').append(fieldset)
				}
				$this.find("textarea").autosize()
				$this.find("input[type=url]").keyup()
			})

			$.each(klosterurl, function(key, value) {
				$.each(value, function(key1, value1) {
					if (key1 == "GND") {
						var gnd = value1;
					}
					else
						if (key1 == "Wikipedia") {
							var wikipedia = value1;
						}

					if (gnd) {
						//$("input[name=gnd]").val("")
						$("input[name=gnd]").val(gnd)
					}
					if (wikipedia) {
						$("input[name=wikipedia]").val(wikipedia)
					}

				})
			})

			if (klosterliteratur && klosterliteratur != "") {
				$.each(klosterliteratur, function(key, value) {
					if ($.isArray(value)) {
						$.each(value, function(literaturkey, literaturvalue) {
							$this.find("select[name='literatur[]'] option").each(function(i, opt) {
								if (opt.value == literaturvalue) {
									$(opt).attr('selected', 'selected')
								}
							})
						})
					}
					else {
						$this.find("select[name='literatur[]'] option").each(function(i, opt) {
							if (opt.value == value) {
								$(opt).attr('selected', 'selected')
							}
						})
					}
				})
			}

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

	find_ort: function(ort, tabindex, kloster_uuid) {
		var url = "searchOrt";
		$.getJSON(url, { searchString: ort }, function(response) {
			var ort = $('input[tabindex=' + tabindex + ']')
			if (response.length > 0) {
				ort.replaceWith('<select name="ort[' + kloster_uuid + '][]" multiple id="ort' + tabindex + '" required="required"></select>')
				$.each(response, function(k, v) {
					$('#ort' + tabindex).append($("<option>", { value: v[0], html: v[1] }))
				})
			}
			else {
				ort.val("Keinen Eintrag vorhanden")
			}
		})
	},

	find_ortEdit: function(ort, tabindex) {
		var url = "searchOrt"
		$.getJSON(url, { searchString: ort }, function(response) {
			var ort = $('input[tabindex=' + tabindex + ']')
			if (response.length > 0) {
				ort.replaceWith('<select name="ort[]" multiple id="ort' + tabindex + '"></select>')
				$.each(response, function(k, v) {
					$('#ort' + tabindex).append($("<option>", { value: v[0], html: v[1] }))
				})
			}
			else {
				ort.val("Keinen Eintrag vorhanden")
			}
		})
	},

	find_ortNew: function(ort, tabindex) {
		var url = "searchOrt"
		$.getJSON(url, { searchString: ort }, function(response) {
			var ort = $('input[tabindex=' + tabindex + ']')
			if (response.length > 0) {
				ort.replaceWith('<select name="new_ort[]" multiple id="ort' + tabindex + '"></select>')
				$.each(response, function(k, v) {
					$('#ort' + tabindex).append($("<option>", { value: v[0], html: v[1] }))
				})
			}
			else {
				ort.val("Keinen Eintrag vorhanden")
			}
		})
	}

})