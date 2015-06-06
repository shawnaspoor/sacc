<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Newsletter
 * Description: Display newsletter custom post type
 */
class TB_Newsletter_Module extends Themify_Builder_Module {
	var $cpt_options = array(
		'show_in_nav_menus' => true
	);
	var $tax_options = array(
		'show_in_nav_menus' => true
	);

	function __construct() {
		parent::__construct(array(
			'name' => __('Newsletter', 'themify'),
			'slug' => 'newsletter'
		));

		///////////////////////////////////////
		// Load Post Type
		///////////////////////////////////////
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if( ! is_plugin_active( 'themify-newsletter-post/themify-newsletter-post.php' ) ) {
			$this->meta_box = $this->set_metabox();
			$this->initialize_cpt( array(
				'plural' => __('Newsletters', 'themify'),
				'singular' => __('Newsletter', 'themify'),
				'rewrite' => apply_filters('themify_newsletter_rewrite', 'project'),
				'menu_icon' => 'dashicons-newsletter'
			));

			if ( ! shortcode_exists( 'themify_newsletter_posts' ) ) {
				add_shortcode( 'themify_newsletter_posts', array( $this, 'do_shortcode' ) );
			}
		}
	}

	public function get_title( $module ) {
		$type = isset( $module['mod_settings']['type_query_newsletter'] ) ? $module['mod_settings']['type_query_newsletter'] : 'category';
		$category = isset( $module['mod_settings']['category_newsletter'] ) ? $module['mod_settings']['category_newsletter'] : '';
		$slug_query = isset( $module['mod_settings']['query_slug_newsletter'] ) ? $module['mod_settings']['query_slug_newsletter'] : '';

		if ( 'category' == $type ) {
			return sprintf( '%s : %s', __('Category', 'themify'), $category );
		} else {
			return sprintf( '%s : %s', __('Slugs', 'themify'), $slug_query );
		}
	}

	public function get_options() {
		$image_sizes = themify_get_image_sizes_list( false );
		$options = array(
			array(
				'id' => 'mod_title_newsletter',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'layout_newsletter',
				'type' => 'layout',
				'label' => __('Newsletter Layout', 'themify'),
				'options' => array(
					array('img' => 'grid4.png', 'value' => 'grid4', 'label' => __('Grid 4', 'themify')),
					array('img' => 'grid3.png', 'value' => 'grid3', 'label' => __('Grid 3', 'themify')),
					array('img' => 'grid2.png', 'value' => 'grid2', 'label' => __('Grid 2', 'themify')),
					array('img' => 'fullwidth.png', 'value' => 'fullwidth', 'label' => __('fullwidth', 'themify'))
				)
			),
			array(
				'id' => 'type_query_newsletter',
				'type' => 'radio',
				'label' => __('Query by', 'themify'),
				'options' => array(
					'category' => __('Category', 'themify'),
					'post_slug' => __('Slug', 'themify')
				),
				'default' => 'category',
				'option_js' => true,
			),
			array(
				'id' => 'category_newsletter',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'newsletter-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">newsletter posts</a>', 'themify'), admin_url('post-new.php?post_type=newsletter')),
				'wrap_with_class' => 'tf-group-element tf-group-element-category'
			),
			array(
				'id' => 'query_slug_newsletter',
				'type' => 'text',
				'label' => __('Newsletter Slugs', 'themify'),
				'class' => 'large',
				'wrap_with_class' => 'tf-group-element tf-group-element-post_slug',
				'help' => '<br/>' . __( 'Insert Newsletter slug. Multiple slug should be separated by comma (,)', 'themify')
			),
			array(
				'id' => 'post_per_page_newsletter',
				'type' => 'text',
				'label' => __('Limit', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of posts to show', 'themify')
			),
			array(
				'id' => 'offset_newsletter',
				'type' => 'text',
				'label' => __('Offset', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of post to displace or pass over', 'themify')
			),
			array(
				'id' => 'order_newsletter',
				'type' => 'select',
				'label' => __('Order', 'themify'),
				'help' => __('Descending = show newer posts first', 'themify'),
				'options' => array(
					'desc' => __('Descending', 'themify'),
					'asc' => __('Ascending', 'themify')
				)
			),
			array(
				'id' => 'orderby_newsletter',
				'type' => 'select',
				'label' => __('Order By', 'themify'),
				'options' => array(
					'date' => __('Date', 'themify'),
					'id' => __('Id', 'themify'),
					'author' => __('Author', 'themify'),
					'title' => __('Title', 'themify'),
					'name' => __('Name', 'themify'),
					'modified' => __('Modified', 'themify'),
					'rand' => __('Rand', 'themify'),
					'comment_count' => __('Comment Count', 'themify')
				)
			),
			array(
				'id' => 'display_newsletter',
				'type' => 'select',
				'label' => __('Display', 'themify'),
				'options' => array(
					'content' => __('Content', 'themify'),
					'excerpt' => __('Excerpt', 'themify'),
					'none' => __('None', 'themify')
				)
			),
			array(
				'id' => 'hide_feat_img_newsletter',
				'type' => 'select',
				'label' => __('Hide Featured Image', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'image_size_newsletter',
				'type' => 'select',
				'label' => Themify_Builder_Model::is_img_php_disabled() ? __('Image Size', 'themify') : false,
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'hide' => Themify_Builder_Model::is_img_php_disabled() ? false : true,
				'options' => $image_sizes
			),
			array(
				'id' => 'img_width_newsletter',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'img_height_newsletter',
				'type' => 'text',
				'label' => __('Image Height', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'unlink_feat_img_newsletter',
				'type' => 'select',
				'label' => __('Unlink Featured Image', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_post_title_newsletter',
				'type' => 'select',
				'label' => __('Hide Post Title', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'unlink_post_title_newsletter',
				'type' => 'select',
				'label' => __('Unlink Post Title', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_post_date_newsletter',
				'type' => 'select',
				'label' => __('Hide Post Date', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_post_meta_newsletter',
				'type' => 'select',
				'label' => __('Hide Post Meta', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_page_nav_newsletter',
				'type' => 'select',
				'label' => __('Hide Page Navigation', 'themify'),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			)
		);
		return $options;
	}

	public function get_styling() {
		$styling = array(
			// Animation
			array(
				'id' => 'separator_animation',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Animation', 'themify').'</h4>'),
			),
			array(
				'id' => 'animation_effect',
				'type' => 'animation_select',
				'label' => __( 'Effect', 'themify' )
			),
			// Background
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_image_background',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Background', 'themify').'</h4>'),
			),
			array(
				'id' => 'background_color',
				'type' => 'color',
				'label' => __('Background Color', 'themify'),
				'class' => 'small',
				'prop' => 'background-color',
				'selector' => array( '.module-newsletter .post' )
			),
			// Font
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_font',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Font', 'themify').'</h4>'),
			),
			array(
				'id' => 'font_family',
				'type' => 'font_select',
				'label' => __('Font Family', 'themify'),
				'class' => 'font-family-select',
				'prop' => 'font-family',
				'selector' => array( '.module-newsletter .post-title', '.module-newsletter .post-title a' ),
			),
			array(
				'id' => 'font_color',
				'type' => 'color',
				'label' => __('Font Color', 'themify'),
				'class' => 'small',
				'prop' => 'color',
				'selector' => array( '.module-newsletter .post', '.module-newsletter h1', '.module-newsletter h2', '.module-newsletter h3:not(.module-title)', '.module-newsletter h4', '.module-newsletter h5', '.module-newsletter h6', '.module-newsletter .post-title', '.module-newsletter .post-title a' ),
			),
			array(
				'id' => 'multi_font_size',
				'type' => 'multi',
				'label' => __('Font Size', 'themify'),
				'fields' => array(
					array(
						'id' => 'font_size',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'font_size_unit',
						'type' => 'select',
						'meta' => array(
							array('value' => '', 'name' => ''),
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => 'em', 'name' => __('em', 'themify'))
						)
					)
				)
			),
			array(
				'id' => 'multi_line_height',
				'type' => 'multi',
				'label' => __('Line Height', 'themify'),
				'fields' => array(
					array(
						'id' => 'line_height',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'line_height_unit',
						'type' => 'select',
						'meta' => array(
							array('value' => '', 'name' => ''),
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => 'em', 'name' => __('em', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					)
				)
			),
			array(
				'id' => 'text_align',
				'label' => __( 'Text Align', 'themify' ),
				'type' => 'radio',
				'meta' => array(
					array( 'value' => '', 'name' => __( 'Default', 'themify' ), 'selected' => true ),
					array( 'value' => 'left', 'name' => __( 'Left', 'themify' ) ),
					array( 'value' => 'center', 'name' => __( 'Center', 'themify' ) ),
					array( 'value' => 'right', 'name' => __( 'Right', 'themify' ) ),
					array( 'value' => 'justify', 'name' => __( 'Justify', 'themify' ) )
				),
				'prop' => 'text-align',
				'selector' => '.module-newsletter .post',
			),
			// Link
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_link',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Link', 'themify').'</h4>'),
			),
			array(
				'id' => 'link_color',
				'type' => 'color',
				'label' => __('Color', 'themify'),
				'class' => 'small',
				'prop' => 'color',
				'selector' => '.module-newsletter a'
			),
			array(
				'id' => 'text_decoration',
				'type' => 'select',
				'label' => __( 'Text Decoration', 'themify' ),
				'meta'	=> array(
					array('value' => '',   'name' => '', 'selected' => true),
					array('value' => 'underline',   'name' => __('Underline', 'themify')),
					array('value' => 'overline', 'name' => __('Overline', 'themify')),
					array('value' => 'line-through',  'name' => __('Line through', 'themify')),
					array('value' => 'none',  'name' => __('None', 'themify'))
				),
				'prop' => 'text-decoration',
				'selector' => '.module-newsletter a'
			),
			// Padding
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_padding',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Padding', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_padding_top',
				'type' => 'multi',
				'label' => __('Padding', 'themify'),
				'fields' => array(
					array(
						'id' => 'padding_top',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'padding-top',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'padding_top_unit',
						'type' => 'select',
						'description' => __('top', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_padding_right',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'padding_right',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'padding-right',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'padding_right_unit',
						'type' => 'select',
						'description' => __('right', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_padding_bottom',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'padding_bottom',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'padding-bottom',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'padding_bottom_unit',
						'type' => 'select',
						'description' => __('bottom', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_padding_left',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'padding_left',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'padding-left',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'padding_left_unit',
						'type' => 'select',
						'description' => __('left', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			// Margin
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_margin',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Margin', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_margin_top',
				'type' => 'multi',
				'label' => __('Margin', 'themify'),
				'fields' => array(
					array(
						'id' => 'margin_top',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'margin-top',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'margin_top_unit',
						'type' => 'select',
						'description' => __('top', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_margin_right',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'margin_right',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'margin-right',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'margin_right_unit',
						'type' => 'select',
						'description' => __('right', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_margin_bottom',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'margin_bottom',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'margin-bottom',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'margin_bottom_unit',
						'type' => 'select',
						'description' => __('bottom', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_margin_left',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'margin_left',
						'type' => 'text',
						'class' => 'xsmall',
						'prop' => 'margin-left',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'margin_left_unit',
						'type' => 'select',
						'description' => __('left', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			// Border
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_border',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Border', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_border_top',
				'type' => 'multi',
				'label' => __('Border', 'themify'),
				'fields' => array(
					array(
						'id' => 'border_top_color',
						'type' => 'color',
						'class' => 'small',
						'prop' => 'border-top-color',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_top_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall',
						'prop' => 'border-top-width',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_top_style',
						'type' => 'select',
						'description' => __('top', 'themify'),
						'meta' => Themify_Builder_model::get_border_styles(),
						'prop' => 'border-top-style',
						'selector' => '.module-newsletter .post',
					),
				)
			),
			array(
				'id' => 'multi_border_right',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_right_color',
						'type' => 'color',
						'class' => 'small',
						'prop' => 'border-right-color',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_right_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall',
						'prop' => 'border-right-width',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_right_style',
						'type' => 'select',
						'description' => __('right', 'themify'),
						'meta' => Themify_Builder_model::get_border_styles(),
						'prop' => 'border-right-style',
						'selector' => '.module-newsletter .post',
					)
				)
			),
			array(
				'id' => 'multi_border_bottom',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_bottom_color',
						'type' => 'color',
						'class' => 'small',
						'prop' => 'border-bottom-color',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_bottom_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall',
						'prop' => 'border-bottom-width',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_bottom_style',
						'type' => 'select',
						'description' => __('bottom', 'themify'),
						'meta' => Themify_Builder_model::get_border_styles(),
						'prop' => 'border-bottom-style',
						'selector' => '.module-newsletter .post',
					)
				)
			),
			array(
				'id' => 'multi_border_left',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_left_color',
						'type' => 'color',
						'class' => 'small',
						'prop' => 'border-left-color',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_left_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall',
						'prop' => 'border-left-width',
						'selector' => '.module-newsletter .post',
					),
					array(
						'id' => 'border_left_style',
						'type' => 'select',
						'description' => __('left', 'themify'),
						'meta' => Themify_Builder_model::get_border_styles(),
						'prop' => 'border-left-style',
						'selector' => '.module-newsletter .post',
					)
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_newsletter',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') )
			)
		);
		return $styling;
	}

	function set_metabox() {
		/** Newsletter Meta Box Options */
		$meta_box = array(
			// Feature Image
			Themify_Builder_Model::$post_image,
			// Featured Image Size
			Themify_Builder_Model::$featured_image_size,
			// Image Width
			Themify_Builder_Model::$image_width,
			// Image Height
			Themify_Builder_Model::$image_height,
			// Hide Title
			array(
				"name" 		=> "hide_post_title",
				"title"		=> __('Hide Post Title', 'themify'),
				"description"	=> "",
				"type" 		=> "dropdown",			
				"meta"		=> array(
					array("value" => "default", "name" => "", "selected" => true),
					array("value" => "yes", 'name' => __('Yes', 'themify')),
					array("value" => "no",	'name' => __('No', 'themify'))
				)
			),
			// Unlink Post Title
			array(
				"name" 		=> "unlink_post_title",
				"title" 		=> __('Unlink Post Title', 'themify'), 	
				"description" => __('Unlink post title (it will display the post title without link)', 'themify'), 				
				"type" 		=> "dropdown",			
				"meta"		=> array(
					array("value" => "default", "name" => "", "selected" => true),
					array("value" => "yes", 'name' => __('Yes', 'themify')),
					array("value" => "no",	'name' => __('No', 'themify'))
				)
			),
			// Hide Post Date
			array(
				"name" 		=> "hide_post_date",
				"title"		=> __('Hide Post Date', 'themify'),
				"description"	=> "",
				"type" 		=> "dropdown",			
				"meta"		=> array(
					array("value" => "default", "name" => "", "selected" => true),
					array("value" => "yes", 'name' => __('Yes', 'themify')),
					array("value" => "no",	'name' => __('No', 'themify'))
				)
			),
			// Hide Post Meta
			array(
				"name" 		=> "hide_post_meta",
				"title"		=> __('Hide Post Meta', 'themify'),
				"description"	=> "",
				"type" 		=> "dropdown",			
				"meta"		=> array(
					array("value" => "default", "name" => "", "selected" => true),
					array("value" => "yes", 'name' => __('Yes', 'themify')),
					array("value" => "no",	'name' => __('No', 'themify'))
				)
			),
			// Hide Post Image
			array(
				"name" 		=> "hide_post_image",
				"title" 		=> __('Hide Featured Image', 'themify'), 	
				"description" => "", 				
				"type" 		=> "dropdown",			
				"meta"		=> array(
					array("value" => "default", "name" => "", "selected" => true),
					array("value" => "yes", 'name' => __('Yes', 'themify')),
					array("value" => "no",	'name' => __('No', 'themify'))
				)			
			),
			// Unlink Post Image
			array(
				"name" 		=> "unlink_post_image",
				"title" 		=> __('Unlink Featured Image', 'themify'), 	
				"description" => __('Display the Featured Image without link)', 'themify'), 				
				"type" 		=> "dropdown",			
				"meta"		=> array(
					array("value" => "default", "name" => "", "selected" => true),
					array("value" => "yes", 'name' => __('Yes', 'themify')),
					array("value" => "no",	'name' => __('No', 'themify'))
				)
			),
			// External Link
			Themify_Builder_Model::$external_link,
			// Lightbox Link
			Themify_Builder_Model::$lightbox_link
		);
		return $meta_box;
	}

	function do_shortcode( $atts ) {
		global $ThemifyBuilder;

		extract( shortcode_atts( array(
			'id' => '',
			'title' => 'yes',
			'unlink_title' => 'no',
			'image' => 'yes', // no
			'image_w' => '',
			'image_h' => '',
			'display' => 'none', // excerpt, content
			'post_meta' => 'yes', // yes
			'post_date' => 'yes', // yes
			'more_link' => false, // true goes to post type archive, and admits custom link
			'more_text' => __('More &rarr;', 'themify'),
			'limit' => 4,
			'category' => 0, // integer category ID
			'order' => 'DESC', // ASC
			'orderby' => 'date', // title, rand
			'style' => '', // grid3, grid2
			'sorting' => 'no', // yes
			'page_nav' => 'no', // yes
			'paged' => '0', // internal use for pagination, dev: previously was 1
			// slider parameters
			'autoplay' => '',
			'effect' => '',
			'timeout' => '',
			'speed' => ''
		), $atts ) );

		$sync = array(
			'mod_title_newsletter' => '',
			'layout_newsletter' => $style,
			'category_newsletter' => $category,
			'post_per_page_newsletter' => $limit,
			'offset_newsletter' => '',
			'order_newsletter' => $order,
			'orderby_newsletter' => $orderby,
			'display_newsletter' => $display,
			'hide_feat_img_newsletter' => $image == 'yes' ? 'no' : 'yes',
			'image_size_newsletter' => '',
			'img_width_newsletter' => $image_w,
			'img_height_newsletter' => $image_h,
			'unlink_feat_img_newsletter' => 'no',
			'hide_post_title_newsletter' => $title == 'yes' ? 'no' : 'yes',
			'unlink_post_title_newsletter' => $unlink_title,
			'hide_post_date_newsletter' => $post_date == 'yes' ? 'no' : 'yes',
			'hide_post_meta_newsletter' => $post_meta == 'yes' ? 'no' : 'yes',
			'hide_page_nav_newsletter' => $page_nav == 'no' ? 'yes' : 'no',
			'animation_effect' => '',
			'css_newsletter' => ''
		);
		$module = array(
			'module_ID' => $this->slug . '-' . rand(0,10000),
			'mod_name' => $this->slug,
			'mod_settings' => $sync
		);

		return $ThemifyBuilder->retrieve_template( 'template-' . $this->slug . '.php', $module, '', '', false );
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
if( $this->is_cpt_active( 'newsletter' ) ) {
	Themify_Builder_Model::register_module( 'TB_Newsletter_Module' );
}