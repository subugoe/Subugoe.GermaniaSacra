germaniaSacra.controller 'listController', ($scope) ->

	entityName = $('section[ng-controller]').attr('id')

	# Init the view after the template has been rendered
	$scope.$watch (->
		$('#content > section').attr('id')
	), ((newval, oldval) ->
		if newval? then init_view()
	), true
