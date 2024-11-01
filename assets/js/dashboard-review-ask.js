jQuery( document ).ready( function( $ ) {

	jQuery( '.ewd-uwpm-main-dashboard-review-ask' ).css( 'display', 'block' );

  jQuery(document).on( 'click', '.ewd-uwpm-main-dashboard-review-ask .notice-dismiss', function( event ) {

  	var params = {
			ask_review_time: '7',
			nonce: ewd_uwpm_review_ask.nonce,
			action: 'ewd_uwpm_hide_review_ask'
		};

		var data = jQuery.param( params );

    jQuery.post( ajaxurl, data, function() {} );
  });

	jQuery( '.ewd-uwpm-review-ask-yes' ).on( 'click', function() {

		jQuery( '.ewd-uwpm-review-ask-feedback-text' ).removeClass( 'ewd-uwpm-hidden' );
		jQuery( '.ewd-uwpm-review-ask-starting-text' ).addClass( 'ewd-uwpm-hidden' );

		jQuery( '.ewd-uwpm-review-ask-no-thanks' ).removeClass( 'ewd-uwpm-hidden' );
		jQuery( '.ewd-uwpm-review-ask-review' ).removeClass( 'ewd-uwpm-hidden' );

		jQuery( '.ewd-uwpm-review-ask-not-really' ).addClass( 'ewd-uwpm-hidden' );
		jQuery( '.ewd-uwpm-review-ask-yes' ).addClass( 'ewd-uwpm-hidden' );

		var params = {
			ask_review_time: '7',
			nonce: ewd_uwpm_review_ask.nonce,
			action: 'ewd_uwpm_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-uwpm-review-ask-not-really' ).on( 'click', function() {

		jQuery( '.ewd-uwpm-review-ask-review-text' ).removeClass( 'ewd-uwpm-hidden' );
		jQuery( '.ewd-uwpm-review-ask-starting-text' ).addClass( 'ewd-uwpm-hidden' );

		jQuery( '.ewd-uwpm-review-ask-feedback-form' ).removeClass( 'ewd-uwpm-hidden' );
		jQuery( '.ewd-uwpm-review-ask-actions' ).addClass( 'ewd-uwpm-hidden' );

		var params = {
			ask_review_time: '1000',
			nonce: ewd_uwpm_review_ask.nonce,
			action: 'ewd_uwpm_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-uwpm-review-ask-no-thanks' ).on( 'click', function() {
		
		var params = {
			ask_review_time: '1000',
			nonce: ewd_uwpm_review_ask.nonce,
			action: 'ewd_uwpm_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );

    jQuery( '.ewd-uwpm-main-dashboard-review-ask' ).css( 'display', 'none' );
	});

	jQuery( '.ewd-uwpm-review-ask-review' ).on( 'click', function() {

		jQuery( '.ewd-uwpm-review-ask-feedback-text' ).addClass( 'ewd-uwpm-hidden' );
		jQuery( '.ewd-uwpm-review-ask-thank-you-text' ).removeClass( 'ewd-uwpm-hidden' );

		var params = {
			ask_review_time: '1000',
			nonce: ewd_uwpm_review_ask.nonce,
			action: 'ewd_uwpm_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-uwpm-review-ask-send-feedback' ).on( 'click', function() {

		var feedback = jQuery( '.ewd-uwpm-review-ask-feedback-explanation textarea' ).val();
		var email_address = jQuery( '.ewd-uwpm-review-ask-feedback-explanation input[name="feedback_email_address"]' ).val();

		var params = {
			feedback: feedback,
			email_address: email_address,
			nonce: ewd_uwpm_review_ask.nonce,
			action: 'ewd_uwpm_send_feedback'
		};

		var data = jQuery.param( params );
    
    jQuery.post( ajaxurl, data, function() {} );

    var params = {
			ask_review_time: '1000',
			nonce: ewd_uwpm_review_ask.nonce,
			action: 'ewd_uwpm_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );

    jQuery( '.ewd-uwpm-review-ask-feedback-form' ).addClass( 'ewd-uwpm-hidden' );
    jQuery( '.ewd-uwpm-review-ask-review-text' ).addClass( 'ewd-uwpm-hidden' );
    jQuery( '.ewd-uwpm-review-ask-thank-you-text' ).removeClass( 'ewd-uwpm-hidden' );
	});
});