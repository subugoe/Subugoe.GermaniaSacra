germaniaSacra.controller('monasteryController', function($scope, $http) {
	$http.get('subugoe.germaniasacra/kloster/list.json').success(function(data) {
		$scope.monasteries = data;
	});
	$scope.orderProp = 'kloster_id';
});

germaniaSacra.controller('bistumController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('bistums', 'subugoe.germaniasacra/bistum/list.json').getList().then(function(data) {
		$scope.bistums = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});
});

germaniaSacra.controller('bandController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('baende', 'subugoe.germaniasacra/band/list.json').getList().then(function(data) {
		$scope.baende = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});});

germaniaSacra.controller('ortController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('orts', 'subugoe.germaniasacra/ort/list.json').getList().then(function(data) {
		$scope.orts = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});});

germaniaSacra.controller('ordenController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('ordens', 'subugoe.germaniasacra/orden/list.json').getList().then(function(data) {
		$scope.ordens = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});});

germaniaSacra.controller('landController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('laender', 'subugoe.germaniasacra/land/list.json').getList().then(function(data) {
		$scope.laender = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});
});

<<<<<<< Updated upstream
germaniaSacra.controller('literaturController', function($scope, Restangular) {
	Restangular.allUrl('laender', 'subugoe.germaniasacra/literatur/list.json').getList().then(function(data){
		$scope.literature = data;
	});
	$scope.orderProp = 'citekey';
=======
germaniaSacra.controller('literaturController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('literature', 'subugoe.germaniasacra/proxy/literature').getList().then(function(data) {
		$scope.literature = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});
	$scope.orderProp = 'citeid';
>>>>>>> Stashed changes
});

germaniaSacra.controller('bearbeitungsstatusController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('bearbeitungsstatus', 'subugoe.germaniasacra/bearbeitungsstatus/list.json').getList().then(function(data) {
		$scope.bearbeitungsstatus = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});
	$scope.orderProp = 'name';
});

germaniaSacra.controller('personallistenstatusController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('personallistenstatus', 'subugoe.germaniasacra/personallistenstatus/list.json').getList().then(function(data) {
		$scope.personallistenstatus = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});
	$scope.orderProp = 'name';
});

germaniaSacra.controller('ordenstypController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('ordenstyp', 'subugoe.germaniasacra/ordenstyp/list.json').getList().then(function(data) {
		$scope.ordenstyp = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});
	$scope.orderProp = 'ordenstyp';
});

germaniaSacra.controller('urltypController', function($scope, Restangular, DTOptionsBuilder) {
	Restangular.allUrl('urltyp', 'subugoe.germaniasacra/urltyp/list.json').getList().then(function(data) {
		$scope.urltyp = data;
	});
	$scope.dtOptions = DTOptionsBuilder.newOptions()
		.withDOM('lifpt')
		.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});
	$scope.orderProp = 'name';
});