<div class='ewd-uwpm-si-explanation'>
	<?php echo esc_html( $this->get_label( 'label-select-topics' ) ); ?>
</div>

<form>

	<?php $this->maybe_print_post_categories(); ?>

	<?php $this->maybe_print_uwpm_categories(); ?>

	<?php $this->maybe_print_woocommerce_categories(); ?>

	<div class='ewd-uwpm-clear'></div>
	
	<button class='ewd-uwpm-topics-sign-up'>
		<?php _e( 'Subscribe', 'ultimate-wp-mail' ); ?>
	</button>

</form>