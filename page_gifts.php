<?php
/*
Template Name: _gifts
*/
?>
<?php get_header(); ?>

<?php
$prices = array(
//**************************************************
	//edit price range
	'0-10',
	'11-50',
	'51-100',
	'101-1000',
	'1001-2000',
	'2001-3000',
	'3001-1000000',
	//end edit price range
//***************************************************
);
?>

<div class="container">
	<div class="row subpage-title text-center">
		<h1><?php the_title(); ?></h1>

		<?php
		$categories = get_categories('exclude=' . implode(',', pinc_blog_cats()) . ', 1');

		if($categories){
			echo __('Category', 'pinc');
			if (!isset($_GET['category']) || $_GET['category'] == '') $active = ' gifts-categories-active';
			echo ' <a class="gifts-categories' . $active . '" href="' . get_permalink() . '?category=&amp;price=' . sanitize_text_field($_GET['price']) . '&amp;sort=' . sanitize_text_field($_GET['sort']) . '">' . __('All', 'pinc') . '</a>';
			foreach($categories as $category) {
			?>
				<a class="gifts-categories<?php if ($_GET['category'] == $category->category_nicename) echo ' gifts-categories-active'; ?>" href="<?php echo get_permalink() . '?category=' . $category->category_nicename; ?>&amp;price=<?php echo sanitize_text_field($_GET['price']); ?>&amp;sort=<?php echo sanitize_text_field($_GET['sort']); ?>"><?php echo $category->name; ?></a> 
		<?php }
		} ?>

		<div class="clearfix"></div>

		<?php _e('Price', 'pinc'); ?> 
		<?php
		if (!isset($_GET['price']) || $_GET['price'] == '') $active_price = ' gifts-categories-active';
			echo ' <a class="gifts-categories' . $active_price . '" href="' . get_permalink() . '?category=' . sanitize_text_field($_GET['category']) . '&amp;price=' . '&amp;sort=' . sanitize_text_field($_GET['sort']) . '">' . __('All', 'pinc') . '</a>';
		foreach ($prices as $price) {
		?>
			<a class="gifts-categories<?php if ($_GET['price'] == $price) echo ' gifts-categories-active'; ?>" href="<?php echo get_permalink() . '?category=' . sanitize_text_field($_GET['category']); ?>&amp;price=<?php echo $price; ?>&amp;sort=<?php echo sanitize_text_field($_GET['sort']); ?>"><?php echo $price; ?></a> 
		<?php } ?>
		
		<div class="clearfix"></div>

		<?php _e('Sort by', 'pinc'); ?> 
			<a class="gifts-categories<?php if (!isset($_GET['sort']) || $_GET['sort'] == '' || $_GET['sort'] == 'recent') echo ' gifts-categories-active'; ?>" href="<?php echo get_permalink() . '?category=' . sanitize_text_field($_GET['category']); ?>&amp;price=<?php echo sanitize_text_field($_GET['price']); ?>&amp;sort=recent"><?php _e('Most recent', 'pinc'); ?></a> 
			<a class="gifts-categories<?php if ($_GET['sort'] == 'popular') echo ' gifts-categories-active'; ?>" href="<?php echo get_permalink() . '?category=' . sanitize_text_field($_GET['category']); ?>&amp;price=<?php echo sanitize_text_field($_GET['price']); ?>&amp;sort=popular"><?php _e('Most popular', 'pinc'); ?></a> 
			<a class="gifts-categories<?php if ($_GET['sort'] == 'lowfirst') echo ' gifts-categories-active'; ?>" href="<?php echo get_permalink() . '?category=' . sanitize_text_field($_GET['category']); ?>&amp;price=<?php echo sanitize_text_field($_GET['price']); ?>&amp;sort=lowfirst"><?php _e('Price lowest', 'pinc'); ?></a> 
			<a class="gifts-categories<?php if ($_GET['sort'] == 'highfirst') echo ' gifts-categories-active'; ?>" href="<?php echo get_permalink() . '?category=' . sanitize_text_field($_GET['category']); ?>&amp;price=<?php echo sanitize_text_field($_GET['price']); ?>&amp;sort=highfirst"><?php _e('Price highest', 'pinc'); ?></a> 
		<br /></br />
	</div>
</div>

<div class="container-fluid">
	<?php
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	switch ($_GET['sort']) {
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
	case "lowfirst":
		$order = 'asc';
		$orderby = 'meta_value_num';
		$meta_key = '_Price';
	break;
	case "highfirst":
		$order = 'desc';
		$orderby = 'meta_value_num';
		$meta_key = '_Price';
	break;
	default:
		$order = '';
		$orderby = '';
		$meta_key = '';
	}


	if (isset($_GET['price']) && $_GET['price'] != '') {
		$price = explode('-', sanitize_text_field($_GET['price']));
		
		$args = array(
			'category_name' => sanitize_text_field($_GET['category']),
			'meta_query' => array(
				array(
					'key' => '_Price',
					'value' => $price,
					'type' => 'numeric',
					'compare' => 'BETWEEN'
				)
			),
			'orderby' => $orderby,
			'meta_key' => $meta_key,
			'order' => $order,
			'paged' => $paged
		);
	} else {
		$args = array(
			'category_name' => sanitize_text_field($_GET['category']),
			'meta_query' => array(
				array(
				'key' => '_Price',
				'compare' => 'EXISTS'
				)
			),
			'orderby' => $orderby,
			'meta_key' => $meta_key,
			'order' => $order,
			'paged' => $paged
		);
	}
	
	if ($orderby == 'meta_value_num')
		add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
	
	query_posts($args);
	
	if ($orderby == 'meta_value_num')
		remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');

	get_template_part('index', 'masonry');
	get_footer();
?>