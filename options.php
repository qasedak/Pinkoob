<?php
function optionsframework_option_name() {
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

add_action('admin_init', 'optionscheck_change_santiziation', 100);
  
function optionscheck_change_santiziation() {
    remove_filter('of_sanitize_textarea', 'of_sanitize_textarea');
	remove_filter('of_sanitize_text', 'sanitize_text_field');
    add_filter('of_sanitize_textarea', 'custom_sanitize_input');
    add_filter('of_sanitize_text', 'custom_sanitize_input');
}
  
function custom_sanitize_input($input) {
    return $input;
}

if (isset($_GET['settings-updated']) && of_get_option('default_avatar') != '') {
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	$image = wp_get_image_editor(get_home_path() . str_replace(home_url('/'), '', of_get_option('default_avatar')));
	$ext = explode('.', of_get_option('default_avatar'));
	$ext = strtolower(array_pop($ext));
	$upload_dir = wp_upload_dir();
	
	if (!is_wp_error($image)) {
		$image->resize(96, 96, true);
		$image->save($upload_dir['basedir'] . '/avatar-96x96.' . $ext);
		update_option('pinc_avatar_96', $upload_dir['baseurl'] . '/avatar-96x96.' . $ext);
		$image->resize(48, 48, true);
		$image->save($upload_dir['basedir'] . '/avatar-48x48.' . $ext);
		update_option('pinc_avatar_48', $upload_dir['baseurl'] . '/avatar-48x48.' . $ext);
	}
} else if (isset($_GET['settings-updated']) && of_get_option('default_avatar') == '') {
	$ext = explode('.', get_option('pinc_avatar_48'));
	$ext = array_pop($ext);
	$upload_dir = wp_upload_dir();

	delete_option('pinc_avatar_48');
	delete_option('pinc_avatar_96');

	if (file_exists($upload_dir['basedir'] . '/avatar-48x48.' . $ext))
		unlink($upload_dir['basedir'] . '/avatar-48x48.' . $ext);

	if (file_exists($upload_dir['basedir'] . '/avatar-96x96.' . $ext))
		unlink($upload_dir['basedir'] . '/avatar-96x96.' . $ext);
}

function optionsframework_options() {
	// Pull all the parent categories into an array	
	$options_categories = array('');
	$options_categories_obj = get_categories('hide_empty=0&exclude=1');
	foreach ($options_categories_obj as $category) {
		if ($category->category_parent == 0) {
			$options_categories[$category->cat_ID] = $category->cat_name;
		}
	}
	
	// Pull all pages into an array	
	$options_pages = array('');
	$options_pages_obj = get_pages();
	foreach ($options_pages_obj as $page) {
			$options_pages[$page->ID] = $page->post_title;
	}
	
	$options = array();
	
	$options[] = array(
		'name' => __('General Settings', 'pinc'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Photo of the day', 'pinc'),
		'id' => 'daily_photo_feature',
		'std' => 'on',
		'type' => 'radio',
		'options' => array('on' => __('Enable', 'pinc'), 'off' => __('Disable', 'pinc'), ));

	$options[] = array(
		'name' => __('Photo of the day post','pinc'),
		'desc' => __('the post ID of the photo you want to display as dailyphoto (use "0" for the latest photo and use "rnd" to get random photos)','pinc'),
		'id' => 'mainpage_daily_photo',
		'std' => '0',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => __('Photo of the day bottom text','pinc'),
		'desc' => __('HTML allowed','pinc'),
		'id' => 'daily_photo_bottom_text',
		'std' => 'متن برای نمایش زیر تصویر روزانه',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Photo of the day top text','pinc'),
		'desc' => __('HTML allowed','pinc'),
		'id' => 'daily_photo_top_text',
		'std' => '<p class="subtitle-main-up">تصاویر رایگان و زیبا.</p><p class="subtitle-main-down">قصد فروش تصاویرتان را دارید؟ وارد <a href="https://store.pinkoob.com">فروشگاه پینکوب</a> شوید!</p>',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Show Frontpage\'s Pins Based On', 'pinc'),
		'id' => 'frontpage_popularity',
		'std' => 'showall',
		'type' => 'radio',
		'options' => array('likes' => __('Most Likes', 'pinc'), 'repins' => __('Most Repins', 'pinc'), 'comments' => __('Most Comments', 'pinc'), 'random' => __('Random', 'pinc'), 'showall' => __('Show All', 'pinc')));

	$options[] = array(
		'desc' => __(' Over Last X Days (only for Most Likes/Repins/Comments)', 'pinc'),
		'id' => 'frontpage_popularity_duration',
		'std' => '180',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => __('Show Popular Page\'s Pins Based On', 'pinc'),
		'id' => 'popularity',
		'std' => 'showall',
		'type' => 'radio',
		'options' => array('likes' => __('Most Likes', 'pinc'), 'repins' => __('Most Repins', 'pinc'), 'comments' => __('Most Comments', 'pinc'), 'showall' => __('Show All', 'pinc')));

	$options[] = array(
		'desc' => __(' Over Last X Days (only for Most Likes/Repins/Comments)', 'pinc'),
		'id' => 'popularity_duration',
		'std' => '180',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'desc' => __('When your site is new, select "Show All" so that the frontpage & popular page will not be blank. As the pins get more likes, repins or comments, select as appropriate.', 'pinc'),
		'type' => 'info');
		
	$options[] = array(
		'name' => __('Header Background', 'pinc'),
		'desc' => __('Header background image'),
		'id' => 'header',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Header Logo Image (Day Theme)', 'pinc'),
		'desc' => __('Logo height should be 50px. Width is flexible. Leave blank to use site title text.', 'pinc'),
		'id' => 'logo',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Header Logo Image (Night theme)', 'pinc'),
		'desc' => __('Logo height should be 50px. Width is flexible. Leave blank to use site title text.', 'pinc'),
		'id' => 'logo_night',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Pin It Button Image', 'pinc'),
		'desc' => sprintf( __('Leave blank to use text for Pin It Button at <a href="%s">Add > Pin</a>.', 'pinc'), home_url('/itm-settings/') ),
		'id' => 'pinit_button',
		'type' => 'upload');
		
	$options[] = array(
		'name' => __('Default Avatar', 'pinc'),
		'desc' => sprintf( __('Recommended size: 96 x 96px. Leave blank to use <a target="_blank" href="%s/img/avatar-48x48.png">Mystery Man</a>', 'pinc'), get_template_directory_uri() ),
		'id' => 'default_avatar',
		'type' => 'upload');
	
	$options[] = array(
		'name' => __('Top Header Message for Non Logged-in Users', 'pinc'),
		'id' => 'top_message',
		'std' => 'Organize and share the things you like.',
		'type' => 'text');

	$options[] = array(
		'name' => __('Social Icon Urls', 'pinc'),
		'desc' => __('Facebook Url. Leave blank to hide facebook icon in header', 'pinc'),
		'id' => 'facebook_icon_url',
		'std' => 'http://facebook.com/#',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Twitter Url. Leave blank to hide twitter icon in header', 'pinc'),
		'id' => 'twitter_icon_url',
		'std' => 'http://twitter.com/#',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Frontpage Comments Number', 'pinc'),
		'desc' => __('Enter 0 to hide comments on frontpage', 'pinc'),
		'id' => 'frontpage_comments_number',
		'std' => '2',
		'type' => 'text');

	$options[] = array(
		'name' => __('Frontpage pin title words limit', 'pinc'),
		'desc' => __('Enter 0 to hide title on frontpage', 'pinc'),
		'id' => 'pin_title_words_limit',
		'std' => '5',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Facebook Comments', 'pinc'),
		'desc' => __('If enabled, Facebook Comments box will be displayed in single post', 'pinc'),
		'id' => 'facebook_comments',
		'std' => 'enable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));
		
	$options[] = array(
		'name' => __('Show Repins', 'pinc'),
		'desc' => __('If disabled, repins are hidden from the front, categories and tags page', 'pinc'),
		'id' => 'show_repins',
		'std' => 'enable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));
		
	$options[] = array(
		'name' => __('Infinite Scroll', 'pinc'),
		'desc' => __('If disabled, the normal pagination links are displayed. The theme is compatible with the <a href="http://wordpress.org/extend/plugins/wp-pagenavi/">WP-PageNavi</a> plugin, but must be deactivated if you re-enable infinite scroll.', 'pinc'),
		'id' => 'infinitescroll',
		'std' => 'enable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));

	$options[] = array(
		'name' => __('Lightbox', 'pinc'),
		'desc' => __('If disabled, clicking on the frontpage thumbnails will go to the single post instead of opening in a lightbox.', 'pinc'),
		'id' => 'lightbox',
		'std' => 'enable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));
		
	$options[] = array(
		'name' => __('Form Title & Description', 'pinc'),
		'desc' => __('For use in add/edit pins forms', 'pinc'),
		'id' => 'form_title_desc',
		'std' => 'single',
		'type' => 'radio',
		'options' => array('single' => __('Show Single Description Field', 'pinc'), 'separate' => __('Show Separate Title & Description Field', 'pinc')));

	$options[] = array(
		'name' => __('Allow HTML Code', 'pinc'),
		'desc' => __('If enabled, the html editor will be displayed on the description field.', 'pinc'),
		'id' => 'htmltags',
		'std' => 'disable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));
		
	$options[] = array(
		'name' => __('Allow Tags Input', 'pinc'),
		'desc' => __('If enabled, users can add tags to pins.', 'pinc'),
		'id' => 'posttags',
		'std' => 'disable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));
		
	$options[] = array(
		'name' => __('Allow Source Input', 'pinc'),
		'desc' => __('If enabled, users can add source url to pins.', 'pinc'),
		'id' => 'source_input',
		'std' => 'disable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));

	$options[] = array(
		'name' => __('Allow Price Input/Currency Symbol', 'pinc'),
		'desc' => __('Default is $. Leave blank to disable price input and price tag shown in top left corner of the pin.', 'pinc'),
		'id' => 'price_currency',
		'std' => '$',
		'type' => 'text');
		
	$options[] = array(
		'id' => 'price_currency_position',
		'std' => 'left',
		'type' => 'radio',
		'options' => array('left' => __('Currency symbol on the left', 'pinc'), 'right' => __('Currency symbol on the right', 'pinc')));

	$options[] = array(
		'name' => __('Allow Users to Delete Own Account', 'pinc'),
		'desc' => sprintf( __('If enabled, users can delete their own account in the <a href="%s">Settings</a> page. Administrator will always see the Delete Account link.', 'pinc'), home_url('/settings/') ),
		'id' => 'delete_account',
		'std' => 'disable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'pinc'), 'disable' => __('Disable', 'pinc')));

	$options[] = array(
		'name' => __('Auto Create These Boards for New Users', 'pinc'),
		'desc' => __('Enter board names seperated by commas e.g My First Board, Gadgets, Humour', 'pinc'),
		'id' => 'auto_create_boards_name',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'desc' => sprintf( __('Enter category ID for each board as above e.g 1, 4, 2. You can find the category ID at <a href="%s">Posts > Categories</a>', 'pinc'), admin_url('edit-tags.php?taxonomy=category')),
		'id' => 'auto_create_boards_cat',
		'std' => '',
		'type' => 'text');

	/* $options[] = array(
		'name' => __('Auto Follow These Users for New Users', 'pinc'),
		'desc' => __('Enter user IDs seperated by commas e.g 1, 23, 45', 'pinc'),
		'id' => 'auto_default_follows',
		'type' => 'text');
	*/

	$options[] = array(
		'name' => __('Outgoing Email Settings', 'pinc'),
		'desc' => __('Email address', 'pinc'),
		'id' => 'outgoing_email',
		'std' => 'noreply@' . parse_url(home_url(), PHP_URL_HOST),
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('From whom', 'pinc'),
		'id' => 'outgoing_email_name',
		'std' => get_bloginfo('name'),
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Your email "From" field. For user email notifications for likes, follows, comments etc.', 'pinc'),
		'type' => 'info');

	$options[] = array(
		'name' => __('Prune Schedule', 'pinc'),
		'desc' => __('posts every', 'pinc'),
		'id' => 'prune_postnumber',
		'std' => '5',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('mins', 'pinc'),
		'id' => 'prune_duration',
		'std' => '5',
		'type' => 'text');

	$options[] = array(
		'desc' => __('When a user delete a pin or a board, the posts are marked as prune for deletion later. Depending on your server load, you can adjust how often the system delete these posts.', 'pinc'),
		'type' => 'info');
		
	$options[] = array(
		'name' => __('Captcha for Register/Login Form', 'pinc'),
		'desc' => __('reCAPTCHA Site Key', 'pinc'),
		'id' => 'captcha_public',
		'std' => '',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('reCAPTCHA Secret Key', 'pinc'),
		'id' => 'captcha_private',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'desc' => __('Google Web Interface <a href="https://sites.google.com/site/tomihasa/google-language-codes">Language Code</a>', 'pinc'),
		'id' => 'captcha_lang',
		'std' => 'fa',
		'type' => 'text');

	$options[] = array(
		'desc' => __('Sign up for the keys at <a href="http://www.google.com/recaptcha/">Google reCAPTCHA</a>. Leave blank to hide captcha.', 'pinc'),
		'type' => 'info');
		
	$options[] = array(
		'name' => __('Terms of Service Page for Register Form', 'pinc'),
		'desc' => sprintf( __('Go to <a href="%s">Pages > Add New</a> to create the page first. Leave blank if you do not need users to tick a box to agree to terms of service before registering.', 'pinc'), admin_url('post-new.php?post_type=page')),
		'id' => 'register_agree',
		'type' => 'select',
		'options' => $options_pages);
		
	$options[] = array(
		'name' => __('Category For Blog', 'pinc'),
		'desc' => __('Hide blog category from the Add/Edit Board page. Leave blank if you do not need a blog yet.', 'pinc'),
		'id' => 'blog_cat_id',
		'std' => '0',
		'type' => 'select',
		'options' => $options_categories);
		
	$options[] = array(
		'name' => __('Header Scripts', 'pinc'),
		'desc' => __('Add scripts before the &lt;/head> tag', 'pinc'),
		'id' => 'header_scripts',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Footer Scripts', 'pinc'),
		'desc' => __('Add scripts before the &lt;/body> tag e.g Google Analytics', 'pinc'),
		'id' => 'footer_scripts',
		'type' => 'textarea');

	/*
	$options[] = array(
		'name' => __('Browser Extension Pack ID', 'pinc'),
		'desc' => __('If you purchase the optional Browser Extension Pack, enter the ID to activate (<a href="" target="_blank">how to get ID</a>).', 'pinc'),
		'id' => 'browser-extension-id',
		'std' => '',
		'type' => 'text');
	*/

	$options[] = array(
		'name' => __('Theme Colors', 'pinc'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Theme color style', 'pinc'),
		'id' => 'theme_color_mod',
		'std' => 'day',
		'type' => 'radio',
		'options' => array('day', 'night', 'day/night'));	

	$options[] = array(
		'name' => __('Night mode start and end time','pinc'),
		'desc' => __('Set time if you have selected "day/night" in <b>Theme color style</b> section','pinc'));
		
	$options[] = array(
		'desc' => __('Night start time (Use this format 00:00:00)','pinc'),
		'id' => 'night_start',
		'std' => '20:00:00',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'desc' => __('Night end time (Use this format 00:00:00)','pinc'),
		'id' => 'night_end',
		'std' => '05:00:00',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => __('Accent Color','pinc'),
		'desc' => __('Accent colors are colors that are used for emphasis in a color scheme. These colors can often be bold or vivid and are used sparingly, to emphasize, contrast or create rhythm.','pinc'),
		'id' => 'accent_color_one',
		'std' => '#b92b2b',
		'type' => 'color');

	$options[] = array(
		'name' => __('Theme Fonts', 'pinc'),
		'type' => 'heading');
	
	$options[] = array(
		'name' => __('Headings font', 'pinc'),
		'desc' => __('leave it empty to use default font','pinc'),
		'id' => 'heading_font_family',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Body and paragraph font', 'pinc'),
		'desc' => __('leave it empty to use default font','pinc'),
		'id' => 'body_font_family',
		'type' => 'upload');

	$options[] = array(
		'name' => __('Advertising Sections', 'pinc'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Header Advertisement', 'pinc'),
		'desc' => __('HTML / PHP / Javascript allowed.', 'pinc'),
		'id' => 'header_ad',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Single Post - Above Photo', 'pinc'),
		'desc' => __('Recommended Width: 700px or lower. HTML / PHP / Javascript allowed. Note: Javascript based ads like adsense may not appear in the lightbox, only in single posts.', 'pinc'),
		'id' => 'single_pin_above_ad',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Single Post - Below Photo', 'pinc'),
		'desc' => __('Recommended Width: 700px or lower. HTML / PHP / Javascript allowed. Note: Javascript based ads like adsense may not appear in the lightbox, only in single posts', 'pinc'),
		'id' => 'single_pin_below_ad',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #1', 'pinc'),
		'desc' => __('Display before X(th) thumbnail', 'pinc'),
		'id' => 'frontpage1_ad',
		'std' => '1',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'pinc'),
		'id' => 'frontpage1_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #2', 'pinc'),
		'desc' => __('Display at X(th) position', 'pinc'),
		'id' => 'frontpage2_ad',
		'std' => '2',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'pinc'),
		'id' => 'frontpage2_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #3', 'pinc'),
		'desc' => __('Display at X(th) position', 'pinc'),
		'id' => 'frontpage3_ad',
		'std' => '3',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'pinc'),
		'id' => 'frontpage3_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #4', 'pinc'),
		'desc' => __('Display at X(th) position', 'pinc'),
		'id' => 'frontpage4_ad',
		'std' => '4',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'pinc'),
		'id' => 'frontpage4_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #5', 'pinc'),
		'desc' => __('Display at X(th) position', 'pinc'),
		'id' => 'frontpage5_ad',
		'std' => '5',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'pinc'),
		'id' => 'frontpage5_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('F.A.Q', 'pinc'),
		'type' => 'heading');
		
	$options[] = array(
		'desc' => __('
<br><u>Remember</u>: Always <b>Read our <a href="https://www.skilledup.ir/1739/%d9%82%d8%a7%d9%84%d8%a8-%d9%be%db%8c%d9%86%da%a9%d9%88%d8%a8/" 				target="_blank">Knowledge Base</a> or <a href="https://www.skilledup.ir/ask-question/forum/%d9%82%d8%a7%d9%84%d8%a8-%d9%be%db%8c%d9%86%da%a9%d9%88%d8%a8/" target="_blank">Raise a Support Ticket</a> for any questions that you might have.</b><br><hr style="border:none;border-top:1px solid #ccc;color" /><br>
		<h2>Recommended Plugins</h2>
		<ol>
		<li><a href="http://wordpress.org/extend/plugins/wp-super-cache/" target="_blank">WP Super Cache</a></li>
		<ul>
		<li>- Advanced Tab: "Don\'t cache pages for known users" must be ticked</li>
		</ul>

		<li><a href="https://wordpress.org/plugins/thesography/" target="_blank">Exifography</a> (display image exif info in posts)</li>
		<ul>
		<li>- Auto insert into post: Don\'t select "Automatically display exif"</li>
		</ul>

		<li><a href="http://wordpress.org/plugins/wordpress-social-login/" target="_blank">WordPress Social Login</a> (allow users to login with Facebook or Twitter)</li>
		<ul>
		<li>- Networks Tab: tested only with Facebook, Twitter & Google</li>
		<li>- Bouncer Tab: features not supported</li>
		<li>- Widget Tab:<br />--- Users avatars: Display the default users avatars<br />--- Authentication flow: No popup window</li>
		</ul>
		<li><a href="http://wordpress.org/plugins/wp-postratings/" target="_blank">WP-Postratings</a> (enable ratings and microformats)</li>
		<ul>
		<li>- After activation, go to Ratings -> Ratings Templates to customize its view (just in case you need to do so)</li>
		</ul>
		</ol><br>
		<hr style="border:none;border-top:1px solid #ccc;color" /><br>
		<h2>Adding Pins</h2>
		<p>All users should add pins from the frontend (top right corner)  > Add Pin. Notes when adding pins from backend e.g WP-Admin > Posts > Add New</p>
		<ol>
		<li>The "Featured Image" must be set.</li>
		<li>The post will be assigned to the board with the same name as the post category. E.g if a post is created under the Humour category, the post will be assigned to the Humour board. If Humour board does not exist, it will be created automatically.</li>
		<ul>
		</ol>
		
		<br><hr style="border:none;border-top:1px solid #ccc;color" /><br>
		<h2>Sideblog</h2>
		If you have enabled the sideblog (General tab > Category For Blog), please do not enter tags for the sideblog posts. You can however create sub-categories under the parent blog category.
		
		<br><hr style="border:none;border-top:1px solid #ccc;color" /><br>
		<h2>Permissions</h2>
		<p>You can change the Settings > General > New User Default Role, depending on your needs. If unsure, leave it as "Author" which best matched Pinterest.com system. The permissions for each role are as below.</p>
		<p><strong>Administrator</strong>
		- Everything</p>
		<p><strong>Editor</strong>
		- All of Author
		- Access WP-Admin
		- Publish "Pending Review" Pin (backend)
		- Edit/Delete Others Pin (frontend)
		- Edit/Delete Others Board (frontend)
		- Edit Others Profile (frontend)
		</p>
		<p><strong>Author</strong>
		- All of Contributor
		- Add Pin (Post Status: Published)
		- Repin
		</p>
		<p><strong>Contributor</strong>
		- All of Subscriber
		- Add Pin (Post Status: Pending Review)
		</p>
		<p><strong>Subscriber</strong>
		- Comment
		- Follow
		- Like
		</p>

		<br><hr style="border:none;border-top:1px solid #ccc;color" /><br>
		<h2>Cautions</h2>
		<ol>
		<li>
		WP-admin > Posts > Color <br /> Use HTML Color Names as Slug (<a href="https://www.w3schools.com/tags/ref_colornames.asp">HTML Standard color names</a>)
		</li>
		<li>
		WP-Admin > Pages > All Pages<br />Do not change the permalink for these pages (Board Settings, Everything, Following, Login, Lost Your Password, Notifications, Pins Settings, Popular, Register, Settings, Source, Top Users)
		</li>
		<li>
		WP-Admin > Posts > Categories<br />Do not delete categories if there are already posts under them. However you can edit them or create new ones.
		</li>
		</ol>
		<br><hr style="border:none;border-top:1px solid #ccc;color" /><br><br><u>Remember</u>: Always <b>Read our <a href="https://www.skilledup.ir/1739/%d9%82%d8%a7%d9%84%d8%a8-%d9%be%db%8c%d9%86%da%a9%d9%88%d8%a8/" target="_blank">Knowledge Base</a> or <a href="https://www.skilledup.ir/ask-question/forum/%d9%82%d8%a7%d9%84%d8%a8-%d9%be%db%8c%d9%86%da%a9%d9%88%d8%a8/" target="_blank">Raise a Support Ticket</a> for any questions that you might have.</b>
		', 'pinc'),
		'type' => 'info');

	return $options;
}
?>