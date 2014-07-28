germaniaSacra.controller('monasteryController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/kloster/list.json').success(function(data) {
		$scope.monasteries = data;
	});

	$scope.orderProp = 'kloster_id';
});

germaniaSacra.controller('bistumController', function($scope, Restangular) {
	Restangular.allUrl('bistums', 'subugoe.germaniasacra/bistum/list.json').getList().then(function(data){
		$scope.bistums = data;
	});

});

germaniaSacra.controller('bandController', function($scope, Restangular) {
	Restangular.allUrl('baende', 'subugoe.germaniasacra/band/list.json').getList().then(function(data){
		$scope.baende = data;
	});

});

germaniaSacra.controller('ortController', function($scope, Restangular) {
	Restangular.allUrl('orts', 'subugoe.germaniasacra/ort/list.json').getList().then(function(data){
		$scope.orts = data;
	});

});

germaniaSacra.controller('ordenController', function($scope, Restangular) {
	Restangular.allUrl('ordens', 'subugoe.germaniasacra/orden/list.json').getList().then(function(data){
		$scope.ordens = data;
	});
});

germaniaSacra.controller('landController', function($scope, Restangular) {
	Restangular.allUrl('laender', 'subugoe.germaniasacra/land/list.json').getList().then(function(data){
		$scope.laender = data;
	});
});

germaniaSacra.controller('literaturController', function($scope, Restangular) {
	Restangular.allUrl('laender', 'subugoe.germaniasacra/literatur/list.json').getList().then(function(data){
		$scope.literature = data;
	});
	$scope.orderProp = 'citekey';
});

germaniaSacra.controller('bearbeitungsstatusController', function($scope, Restangular) {
	Restangular.allUrl('bearbeitungsstatus', 'subugoe.germaniasacra/bearbeitungsstatus/list.json').getList().then(function(data){
		$scope.bearbeitungsstatus = data;
	});

	$scope.orderProp = 'name';
});

germaniaSacra.controller('personallistenstatusController', function($scope, Restangular) {
	Restangular.allUrl('personallistenstatus', 'subugoe.germaniasacra/personallistenstatus/list.json').getList().then(function(data){
		$scope.personallistenstatus = data;
	});

	$scope.orderProp = 'name';
});

germaniaSacra.controller('ordenstypController', function($scope, Restangular) {
	Restangular.allUrl('ordenstyp', 'subugoe.germaniasacra/ordenstyp/list.json').getList().then(function(data){
		$scope.ordenstyp = data;
	});

	$scope.orderProp = 'ordenstyp';
});

germaniaSacra.controller('urltypController', function($scope, Restangular) {
	Restangular.allUrl('urltyp', 'subugoe.germaniasacra/urltyp/list.json').getList().then(function(data){
		$scope.urltyp = data;
	});

	$scope.orderProp = 'name';
});