<?php
if(!function_exists('apollo13framework_make_scroller')){
	/**
	 * Make theme scroller for photos
	 *
	 * @param array    $args
	 * @param null|int $id post ID
	 */
	function apollo13framework_make_scroller($args, $id = null ){
		//translate some options from scroller to slider
		$args['main_slider'] = $args['a13MainSlider'] === true ? 'on' : 'off';
		$args['window_high'] = $args['a13WindowHigh'] === true ? 'on' : 'off';
		$args['texts']       = $args['a13ShowDesc'] === true ? 'on' : 'off';
		$args['autoplay']    = $args['autoPlay'] > 0 ? 'on' : 'off';

		//use slider
		apollo13framework_make_slider($args, $id);
	}
}

/* protects from undefined functions used in framework */
if(!function_exists('apollo13framework_horizontal_header_color_variant')){
	function apollo13framework_horizontal_header_color_variant() {
		return 'normal';
	}
}


/* protects from undefined functions used in framework */
if(!function_exists('apollo13framework_cookie_message')){
	function apollo13framework_cookie_message() {
		return '';
	}
}

if(!function_exists('apollo13framework_cookie_message_css')){
	function apollo13framework_cookie_message_css() {
		return '';
	}
}

if ( ! function_exists( 'apollo13framework_header_top_bar' ) ) {
	function apollo13framework_header_top_bar() {
		return '';
	}
}

if(!function_exists('apollo13framework_menu_overlay')) {
	function apollo13framework_menu_overlay(){
		return '';
	}
}