# Make dataTables global so we can use it to access hidden table rows later
dataTable = null

$ ->

	# TODO: Why is this function called even if there is no #list_form element?
	if $("#list_form").length then $("#list_form").populate_list()

	$(".edit").click (e) ->
		e.preventDefault()
		$("#edit_form").read_kloster $(this).attr("href")

	$(".delete").click (e) ->
		e.preventDefault()
		$("#delete").delete_kloster $(this).attr("href")

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
	$this.after('<div id="loading"><i class="spinner spinner-icon left"/> Klosterdaten werden geladen...</div>')

	$.getJSON "klosterListAll", (response) ->

		$this.show()
		$('#loading').hide()

		# Fill "Status" select fields
		bearbeitungsstatusArray = response[1]
		$inputBearbeitungsstatus = $("select[name='bearbeitungsstatus']")
		$inputBearbeitungsstatus.empty()
		$.each bearbeitungsstatusArray, (k, v) ->
			$.each v, (k1, v1) ->
				$inputBearbeitungsstatus.append $("<option>",
					value: v1
					html: k1
				)

		klosters = response[0]

		$table = $this.find("table:eq(0)")
		$trTemplate = $table.find("tbody tr:first")

		# Add a text input to each header cell used for search
		$table.find("thead th").not(":first").not(":last").each ->
			$(this).append '<div><input type="text"></div>'

		dataTable = $table.DataTable(
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
			fnDrawCallback: ->
				# Since only visible textareas can be autosized, this has to be called after every page render
				$table.find("textarea").autosize()
				# Mark row as dirty on change
				$table.find(":input:not(:checkbox)").change ->
					$(this).closest("td").addClass("dirty").closest("tr").find(":checkbox:eq(0)").prop "checked", true
		)

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

		# Fill the DataTable
		$.each klosters, (index, kloster) ->

			# Clone with triggers for edit and delete
			$tr = $trTemplate.clone(true)
			$tr.find(":input").each ->

				name = $(this).attr("name")

				if typeof name is "undefined"
					return

				val = kloster[name]

				if $(this).is("select")
					if name is "bearbeitungsstatus"
						$tr.find("select[name=bearbeitungsstatus] option").each (i, opt) ->
							if opt.value is val
								$(opt).attr "selected", "selected"
					else
						$(this).append "<option>" + val + "</option>"
				else if name isnt "__csrfToken"
					$(this).val val

				$('<span class="val"/>').text(if $(this).is("select") then $(this).find(":selected").text() else $(this).val()).hide().insertBefore $(this)

			$tr.find(".edit").attr "href", "edit/" + kloster.uuid
			$tr.find(".delete").attr "href", "delete/" + kloster.uuid
			$tr.find("input.csrf").attr "id", "csrf" + index
			dataTable.row.add $tr

		# Remove template row and draw table
		dataTable.row($trTemplate).remove().draw()

	return