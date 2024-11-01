<div class="ewd-uwpm-full-screen-inside">

	<input type='hidden' name='ewd_uwpm_nonce' value='<?php echo $this->nonce; ?>'>

	<input type='hidden' id='ewd-uwpm-email-id' value='<?php echo ( ! empty( $this->post->ID ) ? $this->post->ID : '' ); ?>' />

	<?php $this->print_send_results(); ?>

	<?php $this->print_header_bar(); ?>

	<div id='ewd-uwpm-email-input' class='ewd-uwpm-hidden'>
		
		<textarea name='ewd_uwpm_email_content'>
			<?php echo $this->content; ?>
		</textarea>

	</div>

	<?php $this->maybe_print_template_selector(); ?>

	<div id='ewd-uwpm-visual-builder-area'>
		
		<?php echo $this->content; ?>

		<div id='ewd-uwpm-template-information' data-sectioncount='<?php echo substr_count( $this->content, 'class="ewd-uwpm-section' ); ?>' ></div>

		<div class='ewd-uwpm-clear'></div>

	</div>

	<?php $this->print_styling_options(); ?>

	<?php $this->print_section_editor(); ?>

	<div class="ewd-uwpm-clear"></div>

	<?php $this->print_email_preview(); ?>

	<div class="ewd-uwpm-clear"></div>

</div>