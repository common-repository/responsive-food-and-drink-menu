<?php 

defined( 'ABSPATH' ) or die();

/* handle meta below */
add_action( 'add_meta_boxes', 'rfadm_dynamic_add_custom_box' );

/* Adds a box to the main column on the Post and Page edit screens */
function rfadm_dynamic_add_custom_box() {
	add_meta_box (
			'default_menu_layout',
			'Menu Layout',
			'rfadm_layout_custom_box',
			'menu_page_cpt',
			'side'
	);
	
	add_meta_box (
			'dynamic_sectionid',
			'Menu Items',
			'rfadm_dynamic_inner_custom_box',
			'menu_page_cpt' 
	);
	
	add_meta_box (
			'optional_footer',
			'Optional Footer',
			'rfadm_footer_custom_box',
			'menu_page_cpt' 
	);
}

function rfadm_footer_custom_box() { 
	global $post; 
	$val = get_post_meta ( $post->ID, 'menu_optional_footer', true ); ?>

	<textarea name="menu_optional_footer" id="menu_optional_footer" rows="5" style="width: 100%;"><?php echo $val; ?></textarea>
	
	<?php
}

function rfadm_layout_custom_box() {
	global $post; 
	$current = get_post_meta ( $post->ID, 'default_menu_layout', true ); ?>
	
	<select name="default_menu_layout" id="default_menu_layout">
		<option value="1" <?php if ($current == '1') { echo 'selected=selected'; } ?>>Classic</option>
		<option value="2" <?php if ($current == '2') { echo 'selected=selected'; } ?>>Image</option>
		<option value="3" <?php if ($current == '3') { echo 'selected=selected'; } ?>>Modern</option>
	</select>
	
	<?php
}

function rfadm_dynamic_inner_custom_box() {
	global $post;
	
	//wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' ); // Use nonce for verification
	
	if ( get_post_status($post->ID) == 'publish' ) { ?>
		<p><strong>Reminder: </strong>You can use this menu in any post or page by using this shortcode: [display_menu p=<?php echo $post->ID; ?>]</p>
	<?php } ?>
	
	<input type="button" value="Add New Section" class="add_section button-primary button" />
	
	<?php 
	$menu_items = get_post_meta ( $post->ID, 'menu_items', true );
	$section_names = get_post_meta ( $post->ID, 'menu_section_name', true );
	
	if (empty($section_names)) {
	   $section_names = array();
	}
	
	$item_count = 0;
	$section_count = 0;
	
	if ( count ( $section_names ) == 0 ) { ?>
	
	<div class="menu_section" id="section_0">
		<div class="menu_inner">
			<div class="sec_title">
				Section Title: <br><input type="text" class="title" name="menu_section[0][section_name]" value="" /><input type="hidden" class="title" name="menu_section[0][section_id]" value="0" /><a class="remove_section_link">Remove Section</a>
			</div>
    	<span class="item_titles underline">Menu Item</span><span class="item_prices underline">Price</span><!--<span class="item_images underline">Image</span>-->
		<p class="menu_item_wrap" id="item_0">
			<input type="text"   name="menu_items[0][item_name]" value="" class="item_name" /> 
			<input type="text"   name="menu_items[0][price]"     value="" class="item_price"/>
			<input type="hidden" name="menu_items[0][section]"   value="0" />
			<!-- <input type="file"   name="menu_items[0][image]"     value="" id="item_pic_0_0" accept="image/*" class="item_file"/>-->
			<input type="hidden" name="menu_items[0][image_url]" value="" id="item_pic_url_0_0" class="img_url_input"/>
			<span class="show_image_name"></span>
			<!--<a href="javascript:;" class="rfadm_menu_item_delete">Delete Image</a>-->
			<input type="button" value="X" class="button remove_item" /><br>
			<input type="text" value="" name="menu_items[0][item_description]" placeholder="Optional item description..." class="item_description" />
		</p>
		</div>
		<input type="button" value="Add New Item" class="add_item button" />
	</div>
	<?php } ?>
		
    <div id="meta_inner">
    <span id="menu">
    <?php
    
    if ( count ( $section_names ) > 0 && !empty ( $section_names ) ) {
    	foreach ( $section_names as $section ) {
    		$section_count = $section_count + 1;
    		echo '
			<div class="menu_section" id="section_' . $section['section_id'] . '">
				<div class="menu_inner">
					<div class="sec_title">
						Section Title: <br><input type="text" class="title" name="menu_section[' . $section['section_id'] . '][section_name]" value="' . $section['section_name'] . '" /><input type="hidden" class="title" name="menu_section[' . $section['section_id'] . '][section_id]" value="' . $section['section_id'] . '" /><a class="remove_section_link">Remove Section</a>
					</div>';
    		
    		// display menu items
    		$new_itemID = '';
    		if ( count ( $menu_items ) > 0 ) {
    			echo '<span class="item_titles underline">Menu Item</span><span class="item_prices underline">Price</span>';
	    		foreach ($menu_items as $item) {
	    			$item_count = $item_count + 1;
	    			if ( $item['section'] == $section['section_id'] ) {
	    				
	    				// <input type="file"   name="menu_items[' . $item_count . '][image]"     value="' . (isset($item['image']) ? $item['image'] : '') . '"    id="item_pic_' . $section['section_id']. '_' . $item_count . '" accept="image/*" class=""/>
	    				echo '<p class="menu_item_wrap" id="item_' . $new_itemID . '">
								<input type="text"   name="menu_items[' . $item_count . '][item_name]" value="' . $item['item_name'] . '" class="item_name" /> 
								<input type="text"   name="menu_items[' . $item_count . '][price]"     value="' . $item['price'] . '" />
								<input type="hidden" name="menu_items[' . $item_count . '][section]"   value="' . $section['section_id'] . '" />
								
								<input type="hidden" name="menu_items[' . $item_count . '][image_url]" value="' . (isset($item['image_url']) ? $item['image_url'] : '') . '" id="item_pic_url_' . $section['section_id']. '_' . $item_count . '" class="img_url_input"/>
								
								<span class="show_image_name">' . basename ($item['image_url']) . '</span>
								' . (isset($item['image_url']) && strlen(trim($item['image_url'])) > 0 ? '<a href="javascript:;" class="rfadm_menu_item_delete">Delete Image</a>' : '') .
								'<input type="button" value="X" class="button remove_item" />
								<input type="text" value="' . (isset($item['item_description']) ? $item['item_description'] : '') . '" name="menu_items[' . $item_count . '][item_description]" placeholder="Optional item description..." class="item_description" />
							</p>';
	    			} 
	    		}
    		}
    		
			echo '</div>
				<input type="button" value="Add New Item" class="add_item button" />
			</div>';
    	}
    }
    
    //echo '<div class="menu_section" id="section_' . $new_secID . '">Section Title: <input type="text" name="menu_section_name" value="' . $section_names . '" /><span class="add_item">Add Menu Item</span></div>';

    ?>
	</span><!-- END: #menu -->

<script>
    var $ = jQuery.noConflict();
    
    function getUniqueID(the_object) {
    	var the_ids = [];
    	
    	jQuery(the_object).each(function() {
        	id_attr = jQuery(this).attr('id');
        	curr_id = id_attr.substr(id_attr.length - 1);
    		the_ids.push(parseInt(curr_id));
    	});
    	
    	var i = 1;
    	
    	jQuery.each(the_ids, function(index, value) {
    		if (jQuery.inArray(i, the_ids) === -1) {
    			// not found, do nothing
    		} else {
    			// found
    			i = i + 1;
    		}
    	});
    	
    	return i;
    }
    
    $(document).ready(function() {

		// add new section
    	var numOfSections = <?php echo $section_count; ?>;
		$('.add_section').live('click', function() {
			numOfSections = numOfSections + 1;
			new_secID = getUniqueID('.menu_section');
			$('#menu').append('<div class="menu_section" id="section_' + new_secID + '"><div class="menu_inner"><div class="sec_title">Section Title: <br><input type="text" class="title" name="menu_section[' + new_secID + '][section_name]" value="" /><input type="hidden" class="title" name="menu_section[' + new_secID + '][section_id]" value="' + new_secID + '" /><a class="remove_section_link">Remove Section</a></div><span class="item_titles underline">Menu Item</span><span class="item_prices underline">Price</span></div><input type="button" value="Add New Item" class="add_item button" /></div>');
		});

		// add new item
    	var numOfItems = <?php echo $item_count; ?>;
    	$(".add_item").live('click', function() {
    		numOfItems = numOfItems + 1;
    		new_itemID = getUniqueID('.menu_item_wrap');
    		id_attr    = $(this).parents('.menu_section').attr('id');
    		curr_sec   = id_attr.substr(id_attr.length - 1);

  		  	// <input type="file"   name="menu_items[' + numOfItems + '][image]"  value="" id="item_pic_' + curr_sec + '_' + numOfItems + '" accept="image/*" class=""/>                                                           
    		$(this).parents('.menu_section').find('.menu_inner').append('<p class="menu_item_wrap" id="item_' + new_itemID + '"> <input type="text" class="item_name" name="menu_items[' + numOfItems + '][item_name]" value="" /><input type="text" class="item_price" name="menu_items[' + numOfItems + '][price]" value="" /><input type="hidden" name="menu_items[' + numOfItems + '][section]" value="' + curr_sec + '"/><input type="hidden"   name="menu_items[' + numOfItems + '][image_url]" value="" id="item_pic_url_' + curr_sec + '_' + numOfItems + '" class="img_url_input"/><input type="button" class="button remove_item" value="X" /><input type="text" value="" name="menu_items[' + numOfItems + '][item_description]" placeholder="Optional item description..." class="item_description" /></p>');
    	});
    	
		// remove section
        $(".remove_section_link").live('click', function() {
        	var r = confirm("Are you sure you want to delete this entire section?");
        	if (r == true) {
        		$(this).parents('.menu_section').remove();
        	}
        });

		// remove single item
        $(".remove_item").live('click', function() {
            $(this).parents('.menu_item_wrap').remove();
        });
    });
    </script>
</div><?php

}

add_action( 'save_post', 'rfadm_dynamic_save_postdata' );
function rfadm_dynamic_save_postdata( $post_id ) {
    
    global $post;
    
    if ($post->post_type != 'menu_page_cpt') {
        return $post_id;
    }
    
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    /*
    if ( !isset( $_POST['dynamicMeta_noncename'] ) )
        return;

    if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) )
        return;
	*/
    
    if (!current_user_can('edit_page', $post_id)) {
    	return $post_id;
    }
    
    // OK, we're authenticated: we need to find and save the data
    $menu_items    = ( isset( $_POST['menu_items'] ) ? $_POST['menu_items'] : array() );
    $section_names = ( isset( $_POST['menu_section'] ) ? $_POST['menu_section'] : array() );
    
    $item_count = 0;
    $section_count = 0;
    
    //$image_files = array_fill(0, count($menu_items), '');
    //$image_file_urls = array_fill(0, count($menu_items), '');
    
    if ( count ( $section_names ) > 0 && !empty ( $section_names ) ) {
    	foreach ( $section_names as $section ) {
    		$section_count = $section_count + 1;
    		if ( count ( $menu_items ) > 0 ) {    			
		    	foreach ($menu_items as $key => $field) {
		    		$item_count = $item_count + 1;
		    		$image_file = '';
		    		$image_file_url = '';
		    		
		    		if ( !empty($_POST['menu_items'][$item_count]['image_url'] ) ) {
		    			// already has an image, end current foreach iteration
		    			break;
		    		}
		    		
		    		if ( !empty($_FILES['menu_items']['name'][$item_count] )) {
		    		//if ( !empty($_FILES['item_pic_' . $section['section_id'] . '_' . $item_count]['name'] )) {
		    			
		    			$supported_types = array(		
		    				'image/jpeg',
		    				'image/pjpeg',
		    				'image/jpeg',
		    				'image/pjpeg',
		    				'image/png',
		    				'image/tiff',
		    				'image/gif'	
		    			);
		    			
		    			$arr_file_type = wp_check_filetype(basename($_FILES['menu_items']['name'][$item_count]['image']));
		    			$uploaded_type = $_FILES['menu_items']['type'][$item_count]['image'];
		    			
		    			
		    			// Check if the type is supported. If not, throw an error.
		    			if (in_array($uploaded_type, $supported_types) || empty($_FILES['menu_items']['name'][$item_count]['image']) ) {
		    				
		    				// Use the WordPress API to upload the file
			    			$upload = wp_upload_bits($_FILES['menu_items']['name'][$item_count]['image'], 
			    				null, 
			    				file_get_contents($_FILES['menu_items']['tmp_name'][$item_count]['image']));
		    				
		    				
		    				if (isset($upload['error']) && $upload['error'] != 0) {
		    					wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
		    				} else {
		    					
		    					// update or set var to update here
		    					$image_file = sanitize_text_field($upload['file']);
		    					$image_file_url = sanitize_text_field($upload['url']);
		    					
		    					//wp_die($image_file . " ======== " . $image_file_url);
		    				}
		    				
		    			} else {
		    				wp_die("A menu item image in section " . $section['section_id'] . " is not a supported image file type.");
		    			}
		    			
		    			$menu_items[$item_count]['image_url'] = $image_file_url;
		    			$menu_items[$item_count]['image'] = $image_file;
		    		}			    	
		    	} // end foreach item
    		}
    	} // end foreach section
    }

    array_walk ( $section_names, function ( &$value, &$key ) {
    	$value['section_name'] = sanitize_text_field ( $value['section_name'] );
    	$value['section_id']   = sanitize_text_field ( $value['section_id'] );
    });

    update_post_meta ( $post_id, 'menu_items', $menu_items);
    update_post_meta ( $post_id, 'menu_section_name', $section_names );
    update_post_meta ( $post_id, 'menu_optional_footer', ( isset($_POST['menu_optional_footer']) ? sanitize_text_field( $_POST['menu_optional_footer'] ) : '' ) );
    update_post_meta ( $post_id, 'default_menu_layout', ( isset($_POST['default_menu_layout']) ? absint( $_POST['default_menu_layout'] ) : '' ) );
}
?>