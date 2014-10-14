germaniaSacra.controller 'listController', ($scope, $http, DTOptionsBuilder, DTColumnBuilder) ->

	entityName = $('section[ng-controller]').attr('id')

	$scope.entities = {}
	responsePromise = $http.get('/entity/' + entityName)
	responsePromise.success (data, status, headers, config) ->
		$scope.entities = data
	responsePromise.error (data, status, headers, config) ->
		$scope.message = 'Daten konnten nicht geladen werden'

	$scope.dtOptions = DTOptionsBuilder
		.newOptions()
		.withDOM('lifpt')
		.withLanguage(sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json')
		.withOption 'fnCreatedRow', ->
			$(this).find(':input:not(.processed)').each ->
				$('<span class="val"/>')
					.text if $(this).is("select") then $(this).find(":selected").text() else $(this).val()
					.hide()
					.insertBefore $(this)
				$(this).addClass('processed')
		.withOption "rowCallback", (nRow, aData, iDisplayIndex, iDisplayIndexFull) ->
			$(":input:gt(0)", nRow).bind "change", ->
				$(this).closest('td').addClass('dirty')
				$scope.$apply ->
					$scope.entities[nRow._DT_RowIndex].selected = true
			nRow
		.withDisplayLength(100);

	$scope.update = ->
		# Only post selected rows
		changes = {}
		for entity in $scope.entities
			if entity.selected
				changes[entity.uUID] = entity
		changes.__csrfToken = $('#__csrfToken').val()
		$http.post('subugoe.germaniasacra/' + entityName + '/listupdate', changes)
			.error (data) ->
				# TODO: Error handler
				$scope.message = 'ERROR'
			.success (data) ->
				# TODO: Remove class dirty from saved rows
				$scope.message = 'Änderungen gespeichert.'