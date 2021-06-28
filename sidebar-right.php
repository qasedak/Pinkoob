<div class="sidebar">
	<?php if (!dynamic_sidebar('sidebar-r-t')) : ?>
	<?php endif ?>

	<?php //start board section
	if (pinc_get_post_board()) {
		global $wp_taxonomies;
		$board_id = pinc_get_post_board()->term_id;
		$board_parent_id = pinc_get_post_board()->parent;
		$board_name = pinc_get_post_board()->name;
		$board_count = pinc_get_post_board()->count;
		$board_slug = pinc_get_post_board()->slug;
		$board_link = home_url('/' . $wp_taxonomies["board"]->rewrite['slug'] . '/' . sanitize_title($board_name, '_') . '/' . $board_id . '/');

		$board_results = $wpdb->get_results($wpdb->prepare(
			"
			SELECT v.meta_value, v.post_id
			FROM $wpdb->postmeta AS v
			INNER JOIN (
				SELECT object_id
				FROM $wpdb->term_taxonomy, $wpdb->term_relationships, $wpdb->posts
				WHERE $wpdb->term_taxonomy.term_id = %d
				AND $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
				AND $wpdb->term_taxonomy.taxonomy = 'board'
				AND $wpdb->term_relationships.object_id = $wpdb->posts.ID
				AND $wpdb->posts.post_status = 'publish'
				ORDER BY $wpdb->term_relationships.object_id DESC
				LIMIT 0, 5
				) AS v2 ON v.post_id = v2.object_id
				AND v.meta_key = '_thumbnail_id'
			",
			$board_id
		));


		$board_thumbnail_ids = [];
		foreach ($board_results as $result) {
			$board_thumbnail_ids[$result->meta_value] = $result->post_id;
		}
		?>
		<div class="board-mini hidden-xs">
			<a class="board-title" title="<?php echo esc_attr($board_name); ?>" href="<?php echo $board_link; ?>">
				<h4><?php echo $board_name; ?></h4>

				<div class="board-photo-frame">
					<?php
					$count= 1;
					$post_array = array();
					foreach ($board_thumbnail_ids as $board_thumbnail_id) {
						$videos = get_attached_media('video', $board_post_id);
						if ($count == 1) {
							$imgsrc = wp_get_attachment_image_src($board_thumbnail_id-1, 'board-main-image');
						} else {
							$imgsrc = wp_get_attachment_image_src($board_thumbnail_id-1, 'thumbnail');
						}
						$imgsrc = $imgsrc[0];
						$data = [
							'type' => empty($videos) ? 'image' : 'video',
							'src' => $imgsrc
						];
						array_unshift($post_array, $data);
						$count++;
					}

					$count = 1;

					$post_array_final = array_fill(0, 5, '');

					foreach ($post_array as $post_imgsrc) {
						array_unshift($post_array_final, $post_imgsrc);
						array_pop($post_array_final);
					}

					foreach ($post_array_final as $post_final) {
						if ($count == 1) {
							if ($post_final['src'] !=='') {
							?>
							<div class="board-main-photo-wrapper">
								<span class="board-pin-count"><?php echo $board_count ?> <?php if ($board_count == 1) { _e('pin', 'pinc'); } else { _e('pins', 'pinc'); } ?></span>
								<img src="<?php echo $post_final['src']; ?>" class="board-main-photo" alt="" />
								<?php if ($post_final['type'] == 'video') :?>
									<div class="featured-thumb-video"></div>
								<?php endif;?>
							</div>
							<?php
							} else {
							?>
							<div class="board-main-photo-wrapper">
								<span class="board-pin-count">0 <?php _e('pins', 'pinc'); ?></span>
							</div>
							<?php
							}
						} else if (isset($post_final['src']) && $post_final['src'] !=='') {
							?>
							<div class="board-photo-wrapper">
								<img src="<?php echo $post_final['src']; ?>" class="board-photo" alt="" />
								<?php if ($post_final['type'] == 'video') :?>
									<div class="featured-thumb-video"></div>
								<?php endif;?>
							</div>
							<?php
						} else {
							?>
							<div class="board-photo-wrapper">
							</div>
							<?php
						}
						$count++;
					}
					?>
				</div>
			</a>

			<?php global $user_ID; if ($post->post_author != $user_ID) { ?>
				<span class="undisable_buttons">
				<button class="btn btn-success btn-sm follow pinc-follow<?php if ($followed = pinc_followed($board_id)) { echo ' disabled'; } ?>" data-author_id="<?php echo $post->post_author; ?>" data-board_id="<?php echo $board_id;  ?>" data-board_parent_id="<?php echo $board_parent_id; ?>" type="button"><?php if (!$followed) { _e('Follow Board', 'pinc'); } else { _e('Unfollow Board', 'pinc'); } ?></button>
				</span>
			<?php } else { ?>
				<a class="btn btn-success btn-sm edit-board" href="<?php echo home_url('/grp-settings/?i=') . $board_id; ?>"><?php _e('Edit Board', 'pinc'); ?></a>
			<?php } ?>
		</div>
	<?php } //end board section ?>

	<?php
	//start also from section
	$photo_source_domain = get_post_meta($post->ID, '_Photo Source Domain', true);
	if ($photo_source_domain != '' ) {
		$loop_domain_args = array(
			'posts_per_page' => 9,
			'meta_key' => '_Photo Source Domain',
			'meta_value' => $photo_source_domain,
			'post__not_in' => array($post->ID),
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => '_Original Post ID',
					'compare' => 'NOT EXISTS'
				),
				array(
					'key' => '_Original Post ID',
					'value' => 'deleted'
				)
			)
		);

		$loop_domain = new WP_Query($loop_domain_args);
		if ($loop_domain->post_count > 0) {
		?>
		<div class="board-domain hidden-xs">
			<h4><?php _e('Also from', 'pinc'); ?> <a href="<?php echo home_url('/source/') . $photo_source_domain; ?>/"><?php echo $photo_source_domain; ?></a></h4>
			<a href="<?php echo home_url('/source/') . $photo_source_domain; ?>/">
			<?php
			$post_domain_array = array();
			while ($loop_domain->have_posts()) : $loop_domain->the_post();
				$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id(),'thumbnail');
				$imgsrc = $imgsrc[0];
				array_unshift($post_domain_array, $imgsrc);
			endwhile;
			wp_reset_query();

			$post_domain_array_final = array_fill(0, 9, '');

			foreach ($post_domain_array as $post_imgsrc) {
				array_unshift($post_domain_array_final, $post_imgsrc);
				array_pop($post_domain_array_final);
			}

			foreach ($post_domain_array_final as $post_final) {
				if ($post_final !=='') {
				?>
					<div class="board-domain-wrapper">
						<img src="<?php echo $post_final; ?>" alt="" />
					</div>
				<?php
				} else {
					?>
					<div class="board-domain-wrapper">
					</div>
					<?php
				}
			}
			?>
				<div class="clearfix"></div>
			</a>
		</div>
	<?php }
	} //end also from section ?>

	<?php if (!dynamic_sidebar('sidebar-r')) : ?>
	<?php endif ?>
</div>
