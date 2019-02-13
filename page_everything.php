<?php
/*
Template Name: _everything
*/
?>
<?php get_header(); ?>

<div class="container">
	<div class="row subpage-title">
		<h1><?php _e('Everything', 'pinc'); ?></h1>
	</div>
</div>

<div class="container-fluid">
	<?php
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' => 'post',
		'paged' => $paged
	);
	
	query_posts($args);

	get_template_part('index', 'masonry');
	get_footer();
?>