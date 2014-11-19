initEditor = (type) ->

	populateSelectsAction(type)

	$("#edit textarea").autosize()
	$('#edit').hide()

	# Update clickable URL next to URL input
	$("#edit input[type=url]").keyup ->
		$(this).parent().next(".link").html( if $(this).val() then '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' else '' )

	$("#edit fieldset .multiple .remove").click()

	$("#edit :input:not([name=uUID])").change ->
		$(this).closest("label").addClass("dirty")
		$('body').addClass('dirty')
		$("#edit :submit").prop('disabled', false)

	$("#edit .close").click (e) ->
		if not $('.dirty').length or confirmDiscardChanges()
			$(this).parent().closest("section[id]").slideUp()
			$("#search, #list").slideDown()
			$('.dirty').removeClass('dirty')
			e.preventDefault()

	$("#edit form").submit (e) ->
		e.preventDefault()
		$("select:disabled").prop("disabled", false).addClass "disabled"
		if $(this).find(":input[name=uUID]").first().val().length
			updateAction( type )
		else
			createAction( type )
		$("select.disabled").prop "disabled", true

	initGeopicker()

# Fill the select fields with options
# TODO: Use generalized function, only populate one type at a time and fetch separately
populateSelectsAction = ->
	$.getJSON 'getOptions', (response) ->
		$.each response, (name, values) ->
			$selects = $("#edit select[name='#{name}'], select[name='#{name}[]'], select[name='#{name}_uid']")
			$selects.empty()
			$.each values, (uUID, text) ->
				$selects.append $('<option>',
					value: uUID
					text: text
				)

# Clear the edit form for a new Kloster
newAction = ->
	$form = $('#edit form')
	$form.clearForm()
	$('#search, #list').slideUp()
	$('#edit').slideDown()
	# Select default value for Personallistenstatus
	$form.find('select[name=personallistenstatus] option:contains("Erfassung")').prop('selected', true)
	$("#edit select").autocomplete()
	$form.find('input[type=url]').keyup()
	$form.find('textarea').trigger('autosize.resize')

# Create a new Kloster
createAction = (type, data) ->
	$form = $('#edit form')
	$.post(type + '/create', $form.serialize()).done( (respond, status, jqXHR) ->
		message 'Ein neuer Eintrag wurde angelegt.'
		$form.find('.dirty').removeClass('dirty')
		$('body').removeClass('dirty')
	).fail ->
		message 'Fehler: Eintrag konnte nicht angelegt werden.'

# Load a single entity into the edit form
editAction = (type, id) ->

	$form = $('#edit form')
	$form.clearForm()

	$('#search, #list').slideUp()
	message s_loading, false

	$.getJSON("#{type}/edit/#{id}").done( (obj) ->

		for name, value of obj
			$input = $form.find(":input[data-type=#{name}], :input[name='#{name}']").first()
			if $input.is(':checkbox')
				$input.val 1
				if value then $input.prop('checked', true)
			else if $input.is('select.ajax')
				$input.html $('<option />',
					value: value.uUID
					text: value.name
				).attr('selected', true)
			else
				$input.val value

		$fieldset = $('#klosterdaten')
		if $fieldset.length
			$fieldset.find('label :input').each ->
				name = $(this).attr('name')
				if name then name = name.replace('[]', '')
				val = obj[name]
				$(this).val val

		$fieldset = $('#klosterorden')
		if $fieldset.length and obj.klosterorden?
			$.each obj.klosterorden, (index, value) ->
				if index > 0
					$fieldset.find('.multiple:last()').addInputs 0
				$fieldset.find('.multiple:last() label :input').each ->
					name = $(this).attr('name')
					if typeof name is 'undefined'
						return
					name = name.replace('[]', '')
					$(this).val value[name]

		$fieldset = $('#klosterstandorte')
		if $fieldset.length and obj.klosterstandorte?
			$.each obj.klosterstandorte, (index, value) ->
				if index > 0
					$fieldset.find('.multiple:last()').addInputs 0
				$fieldset.find('.multiple:last() label :input').each ->
					name = $(this).attr('name')
					return	if typeof name is 'undefined'
					name = name.replace('[]', '')
					val = value[name]
					if name is 'wuestung'
						if name is 'wuestung'
							checkedCondition = value[name] is 1
							$(this).prop 'checked', checkedCondition
					else if name is 'ort'
						$(this).html $('<option />',
							value: value.uUID
							text: value.ort
						).attr('selected', true)
					else if name is 'bistum'
						$(this).val(value[name])
						text = $(this).find(':selected')
						disabledCondition = text isnt 'keine Angabe' and text isnt ''
						$(this).prop 'disabled', disabledCondition
					else
						$(this).val value[name]

		$fieldset = $('#links')
		if $fieldset.length and obj.url?
			$fieldset.find('.multiple:eq(0)').removeInputs 0
			$.each obj.url, (index, value) ->
				if value.url_typ_name is 'GND'
					$(':input[name=gnd]').val value.url
					$(':input[name=gnd_label]').val value.url_label
				else if value.url_typ_name is 'Wikipedia'
					$(':input[name=wikipedia]').val value.url
					$(':input[name=wikipedia_label]').val value.url_label
				else
					$fieldset.find('.multiple:last()').addInputs 0
					$fieldset.find('.multiple:last() label :input').each ->
						name = $(this).attr('name')
						if typeof name is 'undefined'
							return
						name = name.replace('[]', '')
						$(this).val value[name]

		$fieldset = $('#literatur')
		if $fieldset.length and obj.literatur?
			$.each obj.literatur, (index, value) ->
				if index > 0
					$fieldset.find('.multiple:last()').addInputs 0
				$fieldset.find('.multiple:last() label :input').each ->
					name = $(this).attr('name')
					if typeof name is 'undefined'
						return
					name = name.replace('[]', '')
					$(this).val value

		$('#edit').slideDown()
		$('#message').slideUp()
		$form.find('select').autocomplete()
		$form.find('input[type=url]').keyup()
		$form.find('textarea').trigger('autosize.resize')

	).fail( ->
		message 'Fehler: Daten konnten nicht geladen werden.'
	)

# Update a single entity
updateAction = (type) ->
	$form = $("#edit form")
	uuid = $form.find(':input[name=uUID]').first().val()
	$.post("#{type}/update/#{uuid}", $form.serialize()).done((respond, status, jqXHR) ->
		message 'Ihre Änderungen wurden gespeichert.'
		$form.find('.dirty').removeClass('dirty')
		$('body').removeClass('dirty')
		$("#edit :submit").prop('disabled', true)
	).fail ->
		message 'Fehler: Ihre Änderungen konnten nicht gespeichert werden.'
