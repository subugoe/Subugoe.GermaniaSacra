message = (text) ->
	date = new Date()
	$('#message').hide().html('<span class="timestamp">' + date.toLocaleString() + '</span>' + text + '<i class="hover close icon-close right">&times;</i>').slideDown();
	$("#message .close").click (e) ->
		e.preventDefault()
		$('#message').slideUp()
	$('html, body').animate
		scrollTop: $('#message').offset().top

ucfirst = (string) ->
	string.charAt(0).toUpperCase() + string.slice(1)

$.fn.addInputs = (slideTime) ->
	if typeof slideTime is "undefined"
		slideTime = 0
	@each ->
		$fieldset = $(this).closest("fieldset")
		$clone = $(this).clone(true)
		$clone.clear_form()
		$clone.find("select.autocomplete").autocomplete()
		$clone.insertAfter($(this)).hide().slideDown slideTime
		$fieldset.find("button.remove").prop "disabled", $fieldset.find(".multiple:not(.dying)").length is 1

$.fn.removeInputs = (slideTime) ->
	if typeof slideTime is "undefined"
		slideTime = 0
	@each ->
		$fieldset = $(this).closest("fieldset")
		$fieldset.find(".multiple").length > 1 and $(this).addClass("dying").slideUp(slideTime, @remove)
		$fieldset.find("button.remove").prop "disabled", $fieldset.find(".multiple:not(.dying)").length is 1

$.fn.clear_form = ->
	@each ->
		$(this).find(":input").prop "disabled", false
		$(this).find(":input:not(:checkbox):not([type=hidden]):not(:submit)").val("")
		$(this).find(":checkbox, :radio").prop "checked", false
		$(this).find(".multiple:gt(0)").removeInputs()
		$(this).find("button.remove").prop 'disabled', true
