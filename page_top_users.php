<?php
/*
Template Name: _top_users
*/
?>
<?php get_header(); global $user_ID; ?>

<div class="container">
	<div class="row subpage-title">
		<h1><?php _e('Top Users (Most Followers)', 'pinc'); ?></h1>
		<br />
	</div>
</div>

<div class="container-fluid">
<div class="row">
<?php
$args = array(
	'order' => 'desc',
	'orderby' => 'meta_value',
	'meta_key' => '_Followers Count',
	'meta_query' => array(
		array(
		'key' => '_Followers Count',
		'compare' => '>',
		'value' => '0',
		'type' => 'numeric'
		)
	),
	'number' => '20'
 );

$top_user_follower_query = new WP_User_Query($args);

if ($top_user_follower_query->total_users > 0) {
	echo '<div id="user-profile-follow">';
	$count = 1;
	foreach ($top_user_follower_query->results as $top_user_follower) {
		?>
		<div class="follow-wrapper">
			<span class="top-user-count<?php if ($count > 9) echo ' top-user-count-double-digit'; ?> top-user-count-alt1"><?php echo $count; $count++; ?></span>
			<a class="follow-user-name" title="<?php echo esc_attr($top_user_follower->display_name); ?>" href="<?php echo home_url('/' . $wp_rewrite->author_base . '/') . $top_user_follower->user_nicename; ?>/">
				<h4><?php echo $top_user_follower->display_name; ?></h4>
				<p class="follow-user-meta"><?php if ('' == $followers_count = get_user_meta($top_user_follower->ID, '_Followers Count', true)) echo '0'; else echo $followers_count; ?> <?php if($followers_count == 1) _e('Follower', 'pinc'); else _e('Followers', 'pinc'); ?> &#8226; <?php $pins_count = count_user_posts($top_user_follower->ID); echo $pins_count; ?> <?php if ($pins_count == 1) _e('Pin', 'pinc'); else _e('Pins', 'pinc'); ?></p>
				<div class="clearfix"></div>
			</a>
			
			<a class="follow-user-name" href="<?php echo home_url('/' . $wp_rewrite->author_base . '/') . $top_user_follower->user_nicename; ?>/">
				<div class="follow-user-avatar">
					<?php echo get_avatar($top_user_follower->ID, 105); ?>
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
						$top_user_follower->ID
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
			if ($top_user_follower->ID != $user_ID) {
			?>
			<span class="undisable_buttons">
			<button class="btn btn-success btn-block follow pinc-follow<?php $parent_board = get_user_meta($top_user_follower->ID, '_Board Parent ID', true); if ($followed = pinc_followed($parent_board)) { echo ' disabled'; } ?>" data-author_id="<?php echo $top_user_follower->ID; ?>" data-board_id="<?php echo $parent_board; ?>" data-board_parent_id="0" data-disable_others="no" type="button"><?php if (!$followed) { _e('Follow', 'pinc'); } else { _e('Unfollow', 'pinc'); } ?></button>
			</span>
			<?php } else { ?>
			<a class="btn btn-success btn-block follow" disabled="disabled"><?php _e('Myself!', 'pinc'); ?></a>
			<?php } ?>
		</div>
	<?php 
	}
	echo '</div></div>';
} else {
?>
	<div class="container">
		<div class="row">
			<div class="bigmsg">
				<h2><?php _e('Nobody yet.', 'pinc'); ?></h2>
			</div>
		</div>
	</div>
<?php } ?>
</div>

<div class="container">
	<div class="row subpage-title">
		<br /><br />
		<h1><?php _e('Top Users (Most Pins)', 'pinc'); ?></h1>
		<br />
	</div>
</div>

<div class="clearfix"></div>

<div class="container-fluid">
<div class="row">
<?php
$args = array(
	'order' => 'desc',
	'orderby' => 'post_count',
	'number' => '20'
 );

$top_user_postcount_query = new WP_User_Query($args);

if ($top_user_postcount_query->total_users > 0) {
	echo '<div id="user-profile-follow">';
	$count = 1;
	foreach ($top_user_postcount_query->results as $top_user_postcount) {
		if (count_user_posts($top_user_postcount->ID) > 0) {
		?>
		<div class="follow-wrapper">
			<span class="top-user-count<?php if ($count > 9) echo ' top-user-count-double-digit'; ?> top-user-count-alt2"><?php echo $count; $count++; ?></span>
			<a class="follow-user-name" title="<?php echo esc_attr($top_user_postcount->display_name); ?>" href="<?php echo home_url('/' . $wp_rewrite->author_base . '/') . $top_user_postcount->user_nicename; ?>/">
				<h4><?php echo $top_user_postcount->display_name; ?></h4>
				<p class="follow-user-meta"><?php $pins_count = count_user_posts($top_user_postcount->ID); echo $pins_count; ?> <?php if ($pins_count == 1) _e('Pin', 'pinc'); else _e('Pins', 'pinc'); ?> &#8226; <?php if ('' == $followers_count = get_user_meta($top_user_postcount->ID, '_Followers Count', true)) echo '0'; else echo $followers_count; ?> <?php if ($followers_count == 1) _e('Follower', 'pinc'); else _e('Followers', 'pinc'); ?></p>
				<div class="clearfix"></div>
			</a>
			
			<a class="follow-user-name" href="<?php echo home_url('/' . $wp_rewrite->author_base . '/') . $top_user_postcount->user_nicename; ?>/">
				<div class="follow-user-avatar">
					<?php echo get_avatar($top_user_postcount->ID, 105); ?>
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
						$top_user_postcount->ID
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
			if ($top_user_postcount->ID != $user_ID) {
			?>
			<span class="undisable_buttons">
				<button class="btn btn-success btn-block follow pinc-follow<?php $parent_board = get_user_meta($top_user_postcount->ID, '_Board Parent ID', true); if ($followed = pinc_followed($parent_board)) { echo ' disabled'; } ?>" data-author_id="<?php echo $top_user_postcount->ID; ?>" data-board_id="<?php echo $parent_board; ?>" data-board_parent_id="0" data-disable_others="no" type="button"><?php if (!$followed) { _e('Follow', 'pinc'); } else { _e('Unfollow', 'pinc'); } ?></button>
			</span>
			<?php } else { ?>
			<a class="btn btn-success btn-block follow" disabled="disabled"><?php _e('Myself!', 'pinc'); ?></a>
			<?php } ?>
		</div>
	<?php 
		}		
	}
	echo '</div></div>';
} else {
?>
	<div class="container">
		<div class="row">
			<div class="bigmsg">
				<h2><?php _e('Nobody yet.', 'pinc'); ?></h2>
			</div>
		</div>
	</div>
<?php } ?>
</div>
<?php get_footer(); ?>