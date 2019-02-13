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
<?php
if(of_get_option('daily_photo_feature') == 'on') {
	/* Photo of the Day Post ID */
	$id = of_get_option('mainpage_daily_photo');
	$hUrl = home_url( '/' );
	echo '
	<style>
	.dropdown.hidden-xs {
		display: none;
	}
	</style>
	<div class="rnd-image" style="background: url('. dailyImage($id) .') no-repeat center center fixed; background-size: cover; color: #fff; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover;">
	    <div class="container-main">
	        <div class="solgan-main">
	            <span class="main-title">'. get_bloginfo( 'name' ) .'</span>
	            <div>
	                <div>
						'. of_get_option('daily_photo_top_text') .'
	                </div>
	                <div>
	                    <div class="saerch-main">
							<form class="navbar-form search-main-form" method="get" id="searchform" action="'. esc_url( $hUrl ) .'">
								<div style="width: 100%;">
									<input id="s" class="form-control input-sm search-query" style="text-indent: 10px; font-size:14px; background-color: #fff" placeholder="'. __('Search free high-resolution photos','pinc') .'" name="s" value="" type="search">
									<input name="q" value="" type="hidden">
								</div>
								<button class="btn btn-sm" type="submit"><i class="fas fa-search"></i></button>
							</form>
	                    </div>
	                <div class="tags-main">
	                    <div class="tags-detail">
							<span>'. __('Common tags','pinc') .': </span>';  wpb_tag_cloud();
							echo '
	                    </div>
	                </div>
	                </div>
	            </div>
	        </div>
	        <div class="down-links">
	            <div class="tags-main">
	                <div class="tags-detail">
	                    <span>'. __('Photo by','pinc') .' <a href="'. esc_url( $hUrl ) .'user/'. author_url($id) .'/">'. author_name($id) .'</a></span>
	                </div>
	                <div class="tags-detail left-flo">
						'. of_get_option('daily_photo_bottom_text') .'
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	';
}

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