<?php
/*
Template Name: _source
*/
?>
<?php get_header(); global $user_ID, $wp_rewrite; ?>

<?php
if(isset($wp_query->query_vars['domain'])) {
	$source = rawurlencode($wp_query->query_vars['domain']);
} else {
	$source = '...';
}
?>
<div class="container">
	<div class="row subpage-title">
		<h1><?php _e('Pins from', 'pinc') ?> <a href="http://<?php echo $source; ?>" target="_blank"><?php echo $source; ?></a></h1>
	</div>
</div>

<div class="container-fluid">
	<div class="row">
	<?php
	$pnum = isset($_GET['pnum']) ? intval($_GET['pnum']) : 1;
	$args = array(
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => '_Photo Source Domain',
				'value' => $source
			),
			array(
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
		),
		'paged' => $pnum
	);
	
	query_posts($args);
	$maxpage = $wp_query->max_num_pages;
	?>

	<div id="ajax-loader-masonry" class="ajax-loader"></div>

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
		else :
		?>
		<div class="container">
			<div class="row">
				<div class="bigmsg">
					<h2><?php _e('Nothing yet.', 'pinc'); ?></h2>
				</div>
			</div>
		</div>
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
				<a href="<?php $permalink = get_permalink(); if (substr($permalink,-1) == '/') { $permalink = substr($permalink,0,-1); } echo $permalink . '/' . $source . '?pnum=' . ($pnum-1); ?>"><?php _e('&laquo; Previous', 'pinc') ?></a>
			</li>
			<?php } ?>
			
			<?php if ($maxpage != 1 && $maxpage != $pnum) { ?>
			<li id="navigation-next">
				<a href="<?php $permalink = get_permalink(); if (substr($permalink,-1) == '/') { $permalink = substr($permalink,0,-1); } echo $permalink . '/' . $source . '?pnum=' . ($pnum+1); ?>"><?php _e('Next &raquo;', 'pinc') ?></a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	</div>
</div>

<div class="modal" id="post-lightbox" tabindex="-1" aria-hidden="true" role="article"></div>	

<?php get_footer(); ?>