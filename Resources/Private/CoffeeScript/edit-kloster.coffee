$ ->
	
	$("#edit textarea").autosize()
	$("#edit").hide().populate_selects()
	$("fieldset .multiple").append "<div class=\"add-remove-buttons\"><button class=\"remove\">-</button><button class=\"add\">+</button></div>"
	$("fieldset .multiple button").click (e) ->
		e.preventDefault()
		div = $(this).closest(".multiple")
		if $(this).hasClass("remove")
			div.removeInputs 250
		else if $(this).hasClass("add")
			div.addInputs 250 

	$("input[type=url]").keyup ->
		$(this).parent().next(".link").html( if $(this).val() then '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' else '' )

	$("fieldset .multiple .remove").click()
	$(".togglable + .togglable").hide()
	$(".toggle").click (e) ->
		e.preventDefault()
		$(this).closest(".togglable").siblings(".togglable").addBack().slideToggle()

	$(".edit").click (e) ->
		e.preventDefault()
		$("#edit").populate_kloster $(this).attr("href")

	$(".new").click (e) ->
		e.preventDefault()
		$("#edit").new_kloster()

	$(".close").click (e) ->
		e.preventDefault()
		$(this).parent().closest("div[id]").slideUp()
		$("#browse").slideDown()

	$(".delete").click (e) ->
		e.preventDefault()
		key = $(this).index(".delete")
		csrfSelector = "input#csrf" + key
		csrf = $(csrfSelector).val()
		$("#delete").delete_kloster $(this).attr("href"), csrf

	$("#UpdateList").submit (e) ->
		e.preventDefault()
		if $("input[name='auswahl[]']:checked").length is 0
			alert "Wählen Sie bitte mindestens einen Eintrag aus."
			return false
		url = $("#UpdateList").attr("action")
		$("#UpdateList").update_list url

	$("#EditKloster").submit (e) ->
		e.preventDefault()
		$("select:disabled").prop("disabled", false).addClass "disabled"
		unless $(this).find("[name=kloster_id]").val().length
			$("#EditKloster").create_kloster()
		else
			$("#EditKloster").update_kloster()
		$("select.disabled").prop "disabled", true

	if $("#UpdateList").length isnt 0
		$("#UpdateList #list").populate_liste()
	
	# Submit by pressing Ctrl-S (PC) or Meta-S (Mac)
	$(window).bind "keydown", (e) ->
		if e.ctrlKey or e.metaKey
			switch String.fromCharCode(e.which).toLowerCase()
				when "s"
					e.preventDefault()
					$(":submit:visible:last").click()
	
	return

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
