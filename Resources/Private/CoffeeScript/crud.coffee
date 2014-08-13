# Save the Kloster list
$.fn.update_list = (url) ->
	$.post(url, $("#UpdateList").serialize()).done((respond, status, jqXHR) ->
		if status is "success"
			$("#confirm").modal
				closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>"
				position: [ "20%" ]
				overlayId: "confirm-overlay"
				containerId: "confirm-container"
				onShow: (dialog) ->
					$(".message").append "Der Eintrag wurde erfolgreich bearbeitet."

			setTimeout (->
				window.location.href = ""
			), 1000
	).fail (jqXHR, textStatus) ->
		$("#confirm").modal
			closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>"
			position: [ "20%" ]
			overlayId: "confirm-overlay"
			containerId: "confirm-container"
			onShow: (dialog) ->
				$(".message").append jqXHR.responseText

# Fill the Kloster list
$.fn.populate_liste = (page) ->
	$this = $(this)
	$.getJSON "klosterListAll", (response) ->
		
		# Fill "Status" select fields
		bearbeitungsstatusArray = response[1]
		$inputBearbeitungsstatus = $("select[name='bearbeitungsstatus']")
		$inputBearbeitungsstatus.empty()
		$.each bearbeitungsstatusArray, (k, v) ->
			$.each v, (k1, v1) ->
				$inputBearbeitungsstatus.append $("<option>",
					value: v1
					html: k1
				)

		klosters = response[0]
		$trTemplate = $("#list tbody tr:first")
		
		# Add a text input to each header cell used for search
		$("#list thead th").not(":first").not(":last").each ->
			$(this).append "<div><input type=\"text\"></div>"

		table = $("#list").DataTable(
			autoWidth: false
			columnDefs: [
				bSortable: false
				aTargets: [ "no-sorting" ]
			,
				width: "10%"
				targets: 1
			]
			dom: "lipt" # 'l' - Length changing, 'f' - Filtering input, 't' - The table, 'i' - Information, 'p' - Pagination, 'r' - pRocessing
			language:
				url: "/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json"
			order: [ [ 3, "asc" ] ]
			fnDrawCallback: ->
				
				# Since only visible textareas can be autosized, this has to be called after every page render
				$("#list textarea").autosize()
				
				# Mark row as dirty when changed
				$("#list :input:not(:checkbox)").change ->
					$(this).closest("td").addClass("dirty").closest("tr").find(":checkbox:eq(0)").prop "checked", true
		)
		
		# Apply the search
		table.columns().eq(0).each (colIdx) ->
			$("input", table.column(colIdx).header()).click((e) ->
				e.stopPropagation()
			).on "keyup change", ->
				table.column(colIdx).search(@value).draw()

		# Filter table by "search all" return values
		$("body").append "<input id=\"uuidFilter\" type=\"hidden\">"
		$("#uuidFilter").change ->
			# enable regex, disable smart search (enabling both will not work)
			table.column(0).search(@value, true, false).draw()

		# Fill the table
		$.each klosters, (index, kloster) ->
			
			# Clone with triggers for edit and delete
			$tr = $trTemplate.clone(true)
			$tr.find(":input").each ->
				name = $(this).attr("name")
				if typeof name is "undefined"
					return
				val = kloster[name]
				if $(this).is("[type=checkbox]")
					if name is "auswahl"
						$(this).val kloster.uuid
				else if $(this).is("select")
					if name is "bearbeitungsstatus"
						$tr.find("select[name=bearbeitungsstatus] option").each (i, opt) ->
							if opt.value is val
								$(opt).attr "selected", "selected"
					else
						$(this).append "<option>" + val + "</option>"
				else
					if name isnt "__csrfToken"
						$(this).val val
				if name isnt "__csrfToken" and name isnt "auswahl"
					$(this).attr "name", name + "[" + kloster.uuid + "]"
					
					# WORKAROUND: DataTables 1.10.1 has a bug that prevents sorting of :input elements, so we use plain text for sorting
					$("<span class=\"val\"/>").text((if $(this).is("select") then $(this).find(":selected").text() else $(this).val())).hide().insertBefore $(this)

			$tr.find(".edit").attr "href", "edit/" + kloster.uuid
			$tr.find(".delete").attr "href", "delete/" + kloster.uuid
			$tr.find("input.csrf").attr "id", "csrf" + index
			table.row.add $tr

		# Remove template row and draw table
		table.row($trTemplate).remove().draw()

# Fill the select fields with options
$.fn.populate_selects = ->
	url = "getOptions"
	$.getJSON url, (response) ->
		
		# Fill select fields with available options
		# TODO: Fill "URL Typ" selects
		options = {}
		options.bearbeitungsstatus = response[0]
		options.personallistenstatus = response[1]
		options.band = response[2]
		options.literatur = response[3]
		options.bistum = response[4]
		options.orden = response[5]
		options.klosterstatus = response[6]
		options.bearbeiter = response[7]
		$.each options, (name, values) ->
			$select = $("select[name=\"" + name + "\"], select[name=\"" + name + "[]\"]")
			$select.empty().append $("<option>",
				value: ""
				text: ""
			)
			$.each values, (index, object) ->
				$.each object, (value, uuid) ->
					$select.append $("<option>",
						value: uuid
						text: value
					)

# Clear the edit form for a new Kloster
$.fn.new_kloster = ->
	$("#edit").clear_form()
	$("#browse").slideUp()
	$("#edit").slideDown()
	$("#edit .autocomplete").autocomplete()
	$("#edit textarea").trigger "autosize.resize"
	$("#edit input[type=url]").keyup()

# Load a single Kloster into the edit form
$.fn.populate_kloster = (url) ->

	$this = $(this)

	$("#edit").clear_form()

	$.getJSON url, (kloster) ->
		uuid = kloster.uuid
		update_url = "update/" + uuid
		$("#EditKloster").attr "action", update_url
		$fieldset = $("#kloster")
		$fieldset.find("label :input").each ->
			name = $(this).attr("name")
			if typeof name is "undefined"
				return name = name.replace("[]", "")
			val = kloster[name]
			$(this).val val

		$fieldset.find(".bearbeiter").text kloster.bearbeiter or "?"
		$fieldset.find(".changeddate").text (if kloster.changeddate then kloster.changeddate.date.substr(0, kloster.changeddate.date.indexOf(".")) else "?")
		$fieldset = $("#klosterorden")
		$.each kloster.klosterorden, (index, value) ->
			if index > 0
				$fieldset.find(".multiple:last()").addInputs 0
			$fieldset.find(".multiple:last() label :input").each ->
				name = $(this).attr("name")
				if typeof name is "undefined"
					return
				name = name.replace("[]", "")
				$(this).val value[name]

		$fieldset = $("#klosterstandorte")
		$.each kloster.klosterstandorte, (index, value) ->
			if index > 0
				$fieldset.find(".multiple:last()").addInputs 0
			$fieldset.find(".multiple:last() label :input").each ->
				name = $(this).attr("name")
				return	if typeof name is "undefined"
				name = name.replace("[]", "")
				val = value[name]
				if name is "wuestung"
					if name is "wuestung"
						$(this).prop "checked", value[name] is 1
				else if name is "ort"
					$(this).html $("<option />",
						value: value["uuid"]
						text: value["ort"]
					).attr("selected", true)
				else if name is "bistum"
					$(this).val(value[name]).prop "disabled", typeof value[name] isnt "undefined"
				else
					$(this).val value[name]

		$fieldset = $("#links")
		$.each kloster.url, (index, value) ->
			if value.url_typ is "GND"
				$("#gnd").val value.url
			else if value.url_typ is "Wikipedia"
				$("#wikipedia").val value.url
			else
				$fieldset.find(".multiple:last()").addInputs 0
				$fieldset.find(".multiple:last() label :input").each ->
					name = $(this).attr("name")
					if typeof name is "undefined"
						return
					name = name.replace("[]", "")
					$(this).val value[name]

		$fieldset.find(".multiple:eq(0)").removeInputs 0
		$fieldset = $("#literatur")
		$.each kloster.literatur, (index, value) ->
			if index > 0
				$fieldset.addInputs 0
			$fieldset.find(".multiple:last() label :input").each ->
				name = $(this).attr("name")
				if typeof name is "undefined"
					return
				name = name.replace("[]", "")
				$(this).val value

		$("#browse").slideUp()
		$("#edit").slideDown()
		$("#edit .autocomplete").autocomplete()
		$("#edit textarea").trigger "autosize.resize"
		$("#edit input[type=url]").keyup()

# Update a single Kloster
$.fn.update_kloster = (url) ->
	$.post(url, $("#EditKloster").serialize()).done((respond, status, jqXHR) ->
		if status is "success"
			$("#confirm").modal
				closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>"
				position: [ "20%" ]
				overlayId: "confirm-overlay"
				containerId: "confirm-container"
				onShow: (dialog) ->
					$(".message").append "Der Eintrag wurde erfolgreich bearbeitet."
	).fail (jqXHR, textStatus) ->
		$("#confirm").modal
			closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>"
			position: [ "20%" ]
			overlayId: "confirm-overlay"
			containerId: "confirm-container"
			onShow: (dialog) ->
				$(".message").append jqXHR.responseText

# Create a new Kloster
$.fn.create_kloster = ->
	$.post("create", $("#EditKloster").serialize()).done((respond, status, jqXHR) ->
		if status is "success"
			dataArray = $.parseJSON(respond)
			uuid = dataArray[0]
			addKlosterId_url = "addKlosterId"
			$.get addKlosterId_url,
				uuid: uuid
			$("#confirm").modal
				closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>"
				position: [ "20%" ]
				overlayId: "confirm-overlay"
				containerId: "confirm-container"
				onShow: (dialog) ->
					$(".message").append "Der Eintrag wurde erfolgreich gespeichert."
	).fail (jqXHR, textStatus) ->
		$("#confirm").modal
			closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>"
			position: [ "20%" ]
			overlayId: "confirm-overlay"
			containerId: "confirm-container"
			onShow: (dialog) ->
				$(".message").append jqXHR.responseText

# Delete a single Kloster
$.fn.delete_kloster = (url, csrf) ->
	check = confirm("Wollen Sie diesen Eintrag wirklich löschen?")
	if check is true
		$.post(url,
			__csrfToken: csrf
		).done((respond, status, jqXHR) ->
			if status is "success"
				$("#confirm").modal
					closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>"
					position: [ "20%" ]
					overlayId: "confirm-overlay"
					containerId: "confirm-container"
					onShow: (dialog) ->
						$(".message").append "Der Eintrag wurde erfolgreich gelöscht."
				setTimeout (->
					window.location.href = ""
				), 1000
		).fail (jqXHR, textStatus) ->
			alert jqXHR.responseText

$.fn.clear_form = ->
	$(this).find(":input").prop "disabled", false
	$(this).find(":input:not(:checkbox):not([type=hidden]):not(:submit)").val("")
	$(this).find(":checkbox, :radio").prop "checked", false
	$(this).find(".multiple:gt(0)").removeInputs 0
	$(this).find(".autofill").text "?"
