# Save the Kloster list
$.fn.update_list =  ->
	$this = $(this)
	$rows = dataTable.$('tr').has('input:checked')
	formData = {}
	$rows.each ->
		uuid = $(this).find(':input[name=uuid]').val()
		formData['klosters[' + uuid + ']'] = {}
		$(this).find(':input:not([name=uuid])').each (i, input) ->
			if input.name then formData['klosters[' + uuid + ']'][input.name] = input.value
			return
	formData.__csrfToken = $(this).find('input[name=__csrfToken]').val()
	$.post('updateList', formData).done((respond, status, jqXHR) ->
		$.post("updateSolrAfterListUpdate", {uuids: respond}).done((respond, status, jqXHR) ->
			if status is "success"
				$this.message 'Ihre Änderungen wurden gespeichert.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText
	).fail (jqXHR, textStatus) ->
		$this.message 'Error'
		console.dir jqXHR.responseText

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
		dataArray = $.parseJSON respond
		uuid = dataArray[0]
		$.get("addKlosterId",
			uuid: uuid
		)
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

