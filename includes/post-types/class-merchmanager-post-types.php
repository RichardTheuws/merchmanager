<?php
/**
 * Register custom post types for the plugin
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/post-types
 */

/**
 * Register custom post types for the plugin.
 *
 * Define and register the custom post types required by the plugin.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/post-types
 * @author     Theuws Consulting
 */
class Merchmanager_Post_Types {

	/**
	 * Register custom post types.
	 *
	 * @since    1.0.0
	 */
	public function register_post_types() {
		
		// Register Band post type
		register_post_type( 'msp_band', array(
			'labels'              => array(
				'name'                  => _x( 'Bands', 'Post type general name', 'merchmanager' ),
				'singular_name'         => _x( 'Band', 'Post type singular name', 'merchmanager' ),
				'menu_name'             => _x( 'Bands', 'Admin Menu text', 'merchmanager' ),
				'name_admin_bar'        => _x( 'Band', 'Add New on Toolbar', 'merchmanager' ),
				'add_new'               => __( 'Add New', 'merchmanager' ),
				'add_new_item'          => __( 'Add New Band', 'merchmanager' ),
				'new_item'              => __( 'New Band', 'merchmanager' ),
				'edit_item'             => __( 'Edit Band', 'merchmanager' ),
				'view_item'             => __( 'View Band', 'merchmanager' ),
				'all_items'             => __( 'All Bands', 'merchmanager' ),
				'search_items'          => __( 'Search Bands', 'merchmanager' ),
				'parent_item_colon'     => __( 'Parent Bands:', 'merchmanager' ),
				'not_found'             => __( 'No bands found.', 'merchmanager' ),
				'not_found_in_trash'    => __( 'No bands found in Trash.', 'merchmanager' ),
				'featured_image'        => _x( 'Band Logo', 'Overrides the "Featured Image" phrase', 'merchmanager' ),
				'set_featured_image'    => _x( 'Set band logo', 'Overrides the "Set featured image" phrase', 'merchmanager' ),
				'remove_featured_image' => _x( 'Remove band logo', 'Overrides the "Remove featured image" phrase', 'merchmanager' ),
				'use_featured_image'    => _x( 'Use as band logo', 'Overrides the "Use as featured image" phrase', 'merchmanager' ),
				'archives'              => _x( 'Band archives', 'The post type archive label used in nav menus', 'merchmanager' ),
				'insert_into_item'      => _x( 'Insert into band', 'Overrides the "Insert into post" phrase', 'merchmanager' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this band', 'Overrides the "Uploaded to this post" phrase', 'merchmanager' ),
				'filter_items_list'     => _x( 'Filter bands list', 'Screen reader text for the filter links heading on the post type listing screen', 'merchmanager' ),
				'items_list_navigation' => _x( 'Bands list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'merchmanager' ),
				'items_list'            => _x( 'Bands list', 'Screen reader text for the items list heading on the post type listing screen', 'merchmanager' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'merchmanager',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-groups',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'bands' ),
			'query_var'           => true,
			'can_export'          => true,
		) );
		
		// Register Tour post type
		register_post_type( 'msp_tour', array(
			'labels'              => array(
				'name'                  => _x( 'Tours', 'Post type general name', 'merchmanager' ),
				'singular_name'         => _x( 'Tour', 'Post type singular name', 'merchmanager' ),
				'menu_name'             => _x( 'Tours', 'Admin Menu text', 'merchmanager' ),
				'name_admin_bar'        => _x( 'Tour', 'Add New on Toolbar', 'merchmanager' ),
				'add_new'               => __( 'Add New', 'merchmanager' ),
				'add_new_item'          => __( 'Add New Tour', 'merchmanager' ),
				'new_item'              => __( 'New Tour', 'merchmanager' ),
				'edit_item'             => __( 'Edit Tour', 'merchmanager' ),
				'view_item'             => __( 'View Tour', 'merchmanager' ),
				'all_items'             => __( 'All Tours', 'merchmanager' ),
				'search_items'          => __( 'Search Tours', 'merchmanager' ),
				'parent_item_colon'     => __( 'Parent Tours:', 'merchmanager' ),
				'not_found'             => __( 'No tours found.', 'merchmanager' ),
				'not_found_in_trash'    => __( 'No tours found in Trash.', 'merchmanager' ),
				'featured_image'        => _x( 'Tour Image', 'Overrides the "Featured Image" phrase', 'merchmanager' ),
				'set_featured_image'    => _x( 'Set tour image', 'Overrides the "Set featured image" phrase', 'merchmanager' ),
				'remove_featured_image' => _x( 'Remove tour image', 'Overrides the "Remove featured image" phrase', 'merchmanager' ),
				'use_featured_image'    => _x( 'Use as tour image', 'Overrides the "Use as featured image" phrase', 'merchmanager' ),
				'archives'              => _x( 'Tour archives', 'The post type archive label used in nav menus', 'merchmanager' ),
				'insert_into_item'      => _x( 'Insert into tour', 'Overrides the "Insert into post" phrase', 'merchmanager' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this tour', 'Overrides the "Uploaded to this post" phrase', 'merchmanager' ),
				'filter_items_list'     => _x( 'Filter tours list', 'Screen reader text for the filter links heading on the post type listing screen', 'merchmanager' ),
				'items_list_navigation' => _x( 'Tours list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'merchmanager' ),
				'items_list'            => _x( 'Tours list', 'Screen reader text for the items list heading on the post type listing screen', 'merchmanager' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'merchmanager',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar-alt',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'tours' ),
			'query_var'           => true,
			'can_export'          => true,
		) );
		
		// Register Show post type
		register_post_type( 'msp_show', array(
			'labels'              => array(
				'name'                  => _x( 'Shows', 'Post type general name', 'merchmanager' ),
				'singular_name'         => _x( 'Show', 'Post type singular name', 'merchmanager' ),
				'menu_name'             => _x( 'Shows', 'Admin Menu text', 'merchmanager' ),
				'name_admin_bar'        => _x( 'Show', 'Add New on Toolbar', 'merchmanager' ),
				'add_new'               => __( 'Add New', 'merchmanager' ),
				'add_new_item'          => __( 'Add New Show', 'merchmanager' ),
				'new_item'              => __( 'New Show', 'merchmanager' ),
				'edit_item'             => __( 'Edit Show', 'merchmanager' ),
				'view_item'             => __( 'View Show', 'merchmanager' ),
				'all_items'             => __( 'All Shows', 'merchmanager' ),
				'search_items'          => __( 'Search Shows', 'merchmanager' ),
				'parent_item_colon'     => __( 'Parent Shows:', 'merchmanager' ),
				'not_found'             => __( 'No shows found.', 'merchmanager' ),
				'not_found_in_trash'    => __( 'No shows found in Trash.', 'merchmanager' ),
				'featured_image'        => _x( 'Show Image', 'Overrides the "Featured Image" phrase', 'merchmanager' ),
				'set_featured_image'    => _x( 'Set show image', 'Overrides the "Set featured image" phrase', 'merchmanager' ),
				'remove_featured_image' => _x( 'Remove show image', 'Overrides the "Remove featured image" phrase', 'merchmanager' ),
				'use_featured_image'    => _x( 'Use as show image', 'Overrides the "Use as featured image" phrase', 'merchmanager' ),
				'archives'              => _x( 'Show archives', 'The post type archive label used in nav menus', 'merchmanager' ),
				'insert_into_item'      => _x( 'Insert into show', 'Overrides the "Insert into post" phrase', 'merchmanager' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this show', 'Overrides the "Uploaded to this post" phrase', 'merchmanager' ),
				'filter_items_list'     => _x( 'Filter shows list', 'Screen reader text for the filter links heading on the post type listing screen', 'merchmanager' ),
				'items_list_navigation' => _x( 'Shows list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'merchmanager' ),
				'items_list'            => _x( 'Shows list', 'Screen reader text for the items list heading on the post type listing screen', 'merchmanager' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'merchmanager',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-tickets-alt',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'shows' ),
			'query_var'           => true,
			'can_export'          => true,
		) );
		
		// Register Merchandise post type
		register_post_type( 'msp_merchandise', array(
			'labels'              => array(
				'name'                  => _x( 'Merchandise', 'Post type general name', 'merchmanager' ),
				'singular_name'         => _x( 'Merchandise Item', 'Post type singular name', 'merchmanager' ),
				'menu_name'             => _x( 'Merchandise', 'Admin Menu text', 'merchmanager' ),
				'name_admin_bar'        => _x( 'Merchandise Item', 'Add New on Toolbar', 'merchmanager' ),
				'add_new'               => __( 'Add New', 'merchmanager' ),
				'add_new_item'          => __( 'Add New Merchandise Item', 'merchmanager' ),
				'new_item'              => __( 'New Merchandise Item', 'merchmanager' ),
				'edit_item'             => __( 'Edit Merchandise Item', 'merchmanager' ),
				'view_item'             => __( 'View Merchandise Item', 'merchmanager' ),
				'all_items'             => __( 'All Merchandise', 'merchmanager' ),
				'search_items'          => __( 'Search Merchandise', 'merchmanager' ),
				'parent_item_colon'     => __( 'Parent Merchandise:', 'merchmanager' ),
				'not_found'             => __( 'No merchandise found.', 'merchmanager' ),
				'not_found_in_trash'    => __( 'No merchandise found in Trash.', 'merchmanager' ),
				'featured_image'        => _x( 'Merchandise Image', 'Overrides the "Featured Image" phrase', 'merchmanager' ),
				'set_featured_image'    => _x( 'Set merchandise image', 'Overrides the "Set featured image" phrase', 'merchmanager' ),
				'remove_featured_image' => _x( 'Remove merchandise image', 'Overrides the "Remove featured image" phrase', 'merchmanager' ),
				'use_featured_image'    => _x( 'Use as merchandise image', 'Overrides the "Use as featured image" phrase', 'merchmanager' ),
				'archives'              => _x( 'Merchandise archives', 'The post type archive label used in nav menus', 'merchmanager' ),
				'insert_into_item'      => _x( 'Insert into merchandise', 'Overrides the "Insert into post" phrase', 'merchmanager' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this merchandise', 'Overrides the "Uploaded to this post" phrase', 'merchmanager' ),
				'filter_items_list'     => _x( 'Filter merchandise list', 'Screen reader text for the filter links heading on the post type listing screen', 'merchmanager' ),
				'items_list_navigation' => _x( 'Merchandise list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'merchmanager' ),
				'items_list'            => _x( 'Merchandise list', 'Screen reader text for the items list heading on the post type listing screen', 'merchmanager' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'merchmanager',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-cart',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'merchandise' ),
			'query_var'           => true,
			'can_export'          => true,
		) );
		
		// Register Sales Page post type
		register_post_type( 'msp_sales_page', array(
			'labels'              => array(
				'name'                  => _x( 'Sales Pages', 'Post type general name', 'merchmanager' ),
				'singular_name'         => _x( 'Sales Page', 'Post type singular name', 'merchmanager' ),
				'menu_name'             => _x( 'Sales Pages', 'Admin Menu text', 'merchmanager' ),
				'name_admin_bar'        => _x( 'Sales Page', 'Add New on Toolbar', 'merchmanager' ),
				'add_new'               => __( 'Add New', 'merchmanager' ),
				'add_new_item'          => __( 'Add New Sales Page', 'merchmanager' ),
				'new_item'              => __( 'New Sales Page', 'merchmanager' ),
				'edit_item'             => __( 'Edit Sales Page', 'merchmanager' ),
				'view_item'             => __( 'View Sales Page', 'merchmanager' ),
				'all_items'             => __( 'All Sales Pages', 'merchmanager' ),
				'search_items'          => __( 'Search Sales Pages', 'merchmanager' ),
				'parent_item_colon'     => __( 'Parent Sales Pages:', 'merchmanager' ),
				'not_found'             => __( 'No sales pages found.', 'merchmanager' ),
				'not_found_in_trash'    => __( 'No sales pages found in Trash.', 'merchmanager' ),
				'archives'              => _x( 'Sales Page archives', 'The post type archive label used in nav menus', 'merchmanager' ),
				'insert_into_item'      => _x( 'Insert into sales page', 'Overrides the "Insert into post" phrase', 'merchmanager' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this sales page', 'Overrides the "Uploaded to this post" phrase', 'merchmanager' ),
				'filter_items_list'     => _x( 'Filter sales pages list', 'Screen reader text for the filter links heading on the post type listing screen', 'merchmanager' ),
				'items_list_navigation' => _x( 'Sales pages list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'merchmanager' ),
				'items_list'            => _x( 'Sales pages list', 'Screen reader text for the items list heading on the post type listing screen', 'merchmanager' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'merchmanager',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-store',
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => array( 'slug' => 'sales-pages' ),
			'query_var'           => true,
			'can_export'          => true,
		) );
	}
}