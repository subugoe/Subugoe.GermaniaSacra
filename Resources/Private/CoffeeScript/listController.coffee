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
		else
			updateListAction(type)
			return true

# Fill the list
editListAction = (type) ->

	$this = $('#list')

	if ! $this.length
		alert('There has to be a <section> whose id equals type')
		return

	$('#search, #list').hide()
	message s_loading, false

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
			$tr = $table.find('tbody tr:not(.processed)')
			$tr.children().each ->
				$td = $(this)
				$th = $table.find('th[data-name]').eq( $td.index() )
				if $th.length
					if $th.data('input') is 'checkbox'
						$input = $('<input type="checkbox"/>')
					else
						$input = $("<#{$th.data('input')}/>")
					$input.attr('name', $th.data('name'))
					# Fill selects
					if $th.data('input') is 'select'
						selectName = $th.data('name')
						if selectOptions[selectName]?
							for obj in selectOptions[selectName]
								$input.append $('<option/>').text(obj.name).attr('value', obj.uuid)
					else if $th.data('input') is 'checkbox'
						if $td.text() is '1' then $input.prop('checked', true)
						if $input.attr('name') isnt 'uuid' and $input.attr('name') isnt 'uUID'
							$td.text('1')
					$(this).html( $input.val($(this).text()) )
			$tr.each ->
				uuid = $(this).find(':input[name=uuid]').val()
				# Since only visible textareas can be autosized, this has to be called after every page render
				$(this).find("textarea").autosize()
				# Mark row as dirty on change
				$(this).find(":input:not([name=uuid]):not([name=uUID])").change ->
					$(this).closest("td").addClass("dirty").closest("tr").find(":checkbox:eq(0)").prop "checked", true
			$tr.addClass('processed')
		ajaxSuccess = (json) ->
			$('#search, #list').slideDown()
			$('#message').slideUp()
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
	$("body").append '<input id="uuid-filter" type="hidden">'
	$("#uuid-filter").change ->
		# enable regex, disable smart search (enabling both will not work)
		dataTable.column(0).search(@value, true, false).draw()

	return

# Save the list
updateListAction = (type) ->

	$form = $('#list form')

	$rows = dataTable.$('tr').has('td:first input:checked')
	formData = {}
	formData.data = {}
	$rows.each (i, row) ->
		uuid = $(row).find('input[name=uuid], input[name=uUID]').first().val()
		formData.data[uuid] = {}
		$(row).find(':input:not([name=uuid]):not([name=uUID])').each (i, input) ->
			if not $(input).is(':checkbox') or $(input).prop('checked')
				if input.name then formData.data[uuid][input.name] = input.value
			return
	formData.__csrfToken = $('#csrf').val()
	$.post(type + '/updateList', formData).done((respond, status, jqXHR) ->
		# TODO: Please find a way to trigger the Solr update server-side
		if type is 'kloster'
			$.post("updateSolrAfterListUpdate", {uuids: respond})
		message 'Ihre Änderungen wurden gespeichert.'
		$form.find('.dirty').removeClass('dirty')
	).fail (jqXHR, textStatus) ->
		message 'Fehler: Daten konnten nicht gespeichert werden.'

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
			message 'Fehler: Eintrag konnte nicht gelöscht werden.'
	return
