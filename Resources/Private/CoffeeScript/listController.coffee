# Make dataTables global so we can use it to access hidden table rows later
dataTable = null

# Fill the Kloster list
populateListAction = (type) ->

	$this = $('#' + type)

	if ! $this.length
		alert('There has to be a <section> whose id equals type')
		return

	$this.hide()
	$('#loading').show()

	$table = $this.find("table:eq(0)")

	# Add a text input to each header cell used for search
	$table.find("thead th").not(":first").not(":last").each ->
		$(this).append '<div><input type="text"></div>'

	$ths = $table.find('th')
	columns = []
	$ths.each ->
		if $(this).data('name')?
			columns.push
				data: $(this).data('name')
	columns.push
		class: 'no-wrap show-only-on-hover'
		data: null
		defaultContent: $ths.last().data('html')

	selectOptions = {}
	dataTable = $table.DataTable(
		sAjaxSource: '/entity/' + type
		columns: columns
		autoWidth: false
		columnDefs: [
			bSortable: false
			aTargets: [ "not-sortable" ]
		]
		dom: "lipt" # 'l' - Length changing, 'f' - Filtering input, 't' - The table, 'i' - Information, 'p' - Pagination, 'r' - pRocessing
		language:
			url: "/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json"
		order: [ [ 3, "asc" ] ]
		fnServerData: (sSource, aoData, fnCallback, oSettings) ->
			oSettings.jqXHR = $.ajax
				dataType: 'json'
				type: 'GET'
				url: sSource
				data: aoData
				success: [ajaxSuccess, fnCallback]
		fnDrawCallback: ->
			# Since only visible textareas can be autosized, this has to be called after every page render
			$tr = $table.find('tbody tr:not(.processed)')
			$tr.children().each ->
				$th = $table.find('th[data-name]').eq( $(this).index() )
				if $th.length
					$input = $('<' + $th.data('input') + '/>').attr
						name: $th.data('name')
					# Fill selects
					if $th.data('input') is 'select'
						select_name = $th.data('name')
						if selectOptions[select_name]?
							for obj in selectOptions[select_name]
								$input.append $('<option/>').text(obj.name).attr('value', obj.uuid)
					$(this).html( $input.val($(this).text()) )
			$tr.each ->
				uuid = $(this).find(':input[name=uuid]').val()
				$(this).find("textarea").autosize()
				# Mark row as dirty on change
				$(this).find(":input:not(:checkbox)").change ->
					$(this).closest("td").addClass("dirty").closest("tr").find(":checkbox:eq(0)").prop "checked", true
			$tr.addClass('processed')
		ajaxSuccess = (json) ->
			$this.show()
			$('#loading').hide()
			# TODO: Get select options for each select type
			selectOptions.bearbeitungsstatus = json.bearbeitungsstatus
	)

	# Click handlers for edit and delete
	$table.on "click", ".edit", (e) ->
		e.preventDefault()
		editAction(type, $(this).closest('tr').find(':input[name=uuid]').val())
	$table.on "click", ".delete", (e) ->
		e.preventDefault()
		deleteAction(type, $(this).closest('tr').find(':input[name=uuid]').val())

	# Apply the search
	dataTable.columns().eq(0).each (colIdx) ->
		$("input", dataTable.column(colIdx).header()).click((e) ->
			e.stopPropagation()
		).on "keyup change", ->
			dataTable.column(colIdx).search(@value).draw()

	# Filter table by "search all" return values
	$("body").append '<input id="uuid_filter" type="hidden">'
	$("#uuid_filter").change ->
		# enable regex, disable smart search (enabling both will not work)
		dataTable.column(0).search(@value, true, false).draw()

	return

# Save the Kloster list
updateListAction = (type) ->

	$this = $('#' + type)

	$rows = dataTable.$('tr').has('input:checked')
	formData = {}
	$rows.each ->
		uuid = $(this).find(':input[name=uuid]').val()
		formData['klosters[' + uuid + ']'] = {}
		$(this).find(':input:not([name=uuid])').each (i, input) ->
			if input.name then formData['klosters[' + uuid + ']'][input.name] = input.value
			return
	formData.__csrfToken = $(this).find('input[name=__csrfToken]').val()
	$.post('updateList', formData).done((respond, status, jqXHR) ->
		$.post("updateSolrAfterListUpdate", {uuids: respond}).done((respond, status, jqXHR) ->
			if status is "success"
				$this.message 'Ihre Änderungen wurden gespeichert.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText
	).fail (jqXHR, textStatus) ->
		$this.message 'Error'
		console.dir jqXHR.responseText
