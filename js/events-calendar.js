document.addEventListener("DOMContentLoaded", function () {
	if (typeof FullCalendar === "undefined") {
		console.error("FullCalendar is not loaded");
		return;
	}

	var calendarEl = document.getElementById("events-calendar");
	if (calendarEl) {
		var calendar = new FullCalendar.Calendar(calendarEl, {
			initialView: "dayGridMonth",
			headerToolbar: {
				left: "prev,next today",
				center: "title",
				right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
			},
			events: {
				url: eventsCalendar.ajax_url,
				method: "POST",
				extraParams: {
					action: "fetch_events",
				},
				failure: function () {
					alert("There was an error while fetching events!");
				},
			},
			eventClick: function (info) {
				window.location.href = info.event.url;
				info.jsEvent.preventDefault(); // prevent browser from following the link in the event title
			},
		});

		calendar.render();
	}
});
