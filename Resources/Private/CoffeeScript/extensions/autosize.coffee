$.fn.autosize = ->

	@each ->

		$(this).unbind('keyup')

		$(this).keyup (e) ->
			el = $(this)[0]
			el.style.height = 0
			el.style.height = ( el.scrollHeight + 2 ) + "px";

		$(this).keyup()
