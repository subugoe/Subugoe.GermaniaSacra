germaniaSacra = angular.module('germaniaSacra', ['ngRoute'])

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
		.when('/bearbeiter', { templateUrl: templatePath + 'Bearbeiter.html' })
		.when('/publish', { templateUrl: templatePath + 'Publish.html' })
		.when('/importlog', { templateUrl: templatePath + 'Importlog.html' })
		.otherwise({ redirectTo: '/' })

germaniaSacra.notSpecifiedValues = ['––', 'keine Angabe', 'unbekannt'] # –– is two n-dashes, not just --

germaniaSacra.messages =
	saveChanges: 'Änderungen speichern'
	saveChangesWithCount: '<span class="count">0</span> <span class="singular hidden">geänderten Datensatz</span><span class="plural">geänderte Datensätze</span> speichern'
	askDelete: 'Möchten Sie diesen Eintrag wirklich löschen?'
	askRemove: 'Möchten Sie dieses Feld wirklich entfernen?'
	askUnsavedChanges: 'Sind Sie sicher, dass Sie diese Ansicht verlassen möchten? Ihre Änderungen wurden nicht gespeichert.'
	loading: '<i class="spinner spinner-icon"></i> Wird geladen&hellip;'
	optionsLoadError: 'Fehler: Optionen können nicht geladen werden'
	entryCreated: 'Ein neuer Eintrag wurde angelegt.'
	entryCreateError: 'Fehler: Eintrag konnte nicht angelegt werden.'
	dataLoadError: 'Fehler: Daten konnten nicht geladen werden.'
	publishError: 'Fehler: Daten konnten nicht veröffentlicht werden.'
	changesSaved: 'Ihre Änderungen wurden gespeichert.'
	changesSavedReloadList: 'Ihre Änderungen wurden gespeichert. <i class="spinner spinner-icon"></i> Liste wird neu geladen&hellip;'
	changesSaveError: 'Fehler: Ihre Änderungen konnten nicht gespeichert werden.'
	selectAtLeastOneEntry: 'Wählen Sie bitte mindestens einen Eintrag aus.'
	entryDeleted: 'Der Eintrag wurde gelöscht.'
	entryDeleteError: 'Fehler: Eintrag konnte nicht gelöscht werden.'
	urlTypeNotSet: 'URL-Typ darf nicht leer sein.'
