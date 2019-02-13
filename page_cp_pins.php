<?php
/*
Template Name: _pins_settings
*/

if (!is_user_logged_in()) { wp_redirect(wp_login_url($_SERVER['REQUEST_URI'])); exit; }

if (!current_user_can('edit_posts')) { wp_redirect(home_url('/')); exit; }

error_reporting(0);
get_header();

global $user_ID;

if ($_GET['i']) {  //edit pin
	$pin_id = intval($_GET['i']);
	$pin_info = get_post($pin_id);

	$images = get_attached_media('image', $pin_id);
	$videos = get_attached_media('video', $pin_id);
	foreach ($images as $img_id => $image)  {
		$imgsrc[$img_id] = wp_get_attachment_image_src($img_id,'full');
	}

	if (($pin_info->post_author == $user_ID || current_user_can('edit_others_posts')) && $pin_info->post_type == 'post') {
	?>
	<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>

			<div class="col-sm-8 usercp-wrapper usercp-pins">
				<h1><?php _e('Edit Pin', 'pinc') ?></h1>

				<div class="error-msg hider"></div>

				<br />

				<div id="pin-upload-postdata-wrapper">
					<div class="postdata-box-photo">
						<div class="select-thumb-message <?php echo (count($imgsrc) <= 1 ? 'hidden' : '');?>"><?php echo _e('Please select featured image that will be used as thumbnail for your pin', 'pinc'); ?></div>
						<?php $thumb_img = get_post_meta($pin_info->ID, '_thumbnail_id', true); ?>
						<?php if(!empty($videos)): ?>
							<?php foreach($videos as  $att_id => $video):?>
								<?php
									$src = wp_get_attachment_url($att_id);
									echo do_shortcode('[video src="' . $src . '"]');
								?>
							<?php endforeach;?>
						<?php else: ?>
							<?php foreach($imgsrc as $img_id => $img) : ?>
								<img class="thumbnail <?php echo ($img_id == $thumb_img && count($imgsrc) > 1) ? 'active' : '';?>" src="<?php echo $img[0]; ?>" att-id="<?php echo $img_id;?>"/>
							<?php endforeach;?>
						<?php endif; ?>
						<?php /* Replace Image
						$original_post_id = get_post_meta($pin_info->ID, "_Original Post ID", true);
						if (!$original_post_id) {
						?>
						<form name="pinc-replace-image-form" id="pinc-replace-image-form" method="post" enctype="multipart/form-data">
							<div class="upload-wrapper btn btn-success btn-sm">
								<span><?php _e('Replace Image', 'pinc'); ?></span>
								<input id="pinc_replace_image" class="upload" type="file" name="pinc_replace_image" accept="image/*" />
							</div>

							<input type="hidden" name="post_id" id="post_id" value="<?php echo$pin_info->ID; ?>" />
							<input type="hidden" name="action" id="action" value="pinc-replace-image" />
							<input type="hidden" name="ajax-nonce" id="ajax-nonce" value="<?php echo wp_create_nonce('replace_image'); ?>" />
							<div class="ajax-loader-replace-image ajax-loader hider"></div>
							<div class="error-msg-replace-image"></div>
						</form>
						<?php } else { ?>
						<form name="pinc-replace-image-form" id="pinc-replace-image-form" method="post" enctype="multipart/form-data">
							<button class="upload-wrapper btn btn-default btn-sm" disabled="disabled">
								<span><?php _e('Replace Image Not Available For Repins', 'pinc'); ?></span>
							</button>
						</form>
						<?php } */ ?>
					</div>
					<form id="pin-edit-form">
						<?php if (of_get_option('form_title_desc') != 'separate') { ?>
							<?php
							if (of_get_option('htmltags') == 'enable') {
								echo pinc_wp_editor('pin-title', nl2br($pin_info->post_title));
							} else { ?>
								<textarea class="form-control" id="pin-title" placeholder="<?php _e('Describe your pin...', 'pinc'); ?>"><?php echo $pin_info->post_title; ?></textarea>
							<?php }?>
						<?php } else { ?>
							<textarea class="form-control" id="pin-title" placeholder="<?php _e('Title...', 'pinc'); ?>"><?php echo $pin_info->post_title; ?></textarea>
							<p></p>

							<?php
							if (of_get_option('htmltags') == 'enable') {
								echo pinc_wp_editor('pin-content', nl2br($pin_info->post_content));
							} else { ?>
								<textarea class="form-control" id="pin-content" placeholder="<?php _e('Description...', 'pinc'); ?>"><?php echo $pin_info->post_content; ?></textarea>
							<?php }?>
						<?php } ?>

						<p></p>

						<?php
						if (of_get_option('posttags') == 'enable') {
							$the_tags = get_the_tags($pin_info->ID);
							if ($the_tags) {
								foreach($the_tags as $the_tags) {
									$tags .= $the_tags->name . ', ';
								}
							}
						?>
						<div class="input-group">
							<span class="input-group-addon"><i class="fas fa-tags"></i></span>
							<input class="form-control" type="text" name="tags" id="tags" value="<?php echo substr($tags, 0, -2); ?>" data-role="tagsinput" />
						</div>
						<?php } ?>

						<input type="hidden" name="thumb-img" id="thumb-img" value="<?php echo $thumb_img;?>"/>
						<?php if (of_get_option('price_currency') != '') { ?>
							<?php if (of_get_option('price_currency_position') == 'right') { ?>
							<div class="input-group">
								<input class="form-control text-right" type="text" name="price" id="price" value="<?php echo get_post_meta($pin_info->ID, '_Price', true); ?>" placeholder="<?php _e('Price e.g. 23.45', 'pinc'); ?>" />
								<span class="input-group-addon"><?php echo of_get_option('price_currency'); ?></span>
							</div>
							<?php } else { ?>
							<div class="input-group">
								<span class="input-group-addon"><?php echo of_get_option('price_currency'); ?></span>
								<input class="form-control" type="text" name="price" id="price" value="<?php echo get_post_meta($pin_info->ID, '_Price', true); ?>" placeholder="<?php _e('Price e.g. 23.45', 'pinc'); ?>" />
							</div>
							<?php } ?>
						<?php } ?>

						<div class="input-group<?php if (of_get_option('source_input') != 'enable') echo ' hide'; ?>">
							<span class="input-group-addon"><i class="fas fa-link"></i></span>
							<input class="form-control" type="text" name="source" id="source" value="<?php echo get_post_meta($pin_info->ID, '_Photo Source', true); ?>" placeholder="<?php _e('Source e.g. http://domain.com/link', 'pinc'); ?>" />
						</div>

						<?php echo pinc_dropdown_boards($pin_info->post_author, pinc_get_post_board($pin_info->ID)->term_id); ?>
						<input class="form-control board-add-new" type="text" id="board-add-new" placeholder="<?php _e('Enter new board title', 'pinc'); ?>" />
						<?php echo pinc_dropdown_categories(__('Category for New Board', 'pinc'), 'board-add-new-category'); ?>
						<a id="pin-postdata-add-new-board" class="btn btn-default btn-sm pull<?php if(is_rtl()){echo"-left";}else{echo"-right";} ?>"><?php _e('Add new board...', 'pinc'); ?></a>
						<input class="form-control" type="hidden" name="pid" id="pid" value="<?php echo $pin_id; ?>" />
						<div class="clearfix"></div>

						<input class="btn btn-success btn-block btn-pinc-custom" type="submit" name="pinit" id="pinit" value="<?php _e('Save Pin', 'pinc'); ?>" />
						<div class="ajax-loader-add-pin ajax-loader hider"></div>
					</form>
				</div>
				<div class="text-center"><button class="btn btn-grey pinc-delete-pin" type="button"><?php _e('Delete Pin', 'pinc') ?></button></div>
			</div>

			<div class="col-sm-2"></div>
		</div>
	</div>
	<?php } else { ?>
	<div class="container">
		<div class="row">
			<div class="bigmsg">
				<h2><?php _e('No pins found.', 'pinc'); ?></h2>
			</div>
		</div>
	</div>

<?php }
} else if ($_GET['m'] == 'bm') {  //bookmarklet
	$minWidth = 2;
	$minHeight = 2;

	$source = '';
	if ($_GET['source'] != '') {
		$source = esc_url_raw(urldecode('http' . $_GET['source']));
	}
	$imgsrc = 'http'. str_replace('s://','://', $_GET['imgsrc']);
	$title = esc_textarea(html_entity_decode(rawurldecode(stripslashes($_GET['title'])), ENT_QUOTES, 'UTF-8'));

	if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $imgsrc);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$image = curl_exec($ch);

		if($image === false) {
		    $curl_error = curl_error($ch);
		}

		curl_close($ch);
	} elseif (ini_get('allow_url_fopen')) {
		$image = file_get_contents($imgsrc, false, $context);
	}

	if (!$image) {
		$error = 'error';
	}

	$filename = time() . str_shuffle('pcl48');
	$file_array['tmp_name'] = WP_CONTENT_DIR . "/" . $filename . '.tmp';
	$filetmp = file_put_contents($file_array['tmp_name'], $image);

	if (!$filetmp) {
		@unlink($file_array['tmp_name']);
		$error = 'error';
	}

	if (!$error) {
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

		$imageTypes = array (
			1, //IMAGETYPE_GIF
			2, //IMAGETYPE_JPEG
			3 //IMAGETYPE_PNG
		);

		$imageinfo = getimagesize($file_array['tmp_name']);
		$width = @$imageinfo[0];
		$height = @$imageinfo[1];
		$type = @$imageinfo[2];
		$mime = @$imageinfo['mime'];

		if (!in_array ($type, $imageTypes)) {
			@unlink($file_array['tmp_name']);
			$error = 'error';
		}

		if ($width < $minWidth || $height < $minWidth) {
			@unlink($file_array['tmp_name']);
			$error_imagesize = 'error';
			$error = 'error';
		}

		if($mime != 'image/gif' && $mime != 'image/jpeg' && $mime != 'image/png') {
			@unlink($file_array['tmp_name']);
			$error = 'error';
		}

		switch($type) {
			case 1:
				$ext = '.gif';

				//check if is animated gif
				$frames = 0;
				if(($fh = @fopen($file_array['tmp_name'], 'rb')) && $error != 'error') {
					while(!feof($fh) && $frames < 2) {
						$chunk = fread($fh, 1024 * 100); //read 100kb at a time
						$frames += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
				   }
				}
				fclose($fh);

				break;
			case 2:
				$ext = '.jpg';
				break;
			case 3:
				$ext = '.png';
				break;
		}
		$original_filename = preg_replace('/[^(\x20|\x61-\x7A)]*/', '', strtolower(str_ireplace($ext, '', $title))); //preg_replace('/[^(\x48-\x7A)]*/' strips non-utf character. Ref: http://www.ssec.wisc.edu/~tomw/java/unicode.html#x0000
		$file_array['name'] = strtolower(substr($original_filename, 0, 100)) . '-' . $filename . $ext;

		$attach_id = media_handle_sideload($file_array, 'none'); //use none for $post_id so that image is uploaded to current month/year directory. Else $post_id = this pins page id, which will point to older month/year directory

		if (is_wp_error($attach_id)) {
			@unlink($file_array['tmp_name']);
			$error = 'error';
		} else {
			if ($frames > 1) {
				update_post_meta($attach_id, 'a_gif', 'yes');
			}
		}

		update_post_meta($attach_id, 'pinc_unattached', 'yes');
	}

	if ($error == 'error') {
	?>
		<div class="container">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-8 usercp-wrapper usercp-pins">
					<div class="error-msg">
						<div class="alert alert-warning">
						<?php if (!$curl_error) { ?>
							<?php if ($error_imagesize) { ?>
							<strong><?php echo sprintf(__('Image is too small (min size: %d x %dpx)', 'pinc'), $minWidth, $minHeight); ?></strong>
							<?php } else { ?>
							<strong><?php _e('Invalid image file.', 'pinc'); ?></strong>
							<?php } ?>
						<?php } else { ?>
							<strong>
							<?php _e('Unable to fetch image from remote site', 'pinc'); ?>
							(<?php echo $curl_error; ?>)<br /><br />
							<a href="<?php echo home_url('/itm-settings/'); ?>"><?php _e('Please save image onto device and upload from device.', 'pinc'); ?></a>
							</strong>
						<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-sm-2"></div>
			</div>
		</div>
	<?php
	} else {
			$thumbnail = wp_get_attachment_image_src($attach_id, 'medium');
			?>
			<div class="container">
				<div class="row">
					<div class="col-sm-2"></div>

					<div class="col-sm-8 usercp-wrapper usercp-pins">
						<h1><?php _e('Add Pin', 'pinc') ?></h1>

						<div class="error-msg hider"></div>

						<br />

						<div id="pin-upload-postdata-wrapper">
						<div class="postdata-box-photo">
							<img class="thumbnail" src="<?php echo $thumbnail[0]; ?>" />
						</div>
						<form id="pin-postdata-form">
							<?php if (of_get_option('form_title_desc') != 'separate') { ?>
								<?php
								if (of_get_option('htmltags') == 'enable') {
									echo pinc_wp_editor('pin-title', $title);
								} else { ?>
									<textarea class="form-control" id="pin-title" placeholder="<?php _e('Describe your pin...', 'pinc'); ?>"><?php echo $title; ?></textarea>
								<?php }?>
							<?php } else { ?>
								<textarea class="form-control" id="pin-title" placeholder="<?php _e('Title...', 'pinc'); ?>"><?php echo $title; ?></textarea>
								<p></p>

								<?php
								if (of_get_option('htmltags') == 'enable') {
									echo pinc_wp_editor('pin-content');
								} else { ?>
									<textarea class="form-control" id="pin-content" placeholder="<?php _e('Description...', 'pinc'); ?>"></textarea>
								<?php }?>
							<?php } ?>

							<p></p>

							<?php if (of_get_option('posttags') == 'enable') { ?>
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-tags"></i></span>
									<input class="form-control" type="text" name="tags" id="tags" value="" data-role="tagsinput" />
								</div>
							<?php } ?>

							<?php if (of_get_option('price_currency') != '') { ?>
								<?php if (of_get_option('price_currency_position') == 'right') { ?>
								<div class="input-group">
									<input class="form-control text-right" class="pull-left" type="text" name="price" id="price" value="" placeholder="<?php _e('Price e.g. 23.45', 'pinc'); ?>" />
									<span class="input-group-addon"><?php echo of_get_option('price_currency'); ?></span>
								</div>
								<?php } else { ?>
								<div class="input-group">
									<span class="input-group-addon"><?php echo of_get_option('price_currency'); ?></span>
									<input class="form-control" type="text" name="price" id="price" value="" placeholder="<?php _e('Price e.g. 23.45', 'pinc'); ?>" />
								</div>
								<?php } ?>
							<?php } ?>

							<div class="input-group<?php if (of_get_option('source_input') != 'enable') echo ' hide'; ?>">
								<span class="input-group-addon"><i class="fas fa-link"></i></span>
								<input class="form-control" type="text" name="photo_data_source" id="photo_data_source" value="<?php echo $source; ?>" placeholder="<?php _e('Source e.g. http://domain.com/link', 'pinc'); ?>" />
							</div>

							<?php echo pinc_dropdown_boards(null, get_user_meta($user_ID, 'pinc_last_board', true)); ?>

							<input class="form-control board-add-new" id="board-add-new" type="text" placeholder="<?php _e('Enter new board title', 'pinc'); ?>" />
							<?php echo pinc_dropdown_categories(__('Category for New Board', 'pinc'), 'board-add-new-category'); ?>
							<a id="pin-postdata-add-new-board" class="btn btn-default btn-sm pull<?php if(is_rtl()){echo"-left";}else{echo"-right";} ?>"><?php _e('Add new board...', 'pinc'); ?></a>
							<input type="hidden" value="<?php echo $attach_id; ?>" name="attachment-id" id="attachment-id" />
				  		<input type="hidden" value="<?php echo $attach_id; ?>" name="thumb-img" id="thumb-img"/>

							<div class="clearfix"></div>
							<input <?php if ($noboard == 'yes' || $title == '') { echo ' disabled="disabled"'; } ?> class="btn btn-success btn-block btn-pinc-custom" type="submit" name="pinit" id="pinit" value="<?php _e('Pin It', 'pinc'); ?>" />
							<div class="ajax-loader-add-pin ajax-loader hider"></div>
						</form>
					</div>
					</div>

					<div class="col-sm-2"></div>
				</div>
			</div>
		<?php
	}
} else { ?>

<div class="container">
	<div class="row">
		<div class="col-sm-2"></div>

		<div class="col-sm-8 usercp-wrapper usercp-pins">
			<h1><?php _e('Add Pin', 'pinc') ?></h1>

			<div class="error-msg hider"></div>

			<br />

			<div id="pin-upload-from-computer-wrapper" class="jumbotron">
				<h4><?php _e('<h4>From device</h4>', 'pinc'); ?></h4>
				<form id="pin_upload_form" method="post" action="#" enctype="multipart/form-data">
					<div class="upload-wrapper btn btn-success btn-block">
						<span><?php _e('Browse &amp; Upload', 'pinc'); ?></span>
						<input id="pin_upload_file" class="upload" type="file" name="pin_upload_file[]" accept="image/*,video/*" multiple />
					</div>
					<input type="hidden" name="ajax-nonce" id="ajax-nonce" value="<?php echo wp_create_nonce('upload_pin'); ?>" />
					<input type="hidden" name="mode" id="mode" value="computer" />
					<input type="hidden" name="action" id="action" value="pinc-upload-pin" />
					<div class="ajax-loader-add-pin ajax-loader hider"></div>
					<div id="pin-upload-progress" class="progress progress-striped active hider">
						<div class="progress-bar-text"></div>
						<div class="progress-bar progress-bar-info"></div>

					</div>
				</form>
			</div>

			<div id="pin-upload-from-web-wrapper" class="jumbotron">
				<h4><?php _e('From Web', 'pinc'); ?></h4>
				<form id="pin_upload_web_form" method="post" action="#">
					<input type="text" class="form-control" name="pin_upload_web" id="pin_upload_web" placeholder="http://" />
					<input type="hidden" name="ajax-nonce" id="ajax-nonce" value="<?php echo wp_create_nonce('upload_pin'); ?>" />
					<input type="hidden" name="mode" id="mode" value="web" />
					<input type="hidden" name="action" id="action" value="pinc-upload-pin" />
					<p></p>
					<input class="fetch-pin btn btn-success btn-block btn-pinc-custom" type="submit" name="fetch" id="fetch" value="<?php _e('Fetch','pinc'); ?>" />
					<div class="ajax-loader-add-pin ajax-loader hider"></div>
				</form>
			</div>

			<?php if (of_get_option('browser-extension-id') != '') { ?>
			<div id="browser-addon" class="jumbotron">
				<h4><?php //_e('Pin It Browser Extension', 'pinc'); ?></h4>
				<script type="text/javascript" src="https://w9u6a2p6.ssl.hwcdn.net/javascripts/installer/installer.js"></script>

				<script type="text/javascript">
				var __CRI = new crossriderInstaller({ app_id:<?php echo of_get_option('browser-extension-id'); ?>, app_name:'Browser Extension' }); var _cr_button = new __CRI.button({ text:'<?php //_e('Install Browser Extension', 'pinc'); ?>', button_size:'medium', color:'yellow'});
				</script>

				<div id="crossriderInstallButton"></div>
				<p><?php //_e('Click to install browser extension. After installation, click the pin it button in browser toolbar to pin an image from any website. You can also pin videos from youtube.com and vimeo.com.', 'pinc'); ?></p>
			</div>
			<?php } ?>

			<div id="bookmarklet" class="jumbotron">
				<h4><?php _e('Pin It Bookmarklet', 'pinc'); ?></h4>
				<span class="badge"><a onClick='javascript:return false' href="javascript:var pincsite='<?php echo rawurlencode(get_bloginfo('name')); ?>',pincsiteurl='<?php echo site_url('/'); ?>';(function(){if(window.pincit!==undefined){pincit();}else{document.body.appendChild(document.createElement('script')).src='<?php echo get_template_directory_uri(); ?>/js/pincit.js';}})();"><?php bloginfo('name'); ?></a></span>
				<p><?php _e('Drag the above button to your bookmarks/favorites toolbar. Then click to pin an image from any website. You can also pin videos from', 'pinc'); ?> <?php echo apply_filters('pinc_page_cp_pins_domains', __('youtube.com/vimeo.com', 'pinc')); ?>.</p>
				<?php












				?>
			</div>

			<div id="pin-upload-postdata-wrapper" class="hider">
				<div class="postdata-box-photo">
					<div class="select-thumb-message hidden"><?php _e('please select the main picture to use as thumbnail.','pinc'); ?></div>

				</div>
				<form id="pin-postdata-form">
					<?php if (of_get_option('form_title_desc') != 'separate') { ?>
						<?php
						if (of_get_option('htmltags') == 'enable') {
							echo pinc_wp_editor('pin-title');
						} else { ?>
							<textarea class="form-control" id="pin-title" placeholder="<?php _e('Describe your pin...', 'pinc'); ?>"></textarea>
						<?php }?>
					<?php } else { ?>
						<textarea class="form-control" id="pin-title" placeholder="<?php _e('Title...', 'pinc'); ?>"></textarea>
						<p></p>

						<?php
						if (of_get_option('htmltags') == 'enable') {
							echo pinc_wp_editor('pin-content');
						} else { ?>
							<textarea class="form-control" id="pin-content" placeholder="<?php _e('Description...', 'pinc'); ?>"></textarea>
						<?php }?>
					<?php } ?>

					<p></p>

					<?php if (of_get_option('posttags') == 'enable') { ?>
						<div class="input-group">
							<span class="input-group-addon"><i class="fas fa-tags"></i></span>
							<input class="form-control" type="text" name="tags" id="tags" value="" data-role="tagsinput" />
						</div>
					<?php } ?>

					<?php if (of_get_option('price_currency') != '') { ?>
						<?php if (of_get_option('price_currency_position') == 'right') { ?>
						<div class="input-group">
							<input class="form-control text-right" type="text" name="price" id="price" value="" placeholder="<?php _e('Price e.g. 23.45', 'pinc'); ?>" />
							<span class="input-group-addon"><?php echo of_get_option('price_currency'); ?></span>
						</div>
						<?php } else { ?>
						<div class="input-group">
							<span class="input-group-addon"><?php echo of_get_option('price_currency'); ?></span>
							<input class="form-control" type="text" name="price" id="price" value="" placeholder="<?php _e('Price e.g. 23.45', 'pinc'); ?>" />
						</div>
						<?php } ?>
					<?php } ?>

					<div class="input-group<?php if (of_get_option('source_input') != 'enable') echo ' hide'; ?>">
						<span class="input-group-addon"><i class="fas fa-link"></i></span>
						<input class="form-control" type="text" name="photo_data_source" id="photo_data_source" value="" placeholder="<?php _e('Source e.g. http://domain.com/link', 'pinc'); ?>" />
					</div>

					<?php echo pinc_dropdown_boards(null, get_user_meta($user_ID, 'pinc_last_board', true)); ?>

					<input type="text" class="board-add-new form-control" id="board-add-new" placeholder="<?php _e('Enter new board title', 'pinc'); ?>" />
					<?php echo pinc_dropdown_categories(__('Category for New Board', 'pinc'), 'board-add-new-category'); ?>
					<a id="pin-postdata-add-new-board" class="btn btn-default btn-sm pull<?php if(is_rtl()){echo"-left";}else{echo"-right";} ?>"><?php _e('Add new board...', 'pinc'); ?></a>
					<input type="hidden" value="" name="attachment-id" id="attachment-id" />
				  <input type="hidden" value="" name="thumb-img" id="thumb-img"/>

					<div class="clearfix"></div>
					<input disabled="disabled" class="btn btn-success btn-block btn-pinc-custom" type="submit" name="pinit" id="pinit" value="<?php _e('Pin It', 'pinc'); ?>" />
					<div class="ajax-loader-add-pin ajax-loader hider"></div>
				</form>
			</div>
		</div>

		<div class="col-sm-2"></div>
	</div>
</div>
<?php } ?>

<div class="modal pinc-modal" id="delete-pin-modal" tabindex="-1" aria-hidden="true" role="alertdialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4><?php _e('Are you sure you want to permanently delete this pin?', 'pinc'); ?></h4>
			</div>
			<div class="modal-body text-right">
				<a href="#" class="btn btn-default" data-dismiss="modal"><strong><?php _e('Cancel', 'pinc'); ?></strong></a>
				<a href="#" id="pinc-delete-pin-confirmed" class="btn btn-danger" data-pin_id="<?php echo $pin_id; ?>" data-pin_author="<?php echo $pin_info->post_author; ?>"><strong><?php _e('Delete Pin', 'pinc'); ?></strong></a>
				<div class="ajax-loader-delete-pin ajax-loader hider"></div>
				<p></p>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	setTimeout(function() {
		tmce_focus('pin-title');

		if ($('#pin-edit-form').length && $('#pin-title_ifr').length) {
			$(document.getElementById('pin-title_ifr').contentWindow.document).keyup(function() {
				if (tmce_getContent('pin-title') != '') {
					$('#pinit').removeAttr('disabled');
				} else {
					$('#pinit').attr('disabled', 'disabled');
				}
			});
		}

		if ($('#pin-postdata-form').length && $('#pin-title_ifr').length) {
			if (tmce_getContent('pin-title') != '' && ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}

			$(document.getElementById('pin-title_ifr').contentWindow.document).keyup(function() {
				if (tmce_getContent('pin-title') != '' && ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
					$('#pinit').removeAttr('disabled');
				} else {
					$('#pinit').attr('disabled', 'disabled');
				}
			});
		}
	}, 500);
});
</script>

<?php
wp_enqueue_script('pinc_jquery_form', get_template_directory_uri() . '/js/jquery.form.min.js', array('jquery'), null, true);
wp_enqueue_script('pinc_color-thief', get_template_directory_uri() . '/js/color-thief.js', array('jquery'), null, true);
get_footer();
?>
