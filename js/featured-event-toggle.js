jQuery(document).ready(function ($) {
	$(".toggle-featured-event").on("click", function (e) {
		e.preventDefault();
		var $icon = $(this).find(".dashicons");
		var postId = $(this).data("post-id");

		$.ajax({
			url: featuredEventToggle.ajax_url,
			type: "post",
			data: {
				action: "toggle_featured_event",
				nonce: featuredEventToggle.nonce,
				post_id: postId,
			},
			success: function (response) {
				if (response.success) {
					$icon.toggleClass(
						"dashicons-star-filled dashicons-star-empty"
					);
				}
			},
		});
	});
});
