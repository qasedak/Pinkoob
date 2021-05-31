<!DOCTYPE html>
<html <?php language_attributes(); ?> prefix="og: http://ogp.me/ns#">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

	<title><?php wp_title('', true, 'right'); if (!is_home() && !is_front_page()) echo ' | '; bloginfo( 'name' ); $site_description = get_bloginfo('description', 'display'); if ($site_description && (is_home() || is_front_page())) echo ' | ' . $site_description; ?></title>
	<?php
	global $user_ID, $user_identity, $post;
	if (is_single() && $post->post_content == '' && !function_exists('wpseo_init')) {
		$meta_categories = get_the_category($post->ID);

		foreach ($meta_categories as $meta_category) {
			$meta_category_name = $meta_category->name;
		}

		if (pinc_get_post_board()) {
			$meta_board_name = pinc_get_post_board()->name;
		} else {
			$meta_board_name = __('Untitled', pinc);
		}
		?>
		<meta name="<?php echo 'descript' . 'ion'; //bypass yoast seo check ?>" content="<?php _e('Pinned onto', 'pinc'); ?> <?php echo esc_attr($meta_board_name); ?> <?php _e('Board in', 'pinc') ?> <?php echo esc_attr($meta_category_name); ?> <?php _e('Category', 'pinc'); ?>" />
		<?php
	}
	?>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/bootstrap/bootstrap-tagsinput.css">
	<?php wp_head(); ?>
	<?php eval('?>' . of_get_option('header_scripts')); ?>
	<style>
	<?php colorGen(); ?>
	</style>

	<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js"></script>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>

<body <?php body_class(); ?>>
	<noscript>
		<style type="text/css" media="all">#masonry { visibility: visible !important; }</style>
	</noscript>

	<?php if (of_get_option('facebook_comments') != 'disable') { ?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1<?php if (get_option('wsl_settings_Facebook_app_id')) echo '&appId=' . get_option('wsl_settings_Facebook_app_id'); ?>";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<?php } ?>
	<?php $header = of_get_option('header'); ?>	<nav id="topmenu" class="navbar
	<?php /* Auto Color by macse */ global $colorScheme;
	if ($colorScheme == 'dark') echo ' navbar-inverse'; else echo ' navbar-default' ?>
	 navbar-fixed-top" style="background-image: url('<?php echo $header ?>'); background-repeat: repeat;">
		<div class="container widthFix">
			<div id="top-menu-right-mobile" class="visible-xs">
			<?php if ($user_ID) { ?>
				<?php
				$notifications_count = get_user_meta($user_ID, 'pinc_user_notifications_count', true);
				if ($notifications_count == '') $notifications_count = '0';
				?>
				<a id="top-notifications-mobile" class="<?php if ($notifications_count != '0') echo 'top-notifications-mobile-count-nth'; ?>" href="<?php echo home_url('/notifications/'); ?>"><?php echo $notifications_count; ?></a>
				<?php if (current_user_can('edit_posts')) { ?>
					<a id="top-add-button-mobile" href="<?php echo home_url('/itm-settings/'); ?>"><i class="fas fa-plus"></i></a>
				<?php } ?>
			<?php } else { ?>
				<a id="top-add-button-mobile" href="<?php echo home_url('/login/'); ?>"><i class="fas fa-user"></i></a>
			<?php } ?>
			</div>

			<div class="navbar-header">
				<button class="navbar-toggle" data-toggle="collapse" data-target="#nav-main" type="button">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<?php global $logo; ?>
				<a class="navbar-brand<?php if ($logo != '') { echo ' logo'; } ?>" href="<?php echo home_url('/'); ?>">
				<?php if ($logo != '') { ?>
					<img src="<?php echo $logo ?>" alt="Logo" />
				<?php } else {
					bloginfo('name');
				}
				?>
				</a>
			</div>

			<div id="nav-main" class="collapse navbar-collapse">
				<ul id="menu-top-right" class="nav navbar-nav navbar<?php if(is_rtl()){echo"-left";}else{echo"-right";} ?>">
				<?php if ($user_ID) { ?>
					<?php if (current_user_can('edit_posts')) { ?>
					<li class="hidden-xs">
						<a id="icon-add-pin" rel="tooltip" data-placement="bottom" title="<?php _e('Add Pin', 'pinc'); ?>" href="<?php echo home_url('/itm-settings/'); ?>">
							<span class="fa-stack">
							<i class="fas fa-file fa-stack-2x" style="font-size: 18px; top: 3px;"></i>
								<i class="fas fa-plus fa-stack-1x fa-inverse" style="font-size: 11px; color: #222; top: 3px;"></i>
							</span>
						</a>
					</li>
					<li class="hidden-xs">
						<a id="icon-add-board" rel="tooltip" data-placement="bottom" title="<?php _e('Add Board', 'pinc'); ?>" href="<?php echo home_url('/grp-settings/'); ?>">
							<span class="fa-stack">
							<i class="fas fa-folder-plus fa-stack-2x"></i>
							</span>
						</a>
					</li>
					<?php } ?>

					<li id="dropdown-user-settings" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" data-target="" href=""><span class="hidden-xs"><?php echo get_avatar($user_ID, '26'); ?></span><span class="visible-xs pull-left"><?php echo $user_identity; ?>&nbsp;</span> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php if (current_user_can('edit_posts')) { ?>
							<li class="visible-xs"><a href="<?php echo home_url('/itm-settings/'); ?>"><?php _e('Add Pin', 'pinc'); ?></a></li>
							<li class="visible-xs"><a href="<?php echo home_url('/grp-settings/'); ?>"><?php _e('Add Board', 'pinc'); ?></a></li>
							<?php } ?>
							<li><a href="<?php echo home_url('/following/'); ?>"><i class="fas fa-list-ul fa-fw hidden-xs"></i> <?php _e('Following Feed', 'pinc'); ?></a></li>
							<li><a href="<?php echo get_author_posts_url($user_ID); ?>"><i class="fas fa-user fa-fw hidden-xs"></i> <?php _e('Profile &amp; Pins', 'pinc'); ?></a></li>
							<li><a href="<?php echo home_url('/settings/'); ?>"><i class="fas fa-cog fa-fw hidden-xs"></i> <?php _e('Settings', 'pinc'); ?></a></li>
							<?php if (current_user_can('administrator') || current_user_can('editor')) { ?>
							<li><a href="<?php echo home_url('/wp-admin/'); ?>"><i class="fab fa-wordpress fa-fw hidden-xs"></i> <?php _e('WP Admin', 'pinc'); ?></a></li>
							<?php } ?>
							<li><a href="<?php echo home_url('/login/?action=logout&amp;nonce=' . wp_create_nonce('logout')); ?>"><i class="fas fa-sign-out-alt fa-fw hidden-xs"></i> <?php _e('Log Out', 'pinc'); ?></a></li>
						</ul>
					</li>
					<li id="user-notifications-count" class="hidden-xs"><a<?php if ($notifications_count != '0') echo ' class="user-notifications-count-nth"'; ?> href="<?php echo home_url('/notifications/'); ?>" rel="tooltip" data-placement="bottom" title="<?php _e('Notifications', 'pinc'); ?>"><span><?php echo $notifications_count; ?></span></a></li>
				<?php } else { ?>
					<li class="visible-xs"><a href="<?php echo home_url('/signup/'); ?>"><?php _e('Sign Up', 'pinc'); ?></a></li>
					<li class="visible-xs"><a href="<?php echo wp_login_url($_SERVER['REQUEST_URI']); ?>"><?php _e('Login', 'pinc'); ?></a></li>
					<li class="hidden-xs" id="loginbox-wrapper"><button id="loginbox" class="btn btn-default navbar-btn" data-wsl='<?php if (function_exists('wsl_activate')) { do_action('wordpress_social_login'); echo '<hr />'; } ?>' aria-hidden="true" type="button"><?php _e('Login', 'pinc'); ?></button></li>
				<?php } ?>
				</ul>

				<?php
				if (has_nav_menu('top_nav')) {
					wp_nav_menu(array('theme_location' => 'top_nav', 'menu_class' => 'nav navbar-nav', 'depth' => '3'));
				} else {
					echo '<ul id="menu-top" class="nav navbar-nav">';
					wp_list_pages('title_li=&depth=0&sort_column=menu_order' );
					echo '</ul>';
				}
				?>

				<?php if ( is_home() == false || of_get_option('daily_photo_feature') == 'off' ) { ?>
					<ul class="nav navbar-nav top-search">
						<form class="navbar-form" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
							<input id="s" class="form-control input-sm search-query" type="search" placeholder="<?php _e('Search', 'pinc'); ?>" name="s" value="<?php the_search_query(); ?>">
							<input type="hidden" name="q" value="<?php echo $_GET['q']; ?>"/>
							<button class="btn btn-success btn-sm" type="submit"><i class="fas fa-search"></i></button>
						</form>
					</ul>
				<?php }?>

				<ul id="topmenu-icons-wrapper" class="nav navbar-nav">
					<?php if ('' != $facebook_icon_url = of_get_option('facebook_icon_url')) { ?>
					<li><a class="topmenu-icons" href="<?php echo $facebook_icon_url; ?>" rel="tooltip" data-placement="bottom" title="<?php _e('Find us on Facebook', 'pinc'); ?>" target="_blank"><i class="fab fa-facebook"></i></a></li>
					<?php } ?>

					<?php if ('' != $twitter_icon_url = of_get_option('twitter_icon_url')) { ?>
					<li><a class="topmenu-icons" href="<?php echo $twitter_icon_url; ?>" rel="tooltip" data-placement="bottom" title="<?php _e('Follow us on Twitter', 'pinc'); ?>" target="_blank"><i class="fab fa-twitter"></i></a></li>
					<?php } ?>

					<li><a class="topmenu-icons" href="<?php bloginfo('rss2_url'); ?>" rel="tooltip" data-placement="bottom" title="<?php _e('Subscribe to RSS Feed', 'pinc'); ?>"><i class="fas fa-rss"></i></a></li>
				</ul>

				<form class="navbar-form visible-xs" method="get" id="searchform-mobile" action="<?php echo home_url('/'); ?>">
					<input type="text" class="form-control search-query" placeholder="<?php _e('Search', 'pinc'); ?>" name="s" value="<?php the_search_query(); ?>">
					<input type="hidden" name="q" value="<?php echo $_GET['q']; ?>"/>
				</form>
			</div>
		</div>
	</nav>

	<?php if (!$user_ID) { ?>
	<div id="top-message-wrapper">
		<div id="top-message" class="container">
			<div class="pull<?php if(is_rtl()){echo"-left";}else{echo"-right";} ?>">
				<a class="btn btn-success" href="<?php echo home_url('/signup/'); ?>"><?php _e('Sign Up', 'pinc'); ?></a>
			</div>
			<div class="top-message-left"><?php eval('?>' . of_get_option('top_message')); ?></div>
		</div>
	</div>
	<?php } ?>

	<?php if (of_get_option('header_ad') != '' && !is_page_template('page_cp_pins.php') && !is_page_template('page_cp_boards.php') && !is_page_template('page_cp_settings.php')) { ?>
	<div id="header-ad" class="container-fluid">
		<div class="row">
			<?php eval('?>' . of_get_option('header_ad')); ?>
		</div>
	</div>
	<?php } ?>

	<?php if (is_search() || is_category() || is_tag()) { ?>
	<div class="container subpage-title">
		<?php if (is_search()) { ?>
			<h1><?php if( get_search_query() != "") { _e('Search results for', 'pinc'); echo' "'; the_search_query(); echo'"';} else { _e('Search query should not be empty!', 'pinc');} ?></h1>
		<?php } else if (is_category()) { ?>
			<h1<?php if (in_category(pinc_blog_cats())) echo ' style="text-align:left;"'; ?>><?php single_cat_title(); ?></h1>
			<?php if (category_description()) { ?>
				<?php echo category_description(); ?>
			<?php } ?>

			<?php
			$current_cat = get_category(get_query_var('cat'));
			if ($current_cat->parent == 0) {
				$is_parent_cat = true;
				$parent_cat_name = $current_cat->name;
				$parent_cat_id = $current_cat->cat_ID;
			} else {
				$is_parent_cat = false;
				$parent_cat = get_category($current_cat->parent);
				$parent_cat_name = $parent_cat->name;
				$parent_cat_id = $parent_cat->cat_ID;
			}
			$categories = get_categories('hide_empty=0&child_of=' . $parent_cat_id);

			if ($categories || !$is_parent_cat) {
				echo '<div class="text-center">';
				if (!$is_parent_cat) {
					echo ' <a class="popular-categories" href="' . get_category_link($parent_cat_id) . '">&laquo; ' . $parent_cat_name . '</a>';
				}
				foreach($categories as $category) {
				?>
				<a class="popular-categories<?php if (is_category($category->cat_ID)) echo ' popular-categories-active'; ?>" href="<?php echo get_category_link($category->cat_ID); ?>"><?php echo $category->name; ?></a>
				<?php }
				echo '</div><br />';
			} ?>
		<?php } else if (is_tag()) { ?>
			<h1>#<?php single_tag_title(); ?></h1>
			<?php if (tag_description()) { ?>
				<?php echo tag_description(); ?>
			<?php } ?>
		<?php } ?>
	</div>
	<?php } ?>
