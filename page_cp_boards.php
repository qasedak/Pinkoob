<?php
/*
Template Name: _boards_settings
*/

if (!is_user_logged_in()) { wp_redirect(wp_login_url($_SERVER['REQUEST_URI'])); exit; }

if (!current_user_can('edit_posts')) { wp_redirect(home_url('/')); exit; }

get_header();

if (isset($_GET['i'])) { 
	$board_id = intval($_GET['i']);
	$board_info = get_term_by('id', $board_id, 'board');
	if ($board_info && $board_info->parent != 0 && ($board_info->parent == get_user_meta($user_ID, '_Board Parent ID', true) || current_user_can('edit_others_posts'))) {
	?>
	<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>
	
			<div class="col-sm-8 usercp-wrapper">

				<h1><?php _e('Edit Board', 'pinc') ?> / <?php echo $board_info->name ?></h1>
				
				<div class="error-msg"></div>
				
				<br />
				
				<div class="jumbotron">
					<p></p>
					<form id="add_board_form">
						<div class="form-group">
							<label class="form-label" for="board-title"><?php _e('Title', 'pinc'); ?></label>
							<input class="form-control" type="text" name="board-title" id="board-title" value="<?php echo esc_attr($board_info->name); ?>">
						</div>
						<div class="form-group">
						<label class="form-label" for="category-id"><?php _e('Category', 'pinc'); ?></label>
						<?php echo pinc_dropdown_categories(__('Select a Category', 'pinc'), 'category-id', $board_info->description); ?>
						</div>
						<br />
						<input type='hidden' value='<?php echo $board_info->term_id; ?>' name='term-id' id='term-id' />
						<input type='hidden' value='edit' name='mode' id='mode' />
						<input class="btn btn-success btn-block btn-pinc-custom" type="submit" name="submit" id="submit" value="<?php _e('Save Settings', 'pinc'); ?>" /> 
						<div class="ajax-loader hider"></div>
					</form>
				</div>
				<p><br /></p>
				<div class="text-center"><button class="btn btn-grey pinc-delete-board" type="button"><?php _e('Delete Board', 'pinc') ?></button></div>
			</div>
	
			<div class="col-sm-2"></div>
		</div>
	</div>
	<?php } else { ?>
	<div class="container">
		<div class="row">
			<div class="bigmsg">
				<h2><?php _e('No boards found.', 'pinc'); ?></h2>
			</div>
		</div>
	</div>

<?php }
} else { ?>

<div class="container">
	<div class="row">
		<div class="col-sm-2"></div>

		<div class="col-sm-8 usercp-wrapper">
			<h1><?php _e('Add Board', 'pinc') ?></h1>
			
			<div class="error-msg hider"></div>
			
			<br />
			
			<div class="jumbotron">
				<p></p>
				<form id="add_board_form">
					<div class="form-group">
						<label class="form-label" for="board-title"><?php _e('Title', 'pinc'); ?></label>
						<input class="form-control" type="text" name="board-title" id="board-title">
					</div>
					<div class="form-group">
						<label class="form-label" for="category-id"><?php _e('Category', 'pinc'); ?></label>
						<?php echo pinc_dropdown_categories(__('Select a Category', 'pinc'), 'category-id'); ?>
					</div>
					<br />
					<input type='hidden' value='add' name='mode' id='mode' />
					<input class="btn btn-success btn-block btn-pinc-custom" type="submit" name="submit" id="submit" value="<?php _e('Add Board', 'pinc'); ?>" /> 
					<div class="ajax-loader hider"></div>
				</form>
			</div>
		</div>

		<div class="col-sm-2"></div>
	</div>
</div>
<?php } ?>

<div class="modal pinc-modal" id="delete-board-modal" tabindex="-1" aria-hidden="true" role="alertdialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4><?php _e('All pins on this board will also be deleted.', 'pinc')?> <br /> <?php _e('Are you sure you want to permanently delete this board?', 'pinc'); ?></h4>
			</div>
			
			<div class="modal-body text-right">
				<a href="#" class="btn btn-default" data-dismiss="modal"><strong><?php _e('Cancel', 'pinc'); ?></strong></a>
				<a href="#" id="pinc-delete-board-confirmed" class="btn btn-danger" data-board_id="<?php echo $board_info->term_id; ?>"><strong><?php _e('Delete Board', 'pinc'); ?></strong></a> 
				<div class="ajax-loader-delete-board ajax-loader hider"></div>
				<p></p>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('#board-title').focus();
});
</script>

<?php get_footer(); ?>