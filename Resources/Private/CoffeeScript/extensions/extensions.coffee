$.fn.appendSaveButton = (messageId) ->
	@each ->
		$button = $('<button/>',
			disabled: 'disabled'
			type: 'submit'
			html: "<i class='icon-disk'></i> #{germaniaSacra.messages[messageId]}</span>"
		)
		$(this)
			.append $button
			.append $('#csrf').clone().removeAttr('id')

$.fn.appendAddRemoveButtons = ->
	@each ->
		$(this).append '<div class="add-remove-buttons"><span class="button remove">&minus;</span><span class="button add">+</span></div>'
		$('.button', $(this)).click (e) ->
			if not $(this).hasClass('disabled')
				e.preventDefault()
				div = $(this).closest('.multiple')
				if $(this).hasClass('remove')
					doit = confirm germaniaSacra.messages.askRemove
					if doit then div.removeInputs 250
				else if $(this).hasClass('add')
					div.addInputs 250

$.fn.addInputs = (slideTime) ->
	unless slideTime? then slideTime = 0
	@each ->
		$fieldset = $(this).closest('fieldset')
		$clone = $(this).clone(true)
		$clone.clearForm()
		$clone.find('select').autocomplete()
		$clone.find('.coordinates').geopicker()
		$clone.insertAfter( $(this) ).hide().slideDown slideTime
		$fieldset.find('.remove').toggleClass 'disabled', $fieldset.find('.multiple:not(.dying)').length is 1

$.fn.removeInputs = (slideTime) ->
	if not slideTime? then slideTime = 0
	@each ->
		$fieldset = $(this).closest('fieldset')
		$fieldset.find('.multiple').length > 1 and $(this).addClass('dying').slideUp(slideTime, @remove)
		$fieldset.find('.remove').toggleClass 'disabled', $fieldset.find('.multiple:not(.dying)').length is 1

$.fn.clearForm = ->
	@each ->
		$form = $(this)
		$form.find('label').removeClass('dirty')
		$form.find(':input').prop('disabled', false)
		$form.find(':input:not([name=__csrfToken]):not(:checkbox):not(:submit)').val('')
		$form.find(':checkbox, :radio').prop('checked', false)
		$form.find('select option:contains("––"), select option:contains("keine Angabe"), select option:contains("unbekannt")').prop('selected', true)
		$form.find('.multiple:gt(0)').removeInputs()
		$form.find('.map-container').remove()
		$form.find('button.remove').prop('disabled', true)
