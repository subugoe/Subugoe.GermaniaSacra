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
