<div class="sidebar">
	<?php if (!dynamic_sidebar('sidebar-others') && current_user_can('administrator')) : ?>
		<div class="sidebar-wrapper">
			<div class="sidebar-inner">
				Only the admin sees this text.
				<a href="<?php echo admin_url('/widgets.php'); ?>">Add some widgets</a> to the sidebar to replace this text.
			</div>
		</div>
	<?php endif ?>
</div>