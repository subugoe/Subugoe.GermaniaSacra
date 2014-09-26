# Fill the select fields with options
# TODO: Use generalized function, only populate one type at a time and fetch separately
populateSelectsAction = ->
	url = "getOptions"
	$.getJSON url, (response) ->

		# Fill select fields with available options
		options = {}
		options.bearbeitungsstatus = response[0]
		options.personallistenstatus = response[1]
		options.band = response[2]
		options.literatur = response[3]
		options.bistum = response[4]
		options.orden = response[5]
		options.klosterstatus = response[6]
		options.bearbeiter = response[7]
		options.url_typ = response[8]
		$.each options, (name, values) ->
			$select = $("#edit select[name=\"" + name + "\"], select[name=\"" + name + "[]\"]")
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
newAction = ->
	$("#list").slideUp()
	$("#edit").slideDown()
	$(this).clear_form()
	# Get selects to be autocompleted by class
	$(this).find(".autocomplete").autocomplete('ort')
	$(this).find("textarea").trigger "autosize.resize"
	$(this).find("input[type=url]").keyup()

# Create a new Kloster
createAction = (type) ->
	$this = $(this)
	if type is 'kloster'
		$.post("create", $this.serialize()).done((respond, status, jqXHR) ->
			$.get("solrUpdateWhenKlosterCreate",
				uuid: respond
			)
			$this.message 'Ein neuer Eintrag wurde angelegt.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText
	else
		$.post("create" + ucfirst(type), $this.serialize()).done((respond, status, jqXHR) ->
			$this.message 'Ein neuer Eintrag wurde angelegt.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText

# Load a single entity into the edit form
editAction = (type, id) ->

	$this = $('#edit form')
	$this.clear_form()

	$("#list").slideUp()
	$('#loading').show()

	url = type + '/edit/' + id

	$.getJSON url, (obj) ->

		if type is 'kloster'

			uuid = kloster.uuid

			$this.attr 'action', 'updateKloster/' + uuid

			$fieldset = $("#klosterdaten")
			$fieldset.find("label :input").each ->
				name = $(this).attr("name")
				if typeof name is "undefined"
					return name = name.replace("[]", "")
				if name is 'changeddate' or name is 'creationdate'
					val = if obj[name] then obj[name].date.substr(0, obj[name].date.indexOf(".")) else ''
				else
					val = obj[name]
				$(this).val val

			$fieldset = $("#klosterorden")
			$.each obj.klosterorden, (index, value) ->
				if index > 0
					$fieldset.find(".multiple:last()").addInputs 0
				$fieldset.find(".multiple:last() label :input").each ->
					name = $(this).attr("name")
					if typeof name is "undefined"
						return
					name = name.replace("[]", "")
					$(this).val value[name]

			$fieldset = $("#klosterstandorte")
			$.each obj.klosterstandorte, (index, value) ->
				if index > 0
					$fieldset.find(".multiple:last()").addInputs 0
				$fieldset.find(".multiple:last() label :input").each ->
					name = $(this).attr("name")
					return	if typeof name is "undefined"
					name = name.replace("[]", "")
					val = value[name]
					if name is "wuestung"
						if name is "wuestung"
							checkedCondition = value[name] is 1
							$(this).prop "checked", checkedCondition
					else if name is "ort"
						$(this).html $("<option />",
							value: value["uuid"]
							text: value["ort"]
						).attr("selected", true)
					else if name is "bistum"
						$(this).val(value[name])
						text = $(this).find(':selected')
						disabledCondition = text isnt "keine Angabe" and text isnt ""
						$(this).prop "disabled", disabledCondition
					else
						$(this).val value[name]

			$fieldset = $("#links")
			$.each obj.url, (index, value) ->
				if value.url_typ_name is "GND"
					$(":input[name=gnd]").val value.url
					$(":input[name=gnd_label]").val value.url_label
				else if value.url_typ_name is "Wikipedia"
					$(":input[name=wikipedia]").val value.url
					$(":input[name=wikipedia_label]").val value.url_label
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
			$.each obj.literatur, (index, value) ->
				if index > 0
					$fieldset.addInputs 0
				$fieldset.find(".multiple:last() label :input").each ->
					name = $(this).attr("name")
					if typeof name is "undefined"
						return
					name = name.replace("[]", "")
					$(this).val value

		else

			$this.attr 'action', 'update' + ucfirst(type) + '/' + uuid
			$this.find("label :input").each ->
				name = $(this).attr("name")
				val = obj[name]
				$(this).val val

		$('#edit').slideDown()
		$('#loading').hide()
		# TODO: Generalize
		$this.find(".autocomplete").autocomplete('ort')
		$this.find("input[type=url]").keyup()
		$this.find("textarea").trigger "autosize.resize"

# Update a single Kloster
updateAction = (type) ->
	$this = $(this)
	url = $this.attr "action"
	$.post(url, $this.serialize()).done((respond, status, jqXHR) ->
		$.post("updateSolrAfterKlosterUpdate", {uuid: respond}).done((respond, status, jqXHR) ->
			if status is "success"
				$this.message 'Ihre Änderungen wurden gespeichert.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText
	).fail (jqXHR, textStatus) ->
		$this.message 'Error'
		console.dir jqXHR.responseText

# Delete a single Kloster
# TODO: Type is not really needed here, URL contains all information
deleteAction = (type, url, csrf) ->
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
