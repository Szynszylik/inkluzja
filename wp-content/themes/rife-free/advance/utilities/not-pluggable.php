<?php
/**
 * functions that should not be overwritten but it is good to keep them grouped here
 */


/**
 * Checking if we are on demo or dev server
 *
 * @return bool
 */
function apollo13framework_is_home_server(){
	return apply_filters('apollo13framework_is_home_server', false);
}


/**
 * Generates user css rule based on settings in admin panel
 *
 * @param                   $property
 * @param                   $value
 * @param string|bool|false $format is some special format used for value
 *
 * @return string CSS rule
 */
function apollo13framework_make_css_rule($property, $value, $format = false){
	if ( $value !== '' &&  $value !== 'default' ){
		//format for some properties
		if( $format !== false ){
			return $property . ': ' . sprintf($format, $value) . ';';
		}

		return $property . ': ' . $value . ";";
	}
	else{
		if( $value === '' && $property === 'background-image' ){
			return $property.': none;';
		}
		return '';
	}
}


/**
 * Removes special CSS added for admin bar by WP
 */
function apollo13framework_remove_admin_head_bump() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'apollo13framework_remove_admin_head_bump');


/**
 * Converts rgba color to hex
 *
 * @param string $r
 * @param string $g
 * @param string $b
 * @param string $a
 *
 * @return string
 */
function apollo13framework_rgba2hex( $r, $g, $b, $a ){
	return sprintf( '#%02s%02s%02s%02s', dechex( 255 * $a ), dechex( $r ), dechex( $g ), dechex( $b ) );
}

/**
 * Converts hex color to rgba
 *
 * @param string    $hex
 * @param int       $opacity
 *
 * @return string
 */
function apollo13framework_hex2rgba( $hex, $opacity = 1 ) {
	list( $r, $g, $b ) = sscanf( $hex, "#%02x%02x%02x" );

	return 'rgba('.$r.','.$g.','.$b.','.$opacity.')';
}


function apollo13framework_is_woocommerce_activated() {
    return class_exists( 'woocommerce' );
}



/**
 * Solves issue with badly named templates in previous theme versions.
 * It works while entering page on front-end
 */
function apollo13framework_check_for_renamed_templates(){
	//check what is current template name
	$current_name = get_post_meta( get_the_ID(), '_wp_page_template', true );

	//verify if it is up to date
	$checked_name = apollo13framework_proper_page_template_name($current_name);
	if( $checked_name !== $current_name  ){
		//update post with new template file name
		update_post_meta(get_the_ID(), '_wp_page_template', $checked_name);

		//only name without .php suffix
		$template_name = basename($checked_name, '.php');

		//run new template
		get_template_part( $template_name );

		//inform that there was redirect
		return false;
	}

	return true;
}


/**
 * Helper function for renaming templates
 *
 * @param $name string name of template to check
 *
 * @return string
 */
function apollo13framework_proper_page_template_name($name){
	$missing_templates = array(
		'archives_template.php',
		'albums_template.php',
		'works_template.php',
	);

	//rename old template file name to new if it is missing template
	return in_array( $name, $missing_templates ) ? str_replace('_', '-', $name) : $name;
}

/**
 * pre-connect to google fonts server - speeds up loading site that use Google fonts from theme
 *
 * @param array $urls
 * @param string $relation_type
 *
 * @return array
 *
 */
function apollo13framework_faster_google_fonts($urls, $relation_type){
	if('preconnect' === $relation_type){
		global $apollo13framework_a13;

		$standard_fonts = array_keys( $apollo13framework_a13->get_standard_fonts_list() );
		$options_fonts = array(
			$apollo13framework_a13->get_option( 'nav_menu_fonts' ),
			$apollo13framework_a13->get_option( 'titles_fonts' ),
			$apollo13framework_a13->get_option( 'normal_fonts' ),
			//default to titles fonts as it was in previous versions
			$apollo13framework_a13->get_option( 'logo_fonts', $apollo13framework_a13->get_option( 'titles_fonts' ) ),
		);

		foreach ( $options_fonts as $font ) {
			//if not standard font create then it is google font
			if ( ! in_array( $font['font-family'], $standard_fonts ) ) {
				$urls[] = array(
					'href' => 'https://fonts.gstatic.com',
					'crossorigin',
				);
				break;
			}
		}
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'apollo13framework_faster_google_fonts', 10, 2 );
