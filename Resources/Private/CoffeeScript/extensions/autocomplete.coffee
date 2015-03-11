$.fn.autocomplete = ->

	@each ->

		$select = $(this)
		$select.css
			opacity: 0

		if $select.data('type')
			name = $select.data('type')
		else if $select.attr('name')
			name = $select.attr('name').replace('[]', '')
		else
			return false

		isAjax = $select.hasClass('ajax')

		# If already autocomplete-enabled, remove autocomplete first
		$select.siblings('.autocomplete').remove()

		$select.on 'refresh', ->
			$fakeSelect
				.val $select.find(':selected').text()
				.attr 'title', $select.find(':selected').text() + (if $select.prop('disabled') then " – keine Änderung möglich" else "")
				.prop 'disabled', $select.prop('disabled')

		$fakeSelect = $('<input class="select" type="text">')
		if $select.prop('disabled') then $fakeSelect.prop('disabled', true)
		$select.trigger('refresh')

		$spinner = $('<i class="spinner spinner-icon"/>')
		$filter =  $('<input type="text">').attr 'placeholder', if isAjax then 'Suchen' else 'Filter'
		$filterContainer = $('<div class="filter"/>').append $filter, $spinner
		$list = $('<ol class="list"/>')
		$popup = $('<div class="popup"/>').append $filterContainer, $list
		$popup.css
			# We are relying on all selects being the same height; cannot get height for elements not yet visible
			top: $('select:eq(0)').outerHeight()
		$overlay = $('<div class="overlay autocomplete"/>').append $fakeSelect, $popup
		$overlay.insertAfter $select

		# If list items aren't ajaxed, populate list with select options
		if not $select.hasClass('ajax')
			$.each $select.find('option'), (index, element) ->
				$list.append "<li data-uuid='#{$(element).val()}'>#{$(element).text()}</li>"

		$fakeSelect.focus ->

			$fakeSelect.blur()
			$list.find('li').show().first().addClass('current')
			$('.autocomplete .popup').hide()

			$popup.slideDown()
			$filter.focus()

		$list.on 'click', 'li', ->

			if isAjax
				$select.empty().append("<option value='#{$(this).data('uuid')}' selected>#{$(this).text()}</option>")
			else
				$select.val( $(this).data('uuid') )

			$fakeSelect
				.val $(this).text()
				.attr 'title', $select.find(':selected').text()

			$select.change()
			$(document).click()

		oldVal = ''

		$filter.on 'keydown', (e) ->

			$visibleItems = $list.children(':visible')
			$visibleItems.filter('.current:gt(0)').removeClass('current')
			$current = $visibleItems.filter('.current')
			unless $current.length then $current = $visibleItems.first().addClass('current')
			switch e.which
				when 13 # enter
					e.preventDefault()
					$current.click()
					false
				when 38 # up
					if $list.children().length > 0
						$newCurrent = $current.prevAll(':visible').first()
						unless $newCurrent.length then $newCurrent = $visibleItems.last()
						$current.removeClass('current')
						$newCurrent.addClass('current')
						$list.scrollTop $newCurrent.offset().top - $list.offset().top + $list.scrollTop() - $list.height() / 3
					false
				when 9, 40 # tab, down
					if $list.children().length > 0
						$newCurrent = $current.nextAll(':visible').first()
						unless $newCurrent.length then $newCurrent = $visibleItems.first()
						$current.removeClass('current')
						$newCurrent.addClass('current')
						$list.scrollTop $newCurrent.offset().top - $list.offset().top + $list.scrollTop() - $list.height() / 3
					false
				when 35, 36, 27 # esc
					$(document).click()
				else
					if isAjax
						if $filter.val().length > 0 and $filter.val() isnt oldVal
							oldVal = $filter.val()
							delay (->
								$spinner.show()
								$.ajax
									url: "/search#{name.charAt(0).toUpperCase() + name.slice(1)}?searchString=#{encodeURIComponent($filter.val())}"
									type: 'GET'
									complete: ->
										$spinner.hide()
									error: ->
										alert 'Fehler: Daten konnten nicht geladen werden.'
									success: (data) ->
										json = $.parseJSON data
										$list.empty()
										$.each json, (index, item) ->
											$list.append "<li data-uuid='#{item.uUID}'>#{item.name}</li>"
										$list.slideDown().scrollTop(0).find('li').first().addClass('current')
							), 500
					else
						$.each $list.find('li'), (index, item) ->
							if $(item).text().toLowerCase().indexOf( $filter.val().toLowerCase() ) > -1
								$(item).show()
							else
								$(item).hide()

		# Close popup
		$(document).click ->
			$popup.slideUp()
			$list.find('.current').removeClass('current')
			$filter.val ''

		$overlay.click (e) ->
			return false

	delay = (->
		timer = 0
		(callback, ms) ->
			clearTimeout timer
			timer = setTimeout callback, ms
	)()
