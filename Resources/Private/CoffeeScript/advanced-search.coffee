# Convert the advanced search form data to JSON, send to controller and apply the result as a filter on the Koster DataTable

$ ->
	$('#search_form, #advanced_search_form').submit ->
		json = $(this).serializeArray()
		search = $.post '/search', json
		search.success (data) ->
			data = $.parseJSON(data)
			if data.length
				$('#uuid_filter').val(data.join('|')).change()
			else
				# WORKAROUND: Server does return 500 if search term is empty
				$('#uuid_filter').val('```').change()
		search.fail (data) ->
			alert 'Suche fehlgeschlagen'

	$('#search_form .reset').click (e) ->
		e.preventDefault()
		$('input[name=alle]').val('')
		$('#uuid_filter').val('').change()