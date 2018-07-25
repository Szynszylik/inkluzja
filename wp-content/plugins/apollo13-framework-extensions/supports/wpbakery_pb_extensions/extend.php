<?php
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/map_config.php';
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/actions.php';
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/filters.php';
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/shortcodes.php';



function a13fe_vc_custom_post_type(){
	//nava post type for anchor navigation
	$nava_type = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A : 'nava';

	$labels   = array(
		'name'               => __( 'One Page Navigation Pointer', 'a13_framework_cpt' ),
	);

	$args     = array(
		'labels'              => $labels,
		'exclude_from_search' => true,
		'public'              => true,
		'show_in_menu'        => false,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'query_var'           => true,
		'rewrite'             => false,
		'supports'            => array(),
	);

	register_post_type( $nava_type, $args );
}
add_action( 'init', 'a13fe_vc_custom_post_type' );