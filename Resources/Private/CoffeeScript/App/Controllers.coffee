germaniaSacra.controller 'listController', ($scope, $http, DTOptionsBuilder, DTColumnBuilder) ->

	entityName = $('section[ng-controller]').attr('id')

	$scope.childRows = $('.child-row')
	$scope.ths = $('th')
	$scope.entities = {}
	$scope.updated = {}
	$scope.newCount = 0

	$scope.dtOptions = DTOptionsBuilder
		.fromSource('/entity/' + entityName)
		.withDOM('lifpt')
		.withLanguage(sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json')
		.withOption 'fnDrawCallback', ->
			for entity in $scope.dataTables.context[0].aoData
				id = if entity._aData.uUID then entity._aData.uUID else 'new' + ++$scope.newCount
				$scope.entities[id] = entity._aData
			$trs = angular.element('tbody tr:not(.processed)')
			$trs.each ->
				$tr = $(this)
				id = $tr.find('td:eq(0)').text()
				if id
					$tr.attr('id', id)
				else
					$tr.attr('id', 'new' + $scope.newCount)
				if $scope.childRows.length
					$(this).find('td:eq(0)').click ->
						row = $scope.dataTables.row( $tr )
						if row.child.isShown()
							row.child.hide()
						else
							data = row.data()
							html = ''
							for childRow in $scope.childRows
								type = $(childRow).attr('id')
								for child in $scope.childRows.children()
									name = $(child).data('name')
									# TODO: Use $(child).data('input')
									html += '<label>' + $(child).text() + ' <input name="' + name + '" value="' + data[type][name] + '"></label>'
							row.child( html ).show()
			$trs.children().each ->
				$th = $scope.ths.eq( $(this).index() )
				if $th.length
					name = $th.data('name')
					$input = $('<' + $th.data('input') + '/>').attr({name: name}).val($(this).text())
					$input.change ->
						uUID = $(this).closest('tr').attr('id')
						$checkbox = $(this).closest('tr').find(':checkbox:eq(0)')
						if $(this).attr('name') isnt 'uUID'
							$scope.entities[uUID][name] = $(this).val()
							$checkbox.prop 'checked', true
							$(this).closest('td').addClass('dirty')
						$scope.updated[uUID] = $checkbox.prop 'checked'
					$(this).html $input
			$trs.addClass('processed')
		#.withOption 'stateSave', true

	$scope.dtColumns = []
	$scope.ths.each (index, th) ->
		# TODO: Handle .not-sortable
		$scope.dtColumns.push DTColumnBuilder.newColumn($(th).data('name')).withTitle($(th).text())

	$scope.$on 'event:dataTableLoaded', (event, loadedDT) ->
		$scope.dataTables = loadedDT.dt

	$scope.new = ($event) ->
		$event.preventDefault()
		data = {}
		$scope.ths.each (index, th) ->
			data[$(th).data('name')] = null
		$scope.dataTables.row.add(data).draw()

	$scope.update = ->
		# Only post selected rows
		updatedEntities = {}
		newEntities = {}
		for id, value of $scope.updated
			if value
				# TODO: This is ugly, please change
				if id.substr(0, 3) == 'new'
					newEntities[id] = $scope.entities[id]
				else
					updatedEntities[id] = $scope.entities[id]
		updatedEntities.__csrfToken = $('#__csrfToken').val()
		newEntities.__csrfToken = updatedEntities.__csrfToken
		if updatedEntities or newEntities
			$http.post('subugoe.germaniasacra/' + entityName + '/listupdate', updatedEntities)
				.error (data) ->
					# TODO: Error handler
					$scope.message = 'ERROR'
				.success (data) ->
					if newEntities
						$http.post('subugoe.germaniasacra/' + entityName + '/listcreate', newEntities)
							.error (data) ->
								# TODO: Error handler
								$scope.message = 'ERROR'
							.success (data) ->
								# TODO: Remove class dirty from saved rows
								# TODO: Treat new entities like old ones from now on
								$scope.message = 'Änderungen gespeichert.'
					else
						# TODO: Remove class dirty from saved rows
						$scope.message = 'Änderungen gespeichert.'
