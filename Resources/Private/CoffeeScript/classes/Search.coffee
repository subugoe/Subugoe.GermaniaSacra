class germaniaSacra.Search

	constructor: ->

		@scope = $('#simple-search, #advanced-search')

		$('select', @scope).autocomplete()

		$('#simple-search').submit ->
			germaniaSacra.message 'loading', false
			searchString = $("[name='alle']", $(this)).val()
			germaniaSacra.list.dataTable.search( searchString, true, false ).draw()

		$('#advanced-search').submit ->
			germaniaSacra.message 'loading', false
			json = $(this).serializeArray()
			search = $.post '/search', json
			search.success (data) ->
				data = $.parseJSON(data)
				if data.length
					# Enable regex, disable smart search (enabling both will not work)
					searchString = data.join('|')
					germaniaSacra.list.dataTable.column(0).search(searchString, true, false).draw()
				else
					# Reset
					germaniaSacra.list.dataTable.draw()
			search.fail (data) ->
				$('#message').slideUp()
				alert 'Suche fehlgeschlagen'

		$('.reset', @scope).click (e) ->
			germaniaSacra.message 'loading', false
			e.preventDefault()
			germaniaSacra.list.dataTable
				.search('')
				.column(0).search('')
				.draw()
			$(this).parents('form').clearForm()
