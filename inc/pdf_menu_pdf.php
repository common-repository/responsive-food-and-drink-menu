<?php 

defined( 'ABSPATH' ) or die();

///////////////////////////////// PDF //////////////////////////////
function rfadm_pdf_menu_pdf_metabox() {
	
	add_meta_box(
			'rfadm_pdf_menu_pdf',
			'PDF Menu',
			'rfadm_pdf_menu_pdf_fill_metabox',
			'pdf_menu_page_cpt'
			);
	
}
add_action('add_meta_boxes', 'rfadm_pdf_menu_pdf_metabox');

function rfadm_pdf_menu_pdf_fill_metabox() {
	//wp_nonce_field(plugin_basename(__FILE__), 'rfadm_pdf_menu_pdf_nonce');
	$html = '';
	
	// Grab the array of file information currently associated with the post
	$doc = get_post_meta(get_the_ID(), 'rfadm_pdf_menu_pdf', true);
	
	$html .= '<p class="description">';
	$html .= 'If you have a PDF of your menu, you can upload it here.';
	$html .= '</p><br>';
	
	if (isset($doc['url'])) {
		$html .= '<span class="current_pdf">The current menu is: <a href="' . esc_url($doc['url']) . '" target="_blank">' . esc_url($doc['url']) . '</a><br><br>';
		$html .= '<strong>If you upload a new menu, the current one will be replaced.</strong><br><br></span>';
	}
	
	$html .= '<input type="file" accept="application/pdf" id="rfadm_pdf_menu_pdf" name="rfadm_pdf_menu_pdf" value="" size="25" />';
	
	// Create the input box and set the file's URL as the text element's value
	$html .= '<input type="hidden" id="rfadm_menu_pdf_url" name="rfadm_pdf_menu_pdf_url" value=" ' . (isset($doc['url']) ? esc_url($doc['url']) : '') . '" />';
	
	// Display the 'Delete' option if a URL to a file exists
	if(isset($doc['url']) && strlen(trim($doc['url'])) > 0) {
		$html .= '<a href="javascript:;" id="rfadm_menu_pdf_delete">Delete PDF</a>';
	}
	
	echo $html;
}

function rfadm_save_pdf_menu_pdf($id) {
	
	/* --- security verification --- */
	/*
	 if (!wp_verify_nonce($_POST['rfadm_pdf_menu_pdf_nonce'], plugin_basename(__FILE__))) {
	 return $id;
	 }
	 */
    
    global $post;
    
    if ($post->post_type != 'pdf_menu_page_cpt') {
        return $post_id;
    }
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $id;
	}
	
	if (isset($_POST['post_type']) && $_POST['post_type'] == 'pdf_menu_page_cpt') {
		if (!current_user_can('edit_page', $id)) {
			return $id;
		}
	} else {
		if (!current_user_can('edit_page', $id)) {
			return $id;
		}
	}
	/* - end security verification - */
	
	if (!empty($_FILES['rfadm_pdf_menu_pdf']['name'])) {
		
		// Setup the array of supported file types. In this case, it's just PDF.
		$supported_types = array('application/pdf');
		
		// Get the file type of the upload
		$arr_file_type = wp_check_filetype(basename($_FILES['rfadm_pdf_menu_pdf']['name']));
		$uploaded_type = $arr_file_type['type'];
		
		// Check if the type is supported. If not, throw an error.
		if (in_array($uploaded_type, $supported_types)) {
			
			// Use the WordPress API to upload the file
			$upload = wp_upload_bits($_FILES['rfadm_pdf_menu_pdf']['name'], null, file_get_contents($_FILES['rfadm_pdf_menu_pdf']['tmp_name']));
			
			if (isset($upload['error']) && $upload['error'] != 0) {
				wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
			} else {
				add_post_meta($id, 'rfadm_pdf_menu_pdf', $upload);
				update_post_meta($id, 'rfadm_pdf_menu_pdf', $upload);
			}
			
		} else {
			wp_die("The file that you've uploaded as the Menu is not a PDF. Please try again with a PDF file.");
		}
	} else if (!empty( $_POST['rfadm_pdf_menu_pdf_url'] )) {
		// do nothing
	} else {
		// Grab a reference to the file associated with this post
		$doc = get_post_meta($id, 'rfadm_pdf_menu_pdf', true);
		
		// Grab the value for the URL to the file stored in the text element
		$delete_flag = get_post_meta($id, 'rfadm_menu_pdf_url', true);
		
		// Determine if a file is associated with this post and if the delete flag has been set (by clearing out the input box)
		if ( isset($doc['url']) && strlen(trim($doc['url'])) > 0 && strlen(trim($delete_flag)) == 0) {
			// Attempt to remove the file. If deleting it fails, print a WordPress error.
			if(unlink($doc['file'])) {
				
				// Delete succeeded so reset the WordPress meta data
				update_post_meta($id, 'rfadm_pdf_menu_pdf', null);
				update_post_meta($id, 'rfadm_menu_pdf_url', '');
				
			} else {
				wp_die('There was an error trying to delete your file.');
			} // end if/el;se
		}
	}
}
add_action('save_post', 'rfadm_save_pdf_menu_pdf');
?>