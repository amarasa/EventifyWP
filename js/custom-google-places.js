jQuery(document).ready(function ($) {
	function initialize() {
		var input = document.getElementById("event_location");
		var autocomplete = new google.maps.places.Autocomplete(input);

		autocomplete.addListener("place_changed", function () {
			var place = autocomplete.getPlace();
			if (!place.geometry) {
				return;
			}

			var mapContainer = $("#event_map");
			var mapOptions = {
				center: place.geometry.location,
				zoom: 15,
			};
			var map = new google.maps.Map(mapContainer[0], mapOptions);
			var marker = new google.maps.Marker({
				position: place.geometry.location,
				map: map,
			});
		});
	}

	google.maps.event.addDomListener(window, "load", initialize);

	$("#event_show_map")
		.change(function () {
			if ($(this).is(":checked")) {
				$("#map-container").show();
				google.maps.event.trigger(window, "resize");
			} else {
				$("#map-container").hide();
			}
		})
		.trigger("change");
});
