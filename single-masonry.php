<div id="post-masonry" class="container-fluid">
	<div class="row">
	<?php
	$pnum = isset($_GET['pnum']) ? intval($_GET['pnum']) : 1;
	$tags_related = get_the_tags();
	
	if (!empty($tags_related)) {
		$tag_in = array();
		foreach ($tags_related as $tag_related) {
			array_push($tag_in, $tag_related->term_id);
		}

		$args = array(
			'tag__in' => $tag_in,
			'post__not_in' => array(get_the_ID()),
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
			),
			'paged' => $pnum
		);
		
		query_posts($args);
		$maxpage = $wp_query->max_num_pages;
		
		//if no posts with same tags, find posts with same category (MACSE)
		if ($wp_query->post_count == 0) {
			$category_in = array();
			foreach (get_the_category() as $category) {
				array_push($category_in, $category->cat_ID);
			}
			
			$args = array(
				'category__in' => $category_in,
				'post__not_in' => array(get_the_ID()),
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
				),
				'paged' => $pnum
			);
	
			query_posts($args);
			$maxpage = $wp_query->max_num_pages;			
		}
	} else {
		$category_in = array();
		foreach (get_the_category() as $category) {
			array_push($category_in, $category->cat_ID);
		}
		
		$args = array(
			'category__in' => $category_in,
			'post__not_in' => array(get_the_ID()),
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
			),
			'paged' => $pnum
		);

		query_posts($args);
		$maxpage = $wp_query->max_num_pages;
	}
	?>

	<?php if (have_posts()) { ?>
		<div id="ajax-loader-masonry" class="ajax-loader"></div>
	
		<h3 class="text-center"><?php _e('Related Pins', 'pinc'); ?></h3>
	<?php } ?>

	<div id="masonry" class="row">
		<?php $count_ad = 1; if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<?php if (of_get_option('frontpage1_ad') == $count_ad && of_get_option('frontpage1_ad_code') != '' && ($pnum == 1  || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb thumb-ad-wrapper">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage1_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage2_ad') == $count_ad && of_get_option('frontpage2_ad_code') != '' && ($pnum == 1  || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb thumb-ad-wrapper">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage2_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage3_ad') == $count_ad && of_get_option('frontpage3_ad_code') != '' && ($pnum == 1  || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb thumb-ad-wrapper">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage3_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage4_ad') == $count_ad && of_get_option('frontpage4_ad_code') != '' && ($pnum == 1 || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb thumb-ad-wrapper">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage4_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage5_ad') == $count_ad && of_get_option('frontpage5_ad_code') != '' && ($pnum == 1 || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb thumb-ad-wrapper">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage5_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php
		get_template_part('index-masonry-inc');
		$count_ad++;
		endwhile;
		?>

		<?php 
		endif;
		wp_reset_query(); 
		?>
	</div>
	
	<?php if ($maxpage != 0) { ?>
	<div id="navigation">
		<ul class="pager">			
			<?php if ($pnum != 1 && $maxpage >= $pnum) { ?>
			<li id="navigation-previous">
				<a href="<?php echo get_permalink() . '?pnum=' . ($pnum-1); ?>"><?php _e('&laquo; Previous', 'pinc') ?></a>
			</li>
			<?php } ?>
			
			<?php if ($maxpage != 1 && $maxpage != $pnum) { ?>
			<li id="navigation-next">
				<a href="<?php echo get_permalink() . '?pnum=' . ($pnum+1); ?>"><?php _e('Next &raquo;', 'pinc') ?></a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	</div>
</div>