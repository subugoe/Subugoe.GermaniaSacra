# Convert the advanced search form data to JSON, send to controller and apply the result as a filter on the Koster DataTable

$ ->
	$('#search, #advancedSearch').submit ->
		json = $(this).serializeArray()
		search = $.post '/search', json
		search.success (data) ->
			data = $.parseJSON(data)
			if data.length
				$('#uuidFilter').val(data.join('|')).change()
			else
				# WORKAROUND: Server does return 500 if search term is empty
				$('#uuidFilter').val('```').change()
		search.fail (data) ->
			alert 'Suche fehlgeschlagen'

	$('#search .reset').click ->
		$('#uuidFilter').val('').change()