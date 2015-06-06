<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php
/** Check if slider is enabled */
if('' == themify_get('setting-header_slider_enabled') || 'on' == themify_get('setting-header_slider_enabled')) { ?>

	<?php themify_slider_before(); //hook ?>
	<div id="header-slider" class="pagewidth slider">
    	<?php themify_slider_start(); //hook ?>
			
		<ul class="slides clearfix">

    		<?php
    		// Get image width and height or set default dimensions
			$img_width = themify_check('setting-header_slider_width')?	themify_get('setting-header_slider_width'): '220';
			$img_height = themify_check('setting-header_slider_height')? themify_get('setting-header_slider_height'): '160';
			
			if(themify_check('setting-header_slider_posts_category')){
				$cat = "&cat=".themify_get('setting-header_slider_posts_category');	
			} else {
				$cat = "";
			}
			if(themify_check('setting-header_slider_posts_slides')){
				$num_posts = "showposts=".themify_get('setting-header_slider_posts_slides')."&";
			} else {
				$num_posts = "showposts=7&";	
			}
			if(themify_check('setting-header_slider_display') && themify_get('setting-header_slider_display') == "images"){ 
        		
				$options = array('one','two','three','four','five','six','seven','eight','nine','ten');
				foreach($options as $option){
					$option = 'setting-header_slider_images_'.$option;
					if(themify_check($option.'_image')){
						echo '<li>';
							$title = function_exists( 'icl_t' )? icl_t('Themify', $option.'_title', themify_get($option.'_title')) : ( themify_check($option.'_title') ? themify_get($option.'_title') : '' );
							$image = themify_get($option.'_image');
							$alt = $title? $title : $image;
							if(themify_check($option.'_link')){ 
								$link = themify_get($option.'_link');
								$title_attr = $title? "title='$title'" : "title='$image'";
								echo "<div class='slide-feature-image'><a href='$link' $title_attr>" . themify_get_image("src=".$image."&ignore=true&w=$img_width&h=$img_height&alt=$alt&class=feature-img") . '</a></div>';
								echo $title? '<div class="slide-content-wrap"><h3 class="slide-post-title"><a href="'.$link.'" '.$title_attr.'>'.$title.'</a></h3></div>' : '';
							} else {
								echo "<div class='slide-feature-image'>" . themify_get_image("src=".$image."&ignore=true&w=$img_width&h=$img_height&alt=".$alt."&class=feature-img") . '</div>';
								echo $title? '<div class="slide-content-wrap"><h3 class="slide-post-title">'.$title.'</h3></div>' : '';
							}
						echo '</li>';
					}
				}
			} else { 

				query_posts($num_posts.$cat); 
				
				if( have_posts() ) {
					
					while ( have_posts() ) : the_post();
						?>                

					<?php $link = themify_get_featured_image_link(); ?>

						<?php $post_format = $themify->get_format_template(); ?>
						<?php $post_color_class = (themify_check('post_color') && themify_get('post_color') != 'default') ? themify_get('post_color') : themify_get('setting-default_color');  ?>
                    	<li class="<?php echo $post_color_class; ?> format-<?php echo $post_format; ?>">
							
							<?php if($post_format == 'video'): ?>
							<!-- post-video -->
							<?php if(themify_get("video_url") != '') :
								
								global $wp_embed;
								echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
								
							endif; ?>
							<!-- /post-video -->
							<?php else: ?>
							<?php if( $post_image = themify_get_image($themify->auto_featured_image) ): ?>
							<div class='slide-feature-image'>
								<a href="<?php echo $link; ?>" title="<?php the_title_attribute(); ?>">
									<?php themify_image($themify->auto_featured_image."ignore=true&w=$img_width&h=$img_height&class=feature-img&alt=".get_the_title()); ?>
								</a>
							</div>
							<!-- /.slide-feature-image -->
							<?php endif; ?>
							<?php endif; ?>

							<div class="slide-content-wrap">

								<?php if(themify_get('setting-header_slider_hide_title') != 'yes'): ?>
									<h3 class="slide-post-title"><a href="<?php echo $link; ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
								<?php endif; ?>
								
								<?php if($post_format == 'audio'): ?>
								<!-- audio-player -->
								<div class="audio-player">
								<?php
									$src = themify_get("audio_url");
									$fallbackpl = '<a href="'.$src.'" title="' . __('Click to open', 'themify') . '" id="f-'.$post->ID.'" style="display:none;">' . __('Audio MP3', 'themify') . '</a><script type="text/javascript">AudioPlayer.embed("f-'.$post->ID.'", {soundFile: "'.$src.'"});</script>';
									
									if(strpos(strtolower($src),'.wav')) $format = 'wav';
									if(strpos(strtolower($src),'.m4a')) $format = 'm4a';
									if(strpos(strtolower($src),'.ogg')) $format = 'ogg';
									if(strpos(strtolower($src),'.oga')) $format = 'oga';
									if(strpos(strtolower($src),'.mp3')) $format = 'mp3';
									
									if(strpos($format, 'og')) $html5incompl = false; else $html5incompl = true;
									
									$output = '<div class="audio_wrap html5audio">';
									if ($html5incompl) $output .= '<div style="display:none;">'.$fallbackpl.'</div>';
									$output .= '<audio controls id="' . $post->ID . '" class="html5audio">';
									
									if ($format == 'wav') $output .= '<source src="'.$src.'" type="audio/wav" />';
									if ($format == 'm4a') $output .= '<source src="'.$src.'" type="audio/mp4" />';
									if ($format == 'oga') $output .= '<source src="'.$src.'" type="audio/ogg" />';
									if ($format == 'ogg') $output .= '<source src="'.$src.'" type="audio/ogg" />';
									if ($format == 'mp3') $output .= '<source src="'.$src.'" type="audio/mpeg" />';
									
									$output .= $fallbackpl . '</audio></div>';
									
									if ($html5incompl) $output .= '<script type="text/javascript">if (jQuery.browser.mozilla) {tempaud=document.getElementsByTagName("audio")[0]; jQuery(tempaud).remove(); jQuery("div.audio_wrap div").show()} else jQuery("div.audio_wrap div *").remove();</script>';
									
									echo $output;
								?>
								</div>
								<!-- /audio-player -->
								<?php endif; ?>
								
								<?php if(themify_get('setting-header_slider_default_display') == 'content'): ?>
									<div class="slide-excerpt <?php echo $post_format; ?>-content">
									<?php the_content(); ?>
									</div>
								<?php elseif( ! themify_get('setting-header_slider_default_display') || themify_get('setting-header_slider_default_display') == 'none'): ?>
										<?php //none ?>
								<?php else: ?>
									<div class="slide-excerpt <?php echo $post_format; ?>-content">
									<?php the_excerpt(); ?>
									</div>
								<?php endif; ?>
									
							</div>
							<!-- /.slide-content-wrap -->
						
							<?php if($post_format == 'quote'):
								// Quote
								$quote_author = themify_get('quote_author');
								$quote_author_link = themify_get('quote_author_link');
							?>
								<!-- quote-author -->
								<p class="quote-author">
									&#8212; <?php if($quote_author_link != '') { echo '<a href="'.$quote_author_link.'">'; } ?><?php echo $quote_author; ?><?php if($quote_author_link != '') { echo '</a>'; } ?>
								</p>
								<!-- /quote-author -->
							<?php endif; ?>
						
                 		</li>
               			<?php 
					endwhile; 
				}
				
				wp_reset_query();
				
			} 
			?>
		</ul>
	  	
        <?php themify_slider_end(); //hook ?>
	</div>
	<!-- /#slider -->
    <?php themify_slider_after(); //hook ?>
    
<?php } ?>