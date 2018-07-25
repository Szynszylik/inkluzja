<?php

//globals used by these functions
global $apollo13framework_customizer_dependencies;

//here we will collect all dependencies to know where to show hide each control
$apollo13framework_customizer_dependencies = array();


/**
 * Generates input, selects and other form controls
 * @param $option : currently processed option with all attributes
 * @param $args : pre-completed array of params for current control
 * @param $wp_customize : reference to $wp_customize
 * @return mixed : true if only array were completed, object for custom control
 */
function apollo13framework_customizer_controls($option, &$args, &$wp_customize){
	switch( $option['type'] ) {
		/** @noinspection PhpMissingBreakStatementInspection */
		case 'code_editor':
			$args['code_type'] = 'text/css';

			global $wp_version;
			if ( version_compare( $wp_version, '4.9', '>=' ) ) {
				// WordPress version is greater than 4.9
				//this is description from "Additional CSS" section
				$args['input_attrs'] = array(
					'aria-describedby' => 'editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4'
				);


				$return = new WP_Customize_Code_Editor_Control( $wp_customize, $args['setting'], $args );

				break;
			}

		case 'textarea':
			$args['type']       = 'textarea';

			$return = true;
			break;

		case 'button-set':
			$args['choices'] = $option['options'];
			$args['multi']   = $option['multi'];

			$return = new A13_Customize_Button_Set_Control( $wp_customize, $args['setting'], $args );

			break;

		case 'radio':
			$args['type']       = 'radio';
			$args['choices']    = $option['options'];

			$return = true;

			break;

		case 'select':
			$args['type']       = 'select';
			$args['choices']    = $option['options'];

			$return = true;
			break;

		case 'wp_dropdown_pages':
			$args['type']       = 'dropdown-pages';

			$return = true;
			break;

		case 'wp_dropdown_albums':
			$args['default']    = isset($option['default']) ? $option['default'] : '';
			$args['type']       = 'select';

			$wp_query_params = array(
				'posts_per_page' => -1,
				'no_found_rows' => true,
				'post_type' => A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM,
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
				'orderby' => 'date'
			);

			$r = new WP_Query($wp_query_params);

			if ($r->have_posts()) :
				while ($r->have_posts()) : $r->the_post();
					$args['choices'][get_the_ID()] = get_the_title();
				endwhile;

				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

			else:
				$args['choices'][0] = esc_html__( 'There are no albums yet!', 'rife-free' );

			endif;

			$return = true;

			break;

		case 'wp_dropdown_works':
			$args['default']    = isset($option['default']) ? $option['default'] : '';
			$args['type']       = 'select';

			$wp_query_params = array(
				'posts_per_page' => -1,
				'no_found_rows' => true,
				'post_type' => A13FRAMEWORK_CUSTOM_POST_TYPE_WORK,
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
				'orderby' => 'date'
			);

			$r = new WP_Query($wp_query_params);

			if ($r->have_posts()) :
				while ($r->have_posts()) : $r->the_post();
					$args['choices'][get_the_ID()] = get_the_title();
				endwhile;

				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

			else:
				$args['choices'][0] = esc_html__( 'There are no works yet!', 'rife-free' );

			endif;

			$return = true;

			break;

		case 'slider':
			$args['min']    = isset($option['min'])? $option['min'] : '';
			$args['max']    = isset($option['max'])? $option['max'] : '';
			$args['step']    = isset($option['step'])? $option['step'] : '';
			$args['unit']   = isset($option['unit'])? $option['unit'] : '';

			$return = new A13_Customize_Slider_Control( $wp_customize, $args['setting'], $args);
			break;

		case 'color':
			$args['default']    = isset($option['default']) ? $option['default'] : '';

			$return = new A13_Customize_Alpha_Color_Control( $wp_customize, $args['setting'], $args);
			break;

		case 'image':
			$args['default']    = isset($option['default']) ? $option['default'] : '';

			$return = new A13_Customize_Image_Control( $wp_customize, $args['setting'], $args);
			break;

		case 'font':
			$args['default']    = isset($option['default']) ? $option['default'] : '';

			$return = new A13_Customize_Font_Control( $wp_customize, $args['setting'], $args);
			break;

		case 'spacing':
			$args['default']    = isset($option['default']) ? $option['default'] : '';
			$args['mode']    = isset($option['mode']) ? $option['mode'] : '';
			$args['sides']    = isset($option['sides']) ? $option['sides'] : '';
			$args['units']    = isset($option['units']) ? $option['units'] : '';

			$return = new A13_Customize_Spacing_Control( $wp_customize, $args['setting'], $args);
			break;

		case 'socials':
			$args['default'] = isset($option['default']) ? $option['default'] : '';
			$args['choices'] = $option['options'];

			$return = new A13_Customize_Socials_Control( $wp_customize, $args['setting'], $args);
			break;

		case 'custom_sidebars':
			$args['default'] = isset($option['default']) ? $option['default'] : '';

			$return = new A13_Customize_Sidebars_Control( $wp_customize, $args['setting'], $args);
			break;

		default:
			$args['type'] = $option['type'];
			if(isset($option['input_attrs'])){
				$args['input_attrs'] = $option['input_attrs'];
			}

			$return = true;
			break;
	}

	return $return;
}


/**
 * Registers all settings for customizer
 *
 * @param WP_Customize_Manager $wp_customize customizer object
 */
function apollo13framework_customizer_settings( $wp_customize ) {
	global $apollo13framework_a13, $apollo13framework_customizer_dependencies;


	//include all custom controls
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-image-control.php' ) );
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-alpha-color-control.php' ) );
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-button-set-control.php' ) );
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-slider-control.php' ) );
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-font-control.php' ) );
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-spacing-control.php' ) );
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-socials-control.php' ) );
	require_once( get_theme_file_path( 'advance/inc/customizer/controls/class-a13-customize-sidebars-control.php' ) );
	// Register the class so that JS template of controls is available in the Customizer.
	$wp_customize->register_control_type( 'A13_Customize_Image_Control' );
	$wp_customize->register_control_type( 'A13_Customize_Button_Set_Control' );
	$wp_customize->register_control_type( 'A13_Customize_Slider_Control' );
	$wp_customize->register_control_type( 'A13_Customize_Font_Control' );
	$wp_customize->register_control_type( 'A13_Customize_Spacing_Control' );
	$wp_customize->register_control_type( 'A13_Customize_Socials_Control' );
	$wp_customize->register_control_type( 'A13_Customize_Sidebars_Control' );

	//sanitization functions
	require_once( get_theme_file_path( 'advance/inc/customizer/sanitization.php' ) );


	$panel_priority = 2;//below theme selector

	$customizer_structure = $apollo13framework_a13->get_sections();
	foreach($customizer_structure as $panel){
		$section_priority = 0;

		$without_panel = false;
		//if we want to have section on front of customizer
		if( isset($panel['without_panel']) && $panel['without_panel'] === true){
			$sections = array( $panel );
			$without_panel = true;
		}
		else{
			//we group sections in panels
			$wp_customize->add_panel(
				$panel['id'],
				array(
					'title'         => $panel['title'],
					'description'   => $panel['desc'],
					'priority'      => $panel_priority++,
				)
			);
			$sections = $panel['sections'];
		}


		foreach( $sections as $section) {
			$wp_customize->add_section(
				$section['id'],
				array(
					'panel'         => $without_panel ? null : $panel['id'],
					'title'         => $section['title'],
					'description'   => isset($section['desc']) ? $section['desc'] : '',
					'priority'      => $without_panel ? $panel_priority++ : $section_priority++,
				)
			);

			//reset counter
			$control_priority = 0;
			foreach( $section['fields'] as $field) {

				$post_message = ( isset( $field['partial'] ) && ( $field['partial'] === true || is_array( $field['partial'] ) ) ) ||
				                ( isset( $field['js'] ) && $field['js'] === true );

				//default sanitization
				if($field['type'] === 'select' || $field['type'] === 'radio'){
					$field['sanitize'] = 'options';
				}
				elseif($field['type'] === 'color'){
					$field['sanitize'] = 'color';
				}
				elseif($field['type'] === 'image'){
					$field['sanitize'] = 'image';
				}
				elseif($field['type'] === 'text'){
					$field['sanitize'] = 'esc_html';
				}
				elseif($field['type'] === 'textarea'){
					$field['sanitize'] = 'wp_kses_data';
				}

				//register setting
				$setting_name = A13FRAMEWORK_OPTIONS_NAME.'['.$field['id'].']';
				$wp_customize->add_setting( $setting_name, array(
					'default'           => isset($field['default']) ? $field['default'] : '',
					'type'              => 'option',
					'sanitize_callback' => isset($field['sanitize']) ? 'apollo13framework_sanitize_'.$field['sanitize'] : '',
					'transport'         => $post_message ? 'postMessage' : 'refresh'
				) );

				$control_args = array(
					'label'       => $field['title'],
					'description' => isset($field['description'])? $field['description'] : '',
					'section'     => $section['id'],
					'setting'     => $setting_name,
					'priority'    => $control_priority++,
					'active_callback' => isset( $field['active_callback'] ) ? $field['active_callback'] : null
				);

				//control needs other controls on particular values?
				if ( isset( $field['required'] ) ) {
					//it checks for dependency on other settings
					$control_args['active_callback'] = 'apollo13framework_customizer_activate_callback';
					$apollo13framework_customizer_dependencies[ $field['id'] ] = $field['required'];
				}

				$control = apollo13framework_customizer_controls( $field, $control_args, $wp_customize );

				if ( $control === true ) {
					$wp_customize->add_control( $setting_name, $control_args );
				}
				elseif ( is_object( $control ) ) {
					$wp_customize->add_control( $control );
				}

				if(isset($field['partial']) && is_array($field['partial'])){
					if(isset($field['partial']['settings'])){
						//make sure we have proper settings names
						foreach($field['partial']['settings'] as &$_setting){
							$_setting = A13FRAMEWORK_OPTIONS_NAME.'['.$_setting.']';
						}
						unset($_setting);
					}

					$wp_customize->selective_refresh->add_partial( $setting_name, $field['partial'] );
				}
			}
		}
	}
}
add_action( 'customize_register', 'apollo13framework_customizer_settings' );



/**
 *
 * Checks if single dependency is met
 * @param array $requirement dependency
 *
 * @return bool result
 */
function apollo13framework_customizer_compare_dependency($requirement){
	global $apollo13framework_a13;

	$id = $requirement[0];
    $operator = $requirement[1];
    $value    = $requirement[2];
	$field_value = $apollo13framework_a13->get_option($id,'',false);

    //check operators
    if($operator === '='){
        return $value === $field_value;
    }
    else if($operator === '!='){
        return $value !== $field_value;
    }

    //for all other operators
    return false;
}



/**
 * checks if control should be visible on page load
 * @param $control
 *
 * @return bool
 */
function apollo13framework_customizer_activate_callback($control) {
	global $apollo13framework_customizer_dependencies;

	//lets get field ID
	$matches = array();
	preg_match('/'.A13FRAMEWORK_OPTIONS_NAME.'\[([a-z0-9_]+)\]/', $control->id, $matches);

	if(strlen($matches[0])){
		//get requirements from global table
		$requirement = $apollo13framework_customizer_dependencies[ $matches[1] ];

		//control have many requirements
		if ( is_array( $requirement[0] ) ) {
			for ( $i = 0; $i < sizeof( $requirement ); $i ++ ) {
				if ( ! apollo13framework_customizer_compare_dependency( $requirement[ $i ] ) ) {
					return false; //some dependency were not met
				}
			}
		}
		//single requirement
		else {
			return apollo13framework_customizer_compare_dependency( $requirement );
		}
	}

	//let field be visible in any other case
	return true;
}



/**
 * adds JS file to run in customizer for controls
 */
add_action( 'customize_controls_enqueue_scripts', 'apollo13framework_customizer_controls_js');
function apollo13framework_customizer_controls_js(){
	global $apollo13framework_a13, $apollo13framework_customizer_dependencies;

	wp_enqueue_script('a13-customize-controls', get_theme_file_uri( 'js/customize-controls.js' ),
		array( 'jquery' ),
		A13FRAMEWORK_THEME_VER,
		true
	);

	//prepare JS dependencies
	$js_dependencies = array(
		'switches' => array(),
		'dependencies' => $apollo13framework_customizer_dependencies
	);
	foreach($apollo13framework_customizer_dependencies as $field => $dependency){
		//control have many requirements
		if ( is_array( $dependency[0] ) ) {
			for ( $i = 0; $i < sizeof( $dependency ); $i ++ ) {
				$js_dependencies['switches'][$dependency[$i][0]][] = $field;
			}
		}
		//single requirement
		else {
			$js_dependencies['switches'][$dependency[0]][] = $field;
		}
	}

//	var_dump( $js_dependencies );exit;

	wp_add_inline_script( 'a13-customize-controls', 'A13_CUSTOMIZER_DEPENDENCIES = '.wp_json_encode($js_dependencies));

	$notices = get_option('a13_'.A13FRAMEWORK_TPL_SLUG.'_ajax_notices');
	$google_fonts_file = get_theme_file_path( 'advance/inc/google-fonts-json.php' );
	$human_variants = array(
		'100' => esc_html__( 'thin', 'rife-free' ),
		'200' => esc_html__( 'extra-light', 'rife-free' ),
		'300' => esc_html__( 'light', 'rife-free' ),
		'400' => esc_html__( 'regular', 'rife-free' ),
		'500' => esc_html__( 'medium', 'rife-free' ),
		'600' => esc_html__( 'semi-bold', 'rife-free' ),
		'700' => esc_html__( 'bold', 'rife-free' ),
		'800' => esc_html__( 'extra-bold', 'rife-free' ),
		'900' => esc_html__( 'black', 'rife-free' ),
	);

	$apollo_params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'options_name' => A13FRAMEWORK_OPTIONS_NAME,
		'standard_fonts' => wp_json_encode($apollo13framework_a13->get_standard_fonts_list()),
		'google_fonts' => file_exists($google_fonts_file)? require($google_fonts_file) : '',
		'human_font_variants' => wp_json_encode($human_variants),
		'notices' => array(
			'header_color_variants' => array(
				'msg'     => __( '<p>Page that is visible in preview is using option "header color variant". Some changes made in header(like logo, social colors, etc.) might not be visible on this page cause of that.</p>', 'rife-free' ) .
				             /* translators: %s is link */
				             sprintf( __( '<p>For more information please check <a href="%s" target="_blank">this section in documentation</a> or below tutorial:</p>', 'rife-free' ), esc_url( $apollo13framework_a13->get_docs_link('header-color-variants') ) ) .
				             '<iframe width="290" height="163" src="https://www.youtube.com/embed/kLP2V8Q6uo4?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>',
				'enabled' => strlen($notices['header_color_variants']) ? $notices['header_color_variants'] : 1,
			),
		)
	);
	wp_localize_script( 'a13-customize-controls', 'A13FECustomizerControls', $apollo_params );
}



/**
 * adds JS file to run in customizer for preview
 */
add_action( 'customize_preview_init', 'apollo13framework_customizer_preview_js');
function apollo13framework_customizer_preview_js(){
	wp_enqueue_script('a13-customize-preview', get_theme_file_uri( 'js/customize-preview.js' ),
		array( 'customize-preview', 'jquery' ),
		A13FRAMEWORK_THEME_VER,
		true
	);

	$apollo_params = array(
		'options_name' => A13FRAMEWORK_OPTIONS_NAME,
		'cursors'    => get_theme_file_uri( 'images/cursors/')
	);
	wp_localize_script( 'a13-customize-preview', 'A13FECustomizerPreview', $apollo_params );

	wp_enqueue_style( 'a13-customize-preview', get_theme_file_uri( 'css/customize-preview.css'), false, A13FRAMEWORK_THEME_VER);
}



/**
 * prints icons selector
 */
add_action( 'customize_controls_print_footer_scripts', 'apollo13framework_customizer_footer' );
function apollo13framework_customizer_footer() {
	echo '<div id="a13-fa-icons">';
	/** @noinspection PhpIncludeInspection */
	$classes = require_once( get_theme_file_path( 'advance/inc/font-awesome-icons' ));
	foreach($classes as $name){
		$name = trim($name);
		echo '<span class="a13-font-icon fa fa-'.esc_attr( $name ).'" title="'.esc_attr( $name ).'"></span>'."\n";
	}
	echo '</div>';
}


function apollo13framework_prepare_partial_css($response, $option, $function) {
	$partial_name = A13FRAMEWORK_OPTIONS_NAME.'['.$option.']';
	if(isset($response['contents'][$partial_name])){
		$css_option_id = A13FRAMEWORK_OPTIONS_NAME.'-'.$option;
		$response['contents'][$partial_name][] = '<style id="'.$css_option_id.'" type="text/css">'.$function().'</style>';
	}

	return $response;
}