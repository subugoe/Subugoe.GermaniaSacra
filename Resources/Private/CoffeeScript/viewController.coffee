init_view = ->

	type = $('#content section:first').attr('id')

	# TODO: Not working yet
	# if type?
	#	readAction(type)

	# TODO: Not working
	$('form').append( $(':input.csrf').html() )

	populateListAction(type)

	$("#list form").submit (e) ->
		e.preventDefault()
		if $("input[name^='uuid']:checked").length is 0
			$(this).message "Wählen Sie bitte mindestens einen Eintrag aus."
			return false
		updateListAction(type)

	### Kloster editor ###

	populateSelectsAction(type)

	$("#edit textarea").autosize()
	$('#edit').hide()

	$("fieldset .multiple").append "<div class=\"add-remove-buttons\"><button class=\"remove\">-</button><button class=\"add\">+</button></div>"
	$("fieldset .multiple button").click (e) ->
		e.preventDefault()
		div = $(this).closest(".multiple")
		if $(this).hasClass("remove")
			div.removeInputs 250
		else if $(this).hasClass("add")
			div.addInputs 250

	# Update clickable URL next to URL input
	$("input[type=url]").keyup ->
		$(this).parent().next(".link").html( if $(this).val() then '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' else '' )

	$("fieldset .multiple .remove").click()
	$(".togglable + .togglable").hide()

	$(".toggle").click (e) ->
		e.preventDefault()
		$(this).closest(".togglable").siblings(".togglable").addBack().slideToggle()

	$(".new").click (e) ->
		e.preventDefault()
		newAction()

	$(".close").click (e) ->
		e.preventDefault()
		$(this).parent().closest("div[id]").slideUp()
		$("#list").slideDown()

	$("#edit form").submit (e) ->
		e.preventDefault()
		type = $(this).closest('section').attr('id')
		if !type?
			alert('Invalid type. Set form ID.')
			return
		$("select:disabled").prop("disabled", false).addClass "disabled"
		unless $(this).find("[name=kloster_id]").val().length
			createAction( type )
		else
			updateAction( type )
		$("select.disabled").prop "disabled", true

	# Submit by pressing Ctrl-S (PC) or Meta-S (Mac)
	$(window).bind "keydown", (e) ->
		if e.ctrlKey or e.metaKey
			switch String.fromCharCode(e.which).toLowerCase()
				when "s"
					e.preventDefault()
					$(":submit:visible:last").click()