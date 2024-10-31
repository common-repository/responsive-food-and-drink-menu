<?php 
/*
 Plugin Name: Responsive Food and Drink Menu
 Description: Quicky and easily create a responsive food or drink menu for your business, or use your existing PDF menus to display your menus in various layouts, in any location.
 Version:     2.3
 Author:      Corporate Zen
 Author URI:  http://www.corporatezen.com/
 License:     GPL3 or later
 License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 
 Responsive Food and Drink Menu is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.
 
 Responsive Food and Drink Menu is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Responsive Food and Drink Menu. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Error: Direct access to this code is not allowed.' );


require_once 'inc/menu_metadata.php';
require_once 'inc/menu_shortcode.php';

require_once 'inc/pdf_menu_cpt.php';
require_once 'inc/pdf_menu_pdf.php';
require_once 'inc/pdf_menu_shortcode.php';

//require_once 'inc/cz_newsletter.php';

// de-activate hook
function rfadm_deactivate_plugin() {
	// clear the permalinks to remove our post type's rules
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rfadm_deactivate_plugin' );


// activation hook
function rfadm_active_plugin() {
	// trigger our function that registers the custom post type
	rfadm_setup_post_type();
	
	// clear the permalinks after the post type has been registered
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rfadm_active_plugin' );

// Our custom post type function
function rfadm_setup_post_type() {
	
	$labels = array(
			'name'                => 'Menu',
			'singular_name'       => 'Menu',
			'menu_name'           => 'Menus',
			'all_items'           => 'All Menus',
			'view_item'           => 'View Menu',
			'add_new_item'        => 'Add New Menu',
			'add_new'             => 'Add New',
			'edit_item'           => 'Edit Menu',
			'update_item'         => 'Update Menu',
			'search_items'        => 'Search Menus',
			'not_found'           => 'Not Found',
			'not_found_in_trash'  => 'Not found in Trash'
	);
	
	$args = array(
			'labels'             => $labels,
			'menu_icon'          => 'dashicons-carrot',
			'description'        => 'Menus are created automatically! You enter what is on your menu and how much it costs, and we do the rest.',
			'public'             => true,
			'publicly_queryable' => false,
			'show_in_nav_menus'  => true,
			'capability_type'    => 'page',
			'map_meta_cap'       => true,
			'menu_position'      => 20,
			'hierarchical'       => false,
			'rewrite'            => array('slug' => 'menu', 'with_front' => false),
			'query_var'          => false,
			'delete_with_user'   => false,
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			/*'show_in_rest'       => false,
			'rest_base'          => 'pages',
			'rest_controller_class' => 'WP_REST_Posts_Controller'*/
	);
	
	register_post_type( 'menu_page_cpt', $args );
}
add_action( 'init', 'rfadm_setup_post_type' );


function rfadm_update_edit_form() {
	echo ' enctype="multipart/form-data"';
}
add_action('post_edit_form_tag', 'rfadm_update_edit_form');


// load script
function rfadm_admin_enqueue() {	
	if (get_post_type() == 'menu_page_cpt' || get_post_type() == 'pdf_menu_page_cpt') {
		wp_register_script('rfadm-script', plugins_url( '/js/rfadm_menu_pdf.js', __FILE__ ),  array('jquery') );
		wp_enqueue_script('rfadm-script');
		
		wp_enqueue_style( 'rfadm_admin_style', plugins_url( '/css/rfadm_admin_style.css', __FILE__ ));
	}
}
add_action('admin_enqueue_scripts', 'rfadm_admin_enqueue');


// load style
add_action('wp_enqueue_scripts', 'rfadm_enqueue_styles' );
function rfadm_enqueue_styles() {
	wp_enqueue_style( 'rfadm_style', plugins_url( '/css/rfadm_style.css', __FILE__ ));
	
	wp_register_script('layout2-pdf-script', plugins_url( 'js/pdf_menu_layout_2_height_control.js', __FILE__ ),  array('jquery') );
	wp_enqueue_script('layout2-pdf-script');
}


// Add the custom columns to the view all list on the admin side
add_filter( 'manage_menu_page_cpt_posts_columns', 'rfadm_set_custom_edit_menu_page_cpt_columns' );
function rfadm_set_custom_edit_menu_page_cpt_columns($columns) {
	$columns['layout_choice'] = 'Layout';
	//$columns['shortcode'] = 'Shortcode <br><i>(use this to display your menu anywhere on your site)</i>';
	
	return $columns;
}


// Add the data to the custom columns on the view all list on the admin side
add_action( 'manage_menu_page_cpt_posts_custom_column' , 'rfadm_custom_menu_page_cpt_column', 10, 2 );
function rfadm_custom_menu_page_cpt_column( $column, $post_id ) {
	switch ( $column ) {
		
		case 'layout_choice' :
			$layout = get_post_meta($post_id, 'default_menu_layout', true);
			if ($layout == '3') {
				echo 'Modern';
			} else if ($layout == '2') {
				echo 'Image';
			} else if ($layout == '1') {
				echo 'Classic';
			} 
			break;
			
		case 'shortcode' :
			echo '[display_menu p=' . $post_id . ']';
			break;			
	}
}

// add description for featured image metabox
add_filter( 'admin_post_thumbnail_html', 'rfadm_add_featured_image_desc');
function rfadm_add_featured_image_desc( $content ) {
	if (get_post_type() == 'menu_page_cpt') {
		return '<p>Note: The Featured Image will only display when using the "Image" layout</p>' . $content;
	} else {
		return $content;
	}
}

// add notice to display shortcode for pdf menus
function rfadm_pdf_menu_notice() {
	global $post;
	
	if (get_post_type($post) != 'pdf_menu_page_cpt') {
		return;
	}
	?> <div style="display: block;" class="update-nag notice">
        <p><strong>Remember</strong>: You can display these PDF menus anywhere on your site by placing this into any page, post, or widget: [display_pdf_menus]</p>
        <!--<p>If you want to use the other layouts avilable, simply add "layout=2" to your shortcode.</p>-->
        <p>To use the Grid layout with <strong>circular</strong> previews, use this instead: [display_pdf_menus layout=2]</p>
        <p>To use the Grid layout with <strong>squared</strong> previews, use this instead: [display_pdf_menus layout=3]</p>
    </div> <?php
}
add_action( 'admin_notices', 'rfadm_pdf_menu_notice' );

// add notice to display shortcode for regular text menus
function rfadm_menu_notice() {
    
    global $post, $pagenow;
	
	if ($post->post_type != 'menu_page_cpt') {
	    return;
	}
	
	if ($pagenow->base == 'post' && $pagenow->action == '') { ?> 
		<div style="display: block;" class="update-nag notice">
	        <p><strong>Remember</strong>: You can display this menu anywhere on your site by placing this into any page, post, or widget: [display_menu p=<?php echo get_the_ID(); ?>]</p>
	    </div> <?php
	}
}
add_action( 'admin_notices', 'rfadm_menu_notice' );



/*
 * settings/options
 */
add_action( 'admin_menu', 'rfadm__add_admin_menu' );
add_action( 'admin_init', 'rfadm__settings_init' );

function rfadm__add_admin_menu(  ) {
	add_options_page( 'Responsive Food and Drink Menu', 'RFADM Display Options', 'manage_options', 'responsive_food_and_drink_menu', 'rfadm__options_page' );	
}

function rfadm__settings_init(  ) {
	
	register_setting( 'rfadm_settings', 'rfadm__settings' );
	
	add_settings_section(
			'rfadm__rfadm_settings_section',
			__( '', 'rfadm' ),
			'rfadm__settings_section_callback',
			'rfadm_settings'
			);
	
	add_settings_field(
			'rfadm__select_field_0',
			__( 'Menu Title', 'rfadm' ),
			'rfadm__select_field_0_render',
			'rfadm_settings',
			'rfadm__rfadm_settings_section'
			);
	
	add_settings_field(
			'rfadm__select_field_1',
			__( 'Menu Header/Main Content', 'rfadm' ),
			'rfadm__select_field_1_render',
			'rfadm_settings',
			'rfadm__rfadm_settings_section'
			);
	
	add_settings_field(
			'rfadm__select_field_2',
			__( 'Section Titles', 'rfadm' ),
			'rfadm__select_field_2_render',
			'rfadm_settings',
			'rfadm__rfadm_settings_section'
			);
	
	add_settings_field(
			'rfadm__select_field_3',
			__( 'Item Titles', 'rfadm' ),
			'rfadm__select_field_3_render',
			'rfadm_settings',
			'rfadm__rfadm_settings_section'
			);
	
	add_settings_field(
			'rfadm__select_field_4',
			__( 'Item Prices', 'rfadm' ),
			'rfadm__select_field_4_render',
			'rfadm_settings',
			'rfadm__rfadm_settings_section'
			);
	
	add_settings_field(
			'rfadm__select_field_5',
			__( 'Item Descriptions', 'rfadm' ),
			'rfadm__select_field_5_render',
			'rfadm_settings',
			'rfadm__rfadm_settings_section'
			);
	
	add_settings_field(
			'rfadm__select_field_6',
			__( 'Menu Footer', 'rfadm' ),
			'rfadm__select_field_6_render',
			'rfadm_settings',
			'rfadm__rfadm_settings_section'
			);
	
	// set defaults
	$defaults = array(
			'rfadm__select_field_0' => 'h3',
			'rfadm__select_field_1' => 'p',
			'rfadm__select_field_2' => 'h4',
			'rfadm__select_field_3' => 'p',
			'rfadm__select_field_4' => 'h5',
			'rfadm__select_field_5' => 'h6',
			'rfadm__select_field_6' => 'p'
	);
	
	if(!get_option('rfadm__settings')) {
		//option not found, add new
		add_option('rfadm__settings', $defaults);
	} else {
		//option already in the database
		//so we get the stored value and merge it with default
		$old = get_option('rfadm__settings');
		$new = wp_parse_args($old, $defaults);
		
		update_option('rfadm__settings', $new);
	}
}


function rfadm__select_field_0_render(  ) {
	
	$options = get_option( 'rfadm__settings' );	
	?>
	<select name='rfadm__settings[rfadm__select_field_0]'>
		<option value='h1' <?php selected( $options['rfadm__select_field_0'], 'h1' ); ?>>Heading 1 (h1)</option>
		<option value='h2' <?php selected( $options['rfadm__select_field_0'], 'h2'); ?>>Heading 2 (h2)</option>
		<option value='h3' <?php selected( $options['rfadm__select_field_0'], 'h3'); ?>>Heading 3 (h3)</option>
		<option value='h4' <?php selected( $options['rfadm__select_field_0'], 'h4'); ?>>Heading 4 (h4)</option>
		<option value='h5' <?php selected( $options['rfadm__select_field_0'], 'h5'); ?>>Heading 5 (h5)</option>
		<option value='h6' <?php selected( $options['rfadm__select_field_0'], 'h6'); ?>>Heading 6 (h6)</option>
		<option value='p' <?php selected( $options['rfadm__select_field_0'], 'p'); ?>>Paragraph (p)</option>
	</select>

<?php
}


function rfadm__select_field_1_render(  ) { 

	$options = get_option( 'rfadm__settings' );
	?>
	<select name='rfadm__settings[rfadm__select_field_1]'>
		<option value='h1' <?php selected( $options['rfadm__select_field_1'], 'h1' ); ?>>Heading 1 (h1)</option>
		<option value='h2' <?php selected( $options['rfadm__select_field_1'], 'h2'); ?>>Heading 2 (h2)</option>
		<option value='h3' <?php selected( $options['rfadm__select_field_1'], 'h3'); ?>>Heading 3 (h3)</option>
		<option value='h4' <?php selected( $options['rfadm__select_field_1'], 'h4'); ?>>Heading 4 (h4)</option>
		<option value='h5' <?php selected( $options['rfadm__select_field_1'], 'h5'); ?>>Heading 5 (h5)</option>
		<option value='h6' <?php selected( $options['rfadm__select_field_1'], 'h6'); ?>>Heading 6 (h6)</option>
		<option value='p' <?php selected( $options['rfadm__select_field_1'], 'p'); ?>>Paragraph (p)</option>
	</select>

<br><br><br>
<?php

}


function rfadm__select_field_2_render(  ) { 

	$options = get_option( 'rfadm__settings' );
	?>
	<select name='rfadm__settings[rfadm__select_field_2]'>
		<option value='h1' <?php selected( $options['rfadm__select_field_2'], 'h1' ); ?>>Heading 1 (h1)</option>
		<option value='h2' <?php selected( $options['rfadm__select_field_2'], 'h2'); ?>>Heading 2 (h2)</option>
		<option value='h3' <?php selected( $options['rfadm__select_field_2'], 'h3'); ?>>Heading 3 (h3)</option>
		<option value='h4' <?php selected( $options['rfadm__select_field_2'], 'h4'); ?>>Heading 4 (h4)</option>
		<option value='h5' <?php selected( $options['rfadm__select_field_2'], 'h5'); ?>>Heading 5 (h5)</option>
		<option value='h6' <?php selected( $options['rfadm__select_field_2'], 'h6'); ?>>Heading 6 (h6)</option>
		<option value='p' <?php selected( $options['rfadm__select_field_2'], 'p'); ?>>Paragraph (p)</option>
	</select>

<?php

}


function rfadm__select_field_3_render(  ) { 

	$options = get_option( 'rfadm__settings' );
	?>
	<select name='rfadm__settings[rfadm__select_field_3]'>
		<option value='h1' <?php selected( $options['rfadm__select_field_3'], 'h1' ); ?>>Heading 1 (h1)</option>
		<option value='h2' <?php selected( $options['rfadm__select_field_3'], 'h2'); ?>>Heading 2 (h2)</option>
		<option value='h3' <?php selected( $options['rfadm__select_field_3'], 'h3'); ?>>Heading 3 (h3)</option>
		<option value='h4' <?php selected( $options['rfadm__select_field_3'], 'h4'); ?>>Heading 4 (h4)</option>
		<option value='h5' <?php selected( $options['rfadm__select_field_3'], 'h5'); ?>>Heading 5 (h5)</option>
		<option value='h6' <?php selected( $options['rfadm__select_field_3'], 'h6'); ?>>Heading 6 (h6)</option>
		<option value='p' <?php selected( $options['rfadm__select_field_3'], 'p'); ?>>Paragraph (p)</option>
	</select>

<?php

}


function rfadm__select_field_4_render(  ) { 

	$options = get_option( 'rfadm__settings' );
	?>
	<select name='rfadm__settings[rfadm__select_field_4]'>
		<option value='h1' <?php selected( $options['rfadm__select_field_4'], 'h1' ); ?>>Heading 1 (h1)</option>
		<option value='h2' <?php selected( $options['rfadm__select_field_4'], 'h2'); ?>>Heading 2 (h2)</option>
		<option value='h3' <?php selected( $options['rfadm__select_field_4'], 'h3'); ?>>Heading 3 (h3)</option>
		<option value='h4' <?php selected( $options['rfadm__select_field_4'], 'h4'); ?>>Heading 4 (h4)</option>
		<option value='h5' <?php selected( $options['rfadm__select_field_4'], 'h5'); ?>>Heading 5 (h5)</option>
		<option value='h6' <?php selected( $options['rfadm__select_field_4'], 'h6'); ?>>Heading 6 (h6)</option>
		<option value='p' <?php selected( $options['rfadm__select_field_4'], 'p'); ?>>Paragraph (p)</option>
	</select>

<?php

}


function rfadm__select_field_5_render(  ) { 

	$options = get_option( 'rfadm__settings' );
	?>
	<select name='rfadm__settings[rfadm__select_field_5]'>
		<option value='h1' <?php selected( $options['rfadm__select_field_5'], 'h1' ); ?>>Heading 1 (h1)</option>
		<option value='h2' <?php selected( $options['rfadm__select_field_5'], 'h2'); ?>>Heading 2 (h2)</option>
		<option value='h3' <?php selected( $options['rfadm__select_field_5'], 'h3'); ?>>Heading 3 (h3)</option>
		<option value='h4' <?php selected( $options['rfadm__select_field_5'], 'h4'); ?>>Heading 4 (h4)</option>
		<option value='h5' <?php selected( $options['rfadm__select_field_5'], 'h5'); ?>>Heading 5 (h5)</option>
		<option value='h6' <?php selected( $options['rfadm__select_field_5'], 'h6'); ?>>Heading 6 (h6)</option>
		<option value='p' <?php selected( $options['rfadm__select_field_5'], 'p'); ?>>Paragraph (p)</option>
	</select>

<br><br><br>
<?php

}


function rfadm__select_field_6_render(  ) {

	$options = get_option( 'rfadm__settings' );
	?>
	<select name='rfadm__settings[rfadm__select_field_6]'>
		<option value='h1' <?php selected( $options['rfadm__select_field_6'], 'h1' ); ?>>Heading 1 (h1)</option>
		<option value='h2' <?php selected( $options['rfadm__select_field_6'], 'h2'); ?>>Heading 2 (h2)</option>
		<option value='h3' <?php selected( $options['rfadm__select_field_6'], 'h3'); ?>>Heading 3 (h3)</option>
		<option value='h4' <?php selected( $options['rfadm__select_field_6'], 'h4'); ?>>Heading 4 (h4)</option>
		<option value='h5' <?php selected( $options['rfadm__select_field_6'], 'h5'); ?>>Heading 5 (h5)</option>
		<option value='h6' <?php selected( $options['rfadm__select_field_6'], 'h6'); ?>>Heading 6 (h6)</option>
		<option value='p' <?php selected( $options['rfadm__select_field_6'], 'p'); ?>>Paragraph (p)</option>
	</select>

<?php

}


function rfadm__settings_section_callback(  ) { 

	echo __( '<strong>Warning</strong>: It is not recommended to change these unless you are farmilar with HTML and your theme\'s CSS rules.', 'rfadm' );
	//echo '<br><br><input type="button" class="button" id="rfadm_restore_settings" value="Restore Defaults" />';
}


function rfadm__options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Responsive Food and Drink Menu - Display Settings</h2>

		<?php
		settings_fields( 'rfadm_settings' );
		do_settings_sections( 'rfadm_settings' );
		submit_button();
		?>

	</form>
	<?php

}

?>