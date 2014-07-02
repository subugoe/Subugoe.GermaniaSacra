$( function() {
	$('.coordinates :input').focus( function() {

		var $container = $(this).closest('.coordinates')

		if ( $container.find('#map').length > 0 ) return false

		// Prepare the map container
		$('<div id="map-container"><button class="close">&times;</button></div>').css({width: $container.width()}).appendTo( $container )
		$('#map-container').prepend( $('<div id="map"></div>').css({width: $container.width()}) ).slideDown()

		// Close button trigger
		$('#map-container .close').hide().click( function() {
			$('#map-container').slideUp(null, function() { $(this).remove() })
		});
		// Close map when close button is or another element outside container is clicked
		$('html').click(function() {
			$('#map-container .close').click()
		});
		$('.coordinates').click(function(event){
			event.stopPropagation()
		});

		// Define map layers
		var layers = []
		layers.mapbox = L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
			maxZoom: 18,
			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
			id: 'examples.map-i86knfo3',
		})
		layers.osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		})
		layers.esri = L.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
			attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
		})
		layers.thunderforest = L.tileLayer('http://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://www.opencyclemap.org">OpenCycleMap</a>, &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
		})

		// Define layer controls
		var baseMaps = {
			"Mapbox": layers.mapbox,
			"OpenStreetMap": layers.osm,
			"ESRI World Imagery": layers.esri,
			"Thunderforest Landscape": layers.thunderforest,
		}
		var overlayMaps = {}

		// Draw the map and add controls, center over and zoom to Germany
		var map = L.map('map', {
			center: [51.163, 10.448],
			zoom: 6,
			layers: layers,
		})
		L.control.layers(baseMaps, overlayMaps).addTo(map)
		L.control.scale().addTo(map)
		map.invalidateSize()

		// Load first map in list
		$('#map .leaflet-control-layers-base input:eq(0)').click()

		// Insert coordinates at pointer into input fields
		map.on('click', function onMapClick(e) {
			// TODO: input[type=number] is localized, so for DE the decimal separator becomes a comma, while JS still uses a point
			var lat = e.latlng.lat.toFixed(6)
			var lng = e.latlng.lng.toFixed(6)
			// Insert values into input fields, cut to 6 decimals (corresponds to a precision of about 10 cm)
			$container.find(':input[name$="breite[]"]').val( lat )
			$container.find(':input[name$="laenge[]"]').val( lng )
			//L.popup()
			//	.setLatLng(e.latlng)
			//	.setContent(lat + " | " + lng)
			//	.openOn(map)
		})

	})
})