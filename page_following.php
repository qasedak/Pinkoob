<?php
/*
Template Name: _following
*/
if (!is_user_logged_in()) { wp_redirect(wp_login_url($_SERVER['REQUEST_URI'])); exit; }
?>
<?php get_header(); global $user_ID; ?>

<div class="container">
	<div class="row subpage-title">
		<h1><?php _e('Following Feed', 'pinc'); ?></h1>
	</div>
</div>

<div class="container-fluid">
	<?php
	global $user_ID;
	
	$boards = get_user_meta($user_ID, '_Following Board ID');
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args = array(
		'tax_query' => array(
			array(
				'taxonomy' => 'board',
				'field' => 'id',
				'terms' => $boards[0],
				'include_children' => false
			)
		),
		'paged' => $paged
	);
	
	query_posts($args);

	get_template_part('index', 'masonry');
	get_footer();
?>