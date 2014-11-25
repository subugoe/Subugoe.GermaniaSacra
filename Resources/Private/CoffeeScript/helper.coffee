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

$.fn.addInputs = (slideTime) ->
	unless slideTime? then slideTime = 0
	@each ->
		$fieldset = $(this).closest("fieldset")
		$clone = $(this).clone(true)
		$clone.clearForm()
		$clone.find("select").autocomplete()
		$clone.insertAfter( $(this) ).hide().slideDown slideTime
		$fieldset.find(".remove").toggleClass "disabled", $fieldset.find(".multiple:not(.dying)").length is 1

$.fn.removeInputs = (slideTime) ->
	unless slideTime? then slideTime = 0
	@each ->
		$fieldset = $(this).closest("fieldset")
		$fieldset.find(".multiple").length > 1 and $(this).addClass("dying").slideUp(slideTime, @remove)
		$fieldset.find(".remove").toggleClass "disabled", $fieldset.find(".multiple:not(.dying)").length is 1

$.fn.clearForm = ->
	@each ->
		$form = $(this)
		$form.find("label").removeClass('dirty')
		$form.find(":input").prop('disabled', false)
		$form.find(":input:not([name=__csrfToken]):not(:checkbox):not(:submit)").val('')
		$form.find(":checkbox, :radio").prop('checked', false)
		# Select "keine Angabe" or "unbekannt" as default if available
		$form.find('select option:contains("keine Angabe"), select option:contains("unbekannt")').prop('selected', true)
		$form.find(".multiple:gt(0)").removeInputs()
		$form.find("button.remove").prop 'disabled', true
