jQuery(document).ready(function() {
	jQuery('.ewd-uwpm-welcome-screen-box h2').on('click', function() {
		var page = jQuery(this).parent().data('screen');
		EWD_UWPM_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-uwpm-welcome-screen-next-button').on('click', function() {
		var page = jQuery(this).data('nextaction');
		EWD_UWPM_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-uwpm-welcome-screen-previous-button').on('click', function() {
		var page = jQuery(this).data('previousaction');
		EWD_UWPM_Toggle_Welcome_Page(page);
	});
});

function EWD_UWPM_Toggle_Welcome_Page(page) {
	jQuery('.ewd-uwpm-welcome-screen-box').removeClass('ewd-uwpm-welcome-screen-open');
	jQuery('.ewd-uwpm-welcome-screen-' + page).addClass('ewd-uwpm-welcome-screen-open');
}