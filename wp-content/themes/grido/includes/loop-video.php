<?php 
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<!-- post-video -->
<?php if(themify_get("video_url") != '') :
	
	global $wp_embed;
	echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
	
endif; ?>
<!-- /post-video -->

<!-- post-content -->
<div class="post-content">
	<div class="entry-content" itemprop="articleBody">

	<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
	
		<?php the_excerpt(); ?>

			<?php if( themify_check('setting-excerpt_more') ) : ?>
				<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>" class="more-link"><?php echo themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify') ?></a></p>
			<?php endif; ?>
	
	<?php elseif ( 'none' == $themify->display_content && ! is_attachment() ) : ?>
	
	<?php else: ?>
	
		<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
	
	<?php endif; //display content ?>

	</div><!-- /.entry-content -->
</div>
<!-- /post-content -->