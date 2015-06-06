<?php if(!is_single()) { global $more; $more = 0; } //enable more link ?>
<?php
/** Themify Default Variables
 @var object */
	global $themify; ?>

<?php $post_color_class = (themify_check('post_color') && themify_get('post_color') != 'default') ? themify_get('post_color') : themify_get('setting-default_color');  ?>

<?php themify_post_before(); //hook ?>
<!-- post -->
<article itemscope itemtype="http://schema.org/Article" id="post-<?php the_ID(); ?>" <?php post_class("post clearfix $post_color_class" . $themify->get_categories_as_classes(get_the_ID())); ?>>
	<?php themify_post_start(); //hook ?>

	<div class="post-inner clearfix">
		
		<span class="post-icon"></span><!-- /post-icon -->
	
		<?php if($themify->hide_date != "yes"): ?>
			<time datetime="<?php the_time('o-m-d') ?>" class="post-date entry-date updated" itemprop="datePublished"><?php echo get_the_date( apply_filters( 'themify_loop_date', '' ) ) ?></time>
		<?php endif; ?>
	
		<!-- post-title -->
		<?php if($themify->hide_title != "yes"): ?>
			<?php themify_before_post_title(); // Hook ?>
			<?php if($themify->unlink_title == "yes"): ?>
				<h1 class="post-title entry-title" itemprop="name"><?php the_title(); ?></h1>
			<?php else: ?>
				<h1 class="post-title entry-title" itemprop="name"><a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<?php endif; //unlink post title ?>
			<?php themify_after_post_title(); // Hook ?>
		<?php endif; //post title ?>    
		<!-- /post-title -->
		
		<?php get_template_part('includes/loop-' . $themify->get_format_template()); ?>
		
		<?php if($themify->hide_meta != 'yes'): ?>
			<!-- post-meta -->
			<p class="post-meta entry-meta"> 
				<span class="post-author"><?php echo themify_get_author_link(); ?></span>
				<span class="post-category"><?php the_category(', ') ?></span>
				<?php  if( !themify_get('setting-comments_posts') && comments_open() ) : ?>
					<span class="post-comment"><?php comments_popup_link('0', '1', '%', 'comments-link', ''); ?></span>
				<?php endif; //post comment ?>
				<?php the_tags(' <span class="post-tag">', ', ', '</span>'); ?>
			</p>
			<!-- /post-meta -->
		<?php endif; ?>
	
		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>
		
	</div>
	<!-- /.post-inner -->

	<?php themify_post_end(); //hook ?>
</article>
<!-- /post -->
<?php themify_post_after(); //hook ?>