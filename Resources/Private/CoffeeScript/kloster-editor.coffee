$ ->

	$("#edit").hide().populate_selects()
	$("#edit textarea").autosize()

	$("fieldset .multiple").append "<div class=\"add-remove-buttons\"><button class=\"remove\">-</button><button class=\"add\">+</button></div>"
	$("fieldset .multiple button").click (e) ->
		e.preventDefault()
		div = $(this).closest(".multiple")
		if $(this).hasClass("remove")
			div.removeInputs 250
		else if $(this).hasClass("add")
			div.addInputs 250

	$("input[type=url]").keyup ->
		$(this).parent().next(".link").html( if $(this).val() then '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' else '' )

	$("fieldset .multiple .remove").click()
	$(".togglable + .togglable").hide()
	$(".toggle").click (e) ->
		e.preventDefault()
		$(this).closest(".togglable").siblings(".togglable").addBack().slideToggle()

	$(".new").click (e) ->
		e.preventDefault()
		$("#edit_form").new_kloster()

	$(".close").click (e) ->
		e.preventDefault()
		$(this).parent().closest("div[id]").slideUp()
		$("#browse").slideDown()

	$("#edit_form").submit (e) ->
		e.preventDefault()
		$("select:disabled").prop("disabled", false).addClass "disabled"
		unless $(this).find("[name=kloster_id]").val().length
			$(this).create_kloster()
		else
			$(this).update_kloster()
		$("select.disabled").prop "disabled", true

	# Submit by pressing Ctrl-S (PC) or Meta-S (Mac)
	$(window).bind "keydown", (e) ->
		if e.ctrlKey or e.metaKey
			switch String.fromCharCode(e.which).toLowerCase()
				when "s"
					e.preventDefault()
					$(":submit:visible:last").click()
	
	return

# Fill the select fields with options
$.fn.populate_selects = ->
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
			$select = $("select[name=\"" + name + "\"], select[name=\"" + name + "[]\"]")
			$select.empty()
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
	$('select option:contains("Erfassung")').prop('selected',true);

# Create a new Kloster
$.fn.create_kloster = ->
	$this = $(this)
	$.post("create", $this.serialize()).done((respond, status, jqXHR) ->
		$.get("solrUpdateWhenKlosterCreate",
			uuid: respond
		)
		$this.message 'Ein neuer Eintrag wurde angelegt.'
	).fail (jqXHR, textStatus) ->
		$this.message 'Error'
		console.dir jqXHR.responseText

# Load a single Kloster into the edit form
$.fn.read_kloster = (url) ->

	$this = $(this)
	$this.clear_form()

	$("#browse").slideUp()
	$('#loading').show()

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
		$.each kloster.literatur, (index, value) ->
			if index > 0
				$fieldset.addInputs 0
			$fieldset.find(".multiple:last() label :input").each ->
				name = $(this).attr("name")
				if typeof name is "undefined"
					return
				name = name.replace("[]", "")
				$(this).val value

		$('#edit').slideDown()
		$('#loading').hide()
		$this.find(".autocomplete").autocomplete()
		$this.find("textarea").trigger "autosize.resize"
		$this.find("input[type=url]").keyup()

# Update a single Kloster
$.fn.update_kloster = ->
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