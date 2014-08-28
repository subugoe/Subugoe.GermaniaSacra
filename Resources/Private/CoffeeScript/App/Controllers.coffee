germaniaSacra.controller 'monasteryController', ($scope, $http) ->
	$http.get('subugoe.germaniasacra/kloster/list.json').success (data) ->
		$scope.monasteries = data

	$scope.orderProp = 'kloster_id'

germaniaSacra.controller 'listController', ($scope, $http, Restangular, dtOptions) ->

	type = angular.element('section[ng-controller]').attr('id')

	Restangular.allUrl('entities', Restangular.configuration.baseUrl + '/' + type + '/list' + Restangular.configuration.suffix).getList().then (data) ->
		$scope.entities = data
		$scope.original = data

	$scope.dtOptions = dtOptions

	$scope.update = ->
		# Only post selected rows
		changes = {}
		# TODO: Selected should be set on change of related inputs
		for entity in $scope.entities
			if entity.selected
				changes[entity.uUID] = entity
		$http.post(Restangular.configuration.baseUrl + '/' + type + '/update', changes)
			.error (data) ->
				# TODO
				$scope.message = 'ERROR'
			.success (data) ->
				$scope.message = 'Änderungen gespeichert.'
