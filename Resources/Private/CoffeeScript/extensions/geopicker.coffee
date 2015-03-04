$.fn.geopicker = ->

	geoJsonSrc = '/proxy/geojson'
	resourcePath = '_Resources/Static/Packages/Subugoe.GermaniaSacra/'

	$(':input', this).unbind('focus').focus =>

		scope = $(this)

		# Skip if map already present
		if $('.map-container', scope).length > 0 then return false

		# Map layers
		layers = []
		layers.osm = L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png",
			attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		)
		layers.esri = L.tileLayer("http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
			attribution: "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community"
		)
		layers.thunderforest = L.tileLayer("http://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png",
			attribution: '&copy; <a href="http://www.opencyclemap.org">OpenCycleMap</a>, &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
		)

		# Layer controls
		baseMaps =
			"OpenStreetMap": layers.osm
			"ESRI World Imagery": layers.esri
			"Thunderforest Landscape": layers.thunderforest

		overlayMaps = {}

		id = "map-#{Math.random()}"
		$map = $("<div id='#{id}' class='map'/>").css
			width: scope.width()
		$container = $('<div class="map-container"><button class="close">&times;</button></div>').css
			width: scope.width()
		.prepend $map
		.appendTo scope
		.slideDown()

		# Close button trigger
		$('.close', $container).hide().click (e) ->
			e.preventDefault()
			$container.slideUp null, ->
				$(this).remove()

		# Close map when close button is or another element outside container is clicked
		$('html').click ->
			$('.close', $container).click()

		scope.click (event) ->
			event.stopPropagation()

		# Get current coordinates, required for determining zoom level
		lat = parseFloat( $('input[name$="breite[]"]', scope).val() ) or 0
		lng = parseFloat( $('input[name$="laenge[]"]', scope).val() ) or 0

		# Draw the map and add controls, center over and zoom to Germany
		map = L.map( id,
			center: [ ( if lat is 0 then 51.163 else lat ), ( if lng is 0 then 10.448 else lng ) ]
			zoom: ( if lat is 0 and lng is 0 then 6 else 18 )
			maxZoom: 19
			layers: layers
		)
		L.control.layers(baseMaps, overlayMaps).addTo map
		L.control.scale().addTo map
		map.invalidateSize()

		icon = L.icon
			iconUrl: resourcePath + 'Images/marker-icon.png'
			shadowUrl: resourcePath + 'Images/marker-shadow.png'
			iconAnchor: [13, 41]

		marker = L.marker([lat, lng], {icon: icon}).addTo(map)

		# Load first map in list
		$('.leaflet-control-layers-base input:eq(0)', scope).click()

		# Insert coordinates at pointer into input fields
		map.on 'click', onMapClick = (e) =>
			lat = e.latlng.lat.toFixed(6)
			lng = e.latlng.lng.toFixed(6)
			doit = confirm "Sollen die Koordinaten #{lat}, #{lng} übernommen werden?"
			if doit is true
				# Insert values into input fields, cut to 6 decimals (corresponds to a precision of about 10 cm)
				$(':input[name$="breite[]"]', scope).val(lat).change()
				$(':input[name$="laenge[]"]', scope).val(lng).change()
				marker.setLatLng(e.latlng)

		# Add GeoJSON
		$.getJSON(geoJsonSrc).success (data) ->
			style =
				clickable: false
				color: "#000"
				fillColor: "#000"
				weight: 1.5
				opacity: 0.3
				fillOpacity: 0.05
			borders = L.geoJson( data,
				style: style
			)
			borders.addTo map
