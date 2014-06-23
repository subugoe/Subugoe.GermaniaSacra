// TODO: Check if Google license permits embedding

window.google = window.google || {};
google.maps = google.maps || {};
(function() {

  function getScript(src) {
    document.write('<' + 'script src="' + src + '"' +
                   ' type="text/javascript"><' + '/script>');
  }

  var modules = google.maps.modules = {};
  google.maps.__gjsload__ = function(name, text) {
    modules[name] = text;
  };

  google.maps.Load = function(apiLoad) {
    delete google.maps.Load;
    apiLoad([0.009999999776482582,[[["http://mt0.googleapis.com/vt?lyrs=m@264000000\u0026src=api\u0026hl=de\u0026","http://mt1.googleapis.com/vt?lyrs=m@264000000\u0026src=api\u0026hl=de\u0026"],null,null,null,null,"m@264000000",["https://mts0.google.com/vt?lyrs=m@264000000\u0026src=api\u0026hl=de\u0026","https://mts1.google.com/vt?lyrs=m@264000000\u0026src=api\u0026hl=de\u0026"]],[["http://khm0.googleapis.com/kh?v=151\u0026hl=de\u0026","http://khm1.googleapis.com/kh?v=151\u0026hl=de\u0026"],null,null,null,1,"151",["https://khms0.google.com/kh?v=151\u0026hl=de\u0026","https://khms1.google.com/kh?v=151\u0026hl=de\u0026"]],[["http://mt0.googleapis.com/vt?lyrs=h@264000000\u0026src=api\u0026hl=de\u0026","http://mt1.googleapis.com/vt?lyrs=h@264000000\u0026src=api\u0026hl=de\u0026"],null,null,null,null,"h@264000000",["https://mts0.google.com/vt?lyrs=h@264000000\u0026src=api\u0026hl=de\u0026","https://mts1.google.com/vt?lyrs=h@264000000\u0026src=api\u0026hl=de\u0026"]],[["http://mt0.googleapis.com/vt?lyrs=t@132,r@264000000\u0026src=api\u0026hl=de\u0026","http://mt1.googleapis.com/vt?lyrs=t@132,r@264000000\u0026src=api\u0026hl=de\u0026"],null,null,null,null,"t@132,r@264000000",["https://mts0.google.com/vt?lyrs=t@132,r@264000000\u0026src=api\u0026hl=de\u0026","https://mts1.google.com/vt?lyrs=t@132,r@264000000\u0026src=api\u0026hl=de\u0026"]],null,null,[["http://cbk0.googleapis.com/cbk?","http://cbk1.googleapis.com/cbk?"]],[["http://khm0.googleapis.com/kh?v=84\u0026hl=de\u0026","http://khm1.googleapis.com/kh?v=84\u0026hl=de\u0026"],null,null,null,null,"84",["https://khms0.google.com/kh?v=84\u0026hl=de\u0026","https://khms1.google.com/kh?v=84\u0026hl=de\u0026"]],[["http://mt0.googleapis.com/mapslt?hl=de\u0026","http://mt1.googleapis.com/mapslt?hl=de\u0026"]],[["http://mt0.googleapis.com/mapslt/ft?hl=de\u0026","http://mt1.googleapis.com/mapslt/ft?hl=de\u0026"]],[["http://mt0.googleapis.com/vt?hl=de\u0026","http://mt1.googleapis.com/vt?hl=de\u0026"]],[["http://mt0.googleapis.com/mapslt/loom?hl=de\u0026","http://mt1.googleapis.com/mapslt/loom?hl=de\u0026"]],[["https://mts0.googleapis.com/mapslt?hl=de\u0026","https://mts1.googleapis.com/mapslt?hl=de\u0026"]],[["https://mts0.googleapis.com/mapslt/ft?hl=de\u0026","https://mts1.googleapis.com/mapslt/ft?hl=de\u0026"]],[["https://mts0.googleapis.com/mapslt/loom?hl=de\u0026","https://mts1.googleapis.com/mapslt/loom?hl=de\u0026"]]],["de","US",null,0,null,null,"http://maps.gstatic.com/mapfiles/","http://csi.gstatic.com","https://maps.googleapis.com","http://maps.googleapis.com"],["http://maps.gstatic.com/intl/de_ALL/mapfiles/api-3/15/19","3.15.19"],[3675077975],1,null,null,null,null,null,"",null,null,0,"http://khm.googleapis.com/mz?v=151\u0026",null,"https://earthbuilder.googleapis.com","https://earthbuilder.googleapis.com",null,"http://mt.googleapis.com/vt/icon",[["http://mt0.googleapis.com/vt","http://mt1.googleapis.com/vt"],["https://mts0.googleapis.com/vt","https://mts1.googleapis.com/vt"],[null,[[0,"m",264000000]],[null,"de","US",null,18,null,null,null,null,null,null,[[47],[37,[["smartmaps"]]]]],0],[null,[[0,"m",264000000]],[null,"de","US",null,18,null,null,null,null,null,null,[[47],[37,[["smartmaps"]]]]],3],[null,[[0,"m",264000000]],[null,"de","US",null,18,null,null,null,null,null,null,[[50],[37,[["smartmaps"]]]]],0],[null,[[0,"m",264000000]],[null,"de","US",null,18,null,null,null,null,null,null,[[50],[37,[["smartmaps"]]]]],3],[null,[[4,"t",132],[0,"r",132000000]],[null,"de","US",null,18,null,null,null,null,null,null,[[5],[37,[["smartmaps"]]]]],0],[null,[[4,"t",132],[0,"r",132000000]],[null,"de","US",null,18,null,null,null,null,null,null,[[5],[37,[["smartmaps"]]]]],3],[null,null,[null,"de","US",null,18],0],[null,null,[null,"de","US",null,18],3],[null,null,[null,"de","US",null,18],6],[null,null,[null,"de","US",null,18],0],["https://mts0.google.com/vt","https://mts1.google.com/vt"],"/maps/vt"],2,500,["http://geo0.ggpht.com/cbk?cb_client=maps_sv.uv_api_demo","http://www.gstatic.com/landmark/tour","http://www.gstatic.com/landmark/config","/maps/preview/reveal?authuser=0","/maps/preview/log204","/gen204?tbm=map","http://static.panoramio.com.storage.googleapis.com/photos/"]], loadScriptTime);
  };
  var loadScriptTime = (new Date).getTime();
  getScript("http://maps.gstatic.com/intl/de_ALL/mapfiles/api-3/15/19/main.js");
})();

/*
 * L.TileLayer is used for standard xyz-numbered tile layers.
 */
L.Google = L.Class.extend({
	includes: L.Mixin.Events,

	options: {
		minZoom: 0,
		maxZoom: 18,
		tileSize: 256,
		subdomains: 'abc',
		errorTileUrl: '',
		attribution: '',
		opacity: 1,
		continuousWorld: false,
		noWrap: false,
	},

	// Possible types: SATELLITE, ROADMAP, HYBRID
	initialize: function(type, options) {
		L.Util.setOptions(this, options);

		this._type = google.maps.MapTypeId[type || 'SATELLITE'];
	},

	onAdd: function(map, insertAtTheBottom) {
		this._map = map;
		this._insertAtTheBottom = insertAtTheBottom;

		// create a container div for tiles
		this._initContainer();
		this._initMapObject();

		// set up events
		map.on('viewreset', this._resetCallback, this);

		this._limitedUpdate = L.Util.limitExecByInterval(this._update, 150, this);
		map.on('move', this._update, this);
		//map.on('moveend', this._update, this);

		this._reset();
		this._update();
	},

	onRemove: function(map) {
		this._map._container.removeChild(this._container);
		//this._container = null;

		this._map.off('viewreset', this._resetCallback, this);

		this._map.off('move', this._update, this);
		//this._map.off('moveend', this._update, this);
	},

	getAttribution: function() {
		return this.options.attribution;
	},

	setOpacity: function(opacity) {
		this.options.opacity = opacity;
		if (opacity < 1) {
			L.DomUtil.setOpacity(this._container, opacity);
		}
	},

	_initContainer: function() {
		var tilePane = this._map._container
			first = tilePane.firstChild;

		if (!this._container) {
			this._container = L.DomUtil.create('div', 'leaflet-google-layer leaflet-top leaflet-left');
			this._container.id = "_GMapContainer";
		}

		if (true) {
			tilePane.insertBefore(this._container, first);

			this.setOpacity(this.options.opacity);
			var size = this._map.getSize();
			this._container.style.width = size.x + 'px';
			this._container.style.height = size.y + 'px';
		}
	},

	_initMapObject: function() {
		this._google_center = new google.maps.LatLng(0, 0);
		var map = new google.maps.Map(this._container, {
		    center: this._google_center,
		    zoom: 0,
		    mapTypeId: this._type,
		    disableDefaultUI: true,
		    keyboardShortcuts: false,
		    draggable: false,
		    disableDoubleClickZoom: true,
		    scrollwheel: false,
		    streetViewControl: false
		});

		var _this = this;
		this._reposition = google.maps.event.addListenerOnce(map, "center_changed",
			function() { _this.onReposition(); });

		map.backgroundColor = '#ff0000';
		this._google = map;
	},

	_resetCallback: function(e) {
		this._reset(e.hard);
	},

	_reset: function(clearOldContainer) {
		this._initContainer();
	},

	_update: function() {
		this._resize();

		var bounds = this._map.getBounds();
		var ne = bounds.getNorthEast();
		var sw = bounds.getSouthWest();
		var google_bounds = new google.maps.LatLngBounds(
			new google.maps.LatLng(sw.lat, sw.lng),
			new google.maps.LatLng(ne.lat, ne.lng)
		);
		var center = this._map.getCenter();
		var _center = new google.maps.LatLng(center.lat, center.lng);

		this._google.setCenter(_center);
		this._google.setZoom(this._map.getZoom());
		//this._google.fitBounds(google_bounds);
	},

	_resize: function() {
		var size = this._map.getSize();
		if (this._container.style.width == size.x &&
		    this._container.style.height == size.y)
			return;
		this._container.style.width = size.x + 'px';
		this._container.style.height = size.y + 'px';
		google.maps.event.trigger(this._google, "resize");
	},

	onReposition: function() {
		//google.maps.event.trigger(this._google, "resize");
	}
});
