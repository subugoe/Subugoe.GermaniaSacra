# Fill the Kloster list
$.fn.populate_list = ->

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

		$table = $this.find("table:eq(0)")
		$trTemplate = $table.find("tbody tr:first")

		# Add a text input to each header cell used for search
		$table.find("thead th").not(":first").not(":last").each ->
			$(this).append "<div><input type=\"text\"></div>"

		dataTable = $table.DataTable(
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
				$table.find("textarea").autosize()
				# Mark row as dirty on change
				$table.find(":input:not(:checkbox)").change ->
					$(this).closest("td").addClass("dirty").closest("tr").find(":checkbox:eq(0)").prop "checked", true
		)

		# Apply the search
		dataTable.columns().eq(0).each (colIdx) ->
			$("input", dataTable.column(colIdx).header()).click((e) ->
				e.stopPropagation()
			).on "keyup change", ->
				dataTable.column(colIdx).search(@value).draw()

		# Filter table by "search all" return values
		$("body").append '<input id="uuid_filter" type="hidden">'
		$("#uuid_filter").change ->
			# enable regex, disable smart search (enabling both will not work)
			dataTable.column(0).search(@value, true, false).draw()

		# Fill the DataTable
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
			dataTable.row.add $tr

		# Remove template row and draw table
		dataTable.row($trTemplate).remove().draw()

# Save the Kloster list
$.fn.update_list =  ->
	$this = $(this)
	url = $this.attr "action"
	$.post(url, $this.serialize()).done((respond, status, jqXHR) ->
		if status is "success"
			$this.message 'Ihre Änderungen wurden gespeichert.'
	).fail (jqXHR, textStatus) ->
		$this.message 'Error'
		console.dir jqXHR.responseText

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
	$("#browse").slideUp()
	$("#edit").slideDown()
	$(this).clear_form()
	$(this).find(".autocomplete").autocomplete()
	$(this).find("textarea").trigger "autosize.resize"
	$(this).find("input[type=url]").keyup()

# Create a new Kloster
$.fn.create_kloster = ->
	$this = $(this)
	$.post("create", $this.serialize()).done((respond, status, jqXHR) ->
		$this.message 'Ein neuer Eintrag wurde angelegt.'
	).fail (jqXHR, textStatus) ->
		$this.message 'Error'
		console.dir jqXHR.responseText

# Load a single Kloster into the edit form
$.fn.read_kloster = (url) ->

	$this = $(this)
	$this.clear_form()

	$.getJSON url, (kloster) ->

		uuid = kloster.uuid
		update_url = "update/" + uuid
		$this.attr "action", update_url

		$fieldset = $("#kloster")
		$fieldset.find("label :input").each ->
			name = $(this).attr("name")
			if typeof name is "undefined"
				return name = name.replace("[]", "")
			val = kloster[name]
			$(this).val val

		$fieldset.find("[name=changeddate]").val( if kloster.changeddate then kloster.changeddate.date.substr(0, kloster.changeddate.date.indexOf(".")) else '' )

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
					$(this).val(value[name]).prop "disabled", typeof value[name] isnt "undefined" and $(this).text isnt "keine Angabe"
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
		$this.find(".autocomplete").autocomplete()
		$this.find("textarea").trigger "autosize.resize"
		$this.find("input[type=url]").keyup()

# Update a single Kloster
$.fn.update_kloster = ->
	$this = $(this)
	url = $this.attr "action"
	$.post(url, $this.serialize()).done((respond, status, jqXHR) ->
		if status is "success"
			$this.message 'Ihre Änderungen wurden gespeichert.'
	).fail (jqXHR, textStatus) ->
		$this.message 'Error'
		console.dir jqXHR.responseText

# Delete a single Kloster
$.fn.delete_kloster = (url, csrf) ->
	$this = $(this)
	check = confirm 'Wollen Sie diesen Eintrag wirklich löschen?'
	if check is true
		csrf = $('#csrf').val()
		$.post(url,
			__csrfToken: csrf
		).done((respond, status, jqXHR) ->
			if status is "success"
				$this.message 'Der Eintrag wurde gelöscht.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText

