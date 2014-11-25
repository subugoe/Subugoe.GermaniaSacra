germaniaSacra.controller 'listController', ($scope) ->

	# Init the view after the template has been rendered
	$scope.$watch (->
		$('#list').data('type')
	), ((newval, oldval) ->
		if newval? then init()
	), true

	# Confirm discard changes on view change
	$scope.$on('$locationChangeStart', (e) ->
		if $('.dirty').length
			if confirmDiscardChanges()
				$('.dirty').removeClass('dirty')
			else
				e.preventDefault()
	)

# TODO: Move this
s_loading = '<i class="spinner spinner-icon"></i> Wird geladen&hellip;'

selectOptions = {}

init = ->

	type = $('#list').data('type')

	$('#message, #search, #list').hide()

	$saveButton = $('<button/>',
		disabled: 'disabled'
		type: 'submit'
		html: '<i class="icon-disk"></i> Änderungen speichern'
	)
	$('#edit form, #list form')
		.append $saveButton
		.append $('#csrf').clone().removeAttr('id')

	initSearch()
	initEditor(type)

	# Load options for select before initializing list
	$.getJSON 'getOptions', (response) ->
		$.each response, (name, values) ->
			$selects = $("#edit select[name='#{name}'], select[name='#{name}[]'], select[name='#{name}_uid']")
			$selects.empty()
			$.each values, (uUID, text) ->
				$selects.append $('<option/>',
					value: uUID
					text: text
				)
		selectOptions = response
		initList(type)

	$("fieldset .multiple").append "<div class='add-remove-buttons'><span class='button remove'>-</span><span class='button add'>+</span></div>"
	$("fieldset .multiple .button").click (e) ->
		e.preventDefault()
		div = $(this).closest(".multiple")
		if $(this).hasClass("remove")
			div.removeInputs 250
		else if $(this).hasClass("add")
			div.addInputs 250

	$(".new").click (e) ->
		e.preventDefault()
		newAction()

	# Submit by pressing Ctrl-S (PC) or Meta-S (Mac)
	$(window).bind "keydown", (e) ->
		if e.ctrlKey or e.metaKey
			switch String.fromCharCode(e.which).toLowerCase()
				when "a"
					e.preventDefault()
					$("button.new:visible:last").click()
				when "s"
					e.preventDefault()
					$(":submit[type=submit]:visible:last").click()

	$(".togglable + .togglable").hide()

	$(".toggle").click (e) ->
		e.preventDefault()
		$(this).closest(".togglable").siblings(".togglable").addBack().slideToggle()

	# Confirm discard changes on window close/reload
	window.onbeforeunload = ->
		if $('.dirty').length
			return 'Sind Sie sicher, dass Sie diese Seite verlassen wollen? Ihre Änderungen wurden nicht gespeichert.'
