<div id='ewd-uwpm-email-styling-options'>

	<div class='ewd-uwpm-styling-option'>

		<h4><?php _e( 'Styling Options', 'ultimate-wp-mail' ); ?></h4>

		<div class='ewd-uwpm-styling-label'><?php _e( 'Content Alignment:', 'ultimate-wp-mail' ); ?></div>

		<div class='ewd-uwpm-styling-input'>

			<select name='ewd_uwpm_content_alignment'>
				<option value='left' <?php echo ( $this->content_alignment == 'left' ? 'selected' : ''); ?> ><?php _e('Left', 'ultimate-wp-mail' ); ?></option>
				<option value='center' <?php echo ( ( $this->content_alignment != 'left' and  $this->content_alignment != 'right' ) ? 'selected' : '' ); ?> ><?php _e('Center', 'ultimate-wp-mail'); ?></option>
				<option value='right' <?php echo ( $this->content_alignment == 'right' ? 'selected' : '' ); ?> ><?php _e('Right', 'ultimate-wp-mail' ); ?></option>
			</select>

		</div>

	</div>

	<div class='ewd-uwpm-styling-option'>
		<div class='ewd-uwpm-styling-label'><?php _e( 'Max Email Width:', 'ultimate-wp-mail' ); ?></div>
		<div class='ewd-uwpm-styling-input'><input type='text' name='ewd_uwpm_max_width' value='<?php echo esc_attr( $this->max_width ); ?>' /></div>
	</div>

	<div class='ewd-uwpm-styling-option'>
		<div class='ewd-uwpm-styling-label'><?php _e( 'Email Background Color:', 'ultimate-wp-mail' ); ?></div>
		<div class='ewd-uwpm-styling-input ewd-uwpm-spectrum'><input type='text' name='ewd_uwpm_background_color' value='<?php echo esc_attr( $this->background_color ); ?>' /></div>
	</div>

	<div class='ewd-uwpm-styling-option'>
		<div class='ewd-uwpm-styling-label'><?php _e( 'Body Background Color:', 'ultimate-wp-mail' ); ?></div>
		<div class='ewd-uwpm-styling-input ewd-uwpm-spectrum'><input type='text' name='ewd_uwpm_body_background_color' value='<?php echo esc_attr( $this->body_background_color ); ?>' /></div>
	</div>

	<div class='ewd-uwpm-styling-option'>
		<div class='ewd-uwpm-styling-label'><?php _e( 'Blocks Background Color:', 'ultimate-wp-mail' ); ?></div>
		<div class='ewd-uwpm-styling-input ewd-uwpm-spectrum'><input type='text' name='ewd_uwpm_block_background_color' value='<?php echo esc_attr( $this->block_background_color ); ?>' /></div>
	</div>

	<div class='ewd-uwpm-styling-option'>
		<div class='ewd-uwpm-styling-label'><?php _e(' Blocks Border:', 'ultimate-wp-mail' ); ?></div>
		<div class='ewd-uwpm-styling-input'><input type='text' name='ewd_uwpm_block_border' value='<?php echo esc_attr( $this->block_border ); ?>' /></div>
	</div>

	<p>
		<?php _e( 'Styling settings will only display in emails after saving!', 'ultimate-wp-mail' ); ?>
	</p>

	<div class='ewd-uwpm-clear'></div>

</div>