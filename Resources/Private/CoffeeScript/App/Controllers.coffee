germaniaSacra.controller 'listController', ($scope) ->

	# Init the view after the template has been rendered
	$scope.$watch (->
		$('#content > section:first').data('type')
	), ((newval, oldval) ->
		if newval? then init()
	), true

init = ->

	type = $('#content > section:first').data('type')

	unless type?
		alert('There has to be at least one <section> with data-type set.')
		return

	$('form').append( $('input.csrf:first').clone() )

	initList(type)
	initEditor(type)

	$(".new").click (e) ->
		e.preventDefault()
		newAction()

	$(".togglable + .togglable").hide()

	$(".toggle").click (e) ->
		e.preventDefault()
		$(this).closest(".togglable").siblings(".togglable").addBack().slideToggle()