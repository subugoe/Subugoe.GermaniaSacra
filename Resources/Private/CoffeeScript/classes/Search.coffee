class germaniaSacra.Search

	constructor: ->

		@scope = $('#simple-search, #advanced-search')

		@scope.submit ->
			json = $(this).serializeArray()
			search = $.post '/search', json
			search.success (data) ->
				data = $.parseJSON(data)
				if data.length
					$('#uuid-filter').val(data.join('|')).change()
				else
					# WORKAROUND: Server does return 500 if search term is empty
					$('#uuid-filter').val('```').change()
			search.fail (data) ->
				alert 'Suche fehlgeschlagen'

		$('.reset', @scope).click (e) ->
			e.preventDefault()
			$('#uuid-filter').val('').change()
			$(this).parents('form').clearForm()
