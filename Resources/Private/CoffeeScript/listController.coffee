# Make dataTables global so we can use it to access hidden table rows later
dataTable = null

initList = (type) ->

	editListAction(type)

	$("#list form").submit (e) ->
		e.preventDefault()
		# WORKAROUND for different spelling of uuid
		if $(this).find("input[name=uuid]:checked, input[name=uUID]:checked").length is 0
			message "Wählen Sie bitte mindestens einen Eintrag aus."
			return false
		updateListAction(type)

	return

# Fill the list
editListAction = (type) ->

	$this = $('#list')

	if ! $this.length
		alert('There has to be a <section> whose id equals type')
		return

	$this.hide()
	$('#loading').slideDown()

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

	orderBy = $table.find('th.order-by').index()
	if orderBy < 0 then orderBy = 1

	selectOptions = {}
	dataTable = $table.DataTable(
		sAjaxSource: '/entity/' + type
		columns: columns
		autoWidth: false
		pageLength: 10
		#pageLength: 100
		columnDefs: [
			bSortable: false
			aTargets: [ "not-sortable" ]
		]
		dom: "lipt" # 'l' - Length changing, 'f' - Filtering input, 't' - The table, 'i' - Information, 'p' - Pagination, 'r' - pRocessing
		language:
			url: "/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json"
		order: [ [ orderBy, "asc" ] ]
		fnServerData: (sSource, aoData, fnCallback, oSettings) ->
			oSettings.jqXHR = $.ajax
				cache: false
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
					$input = $('<' + $th.data('input') + '/>').attr('name', $th.data('name'))
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
			$('#loading').slideUp()
			# TODO: Get select options for each select type
			selectOptions.bearbeitungsstatus = json.bearbeitungsstatus
	)

	# Click handlers for edit and delete
	$table.on "click", ".edit", (e) ->
		e.preventDefault()
		# WORKAROUND: uuid is spelled uUID for Stammdaten
		uuid = $(this).closest('tr').find(':input[name=uuid], :input[name=uUID]').first().val()
		editAction(type, uuid)
	$table.on "click", ".delete", (e) ->
		e.preventDefault()
		# WORKAROUND: uuid is spelled uUID for Stammdaten
		uuid = $(this).closest('tr').find(':input[name=uuid], :input[name=uUID]').first().val()
		deleteAction(type, uuid)

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

# Save the list
updateListAction = (type) ->

	$this = $('#list form')

	$rows = dataTable.$('tr').has('input:checked')
	formData = {}
	$rows.each ->
		uuid = $(this).find(':input[name=uuid]').val()
		#formData['klosters[' + uuid + ']'] = {}
		formData[uuid] = {}
		$(this).find(':input:not([name=uuid])').each (i, input) ->
			#if input.name then formData['klosters[' + uuid + ']'][input.name] = input.value
			if input.name then formData[uuid][input.name] = input.value
			return
	formData.__csrfToken = $(this).find('input[name=__csrfToken]').val()
	$.post(type + '/updateList', formData).done((respond, status, jqXHR) ->
		message 'Ihre Änderungen wurden gespeichert.'
		# TODO: Please find a way to trigger the Solr update server-side
		if type is 'kloster'
			$.post("updateSolrAfterListUpdate", {uuids: respond})
	).fail (jqXHR, textStatus) ->
		message 'Fehler'
		console.dir jqXHR.responseText

	return

# Delete a single entity
deleteAction = (type, id) ->
	$this = $(this)
	check = confirm 'Wollen Sie diesen Eintrag wirklich löschen?'
	if check is true
		csrf = $('#csrf').val()
		$.post(type + '/delete/' + id,
			__csrfToken: csrf
		).done((respond, status, jqXHR) ->
			if status is 'success'
				message 'Der Eintrag wurde gelöscht.'
		).fail (jqXHR, textStatus) ->
			message 'Fehler'
			console.dir jqXHR.responseText
	return