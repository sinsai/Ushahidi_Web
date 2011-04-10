$(function() {
	// Highlight Selected Categories
	$("input[type=checkbox]").change( function() {
		if ($(this).is(":checked")) {
			$(this).parent().addClass("highlight");
		} else {
			$(this).parent().removeClass("highlight");
		}
	});
	
	// Fill Latitude/Longitude with selected city
	$("#select_city").change(function() {
		var lonlat = $(this).val().split(",");
		if ( lonlat[0] && lonlat[1] )
		{
			$("#latitude").attr("value", lonlat[1]);
			$("#longitude").attr("value", lonlat[0]);
			$("#location_name").attr("value", $('#select_city :selected').text());
		}
	});
	
	$("#category-column-1,#category-column-2").treeview({
        persist: "location",
        collapsed: true,
        unique: false
	});

	if (navigator.geolocation) {
		var setFromGpsButton = $("#set_from_gps");

		setFromGpsButton.show();
		setFromGpsButton.bind("click", function() {
			$(this).val("Loading...");
			navigator.geolocation.getCurrentPosition(function(position) {
				var latitude = position.coords.latitude;
				var longitude = position.coords.longitude;
				$("#latitude").val(latitude);
				$("#longitude").val(longitude);
				var initialLocation = new google.maps.LatLng(latitude, longitude);
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({ "location": initialLocation }, function(results, status) {
					if(status == google.maps.GeocoderStatus.OK) {
						var address = results[0].formatted_address;
						$("#location_name").val(address);
					}
					else {
						alert("Failed to set from GPS: " + status);
					}
					setFromGpsButton.val("Set from GPS");
				});
			}, function(error) {
				alert("Failed to set from GPS: " + error.message);
				setFromGpsButton.val("Set from GPS");
			}, {"maximumAge":600000, "timeout":60000});
		});
	}
});