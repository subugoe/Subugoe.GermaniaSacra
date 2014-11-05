###
Autocomplete for select fields
###

$.fn.autocomplete = ->

	@each ->

		$select = $(this)
		name = if $select.data('type') then $select.data('type') else $select.attr('name').replace('[]', '')
		isAjax = $select.hasClass('ajax')

		# If already autocomplete-enabled, remove autocomplete first
		$select.hide().siblings('.autocomplete').remove()

		$input = $('<input type="text" placeholder="Zum Suchen tippen&hellip;">').val $select.find(':selected').text()
		$spinner = $ '<i class="spinner spinner-icon"/>'
		$spinner.hide()
		$list = $('<ol class="list"/>')
		$list.css
			# We are relying on all selects being the same height; cannot get height for elements not yet visible
			top: $('select:eq(0)').outerHeight()
		$overlay = $('<div class="overlay autocomplete"/>').append $input, $spinner, $list
		$overlay.insertAfter $select

		# If list items aren't ajaxed, populate list with select options
		unless $select.hasClass('ajax')
			$.each $select.find('option'), (index, element) ->
				$list.append "<li data-uuid='#{$(element).val()}'>#{$(element).text()}</li>"

		$input.click ->
			$input.val('')
			$list.find('li').show().first().addClass('current')
			$list.slideDown()

		$list.on 'click', 'li', ->
			$input.val $(this).text()
			if isAjax
				$select.empty().append("<option value='#{$(this).data('uuid')}' selected>#{$(this).text()}</option>")
			else
				$select.val( $(this).data('uuid') )
			$input.blur()

		oldVal = ''

		$input.on 'keyup', (e) ->

			if isAjax
				if $input.val().length > 0 and $input.val() isnt oldVal
					oldVal = $input.val()
					delay (->
						$spinner.show()
						$.ajax
							url: "/search#{ucfirst(name)}?searchString=#{encodeURIComponent($input.val())}"
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
					if $(item).text().toLowerCase().indexOf( $input.val().toLowerCase() ) > -1
						$(item).show()
					else
						$(item).hide()

			if $list.is ':visible'
				$visibleItems = $list.children(':visible')
				liHeight = $list.children(':first').outerHeight();
				$visibleItems.filter('.current:gt(0)').removeClass('current')
				$current = $visibleItems.filter('.current')
				unless $current.length then $current = $visibleItems.first().addClass('current')
				switch e.which
					when 13 # enter
						e.preventDefault()
						$current.click()
					when 38 # up
						$newCurrent = $current.prevAll(':visible').first()
						unless $newCurrent.length then $newCurrent = $visibleItems.last()
						$current.removeClass('current')
						$newCurrent.addClass('current')
						$list.scrollTop( $visibleItems.index($newCurrent) * liHeight - ($list.height() - liHeight) / 2 )
						false
					when 9, 40 # tab, down
						$newCurrent = $current.nextAll(':visible').first()
						unless $newCurrent.length then $newCurrent = $visibleItems.first()
						$current.removeClass('current')
						$newCurrent.addClass('current')
						$list.scrollTop( $visibleItems.index($newCurrent) * liHeight - ($list.height() - liHeight) / 2 )
						false
					when 35, 36, 27 # esc
						$input.blur()

		$input.blur ->
			$list.slideUp()
			$list.find('.current').removeClass('current')
			$input.val $select.find(':selected').text()

delay = (->
	timer = 0
	(callback, ms) ->
		clearTimeout timer
		timer = setTimeout callback, ms
)()
