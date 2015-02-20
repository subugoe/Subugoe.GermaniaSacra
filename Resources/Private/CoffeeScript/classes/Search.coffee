class germaniaSacra.Search

	constructor: ->

		@scope = $('#simple-search, #advanced-search')

		@advancedSearchRequest = null

		$('select', @scope).autocomplete()

		$('#simple-search').submit =>
			@simpleSearch()

		$('#advanced-search').submit =>
			@advancedSearch()

		$('.reset', @scope).click (e) =>
			e.preventDefault()
			@reset(true)

	simpleSearch: ->
		germaniaSacra.message 'loading', false
		germaniaSacra.list.resetFilters()
		searchString = $("#simple-search [name='alle']").val()
		@advancedSearchRequest = null
		germaniaSacra.list.dataTable.search( searchString, true, false ).draw()

	advancedSearch: ->
		germaniaSacra.message 'loading', false
		germaniaSacra.list.resetFilters()
		@advancedSearchRequest = $('#advanced-search').serializeArray()
		germaniaSacra.list.dataTable.ajax.reload()

	reset: (redraw = false) ->
		@scope.clearForm()
		@advancedSearchRequest = null
		germaniaSacra.list.dataTable.search('')
		if redraw
			germaniaSacra.message 'loading', false
			germaniaSacra.list.dataTable.column(0).search('').draw()
