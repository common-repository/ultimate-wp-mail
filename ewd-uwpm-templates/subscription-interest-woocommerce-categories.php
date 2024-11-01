<div class='ewd-uwpm-si-woocommerce-categories ewd-uwpm-si-columns'>

	<h3>
		<?php _e( 'Product Categories', 'ultimate-wp-mail' ); ?>
	</h3>

	<?php foreach ( $this->get_woocommerce_categories() as $woocommerce_category ) { ?>

		<div class='ewd-uwpm-subscription-interests-item'>

			<input type='hidden' class='ewd-uwpm-si-possible-woocommerce-category' value='<?php echo esc_attr( $woocommerce_category->term_id ); ?>' />

			<input type='checkbox' class='ewd-uwpm-si-woocommerce-category' value='<?php echo esc_attr( $woocommerce_category->term_id ); ?>' <?php echo ( in_array( $woocommerce_category->term_id, $this->get_woocommerce_category_interests() ) ? 'checked' : '' ); ?> />

			<span><?php echo esc_html( $woocommerce_category->name ); ?></span>

		</div>

	<?php } ?>

</div>