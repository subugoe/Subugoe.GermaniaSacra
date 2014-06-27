germaniaSacra.controller('monasteryController', function($scope, $http) {
	$http.get('jsonList').success(function(data) {
		$scope.monasteries = data;
	});

	$scope.orderProp = 'kloster_id';
});


germaniaSacra.controller('bistumController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/bistum/list.json').success(function(data) {
		$scope.bistums = data;
	});

	$scope.orderProp = 'uid';
});

germaniaSacra.controller('ordenController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/orden/list.json').success(function(data) {
		$scope.ordens = data;
	});

	$scope.orderProp = 'orden';
});

germaniaSacra.controller('ortController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/ort/list.json').success(function(data) {
		$scope.orts = data;
	});

	$scope.orderProp = 'ort';
});

germaniaSacra.controller('bandController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/band/list.json').success(function(data) {
		$scope.baende = data;
	});

	$scope.orderProp = 'kurztitel';
});

germaniaSacra.controller('landController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/land/list.json').success(function(data) {
		$scope.laender = data;
	});

	$scope.orderProp = 'land';
});

germaniaSacra.controller('literaturController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/literatur/list.json').success(function(data) {
		$scope.literature = data;
	});

	$scope.orderProp = 'citekey';
});

germaniaSacra.controller('bearbeitungsstatusController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/bearbeitungsstatus/list.json').success(function(data) {
		$scope.bearbeitungsstatus = data;
	});

	$scope.orderProp = 'name';
});

germaniaSacra.controller('personallistenstatusController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/personallistenstatus/list.json').success(function(data) {
		$scope.personallistenstatus = data;
	});

	$scope.orderProp = 'name';
});

germaniaSacra.controller('ordenstypController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/ordenstyp/list.json').success(function(data) {
		$scope.ordenstyp = data;
	});

	$scope.orderProp = 'ordenstyp';
});

germaniaSacra.controller('urltypController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/urltyp/list.json').success(function(data) {
		$scope.urltyp = data;
	});

	$scope.orderProp = 'name';
});