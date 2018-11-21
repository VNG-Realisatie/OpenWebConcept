var map, mapMarker, markerLocation;

$(function(){

	var GeoSearchControl 		= window.GeoSearch.GeoSearchControl;
	var OpenStreetMapProvider 	= window.GeoSearch.OpenStreetMapProvider;
	var provider 				= new OpenStreetMapProvider();
	var searchControl 			= new GeoSearchControl({
		style: 'bar',
		provider: provider,
		showPopup: false,
		autoClose: true,
		searchLabel: 'Vul je postcode of straatnaam in',
	});
		
	// Initialize the map
	map = L.map('leaflet_map').on('load', function(){
		
		// Set to current location
		if( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition(zoomLeafletToCurrentLocation);
		}

	});

	map.setView([51.89, 5.46], 7);
	map.addControl(searchControl);
	L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
    	maxZoom: 18
	}).addTo(map);

	map.on('click', addMarker);
	map.on('geosearch/showlocation', addAddressMarker);

	$('.set_leaflet_current_location').on('click', function(e){

		e.preventDefault();

		if( navigator.geolocation ) {
		
			navigator.geolocation.getCurrentPosition(setLeafletToCurrentLocation);
		
		}

	});

	function addMarker(e){

		// Check the target
		if( $(e.target._container).hasClass('leaflet-touch-drag') ) {

			// Set the market location
            setMarker(e.latlng);

		}

	}

	function addAddressMarker(e) {

		var latlng = {
			lat: e.location.raw.lat,
			lng: e.location.raw.lon
		}

		setMarker(latlng);
	}

	function reloadLeafletMap() {
		console.log(map);
	}

	function setMarker(latlng, zoomTo) {

		var zoomTo = zoomTo || false;

		// Delete address markers
		$.each(searchControl.markers._layers, function( index, value ) {
			map.removeLayer(value);
		});

		// Check if there is a marker set, else create one
		if( typeof mapMarker == 'undefined' ) {
			
			// Add marker to map
			mapMarker = new L.marker(latlng).addTo(map);

		} else {

			// Change marker location
			mapMarker.setLatLng(latlng);

		}

		if( typeof latlng.lat !== 'undefined' && typeof latlng.lng !== 'undefined' ) {
			
			$('#input--location_lat').val(latlng.lat);
			$('#input--location_lng').val(latlng.lng);

			// Set the display address name
			setAddressDisplayName(latlng.lat, latlng.lng)

		} else {
			$('#input--location_lat, #input--location_lng').val('');
		}

		// Check if we need to zoom to the marker
		if( zoomTo ) {
			var latLngs = [ mapMarker.getLatLng() ];

			var markerBounds = L.latLngBounds(latLngs);
  			map.fitBounds(markerBounds);

		}

	}

	function setLeafletToCurrentLocation( position ) {

		var latlng = {
			lat: position.coords.latitude,
			lng: position.coords.longitude
		}
		setMarker(latlng, true);

	}

	function zoomLeafletToCurrentLocation( position ) {

		var marker = L.marker([position.coords.latitude, position.coords.longitude],{});
		var latLngs = [ marker.getLatLng() ];

		var markerBounds = L.latLngBounds(latLngs);
  		map.fitBounds(markerBounds);

	}
	
	function setAddressDisplayName(lat, lng) {
		
		$('.address--display_name').hide();

		$.ajax({
			type: "GET",
			url: "https://nominatim.openstreetmap.org/search.php",
			dataType: 'json',
			data: {
				q: lat + "," + lng,
				format: 'json',
				addressdetails: 1
			},
			success: function(data){

				if( typeof data[0] !== 'undefined' ) {
					
					var $nice_address = getNiceAddressDisplayName(data[0].address);
					$('#intro--address_name').html(data[0].display_name);
					$('.address--display_name').show();

				}

			}
		});

	}

	function getNiceAddressDisplayName(address_object) {

	}

});