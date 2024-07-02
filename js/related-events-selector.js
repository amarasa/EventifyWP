jQuery(document).ready(function ($) {
	$("#add-related-event").on("click", function () {
		$("#all-events-list option:selected").each(function () {
			var selectedOption = $(this);
			$("#related-events-list").append(selectedOption);
		});
	});

	$("#remove-related-event").on("click", function () {
		$("#related-events-list option:selected").each(function () {
			var selectedOption = $(this);
			$("#all-events-list").append(selectedOption);
		});
	});

	$("#related-events-search").on("input", function () {
		var searchTerm = $(this).val().toLowerCase();
		$("#all-events-list option").each(function () {
			var optionText = $(this).text().toLowerCase();
			if (optionText.includes(searchTerm)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});
});
