<?php 

defined( 'ABSPATH' ) or die();

// display shortcode
function rfadm_pdf_menu_shortcode_handler($atts, $content) {
	global $wp_query, $post;
	
	$args = array(
			'posts_per_page' => '-1',
			'post_type'      => 'pdf_menu_page_cpt',
			'post_status'    => 'publish',
			'layout'         => (isset($atts['layout']) ? $atts['layout'] : '1'),
	);
	
	if (isset($atts['layout'])) {
		$layout = $atts['layout'];
	} else {
		$layout = '1';
	}
	
	$the_query= new WP_Query($args);
	
	$output = '<div style="max-width: 100%; width: 100%;" class="rfadm_pdf_shortcode pdf_menu_widget_layout_' . $layout . '">';
	
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			
			$pdf      = get_post_meta($post->ID, 'rfadm_pdf_menu_pdf', true);
			$featured = get_the_post_thumbnail_url($post->ID);
			$options  = get_option('rfadm__settings');
			
			$menu_title_tag = $options['rfadm__select_field_0'];
			
			if ( ($layout == '3' || $layout == '2') && isset($pdf['url']) ) { // START "Grid" layout
				
				if ( !empty($featured) ) {
					$style = 'background-image: url(\'' . $featured . '\');';
				} else {
					$style = '';
				}
				
				if ($layout == '2') {
					$style .= 'border-radius: 50%;';
				}

				$output .= '
				<div class="pdf_menu_widget_grid_item_wrap">
					<a target="_blank" href="' . esc_url($pdf['url']) . '">
						<div class="pdf_menu_widget_grid_item" style="' . $style . '">
							<span class="centered_v_h"><' . $menu_title_tag . ' class="pdf_menu_title">' . esc_html(get_the_title()) . '</' . $menu_title_tag . '></span>
						</div>
					</a>
				</div>';
				
			} else { 
								
				if ( isset($pdf['url']) ) {
					
					if ( !empty($featured) ) {
						$style = 'background-image: url(\'' . $featured . '\');';
					} else {
						$style = 'border: 1px solid lightgrey;';
					}
					
					$output .= '
<a target="_blank" href="' . esc_url($pdf['url']) . '" class="effect-me">
	<div class="col-full pdf_menu_img_wrap" style="' . $style . '">
		<span class="centered_v_h"><' . $menu_title_tag . ' class="pdf_menu_title_full">' . esc_html(get_the_title()) . '</' . $menu_title_tag . '></span>
	</div>
</a>';
				}		
			}
		}
	} else {
		// no posts found
	}
	
	wp_reset_query();
	wp_reset_postdata();
	return $output . '<div class="clear"></div></div>';
	
}
add_shortcode( 'display_pdf_menus', 'rfadm_pdf_menu_shortcode_handler');
?>