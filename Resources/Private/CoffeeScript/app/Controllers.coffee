germaniaSacra.controller 'listController', ($scope) ->

	# Init the view after the template has been rendered
	$scope.$watch (->
		$('#list').data('type')
	), ((newval, oldval) ->
		if newval?
			type = $('#list').data('type')
			if $('#list').data('no-table')?
				germaniaSacra.message 'loading', false
				$('#list').hide()
				$.get(type)
					.done (text) ->
						germaniaSacra.hideMessage()
						$('#list')
							.append ( if $('#list').data('code')? then "<pre>#{text}</pre>" else "<p>#{text}</p>" )
							.slideDown()
					.fail () ->
						germaniaSacra.message 'publishError', false
			else
				germaniaSacra.search = new germaniaSacra.Search()
				germaniaSacra.editor = new germaniaSacra.Editor(type)
				$('#message, #search, #list').hide()
				$('.togglable + .togglable').hide()
				$.when( germaniaSacra.getOptions() ).then(
					(selectOptions) ->
						germaniaSacra.selectOptions = selectOptions
						germaniaSacra.list = new germaniaSacra.List(type)
						$('.toggle').click (e) ->
							e.preventDefault()
							$(this).closest('.togglable').siblings('.togglable').addBack().slideToggle()
						germaniaSacra.bindKeys()
						$('#edit form').appendSaveButton 'saveChanges'
						$('#list form').appendSaveButton 'saveChangesWithCount'
						$('fieldset .multiple').appendAddRemoveButtons()
					,
					->
						germaniaSacra.message 'optionsLoadError'
				)
	), true

	# Confirm discard changes on view change
	$scope.$on '$locationChangeStart', (e) ->
		if $('.dirty').length
			if confirm(germaniaSacra.messages.askUnsavedChanges)
				$('.dirty').removeClass('dirty')
			else
				e.preventDefault()

germaniaSacra.getOptions = ->
	dfd = $.Deferred()
	# Load options for selects before initializing list
	$.getJSON 'getOptions', (response) ->
		$.each response, (name, values) ->
			$selects = $("#edit select[name='#{name}'], select[name='#{name}[]'], select[name='#{name}_uid']")
			$selects.empty()
			$.each values, (uUID, text) ->
				$selects.append $('<option/>',
					value: uUID
					text: text
				)
		dfd.resolve(response)
	return dfd.promise()

germaniaSacra.bindKeys = ->
	$(window).bind 'keydown', (e) ->
		if e.ctrlKey or e.metaKey
			switch String.fromCharCode(e.which).toLowerCase()
				when 'a'
					e.preventDefault()
					$('button.new:visible:last').click()
				when 's'
					e.preventDefault()
					$('[type=submit]:visible:last').click()

germaniaSacra.message = (messageId, withTimestampAndCloseButton = true) ->
	$message = $('#message')
	date = new Date()
	timestamp = if withTimestampAndCloseButton then "<span class='timestamp'>#{date.toLocaleString()}</span>" else ''
	text = "<span class='text'>#{germaniaSacra.messages[messageId]}</span>"
	# TODO: No animation since it freezes during DataTables render
	$message.hide().html(timestamp + text).show()
	if withTimestampAndCloseButton
		$close = $("<i class='hover close icon-close right'>&times;</i>")
		$close.appendTo($message).click (e) ->
			e.preventDefault()
			$message.hide()
	$('html, body').animate
		scrollTop: 0

germaniaSacra.hideMessage = ->
	$('#message').slideUp()

# Confirm discard changes on window close/reload
window.onbeforeunload = -> if $('.dirty').length then return germaniaSacra.messages.askUnsavedChanges
