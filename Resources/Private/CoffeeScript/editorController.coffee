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
		if $(this).find("input[name=uUID]").val().length
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
	url = 'getOptions'
	$.getJSON url, (response) ->

		# Fill select fields with available options
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
			$selects = $('#edit select[name="' + name + '"], select[name="' + name + '[]"]')
			$selects.empty().append $('<option>',
				value: ''
				text: ''
			)
			$.each values, (index, object) ->
				$.each object, (value, uuid) ->
					$selects.append $('<option>',
						value: uuid
						text: value
					)

# Clear the edit form for a new Kloster
newAction = ->
	$('#list').slideUp()
	$('#edit').slideDown()
	$(this).clear_form()
	# Get selects to be autocompleted by class
	$(this).find('.autocomplete').autocomplete('ort')
	$(this).find('input[type=url]').keyup()
	$(this).find('textarea').trigger('autosize.resize')

# Create a new Kloster
createAction = (type, data) ->

	$this = $('#edit form')

	if type is 'kloster'
		$.post('kloster/create', $this.serialize()).done((respond, status, jqXHR) ->
			# TODO: Please find a way to trigger the Solr update server-side
			$.get('solrUpdateWhenKlosterCreate',
				uuid: respond
			)
			message 'Ein neuer Eintrag wurde angelegt.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText
	else
		$.post(type + '/create', $this.serialize()).done((respond, status, jqXHR) ->
			message 'Ein neuer Eintrag wurde angelegt.'
		).fail (jqXHR, textStatus) ->
			message 'Fehler'
			console.dir jqXHR.responseText

# Load a single entity into the edit form
readAction = (type, id) ->

	$this = $('#edit form')
	$this.clear_form()

	$('#list').slideUp()
	$('#loading').slideDown()

	url = type + '/read/' + id

	$.getJSON url, (obj) ->

		$this.attr 'action', type + '/update/' + id

		# TODO: Merge handling of Kloster and other types
		if type is 'kloster'

			$fieldset = $('#klosterdaten')
			if $fieldset.length
				$fieldset.find('label :input').each ->
					name = $(this).attr('name')
					unless name?
						return name = name.replace('[]', '')
					if name is 'changeddate' or name is 'creationdate'
						val = if obj[name] then obj[name].date.substr(0, obj[name].date.indexOf('.')) else ''
					else
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

			$this.find('label :input').each ->
				name = $(this).attr('name')
				val = obj[name]
				$(this).val val

		$('#edit').slideDown()
		$('#loading').slideUp()
		# TODO: Generalize
		$this.find('.autocomplete').autocomplete('ort')
		$this.find('input[type=url]').keyup()
		$this.find('textarea').trigger('autosize.resize')

# Update a single entity
updateAction = (type) ->
	$this = $(this)
	url = $this.attr 'action'
	$.post(url, $this.serialize()).done((respond, status, jqXHR) ->
		message 'Ihre Änderungen wurden gespeichert.'
		# TODO: Please find a way to trigger the Solr update server-side
		if type is 'kloster'
			$.post("updateSolrAfterListUpdate", {uuids: respond})
	).fail (jqXHR, textStatus) ->
		message 'Fehler'
		console.dir jqXHR.responseText
