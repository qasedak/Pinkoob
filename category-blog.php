<?php get_header(); ?>

<div class="container">
	<div class="row">
		<div class="col-sm-8">
			<div class="row">
				<div class="col-sm-12">
					<?php while (have_posts()) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('post-wrapper'); ?>>
						<div class="h1-wrapper">
							<a href="<?php the_permalink(); ?>"><h1><?php the_title(); ?></h1></a>
						</div>

						<div class="post-blog-top">
							<div class="pull-left"><?php echo pinc_human_time_diff(get_post_time('U', true)) . ' / ';the_author(); ?></div>
							<div class="pull<?php if(is_rtl()){echo"-left";}else{echo"-right";} ?>"><a href="<?php the_permalink(); ?>#comments"><?php comments_number(__('0 Comments', 'pinc'), __('1 Comment', 'pinc'), __('% Comments', 'pinc'));?></a><?php edit_post_link(__('Edit', 'pinc'), ' | '); ?></div>
						</div>

						<?php
						$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
						if ($imgsrc[0] != '') {
						?>
						<div class="post-featured-photo post-featured-photo-blog">
							<a href="<?php the_permalink(); ?>" target="_blank"><img class="featured-thumb" src="<?php echo $imgsrc[0]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
						</div>
						<?php } ?>

						<div class="post-content">
							<div class="thecontent">
							<?php
							the_content();
							wp_link_pages( array( 'before' => '<p><strong>' . __('Pages:', 'pinc') . '</strong>', 'after' => '</p>' ) );
							?>
							</div>
							
							<div class="clearfix"></div>
							
							<div class="post-meta-category-tag">
								<?php
								$categories = get_the_category();
								if($categories){
									echo __('Category', 'pinc') . ' <span class="thetags">';
									
									foreach($categories as $category) {
										echo '<a href="'.get_category_link( $category->term_id ).'">'.$category->cat_name.'</a> ';
									}
									
									echo '</span>';
								}
								
								/*
								$posttags = get_the_tags();
								if ($posttags) {
									echo __('Tags', 'pinc') . ' <span class="thetags">';
									
									foreach($posttags as $tag) {
										echo '<a href="' . get_tag_link($tag->term_id). '">' . $tag->name . '</a> '; 
									}
									
									echo '</span>';
								}
								*/
								?>
							</div>
						</div>
						
					</div>
					<?php endwhile; ?>


					<?php if(function_exists('wp_pagenavi')) { ?>
					<div class="text-center">
						<div id="navigation" class="pagination">
							<?php wp_pagenavi(); ?>
						</div>
					</div>
					<?php } else { ?>
						<div id="navigation-blog">
							<ul class="pager">
								<li class="previous"><?php next_posts_link(__('&laquo; Previous', 'pinc')) ?></li>
								<li class="next"><?php previous_posts_link(__('Next &raquo;', 'pinc')) ?></li>
							</ul>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		
		<div class="col-sm-4">
			<?php get_sidebar('others'); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>