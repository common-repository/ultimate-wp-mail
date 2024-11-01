<div class='ewd-uwpm-si-post-categories ewd-uwpm-si-columns'>

	<h3>
		<?php _e( 'Post Categories', 'ultimate-wp-mail' ); ?>
	</h3>

	<?php foreach ( $this->get_post_categories() as $post_category ) { ?>

		<div class='ewd-uwpm-subscription-interests-item'>

			<input type='hidden' class='ewd-uwpm-si-possible-post-category' value='<?php echo esc_attr( $post_category->term_id ); ?>' />

			<input type='checkbox' class='ewd-uwpm-si-post-category' value='<?php echo esc_attr( $post_category->term_id ); ?>' <?php echo ( in_array( $post_category->term_id, $this->get_post_category_interests() ) ? 'checked' : '' ); ?> />

			<span><?php echo esc_html( $post_category->name ); ?></span>

		</div>

	<?php } ?>

</div>