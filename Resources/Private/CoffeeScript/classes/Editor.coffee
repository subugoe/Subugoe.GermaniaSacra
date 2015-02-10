class germaniaSacra.Editor

	constructor: (@type) ->

		@scope = $('#edit')
		self = @

		$('textarea', @scope).autosize()
		@scope.hide()

		# Update clickable URL next to URL input
		$('input[type=url]', @scope).keyup ->
			$(this).parent().next(".link").html( if $(this).val() then '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' else '' )

		$('fieldset .multiple .remove', @scope).click()

		$(':input:not([name=uUID])', @scope).change ->
			$(this).closest("label").addClass("dirty")
			$('body').addClass('dirty')
			$(':submit[type=submit]', self.scope).prop('disabled', false)

		$('.close', @scope).click (e) ->
			if not $('.dirty', self.scope).length or confirm(germaniaSacra.messages.askUnsavedChanges)
				$(this).parent().closest('section[id]').slideUp()
				$('#search, #list').slideDown()
				$('.dirty', self.scope).removeClass('dirty')
				e.preventDefault()

		$('form', @scope).submit (e) ->
			e.preventDefault()
			$('select:disabled', @scope).prop('disabled', false).addClass 'disabled'
			if $(this).find(':input[name=uUID]').first().val().length
				self.update()
			else
				self.create()
			$('select.disabled', self.scope).prop 'disabled', true

		$('.coordinates').geopicker()

	# Clear the edit form for a new Kloster
	new: ->
		$form = $('form', @scope)
		$form.clearForm()
		$('#search, #list').slideUp()
		$(@scope).slideDown()
		# Select default value for Personallistenstatus
		$form.find('select[name=personallistenstatus] option:contains("Erfassung")').prop('selected', true)
		$('select', @scope).autocomplete()
		$form.find('input[type=url]').keyup()
		$form.find('textarea').trigger('autosize.resize')

	# Create a new Kloster
	create: (data) ->
		$form = $('form', @scope)
		$.post(@type + '/create', $form.serialize()).done( (respond, status, jqXHR) ->
			germaniaSacra.message 'Ein neuer Eintrag wurde angelegt.'
			$form.find('.dirty').removeClass('dirty')
			$('body').removeClass('dirty')
		).fail ->
			germaniaSacra.message 'Fehler: Eintrag konnte nicht angelegt werden.'

	# Load a single entity into the edit form
	edit: (id) ->

		type = @type

		$form = $('form', @scope)
		$form.clearForm()

		$('#search, #list').slideUp()
		germaniaSacra.message germaniaSacra.messages.loading, false

		$.getJSON("#{type}/edit/#{id}").done( (obj) =>

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

			$fieldset = $('#klosterdaten', @scope)
			if $fieldset.length
				$fieldset.find('label :input').each ->
					name = $(this).attr('name')
					if name then name = name.replace('[]', '')
					val = obj[name]
					$(this).val val

			$fieldset = $('#klosterorden', @scope)
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

			$fieldset = $('#klosterstandorte', @scope)
			if $fieldset.length and obj.klosterstandorte?
				$fieldset.find('.multiple:eq(0)').removeInputs 0
				$.each obj.klosterstandorte, (index, value) ->
					if index > 0
						$fieldset.find('.multiple:last()').addInputs 0
					$fieldset.find('.multiple:last() label :input').each ->
						name = $(this).attr('name')
						if typeof name is 'undefined' then return
						name = name.replace('[]', '')
						val = value[name]
						if name is 'wuestung'
							checkedCondition = value[name] is 1
							$(this).prop 'checked', checkedCondition
						else if name is 'ort'
							$(this).html $('<option />',
								value: value.uUID
								text: value.ort
							).attr('selected', true)
							$(this).change ->
								$.get "#{type}/searchBistum/#{$(this).val()}", (uUID) =>
									$(this).closest('.multiple').find('[name="bistum[]"]').val(uUID).change().trigger('refresh')
						else if name is 'bistum'
							$(this).val(value[name])
							text = $(this).find(':selected')
							disabledCondition = text isnt 'keine Angabe' and text isnt ''
							$(this).prop 'disabled', disabledCondition
						else
							$(this).val value[name]

			$fieldset = $('#links', @scope)
			if $fieldset.length and obj.url?
				$fieldset.find('.multiple:eq(0)').removeInputs 0
				$.each obj.url, (index, value) ->
					if value.url_typ_name is 'GND'
						$form.find(':input[name=gnd]').val value.url
						$form.find(':input[name=gnd_label]').val value.url_label
					else if value.url_typ_name is 'Wikipedia'
						$form.find(':input[name=wikipedia]').val value.url
						$form.find(':input[name=wikipedia_label]').val value.url_label
					else
						$fieldset.find('.multiple:last()').addInputs 0
						$fieldset.find('.multiple:last() label :input').each ->
							name = $(this).attr('name')
							if typeof name is 'undefined'
								return
							name = name.replace('[]', '')
							$(this).val value[name]

			$fieldset = $('#literatur', @scope)
			if $fieldset.length and obj.literatur?
				$.each obj.literatur, (index, value) ->
					if index > 0
						$fieldset.find('.multiple:last()').addInputs 0
					$fieldset.find('.multiple:last() label :input').each ->
						name = $(this).attr('name')
						if typeof name is 'undefined'
							return
						name = name.replace('[]', '')
						$(this).val value[name]

			@scope.slideDown()
			germaniaSacra.hideMessage()
			$form.find('select').autocomplete()
			$form.find('input[type=url]').keyup()
			$form.find('textarea').trigger('autosize.resize')
			# TODO: This should not be necessary, so why is it?
			$(':submit[type=submit]', @scope).prop('disabled', true)

		).fail( ->
			germaniaSacra.message 'Fehler: Daten konnten nicht geladen werden.'
		)

	# Update a single entity
	update: ->
		$form = $('form', @scope)
		uuid = $form.find(':input[name=uUID]').first().val()
		$.post("#{@type}/update/#{uuid}", $form.serialize()).done((respond, status, jqXHR) =>
			germaniaSacra.message 'Ihre Änderungen wurden gespeichert. <i class="spinner spinner-icon"></i> Liste wird neu geladen&hellip;'
			$form.find('.dirty').removeClass('dirty')
			$('body').removeClass('dirty')
			$(':submit[type=submit]', @scope).prop('disabled', true)
			$('.close', @scope).click()
			germaniaSacra.list.reload()
		).fail ->
			germaniaSacra.message 'Fehler: Ihre Änderungen konnten nicht gespeichert werden.'
