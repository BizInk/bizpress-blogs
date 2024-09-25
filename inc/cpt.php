<?php
// Register Custom Post Type
function bizpress_automations_cpt() {

	$labels = array(
		'name'                  => _x( 'Automations', 'Post Type General Name', 'bizink-content' ),
		'singular_name'         => _x( 'Automation', 'Post Type Singular Name', 'bizink-content' ),
		'menu_name'             => __( 'Automations', 'bizink-content' ),
		'name_admin_bar'        => __( 'Automation', 'bizink-content' ),
		'archives'              => __( 'Automation Archives', 'bizink-content' ),
		'attributes'            => __( 'Automation Attributes', 'bizink-content' ),
		'parent_item_colon'     => __( 'Parent Automation:', 'bizink-content' ),
		'all_items'             => __( 'All Automations', 'bizink-content' ),
		'add_new_item'          => __( 'Add New Automation', 'bizink-content' ),
		'add_new'               => __( 'Add New', 'bizink-content' ),
		'new_item'              => __( 'New Automation', 'bizink-content' ),
		'edit_item'             => __( 'Edit Automation', 'bizink-content' ),
		'update_item'           => __( 'Update Automation', 'bizink-content' ),
		'view_item'             => __( 'View Automation', 'bizink-content' ),
		'view_items'            => __( 'View Automations', 'bizink-content' ),
		'search_items'          => __( 'Search Automations', 'bizink-content' ),
		'not_found'             => __( 'Not found', 'bizink-content' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'bizink-content' ),
		'featured_image'        => __( 'Featured Image', 'bizink-content' ),
		'set_featured_image'    => __( 'Set featured image', 'bizink-content' ),
		'remove_featured_image' => __( 'Remove featured image', 'bizink-content' ),
		'use_featured_image'    => __( 'Use as featured image', 'bizink-content' ),
		'insert_into_item'      => __( 'Insert into Automation', 'bizink-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'bizink-content' ),
		'items_list'            => __( 'Automations list', 'bizink-content' ),
		'items_list_navigation' => __( 'Automations list navigation', 'bizink-content' ),
		'filter_items_list'     => __( 'Filter Automations list', 'bizink-content' ),
	);
	$capabilities = array(
		'edit_post'             => 'activate_plugins',
		'read_post'             => 'activate_plugins',
		'delete_post'           => 'activate_plugins',
		'edit_posts'            => 'activate_plugins',
		'edit_others_posts'     => 'activate_plugins',
		'publish_posts'         => 'activate_plugins',
		'read_private_posts'    => 'activate_plugins',
	);
	$args = array(
		'label'                 => __( 'Automation', 'bizink-content' ),
		'description'           => __( 'Used for the Automations tool with Bizpress', 'bizink-content' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'revisions', 'custom-fields' ),
		'taxonomies'            => array( 'automation-category' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => false,
		'show_in_menu'          => true,
		'menu_position'         => 65,
		'menu_icon'             => 'dashicons-plugins-checked',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => false,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'query_var'             => 'bizpress_automations',
		'rewrite'               => false,
		'capabilities'          => $capabilities,
		'show_in_rest'          => false,
	);
	register_post_type( 'bizpress_automations', $args );

}
add_action( 'init', 'bizpress_automations_cpt', 0 );