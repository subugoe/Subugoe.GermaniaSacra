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
		$form.find('button.remove').prop 'disabled', true
