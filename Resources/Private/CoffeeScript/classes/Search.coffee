class germaniaSacra.Search

	constructor: ->

		@scope = $('#simple-search, #advanced-search')

		$('select', @scope).autocomplete()

		$('#simple-search').submit =>
			@simpleSearch()

		$('#advanced-search').submit =>
			@advancedSearch()

		$('.reset', @scope).click (e) =>
			e.preventDefault()
			@reset()

	simpleSearch: ->
		germaniaSacra.message 'loading', false
		germaniaSacra.list.resetFilters()
		searchString = $("#simple-search [name='alle']").val()
		germaniaSacra.list.dataTable.search( searchString, true, false ).draw()

	advancedSearch: ->
		germaniaSacra.message 'loading', false
		json = $('#advanced-search').serializeArray()
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

	reset: (redraw = false) ->
		@scope.clearForm()
		germaniaSacra.list.dataTable.search('')
		if redraw
			germaniaSacra.message 'loading', false
			germaniaSacra.list.dataTable.column(0).search('').draw()
