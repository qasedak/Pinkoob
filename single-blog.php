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

						<div class="post-blog-top">
							<div class="pull-left"><?php echo pinc_human_time_diff(get_post_time('U', true)) . ' / ';the_author(); ?></div>
							<div class="pull<?php if(is_rtl()){echo"-left";}else{echo"-right";} ?>"><a href="#navigation"><?php comments_number(__('0 Comments', 'pinc'), __('1 Comment', 'pinc'), __('% Comments', 'pinc'));?></a><?php edit_post_link(__('Edit', 'pinc'), ' | '); ?></div>
						</div>

						<?php
						$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
						if ($imgsrc[0] != '') {
						?>
						<div class="post-featured-photo post-featured-photo-blog">
							<img class="featured-thumb" src="<?php echo $imgsrc[0]; ?>" alt="<?php the_title_attribute(); ?>" />
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
							
							<div>
								<ul class="pager">
									<li class="previous"><?php previous_post_link('%link', '&laquo; %title', true); ?></li>
									<li class="next"><?php next_post_link('%link', '%title &raquo;', true); ?></li>
								</ul>
							</div>
						</div>
						
						<div class="post-comments">
							<div class="post-comments-wrapper">
								<?php comments_template(); ?>
								<?php if (of_get_option('facebook_comments') != 'disable' && comments_open()) { ?>
								<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-num-posts="5"<?php /* Auto Color by macse */
									if(of_get_option("theme_color_mod") == 2){
										global $user_ID;
										$user_info = get_userdata($user_ID);
										if(!empty($user_info->pinc_user_timezone)){
											$timeZoneSet = $user_info->pinc_user_timezone;
										}else{
											$timeZoneSet = get_option('timezone_string');
										}
										$getUserDate = new DateTime(null, new DateTimeZone($timeZoneSet));
										$currentTime = $getUserDate->format('H:i:s');
									
										if ($currentTime > of_get_option("night_start") || $currentTime < of_get_option("night_end")) {
											$colorScheme = 'dark';
										}else{
											$colorScheme = 'light';
										}
									}elseif(of_get_option("theme_color_mod") == 0){
										$colorScheme = 'light';
									}else{
										$colorScheme = 'dark';
									}
								if ($colorScheme == 'dark') { echo ' data-colorscheme="dark"'; } ?> data-width="100%"></div>
								<?php } ?>
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