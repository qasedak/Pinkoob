<?php get_header(); global $user_ID; ?>

<div class="container-fluid">
	<div id="userbar" class="row">
		<ul class="nav">
			<li<?php if (!isset($_GET['q']) || $_GET['q'] == '') { echo ' class="active"'; } ?>><a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;category=<?php echo $_GET['category']; ?>&amp;sort=<?php echo $_GET['sort']; ?>&amp;minprice=<?php echo $_GET['minprice']; ?>&amp;maxprice=<?php echo $_GET['maxprice']; ?>&amp;filter=<?php echo $_GET['filter']; ?>"><?php _e('Pins', 'pinc'); ?><br /><strong><?php echo get_items_count('pin'); ?></strong></a></li>
			<?php if ($user_ID) { ?>
			<li<?php if (isset($_GET['q']) && $_GET['q'] == 'my-own-pins') { echo ' class="active"'; } ?>><a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'my-own-pins'; ?>&amp;category=<?php echo $_GET['category']; ?>&amp;sort=<?php echo $_GET['sort']; ?>&amp;minprice=<?php echo $_GET['minprice']; ?>&amp;maxprice=<?php echo $_GET['maxprice']; ?>&amp;filter=<?php echo $_GET['filter']; ?>"><?php _e('My Own Pins', 'pinc'); ?><br /><strong><?php echo get_items_count('ownpin'); ?></strong></a></li>
			<?php } ?>
			<li <?php if (isset($_GET['q']) && $_GET['q'] == 'boards') { echo ' class="active"'; } ?>><a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'boards'; ?>&amp;category=<?php echo $_GET['category']; ?>&amp;sort=<?php echo $_GET['sort']; ?>&amp;minprice=<?php echo $_GET['minprice']; ?>&amp;maxprice=<?php echo $_GET['maxprice']; ?>&amp;filter=<?php echo $_GET['filter']; ?>"><?php _e('Boards', 'pinc'); ?><br /><strong><?php echo get_items_count('board'); ?></strong></a></li>
			<?php if (of_get_option('posttags') != 'disable') { ?>
			<li<?php if (isset($_GET['q']) && $_GET['q'] == 'tags') { echo ' class="active"'; } ?>><a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'tags'; ?>&amp;category=<?php echo $_GET['category']; ?>&amp;sort=<?php echo $_GET['sort']; ?>&amp;minprice=<?php echo $_GET['minprice']; ?>&amp;maxprice=<?php echo $_GET['maxprice']; ?>&amp;filter=<?php echo $_GET['filter']; ?>"><?php _e('Tags', 'pinc'); ?><br /><strong><?php echo get_items_count('tag'); ?></strong></a></li>
			<?php } ?>
			<?php if ($user_ID) { ?>
			<li<?php if (isset($_GET['q']) && $_GET['q'] == 'users') { echo ' class="active"'; } ?>><a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'users'; ?>&amp;category=<?php echo $_GET['category']; ?>&amp;sort=<?php echo $_GET['sort']; ?>&amp;minprice=<?php echo $_GET['minprice']; ?>&amp;maxprice=<?php echo $_GET['maxprice']; ?>&amp;filter=<?php echo $_GET['filter']; ?>"><?php _e('Users', 'pinc'); ?><br /><strong><?php echo get_items_count('user'); ?></strong></a></li>
			<?php } ?>
		</ul>
	</div>

	<div class="clearfix"><br /></div>
	
	<?php if (isset($_GET['q']) && $_GET['q'] == 'boards') { ?>
		<div id="advanced-search-form" class="text-center">
			<form method="get" action="<?php echo home_url('/'); ?>" class="form-inline">
				<input class="form-control input-sm" type="search" name="s" value="<?php the_search_query(); ?>" placeholder="<?php _e('Search...', 'pinc'); ?>" />				
				<input type="hidden" name="q" value="<?php echo 'boards'; ?>" />
				<input type="submit" class="btn btn-success btn-sm" value="<?php _e('Search', 'pinc'); ?>" />
			</form>
			<p></p>
		</div>

		<?php
		global $wp_taxonomies;

		$boards_count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT($wpdb->terms.term_id)
			FROM $wpdb->terms, $wpdb->term_taxonomy
			WHERE $wpdb->terms.name LIKE %s
			AND $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
			AND $wpdb->term_taxonomy.taxonomy = 'board'
			AND $wpdb->term_taxonomy.description != ''"
			, '%' . get_search_query() . '%' 
			)
		);
		
		if ($boards_count > 0) {
		?>
		<div id="user-profile-boards">
		<?php	
			$pnum = intval($_GET['pnum']) ? $_GET['pnum'] : 1;
			$boards_per_page = 24;
			$maxpage = ceil($boards_count/$boards_per_page);
			$limit = ($pnum - 1) * $boards_per_page;
			
			$boards_id = $wpdb->get_col($wpdb->prepare(
				"SELECT $wpdb->terms.term_id
				FROM $wpdb->terms, $wpdb->term_taxonomy
				WHERE $wpdb->terms.name LIKE %s
				AND $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
				AND $wpdb->term_taxonomy.taxonomy = 'board'
				AND $wpdb->term_taxonomy.description != ''
				ORDER BY $wpdb->terms.name ASC
				LIMIT $limit, $boards_per_page"
				, '%' . get_search_query() . '%'
				)
			);
			
			if (empty($boards_id)) {
				$boards_paginated = array();
			} else {
				$boards_paginated = get_terms('board', array('hide_empty' => false, 'include' => $boards_id));
			}
			
			foreach ($boards_paginated as $board) {
				$board_id = $board->term_id;
				$board_parent_id = $board->parent;
				$board_name = $board->name;
				$board_count = $board->count;
				$board_slug = $board->slug;
				$board_user_id = get_term($board_parent_id, 'board');
				
				$board_thumbnail_ids = $wpdb->get_col($wpdb->prepare(
					"
					SELECT v.meta_value
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
				?>
				<div class="board-mini">
					<a class="board-title" title="<?php echo esc_attr($board_name); ?>" href="<?php echo home_url('/' . $wp_taxonomies["board"]->rewrite['slug'] . '/' . sanitize_title($board_name, '_') . '/' . $board_id . '/'); ?>">
						<h4><?php echo $board_name; ?></h4>
					</a>
						
					<a href="<?php echo home_url('/' . $wp_taxonomies["board"]->rewrite['slug'] . '/' . sanitize_title($board_name, '_') . '/' . $board_id . '/'); ?>">
						<div class="board-photo-frame">
							<?php
							$count= 1;
							$post_array = array();
							foreach ($board_thumbnail_ids as $board_thumbnail_id) {
								if ($count == 1) {
									$imgsrc = wp_get_attachment_image_src($board_thumbnail_id, 'medium');
									$imgsrc = $imgsrc[0];
									array_unshift($post_array, $imgsrc);
								} else {
								$imgsrc = wp_get_attachment_image_src($board_thumbnail_id, 'thumbnail');
									$imgsrc = $imgsrc[0];
									array_unshift($post_array, $imgsrc);
								}
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
									if ($post_final !=='') {
									?>
									<div class="board-main-photo-wrapper">
										<span class="board-pin-count"><?php echo $board_count ?> <?php if ($board_count == 1) { _e('pin', 'pinc'); } else { _e('pins', 'pinc'); } ?></span>
										<img src="<?php echo $post_final; ?>" class="board-main-photo" alt="" />
									</div>
									<?php
									} else {
									?>
									<div class="board-main-photo-wrapper">
										<span class="board-pin-count">0 <?php _e('pins', 'pinc'); ?></span>
									</div>
									<?php 
									}
								} else if ($post_final !=='') {
									?>
									<div class="board-photo-wrapper">
									<img src="<?php echo $post_final; ?>" class="board-photo" alt="" />
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
					
					<?php if ($board_user_id->name != $user_ID) { ?>
						<span class="undisable_buttons">
							<button class="btn btn-success btn-sm follow pinc-follow<?php if ($followed = pinc_followed($board_id)) { echo ' disabled'; } ?>" data-author_id="<?php echo $user_info->ID; ?>" data-board_id="<?php echo $board_id;  ?>" data-board_parent_id="<?php echo $board_parent_id; ?>" type="button"><?php if (!$followed) { _e('Follow Board', 'pinc'); } else { _e('Unfollow Board', 'pinc'); } ?></button>
						</span>
					<?php } else { ?>
						<a class="btn btn-success btn-sm edit-board" href="<?php echo home_url('/grp-settings/?i=') . $board_id; ?>"><?php _e('Edit Board', 'pinc'); ?></a>
					<?php } ?>
				</div>
			<?php } //end foreach	?>
			
			<?php if ($maxpage != 0) { ?>
			<div id="navigation">
				<ul class="pager">
					<?php if ($pnum != 1 && $maxpage >= $pnum) { ?>
					<li id="navigation-previous">
						<a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'boards'; ?>&amp;pnum=<?php echo $pnum-1; ?>"><?php _e('&laquo; Previous', 'pinc') ?></a>
					</li>
					<?php } ?>
					
					<?php if ($maxpage != 1 && $maxpage != $pnum) { ?>
					<li id="navigation-next">
						<a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'boards'; ?>&amp;pnum=<?php echo $pnum+1; ?>"><?php _e('Next &raquo;', 'pinc') ?></a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>
		</div>
		<div class="clearfix"></div>
		</div>
	
	
		<?php } else { ?>
			<div class="row">
				<div class="bigmsg">
					<h2><?php _e('Nothing yet.', 'pinc'); ?></h2>
				</div>
			</div>
		</div>
		<?php }
			
	} else if (isset($_GET['q']) && $_GET['q'] == 'users') {
	?>
		<div id="advanced-search-form" class="text-center">
			<form method="get" action="<?php echo home_url('/'); ?>" class="form-inline">
				<input class="form-control input-sm" type="search" name="s" value="<?php the_search_query(); ?>" placeholder="<?php _e('Search...', 'pinc'); ?>" />				
				<input type="hidden" name="q" value="<?php echo 'users'; ?>" />
				<input type="submit" class="btn btn-success btn-sm" value="<?php _e('Search', 'pinc'); ?>" />
			</form>
			<p></p>
		</div>
	
		<?php
		$pnum = isset($_GET['pnum']) ? intval($_GET['pnum']) : 1;
		$args = array(
			'search' => '*' . get_search_query() . '*',
			'search_columns' => array('user_login'),
			'orderby' => 'display_name',
			'number' => get_option('posts_per_page'),
			'offset' => ($pnum-1) * get_option('posts_per_page')
		 );
	
		$search_user_query = new WP_User_Query($args);
		$maxpage = ceil($search_user_query->total_users/get_option('posts_per_page'));
		$user_info = get_user_by('id', $user_ID);
	
		if ($search_user_query->total_users > 0) {
			echo '<div id="user-profile-follow" class="row">';
			foreach ($search_user_query->results as $search_user) {
				?>
				<div class="follow-wrapper">					
					<a class="follow-user-name" title="<?php echo esc_attr($search_user->display_name); ?>" href="<?php echo home_url('/' . $wp_rewrite->author_base . '/') . $search_user->user_nicename; ?>/">
						<h4><?php echo $search_user->display_name; ?></h4>
						<p class="follow-user-meta"><?php $pins_count = count_user_posts($search_user->ID); echo $pins_count; ?> <?php if ($pins_count == 1) _e('Pin', 'pinc'); else _e('Pins', 'pinc'); ?> &#8226; <?php if ('' == $followers_count = get_user_meta($search_user->ID, '_Followers Count', true)) echo '0'; else echo $followers_count; ?> <?php if ($followers_count == 1) _e('Follower', 'pinc'); else _e('Followers', 'pinc'); ?></p>
						<div class="clearfix"></div>
					</a>
					
					<a class="follow-user-name" href="<?php echo home_url('/' . $wp_rewrite->author_base . '/') . $search_user->user_nicename; ?>/">
						<div class="follow-user-avatar">
							<?php echo get_avatar($search_user->ID, 105); ?>
						</div>

						<div class="follow-user-posts">
							<?php
							$follower_posts_count = 0;

						$follower_posts_thumbnail_ids = $wpdb->get_col($wpdb->prepare(
							"		
							SELECT $wpdb->postmeta.meta_value
							FROM $wpdb->posts, $wpdb->postmeta
							WHERE $wpdb->posts.post_author = %d
							AND $wpdb->posts.post_status = 'publish'
							AND $wpdb->posts.ID = $wpdb->postmeta.post_id
							AND $wpdb->postmeta.meta_key = '_thumbnail_id'
							ORDER BY $wpdb->posts.ID DESC
							LIMIT 0, 4
							",
							$search_user->ID
						));

						foreach ($follower_posts_thumbnail_ids as $follower_posts_thumbnail_id) {
							$imgsrc = wp_get_attachment_image_src($follower_posts_thumbnail_id, 'thumbnail');
							echo '<div class="follow-user-posts-thumb"><img src="' . $imgsrc[0] . '" alt="" /></div>';
							$follower_posts_count++;						
						}
							
							while ($follower_posts_count < 4):
								echo '<div class="follow-user-posts-thumb follow-user-posts-thumb-blank"><img src="' . get_template_directory_uri() . '/img/blank2.gif" alt="" /></div>';
								$follower_posts_count++;
							endwhile;
							?>
						</div>
						<div class="clearfix"></div>
					</a>
					
					<?php
					if ($search_user->ID != $user_info->ID) {
					?>
					<span class="undisable_buttons">
						<button class="btn btn-success btn-block follow pinc-follow<?php $parent_board = get_user_meta($search_user->ID, '_Board Parent ID', true); if ($followed = pinc_followed($parent_board)) { echo ' disabled'; } ?>" data-author_id="<?php echo $search_user->ID; ?>" data-board_id="<?php echo $parent_board; ?>" data-board_parent_id="0" data-disable_others="no" type="button"><?php if (!$followed) { _e('Follow', 'pinc'); } else { _e('Unfollow', 'pinc'); } ?></button>
					</span>
					<?php } else { ?>
					<button class="btn btn-success btn-block follow" disabled="disabled" type="button"><?php _e('Myself!', 'pinc'); ?></button>
					<?php } ?>
				</div>
			<?php 
			}
			
			if ($maxpage != 0) { ?>
			<div id="navigation">
				<ul class="pager">				
					<?php if ($pnum != 1 && $maxpage >= $pnum) { ?>
					<li id="navigation-previous">
						<a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'users'; ?>&amp;pnum=<?php echo $pnum-1; ?>"><?php _e('&laquo; Previous', 'pinc') ?></a>
					</li>
					<?php } ?>
					
					<?php if ($maxpage != 1 && $maxpage != $pnum) { ?>
					<li id="navigation-next">
						<a href="<?php echo home_url('/?s=') . str_replace(' ','+',get_search_query()); ?>&amp;q=<?php echo 'users'; ?>&qmp;pnum=<?php echo $pnum+1; ?>"><?php _e('Next &raquo;', 'pinc') ?></a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php }
			echo '</div><div class="clearfix"></div></div>';
		} else {
		?>
			<div class="row">
				<div class="bigmsg">
					<h2><?php _e('Nobody yet.', 'pinc'); ?></h2>
				</div>
			</div>
		</div>
		<?php
		}
		
	} else if (isset($_GET['q']) && $_GET['q'] == 'tags') {
	?>
		<div id="advanced-search-form" class="text-center">
			<form method="get" action="<?php echo home_url('/'); ?>" class="form-inline">
				<input class="form-control input-sm" type="search" name="s" value="<?php the_search_query(); ?>" placeholder="<?php _e('Search...', 'pinc'); ?>" />				
				<input type="hidden" name="q" value="<?php echo 'tags'; ?>" />
				<input type="submit" class="btn btn-success btn-sm" value="<?php _e('Search', 'pinc'); ?>" />
			</form>
			<p></p>
		</div>
		
		<?php
		$args = array(
			'search' => get_search_query(),
			'orderby' => 'count',
			'order' => 'DESC',
			'number' => '100',
		 );
	
		$search_tags = get_tags($args);

		if (!empty($search_tags)) {
			echo '<div id="search-tags" class="row">';
	
			foreach ($search_tags as $tag) {
				echo '<a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . ' (' . $tag->count . ')</a>';
			}
	
			echo '</div><div class="clearfix"></div></div>';
		} else {
		?>
			<div class="row">
				<div class="bigmsg">
					<h2><?php _e('Nothing yet.', 'pinc'); ?></h2>
				</div>
			</div>
		</div>
		<?php
		}
		
	} else if (isset($_GET['q']) && $_GET['q'] == 'my-own-pins') {
		?>
		<div id="advanced-search-form" class="text-center">
			<form method="get" action="<?php echo home_url('/'); ?>" class="form-inline">
				<input class="form-control input-sm" type="search" name="s" value="<?php the_search_query(); ?>" placeholder="<?php _e('Search...', 'pinc'); ?>" />
				
				<?php
				echo pinc_dropdown_categories(__('All categories', 'pinc'), 'category', intval($_GET['category']));
				?>

				<select id="sort" class="form-control input-sm" name="sort">
					<option<?php if ($_GET['sort'] == 'recent') echo ' selected'; ?> value="recent"><?php _e('Most recent', 'pinc'); ?></option>
					<option<?php if ($_GET['sort'] == 'popular') echo ' selected'; ?> value="popular"><?php _e('Most popular', 'pinc'); ?></option>
					<?php if (of_get_option('price_currency') != '') { ?>
						<option<?php if ($_GET['sort'] == 'pricelowest') echo ' selected'; ?> value="pricelowest"><?php _e('Price lowest', 'pinc'); ?></option>
						<option<?php if ($_GET['sort'] == 'pricehighest') echo ' selected'; ?> value="pricehighest"><?php _e('Price highest', 'pinc'); ?></option>
					<?php } ?>
				</select>
				
				<?php if (of_get_option('price_currency') != '') { ?>
					<input id="minprice" class="form-control input-sm" type="text" name="minprice" value="<?php if (is_numeric($_GET['minprice']) && $_GET['minprice'] >= 0) echo $_GET['minprice']; else echo ''; ?>" placeholder="<?php _e('Min Price', 'pinc'); ?>" />
					<input id="maxprice" class="form-control input-sm" type="text" name="maxprice" value="<?php if (is_numeric($_GET['maxprice']) && $_GET['maxprice'] >= $_GET['minprice'] && $_GET['maxprice'] >= 0) echo $_GET['maxprice']; else echo ''; ?>" placeholder="<?php _e('Max Price', 'pinc'); ?>"  />
				<?php } ?>
				
				<input type="hidden" name="q" value="<?php echo 'my-own-pins'; ?>" />
				<input type="hidden" name="filter" value="1" />
				<input type="submit" class="btn btn-success btn-sm" value="<?php _e('Search', 'pinc'); ?>" />
			</form>
			<p></p>
		</div>
		<?php
		if (isset($_GET['filter']) && $_GET['filter'] == '1') {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			
			if (isset($_GET['category']) && $_GET['category'] != '-1') {
				$args_category = array(
					'category__in' => intval($_GET['category'])
				);
			} else {
				$args_category = array();
			}
			
			switch($_GET['sort']) {
			case "recent":
				$order = '';
				$orderby = '';
				$meta_key = '';
			break;
			case "popular":
				if ('likes' == $popularity = of_get_option('popularity')) {
					$order = 'desc';
					$orderby = 'meta_value_num';
					$meta_key = '_Likes Count';
				} else if ($popularity == 'repins') {
					$order = 'desc';
					$orderby = 'meta_value_num';
					$meta_key = '_Repin Count';
				} else if ($popularity == 'comments') {
					$order = 'desc';
					$orderby = 'comment_count';
					$meta_key = '';
				} else {
					$order = 'desc';
					$orderby = 'comment_count';
					$meta_key = '';
				}
			break;
			case "pricelowest":
				$order = 'asc';
				$orderby = 'meta_value_num';
				$meta_key = '_Price';
			break;
			case "pricehighest":
				$order = 'desc';
				$orderby = 'meta_value_num';
				$meta_key = '_Price';
			break;
			default:
				$order = '';
				$orderby = '';
				$meta_key = '';
			}

			if (!is_numeric($_GET['minprice']) || $_GET['minprice'] < 0)
				$_GET['minprice'] = '';

			if (!is_numeric($_GET['maxprice']) || $_GET['maxprice'] < $_GET['minprice'] || $_GET['maxprice'] < 0)
				$_GET['maxprice'] = '';

			if ($_GET['minprice'] != '' && $_GET['maxprice'] == '') {
				$args_price = array(
					'meta_query' => array(
						array(
							'key' => '_Price',
							'value' => $_GET['minprice'],
							'type' => 'numeric',
							'compare' => '>='
						)
					)
				);
			} else if ($_GET['minprice'] == '' && $_GET['maxprice'] != '') {
				$args_price = array(
					'meta_query' => array(
						array(
							'key' => '_Price',
							'value' => $_GET['maxprice'],
							'type' => 'numeric',
							'compare' => '<='
						)
					)
				);
			} else if ($_GET['minprice'] != '' && $_GET['maxprice'] != '') {
				$args_price = array(
					'meta_query' => array(
						array(
							'key' => '_Price',
							'value' => array($_GET['minprice'], $_GET['maxprice']),
							'type' => 'numeric',
							'compare' => 'BETWEEN'
						)
					)
				);
			} else {
				$args_price = array();
			}
			
			$args = array(
				's' => get_search_query(),
				'author' => $user_ID,
				'post_type' => 'post',
				'orderby' => $orderby,
				'order' => $order,
				'meta_key' => $meta_key,
				'paged' => $paged
			);

            $args = array_merge($args_category, $args_price, $args);
		} else {
			$args = array(
				'author' => $user_ID,
				'post_type' => 'post',
				's' => get_search_query()
			);
		}
		if ($orderby == 'meta_value_num')
			add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
			
		if ($orderby == 'meta_value_num')
			add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
	
		query_posts($args);
		
		if ($orderby == 'meta_value_num')
			remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');
			
		if ($orderby == 'meta_value_num')
			remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');
		
		get_template_part('index', 'masonry');


	} else { //default search for pins
		?>
		<div id="advanced-search-form" class="text-center">			
			<form method="get" action="<?php echo home_url('/'); ?>" class="form-inline">
				<input class="form-control input-sm" type="search" name="s" value="<?php the_search_query(); ?>" placeholder="<?php _e('Search...', 'pinc'); ?>" />
				
				<?php echo pinc_dropdown_categories(__('All categories', 'pinc'), 'category', intval($_GET['category'])); ?>

				<select id="sort" class="form-control input-sm" name="sort">
					<option<?php if ($_GET['sort'] == 'recent') echo ' selected'; ?> value="recent"><?php _e('Most recent', 'pinc'); ?></option>
					<option<?php if ($_GET['sort'] == 'popular') echo ' selected'; ?> value="popular"><?php _e('Most popular', 'pinc'); ?></option>
					<?php if (of_get_option('price_currency') != '') { ?>
						<option<?php if ($_GET['sort'] == 'pricelowest') echo ' selected'; ?> value="pricelowest"><?php _e('Price lowest', 'pinc'); ?></option>
						<option<?php if ($_GET['sort'] == 'pricehighest') echo ' selected'; ?> value="pricehighest"><?php _e('Price highest', 'pinc'); ?></option>
					<?php } ?>
				</select>
				
				<?php if (of_get_option('price_currency') != '') { ?>
					<input id="minprice" class="form-control input-sm" type="text" name="minprice" value="<?php if (is_numeric($_GET['minprice']) && $_GET['minprice'] >= 0) echo $_GET['minprice']; else echo ''; ?>" placeholder="<?php _e('Min Price', 'pinc'); ?>"  />
					<input id="maxprice" class="form-control input-sm" type="text" name="maxprice" value="<?php if (is_numeric($_GET['maxprice']) && $_GET['maxprice'] >= $_GET['minprice'] && $_GET['maxprice'] >= 0) echo $_GET['maxprice']; else echo ''; ?>" placeholder="<?php _e('Max Price', 'pinc'); ?>" />
				<?php } ?>
				
				<input type="hidden" name="filter" value="1" />
				<input type="submit" class="btn btn-success btn-sm" value="<?php _e('Search', 'pinc'); ?>" />
			</form>
			<p></p>
		</div>
		<?php
		if (isset($_GET['filter']) && $_GET['filter'] == '1') {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			
			if (isset($_GET['category']) && $_GET['category'] != '-1') {
				$args_category = array(
					'category__in' => intval($_GET['category'])
				);
			} else {
				$args_category = array();
			}
			
			switch($_GET['sort']) {
			case "recent":
				$order = '';
				$orderby = '';
				$meta_key = '';
			break;
			case "popular":
				if ('likes' == $popularity = of_get_option('popularity')) {
					$order = 'desc';
					$orderby = 'meta_value_num';
					$meta_key = '_Likes Count';
				} else if ($popularity == 'repins') {
					$order = 'desc';
					$orderby = 'meta_value_num';
					$meta_key = '_Repin Count';
				} else if ($popularity == 'comments') {
					$order = 'desc';
					$orderby = 'comment_count';
					$meta_key = '';
				} else {
					$order = 'desc';
					$orderby = 'comment_count';
					$meta_key = '';
				}
			break;
			case "pricelowest":
				$order = 'asc';
				$orderby = 'meta_value_num';
				$meta_key = '_Price';
			break;
			case "pricehighest":
				$order = 'desc';
				$orderby = 'meta_value_num';
				$meta_key = '_Price';
			break;
			default:
				$order = '';
				$orderby = '';
				$meta_key = '';
			}

			if (!is_numeric($_GET['minprice']) || $_GET['minprice'] < 0)
				$_GET['minprice'] = '';

			if (!is_numeric($_GET['maxprice']) || $_GET['maxprice'] < $_GET['minprice'] || $_GET['maxprice'] < 0)
				$_GET['maxprice'] = '';

			if ($_GET['minprice'] != '' && $_GET['maxprice'] == '') {
				$args_price = array(
					'meta_query' => array(
						array(
							'key' => '_Price',
							'value' => $_GET['minprice'],
							'type' => 'numeric',
							'compare' => '>='
						)
					)
				);
			} else if ($_GET['minprice'] == '' && $_GET['maxprice'] != '') {
				$args_price = array(
					'meta_query' => array(
						array(
							'key' => '_Price',
							'value' => $_GET['maxprice'],
							'type' => 'numeric',
							'compare' => '<='
						)
					)
				);
			} else if ($_GET['minprice'] != '' && $_GET['maxprice'] != '') {
				$args_price = array(
					'meta_query' => array(
						array(
							'key' => '_Price',
							'value' => array($_GET['minprice'], $_GET['maxprice']),
							'type' => 'numeric',
							'compare' => 'BETWEEN'
						)
					)
				);
			} else {
				$args_price = array();
			}
			
			$args = array(
				's' => get_search_query(),
				'orderby' => $orderby,
				'order' => $order,
				'meta_key' => $meta_key,
				'paged' => $paged
			);

            $args = array_merge($args_category, $args_price, $args);
			
			if ($orderby == 'meta_value_num')
				add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
				
			if ($orderby == 'comment_count')
				add_filter('posts_orderby', 'pinc_comments_orderby');
			
			query_posts($args);
			
			if ($orderby == 'meta_value_num')
				remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');
				
			if ($orderby == 'comment_count')
				remove_filter('posts_orderby', 'pinc_comments_orderby');
		}
	
		get_template_part('index', 'masonry');
	}
	?>

	<?php
	function get_items_count($type = 'pin') {
		global $wpdb, $user_ID;
		if ($type == 'board') {
			$boards_count = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT($wpdb->terms.term_id)
				FROM $wpdb->terms, $wpdb->term_taxonomy
				WHERE $wpdb->terms.name LIKE %s
				AND $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
				AND $wpdb->term_taxonomy.taxonomy = 'board'
				AND $wpdb->term_taxonomy.description != ''"
				, '%' . get_search_query() . '%' 
				)
			);
			
			return $boards_count;
			
		} else if ($type == 'tag') {
			$args = array(
				'search' => get_search_query(),
				'number' => '100',
			 );
		
			$tags_query = get_tags($args);
			return count($tags_query);
			
		} else if ($type == 'user') {
			$args = array(
				'search' => '*' . get_search_query() . '*',
				'search_columns' => array('user_login'),
				'number' => 1,
				'page' => 1
			 );

			$users_query = new WP_User_Query($args);
			return $users_query->total_users;
		
		} else if ($type == 'ownpin') {
			if (isset($_GET['filter']) && $_GET['filter'] == '1') {
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				
				if (isset($_GET['category']) && $_GET['category'] != '-1') {
					$args_category = array(
						'category__in' => intval($_GET['category'])
					);
				} else {
					$args_category = array();
				}
				
				switch($_GET['sort']) {
				case "recent":
					$order = '';
					$orderby = '';
					$meta_key = '';
				break;
				case "popular":
					if ('likes' == $popularity = of_get_option('popularity')) {
						$order = 'desc';
						$orderby = 'meta_value_num';
						$meta_key = '_Likes Count';
					} else if ($popularity == 'repins') {
						$order = 'desc';
						$orderby = 'meta_value_num';
						$meta_key = '_Repin Count';
					} else if ($popularity == 'comments') {
						$order = 'desc';
						$orderby = 'comment_count';
						$meta_key = '';
					} else {
						$order = 'desc';
						$orderby = 'comment_count';
						$meta_key = '';
					}
				break;
				case "pricelowest":
					$order = 'asc';
					$orderby = 'meta_value_num';
					$meta_key = '_Price';
				break;
				case "pricehighest":
					$order = 'desc';
					$orderby = 'meta_value_num';
					$meta_key = '_Price';
				break;
				default:
					$order = '';
					$orderby = '';
					$meta_key = '';
				}
	
				if (!is_numeric($_GET['minprice']) || $_GET['minprice'] < 0)
					$_GET['minprice'] = '';
	
				if (!is_numeric($_GET['maxprice']) || $_GET['maxprice'] < $_GET['minprice'] || $_GET['maxprice'] < 0)
					$_GET['maxprice'] = '';
	
				if ($_GET['minprice'] != '' && $_GET['maxprice'] == '') {
					$args_price = array(
						'meta_query' => array(
							array(
								'key' => '_Price',
								'value' => $_GET['minprice'],
								'type' => 'numeric',
								'compare' => '>='
							)
						)
					);
				} else if ($_GET['minprice'] == '' && $_GET['maxprice'] != '') {
					$args_price = array(
						'meta_query' => array(
							array(
								'key' => '_Price',
								'value' => $_GET['maxprice'],
								'type' => 'numeric',
								'compare' => '<='
							)
						)
					);
				} else if ($_GET['minprice'] != '' && $_GET['maxprice'] != '') {
					$args_price = array(
						'meta_query' => array(
							array(
								'key' => '_Price',
								'value' => array($_GET['minprice'], $_GET['maxprice']),
								'type' => 'numeric',
								'compare' => 'BETWEEN'
							)
						)
					);
				} else {
					$args_price = array();
				}
				
				$args = array(
					's' => get_search_query(),
					'author' => $user_ID,
					'posts_per_page' => 1
				);
	
				$args = array_merge($args_category, $args_price, $args);
			} else {
				$args = array(
					's' => get_search_query(),
					'author' => $user_ID,
					'posts_per_page' => 1,
					'fields' => 'ids'
				);
			}
			
			$ownpin_query = new WP_Query($args);
			return $ownpin_query->found_posts;
	
		} else if ($type == 'pin') {
			if (isset($_GET['filter']) && $_GET['filter'] == '1') {
				if (isset($_GET['category']) && $_GET['category'] != '-1') {
					$args_category = array(
						'category__in' => intval($_GET['category'])
					);
				} else {
					$args_category = array();
				}
				
				switch($_GET['sort']) {
				case "recent":
					$order = '';
					$orderby = '';
					$meta_key = '';
				break;
				case "popular":
					if ('likes' == $popularity = of_get_option('popularity')) {
						$order = 'desc';
						$orderby = 'meta_value_num';
						$meta_key = '_Likes Count';
					} else if ($popularity == 'repins') {
						$order = 'desc';
						$orderby = 'meta_value_num';
						$meta_key = '_Repin Count';
					} else if ($popularity == 'comments') {
						$order = 'desc';
						$orderby = 'comment_count';
						$meta_key = '';
					} else {
						$order = 'desc';
						$orderby = 'comment_count';
						$meta_key = '';
					}
				break;
				case "pricelowest":
					$order = 'asc';
					$orderby = 'meta_value_num';
					$meta_key = '_Price';
				break;
				case "pricehighest":
					$order = 'desc';
					$orderby = 'meta_value_num';
					$meta_key = '_Price';
				break;
				default:
					$order = '';
					$orderby = '';
					$meta_key = '';
				}
				
				if (!is_numeric($_GET['minprice']) || $_GET['minprice'] < 0)
					$_GET['minprice'] = '';
				
				if (!is_numeric($_GET['maxprice']) || $_GET['maxprice'] < $_GET['minprice'] || $_GET['maxprice'] < 0)
					$_GET['maxprice'] = '';
				
				if ($_GET['minprice'] != '' && $_GET['maxprice'] == '') {
					$args_price = array(
						'meta_query' => array(
							array(
								'key' => '_Price',
								'value' => $_GET['minprice'],
								'type' => 'numeric',
								'compare' => '>='
							)
						)
					);
				} else if ($_GET['minprice'] == '' && $_GET['maxprice'] != '') {
					$args_price = array(
						'meta_query' => array(
							array(
								'key' => '_Price',
								'value' => $_GET['maxprice'],
								'type' => 'numeric',
								'compare' => '<='
							)
						)
					);
				} else if ($_GET['minprice'] != '' && $_GET['maxprice'] != '') {
					$args_price = array(
						'meta_query' => array(
							array(
								'key' => '_Price',
								'value' => array($_GET['minprice'], $_GET['maxprice']),
								'type' => 'numeric',
								'compare' => 'BETWEEN'
							)
						)
					);
				} else {
					$args_price = array();
				}
			
				$args = array(
					's' => get_search_query(),
					'posts_per_page' => 1
				);
				
				$args = array_merge($args_category, $args_price, $args);
			} else {
				$args = array(
					's' => get_search_query(),
					'posts_per_page' => 1,
					'fields' => 'ids'
				);
			}
			
			$pin_query = new WP_Query($args);
			return $pin_query->found_posts;
		}
	}
	?>

<?php get_footer(); ?>