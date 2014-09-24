# Make dataTables global so we can use it to access hidden table rows later
dataTable = null

$ ->

	# TODO: Why is this function called even if there is no #list_form element?
	if $("#list_form").length then $("#list_form").populate_list()

	$("#list_form").submit (e) ->
		e.preventDefault()
		if $("input[name^='uuid']:checked").length is 0
			$(this).message "Wählen Sie bitte mindestens einen Eintrag aus."
			return false
		$(this).update_list()

	return

# Fill the Kloster list
$.fn.populate_list = ->

	$this = $(this)

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
		sAjaxSource: '/entity/kloster'
		columns: columns
		autoWidth: false
		columnDefs: [
			bSortable: false
			aTargets: [ "no-sorting" ]
		,
			width: "10%"
			targets: 1
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
				success: [setSelectOptions, fnCallback]
		fnDrawCallback: ->
			# Since only visible textareas can be autosized, this has to be called after every page render
			$tr = $table.find('tbody tr:not(.processed)')
			$tr.children().each ->
				$th = $table.find('th[data-name]').eq( $(this).index() )
				if $th.length
					$input = $('<' + $th.data('input') + '/>').attr
						name: $th.data('name')
					if $th.data('name') is 'bearbeitungsstatus'
						for obj in selectOptions.bearbeitungsstatus
							$input.append $('<option/>').text(obj.name).attr('value', obj.uuid)
					$(this).html( $input.val($(this).text()) )
			$tr.each ->
				uuid = $(this).find(':input[name=uuid]').val()
				$(this).find(".edit").attr "href", "edit/" + uuid
				$(this).find(".delete").attr "href", "delete/" + uuid
				$(this).find("textarea").autosize()
				# Mark row as dirty on change
				$(this).find(":input:not(:checkbox)").change ->
					$(this).closest("td").addClass("dirty").closest("tr").find(":checkbox:eq(0)").prop "checked", true
			$tr.addClass('processed')
		setSelectOptions = (json) ->
			$this.show()
			$('#loading').hide()
			selectOptions.bearbeitungsstatus = json.bearbeitungsstatus
	)

	# Click handlers for edit and delete
	$table.on "click", ".edit", (e) ->
		e.preventDefault()
		$("#edit form").read 'kloster', $(this).attr("href")
	$table.on "click", ".delete", (e) ->
		e.preventDefault()
		$("#delete").delete 'kloster', $(this).attr("href")

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
$.fn.update_list =  ->
	$this = $(this)
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


# Delete a single Kloster
# TODO: Type is not really needed here, URL contains all information
$.fn.delete = (type, url, csrf) ->
	$this = $(this)
	check = confirm 'Wollen Sie diesen Eintrag wirklich löschen?'
	if check is true
		csrf = $('#csrf').val()
		$.post(url,
			__csrfToken: csrf
		).done((respond, status, jqXHR) ->
			if status is "success"
				$this.message 'Der Eintrag wurde gelöscht.'
		).fail (jqXHR, textStatus) ->
			$this.message 'Error'
			console.dir jqXHR.responseText
