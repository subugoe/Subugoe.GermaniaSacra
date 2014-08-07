###
Autocomplete for select fields

Overlaying input field, data AJAXed on type
Requires returned JSON to contain $uuid and $name for each item
###

$.fn.extend
	autocomplete: () ->

		@each ->

			if $(this).siblings('.autocomplete').length # Already is autocomplete-enabled
				$(this).siblings('.autocomplete').find('input').val $(this).find(':selected').text()
				return

			$select = $(this).css
				opacity: 0
			$input = $('<input type="text">').val $select.find(':selected').text()
			$spinner = $ '<i class="spinner-icon"/>'
			$spinner.hide()
			$list = $ '<ol/>'
			$list.css
				top: $select.outerHeight()
			$overlay = $('<div class="autocomplete"/>').append $input, $spinner, $list
			$overlay.css
				width: $select.outerWidth()
				height: $select.outerHeight()
				position: 'absolute'
				right: 0
				top: 0
			$overlay.insertAfter $select

			$input.click ->
				this.select()
				$list.slideDown().scrollTop(0).find('li').first().addClass('current')

			$input.on 'input', ->
				if $input.val().length > 0
					delay (->
						$spinner.show()
						$.ajax
							url: '/searchOrt?searchString=' + encodeURIComponent($input.val())
							type: 'GET'
							complete: ->
								$spinner.hide()
							error: ->
								console.log 'autocomplete ajax error'
							success: (data) ->
								json = $.parseJSON data
								$list.empty()
								$.each json, (index, element) ->
									$list.append '<li data-uuid="' + element.uuid + '">' + element.name + '</li>'
								$list.slideDown().scrollTop(0).find('li').first().addClass('current')
								$list.find('li').click ->
									$input.val $(this).text()
									$select.setSelected $(this)
									$list.slideUp()
					), 500

			$input.on 'blur', ->
				$list.slideUp()
				$input.val $select.find(':selected').text()

			$input.on 'keydown', (e) ->
				if $list.is ':visible'
					$lis = $list.children()
					li_height = $list.children(':eq(0)').outerHeight();
					$current = $list.find('.current')
					index = $list.children('.current').siblings().addBack().index( $list.children('.current') )
					switch e.which
						when 13  # enter
							e.preventDefault()
							$input.val $current.text()
							$select.setSelected $current
							$list.slideUp()
						when 38  # up
							if  --index < 0 then index = $lis.length - 1
							$lis.removeClass('current').eq(index).addClass('current')
							$list.scrollTop( index * li_height - ($list.height() - li_height) / 2)
							false
						when 9, 40  # tab, down
							if ++index >= $lis.length then index = 0
							$lis.removeClass('current').eq(index).addClass('current')
							$list.scrollTop( index * li_height - ($list.height() - li_height) / 2)
							false
						when 35, 36, 27 # sec
							$input.blur()

	setSelected: ($data) ->
		@each ->
			$(this).empty().append('<option value="' + $data.data('uuid') + '" selected>' + $data.text() + '</option>')

delay = (->
	timer = 0
	(callback, ms) ->
		clearTimeout timer
		timer = setTimeout callback, ms
)()
