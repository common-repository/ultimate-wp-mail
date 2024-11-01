jQuery(document).ready(function() {
	jQuery('.ewd-uwpm-element-type-select').on('change', function() {
		var type = jQuery(this).val();

		jQuery('.ewd-uwpm-elements-container').addClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-elements-container[data-type="' + type + '"]').removeClass('ewd-uwpm-hidden');
	});

	jQuery('.ewd-uwpm-column-type').on('click', function() {
		jQuery('.ewd-uwpm-email-templates').addClass('ewd-uwpm-hidden');

		var type = jQuery(this).data('type');
		var display_HTML = EWD_UWPM_Get_Display_HTML(type);
		var current_display = jQuery('#ewd-uwpm-visual-builder-area').html();

		jQuery('#ewd-uwpm-visual-builder-area').html(current_display + display_HTML);

		var save_HTML = jQuery('#ewd-uwpm-visual-builder-area').html();
		jQuery('#ewd-uwpm-email-input textarea').val(save_HTML);

		EWD_UWPM_Section_Editor_Click_Handlers();
		EWD_UWPM_Enable_Sortable();
		EWD_UWPM_Enable_Delete_Section();
	});

	jQuery('.ewd-uwpm-full-screen').on('click', function() {
		jQuery('#ewd-uwpm-build-email .inside').addClass('ewd-uwpm-full-screen-container');
		jQuery(this).addClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-exit-full-screen').removeClass('ewd-uwpm-hidden');
		jQuery('body').addClass('ewd-uwpm-full-screen-body-overflow');
	});

	jQuery('.ewd-uwpm-exit-full-screen').on('click', function() {
		jQuery('#ewd-uwpm-build-email .inside').removeClass('ewd-uwpm-full-screen-container');
		jQuery(this).addClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-full-screen').removeClass('ewd-uwpm-hidden');
		jQuery('body').removeClass('ewd-uwpm-full-screen-body-overflow');
	});

	jQuery('#ewd-uwpm-section-editor-save-button').on('click', function() {
		EWD_UWPM_Save_Section_Editor();

		jQuery('#ewd-uwpm-section-editor').addClass('ewd-uwpm-hidden').removeClass('ewd-uwpm-split-screen');
		jQuery('#ewd-uwpm-email-styling-options').removeClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-visual-builder-area').removeClass('ewd-uwpm-split-screen');

		var save_HTML = jQuery('#ewd-uwpm-visual-builder-area').html();
		jQuery('#ewd-uwpm-email-input textarea').val(save_HTML);
	});

	jQuery('#ewd-uwpm-plain-text-toggle').on('click', function() {
		jQuery('#ewd-uwpm-plain-text-version').toggleClass('ewd-uwpm-hidden');
	});

//	jQuery('.ewd-uwpm-selectable-element').on('click', function() {
//		var type = jQuery(this).data('type');
//		var name = jQuery(this).data('name');
//		var label = jQuery(this).html();
//
//		var display_HTML = EWD_UWPM_Get_Display_HTML(type, name, label);
//
//		var current_display = jQuery('#ewd-uwpm-visual-builder-area').html();
//
//		var new_display = current_display + display_HTML;
//
//		jQuery('#ewd-uwpm-visual-builder-area').html(new_display);
//	});

	jQuery( '#ewd-uwpm-visual-builder-area' ).sortable({
		items: '.ewd-uwpm-section-container',
		handle: '.ewd-uwpm-section-handle',
		cursor: 'move',
		axis: 'y'
	});

	jQuery('#post-preview').off('click');
	jQuery('#post-preview').on('click', function(event) {
		event.preventDefault();

		jQuery('#ewd-uwpm-ajax-email-preview-body').html('Loading email...');

		jQuery('#ewd-uwpm-ajax-email-preview').removeClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-email-preview-overlay').removeClass('ewd-uwpm-hidden');

		var email_content = jQuery('#ewd-uwpm-visual-builder-area').html();
		var email_id = jQuery('#ewd-uwpm-email-id').val();

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&email_content=' + encodeURIComponent( email_content ) + '&email_id=' + email_id + '&action=ewd_uwpm_ajax_preview_email';
		jQuery.post(ajaxurl, data, function(response) {
			console.log(response);

			jQuery('#ewd-uwpm-ajax-email-preview-body').html(response);
		});
	});

	jQuery('#ewd-uwpm-ajax-email-preview-exit, #ewd-uwpm-email-preview-overlay').on('click', function() {
		jQuery('#ewd-uwpm-ajax-email-preview').addClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-email-preview-overlay').addClass('ewd-uwpm-hidden');
	});

	jQuery('#ewd-uwpm-send-test-button').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-test-email').removeClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-send-test-email-overlay').removeClass('ewd-uwpm-hidden');
	});

	jQuery('#ewd-uwpm-email-all-users-button').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-email-all-users').removeClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-send-test-email-overlay').removeClass('ewd-uwpm-hidden');
	});

	jQuery('#ewd-uwpm-send-test-email-close, #ewd-uwpm-email-all-users-close, #ewd-uwpm-email-all-users-cancel, #ewd-uwpm-send-test-email-overlay').on('click', function() {
		jQuery('#ewd-uwpm-send-test-email').addClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-email-all-users').addClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-send-test-email-overlay').addClass('ewd-uwpm-hidden');
	});

	jQuery('#ewd-uwpm-send-test').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-reponse-message').html('Sending email...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var email_address = jQuery('input[name="EWD_UWPM_Test_Email_Address"]').val();
		var email_title = jQuery('#title').val();
		var email_content = jQuery('#ewd-uwpm-visual-builder-area').html();
		var email_id = jQuery('#ewd-uwpm-email-id').val();

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&email_address=' + email_address + '&email_title=' + email_title + '&email_content=' + encodeURIComponent( email_content ) + '&email_id=' + email_id + '&action=ewd_uwpm_send_test_email';
		jQuery.post(ajaxurl, data, function(response) {
			console.log(response);

			jQuery('#ewd-uwpm-send-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 4000);

			jQuery('#ewd-uwpm-send-test-email-overlay').addClass('ewd-uwpm-hidden');
			jQuery('#ewd-uwpm-send-test-email').addClass('ewd-uwpm-hidden');
		});
	});

	jQuery('#ewd-uwpm-email-all').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-reponse-message').html('Sending email...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var email_id = jQuery('#ewd-uwpm-email-id').val();
		var email_title = jQuery('#title').val();
		var email_content = jQuery('#ewd-uwpm-visual-builder-area').html();

		if (jQuery('.ewd-uwpm-delay-send-toggle').val() == 'now') {var send_time = 'now';}
		else {var send_time = jQuery('#ewd-uwpm-send-datetime').val();}

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&email_id=' + email_id + '&email_title=' + email_title + '&email_content=' + encodeURIComponent( email_content ) + '&send_time=' + send_time + '&action=ewd_uwpm_email_all_users';
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#ewd-uwpm-send-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 4000);

			jQuery('#ewd-uwpm-send-test-email-overlay').addClass('ewd-uwpm-hidden');
			jQuery('#ewd-uwpm-email-all-users').addClass('ewd-uwpm-hidden');

		});
	});

	jQuery('#ewd-uwpm-email-specific-user').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-reponse-message').html('Sending email...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var email_id = jQuery('#ewd-uwpm-email-id').val();
		var email_title = jQuery('#title').val();
		var email_content = jQuery('#ewd-uwpm-visual-builder-area').html();
		var user_id = jQuery('#ewd-uwpm-email-user-select').val();

		if (jQuery('.ewd-uwpm-delay-send-toggle').val() == 'now') {var send_time = 'now';}
		else {var send_time = jQuery('#ewd-uwpm-send-datetime').val();}

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&email_id=' + email_id + '&email_title=' + email_title + '&email_content=' + encodeURIComponent(email_content) + '&user_id=' + user_id + '&send_time=' + send_time + '&action=ewd_uwpm_email_specific_user';
		jQuery.post(ajaxurl, data, function(response) {console.log(response);
			jQuery('#ewd-uwpm-send-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 4000);
		});
	});

	jQuery('#ewd-uwpm-email-user-list').on('click', function(event) {
		event.preventDefault();

		if (jQuery('#ewd-uwpm-email-list-select').val() == -1) {
			jQuery('.ewd-uwpm-auto-list-overlay').removeClass('ewd-uwpm-hidden');
			jQuery('.ewd-uwpm-auto-list-options').removeClass('ewd-uwpm-hidden');

			return;
		}

		jQuery('#ewd-uwpm-send-reponse-message').html('Sending email...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var email_id = jQuery('#ewd-uwpm-email-id').val();
		var email_title = jQuery('#title').val();
		var email_content = jQuery('#ewd-uwpm-visual-builder-area').html();
		var list_id = jQuery('#ewd-uwpm-email-list-select').val();

		if (jQuery('.ewd-uwpm-delay-send-toggle').val() == 'now') {var send_time = 'now';}
		else {var send_time = jQuery('#ewd-uwpm-send-datetime').val();}

		var post_categories = [];
		var uwpm_categories = [];
		var wc_categories = [];

		jQuery('.ewd-uwpm-al-post-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {post_categories.push(jQuery(this).val());}
		});
		jQuery('.ewd-uwpm-al-uwpm-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {uwpm_categories.push(jQuery(this).val());}
		});
		jQuery('.ewd-uwpm-al-wc-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {wc_categories.push(jQuery(this).val());}
		});

		var previous_purchasers = jQuery('.ewd-uwpm-al-wc-previous-purchasers').is(':checked');
		var product_purchasers = jQuery('.ewd-uwpm-al-wc-previous-products').is(':checked');
		var previous_wc_products = jQuery('.ewd-uwpm-al-wc-products').val();
		var category_purchasers = jQuery('.ewd-uwpm-al-wc-previous-categories').is(':checked');
		var previous_wc_categories = jQuery('.ewd-uwpm-al-wc-categories').val();

		if (!jQuery.isArray(previous_wc_products)) {previous_wc_products = [];}
		if (!jQuery.isArray(previous_wc_categories)) {previous_wc_categories = [];}

		jQuery('.ewd-uwpm-al-post-category').attr('checked', false);
		jQuery('.ewd-uwpm-al-uwpm-category').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-category').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-previous-purchasers').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-previous-products').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-previous-categories').attr('checked', false);

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&email_id=' + email_id + '&email_title=' + email_title + '&email_content=' + encodeURIComponent( email_content ) + '&list_id=' + list_id + '&send_time=' + send_time + '&post_categories=' + post_categories.join() + '&uwpm_categories=' + uwpm_categories.join() + '&wc_categories=' + wc_categories.join() + '&previous_purchasers=' + previous_purchasers + '&product_purchasers=' + product_purchasers + '&previous_wc_products=' + previous_wc_products.join() + '&category_purchasers=' + category_purchasers + '&previous_wc_categories=' + previous_wc_categories.join() + '&action=ewd_uwpm_email_user_list';
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#ewd-uwpm-send-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 3000);
		});

		jQuery('#ewd-uwpm-email-list-select option[value=-2]').remove();
	}); 

	/* SMS */
	jQuery('#ewd-uwpm-send-test-sms-button').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-test-sms-container').removeClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-send-test-sms-overlay').removeClass('ewd-uwpm-hidden');
	});

	jQuery('#ewd-uwpm-send-test-sms-close, #ewd-uwpm-send-test-sms-overlay').on('click', function() {
		jQuery('#ewd-uwpm-send-test-sms-container').addClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-send-test-sms-overlay').addClass('ewd-uwpm-hidden');
	});

	jQuery('#ewd-uwpm-send-test-sms').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-sms-reponse-message').html('Sending SMS...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var phone_number = jQuery('input[name="EWD_UWPM_Test_Phone_Number"]').val();
		var message_content = jQuery('textarea[name="ewd_uwpm_sms_content"]').val();
		var message_id = jQuery( 'input[name="post_ID"]' ).length ? jQuery( 'input[name="post_ID"]' ).val() : 0;

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&phone_number=' + phone_number + '&message_content=' + message_content + '&message_id=' + message_id + '&action=ewd_uwpm_send_test_sms';
		jQuery.post(ajaxurl, data, function(response) {
			console.log(response);

			jQuery('#ewd-uwpm-send-sms-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-sms-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 4000);

			jQuery('#ewd-uwpm-send-test-sms-overlay').addClass('ewd-uwpm-hidden');
			jQuery('#ewd-uwpm-send-test-sms-container').addClass('ewd-uwpm-hidden');
		});
	});

	jQuery('#ewd-uwpm-sms-all').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-sms-reponse-message').html('Sending SMS...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var message_id = jQuery( 'input[name="post_ID"]' ).length ? jQuery( 'input[name="post_ID"]' ).val() : 0;
		var message_content = jQuery('textarea[name="ewd_uwpm_sms_content"]').val();

		if (jQuery('.ewd-uwpm-delay-send-toggle').val() == 'now') {var send_time = 'now';}
		else {var send_time = jQuery('#ewd-uwpm-send-datetime').val();}

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&message_id=' + message_id + '&message_content=' + message_content + '&send_time=' + send_time + '&action=ewd_uwpm_sms_all_users';
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#ewd-uwpm-send-sms-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-sms-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 4000);
		});
	});

	jQuery('#ewd-uwpm-sms-specific-user').on('click', function(event) {
		event.preventDefault();
		jQuery('#ewd-uwpm-send-sms-reponse-message').html('Sending SMS...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var message_id = jQuery( 'input[name="post_ID"]' ).length ? jQuery( 'input[name="post_ID"]' ).val() : 0;
		var message_content = jQuery('textarea[name="ewd_uwpm_sms_content"]').val();
		var user_id = jQuery('#ewd-uwpm-sms-user-select').val();

		if (jQuery('.ewd-uwpm-delay-send-toggle').val() == 'now') {var send_time = 'now';}
		else {var send_time = jQuery('#ewd-uwpm-send-datetime').val();}

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&message_id=' + message_id + '&message_content=' + message_content + '&user_id=' + user_id + '&send_time=' + send_time + '&action=ewd_uwpm_sms_specific_user';
		jQuery.post(ajaxurl, data, function(response) {console.log(response);
			jQuery('#ewd-uwpm-send-sms-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-sms-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 4000);
		});
	});

	jQuery('#ewd-uwpm-sms-user-list').on('click', function(event) {
		event.preventDefault();

		if (jQuery('#ewd-uwpm-sms-list-select').val() == -1) {
			jQuery('.ewd-uwpm-auto-list-overlay').removeClass('ewd-uwpm-hidden');
			jQuery('.ewd-uwpm-auto-list-options').removeClass('ewd-uwpm-hidden');

			return;
		}

		jQuery('#ewd-uwpm-send-sms-reponse-message').html('Sending SMS...').removeClass('ewd-uwpm-hidden').css('display', 'block');

		var message_id = jQuery( 'input[name="post_ID"]' ).length ? jQuery( 'input[name="post_ID"]' ).val() : 0;
		var message_content = jQuery('textarea[name="ewd_uwpm_sms_content"]').val();
		var list_id = jQuery('#ewd-uwpm-sms-list-select').val();

		if (jQuery('.ewd-uwpm-delay-send-toggle').val() == 'now') {var send_time = 'now';}
		else {var send_time = jQuery('#ewd-uwpm-send-datetime').val();}

		var post_categories = [];
		var uwpm_categories = [];
		var wc_categories = [];

		jQuery('.ewd-uwpm-al-post-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {post_categories.push(jQuery(this).val());}
		});
		jQuery('.ewd-uwpm-al-uwpm-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {uwpm_categories.push(jQuery(this).val());}
		});
		jQuery('.ewd-uwpm-al-wc-category').each(function(index, el) {
			if (jQuery(this).is(':checked')) {wc_categories.push(jQuery(this).val());}
		});

		var previous_purchasers = jQuery('.ewd-uwpm-al-wc-previous-purchasers').is(':checked');
		var product_purchasers = jQuery('.ewd-uwpm-al-wc-previous-products').is(':checked');
		var previous_wc_products = jQuery('.ewd-uwpm-al-wc-products').val();
		var category_purchasers = jQuery('.ewd-uwpm-al-wc-previous-categories').is(':checked');
		var previous_wc_categories = jQuery('.ewd-uwpm-al-wc-categories').val();

		if (!jQuery.isArray(previous_wc_products)) {previous_wc_products = [];}
		if (!jQuery.isArray(previous_wc_categories)) {previous_wc_categories = [];}

		jQuery('.ewd-uwpm-al-post-category').attr('checked', false);
		jQuery('.ewd-uwpm-al-uwpm-category').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-category').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-previous-purchasers').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-previous-products').attr('checked', false);
		jQuery('.ewd-uwpm-al-wc-previous-categories').attr('checked', false);

		var data = 'nonce=' + ewd_uwpm_admin_php_data.nonce + '&message_id=' + message_id + '&message_content=' + message_content + '&list_id=' + list_id + '&send_time=' + send_time + '&post_categories=' + post_categories.join() + '&uwpm_categories=' + uwpm_categories.join() + '&wc_categories=' + wc_categories.join() + '&previous_purchasers=' + previous_purchasers + '&product_purchasers=' + product_purchasers + '&previous_wc_products=' + previous_wc_products.join() + '&category_purchasers=' + category_purchasers + '&previous_wc_categories=' + previous_wc_categories.join() + '&action=ewd_uwpm_sms_user_list';
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#ewd-uwpm-send-sms-reponse-message').html(response);

			setTimeout(function() {
				jQuery('#ewd-uwpm-send-sms-reponse-message').fadeOut(400).addClass('ewd-uwpm-hidden');
			}, 3000);
		});

		jQuery('#ewd-uwpm-email-list-select option[value=-2]').remove();
	}); 

	/* Auto-lists */
	jQuery('.ewd-uwpm-submit-al').on('click', function(event) {
		event.preventDefault();

		jQuery('.ewd-uwpm-auto-list-overlay').addClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-auto-list-options').addClass('ewd-uwpm-hidden');

		jQuery('#ewd-uwpm-email-list-select').append('<option value="-2">AL Option</option>');
		jQuery('#ewd-uwpm-email-list-select').val(-2);
		jQuery('#ewd-uwpm-email-user-list').trigger('click');
	});

	jQuery('.ewd-uwpm-submit-sms-al').on('click', function(event) {
		event.preventDefault();

		jQuery('.ewd-uwpm-auto-list-overlay').addClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-auto-list-options').addClass('ewd-uwpm-hidden');

		jQuery('#ewd-uwpm-sms-list-select').append('<option value="-2">AL Option</option>');
		jQuery('#ewd-uwpm-sms-list-select').val(-2);
		jQuery('#ewd-uwpm-sms-user-list').trigger('click');
	});

	jQuery('.ewd-uwpm-auto-list-overlay').on('click', function() {
		jQuery('.ewd-uwpm-auto-list-overlay').addClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-auto-list-options').addClass('ewd-uwpm-hidden');
	});

	jQuery('.ewd-uwpm-al-interests').on('click', function() {
		jQuery('.ewd-uwpm-al-interests').addClass('ewd-uwpm-auto-list-tab-active');
		jQuery('.ewd-uwpm-al-wc').removeClass('ewd-uwpm-auto-list-tab-active');
		jQuery('.ewd-uwpm-al-interest-groups').removeClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-al-woocommerce-lists').addClass('ewd-uwpm-hidden');
	});

	jQuery('.ewd-uwpm-al-wc').on('click', function() {
		jQuery('.ewd-uwpm-al-interests').removeClass('ewd-uwpm-auto-list-tab-active');
		jQuery('.ewd-uwpm-al-wc').addClass('ewd-uwpm-auto-list-tab-active');
		jQuery('.ewd-uwpm-al-interest-groups').addClass('ewd-uwpm-hidden');
		jQuery('.ewd-uwpm-al-woocommerce-lists').removeClass('ewd-uwpm-hidden');
	}); 

	jQuery('.ewd-uwpm-delay-send-toggle').on('change', function() {
		if (jQuery('.ewd-uwpm-delay-send-toggle').val() == 'now') {jQuery('#ewd-uwpm-send-datetime').addClass('ewd-uwpm-hidden');}
		else {jQuery('#ewd-uwpm-send-datetime').removeClass('ewd-uwpm-hidden');}
	});

	jQuery('#ewd-uwpm-send-reponse-overlay').on('click', function() {
		jQuery(this).addClass('ewd-uwpm-hidden');
		jQuery('#ewd-uwpm-send-reponse-message').addClass('ewd-uwpm-hidden');
	}); 

	jQuery( document ).on('click', '.ewd-uwpm-email-log-details' , function(ev) {

		var params = {
			action: 'ewd_uwpm_get_email_log_details',
			post_id: jQuery( this ).data( 'post_id' )
		};

		var data = jQuery.param( params );
		
		jQuery.post(ajaxurl, data, function( response ) {

			if ( ! response.success ) { return; }

			jQuery( 'body' ).append( response.data.output );
		});
	} );

	jQuery( document ).on('click', '.ewd-uwpm-email-log-details-modal-background' , function(ev) {

		jQuery( '.ewd-uwpm-email-log-details-modal-background, .ewd-uwpm-email-log-details-modal' ).remove();
	} );

	EWD_UWPM_Section_Editor_Click_Handlers();
	EWD_UWPM_Enable_Sortable();
	EWD_UWPM_Form_Templates();
	EWD_UWPM_Enable_Delete_Section();
});

function EWD_UWPM_Get_Display_HTML(type, section_count, section_one_content, section_two_content, section_three_content, section_four_content) {
	if (typeof section_count === 'undefined' || section_count === null) {section_count = jQuery('#ewd-uwpm-template-information').data('sectioncount');}

	if (typeof section_one_content === 'undefined' || section_one_content === null) {section_one_content = '';}
	if (typeof section_two_content === 'undefined' || section_two_content === null) {section_two_content = '';}
	if (typeof section_three_content === 'undefined' || section_three_content === null) {section_three_content = '';}
	if (typeof section_four_content === 'undefined' || section_four_content === null) {section_four_content = '';}

	if (type == 1) {
		var display_HTML = '<div class="ewd-uwpm-section-container">';
		display_HTML += '<div class="ewd-uwpm-section-handle dashicons dashicons-leftright"></div>';
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-1" data-sectioncount="' + section_count + '">' + section_one_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div class="ewd-uwpm-delete dashicons dashicons-no" data-section="' + section_count + '"></div>';
		display_HTML += '<div class="ewd-uwpm-clear"></div>';
		display_HTML += '</div>';
	}
	else if (type == 2) {
		var display_HTML = '<div class="ewd-uwpm-section-container">';
		display_HTML += '<div class="ewd-uwpm-section-handle dashicons dashicons-leftright"></div>';
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-2" data-sectioncount="' + section_count + '">' + section_one_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-2" data-sectioncount="' + section_count + '">' + section_two_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div class="ewd-uwpm-delete dashicons dashicons-no" data-section="' + section_count + '"></div>';
		display_HTML += '<div class="ewd-uwpm-clear"></div>';
		display_HTML += '</div>';
	}
	else if (type == 3) {
		var display_HTML = '<div class="ewd-uwpm-section-container">';
		display_HTML += '<div class="ewd-uwpm-section-handle dashicons dashicons-leftright"></div>';
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-3" data-sectioncount="' + section_count + '">' + section_one_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-3" data-sectioncount="' + section_count + '">' + section_two_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-3" data-sectioncount="' + section_count + '">' + section_three_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div class="ewd-uwpm-delete dashicons dashicons-no" data-section="' + section_count + '"></div>';
		display_HTML += '<div class="ewd-uwpm-clear"></div>';
		display_HTML += '</div>';
	}
	else if (type == 4) {
		var display_HTML = '<div class="ewd-uwpm-section-container">';
		display_HTML += '<div class="ewd-uwpm-section-handle dashicons dashicons-leftright"></div>';
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-4" data-sectioncount="' + section_count + '">' + section_one_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-4" data-sectioncount="' + section_count + '">' + section_two_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-4" data-sectioncount="' + section_count + '">' + section_three_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-4" data-sectioncount="' + section_count + '">' + section_four_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div class="ewd-uwpm-delete dashicons dashicons-no" data-section="' + section_count + '"></div>';
		display_HTML += '<div class="ewd-uwpm-clear"></div>';
		display_HTML += '</div>';
	}
	else if (type == '1-2') {
		var display_HTML = '<div class="ewd-uwpm-section-container">';
		display_HTML += '<div class="ewd-uwpm-section-handle dashicons dashicons-leftright"></div>';
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-1-3" data-sectioncount="' + section_count + '">' + section_one_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-2-3" data-sectioncount="' + section_count + '">' + section_two_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div class="ewd-uwpm-delete dashicons dashicons-no" data-section="' + section_count + '"></div>';
		display_HTML += '<div class="ewd-uwpm-clear"></div>';
		display_HTML += '</div>';
	}
	else if (type == '2-1') {
		var display_HTML = '<div class="ewd-uwpm-section-container">';
		display_HTML += '<div class="ewd-uwpm-section-handle dashicons dashicons-leftright"></div>';
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-2-3" data-sectioncount="' + section_count + '">' + section_one_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div id="ewd-uwpm-section-' + section_count + '" class="ewd-uwpm-section width-1-3" data-sectioncount="' + section_count + '">' + section_two_content + '<div class="ewd-uwpm-edit dashicons dashicons-edit" data-section="' + section_count + '"></div></div>'; section_count++;
		display_HTML += '<div class="ewd-uwpm-delete dashicons dashicons-no" data-section="' + section_count + '"></div>';
		display_HTML += '<div class="ewd-uwpm-clear"></div>';
		display_HTML += '</div>';
	}

	jQuery('#ewd-uwpm-template-information').attr('data-sectioncount', section_count);
	
	return display_HTML;
}

function EWD_UWPM_Form_Templates() {
	jQuery('.ewd-uwpm-template').on('click', function() {
		jQuery('.ewd-uwpm-email-templates').addClass('ewd-uwpm-hidden');
		var Email_Type = jQuery(this).data('template');

		var Elements = EWD_UWPM_Get_Template_Elements(Email_Type);
		jQuery(Elements).each(function(index, el) {
			jQuery('#ewd-uwpm-visual-builder-area').append(EWD_UWPM_Get_Display_HTML(el.type, el.count, el.cont_one, el.cont_two, el.cont_three, el.cont_four));
		});

		EWD_UWPM_Section_Editor_Click_Handlers();
		EWD_UWPM_Enable_Sortable();
		EWD_UWPM_Enable_Delete_Section();

		var save_HTML = jQuery('#ewd-uwpm-visual-builder-area').html();
		jQuery('#ewd-uwpm-email-input textarea').val(save_HTML);
	});
}

function EWD_UWPM_Get_Template_Elements(Form_Type) {
	var plugin_link = jQuery('.ewd-uwpm-email-templates').data('pluginlink');
	switch(Form_Type) {
		case 'newsletter':
			var Elements = [
				{type: 1, count: 0, cont_one: '<p style="text-align: center;"><img class="alignnone size-medium" src="' + plugin_link + '/assets/img/Logo_Image.png" alt="" width="300" height="135" /></a></p>'},
				{type: 1, count: 1, cont_one: '<h1 style="text-align: center;">Newsletter Title</h1><h4 style="text-align: center;">Subtitle to Tell Subscribers the Key Points in your Newsletter</h4>'},
				{type: 1, count: 2, cont_one: '<p style="text-align: center;"><img class="alignnone size-large" src="' + plugin_link + '/assets/img/Banner_Image.png" alt="" width="840" height="588" /></a></p>'},
				{type: 2, count: 3, cont_one: '<h4 style="text-align: left;">Story Section One</h4><p>Here\'s the first story. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quam mauris, euismod ut turpis vel, finibus tristique nisi. Duis ullamcorper turpis tortor, quis venenatis dolor fringilla egestas.</p>', cont_two: '<h4 style="text-align: left;">Story Section Two</h4><p>Here\'s the second story. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quam mauris, euismod ut turpis vel, finibus tristique nisi. Duis ullamcorper turpis tortor, quis venenatis dolor fringilla egestas.</p>'}
			];
			break;
		case 'product_showcase':
			var Elements = [
				{type: 1, count: 0, cont_one: '<p style="text-align: center;"><img class="alignnone size-medium" src="' + plugin_link + '/assets/img/Logo_Image.png" alt="" width="300" height="135" /></a></p><h1 style="text-align: center;">Awesome Products Below!</h1>'},
				{type: 1, count: 1, cont_one: '<p style="text-align: center;"><img class="alignnone size-large" src="' + plugin_link + '/assets/img/Product_Image.png" alt="" width="840" height="588" /></a></p>'},
				{type: 1, count: 2, cont_one: '<h4 style="text-align: left;">Feature your all-star products!</h4><p>Here\'s the description of your collection. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quam mauris, euismod ut turpis vel, finibus tristique nisi. Duis ullamcorper turpis tortor, quis venenatis dolor fringilla egestas.</p>'},
				{type: 1, count: 3, cont_one: '<h3 style="text-align: center;"><a href="https://www.etoilewebdesign.com">Link to your collection!</a></h3>'}
			];
			break;
		case 'thank_you':
			var Elements = [
				{type: 1, count: 0, cont_one: '<p style="text-align: center;"><img class="alignnone size-medium" src="' + plugin_link + '/assets/img/Logo_Image.png" alt="" width="300" height="135" /></a></p><h1 style="text-align: center;">Thank You!</h1>'},
				{type: 1, count: 1, cont_one: '<p style="text-align: center;"><img class="alignnone size-large" src="' + plugin_link + '/assets/img/Banner_Image.png" alt="" width="840" height="588" /></a></p>'},
				{type: '2-1', count: 2, cont_one: '<h4 style="text-align: left;">Loyal Customer</h4><p>We wanted to thank your for your recent purchase, and also let you know about some other products that we have that you might be interested in. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quam mauris, euismod ut turpis vel, finibus tristique nisi. Duis ullamcorper turpis tortor, quis venenatis dolor fringilla egestas.</p>', cont_two: '<h4>Awesome Product</h4><p><a href="http://www.etoilewebdesign.com/plugins/ultimate-product-catalog/">Product One</a><br/><a href="http://www.etoilewebdesign.com/plugins/ultimate-faq/">Product Two</a></p>'},
				{type: 1, count: 4, cont_one: '<h3 style="text-align: center;">Your friendly suppliers,</h3><p style="text-align: center;">Awesome company name</p>'}
			];
			break;
		case 'promotion':
			var Elements = [
				{type: 1, count: 0, cont_one: '<p style="text-align: center;"><img class="alignnone size-medium" src="' + plugin_link + '/assets/img/Logo_Image.png" alt="" width="300" height="135" /></a></p>'},
				{type: 1, count: 1, cont_one: '<h4 style="text-align: center;">Been waiting for the perfect opportunity to buy our products?</h4><h1 style="text-align: center;">Gigantic Sale</h1>'},
				{type: 1, count: 2, cont_one: '<p style="text-align: center;"><img class="alignnone size-large" src="' + plugin_link + '/assets/img/Product_Image.png" alt="" width="840" height="588" /></a></p>'},
				{type: 2, count: 3, cont_one: '<img class="alignnone size-medium" src="' + plugin_link + '/assets/img/Small_Product_Image.png" alt="" width="214" height="300" />', cont_two: '<img class="alignnone size-medium" src="' + plugin_link + '/assets/img/Small_Product_Image.png" alt="" width="214" height="300" />'}
			];
			break;
		case 'follow_up':
			var Elements = [
				{type: 1, count: 0, cont_one: '<p style="text-align: center;"><img class="alignnone size-medium" src="' + plugin_link + '//assets/img/Logo_Image.png" alt="" width="300" height="135" /></a></p>'},
				{type: 1, count: 1, cont_one: '<h1 style="text-align: center;">Just Checking In</h1>'},
				{type: 1, count: 2, cont_one: '<p>We noticed you haven\'t come by our website recently, and we wanted to make sure everything is alright with the service you\'re getting from us.</p>'},
				{type: 1, count: 3, cont_one: '<h3 style="text-align: center;">Your friendly suppliers,</h3><p style="text-align: center;">Awesome company name</p>'}
			];
			break;
		case 'tutorial':
			var Elements = [
				{type: 1, count: 0, cont_one: '<p style="text-align: center;"><img class="alignnone size-medium" src="' + plugin_link + '/assets/img/Logo_Image.png" alt="" width="300" height="135" /></a></p>'},
				{type: 1, count: 1, cont_one: '<h3>Help customers get started with your product or service</h3>'},
				{type: 1, count: 2, cont_one: '<p>Link to tutorials, product manuals and more! Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quam mauris, euismod ut turpis vel, finibus tristique nisi. Duis ullamcorper turpis tortor, quis venenatis dolor fringilla egestas.</p>'},
				{type: 1, count: 3, cont_one: '<h3 style="text-align: center;"><a href="https://www.etoilewebdesign.com">Link to your help section</a></h3>'},
				{type: 2, count: 4, cont_one: '<h4 style="text-align: left;">Way to Get Help</h4><p>Here\'s one way of getting help. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quam mauris, euismod ut turpis vel, finibus tristique nisi. Duis ullamcorper turpis tortor, quis venenatis dolor fringilla egestas.</p>', cont_two: '<h4 style="text-align: left;">Another Way</h4><p>Here\'s the second way to get help. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quam mauris, euismod ut turpis vel, finibus tristique nisi. Duis ullamcorper turpis tortor, quis venenatis dolor fringilla egestas.</p>'}
			];
			break;
		default:
			var Elements = [];
			break;
	}

	return Elements;
}

function EWD_UWPM_Section_Editor_Click_Handlers() {
	jQuery('.ewd-uwpm-section').off('click')
	jQuery('.ewd-uwpm-section').on('click', function() {
		var section_id = jQuery(this).attr('id');
		var current_HTML = jQuery(this).html();
		var section_count = jQuery(this).data('sectioncount');

		var div_character_count = -74 - String(section_count).length;
		var editor_HTML = current_HTML.slice(0, div_character_count);

		jQuery('#ewd-uwpm-section-editor').removeClass('ewd-uwpm-hidden').data('sectionid', section_id);
		jQuery('#ewd-uwpm-email-styling-options').addClass('ewd-uwpm-hidden');
		if (!tinymce.activeEditor) {
			jQuery('.wp-editor-wrap .switch-tmce').trigger('click');
			setTimeout(function() {EWD_UWPM_Display_TinyMCE_Content(editor_HTML)}, 150);
		}
		else {EWD_UWPM_Display_TinyMCE_Content(editor_HTML);}
	});
}

function EWD_UWPM_Display_TinyMCE_Content(editor_HTML) {
	tinymce.activeEditor.setContent(editor_HTML);

	jQuery('#ewd-uwpm-section-editor').addClass('ewd-uwpm-split-screen');
	jQuery('#ewd-uwpm-visual-builder-area').addClass('ewd-uwpm-split-screen');

	tinymce.activeEditor.focus();
}

function EWD_UWPM_Enable_Sortable() {
	jQuery('.ewd-uwpm-section-container').sortable({
		items: '.ewd-uwpm-section',
		cursor: 'move',
		axis: 'x'
	});
}

function EWD_UWPM_Enable_Delete_Section() {
	jQuery('.ewd-uwpm-delete').off('click');
	jQuery('.ewd-uwpm-delete').on('click', function() {
		jQuery(this).parent().remove();
	});
}

function EWD_UWPM_Save_Section_Editor() {
	var section_id = jQuery('#ewd-uwpm-section-editor').data('sectionid');
	//var editor_HTML = jQuery('#ewd-uwpm-editor-textarea').val();
	var editor_HTML = tinymce.activeEditor.getContent();
	var current_HTML = jQuery('#' + section_id).html();

	var section_count = jQuery('#' + section_id).data('sectioncount');
	var div_character_count = 74 + String(section_count).length ;
	var div_HTML = current_HTML.substr(current_HTML.length - div_character_count);

	jQuery('#' + section_id).html(editor_HTML + div_HTML);
}

/***********************************************
* EMAIL LISTS TABLE 
***********************************************/

jQuery( document ).ready( function() {

	ewd_uwpm_email_list_delete_handlers();

	ewd_uwpm_email_list_edit_handlers()

	// Add a new list to the table
	jQuery( '.ewd-uwpm-email-lists-add' ).on( 'click', function() {
	
		var max_id = 1;
	
		jQuery( 'input[name="ewd_uwpm_email_list_id"]' ).each( function() {
	
			max_id = Math.max( max_id, jQuery( this ).val() );
		});
	
		max_id += 1;
	
		let _template = jQuery( '.ewd-uwpm-email-list-template' ).clone();
	
	    _template.hide()
	      .removeClass()
	      .addClass( 'ewd-uwpm-email-list' );
	
	    _template.find( 'input[name="ewd_uwpm_email_list_id"]' ).val( max_id );

	    jQuery( this ).before( _template );
	
	    _template.fadeIn( 'fast' );
	
		ewd_uwpm_email_list_delete_handlers();

		ewd_uwpm_email_list_edit_handlers();
	});

	// Save the list information to a hidden input before submitting the form
	jQuery( '#ewd-uwpm-email-lists-table' ).on( 'submit', function() {

		var email_lists = [];

		jQuery( '.ewd-uwpm-email-list' ).each( function() {

			if ( jQuery( this ).find( 'input[name="ewd_uwpm_email_list_id"]' ).val() == 0 ) { return; }

			var email_list = {};

			email_list.id 					= jQuery( this ).find( 'input[name="ewd_uwpm_email_list_id"]' ).val();
			email_list.name 				= jQuery( this ).find( 'input[name="ewd_uwpm_email_list_name"]' ).val();
			email_list.emails_sent 			= jQuery( this ).find( 'input[name="ewd_uwpm_email_list_emails_sent"]' ).val();
			email_list.last_send_date 		= jQuery( this ).find( 'input[name="ewd_uwpm_email_list_last_send_date"]' ).val();
			email_list.user_list 			= jQuery( this ).find( 'input[name="ewd_uwpm_email_list_user_list"]' ).val();

			email_lists.push( email_list ); 
		});

		jQuery( 'input[name="ewd_uwpm_email_list_save_values"]' ).val( JSON.stringify( email_lists ) );
	});
	
	// Hide the list edit pop-up
	jQuery( '.ewd-uwpm-close-list-edit, .ewd-uwpm-list-background ').on( 'click', function() {
		
		jQuery( '.ewd-uwpm-list-background, .ewd-uwpm-edit-list' ).addClass( 'ewd-uwpm-hidden' );
	});
	
	// Save the list details from the pop-up
	jQuery( '.ewd-uwpm-save-list-edit' ).on( 'click', function() {

		var list_row = jQuery( 'input[name="ewd_uwpm_email_list_id"][value="' + jQuery( '.ewd-uwpm-edit-list' ).data( 'currentid' ) + '"]' ).parent();
	
		var list_name = jQuery( '.ewd-uwpm-list-name' ).val();
		var user_ids = [];

		jQuery( '.ewd-uwpm-list-user' ).each( function( index, el ) {

			user_ids.push( { 
				id : jQuery(this).data('userid'), 
				name:jQuery(this).html()
			} );
		} );
	
		jQuery( list_row ).find( 'input[name="ewd_uwpm_email_list_user_list"]' ).val( JSON.stringify( user_ids ));
		jQuery( list_row ).find( '.ewd_uwpm_email_list_name' ).html( list_name );
		jQuery( list_row ).find( 'input[name="ewd_uwpm_email_list_name"]' ).val( list_name );
		jQuery( list_row ).find( '.ewd_uwpm_email_list_user_count' ).html( user_ids.length );
	
		jQuery( '.ewd-uwpm-list-background, .ewd-uwpm-edit-list' ).addClass( 'ewd-uwpm-hidden' );
	});
});

function ewd_uwpm_email_list_edit_handlers() {

	// Show the list edit pop-up, after clicking on a specific list
	jQuery( '.ewd-uwpm-email-list-details' ).off( 'click' ).on( 'click', function() {

		var list_row = jQuery( this ).parent();

		var ID = jQuery( this ).parent().data( 'rowid' );
	
		jQuery( '.ewd-uwpm-list-background, .ewd-uwpm-edit-list' ).removeClass( 'ewd-uwpm-hidden' );
	
		jQuery( '.ewd-uwpm-edit-list' ).data( 'currentid', list_row.find( 'input[name="ewd_uwpm_email_list_id"]' ).val() );
	
		jQuery( '.ewd-uwpm-list-name' ).val( list_row.find( 'input[name="ewd_uwpm_email_list_name"]' ).val() );
	
		jQuery( '.ewd-uwpm-current-users-table' ).html( '' );

		var user_list = JSON.parse( list_row.find( 'input[name="ewd_uwpm_email_list_user_list"]' ).val() );
	
		jQuery( user_list ).each( function( index, el ) {

			var html = '<input type="checkbox" class="ewd-uwpm-remove-user-from-list" value="' + el.id + '">';
			html += '<div class="ewd-uwpm-list-user" data-userid="' + el.id + '">' + el.name + '</div>';
			html += '<div class="ewd-uwpm-clear"></div>';
	
			jQuery( '.ewd-uwpm-current-users-table' ).append( html );
		});

		ewd_uwpm_email_list_add_users_handler();

		ewd_uwpm_email_list_remove_users_handler();
	});

	jQuery( '.ewd-uwpm-add-select-all-users' ).off( 'click' ).on( 'click', function() {

		jQuery( '.ewd-uwpm-add-user-id' ).prop( 'checked', true );
	});
}

function ewd_uwpm_email_list_delete_handlers() {

	jQuery( '.ewd-uwpm-email-list-delete' ).off( 'click' ).on( 'click', function() {

		jQuery( this ).parent().remove();

	});
}

function ewd_uwpm_email_list_add_users_handler() {
	
	jQuery('.ewd-uwpm-add-list-users').off( 'click' ).on( 'click', function(event) { 

		event.preventDefault();
	
		jQuery( '.ewd-uwpm-add-user-id' ).each( function() {

			var user_id = jQuery( this ).val(); 
	
			if ( ! jQuery( this ).is( ':checked' ) || jQuery( '.ewd-uwpm-list-user[data-userid="' + user_id + '"]' ).length ) { return; }

			var html = '<input type="checkbox" class="ewd-uwpm-remove-user-from-list" value="' + user_id + '">';
			html += '<div class="ewd-uwpm-list-user" data-userid="' + user_id + '">' + jQuery(this).data('name') + '</div>';
			html += '<div class="ewd-uwpm-clear"></div>';

			jQuery( '.ewd-uwpm-current-users-table' ).append( html );
	
			jQuery( this ).attr('checked', false);
		});
	});
}
	
function ewd_uwpm_email_list_remove_users_handler() {
	
	jQuery( '.ewd-uwpm-remove-list-users' ).off( 'click' ).on( 'click', function( event ) {
		
		event.preventDefault();
	
		jQuery( '.ewd-uwpm-remove-user-from-list' ).each( function() {

			if ( jQuery( this ).is( ':checked' ) ) {
	
				jQuery( '.ewd-uwpm-list-user[data-userid="' + jQuery(this).val() + '"]' ).remove();

				jQuery( this ).remove();
			}
		});
	});
}

//NEW DASHBOARD MOBILE MENU AND WIDGET TOGGLING
jQuery(document).ready(function($){
	$('#ewd-uwpm-dash-mobile-menu-open').click(function(){
		$('.EWD_UWPM_Menu .nav-tab:nth-of-type(1n+2)').toggle();
		$('#ewd-uwpm-dash-mobile-menu-up-caret').toggle();
		$('#ewd-uwpm-dash-mobile-menu-down-caret').toggle();
		return false;
	});
	$(function(){
		$(window).resize(function(){
			if($(window).width() > 800){
				$('.EWD_UWPM_Menu .nav-tab:nth-of-type(1n+2)').show();
			}
			else{
				$('.EWD_UWPM_Menu .nav-tab:nth-of-type(1n+2)').hide();
				$('#ewd-uwpm-dash-mobile-menu-up-caret').hide();
				$('#ewd-uwpm-dash-mobile-menu-down-caret').show();
			}
		}).resize();
	});	
	$('#ewd-uwpm-dashboard-support-widget-box .ewd-uwpm-dashboard-new-widget-box-top').click(function(){
		$('#ewd-uwpm-dashboard-support-widget-box .ewd-uwpm-dashboard-new-widget-box-bottom').toggle();
		$('#ewd-uwpm-dash-mobile-support-up-caret').toggle();
		$('#ewd-uwpm-dash-mobile-support-down-caret').toggle();
	});
	$('#ewd-uwpm-dashboard-optional-table .ewd-uwpm-dashboard-new-widget-box-top').click(function(){
		$('#ewd-uwpm-dashboard-optional-table .ewd-uwpm-dashboard-new-widget-box-bottom').toggle();
		$('#ewd-uwpm-dash-optional-table-up-caret').toggle();
		$('#ewd-uwpm-dash-optional-table-down-caret').toggle();
	});
});


/***********************************************
* INFINITE TABLES
***********************************************/

jQuery(document).ready(function($){

	$( '.sap-new-admin-add-button' ).on( 'click', function() {

		setTimeout( ewd_uwpm_field_added_handler, 300);
	});

	jQuery( '.sap-infinite-table tbody tr' ).each( function() {

		ewd_uwpm_enable_disable_wc_includes( jQuery( this ) );		
	});

	ewd_uwpm_set_action_type_disable_handlers();
});

function ewd_uwpm_field_added_handler() {

	var highest = 0;
	jQuery( '.sap-infinite-table input[data-name="id"]' ).each( function() {
		if ( ! isNaN( this.value ) ) { highest = Math.max( highest, this.value ); }
	});

	jQuery( '.sap-infinite-table  tbody tr:last-of-type span.sap-infinite-table-hidden-value' ).html( highest + 1 );
	jQuery( '.sap-infinite-table  tbody tr:last-of-type input[data-name="id"]' ).val( highest + 1 );

	ewd_uwpm_enable_disable_wc_includes( jQuery( '.sap-infinite-table  tbody tr:last-of-type' ) );

	ewd_uwpm_set_action_type_disable_handlers();
}

function ewd_uwpm_set_action_type_disable_handlers() {

	jQuery( '.sap-infinite-table tbody tr select[data-name="action_type"]' ).on( 'change', function() {

		ewd_uwpm_enable_disable_wc_includes( jQuery( this ).parent().parent() );		
	});
}

function ewd_uwpm_enable_disable_wc_includes( tr ) {

	var wc_actions = [ 'wc_x_time_since_cart_abandoned', 'wc_x_time_after_purchase', 'product_added', 'product_purchased' ];

	if ( jQuery.inArray( tr.find( 'select[data-name="action_type"]' ).val(), wc_actions ) !== -1 ) { 

		tr.find( 'select[data-name="includes"]' ).prop( 'disabled', false );
	}
	else { 

		tr.find( 'select[data-name="includes"]' ).prop( 'disabled', true );
	}
}


// STATS TABLE SPECIFIC DATE FILTERING

jQuery(document).ready(function(){

	jQuery( '#ewd-uwpm-date-filter-link' ).click( function() {
		
		jQuery( '#ewd-uwpm-filters' ).toggleClass( 'date-filters-visible' );
	});
});


// SPECTRUM

jQuery(document).ready(function() {
	EWD_UWPM_Setup_Spectrum();

	jQuery('.uwpm-spectrum').each(function() {
		if (jQuery(this).val() != "") {
			jQuery(this).css('background', jQuery(this).val());
			var rgb = EWD_UWPM_hexToRgb(jQuery(this).val());
			var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
			if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
			else {jQuery(this).css('color', '#000000');}
		}
	});
});

function EWD_UWPM_Setup_Spectrum() {
	jQuery('.ewd-uwpm-spectrum input').spectrum({
		showInput: true,
		showInitial: true,
		preferredFormat: "hex",
		allowEmpty: true
	});

	jQuery('.ewd-uwpm-spectrum input').css('display', 'inline');

	jQuery('.ewd-uwpm-spectrum input').on('change', function() {
		if (jQuery(this).val() != "") {
			jQuery(this).css('background', jQuery(this).val());
			var rgb = EWD_UWPM_hexToRgb(jQuery(this).val());
			var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
			if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
			else {jQuery(this).css('color', '#000000');}
		}
		else {
			jQuery(this).css('background', 'none');
		}
	});
}

function EWD_UWPM_hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

// About Us Page
jQuery( document ).ready( function( $ ) {

	jQuery( '.ewd-uwpm-about-us-tab-menu-item' ).on( 'click', function() {

		jQuery( '.ewd-uwpm-about-us-tab-menu-item' ).removeClass( 'ewd-uwpm-tab-selected' );
		jQuery( '.ewd-uwpm-about-us-tab' ).addClass( 'ewd-uwpm-hidden' );

		var tab = jQuery( this ).data( 'tab' );

		jQuery( this ).addClass( 'ewd-uwpm-tab-selected' );
		jQuery( '.ewd-uwpm-about-us-tab[data-tab="' + tab + '"]' ).removeClass( 'ewd-uwpm-hidden' );
	} );

	jQuery( '.ewd-uwpm-about-us-send-feature-suggestion' ).on( 'click', function() {

		var feature_suggestion = jQuery( '.ewd-uwpm-about-us-feature-suggestion textarea' ).val();
		var email_address = jQuery( '.ewd-uwpm-about-us-feature-suggestion input[name="feature_suggestion_email_address"]' ).val();
    
    	var params = {};

    	params.nonce  				= ewd_uwpm_admin_php_data.nonce;
    	params.action 				= 'ewd_uwpm_send_feature_suggestion';
    	params.feature_suggestion   = feature_suggestion;
    	params.email_address 		= email_address;

    	var data = jQuery.param( params );
    	jQuery.post( ajaxurl, data, function() {} );

    	jQuery( '.ewd-uwpm-about-us-feature-suggestion' ).prepend( '<p>Thank you, your feature suggestion has been submitted.' );
	} );
} );
