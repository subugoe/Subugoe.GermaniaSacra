germaniaSacra = angular.module('germaniaSacra', ['angular-loading-bar', 'datatables', 'ngRoute', 'ngResource'])

germaniaSacra.config ($routeProvider, $locationProvider) ->
	templatePath = '/_Resources/Static/Packages/Subugoe.GermaniaSacra/Templates/'
	$locationProvider.html5Mode(true)
	$routeProvider
	.when('/', { redirectTo: '/kloster' })
	.when('/kloster', { templateUrl: templatePath + 'Kloster.html' })
	.when('/ort', { templateUrl: templatePath + 'Ort.html' })
	.when('/orden', { templateUrl: templatePath + 'Orden.html' })
	.when('/band', { templateUrl: templatePath + 'Band.html' })
	.when('/bistum', { templateUrl: templatePath + 'Bistum.html' })
	.when('/land', { templateUrl: templatePath + 'Land.html' })
	.when('/literatur', { templateUrl: templatePath + 'Literatur.html' })
	.when('/bearbeitungsstatus', { templateUrl: templatePath + 'Bearbeitungsstatus.html' })
	.when('/personallistenstatus', { templateUrl: templatePath + 'Personallistenstatus.html' })
	.when('/ordenstyp', { templateUrl: templatePath + 'Ordenstyp.html' })
	.when('/urltyp', { templateUrl: templatePath + 'Urltyp.html' })
	.otherwise({ redirectTo: '/' })