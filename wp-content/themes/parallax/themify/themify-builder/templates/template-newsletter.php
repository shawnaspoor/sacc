<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Newsletter
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

// Load styles and scripts registered in Themify_Builder::register_frontend_js_css()
$GLOBALS['ThemifyBuilder']->load_templates_js_css();

$fields_default = array(
	'mod_title_newsletter' => '',
	'layout_newsletter' => '',
	'type_query_newsletter' => 'category',
	'category_newsletter' => '',
	'query_slug_newsletter' => '',
	'post_per_page_newsletter' => '',
	'offset_newsletter' => '',
	'order_newsletter' => 'desc',
	'orderby_newsletter' => 'date',
	'display_newsletter' => 'content',
	'hide_feat_img_newsletter' => 'no',
	'image_size_newsletter' => '',
	'img_width_newsletter' => '',
	'img_height_newsletter' => '',
	'unlink_feat_img_newsletter' => 'no',
	'hide_post_title_newsletter' => 'no',
	'unlink_post_title_newsletter' => 'no',
	'hide_post_date_newsletter' => 'no',
	'hide_post_meta_newsletter' => 'no',
	'hide_page_nav_newsletter' => 'yes',
	'animation_effect' => '',
	'css_newsletter' => ''
);

if ( isset( $mod_settings['category_newsletter'] ) )	
	$mod_settings['category_newsletter'] = $this->get_param_value( $mod_settings['category_newsletter'] );

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );
$animation_effect = $this->parse_animation_effect( $animation_effect );

$container_class = implode(' ', 
	apply_filters( 'themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID, $css_newsletter
	), $mod_name, $module_ID, $fields_args )
);

$this->add_post_class( $animation_effect );
?>
<!-- module newsletter -->
<div id="<?php echo esc_attr( $module_ID ); ?>" class="<?php echo esc_attr( $container_class ); ?>">
	<?php if ( $mod_title_newsletter != '' ): ?>
	<h3 class="module-title"><?php echo wp_kses_post( $mod_title_newsletter ); ?></h3>
	<?php endif; ?>

	<?php
	do_action( 'themify_builder_before_template_content_render' );
	$this->in_the_loop = true;
	
	// The Query
	global $paged;
	$order = $order_newsletter;
	$orderby = $orderby_newsletter;
	$paged = $this->get_paged_query();
	$limit = $post_per_page_newsletter;
	$terms = $category_newsletter;
	$temp_terms = explode(',', $terms);
	$new_terms = array();
	$is_string = false;
	foreach ( $temp_terms as $t ) {
		if ( ! is_numeric( $t ) )
			$is_string = true;
		if ( '' != $t ) {
			array_push( $new_terms, trim( $t ) );
		}
	}
	$tax_field = ( $is_string ) ? 'slug' : 'id';

	$args = array(
		'post_type' => 'newsletter',
		'post_status' => 'publish',
		'posts_per_page' => $limit,
		'order' => $order,
		'orderby' => $orderby,
		'suppress_filters' => false,
		'paged' => $paged
	);

	if ( count($new_terms) > 0 && ! in_array('0', $new_terms) && 'category' == $type_query_newsletter ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'newsletter-category',
				'field' => $tax_field,
				'terms' => $new_terms
			)
		);
	}

	if ( ! empty( $query_slug_newsletter ) && 'post_slug' == $type_query_newsletter ) {
		$args['post__in'] = $this->parse_slug_to_ids( $query_slug_newsletter, 'newsletter' );
	}

	// add offset posts
	if ( $offset_newsletter != '' ) {
		if ( empty( $limit ) ) 
			$limit = get_option('posts_per_page');

		$args['offset'] = ( ( $paged - 1) * $limit ) + $offset_newsletter;
	}
	
	$the_query = new WP_Query();
	$posts = $the_query->query( $args );

	echo '<div class="builder-posts-wrap newsletter clearfix loops-wrapper '. $layout_newsletter .'">';

	// check if theme loop template exists
	$is_theme_template = $this->is_loop_template_exist('loop-newsletter.php', 'includes');

	// use theme template loop
	if ( $is_theme_template ) {
		// save a copy
		global $themify;
		$themify_save = clone $themify;

		// override $themify object
		$themify->hide_image = $hide_feat_img_newsletter;
		$themify->unlink_image = $unlink_feat_img_newsletter;
		$themify->hide_title = $hide_post_title_newsletter;
		$themify->width = $img_width_newsletter;
		$themify->height = $img_height_newsletter;
		$themify->image_setting = 'ignore=true&';
		if ( $this->is_img_php_disabled() ) 
			$themify->image_setting .= $image_size_newsletter != '' ? 'image_size=' . $image_size_newsletter . '&' : '';
		$themify->unlink_title = $unlink_post_title_newsletter;
		$themify->display_content = $display_newsletter;
		$themify->hide_date = $hide_post_date_newsletter;
		$themify->hide_meta = $hide_post_meta_newsletter;
		$themify->post_layout = $layout_newsletter;

		// hooks action
		do_action_ref_array('themify_builder_override_loop_themify_vars', array( $themify, $mod_name ) );

		$out = '';
		if ($posts) {
			$out .= themify_get_shortcode_template($posts, 'includes/loop', 'newsletter');
		}
		
		// revert to original $themify state
		$themify = clone $themify_save;
		echo !empty( $out ) ? $out : '';
	} else {
		// use builder template
		global $post; $temp_post = $post;
		foreach($posts as $post): setup_postdata( $post ); ?>

		<?php themify_post_before(); // hook ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class("post newsletter-post clearfix"); ?>>
			
			<?php themify_post_start(); // hook ?>
			
			<?php
			if ( $hide_feat_img_newsletter != 'yes' ) {
				$width = $img_width_newsletter;
				$height = $img_height_newsletter;
				$param_image = 'w='.$width .'&h='.$height.'&ignore=true';
				if ( $this->is_img_php_disabled() ) 
					$param_image .= $image_size_newsletter != '' ? '&image_size=' . $image_size_newsletter : '';

				// Check if there is a video url in the custom field
				if( themify_get('video_url') != '' ){
					global $wp_embed;
					
					themify_before_post_image(); // Hook
					
					echo $wp_embed->run_shortcode('[embed]' . esc_url( themify_get( 'video_url' ) ) . '[/embed]');
					
					themify_after_post_image(); // Hook
					
				} elseif ( $post_image = themify_get_image( $param_image ) ) {
						themify_before_post_image(); // Hook ?>
						<figure class="post-image">
							<?php if ( $unlink_feat_img_newsletter == 'yes' ): ?>
								<?php echo wp_kses_post( $post_image ); ?>
							<?php else: ?>
								<a href="<?php echo themify_get_featured_image_link(); ?>"><?php echo wp_kses_post( $post_image ); ?></a>
							<?php endif; ?>
						</figure>
						<?php themify_after_post_image(); // Hook
					} 
			}
			?>

			<div class="post-content">
			
				<?php if ( $hide_post_date_newsletter == 'no' ): ?>
					<time datetime="<?php the_time('o-m-d') ?>" class="post-date" pubdate><?php the_date( apply_filters( 'themify_loop_date', '' ) ) ?></time>
				<?php endif; //post date ?>

				<?php if ( $hide_post_title_newsletter != 'yes' ): ?>
					<?php themify_before_post_title(); // Hook ?>
					<?php if ( $unlink_post_title_newsletter == 'yes' ): ?>
						<h1 class="post-title"><?php the_title(); ?></h1>
					<?php else: ?>
						<h1 class="post-title"><a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					<?php endif; //unlink post title ?>
					<?php themify_after_post_title(); // Hook ?> 
				<?php endif; //post title ?>    

				<?php if ( $hide_post_meta_newsletter == 'no' ): ?>
					<p class="post-meta"> 
						<span class="post-author"><?php the_author_posts_link() ?></span>
						<span class="post-category"><?php the_terms($post->ID, 'newsletter-category'); ?></span>
						<?php the_tags(' <span class="post-tag">', ', ', '</span>'); ?>
						<?php  if ( ! themify_get('setting-comments_posts') && comments_open() ) : ?>
							<span class="post-comment"><?php comments_popup_link( __( '0 Comments', 'themify' ), __( '1 Comment', 'themify' ), __( '% Comments', 'themify' ) ); ?></span>
						<?php endif; //post comment ?>
					</p>
				<?php endif; //post meta ?>    
				
				<?php
				// fix the issue more link doesn't output
				global $more;
				$more = 0;
				?>
				
				<?php if ( $display_newsletter == 'excerpt' ): ?>
			
					<?php the_excerpt(); ?>
			
				<?php elseif ( $display_newsletter == 'none' ): ?>
			
				<?php else: ?>

					<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
				
				<?php endif; //display content ?>
				
				<?php edit_post_link(__('Edit', 'themify'), '[', ']'); ?>
				
			</div>
			<!-- /.post-content -->
			<?php themify_post_end(); // hook ?>
			
		</article>
		<?php themify_post_after(); // hook ?>
		<?php endforeach; wp_reset_postdata(); $post = $temp_post; ?>

	<?php
	} // end $is_theme_template

	echo '</div><!-- .builder-posts-wrap -->';

	echo 'yes' != $hide_page_nav_newsletter ? $this->get_pagenav( '', '', $the_query ) : '';
	?>

	<?php do_action( 'themify_builder_after_template_content_render' ); $this->remove_post_class( $animation_effect ); $this->in_the_loop = false; ?>
</div>
<!-- /module newsletter -->