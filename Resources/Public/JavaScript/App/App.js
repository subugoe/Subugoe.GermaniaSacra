var germaniaSacra = angular.module('germaniaSacra', ['angular-loading-bar', 'restangular', 'datatables']);

germaniaSacra.config(function(RestangularProvider) {
	RestangularProvider.setBaseUrl('/subugoe.germaniasacra');
	RestangularProvider.setRequestSuffix('.json');
	RestangularProvider.setRestangularFields({
		id: "uuid"
	});

});

germaniaSacra.factory('datatables', function(DTOptionsBuilder) {
	var dtOptions = DTOptionsBuilder.newOptions()
			.withDOM('lifpt')
			.withLanguage({sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json'});



    return dtOptions;
});