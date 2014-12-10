class germaniaSacra.List

	constructor: (@type) ->

		@scope = $('#list')
		self = @

		@dataTable = null

		@editList()

		$('.new', @scope).click (e) ->
			e.preventDefault()
			germaniaSacra.editor.new()

		$('form', @scope).submit (e) ->
			e.preventDefault()
			if $(this).find('input[name=uUID]:checked').length is 0
				germaniaSacra.message 'Wählen Sie bitte mindestens einen Eintrag aus.'
				return false
			else
				self.updateList()
				return true

	editList: ->

		self = @

		$('#search, #list').hide()
		germaniaSacra.message germaniaSacra.messages.loading, false

		$table = @scope.find('table:eq(0)')

		# Add a text input to each header cell used for search
		$table.find('thead th').not(':first').not(':last').each ->
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

		@dataTable = $table.DataTable
			sAjaxSource: '/entity/' + @type
			columns: columns
			autoWidth: false
			pageLength: 100
			columnDefs: [
				bSortable: false
				aTargets: [ 'not-sortable' ]
			]
			dom: 'lipt' # 'l' - Length changing, 'f' - Filtering input, 't' - The table, 'i' - Information, 'p' - Pagination, 'r' - pRocessing
			language:
				url: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'
			order: [ [ orderBy, 'asc' ] ]
			fnServerData: (sSource, aoData, fnCallback, oSettings) ->
				oSettings.jqXHR = $.ajax
					cache: false
					dataType: 'json'
					type: 'GET'
					url: sSource
					data: aoData
					success: [ajaxSuccess, fnCallback]
					error: -> germaniaSacra.message 'Fehler: Daten konnten nicht geladen werden.'

			fnDrawCallback: ->

				$tr = $table.find('tbody tr:not(.processed)')

				$tr.children().each ->

					$td = $(this)
					$th = $table.find('th[data-name]').eq( $td.index() )
					value = $td.text().trim()

					if $th.length

						dataInput = $th.data('input')
						name = $th.data('name')

						if dataInput is 'checkbox'
							$input = $('<input type="checkbox"/>')
							if value is '1' then $input.prop('checked', true)
							if name isnt 'uUID' then value = 1
						else
							$input = $("<#{dataInput}/>")

						# Fill selects

						if dataInput.indexOf('select') is 0
							if germaniaSacra.selectOptions[name]?
								for uuid, text of germaniaSacra.selectOptions[name]
									$input.append $('<option/>').text(text).attr('value', uuid)
								for optionUuid, option of germaniaSacra.selectOptions[name]
									if option is value
										value = optionUuid
										break
							else # name is not in selectOptions, assume <uuid>:<text>, options will be ajaxed on change
								[uuid, text] = value.trim().split(':', 2)
								if uuid
									$input.append $('<option/>').text(text).attr('value', uuid)
									value = uuid
								else
									value = ''

						$(this).html $input.attr('name', name).val( value )

				$tr.each ->
					uuid = $(this).find(':input[name=uUID]').val()
					# Since only visible textareas can be autosized, this has to be called after every page render
					$(this).find('textarea').autosize()
					# Mark row as dirty on change
					$(this).find(':input:not([name=uUID])').change ->
						$(this).closest('td').addClass('dirty').closest('tr').find(':checkbox:eq(0)').prop 'checked', true
						$('body').addClass('dirty')
						$(':submit[type=submit]', self.scope).prop('disabled', false)

				$tr.find('select').autocomplete()
				$tr.addClass('processed')

			ajaxSuccess = (json) ->
				$('#search, #list').slideDown()
				$('#message').slideUp()
				# TODO: Find a more elegant way to use text instead of uuid for filtering and sorting
				for index, entity of json.data
					# Prepare options data for selects. Currently only this one is used within the lists.
					json.data[index].bearbeitungsstatus = germaniaSacra.selectOptions.bearbeitungsstatus[entity.bearbeitungsstatus]
					# WORKAROUND: Fix table filtering. Empty values pose a problem.
					for key, value of entity
						if not value then json.data[index][key] = ' '

		# Click handlers for edit and delete

		$table.on 'click', '.edit', (e) ->
			e.preventDefault()
			uuid = $(this).closest('tr').find(':input[name=uUID]').first().val()
			germaniaSacra.editor.edit(uuid)
		$table.on 'click', '.delete', (e) ->
			e.preventDefault()
			uuid = $(this).closest('tr').find(':input[name=uUID]').first().val()
			self.delete(uuid)

		# Apply the search
		@dataTable.columns().eq(0).each (colIdx) ->
			$('input', self.dataTable.column(colIdx).header()).click((e) ->
				e.stopPropagation()
			).on 'keyup change', ->
				self.dataTable.column(colIdx).search(@value).draw()

		# Filter table by "search all" return values
		$('#uuid-filter').change ->
			# Enable regex, disable smart search (enabling both will not work)
			self.dataTable.column(0).search(@value, true, false).draw()

		return

	updateList: ->

		$form = $('form', @scope)

		$rows = @dataTable.$('tr').has('td:first input:checked')
		formData = {}
		formData.data = {}
		$rows.each (i, row) ->
			uuid = $(row).find(':input[name=uUID]').first().val()
			formData.data[uuid] = {}
			$(row).find(':input:not([name=uUID])').each (i, input) ->
				if not $(input).is(':checkbox') or $(input).prop('checked')
					if input.name then formData.data[uuid][input.name] = input.value
				return
		formData.__csrfToken = $('#csrf').val()
		$.post(@type + '/updateList', formData).done((respond, status, jqXHR) =>
			germaniaSacra.message 'Ihre Änderungen wurden gespeichert.'
			$form.find('.dirty').removeClass('dirty')
			$form.find('input[name=uUID]').prop('checked', false)
			$('body').removeClass('dirty')
			$(':submit[type=submit]', @scope).prop('disabled', true)
		).fail (jqXHR, textStatus) ->
			germaniaSacra.message 'Fehler: Daten konnten nicht gespeichert werden.'

		return

	# Delete a single entity
	delete: (uuid) ->
		check = confirm germaniaSacra.messages.askDelete
		if check is true
			csrf = $('#csrf').val()
			$.post(@type + '/delete/' + uuid,
				__csrfToken: csrf
			).done((respond, status, jqXHR) =>
				if status is 'success'
					@dataTable
						.row( $('tr').has("td:first input[value='#{uuid}']") )
						.remove()
						.draw()
					germaniaSacra.message 'Der Eintrag wurde gelöscht.'
			).fail (jqXHR, textStatus) ->
				germaniaSacra.message 'Fehler: Eintrag konnte nicht gelöscht werden.'
		return

	reload: ->
		@dataTable.ajax.reload()
