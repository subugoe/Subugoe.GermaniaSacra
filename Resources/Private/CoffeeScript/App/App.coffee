germaniaSacra = angular.module('germaniaSacra', [ 'angular-loading-bar', 'restangular', 'datatables' ])
germaniaSacra.config (RestangularProvider) ->
	RestangularProvider.setBaseUrl '/subugoe.germaniasacra'
	RestangularProvider.setRequestSuffix '.json'
	RestangularProvider.setRestangularFields id: 'uuid'

germaniaSacra.factory 'dtOptions', (DTOptionsBuilder) ->
	dtOptions = DTOptionsBuilder
		.newOptions()
		.withDOM('lifpt')
		.withLanguage(sUrl: '/_Resources/Static/Packages/Subugoe.GermaniaSacra/JavaScript/DataTables/German.json')
		.withOption('fnCreatedRow', ->
			angular.element(this).find(':input:not(.processed)').each ->
				angular.element('<span class="val"/>')
					.text(if angular.element(this).is("select") then angular.element(this).find(":selected").text() else angular.element(this).val())
					.hide()
					.insertBefore(angular.element(this))
				angular.element(this).addClass('processed')
			# TODO: This does NOT update the model
			#angular.element(this).find(':input:not(.marker)').change ->
			#	angular.element(this).closest('td').addClass('dirty').closest('tr').find(':checkbox:eq(0)').prop 'checked', true
		)
	dtOptions
