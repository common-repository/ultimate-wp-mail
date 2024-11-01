jQuery(function(){ //DOM Ready
	jQuery('.ewd-uwpm-topics-sign-up').on('click', function() {
		jQuery(this).attr('disabled', true);

		var post_categories = [];
		var uwpm_categories = [];
		var wc_categories = [];

		var possible_post_categories = [];
		var possible_uwpm_categories = [];
		var possible_wc_categories = [];

		jQuery(this).parent().find('.ewd-uwpm-si-post-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {post_categories.push(jQuery(this).val());}
		});
		jQuery(this).parent().find('.ewd-uwpm-si-uwpm-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {uwpm_categories.push(jQuery(this).val());}
		});
		jQuery(this).parent().find('.ewd-uwpm-si-wc-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {wc_categories.push(jQuery(this).val());}
		});

		jQuery(this).parent().find('.ewd-uwpm-si-possible-post-category').each(function(index, el) {
			possible_post_categories.push(jQuery(this).val());
		});
		jQuery(this).parent().find('.ewd-uwpm-si-possible-uwpm-category').each(function(index, el) {
			possible_uwpm_categories.push(jQuery(this).val());
		});
		jQuery(this).parent().find('.ewd-uwpm-si-possible-wc-category').each(function(index, el) {
			possible_wc_categories.push(jQuery(this).val());
		});

		var data = 'post_categories=' + post_categories.join() + '&uwpm_categories=' + uwpm_categories.join() + '&wc_categories=' + wc_categories.join() + '&possible_post_categories=' + possible_post_categories.join() + '&possible_uwpm_categories=' + possible_uwpm_categories.join() + '&possible_wc_categories=' + possible_wc_categories.join() + '&action=ewd_uwpm_interests_sign_up';
		jQuery.post( ajaxurl, data, function( response ) {

			jQuery( '.ewd-uwpm-subscription-interests' ).append( '<div class="ewd-uwpm-si-result">Interests have been saved!</div>' );
			jQuery( '.ewd-uwpm-topics-sign-up' ).attr( 'disabled', false );

			setTimeout( function() {
				jQuery( '.ewd-uwpm-si-result' ).fadeOut( '400', function() {
					jQuery(this).remove();
				});
			}, 3000 );
		});
	});
});