message = (text, withTimestampAndCloseButton = true) ->
	$message = $('#message')
	date = new Date()
	timestamp = if withTimestampAndCloseButton then "<span class='timestamp'>#{date.toLocaleString()}</span>" else ''
	text = "<span class='text'>#{text}</span>"
	$message.hide().html(timestamp + text).slideDown()
	if withTimestampAndCloseButton
		$close = $("<i class='hover close icon-close right'>&times;</i>")
		$close.appendTo($message).click (e) ->
			e.preventDefault()
			$message.slideUp()
	$('html, body').animate
		scrollTop: $message.offset().top

ucfirst = (string) ->
	string.charAt(0).toUpperCase() + string.slice(1)

$.fn.addInputs = (slideTime) ->
	unless slideTime? then slideTime = 0
	@each ->
		$fieldset = $(this).closest("fieldset")
		$clone = $(this).clone(true)
		$clone.clearForm()
		$clone.find("select.autocomplete").autocomplete()
		$clone.insertAfter($(this)).hide().slideDown slideTime
		$fieldset.find("button.remove").prop "disabled", $fieldset.find(".multiple:not(.dying)").length is 1

$.fn.removeInputs = (slideTime) ->
	unless slideTime? then slideTime = 0
	@each ->
		$fieldset = $(this).closest("fieldset")
		$fieldset.find(".multiple").length > 1 and $(this).addClass("dying").slideUp(slideTime, @remove)
		$fieldset.find("button.remove").prop "disabled", $fieldset.find(".multiple:not(.dying)").length is 1

$.fn.clearForm = ->
	@each ->
		$(this).find("label").removeClass('dirty')
		$(this).find(":input").prop "disabled", false
		$(this).find(":input:not([name=__csrfToken]):not(:checkbox):not(:submit)").val('')
		$(this).find(":checkbox, :radio").prop "checked", false
		$(this).find(".multiple:gt(0)").removeInputs()
		$(this).find("button.remove").prop 'disabled', true
