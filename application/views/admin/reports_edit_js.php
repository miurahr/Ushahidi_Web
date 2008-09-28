/*
		* Edit Reports Javascript
		*/	
		// Date Picker JS
		$("#incident_date").datepicker({ 
		    showOn: "both", 
		    buttonImage: "<?php echo url::base() ?>media/img/admin/icon-calendar.gif", 
		    buttonImageOnly: true 
		});
	
		function addFormField(div, field, hidden_id, field_type) {
			var id = document.getElementById(hidden_id).value;
			$("#" + div).append("<div class=\"row link-row second\" id=\"" + field + "_" + id + "\"><input type=\"" + field_type + "\" name=\"" + field + "[]\" class=\"" + field_type + " long\" /><a href=\"#\" class=\"add\" onClick=\"addFormField('" + div + "','" + field + "','" + hidden_id + "','" + field_type + "'); return false;\">add</a><a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#" + field + "_" + id + "\"); return false;'>remove</a></div>");

			$("#" + field + "_" + id).effect("highlight", {}, 800);

			id = (id - 1) + 2;
			document.getElementById(hidden_id).value = id;
		}

		function removeFormField(id) {
			var answer = confirm("Are You Sure You Want To Delete This Item?");
		    if (answer){
				$(id).remove();
		    }
			else{
				return false;
		    }
		}
			
		// Map JS
		jQuery(function() {
			var moved=false;
			
			// Now initialise the map
			var options = {
			units: "dd"
			, numZoomLevels: 16
			, controls:[]};
			var map = new OpenLayers.Map('divMap', options);
			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				var map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth");
			}
			else if (default_map == 3)
			{
				var map_layer = new OpenLayers.Layer.Yahoo("yahoo");
			}
			else if (default_map == 4)
			{
				var map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap");
			}
			else
			{
				var map_layer = new OpenLayers.Layer.Google("google");
			}
			
			map.addLayer(map_layer);
			
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.Attribution());
			map.addControl(new OpenLayers.Control.MousePosition());
			
			// Create the markers layer
			var markers = new OpenLayers.Layer.Markers("Markers");
			map.addLayer(markers);
			
			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			
			// create a marker positioned at a lon/lat
			var marker = new OpenLayers.Marker(myPoint);
			markers.addMarker(marker);
			
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);
			
			// Detect Map Clicks
			map.events.register("click", map, function(e){
				var lonlat = map.getLonLatFromViewPortPx(e.xy);
			    m = new OpenLayers.Marker(lonlat);
				markers.clearMarkers();
		    	markers.addMarker(m);
							
				// Update form values (jQuery)
				$("#latitude").attr("value", lonlat.lat);
				$("#longitude").attr("value", lonlat.lon);
			});
			
			$("#findAddress").click(function () {
				var selected = $("#country_id option[@selected]");
				address = $("#location_name").val() + ', ' + selected.text();
				var geocoder = new GClientGeocoder();
				if (geocoder) {
					geocoder.getLatLng(
						address,
						function(point) {
							if (!point) {
								alert(address + " not found!\n\n***************************\nFind a city or town close by and zoom in\nto find your precise location");
							} else {
								var lonlat = new OpenLayers.LonLat(point.lng(), point.lat());
								m = new OpenLayers.Marker(lonlat);
								markers.clearMarkers();
						    	markers.addMarker(m);
								map.setCenter(lonlat, <?php echo $default_zoom; ?>);
								
								// Update form values (jQuery)
								$("#latitude").attr("value", lonlat.lat);
								$("#longitude").attr("value", lonlat.lon);
							}
						}
					);
				}
			});
			
			// Action on Save Only
			$("#save_only").click(function () {
				$("#save").attr("value", "1");
			});
			
			// Prevent Enter Button Submit
			$("#reportForm").bind("keypress", function(e) {
			  if (e.keyCode == 13) return false;
			});

		});			