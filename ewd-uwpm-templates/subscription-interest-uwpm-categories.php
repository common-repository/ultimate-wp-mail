<div class='ewd-uwpm-si-uwpm-categories ewd-uwpm-si-columns'>

	<h3>
		<?php _e( 'Email Categories', 'ultimate-wp-mail' ); ?>
	</h3>

	<?php foreach ( $this->get_uwpm_categories() as $uwpm_category ) { ?>

		<div class='ewd-uwpm-subscription-interests-item'>

			<input type='hidden' class='ewd-uwpm-si-possible-uwpm-category' value='<?php echo esc_attr( $uwpm_category->term_id ); ?>' />

			<input type='checkbox' class='ewd-uwpm-si-uwpm-category' value='<?php echo esc_attr( $uwpm_category->term_id ); ?>' <?php echo ( in_array( $uwpm_category->term_id, $this->get_uwpm_category_interests() ) ? 'checked' : '' ); ?> />

			<span><?php echo esc_html( $uwpm_category->name ); ?></span>

		</div>

	<?php } ?>

</div>