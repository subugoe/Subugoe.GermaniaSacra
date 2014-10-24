initEditor = (type) ->

	populateSelectsAction(type)

	$("#edit textarea").autosize()
	$('#edit').hide()

	$("#edit fieldset .multiple").append "<div class=\"add-remove-buttons\"><button class=\"remove\">-</button><button class=\"add\">+</button></div>"
	$("#edit fieldset .multiple button").click (e) ->
		e.preventDefault()
		div = $(this).closest(".multiple")
		if $(this).hasClass("remove")
			div.removeInputs 250
		else if $(this).hasClass("add")
			div.addInputs 250

	# Update clickable URL next to URL input
	$("#edit input[type=url]").keyup ->
		$(this).parent().next(".link").html( if $(this).val() then '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' else '' )

	$("#edit fieldset .multiple .remove").click()

	$("#edit .close").click (e) ->
		e.preventDefault()
		$(this).parent().closest("section[id]").slideUp()
		$("#list").slideDown()

	$("#edit form").submit (e) ->
		e.preventDefault()
		$("select:disabled").prop("disabled", false).addClass "disabled"
		if $(this).find(":input[name=uuid], :input[name=uUID]").first().val().length
			updateAction( type )
		else
			createAction( type )
		$("select.disabled").prop "disabled", true

	# Submit by pressing Ctrl-S (PC) or Meta-S (Mac)
	$(window).bind "keydown", (e) ->
		if e.ctrlKey or e.metaKey
			switch String.fromCharCode(e.which).toLowerCase()
				when "s"
					e.preventDefault()
					$(":submit:visible:last").click()

# Fill the select fields with options
# TODO: Use generalized function, only populate one type at a time and fetch separately
populateSelectsAction = ->
	$.getJSON 'getOptions', (response) ->
		options = {}
		options.bearbeitungsstatus = response[0]
		options.personallistenstatus = response[1]
		options.band = response[2]
		options.literatur = response[3]
		options.bistum = response[4]
		options.orden = response[5]
		options.klosterstatus = response[6]
		options.bearbeiter = response[7]
		options.url_typ = response[8]
		$.each options, (name, values) ->
			$selects = $("#edit select[name='#{name}'], select[name='#{name}[]']")
			$selects.empty()
			$.each values, (uuid, text) ->
				$selects.append $('<option>',
					value: uuid
					text: text
				)

# Clear the edit form for a new Kloster
newAction = ->
	$form = $('#edit form')
	$form.clearForm()
	$('#list').slideUp()
	$('#edit').slideDown()
	$("#edit select").autocomplete()
	$("#edit").find('input[type=url]').keyup()
	$("#edit").find('textarea').trigger('autosize.resize')

# Create a new Kloster
createAction = (type, data) ->
	$form = $('#edit form')
	if type is 'kloster'
		$.post('kloster/create', $form.serialize()).done((respond, status, jqXHR) ->
			# TODO: Please find a way to trigger the Solr update server-side
			$.get('solrUpdateWhenKlosterCreate',
				uuid: respond
			)
			message 'Ein neuer Eintrag wurde angelegt.'
		).fail (jqXHR, textStatus) ->
			message 'Error'
			console.dir jqXHR.responseText
	else
		$.post(type + '/create', $form.serialize()).done((respond, status, jqXHR) ->
			message 'Ein neuer Eintrag wurde angelegt.'
		).fail (jqXHR, textStatus) ->
			message 'Fehler'
			console.dir jqXHR.responseText

# Load a single entity into the edit form
editAction = (type, id) ->

	$form = $('#edit form')
	$form.clearForm()

	$('#list').slideUp()
	$('#loading').slideDown()

	$.getJSON "#{type}/edit/#{id}", (obj) ->

		# TODO: Merge handling of Kloster and other types
		if type is 'kloster'

			$form.find(':input[name=uuid]').val(obj.uuid)

			$fieldset = $('#klosterdaten')
			if $fieldset.length
				$fieldset.find('label :input').each ->
					name = $(this).attr('name')
					if name then name = name.replace('[]', '')
					val = obj[name]
					$(this).val val

			$fieldset = $('#klosterorden')
			if $fieldset.length
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
			if $fieldset.length
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
								value: value['uuid']
								text: value['ort']
							).attr('selected', true)
						else if name is 'bistum'
							$(this).val(value[name])
							text = $(this).find(':selected')
							disabledCondition = text isnt 'keine Angabe' and text isnt ''
							$(this).prop 'disabled', disabledCondition
						else
							$(this).val value[name]

			$fieldset = $('#links')
			if $fieldset.length
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
			if $fieldset.length
				$.each obj.literatur, (index, value) ->
					if index > 0
						$fieldset.addInputs 0
					$fieldset.find('.multiple:last() label :input').each ->
						name = $(this).attr('name')
						if typeof name is 'undefined'
							return
						name = name.replace('[]', '')
						$(this).val value

		else

			$form.find(':input:not(:submit)').each ->
				name = $(this).attr('name')
				$(this).val obj[name]

		$('#edit').slideDown()
		$('#loading').slideUp()
		$form.find('select').autocomplete()
		$form.find('input[type=url]').keyup()
		$form.find('textarea').trigger('autosize.resize')
		$('select option:contains("Erfassung")').prop('selected',true)

# Update a single entity
updateAction = (type) ->
	$form = $("#edit form")
	uuid = $form.find(':input[name=uuid], :input[name=uUID]').first().val()
	$.post("#{type}/update/#{uuid}", $form.serialize()).done((respond, status, jqXHR) ->
		message 'Ihre Änderungen wurden gespeichert.'
		# TODO: Find a way to trigger Solr update server-side
		if type is 'kloster'
			$.post("updateSolrAfterListUpdate", {uuids: respond})
	).fail (jqXHR, textStatus) ->
		message 'Fehler: Ihre Änderungen konnten nicht gespeichert werden.'
		console.dir jqXHR.responseText
