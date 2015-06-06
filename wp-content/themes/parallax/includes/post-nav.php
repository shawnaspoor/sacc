<?php
/**
 * Post Navigation Template
 * @package themify
 * @since 1.0.0
 */

$post_type = 'portfolio' == get_post_type() ? 'portfolio' : 'post';

if ( ! themify_check( "setting-{$post_type}_nav_disable" ) ) :

	if ( ! is_attachment() ):
		$in_same_cat = themify_check( "setting-{$post_type}_nav_same_cat" )? true: false;
		$this_taxonomy = 'post' == get_post_type() ? 'category' : get_post_type() . '-category';
		$previous = get_previous_post_link( '<span class="prev">%link</span>', '<span class="arrow">' . _x( '&laquo;', 'Previous entry link arrow', 'themify' ) . '</span> %title', $in_same_cat, '', $this_taxonomy );
		$next = get_next_post_link( '<span class="next">%link</span>', '<span class="arrow">' . _x( '&raquo;', 'Next entry link arrow', 'themify' ) . '</span> %title', $in_same_cat, '', $this_taxonomy );
	else:
		ob_start();
		previous_image_link( 'large', '<span class="arrow">&laquo;</span>' . __( 'Previous Image', 'themify' ) );
		$previous = ob_get_clean();
		ob_start();
		next_image_link( 'large', '<span class="arrow">&raquo;</span>' . __( 'Next Image', 'themify' ) );
		$next = ob_get_clean();
		if ( !empty( $previous ) ) {
			$previous = '<span class="prev">' . $previous . '</span>';
		}
		if ( !empty( $next ) ) {
			$next = '<span class="next">' . $next . '</span>';
		}
	endif; // is attachment

	if ( ! empty( $previous ) || ! empty( $next ) ) : ?>

		<div class="post-nav clearfix">
			<?php echo $previous; ?>
			<?php echo $next; ?>
		</div>
		<!-- /.post-nav -->

	<?php endif; // empty previous or next

endif; // check setting nav disable ?>