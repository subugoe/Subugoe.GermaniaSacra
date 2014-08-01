germaniaSacra.controller('monasteryController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/kloster/list.json').success(function(data) {
		$scope.monasteries = data;
	});
	$scope.orderProp = 'kloster_id';
});

germaniaSacra.controller('bistumController', function($scope, Restangular, datatables) {
	Restangular.allUrl('bistums', Restangular.configuration.baseUrl + '/bistum/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.bistums = data;
	});
	$scope.dtOptions = datatables;
});

germaniaSacra.controller('bandController', function($scope, Restangular, datatables) {
	Restangular.allUrl('baende', Restangular.configuration.baseUrl + '/band/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.baende = data;
	});
	$scope.dtOptions = datatables;
});

germaniaSacra.controller('ortController', function($scope, Restangular, datatables) {
	Restangular.allUrl('orts', Restangular.configuration.baseUrl + '/ort/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.orts = data;
	});
	$scope.dtOptions = datatables;
});

germaniaSacra.controller('ordenController', function($scope, Restangular, datatables) {
	Restangular.allUrl('ordens', Restangular.configuration.baseUrl + '/orden/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.ordens = data;
	});
	$scope.dtOptions = datatables;
});

germaniaSacra.controller('landController', function($scope, Restangular, datatables) {
	Restangular.oneUrl('laender', Restangular.configuration.baseUrl + '/land/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.laender = data;
	});
	$scope.update = function(land) {
		Restangular.oneUrl('land', Restangular.configuration.baseUrl + '/land/update' + Restangular.configuration.suffix).save();
	}
	$scope.dtOptions = datatables;
});

germaniaSacra.controller('literaturController', function($scope, Restangular, datatables) {
	Restangular.allUrl('literature', Restangular.configuration.baseUrl + '/proxy/literature').getList().then(function(data) {
		$scope.literature = data;
	});
	$scope.dtOptions = datatables;
	$scope.orderProp = 'citeid';
});

germaniaSacra.controller('bearbeitungsstatusController', function($scope, Restangular, datatables) {
	Restangular.allUrl('bearbeitungsstatus', Restangular.configuration.baseUrl + '/bearbeitungsstatus/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.bearbeitungsstatus = data;
	});
	$scope.dtOptions = datatables;
	$scope.orderProp = 'name';
});

germaniaSacra.controller('personallistenstatusController', function($scope, Restangular, datatables) {
	Restangular.allUrl('personallistenstatus', Restangular.configuration.baseUrl + '/personallistenstatus/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.personallistenstatus = data;
	});
	$scope.dtOptions = datatables;
	$scope.orderProp = 'name';
});

germaniaSacra.controller('ordenstypController', function($scope, Restangular, datatables) {
	Restangular.allUrl('ordenstyp', Restangular.configuration.baseUrl + '/ordenstyp/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.ordenstyp = data;
	});
	$scope.dtOptions = datatables;
	$scope.orderProp = 'ordenstyp';
});

germaniaSacra.controller('urltypController', function($scope, Restangular, datatables) {
	Restangular.allUrl('urltyp', Restangular.configuration.baseUrl + '/urltyp/list' + Restangular.configuration.suffix).getList().then(function(data) {
		$scope.urltyp = data;
	});
	$scope.dtOptions = datatables;
	$scope.orderProp = 'name';
});