# Convert the advanced search form data to JSON, send to controller and apply the result as a filter on the Koster DataTable

$ ->
	$('#advancedSearch').submit (e) ->
		e.preventDefault()

		json = JSON.stringify $('#advancedSearch').serializeArray()

		search = $.post '/search', json

		search.done (data) ->
			# TODO
			console.dir data

		search.fail (data) ->
			# TODO
			console.dir data
