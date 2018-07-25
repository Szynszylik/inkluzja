<?php
if ( ! function_exists( 'a13fe_activation_flush' ) ) {
	function a13fe_activation_flush() {
		update_option( 'a13_force_to_flush', 'on' );
	}
}



if ( ! function_exists( 'a13fe_flush_rewrites' ) ) {
	function a13fe_flush_rewrites() {
		flush_rewrite_rules();
		update_option( 'a13_force_to_flush', 'off' );
	}
}



if ( ! function_exists( 'a13fe_get_extra_class' ) ) {
	function a13fe_get_extra_class( $el_class ) {
		$output = '';
		if ( $el_class != '' ) {
			$output = ' ' . str_replace( '.', '', $el_class );
		}

		return $output;
	}
}



if ( ! function_exists( 'a13fe_register_custom_post_types' ) ) {
	/**
	 * Register custom post types for special use
	 */
	function a13fe_register_custom_post_types() {
		//Album post type
		$album_type = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM : 'album';
		$album_slug = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG : 'album';
		$album_tax  = defined( 'A13FRAMEWORK_CPT_ALBUM_TAXONOMY' ) ? A13FRAMEWORK_CPT_ALBUM_TAXONOMY : 'genre';

		$labels = array(
			'name'               => __( 'Albums', 'a13_framework_cpt' ),
			'singular_name'      => __( 'Album', 'a13_framework_cpt' ),
			'add_new'            => __( 'Add New', 'a13_framework_cpt' ),
			'add_new_item'       => __( 'Add New Album', 'a13_framework_cpt' ),
			'edit_item'          => __( 'Edit Album', 'a13_framework_cpt' ),
			'new_item'           => __( 'New Album', 'a13_framework_cpt' ),
			'view_item'          => __( 'View Album', 'a13_framework_cpt' ),
			'search_items'       => __( 'Search Albums', 'a13_framework_cpt' ),
			'not_found'          => __( 'Nothing found', 'a13_framework_cpt' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'a13_framework_cpt' ),
			'parent_item_colon'  => ''
		);

		$supports = array( 'title', 'thumbnail', 'editor' );

		$args = array(
			'labels'        => $labels,
			'public'        => true,
			'query_var'     => true,
			'menu_icon'           => 'dashicons-images-alt2',
			//'has_archive' => true, //will make that yoursite.com/album/ will work as list of all albums with pagination
			'menu_position' => 7,
			'rewrite'       => array( 'slug' => $album_slug ),

		);

		//if you need to arrange albums in hierarchy, set this to true
		$is_hierarchical = false;

		if ( $is_hierarchical ) {
			$args['hierarchical'] = true;
			array_push( $supports, 'page-attributes' );
		}

		$args['supports'] = $supports;

		//register albums
		register_post_type( $album_type, $args );

		//prepare taxonomy for albums
		$genre_labels = array(
			'name'                       => __( 'Album Categories', 'a13_framework_cpt' ),
			'singular_name'              => __( 'Album Category', 'a13_framework_cpt' ),
			'search_items'               => __( 'Search Album Categories', 'a13_framework_cpt' ),
			'popular_items'              => __( 'Popular Album Categories', 'a13_framework_cpt' ),
			'all_items'                  => __( 'All Album Categories', 'a13_framework_cpt' ),
			'parent_item'                => __( 'Parent Album Category', 'a13_framework_cpt' ),
			'parent_item_colon'          => __( 'Parent Album Category:', 'a13_framework_cpt' ),
			'edit_item'                  => __( 'Edit Album Category', 'a13_framework_cpt' ),
			'update_item'                => __( 'Update Album Category', 'a13_framework_cpt' ),
			'add_new_item'               => __( 'Add New Album Category', 'a13_framework_cpt' ),
			'new_item_name'              => __( 'New Album Category Name', 'a13_framework_cpt' ),
			'menu_name'                  => __( 'Categories', 'a13_framework_cpt' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'a13_framework_cpt' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'a13_framework_cpt' ),
			'choose_from_most_used'      => __( 'Choose from the most used items', 'a13_framework_cpt' ),
			'not_found'                  => __( 'Not Found', 'a13_framework_cpt' ),
		);

		register_taxonomy( $album_tax, array( $album_type ),
			array(
				"hierarchical"      => true,
				"label"             => __( 'Albums Genres', 'a13_framework_cpt' ),
				"labels"            => $genre_labels,
				"rewrite"           => array(
					'hierarchical' => true
				),
				'show_admin_column' => true
			)
		);



		//Work post type
		$work_type = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_WORK' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_WORK : 'work';
		$work_slug = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG : 'work';
		$work_tax  = defined( 'A13FRAMEWORK_CPT_WORK_TAXONOMY' ) ? A13FRAMEWORK_CPT_WORK_TAXONOMY : 'work_genre';

		$labels = array(
			'name'               => __( 'Works', 'a13_framework_cpt' ),
			'singular_name'      => __( 'Work', 'a13_framework_cpt' ),
			'add_new'            => __( 'Add New', 'a13_framework_cpt' ),
			'add_new_item'       => __( 'Add New Work', 'a13_framework_cpt' ),
			'edit_item'          => __( 'Edit Work', 'a13_framework_cpt' ),
			'new_item'           => __( 'New Work', 'a13_framework_cpt' ),
			'view_item'          => __( 'View Work', 'a13_framework_cpt' ),
			'search_items'       => __( 'Search Works', 'a13_framework_cpt' ),
			'not_found'          => __( 'Nothing found', 'a13_framework_cpt' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'a13_framework_cpt' ),
			'parent_item_colon'  => ''
		);

		$supports = array( 'title', 'thumbnail', 'editor', 'excerpt' );

		$args = array(
			'labels'        => $labels,
			'public'        => true,
			'query_var'     => true,
			'menu_position' => 6,
			'menu_icon'           => 'dashicons-screenoptions',
			'rewrite'       => array( 'slug' => $work_slug ),
			'supports'      => $supports,
		);

		//register works
		register_post_type( $work_type, $args );

		//prepare taxonomy for works
		$genre_labels = array(
			'name'              => __( 'Works Categories', 'a13_framework_cpt' ),
			'singular_name'     => __( 'Category', 'a13_framework_cpt' ),
			'search_items'      => __( 'Search Categories', 'a13_framework_cpt' ),
			'popular_items'     => __( 'Popular Categories', 'a13_framework_cpt' ),
			'all_items'         => __( 'All Categories', 'a13_framework_cpt' ),
			'parent_item'       => __( 'Parent Category', 'a13_framework_cpt' ),
			'parent_item_colon' => __( 'Parent Category:', 'a13_framework_cpt' ),
			'edit_item'         => __( 'Edit Category', 'a13_framework_cpt' ),
			'update_item'       => __( 'Update Category', 'a13_framework_cpt' ),
			'add_new_item'      => __( 'Add New Category', 'a13_framework_cpt' ),
			'new_item_name'     => __( 'New Category Name', 'a13_framework_cpt' ),
			'menu_name'         => __( 'Categories', 'a13_framework_cpt' ),
		);

		register_taxonomy( $work_tax, array( $work_type ),
			array(
				"hierarchical"      => true,
				"label"             => __( 'Works Categories', 'a13_framework_cpt' ),
				"labels"            => $genre_labels,
				"rewrite"           => array(
					'hierarchical' => true
				),
				'show_admin_column' => true
			)
		);



		//People post type
		$people_type = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_PEOPLE' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_PEOPLE : 'people';
		$people_tax  = defined( 'A13FRAMEWORK_CPT_PEOPLE_TAXONOMY' ) ? A13FRAMEWORK_CPT_PEOPLE_TAXONOMY : 'group';

		$labels = array(
			'name'               => __( 'People', 'a13_framework_cpt' ),
			'singular_name'      => __( 'Man', 'a13_framework_cpt' ),
			'add_new'            => __( 'Add New', 'a13_framework_cpt' ),
			'add_new_item'       => __( 'Add New', 'a13_framework_cpt' ),
			'edit_item'          => __( 'Edit', 'a13_framework_cpt' ),
			'new_item'           => __( 'New', 'a13_framework_cpt' ),
			'view_item'          => __( 'View', 'a13_framework_cpt' ),
			'search_items'       => __( 'Search', 'a13_framework_cpt' ),
			'not_found'          => __( 'Nothing found', 'a13_framework_cpt' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'a13_framework_cpt' ),
			'parent_item_colon'  => ''
		);

		$supports = array( 'title', 'thumbnail', 'editor', 'page-attributes' );

		$args = array(
			'labels'              => $labels,
			'exclude_from_search' => true,
			'public'              => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-admin-users',
			'menu_position'       => 8,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => false,
			'supports'            => $supports,
		);

		//register people
		register_post_type( $people_type, $args );

		//prepare taxonomy for people
		$group_labels = array(
			'name'              => __( 'People groups', 'a13_framework_cpt' ),
			'singular_name'     => __( 'Group', 'a13_framework_cpt' ),
			'search_items'      => __( 'Search Groups', 'a13_framework_cpt' ),
			'popular_items'     => __( 'Popular Groups', 'a13_framework_cpt' ),
			'all_items'         => __( 'All Groups', 'a13_framework_cpt' ),
			'parent_item'       => __( 'Parent Group', 'a13_framework_cpt' ),
			'parent_item_colon' => __( 'Parent Group:', 'a13_framework_cpt' ),
			'edit_item'         => __( 'Edit Group', 'a13_framework_cpt' ),
			'update_item'       => __( 'Update Group', 'a13_framework_cpt' ),
			'add_new_item'      => __( 'Add New Group', 'a13_framework_cpt' ),
			'new_item_name'     => __( 'New Group Name', 'a13_framework_cpt' ),
			'menu_name'         => __( 'Group', 'a13_framework_cpt' ),
		);

		register_taxonomy( $people_tax, array( $people_type ),
			array(
				"hierarchical"      => false,
				"label"             => __( 'People groups', 'a13_framework_cpt' ),
				"labels"            => $group_labels,
				"rewrite"           => true,
				'show_admin_column' => true
			)
		);

		//if slug of CPT changed flush rules
		if( get_option('a13_force_to_flush') === 'on'){
			a13fe_flush_rewrites();
		}
	}
}