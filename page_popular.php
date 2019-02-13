<?php
/*
Template Name: _popular
*/
?>
<?php get_header(); ?>

<div class="container">
	<div class="row subpage-title">
		<h1><?php _e('Popular', 'pinc'); ?></h1>

		<?php
		$categories = get_categories('exclude=' . implode(',', pinc_blog_cats()) . ', 1');

		if ($categories){
			echo '<div class="text-center">' . __('Category', 'pinc');
			if (!isset($_GET['category'])) $active = ' popular-categories-active';
			echo ' <a class="popular-categories' . $active . '" href="' . get_permalink() . '">' . __('All', 'pinc') . '</a>';
			foreach($categories as $category) {
			?>
				<a class="popular-categories<?php if ($_GET['category'] == $category->category_nicename) echo ' popular-categories-active'; ?>" href="<?php echo get_permalink(); ?>?category=<?php echo $category->category_nicename; ?>"><?php echo $category->name; ?></a> 
		<?php }
			echo '</div><br />';
		} ?>
	</div>
</div>

<div class="container-fluid">
	<?php
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	function filter_where($where = '') {
		$duration = '-' . of_get_option('popularity_duration') . ' days';
		$where .= " AND post_date > '" . date('Y-m-d', strtotime($duration)) . "'";
		if (of_get_option('popularity') == 'comments') {
			$where .= ' AND comment_count != 0';
		}
		return $where;
	}
	
	if ('likes' == $popularity = of_get_option('popularity')) {
		$args = array(
			'meta_key' => '_Likes Count',
			'meta_compare' => '>',
			'meta_value' => '0',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'category_name' => sanitize_text_field($_GET['category']),
			'paged' => $paged
		);
		add_filter('posts_where', 'filter_where');
		add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
		query_posts($args);
		remove_filter('posts_where', 'filter_where');
		remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');
	} else if ($popularity == 'repins') {	
		$args = array(
			'meta_key' => '_Repin Count',
			'meta_compare' => '>',
			'meta_value' => '0',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'category_name' => sanitize_text_field($_GET['category']),
			'paged' => $paged
		);
		add_filter('posts_where', 'filter_where');
		add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
		query_posts($args);
		remove_filter('posts_where', 'filter_where');
		remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');
	} else if ($popularity == 'comments') {
		$args = array(
			'orderby' => 'comment_count',
			'category_name' => sanitize_text_field($_GET['category']),
			'paged' => $paged
		);
		add_filter('posts_where', 'filter_where');
		add_filter('posts_orderby', 'pinc_comments_orderby');
		query_posts($args);
		remove_filter('posts_where', 'filter_where');
		remove_filter('posts_orderby', 'pinc_comments_orderby');
	} else {
		$args = array(
			'category_name' => sanitize_text_field($_GET['category']),
			'paged' => $paged
		);
		query_posts($args);
	}

	get_template_part('index', 'masonry');
	get_footer();
?>