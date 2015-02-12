class germaniaSacra.List

	constructor: (@type) ->

		self = @

		@scope = $('#list')

		@dataTable = null

		@formData =
			data: {}
			__csrfToken: $('#csrf').val()

		@editList()

		$('.new', @scope).click (e) ->
			e.preventDefault()
			germaniaSacra.editor.new()

		$('form', @scope).submit (e) ->
			e.preventDefault()
			if not self.formData.data
				germaniaSacra.message 'selectAtLeastOneEntry'
				return false
			else
				self.updateList()
				return true

	editList: ->

		self = @

		$('#search, #list').hide()
		germaniaSacra.message 'loading', false

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
			ajax:
				url: @type + '/list/'
				type: 'post'
				dataSrc: (json) -> self.onJsonLoad(json)
			serverSide: true
			columns: columns
			autoWidth: false
			pageLength: 100
			columnDefs: [
				targets: ['not-sortable']
				sortable: false
			]
			dom: 'lipt' # 'l' - Length changing, 'f' - Filtering input, 't' - The table, 'i' - Information, 'p' - Pagination, 'r' - pRocessing
			language:
				url: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'
			order: [ [ orderBy, 'asc' ] ]
			createdRow: (row, data, dataIndex) -> self.onCreatedRow(this, row)
			drawCallback: -> self.onDraw(this)

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
				clearTimeout self.timeout
				self.timeout = setTimeout(
					=>
						self.dataTable.column(colIdx).search(@value).draw()
					,
					500
				)

		return

	updateList: ->

		$.post(@type + '/updateList', @formData).done( (respond, status, jqXHR) =>
			germaniaSacra.message 'changesSavedReloadList'
			@formData.data = {}
			@scope.find('.dirty').removeClass('dirty')
			$('body').removeClass('dirty')
			@scope.find('input[name=uUID]').prop('checked', false)
			@dataTable.ajax.reload()
			@updateSubmitButton 0
		).fail (jqXHR, textStatus) ->
			germaniaSacra.message 'changesSaveError'

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
					germaniaSacra.message 'entryDeleted'
			).fail (jqXHR, textStatus) ->
				germaniaSacra.message 'entryDeleteError'
		return

	reload: ->
		@dataTable.ajax.reload()

	onJsonLoad: (json) ->
		$('#search, #list').slideDown()
		$('#message').slideUp()
		# TODO: Find a more elegant way to use text instead of uuid for filtering and sorting
		for index, entity of json.data
			# Prepare options data for selects. Currently only this one is used within the lists.
			json.data[index].bearbeitungsstatus = germaniaSacra.selectOptions.bearbeitungsstatus[entity.bearbeitungsstatus]
			# WORKAROUND: Fix table filtering. Empty values pose a problem.
			for key, value of entity
				if not value then json.data[index][key] = ' '
		return json.data

	onCreatedRow: (table, row) ->

		self = @

		$tr = $(row)

		$tr.children().each ->

			$td = $(this)
			$th = table.find('th[data-name]').eq( $td.index() )
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

		$rowSelector = $tr.find(':input[name=uUID]:eq(0)')
		$rowInputs = $tr.find(':input:not([name=uUID])')

		# Build form data and mark row as dirty on change

		$rowInputs.change ->
			uuid = $tr.find(':input[name=uUID]').first().val()
			$(this).closest('td').addClass('dirty')
			$rowSelector.prop('checked', true).change()
			$('body').addClass('dirty')

		$rowSelector.change ->
			uuid = $(this).val()
			if $(this).prop('checked')
				self.formData.data[uuid] = {}
				$tr = $(this).closest('tr')
				$tr.find(':input:not([name=uUID])').each (i, input) ->
					if not $(input).is(':checkbox') or $(input).prop('checked')
						if input.name then self.formData.data[uuid][input.name] = input.value
					return
			else
				delete self.formData.data[uuid]
			self.updateSubmitButton( Object.keys(self.formData.data).length )

		# If this row's data is already part of formData (i.e. it was changed by the user), update the view
		uuid = $rowSelector.val()
		if self.formData.data[uuid]?
			$rowSelector.prop('checked', true)
			for name, value of self.formData.data[uuid]
				$tr.find(":input[name='#{name}']").val(value)

		$tr.find('select').autocomplete()

	onDraw: (table) ->
		# Since only visible textareas can be autosized, this has to be called after every page render.
		# This is still slow (about 500 ms for 100 textareas in current Chrome), consider disabling it for faster rendering.
		table.find('textarea').autosize()

	updateSubmitButton: (count) ->
		$el = $('[type=submit]', @scope)
		$el.find('.count').text(count)
		$el.find('.singular').toggle(count is 1)
		$el.find('.plural').toggle(count isnt 1)
		$el.prop('disabled', count < 1)
