germaniaSacra.controller 'listController', ($scope) ->

	# Init the view after the template has been rendered
	$scope.$watch (->
		$('#list').data('type')
	), ((newval, oldval) ->
		if newval? then init()
	), true

	$scope.$on('$locationChangeStart', (e) ->
		if $('form .dirty').length
			cnfrm = confirm("Sind Sie sicher, dass Sie diese Seite verlassen wollen? Ihre Änderungen wurden nicht gespeichert.")
			if cnfrm isnt true
				e.preventDefault()
	)

# TODO: Move this
s_loading = '<i class="spinner spinner-icon"></i> Wird geladen&hellip;'

init = ->

	type = $('#list').data('type')

	$('#message').hide()

	unless type?
		alert('There has to be at least one <section> with data-type set.')
		return

	$('form').append( $('#csrf').clone().removeAttr('id') )

	initSearch()
	initList(type)
	initEditor(type)

	$("fieldset .multiple").append "<div class='add-remove-buttons'><button class='remove'>-</button><button class='add'>+</button></div>"
	$("fieldset .multiple button").click (e) ->
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
				when "s"
					e.preventDefault()
					$(":submit[type=submit]:visible:last").click()

	$(".togglable + .togglable").hide()

	$(".toggle").click (e) ->
		e.preventDefault()
		$(this).closest(".togglable").siblings(".togglable").addBack().slideToggle()

	# Warn if changes not saved
	window.onbeforeunload = ->
		if $('form .dirty').length
			return 'Sind Sie sicher, dass Sie diese Seite verlassen möchten? Ihre Änderungen wurden nicht gespeichert.'

