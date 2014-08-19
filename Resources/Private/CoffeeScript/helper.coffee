$.fn.message = (text) ->
	$('#message').remove()
	date = new Date()
	$message = $('<div id="message"><span class="timestamp">' + date.toLocaleString() + '</span>' + text + '</div>')
	$message.insertBefore( $(this) ).hide().slideDown()
