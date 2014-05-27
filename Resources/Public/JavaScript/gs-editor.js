$(function () {
    $("#edit textarea").autosize(),

	$("#new textarea").autosize(),

	$("fieldset .multiple").append('<div class="large-2 columns text-right"><button class="remove">-</button><button class="add">+</button></div>'),
	$("fieldset .multiple button").click(function (t) {
        t.preventDefault();
        var e = $(this).closest(".multiple"),
            i = e.closest("fieldset");
        if ($(this).hasClass("remove")) i.find(".multiple").length > 1 && e.addClass("dying").slideUp(null, this.remove);
        else if ($(this).hasClass("add")) {
            var n = e.clone(!0);
            n.find(":input").val(""), n.css("display", "none").insertAfter(e).slideDown()
        }
        i.find("button.remove").prop("disabled", 1 === i.find(".multiple:not(.dying)").length)
    }),
	$("input[type=url]").keyup(function () {
        $(this).parent().next(".link").html('<a href="' + $(this).val() + '">' + $(this).val() + "</a>")
    }),
	$("fieldset .multiple .remove").click(), $(".togglable").hide(), $(".toggle").click(function (t) {
        t.preventDefault();
        var e = $(this).text(),
        i = e.substr(e.lastIndexOf(" "));
        $(this).text(e.substring(0, e.lastIndexOf(" ") + 1) + $(this).data("text")), $(this).data("text", i), $(this).closest("fieldset").next(".togglable").slideToggle()
    }),
	$(".edit").click(function (t) {

	        var key = $(this).index(".edit");
	        var selector = "a#editLink" + key;
	        var url = $(selector).attr('href');

            t.preventDefault(),
		    $("#edit").populate_kloster(($(this).index(".edit") - 1), url),
		    $("#edit").slideDown(),
		    $("html, body").animate({
                scrollTop: $("#edit").position().top
            })
    }),
	$("#edit").hide(),
	$(".close").click(function (t) {
		t.preventDefault();
		var e = $(this).parent();
		e.slideUp(), $("html, body").animate({
			scrollTop: 0
		})
	}),

	$(".new").click(function (t) {
		t.preventDefault();
		$("#new").new_kloster();
        $("#new").slideDown();
         $("html, body").animate({
              scrollTop: $("#new").position().top
         })
	}),

	$("#new").hide(),
	$(".close").click(function (t) {
		t.preventDefault();
		var e = $(this).parent();
		e.slideUp(), $("html, body").animate({
			scrollTop: 0
		})
	}),

	$("#UpdateList").submit(function (t) {
		t.preventDefault();
		if ($("input[name='auswahl[]']:checked").length == 0) {
			alert("Wählen Sie bitte mindestens einen Eintrag aus.");
			return false;
		}
		var url = $('#UpdateList').attr("action");
		$("#UpdateList").update_list(url);
	}),

	$("#EditKloster").submit(function (t) {
		t.preventDefault();
		var url = $('#EditKloster').attr("action");
		$("#EditKloster").update_kloster(url);
	}),

	$("#NewKloster").submit(function (t) {
		t.preventDefault();
		$("#NewKloster").create_kloster();
	}),

	$(".delete").click(function (t) {
		t.preventDefault();
		var key = $(this).index(".delete");
		var selector = "a#deleteLink" + key;
		var uuid = $(selector).attr('href');
		var csrfSelector = "input#csrf" + key;
		var csrf = $(csrfSelector).val();

		$("#delete").delete_kloster(uuid, csrf);
	}),

	$( "select[name='ort[]']" ).on("click", function (t) {
		t.preventDefault();
		var index = $(this).index("select[name='ort[]']");
		$( "select[name='ort[]']:eq(" + index + ")" ).replaceWith( '<input id="searchOrt" type="text" name="ort">' );
	}),

	$( "#searchOrt" ).keyup(function (t) {
		t.preventDefault();
		var tabindex = ($(this).prop("tabindex"));
		var ort = $(this).val();
		if (ort.length > 3) {
			$("#searchOrt").find_ort(ort, tabindex);
		}
	}),

	$( "#searchOrtEdit" ).keyup(function (t) {
		t.preventDefault();
		var tabindex = ($(this).prop("tabindex"));
		var ort = $(this).val();
		if (ort.length > 3) {
			$("#searchOrt").find_ortEdit(ort, tabindex);
		}
	}),

	$( "#searchOrtNew" ).keyup(function (t) {
		t.preventDefault();
		var tabindex = ($(this).prop("tabindex"));
		var ort = $(this).val();
		if (ort.length > 3) {
		  	$("#searchOrt").find_ortNew(ort, tabindex);
		}
	}),

	$( "a" ).on("click", function (t) {
		t.preventDefault();
		var page = $(this).data("page");

		if (page) {
			var url = "kloster?--subugoe-germaniasacra-viewhelpers-paginateviewhelper[currentPage]=" + page;
			window.location = url;
		}
	}),

	$("#list").populate_liste(temp_page)
});