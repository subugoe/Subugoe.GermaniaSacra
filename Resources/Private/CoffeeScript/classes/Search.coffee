class germaniaSacra.Search

	constructor: ->

		@scope = $('#simple-search, #advanced-search')

		$('select', @scope).autocomplete()

		@scope.submit ->
			germaniaSacra.message germaniaSacra.messages.loading, false
			json = $(this).serializeArray()
			search = $.post '/search', json
			search.success (data) ->
				data = $.parseJSON(data)
				if data.length
					$('#uuid-filter').val(data.join('|')).change()
				else
					# WORKAROUND: Server does return 500 if search term is empty
					$('#uuid-filter').val('```').change()
				germaniaSacra.hideMessage()
			search.fail (data) ->
				germaniaSacra.hideMessage()
				alert 'Suche fehlgeschlagen'

		$('.reset', @scope).click (e) ->
			e.preventDefault()
			$('#uuid-filter').val('').change()
			$(this).parents('form').clearForm()
