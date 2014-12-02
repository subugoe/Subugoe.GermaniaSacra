###
Autocomplete for select fields
###

$.fn.autocomplete = ->

	@each ->

		$select = $(this)
		$select.css
			opacity: 0
		name = if $select.data('type') then $select.data('type') else $select.attr('name').replace('[]', '')
		isAjax = $select.hasClass('ajax')

		# If already autocomplete-enabled, remove autocomplete first
		$select.siblings('.autocomplete').remove()

		$fakeSelect = $('<input class="select" type="text">').val $select.find(':selected').text()
		$spinner = $('<i class="spinner spinner-icon"/>')
		$filter = $('<input type="text" placeholder="Filter">')
		$filterContainer = $('<div class="filter"/>').append $filter
		$list = $('<ol class="list"/>')
		$popup = $('<div class="popup"/>').append $filterContainer, $list
		$popup.css
			# We are relying on all selects being the same height; cannot get height for elements not yet visible
			top: $('select:eq(0)').outerHeight()
		$overlay = $('<div class="overlay autocomplete"/>').append $fakeSelect, $spinner, $popup
		$overlay.insertAfter $select

		# If list items aren't ajaxed, populate list with select options
		unless $select.hasClass('ajax')
			$.each $select.find('option'), (index, element) ->
				$list.append "<li data-uuid='#{$(element).val()}'>#{$(element).text()}</li>"

		$fakeSelect.focus ->
			$fakeSelect.blur()
			$list.find('li').show().first().addClass('current')
			$popup.slideDown()
			$filter.focus()

		$list.on 'click', 'li', ->
			if isAjax
				$select.empty().append("<option value='#{$(this).data('uuid')}' selected>#{$(this).text()}</option>")
			else
				$select.val( $(this).data('uuid') )
			$fakeSelect.val $(this).text()
			$select.trigger('change')
			$(document).click()

		oldVal = ''

		$filter.on 'keydown', (e) ->

			if isAjax
				if $filter.val().length > 0 and $filter.val() isnt oldVal
					oldVal = $filter.val()
					delay (->
						$spinner.show()
						$.ajax
							url: "/search#{ucfirst(name)}?searchString=#{encodeURIComponent($filter.val())}"
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

			if $list.is ':visible'
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
						$newCurrent = $current.prevAll(':visible').first()
						unless $newCurrent.length then $newCurrent = $visibleItems.last()
						$current.removeClass('current')
						$newCurrent.addClass('current')
						$list.scrollTop $newCurrent.offset().top - $list.offset().top + $list.scrollTop() - $list.height() / 3
						false
					when 9, 40 # tab, down
						$newCurrent = $current.nextAll(':visible').first()
						unless $newCurrent.length then $newCurrent = $visibleItems.first()
						$current.removeClass('current')
						$newCurrent.addClass('current')
						$list.scrollTop $newCurrent.offset().top - $list.offset().top + $list.scrollTop() - $list.height() / 3
						false
					when 35, 36, 27 # esc
						$(document).click()

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
