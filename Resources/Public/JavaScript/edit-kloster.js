$(function() {

	$("#edit textarea").autosize()
	$("#edit").hide().populate_selects()

	$("fieldset .multiple").append('<div class="add-remove-buttons"><button class="remove">-</button><button class="add">+</button></div>')
	$("fieldset .multiple button").click(function(e) {
		e.preventDefault()
		var div = $(this).closest('.multiple')
		if ($(this).hasClass("remove")) {
			div.removeInputs(250)
		} else if ($(this).hasClass("add")) {
			div.addInputs(250)
		}
	})

	$("input[type=url]").keyup(function() {
		$(this).parent().next(".link").html( $(this).val() ? '<a class="icon-link" href="' + $(this).val() + '" target="_blank"></a>' : '' )
	})

	$("fieldset .multiple .remove").click()
	$(".togglable + .togglable").hide()

	$(".toggle").click( function(e) {
		e.preventDefault()
		$(this).closest('.togglable').siblings('.togglable').addBack().slideToggle()
	})

	$(".edit").click(function(e) {
		e.preventDefault()
		$("#edit").populate_kloster( $(this).attr("href") )
	})

	$(".new").click(function(e) {
		e.preventDefault()
		$("#edit").new_kloster()
	})

	$(".close").click(function(e) {
		e.preventDefault()
		$(this).parent().closest('div[id]').slideUp()
		$('#browse').slideDown()
	})

	$(".delete").click(function(e) {
		e.preventDefault()
		var key = $(this).index(".delete")
		var csrfSelector = "input#csrf" + key
		var csrf = $(csrfSelector).val()
		$("#delete").delete_kloster($(this).attr("href"), csrf)
	})

	$("#UpdateList").submit(function(e) {
		e.preventDefault()
		if ($("input[name='auswahl[]']:checked").length == 0) {
			alert('Wählen Sie bitte mindestens einen Eintrag aus.')
			return false
		}
		var url = $('#UpdateList').attr("action")
		$("#UpdateList").update_list(url)
	})

	$("#EditKloster").submit(function(e) {
		e.preventDefault()
		var url = $('#EditKloster').attr("action")
	    $('select[disabled]').prop('disabled', false).addClass('disabled');
		if ( ! $(this).find('[name=kloster_id]').val().length ) {
			$("#EditKloster").create_kloster()
		} else {
			$("#EditKloster").update_kloster(url);
		}
		$('select.disabled').prop('disabled', true);
	})

	$("#NewKloster").submit(function(e) {
		e.preventDefault()
		$("#NewKloster").create_kloster()
	})

	if ($('#UpdateList').length !== 0) {
		$("#UpdateList #list").populate_liste();
	}

	// Submit by pressing Ctrl-S (PC) or Meta-S (Mac)
	$(window).bind('keydown', function(e) {
		if (e.ctrlKey || e.metaKey) {
			switch (String.fromCharCode(e.which).toLowerCase()) {
				case 's':
					e.preventDefault();
					$(':submit:visible:last').click();
					break;
			}
		}
	})

	function add_or_remove_inputs($action, $time) {
		var div = $(this).closest(".multiple"),
			fieldset = div.closest("fieldset")
		if ( typeof $time === undefined ) $time = 0
	}

});

$.fn.addInputs = function(slideTime) {
	if ( typeof slideTime === undefined ) slideTime = 0
	return this.each(function() {
		var $fieldset = $(this).closest('fieldset')
		var $clone = $(this).clone(true)
		$clone.find(':input').val('')
		$clone.find('select.autocomplete').autocomplete()
		$clone.insertAfter( $(this) ).hide().slideDown(slideTime)
		$fieldset.find('button.remove').prop('disabled', $fieldset.find('.multiple:not(.dying)').length === 1)
	})
}

$.fn.removeInputs = function(slideTime) {
	if ( typeof slideTime === undefined ) slideTime = 0
	return this.each(function() {
		var $fieldset = $(this).closest('fieldset')
		$fieldset.find('.multiple').length > 1 && $(this).addClass('dying').slideUp(slideTime, this.remove)
		$fieldset.find('button.remove').prop('disabled', $fieldset.find('.multiple:not(.dying)').length === 1)
	})
}
