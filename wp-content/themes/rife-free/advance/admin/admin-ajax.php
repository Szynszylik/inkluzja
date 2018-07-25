<?php
add_action( 'wp_ajax_apollo13framework_check_license_code', 'apollo13framework_check_license_code' );
function apollo13framework_check_license_code() {
	global $apollo13framework_a13;
	$out = $apollo13framework_a13->register_new_license_code( isset( $_POST['code'] )? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '' );
	echo wp_json_encode( $out, JSON_FORCE_OBJECT );
	exit;
}


add_action( 'wp_ajax_apollo13framework_import_demo_data', 'apollo13framework_import_demo_data' );


/**
 * Function leading demo data import process
 */
function apollo13framework_import_demo_data() {
	global $apollo13framework_a13;
	//check if we got license key
	if ( !$apollo13framework_a13->check_is_import_allowed() ) {
		$msg    = esc_html__( 'Import stopped - there is no valid Purchase/License Code', 'rife-free' );
		$result = array(
			'log'           => $msg,
			'sublevel_name' => '',
			'level_name'    => $msg,
			'is_it_end'     => true
		);

		//send AJAX response
		echo json_encode( sizeof( $result ) ? $result : false );
		die(); //this is required to return a proper result
	}
	/** @noinspection PhpIncludeInspection */
	$file_system_check = require_once( get_theme_file_path( 'advance/admin/demo-data.php' ) );

	//error on file system access
	if(!$file_system_check){
		$result = array(
			'level'         => '',
			'level_name'    => esc_html__( 'Import Failed', 'rife-free' ),
			'sublevel'      => '',
			'sublevel_name' => '',
			'log'           => esc_html__( 'Can not access File system!', 'rife-free' ),
			'is_it_end'     => true,
			'alert'         => true
		);
	}
	else{
		$level         = isset( $_POST['level'] )? sanitize_text_field( wp_unslash( $_POST['level'] ) ) : '';
		$sublevel      = isset( $_POST['sublevel'] )? sanitize_text_field( wp_unslash( $_POST['sublevel'] ) ) : '';
		$sublevel_name = '';
		$log           = '';
		$array_index   = 0;
		$alert         = false;

		$chosen_options = isset($_POST['import_options'])? array_map( 'sanitize_text_field', wp_unslash( $_POST['import_options'] ) ): array();

		$levels = array(
			'_'                     => '', //empty to avoid bonus logic
			'start'                 => esc_html__( 'Starting import', 'rife-free' ),
			'download_files'        => esc_html__( 'Downloading files', 'rife-free' ),
			'clear_content'         => esc_html__( 'Removing content', 'rife-free' ),
			'install_plugins'       => esc_html__( 'Installing plugins', 'rife-free' ),
			'install_content'       => esc_html__( 'Importing content', 'rife-free' ),
			'install_revo_sliders'  => esc_html__( 'Importing Revolution Slider', 'rife-free' ),
			'setup_plugins_configs' => esc_html__( 'Setting up various plugins settings', 'rife-free' ),
			'setup_wc'              => esc_html__( 'Setting up Woocommerce settings', 'rife-free' ),
			'setup_fp'              => esc_html__( 'Setting up Front Page', 'rife-free' ),
			'setup_menus'           => esc_html__( 'Setting menus to proper locations', 'rife-free' ),
			'setup_widgets'         => esc_html__( 'Setting up widgets', 'rife-free' ),
			'setup_permalinks'      => esc_html__( 'Setting up permalinks', 'rife-free' ),
			'import_predefined_set' => esc_html__( 'Importing settings', 'rife-free' ),
			'generate_custom_style' => esc_html__( 'Generate styles', 'rife-free' ),
			'install_plugins_2'     => esc_html__( 'Installing plugins', 'rife-free' ),
			'clean'                 => esc_html__( 'cleaning...', 'rife-free' ),
			'end'                   => esc_html__( 'Everything done!', 'rife-free' ),
		);

		//check what options are selected
		if(!isset($chosen_options['download_files'])){
			unset( $levels['download_files'] );
		}
		if(!isset($chosen_options['clear_content'])){
			unset( $levels['clear_content'] );
		}
		if(!isset($chosen_options['install_plugins'])){
			unset( $levels['install_plugins'] );
			unset( $levels['setup_plugins_configs'] );
			unset( $levels['setup_wc'] );
			unset( $levels['install_plugins_2'] );
		}
		if(!isset($chosen_options['import_shop'])){
			unset( $levels['setup_wc'] );
			unset( $levels['install_plugins_2'] );
		}
		if(!isset($chosen_options['install_content'])){
			unset( $levels['install_content'] );
		}
		if(!isset($chosen_options['install_revo_sliders'])){
			unset( $levels['install_revo_sliders'] );
		}
		if(!isset($chosen_options['install_site_settings'])){
			unset( $levels['setup_fp'] );
			unset( $levels['setup_menus'] );
			unset( $levels['setup_widgets'] );
			unset( $levels['setup_permalinks'] );
		}
		if(!isset($chosen_options['install_theme_settings'])){
			unset( $levels['import_predefined_set'] );
			unset( $levels['generate_custom_style'] );
		}
		if(!isset($chosen_options['clean'])){
			unset( $levels['clean'] );
		}

		//get current level key
		if ( strlen( $level ) === 0 ) {
			//get first level to process
			$level = key( $levels );
		}
		else {
			//move array pointer to current importing level
			while ( key( $levels ) !== $level ) {
				//and ask for next one
				next( $levels );
				$array_index++;
			}
			//save new current level
			$level = key( $levels );
		}

		//Execute current level function
		$function = 'apollo13framework_demo_data_' . $level;
		if ( function_exists( $function ) ) {
			//no notices or other "echos", we put it in $log
			ob_start();

			$functions_with_1_param = array(
				'apollo13framework_demo_data_import_predefined_set',
				'apollo13framework_demo_data_start',
				'apollo13framework_demo_data_clean',
				'apollo13framework_demo_data_install_revo_sliders'
			);

			$demo_id = isset( $_POST['demo_id'] )? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
			//how many params should function receive
			if ( in_array($function, $functions_with_1_param ) ) {
				$sublevel = $function( $demo_id );
			}
			else {
				$sublevel = $function( $sublevel, $sublevel_name, $demo_id, $chosen_options );
			}

			//collect all produced output to log
			$log = ob_get_contents();
			ob_end_clean();

			//should we move to next level
			if ( $sublevel === true ) {
				$sublevel = ''; //reset
				next( $levels );
				$level = key( $levels );
			}
		}
		//no function - move to next level. Some steps are just information without action
		else {
			next( $levels );
			$array_index ++;
			$level = key( $levels );
		}

		//check if this is last element
		$is_it_end = false;
		end( $levels );
		if ( key( $levels ) === $level ) {
			$is_it_end = true;
		}

		//prepare progress info
		$progress = round( 100 * ( 1 + $array_index ) / count( $levels ) );

		//special case - demo import files download failure
		$failure_codes = array(
			620,    // invalid purchase code
			621,    // trying to get paid demo
	//		1012,   // no available servers
			1013    // server directory no writable
		);
		if ( is_array( $sublevel ) && $sublevel['sublevel'] === false && in_array($sublevel['response']['code'], $failure_codes) ) {
			$log       = $sublevel['response']['message'];
			$sublevel  = false;
			$is_it_end = true;
			$alert     = true;
		}

		$result = array(
			'level'         => $level,
			'level_name'    => $levels[ $level ],
			'sublevel'      => $sublevel,
			'sublevel_name' => $sublevel_name,
			'log'           => $log,
			'progress'      => $progress,
			'is_it_end'     => $is_it_end,
			'alert'         => $alert
		);
	}

	//send AJAX response
	echo json_encode( sizeof( $result ) ? $result : false );

	die(); //this is required to return a proper result
}



add_action( 'wp_ajax_apollo13framework_import_theme_settings', 'apollo13framework_import_theme_settings' );
/**
 * Imports theme settings from "Export" screen
 *
 */
function apollo13framework_import_theme_settings(){
	$out['response'] = 'success';
	$out['message']  =  '';

	$settings =  isset( $_POST['settings'] )? sanitize_text_field( wp_unslash( $_POST['settings'] ) ) : '';
	if( strlen( $settings ) ){
		//make sure we will have UTF8 JSON
		if( function_exists('utf8_encode') ){
			$settings = utf8_encode( $settings );
		}

		//decode
		$settings = json_decode( $settings, true );

		if( !is_null($settings) ){
			global $apollo13framework_a13;

			//do the import
			$apollo13framework_a13->set_options( $settings );
			//generate user.css file
			$apollo13framework_a13->generate_user_css(true);

			$out['message'] = esc_html__( 'Import successful!', 'rife-free' );
		}
		else{
			$out['response'] = 'error';
			$out['message']  = esc_html__( 'Looks like malformatted JSON string, cannot proceed with the import.', 'rife-free' );
		}
	}
	else{
		$out['response'] = 'error';
		$out['message']  = esc_html__( 'Nothing to import.', 'rife-free' );
	}

	echo wp_json_encode( $out, JSON_FORCE_OBJECT );
	exit;
}

add_action( 'wp_ajax_apollo13framework_prepare_gallery_items_html', 'apollo13framework_prepare_gallery_items_html' );

/**
 * Prints HTML for new items selected from WordPress media uploader
 */
function apollo13framework_prepare_gallery_items_html() {
	//returned value is array from attachment upload, so array_map( 'sanitize_text_field', wp_unslash( $_POST['items'] ) ) would break array
	$items = isset( $_POST['items'] )? wp_unslash( $_POST['items'] ) : array();
	apollo13framework_prepare_admin_gallery_html( $items );

	die(); // this is required to return a proper result
}


add_action( 'wp_ajax_apollo13framework_rating_notice_action', 'apollo13framework_rating_notice_action' );

/**
 * Mark rating notice to be displayed later or disabled
 */
function apollo13framework_rating_notice_action() {
	$what_to_do = isset( $_POST['what'] )? sanitize_text_field( wp_unslash( $_POST['what'] ) ) : '';
	$new_value = '';

	if($what_to_do === 'remind-later'){
		$new_value = time();
	}
	elseif($what_to_do === 'disable-rating'){
		$new_value = 'disabled';
	}

	update_option('a13_'.A13FRAMEWORK_TPL_SLUG.'_rating', $new_value);

	echo esc_html( $what_to_do );

	die(); // this is required to return a proper result
}



add_action( 'wp_ajax_apollo13framework_disable_ajax_notice', 'apollo13framework_disable_ajax_notice' );

/**
 * Mark notice to be displayed later or disabled
 */
function apollo13framework_disable_ajax_notice() {
	$id = isset( $_POST['notice_id'] )? sanitize_text_field( wp_unslash( $_POST['notice_id'] ) ) : '';
	$option_name = 'a13_'.A13FRAMEWORK_TPL_SLUG.'_ajax_notices';

	//get notices
	$current_notices = get_option($option_name);
	//update mentioned notice
	$current_notices[$id] = 0;

	//save
	update_option($option_name, $current_notices);

	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_apollo13framework_prepare_gallery_single_item_html', 'apollo13framework_prepare_gallery_single_item_html' );

/**
 * Just a helper to create item for gallery in album/work
 */
function apollo13framework_prepare_gallery_single_item_html() {
	$array[] = isset( $_POST['item'] )? array_map( 'sanitize_text_field', wp_unslash( $_POST['item'] ) ) : array();
	apollo13framework_prepare_external_media( $array );
	apollo13framework_prepare_admin_gallery_html( $array );

	die(); // this is required to return a proper result
}



/**
 * custom function to add/remove NAVA CPT post
 */
add_action( 'wp_ajax_apollo13framework_delete_post', 'apollo13framework_delete_post' );
function apollo13framework_delete_post() {
	if ( ! current_user_can( 'delete_posts' ) ) {
		exit;
	}

	if( isset( $_POST['id'] )){
		$post_id = intval( wp_unslash( $_POST['id'] ) );
		wp_delete_post( $post_id );
	}
	echo 'success';

	die();

}

add_action( 'wp_ajax_apollo13framework_add_post', 'apollo13framework_add_post' );
function apollo13framework_add_post() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		exit;
	}
	$title = isset( $_REQUEST['title'] )? sanitize_title( wp_unslash( $_REQUEST['title'] ) ) : '';

	$new_nava    = array(
		'post_title'   => $title,
		'post_status'  => 'publish',
		'post_content' => '',
		'post_type'    => 'nava'
	);
	$new_post_ID = wp_insert_post( $new_nava );


	$response = array(
		'status'         => '200',
		'message'        => 'OK',
		'new_post_ID'    => $new_post_ID,
		'new_post_title' => $title
	);

	// normally, the script expects a json response
	header( 'Content-Type: application/json; charset=utf-8' );
	echo json_encode( $response );

	exit; // important

}

//nava - add page slug to nava post
add_action( 'save_post', 'apollo13_after_page_save' );
function apollo13_after_page_save( $post_id ) {

	// If this is just a revision - exit
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	// avoid generating nava in case of page import
	if ( isset( $_SESSION['import_is_runnig'] ) && $_SESSION['import_is_runnig'] == 1 ) {
		return;
	}
	$page = get_post( $post_id );

	$a13_nava_page_slug = $page->post_name;
	//prepare array of params with a13_one_page_mode = 1
	//search for vc_row shortcodes inside page
	preg_match_all( '/\[(\[?)(vc_row)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s', $page->post_content, $matches );
	// array of shortcode's params
	$param_sets = $matches[3];

	if ( empty( $param_sets ) ) {

		//return;
	}

	foreach ( $param_sets as $param_set ) {
		if ( stripos( $param_set, 'a13_one_page_mode="1"' ) === false ) {
			continue;
		}
		$found       = false;
		$a13_nava_id = '';
		//get shortcode's params
		$params = explode( '" ', $param_set );
		foreach ( $params as $param ) {
			$parts = explode( '=', $param );
			if ( $parts[0] == 'a13_nava_id' ) {
				$a13_nava_id = str_replace( '"', '', $parts[1] );
				$found       = true;
			}
		}

		if ( $found ) {
			update_post_meta( $a13_nava_id, 'a13_nava_page_slug', $a13_nava_page_slug );
		}
	}

	//search for navas with this page slug - means that those navas were assigned to this page
	//and remove orphans


}