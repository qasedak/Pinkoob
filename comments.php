<?php
	if (post_password_required())
		return;
?>

<div id="comments">
	<?php if (have_comments()) : ?>

		<ol class="commentlist">
			<?php wp_list_comments(array('callback' => 'pinc_list_comments')); ?>
		</ol>

		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
		<ul class="pager">
			<li class="previous"><?php previous_comments_link(__( '&laquo; Older Comments', 'pinc')); ?></li>
			<li class="next"><?php next_comments_link(__('Newer Comments &raquo;', 'pinc')); ?></li>
		</ul>
		<?php endif;?>

	<?php
	elseif (!comments_open() && '0' != get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
	endif;

	if (is_user_logged_in()) {
		global $user_ID;
		if(is_rtl()){
			comment_form(array(
				'title_reply' => '',
				'title_reply_to' => '',
				'cancel_reply_link' => __('X Cancel reply', 'pinc'),
				'comment_notes_before' => '',
				'comment_notes_after' => '',
				'logged_in_as' => '',
				'label_submit' => __('Post Comment', 'pinc'),
				'comment_field' => '<div class="pull-right">' . get_avatar($user_ID, '48') . '</div>' . '<div class="textarea-wrapper"><textarea class="form-control" placeholder="' . __('Add a comment...', 'pinc') . '" id="comment" name="comment" aria-required="true"></textarea></div>'
			));
		}else{
			comment_form(array(
				'title_reply' => '',
				'title_reply_to' => '',
				'cancel_reply_link' => __('X Cancel reply', 'pinc'),
				'comment_notes_before' => '',
				'comment_notes_after' => '',
				'logged_in_as' => '',
				'label_submit' => __('Post Comment', 'pinc'),
				'comment_field' => '<div class="pull-left">' . get_avatar($user_ID, '48') . '</div>' . '<div class="textarea-wrapper"><textarea class="form-control" placeholder="' . __('Add a comment...', 'pinc') . '" id="comment" name="comment" aria-required="true"></textarea></div>'
			));
		}
	} else if (comments_open()) {
	?>
		<form method="post" id="commentform">
			<div class="pull-<?php if(is_rtl()){echo"right";}else{echo"left";} ?>"><?php echo get_avatar('', '48'); ?></div>
			<div class="textarea-wrapper">
				<textarea class="form-control" disabled placeholder="<?php _e('Login to comment...', 'pinc'); ?>"></textarea>
				<button id="submit" class="btn btn-success" type="submit"><?php _e('Post Comment', 'pinc'); ?></button>
			</div>
		</form>
	<?php
	}
	?>
</div>
