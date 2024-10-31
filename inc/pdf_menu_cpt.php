<?php

defined( 'ABSPATH' ) or die();

// Our custom post type function
function rfadm_setup_pdf_post_type() {
	
	$labels = array(
			'name'                => 'PDF Menu',
			'singular_name'       => 'PDF Menu',
			'menu_name'           => 'PDF Menus',
			'all_items'           => 'All PDF Menus',
			'view_item'           => 'View PDF Menu',
			'add_new_item'        => 'Add New PDF Menu',
			'add_new'             => 'Add New',
			'edit_item'           => 'Edit PDF Menu',
			'update_item'         => 'Update PDF Menu',
			'search_items'        => 'Search PDF Menus',
			'not_found'           => 'Not Found',
			'not_found_in_trash'  => 'Not found in Trash'
	);
	
	$args = array(
			'labels'             => $labels,
			'menu_icon'          => 'dashicons-carrot',
			'description'        => 'Give your menu a title and an optional image, upload your PDF, and you can display it - either by itself, or alongside your other menus - anywhere on your site using shortcodes.',
			'public'             => true,
			'publicly_queryable' => false,
			'show_in_nav_menus'  => true,
			'capability_type'    => 'page',
			'map_meta_cap'       => true,
			'menu_position'      => 20,
			'hierarchical'       => false,
			'rewrite'            => array('slug' => 'pdf_menu', 'with_front' => false),
			'query_var'          => false,
			'delete_with_user'   => false,
			'supports'           => array( 'title', 'thumbnail' ),
			/*'show_in_rest'       => false,
			'rest_base'          => 'pages',
			'rest_controller_class' => 'WP_REST_Posts_Controller'*/
	);
	
	register_post_type( 'pdf_menu_page_cpt', $args );
}
add_action( 'init', 'rfadm_setup_pdf_post_type' );

?>