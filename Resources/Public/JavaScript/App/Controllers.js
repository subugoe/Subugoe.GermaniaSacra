var germaniaSacra = angular.module('germaniaSacra', []);

germaniaSacra.controller('monasteryController', function($scope, $http) {
	$http.get('jsonList').success(function(data) {
		$scope.monasteries = data;
	});

	$scope.orderProp = 'kloster_id';
});