###

Autocomplete for select fields

Overlaying input field, data AJAXed on type
Requires returned JSON to contain $uuid and $name for each item

###

$.fn.extend
	autocomplete: () ->
		return this.each ->
			$select = $(this)
			$input = $('<input type="text">').val( $select.find(':selected').text() )
			$input.click ->
				this.select()
			$list = $ '<ul class="dropdown-list"/>'
			$list.css
				top: $select.outerHeight()
			$overlay =  $('<div class="autocomplete"/>').append $input, $list
			$overlay.css
				width: $select.outerWidth()
				height: $select.outerHeight()
				position: 'absolute'
				right: 0
				top: 0
			$overlay.insertAfter $select
			$input.on 'input', ->
				if $input.val().length > 1
					delay (->
						$.ajax
							url: '/searchOrt?searchString=' + encodeURIComponent($input.val())
							type: 'GET'
							error: ->
								console.log 'autocomplete ajax error'
							success: (data) ->
								json = $.parseJSON data
								$list.empty()
								$.each json, (index, element) ->
									$list.append '<li data-uuid="' + element.uuid + '">' + element.name + '</li>'
								$list.slideDown().find('li').first().addClass('current')
								$list.find('li').click ->
									$input.val $(this).text()
									$select.setSelected $(this)
									$list.slideUp()
					), 500
			$input.on 'keydown', (e) ->
				if $list.is ':visible'
					$current = $list.find('.current')
					switch e.which
						when 13
							e.preventDefault()
							$input.val $current.text()
							$select.setSelected $current # enter
							$list.slideUp()
						when 38
							e.preventDefault()
							$current.removeClass('current').prev().addClass('current') # up
						when 9, 40
							e.preventDefault()
							$current.removeClass('current').next().addClass('current') # tab, down
	setSelected: ($data) ->
		return this.each ->
			$(this).empty().append('<option value="' + $data.data('uuid') + '" selected>' + $data.text() + '</option>')

delay = (->
	timer = 0
	(callback, ms) ->
		clearTimeout timer
		timer = setTimeout callback, ms
)()