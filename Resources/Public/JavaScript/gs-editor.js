$(function() {

	$('button').click( function(e) {
		e.preventDefault()
	})

	$("#edit textarea, #new textarea").autosize()
	$("#edit, #new").hide()

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

	function add_or_remove_inputs($action, $time) {
		var div = $(this).closest(".multiple"),
			fieldset = div.closest("fieldset")
		if ( typeof $time === undefined ) $time = 0

	}

	$("input[type=url]").keyup(function() {
		$(this).parent().next(".link").html('<a href="' + $(this).val() + '">' + $(this).val() + "</a>")
	})

	$("fieldset .multiple .remove").click()
	$(".togglable + .togglable").hide()

	$(".toggle").click( function(e) {
		e.preventDefault()
		$(this).closest('.togglable').siblings('.togglable').addBack().slideToggle()
	})

	$(".edit").click(function(t) {
		t.preventDefault()
		$("#edit").populate_kloster($(this).attr("href"))
	})

	$(".close").click(function(t) {
		t.preventDefault()
		$(this).parent().closest('div[id]').slideUp()
		$('#browse').slideDown()
	})

	$(".new").click(function(t) {
		t.preventDefault()
		$("#new").new_kloster()
		$("#new").slideDown()
		$("#browse").slideUp()
	})

	$(".delete").click(function(t) {
		t.preventDefault()
		var key = $(this).index(".delete")
		var csrfSelector = "input#csrf" + key
		var csrf = $(csrfSelector).val()
		$("#delete").delete_kloster($(this).attr("href"), csrf)
	})

	$("#UpdateList").submit(function(t) {
		t.preventDefault()
		if ($("input[name='auswahl[]']:checked").length == 0) {
			alert("Wählen Sie bitte mindestens einen Eintrag aus.")
			return false
		}
		var url = $('#UpdateList').attr("action")
		$("#UpdateList").update_list(url)
	})

	$("#EditKloster").submit(function(t) {
		t.preventDefault()
		var url = $('#EditKloster').attr("action")
		$("#EditKloster").update_kloster(url);
	})

	$("#NewKloster").submit(function(t) {
		t.preventDefault()
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
	});

});

jQuery.fn.extend({
	addInputs: function(slideTime) {
		if ( typeof slideTime === undefined ) slideTime = 0
		return this.each(function() {
			var $fieldset = $(this).closest('fieldset')
			var $clone = $(this).clone(true)
			$clone.find(':input').val('')
			$clone.insertAfter( $(this) ).hide().slideDown(slideTime)
			$fieldset.find("button.remove").prop("disabled", $fieldset.find(".multiple:not(.dying)").length === 1)
		})
	},
	removeInputs: function(slideTime) {
		if ( typeof slideTime === undefined ) slideTime = 0
		return this.each(function() {
			var $fieldset = $(this).closest('fieldset')
			$fieldset.find(".multiple").length > 1 && $(this).addClass("dying").slideUp(slideTime, this.remove)
			$fieldset.find("button.remove").prop("disabled", $fieldset.find(".multiple:not(.dying)").length === 1)
		})
	}
})
