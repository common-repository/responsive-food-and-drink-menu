<?php 

defined( 'ABSPATH' ) or die();

// display shortcode
function rfadm_menu_shortcode_handler($atts, $content) {
	global $wp_query, $post;
	
	$args = array(
			'posts_per_page' => '1',
			'post_type'      => 'menu_page_cpt',
			'post_status'    => 'publish',
			'p'              => $atts['p'],
	);
	
	$the_query= new WP_Query($args);
	
	$output = '';
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			
			$menu_items      = get_post_meta($post->ID, 'menu_items', true);
			$section_names   = get_post_meta($post->ID, 'menu_section_name', true);
			$optional_footer = get_post_meta($post->ID, 'menu_optional_footer', true);
			$layout          = get_post_meta($post->ID, 'default_menu_layout', true);
			$featured        = get_the_post_thumbnail_url($post->ID);
			$options         = get_option('rfadm__settings');
			
			$menu_title_tag    = $options['rfadm__select_field_0'];
			$menu_content_tag  = $options['rfadm__select_field_1'];
			$section_title_tag = $options['rfadm__select_field_2'];
			$item_title_tag    = $options['rfadm__select_field_3'];
			$item_price_tag    = $options['rfadm__select_field_4'];
			$item_desc_tag     = $options['rfadm__select_field_5'];
			$menu_footer_tag   = $options['rfadm__select_field_6'];
			
			$menu_title = '<' . $menu_title_tag. '>' . esc_html(get_the_title($post->ID)) . '</' . $menu_title_tag. '>';
			$menu_content = '<' . $menu_content_tag . '>' . esc_html(get_the_content($post->ID)) . '</' . $menu_content_tag . '>';
			$menu_footer = '<' . $menu_footer_tag . ' class="menu_footer italic">' . esc_html($optional_footer) . '</' . $menu_footer_tag . '>';		
					
			$output .= '';
			
			if ( $layout == '3' ) { // "Modern" layout
				
				$output .= '<div class="menu_title centered-text">' . $menu_title . '</div>
					<div class="menu_main_text centered-text">' .  $menu_content . '</div>';
				
				foreach ( $section_names as $section ) {
					$output .= '
					<div class="menu_section">
						<div class="title centered-text"><' . $section_title_tag . '>' .   esc_html($section['section_name']) . '</' . $section_title_tag . '></div>
							<div class="items layout3_items">';
					
					$item_count = 0;
					foreach ( $menu_items as $item ) {
						$item_count = $item_count + 1;
						if ( $item['section'] == $section['section_id'] ) {
							$output .= '<div class="layout_3_item">';
							$output .= '<div class="item bold centered-text"><' . $item_title_tag . '>' .  esc_html($item['item_name']) . '</' . $item_title_tag . '></div>';
							$output .= '<div class="description centered-text"><' . $item_desc_tag . ' class="italic">' .  esc_html($item['item_description']) . '</' . $item_desc_tag . '></div>';
							$output .= '<div class="price centered-text"><' . $item_price_tag . '>' . esc_html($item['price']) . '</' . $item_price_tag . '></div>';
							$output .= '</div>';
						}
						
					}
					
					$output .= '</div>
						<div class="clear"></div>
					</div><!-- .menu_section -->';
				}
				
				$output .= '<div class="clear"></div>' . $menu_footer . '<div class="clear"></div>';
				
			} else if ( $layout == '2') { // "Image" layout
				
				wp_register_script('layout2-script', plugins_url( '../js/menu_layout_2_height_control.js', __FILE__ ),  array('jquery') );
				wp_enqueue_script('layout2-script');
				
				$output .=
				'<div class="rfadm_menu_wrap col-full">
					
					<div class="menu_title centered-text">' . $menu_title . '</div>
					<div class="menu_main_text centered-text">' . $menu_content . '</div>

					<div class="rfadm_layout2_container">';
					
					if ( !empty($featured) ) {
						$output .= '<div class="menu_image_layout_wrap col-half" style="background-image: url(\'' . $featured . '\');"></div>';
					}
										
					$output .= '<div class="' . (!empty($featured) ? 'col-half' : 'col-full') . ' layout_2_text_wrap">';
					
						$output .= '<div id="rfadm_menu">';
				
						foreach ( $section_names as $section ) {
							$output .= '<div class="menu_section">
									<div class="col-full title"><' . $section_title_tag . '>' .  esc_html($section['section_name']) . '</' . $section_title_tag . '></div>
										<div class="col-full items">';
							
							foreach ( $menu_items as $item ) {
								if ( $item['section'] == $section['section_id'] ) {
									$output .= '<div class="item bold"><' . $item_title_tag . '>' . esc_html($item['item_name']) . '</' . $item_title_tag . '></div>';
									$output .= '<div class="price"><' . $item_price_tag . '>' . esc_html($item['price']) . '</' . $item_price_tag . '></div>';
									$output .= '<div class="col-full description"><' . $item_desc_tag . ' class="italic">' . esc_html($item['item_description']) . '</' . $item_desc_tag . '></div>';
								}
							}
							
							$output .= '</div>
								<div class="clear"></div>
							</div>';
						}
				
				$output .= '</div></div>
				</div><div class="clear"></div>' . $menu_footer . '</div><!-- .rfadm_menu_wrap -->';
				
			} else { // "Classic" layout
								
				$output .=
					'<div class="rfadm_menu_wrap">
					<div class="menu_title centered-text">' . $menu_title . '</div>
					<div class="menu_main_text centered-text">' . $menu_content . '</div>';
					
					$output .= '<div id="rfadm_menu">';
					
					foreach ( $section_names as $section ) {
						$output .= '<div class="menu_section">
							<div class="col-full title"><' . $section_title_tag . '>' .  esc_html($section['section_name']) . '</' . $section_title_tag . '></div>
								<div class="col-full items">';
						
						foreach ( $menu_items as $item ) {
							if ( $item['section'] == $section['section_id'] ) {
								$output .= '<div class="layout_1_item_wrap">';
								$output .= '<div class="item bold"><' . $item_title_tag . '>' . esc_html($item['item_name']) . '</' . $item_title_tag . '></div>';
								$output .= '<div class="price"><' . $item_price_tag . '>' . esc_html($item['price']) . '</' . $item_price_tag . '></div>';
								$output .= '<div class="col-full description"><' . $item_desc_tag . ' class="italic">' . esc_html($item['item_description']) . '</' . $item_desc_tag . '></div>';
								$output .= '</div>';
							}
						}
						
						$output .= '</div>
						<div class="clear"></div>
						</div>';
					}
					
					$output .= '</div><!-- #menu -->' . $menu_footer . '
				</div><!-- .wrap -->';
			}
		}
	} else {
		// no posts found
	}
	
	wp_reset_query();
	wp_reset_postdata();
	return $output . '<div class="clear"></div>';
	
}
add_shortcode( 'display_menu', 'rfadm_menu_shortcode_handler');
?>