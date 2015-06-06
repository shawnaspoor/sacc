<?php 
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php $quote_author = themify_get('quote_author'); ?>
<?php $quote_author_link = themify_get('quote_author_link'); ?>

<!-- quote-content -->
<div class="quote-content">
	
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
<!-- /quote-content -->

<!-- quote-author -->
<p class="quote-author">
	&#8212; <?php if($quote_author_link != '') { echo '<a href="'.$quote_author_link.'">'; } ?><?php echo $quote_author; ?><?php if($quote_author_link != '') { echo '</a>'; } ?>
</p>
<!-- /quote-author -->