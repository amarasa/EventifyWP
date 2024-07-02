function CountdownTracker(label, value, style) {
	var el = document.createElement("span");
	el.className = "flip-clock__piece";

	if (style === "flip") {
		el.innerHTML =
			'<b class="flip-clock__card card"><b class="card__top"></b><b class="card__bottom"></b><b class="card__back"><b class="card__bottom"></b></b></b>' +
			'<span class="flip-clock__slot">' +
			label +
			"</span>";
	} else {
		el.innerHTML =
			'<span class="flip-clock__slot">' +
			label +
			'</span><span class="value">' +
			value +
			"</span>";
	}

	this.el = el;

	if (style === "flip") {
		var top = el.querySelector(".card__top"),
			bottom = el.querySelector(".card__bottom"),
			back = el.querySelector(".card__back"),
			backBottom = el.querySelector(".card__back .card__bottom");

		this.update = function (val) {
			val = ("0" + val).slice(-2);
			if (val !== this.currentValue) {
				if (this.currentValue >= 0) {
					back.setAttribute("data-value", this.currentValue);
					bottom.setAttribute("data-value", this.currentValue);
				}
				this.currentValue = val;
				top.innerText = this.currentValue;
				backBottom.setAttribute("data-value", this.currentValue);

				this.el.classList.remove("flip");
				void this.el.offsetWidth;
				this.el.classList.add("flip");
			}
		};

		this.update(value);
	} else {
		this.update = function (val) {
			val = ("0" + val).slice(-2);
			if (val !== this.currentValue) {
				this.currentValue = val;
				el.querySelector(".value").innerText = this.currentValue;
			}
		};

		this.update(value);
	}
}

function getTimeRemaining(endtime) {
	var t = Date.parse(endtime) - Date.parse(new Date());
	return {
		Total: t,
		Days: Math.floor(t / (1000 * 60 * 60 * 24)),
		Hours: Math.floor((t / (1000 * 60 * 60)) % 24),
		Minutes: Math.floor((t / 1000 / 60) % 60),
		Seconds: Math.floor((t / 1000) % 60),
	};
}

function Clock(countdown, callback, style) {
	countdown = countdown ? new Date(Date.parse(countdown)) : false;
	callback = callback || function () {};

	var updateFn = countdown
		? getTimeRemaining
		: function () {
				return { Total: 0, Days: 0, Hours: 0, Minutes: 0, Seconds: 0 };
		  };

	this.el = document.createElement("div");
	this.el.className = "flip-clock";

	var trackers = {},
		t = updateFn(countdown),
		key,
		timeinterval;

	for (key in t) {
		if (key === "Total") {
			continue;
		}
		trackers[key] = new CountdownTracker(key, t[key], style);
		this.el.appendChild(trackers[key].el);
	}

	var i = 0;
	function updateClock() {
		timeinterval = requestAnimationFrame(updateClock);

		if (i++ % 10) {
			return;
		}

		var t = updateFn(countdown);
		if (t.Total < 0) {
			cancelAnimationFrame(timeinterval);
			for (key in trackers) {
				trackers[key].update(0);
			}
			callback();
			return;
		}

		for (key in trackers) {
			trackers[key].update(t[key]);
		}
	}

	setTimeout(updateClock, 500);
}

document.addEventListener("DOMContentLoaded", function () {
	var countdownElements = document.querySelectorAll(".event-countdown");

	countdownElements.forEach(function (element) {
		var endDate = element.getAttribute("data-end-date");
		var style = element.getAttribute("data-style") || "plain";
		var clock = new Clock(
			new Date(Date.parse(endDate)),
			function () {
				console.log("countdown complete");
			},
			style
		);
		element.appendChild(clock.el);
	});
});
