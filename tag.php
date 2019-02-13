<?php
if (is_home()) {
	$popularity = of_get_option('frontpage_popularity');
	if ($popularity == 'random') {
		$_SESSION["pinc_rand"] = rand();
		session_start();
	}
}

get_header(); global $user_ID;
?>
<div class="container-fluid">
	<?php
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	function filter_where($where = '') {
		$duration = '-' . of_get_option('frontpage_popularity_duration') . ' days';
		$where .= " AND post_date > '" . date('Y-m-d', strtotime($duration)) . "'";
		if (of_get_option('frontpage_popularity') == 'comments') {
			$where .= ' AND comment_count != 0';
		}
		return $where;
	}
	
	function random_posts_orderby($orderby_statement) {
		$seed = $_SESSION["pinc_rand"];

		if (empty($seed)) {
			$_SESSION["pinc_rand"] = rand();
		}

		$orderby_statement = 'RAND('.$seed.')';
		return $orderby_statement;	
	}
	
	if (is_home()) {
		if ('likes' == $popularity) {
			if (of_get_option('show_repins') != 'disable') {
				$args = array(
					'meta_key' => '_Likes Count',
					'meta_compare' => '>',
					'meta_value' => '0',
					'orderby' => 'meta_value_num',
					'order' => 'DESC',
					'paged' => $paged
				);				
			} else {
				$args = array(
					'meta_key' => '_Likes Count',
					'meta_compare' => '>',
					'meta_value' => '0',
					'orderby' => 'meta_value_num',
					'order' => 'DESC',
					'paged' => $paged,
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
			}
			add_filter('posts_where', 'filter_where');
			add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
			query_posts($args);
			remove_filter('posts_where', 'filter_where');
			remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');
		} else if ($popularity == 'repins') {	
			if (of_get_option('show_repins') != 'disable') {
				$args = array(
					'meta_key' => '_Repin Count',
					'meta_compare' => '>',
					'meta_value' => '0',
					'orderby' => 'meta_value_num',
					'order' => 'DESC',
					'paged' => $paged
				);
			} else {
				$args = array(
					'meta_key' => '_Repin Count',
					'meta_compare' => '>',
					'meta_value' => '0',
					'orderby' => 'meta_value_num',
					'order' => 'DESC',
					'paged' => $paged,
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
			}
			add_filter('posts_where', 'filter_where');
			add_filter('posts_orderby', 'pinc_meta_value_num_orderby');
			query_posts($args);
			remove_filter('posts_where', 'filter_where');
			remove_filter('posts_orderby', 'pinc_meta_value_num_orderby');
		} else if ($popularity == 'comments') {	
			if (of_get_option('show_repins') != 'disable') {
				$args = array(
					'orderby' => 'comment_count',
					'paged' => $paged
				);
			} else {
				$args = array(
					'orderby' => 'comment_count',
					'paged' => $paged,
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
			}
			add_filter('posts_where', 'filter_where');
			add_filter('posts_orderby', 'pinc_comments_orderby');
			query_posts($args);
			remove_filter('posts_where', 'filter_where');
			remove_filter('posts_orderby', 'pinc_comments_orderby');
		} else if ($popularity == 'random') {			
			if (of_get_option('show_repins') != 'disable') {
				$args = array(
					'paged' => $paged
				);
			} else {
				$args = array(
					'paged' => $paged,
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
			}
			add_filter('posts_orderby', 'random_posts_orderby');
			query_posts($args);
			remove_filter('posts_orderby', 'random_posts_orderby');
		} else {
			if (of_get_option('show_repins') != 'disable') {
				$args = array(
					'paged' => $paged
				);
			} else {
				$args = array(
					'paged' => $paged,
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
			}
			query_posts($args);
		}
	}
	
	if (is_category()) {
		$categories = array(get_query_var('cat'));
		$subcats = get_categories(array('child_of' => get_query_var('cat')));
		if ($subcats) {
			foreach ($subcats as $subcat) {
				array_push($categories, $subcat->cat_ID);
			}
		}
		
		if (of_get_option('show_repins') != 'disable') {			
			$args = array(
				'category__in' => $categories,
				'paged' => $paged
			);
		} else {
			$args = array(
				'category__in' => $categories,
				'paged' => $paged,
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
		}
		query_posts($args);	
	}
	
	if (is_tag()) {
		if (of_get_option('show_repins') != 'disable') {
			$args = array(
				'tag__in' => get_query_var('tag_id'),
				'paged' => $paged
			);
		} else {
			$args = array(
				'tag__in' => get_query_var('tag_id'),
				'paged' => $paged,
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
		}
		query_posts($args);	
	}

	get_template_part('index', 'masonry');
	get_footer();
?>