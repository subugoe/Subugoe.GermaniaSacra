$( function() {
	$('.coordinates :input').focus( function() {

		var $container = $(this).closest('.coordinates');

		if ( $container.find('#map').length > 0 ) return false;

		$('<div id="map-container"><button class="close">&times;</button></div>').css({width: $container.width()}).appendTo( $container );
		$('#map-container').prepend( $('<div id="map"></div>').css({width: $container.width()}) ).slideDown();

		$('#map-container .close').click( function() {
			$('#map-container').slideUp(null, function() { $(this).remove(); });
		});
		// Close map when close button is or another element outside container is clicked
		$('html').click(function() {
			$('#map-container .close').click();
		});
		$('.coordinates').click(function(event){
			event.stopPropagation();
		});

		var map = L.map('map').setView([51.163, 10.448], 6);
		map.invalidateSize();
		L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
			maxZoom: 18,
			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
			id: 'examples.map-i86knfo3'
		}).addTo(map);

		// var popup = L.popup();
		// Insert coordinates at pointer into input fields
		map.on('click', function onMapClick(e) {
			// Insert values into input fields, cut to 6 decimals (correspongs to a precision of about 10 cm)
			$container.find(':input[name$="breite[]"]').val( e.latlng.lat.toFixed(6) );
			$container.find(':input[name$="laenge[]"]').val( e.latlng.lng.toFixed(6) );
			//popup
			//	.setLatLng(e.latlng)
			//	.setContent(e.latlng.lat + " " + e.latlng.lng)
			//	.openOn(map);
		});

	});
});