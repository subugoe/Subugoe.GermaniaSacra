confirmDiscardChanges = ->
	return confirm("Sind Sie sicher, dass Sie diese Seite verlassen wollen? Ihre Änderungen wurden nicht gespeichert.")

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
