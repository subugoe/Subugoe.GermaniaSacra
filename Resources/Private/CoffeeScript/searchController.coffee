# Convert the advanced search form data to JSON, send to controller and apply the result as a filter on the Koster DataTable

initSearch = ->

	$('#simple-search, #advanced-search').submit ->
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

	$('form .reset').click (e) ->
		e.preventDefault()
		$('#uuid-filter').val('').change()
		$(this).parents('form').clearForm()
