<?php get_header(); ?>

<div class="container">
	<div class="row">
		<div class="col-sm-8">
			<div class="row">
				<div class="col-sm-12">
					<?php while (have_posts()) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('post-wrapper'); ?>>
						<div class="h1-wrapper">
							<h1><?php the_title(); ?></h1>
						</div>		

						<div class="post-content">
							<div class="thecontent">
							<?php the_content(); ?>
							</div>
							<?php
							wp_link_pages( array( 'before' => '<p><strong>' . __('Pages:', 'pinc') . '</strong>', 'after' => '</p>' ) );
							edit_post_link(__('Edit Page', 'pinc'),'<p>[ ',' ]</p>');
							?>
						</div>
						
						<div class="post-comments">
							<div class="post-comments-wrapper">
								<?php comments_template(); ?>
							</div>
						</div>
						
					</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
		
		<div class="col-sm-4">
			<?php get_sidebar('others'); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>