<?php


function a13fe_vc_config_map(){

	$nava_list = array();

	/* Nava extensions
	---------------------------------------------------------- */
	//try to find only unassigned nava post
	$args                               = array( 'numberposts' => - 1, "post_type" => defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A : 'nava' );
	$posts                              = get_posts( $args );
	$nava_list[ esc_html__( 'Pick one', 'a13_framework_cpt' ) ] = - 1;

	foreach ( $posts as $post ) {
		$nava_page_slug = get_post_meta( $post->ID, 'a13_nava_page_slug', true );
		$nava_title     = $post->post_title;
		if ( $nava_page_slug != '' ) {
			$nava_page_title = get_the_title( get_page_by_path( $nava_page_slug ) );
			$nava_title .= ' (' . esc_html__( 'already used in page', 'a13_framework_cpt' ) . ': ' . $nava_page_title . ')';
		}
		$nava_list[ $nava_title ] = $post->ID;
	}

	vc_add_param( "vc_row", array(
		'type'        => 'dropdown',
		'value'       => array(
			__( 'As it is on page', 'a13_framework_cpt' )     => 'none',
			__( 'Normal color variant', 'a13_framework_cpt' ) => 'normal',
			__( 'Light color variant', 'a13_framework_cpt' )  => 'light',
			__( 'Dark color variant', 'a13_framework_cpt' )   => 'dark',
		),
		'param_name'  => 'a13_header_color_variant',
		'heading'     => esc_html__( 'Header color variant', 'a13_framework_cpt' ),
		'description' => esc_html__( 'It works only for horizontal header. You can choose what color varaint header should be when row is displayed under it.', 'a13_framework_cpt' ),
	) );

	vc_add_param( "vc_row", array(
		"type"        => "checkbox",
		"weight"      => 0,
		"heading"     => esc_html__( 'Enable One Page Navigation?', 'a13_framework_cpt' ),
		"param_name"  => "a13_one_page_mode",
		"value"       => Array( esc_html__( 'Yes, please', 'a13_framework_cpt' ) => true ),
		"description" => '',
		"group"       => esc_html__( 'One Page Navigation', 'a13_framework_cpt' ),
	) );

	vc_add_param( "vc_row", array(
		"type"        => "dropdown",
		"weight"      => 0,
		"group"       => esc_html__( 'One Page Navigation', 'a13_framework_cpt' ),
		"heading"     => esc_html__( 'This row is pointed by:', 'a13_framework_cpt' ),
		"param_name"  => "a13_nava_id",
		"value"       => $nava_list,
		"description" => esc_html__( 'Pick one or', 'a13_framework_cpt' ) . ' <a href="#" class="a13_delete_selected_nava">' . esc_html__( 'DELETE SELECTED', 'a13_framework_cpt' ) . '</a> (' . esc_html__( 'Before deleting make sure it is not used anywhere else.', 'a13_framework_cpt' ) . ')',
		"dependency"  => array(
			"element"   => "a13_one_page_mode",
			"not_empty" => true
		)

	) );

	vc_add_param( "vc_row", array(
		"type"        => "textfield",
		"weight"      => 0,
		"heading"     => esc_html__( 'Here You can add another Pointer for One Page Navigation', 'a13_framework_cpt' ),
		"param_name"  => "a13_new_nava_id",
		"value"       => '',
		"description" => esc_html__( 'Enter name for it and hit Enter', 'a13_framework_cpt' ),
		"group"       => esc_html__( 'One Page Navigation', 'a13_framework_cpt' ),
		"dependency"  => array(
			"element"   => "a13_one_page_mode",
			"not_empty" => true
		)
	) );

	vc_add_params( "vc_row", array(
		array(
			"type"        => "textfield",
			"weight"      => 0,
			"heading"     => esc_html__( 'Navigation bullets title', 'a13_framework_cpt' ),
			"param_name"  => "a13_sticky_one_page_title",
			"value"       => '',
			"description" => '',
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
		),
		array(
			"type"        => "checkbox",
			"heading"     => esc_html__( 'Change the bullet in Sticky One Page Navigation?', 'a13_framework_cpt' ),
			"param_name"  => "a13_sticky_one_page_mode",
			"value"       => Array( esc_html__( 'Yes, please', 'a13_framework_cpt' ) => true ),
			"description" => '',
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Icon library', 'a13_framework_cpt' ),
			'value'       => array(
				__( 'Pick icons library', 'a13_framework_cpt' ) => '0',
				__( 'Font Awesome', 'a13_framework_cpt' )       => 'fontawesome',
				__( 'Open Iconic', 'a13_framework_cpt' )        => 'openiconic',
				__( 'Typicons', 'a13_framework_cpt' )           => 'typicons',
				__( 'Entypo', 'a13_framework_cpt' )             => 'entypo',
				__( 'Linecons', 'a13_framework_cpt' )           => 'linecons',
			),
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
			'param_name'  => 'type',
			'description' => esc_html__( 'Select icon library.', 'a13_framework_cpt' ),
			"dependency"  => array(
				"element"   => "a13_sticky_one_page_mode",
				"not_empty" => true
			)
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'a13_framework_cpt' ),
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
			'param_name'  => 'icon_fontawesome',
			'value'       => 'fa fa-adjust', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false,
				// default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'fontawesome',
			),
			'description' => esc_html__( 'Choose icon from library.', 'a13_framework_cpt' ),
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'a13_framework_cpt' ),
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
			'param_name'  => 'icon_openiconic',
			'value'       => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'openiconic',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'openiconic',
			),
			'description' => esc_html__( 'Choose icon from library.', 'a13_framework_cpt' ),
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'a13_framework_cpt' ),
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
			'param_name'  => 'icon_typicons',
			'value'       => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'typicons',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'typicons',
			),
			'description' => esc_html__( 'Choose icon from library.', 'a13_framework_cpt' ),
		),
		array(
			'type'       => 'iconpicker',
			'heading'    => esc_html__( 'Icon', 'a13_framework_cpt' ),
			"group"      => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
			'param_name' => 'icon_entypo',
			'value'      => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
			'settings'   => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'entypo',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'type',
				'value'   => 'entypo',
			),
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'a13_framework_cpt' ),
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
			'param_name'  => 'icon_linecons',
			'value'       => 'vc_li vc_li-heart', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'linecons',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'linecons',
			),
			'description' => esc_html__( 'Choose icon from library.', 'a13_framework_cpt' ),
		),
		array(
			'type'        => 'colorpicker',
			"group"       => esc_html__( 'Sticky One Page', 'a13_framework_cpt' ),
			'heading'     => esc_html__( 'Icon color', 'a13_framework_cpt' ),
			'param_name'  => 'color',
			'value'       => '#444444',
			'description' => esc_html__( 'Select icon color.', 'a13_framework_cpt' ),
			"dependency"  => array(
				"element"   => "a13_sticky_one_page_mode",
				"not_empty" => true
			)
		),
	) );




	/* CountDown shortcode
	---------------------------------------------------------- */

	vc_map( array(
		"name"     => esc_html__( 'Countdown', 'a13_framework_cpt' ),
		"base"     => "a13_countdown",
		'icon'     => 'icon-wpb-layer-shape-text',
		"category" => esc_html__( 'Content', 'a13_framework_cpt' ),
		"params"   => array(
			array(
				"type"        => "dropdown",
				"heading"     => esc_html__( 'Style', 'a13_framework_cpt' ),
				"param_name"  => "style",
				"value"       => array(
					__( 'Pick one', 'a13_framework_cpt' ) => '0',
					__( 'Simple', 'a13_framework_cpt' )   => 'simple',
					__( 'Flipping', 'a13_framework_cpt' ) => 'flipping',
				),
				"description" => ''
			),
			array(
				"type"       => "colorpicker",
				"heading"    => esc_html__( 'Font color', 'a13_framework_cpt' ),
				"param_name" => "fcolor",
				"value"      => '',
				"dependency" => Array( 'element' => 'style', 'value' => 'simple' )
			),
			array(
				"type"       => "colorpicker",
				"heading"    => esc_html__( 'Background color', 'a13_framework_cpt' ),
				"param_name" => "bcolor",
				"value"      => '',
				"dependency" => Array( 'element' => 'style', 'value' => 'simple' )
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Year', 'a13_framework_cpt' ),
				"param_name"  => "year",
				"value"       => '',
				"description" => esc_html__( 'Set a Year of the date to countdown to. Use four-digits format like 2016 or 2020', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Month', 'a13_framework_cpt' ),
				"param_name"  => "month",
				"value"       => '',
				"description" => esc_html__( 'Set a Month of the date to countdown to. Use two-digits format like 10 (for october) or 05 (for may)', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Day', 'a13_framework_cpt' ),
				"param_name"  => "day",
				"value"       => '',
				"description" => esc_html__( 'Set a Day of the date to countdown to. Use two-digits format like 21 or 09', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Hour', 'a13_framework_cpt' ),
				"param_name"  => "hour",
				"value"       => '',
				"description" => esc_html__( 'Set a Hour of the date to countdown to. Use 24-hour format like 15 or 03', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Minute', 'a13_framework_cpt' ),
				"param_name"  => "minute",
				"value"       => '',
				"description" => esc_html__( 'Set a Minute of the date to countdown to. Use two-digits format', 'a13_framework_cpt' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Extra class name', 'a13_framework_cpt' ),
				'param_name'  => 'el_class',
				'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'a13_framework_cpt' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Gap between counter elements', 'a13_framework_cpt' ),
				'param_name'  => 'gap',
				'description' => esc_html__( 'Set value in pixels.', 'a13_framework_cpt' ),
			)
		),
	) );




	/* Counter shortcode
	---------------------------------------------------------- */
	vc_map( array(
		"name"     => esc_html__( 'Counter', 'a13_framework_cpt' ),
		"base"     => "a13_counter",
		"icon"     => "icon-a13_counter",
		"category" => esc_html__( 'Content', 'a13_framework_cpt' ),
		"params"   => array(
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Count from', 'a13_framework_cpt' ),
				"param_name"  => "from",
				"value"       => '',
				"description" => esc_html__( 'Starting value of counter.', 'a13_framework_cpt' ),
				"admin_label" => true,
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Count to', 'a13_framework_cpt' ),
				"param_name"  => "to",
				"value"       => '',
				"description" => esc_html__( 'Finish value of counter.', 'a13_framework_cpt' ),
				"admin_label" => true,
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Digits font size', 'a13_framework_cpt' ),
				"param_name"  => "digits_font_size",
				"value"       => "",
				"description" => esc_html__( 'Type number. Value in pixels.', 'a13_framework_cpt' ),
			),
			array(
				"type"       => 'checkbox',
				"heading"    => esc_html__( 'Bold digits?', 'a13_framework_cpt' ),
				"param_name" => "digits_bold",
				"value"      => Array( esc_html__( "Yes, please", 'a13_framework_cpt' ) => true )
			),
			array(
				"type"        => "colorpicker",
				"heading"     => esc_html__( 'Digits color', 'a13_framework_cpt' ),
				"param_name"  => "digits_color",
				"description" => esc_html__( 'Select custom text color.', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Duration(ms)', 'a13_framework_cpt' ),
				"param_name"  => "speed",
				"value"       => '3000',
				"description" => esc_html__( 'How long it should take to count to end value. Eneter value in milliseconds.', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Refreshing time(ms)', 'a13_framework_cpt' ),
				"param_name"  => "refresh_interval",
				"value"       => '100',
				"description" => esc_html__( 'The number of milliseconds to wait between refreshing the counter.', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Finish text', 'a13_framework_cpt' ),
				"param_name"  => "finish_text",
				"value"       => '',
				"description" => esc_html__( 'This text will be displayed when counter will end counting. Optional.', 'a13_framework_cpt' ),
				"admin_label" => true,
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Text font size', 'a13_framework_cpt' ),
				"param_name"  => "text_font_size",
				"value"       => "",
				"description" => esc_html__( 'Type number. Value in pixels.', 'a13_framework_cpt' ),
			),
			array(
				"type"       => 'checkbox',
				"heading"    => esc_html__( 'Bold text?', 'a13_framework_cpt' ),
				"param_name" => "text_bold",
				"value"      => Array( esc_html__( "Yes, please", 'a13_framework_cpt' ) => true )
			),
			array(
				"type"        => "colorpicker",
				"heading"     => esc_html__( 'Text color', 'a13_framework_cpt' ),
				"param_name"  => "text_color",
				"description" => esc_html__( 'Select custom text color.', 'a13_framework_cpt' ),
			),
			array(
				"type"        => "dropdown",
				"heading"     => esc_html__( 'Align', 'a13_framework_cpt' ),
				"param_name"  => "align",
				"value"       => array(
						__( 'Pick one', 'a13_framework_cpt' ) => '0',
						__( 'Left', 'a13_framework_cpt' )     => 'left',
						__( 'Center', 'a13_framework_cpt' )   => 'center',
						__( 'Right', 'a13_framework_cpt' )    => 'right',
				),
				"description" => ''
			),
			array(
				"type"       => 'checkbox',
				"heading"    => esc_html__( 'Uppercase?', 'a13_framework_cpt' ),
				"param_name" => "uppercase",
				"value"      => Array( esc_html__( "Yes, please", 'a13_framework_cpt' ) => true )
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( "Extra class name", 'a13_framework_cpt' ),
				"param_name"  => "el_class",
				"description" => esc_html__( "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'a13_framework_cpt' )
			)
		),
	) );




	/* Image carousel
	---------------------------------------------------------- */
	vc_remove_param("vc_images_carousel", "mode");
	vc_remove_param("vc_images_carousel", "speed");
	vc_remove_param("vc_images_carousel", "slides_per_view");
	vc_remove_param("vc_images_carousel", "autoplay");
	vc_remove_param("vc_images_carousel", "hide_pagination_control");
	vc_remove_param("vc_images_carousel", "hide_prev_next_buttons");
	vc_remove_param("vc_images_carousel", "partial_view");
	vc_remove_param("vc_images_carousel", "wrap");
	vc_remove_param("vc_images_carousel", "el_class");


	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Interval', 'a13_framework_cpt' ),
			'param_name' => 'interval',
			'description' => esc_html__( 'in milliseconds', 'a13_framework_cpt' ),
			'value' => '2000'
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Slider speed', 'a13_framework_cpt' ),
			'param_name' => 'speed',
			'value' => '1000',
			'description' => esc_html__( 'Duration of animation between slides (in ms)', 'a13_framework_cpt' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Amount of sliders to scroll at once', 'a13_framework_cpt' ),
			'param_name' => 'scroll',
			'value' => '1',
			'description' => ''
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Slides per view', 'a13_framework_cpt' ),
			'param_name' => 'slides_per_view',
			'value' => '3',
			'description' => esc_html__( 'Set numbers of slides you want to display at the same time on slider container for carousel mode.', 'a13_framework_cpt' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Slider autoplay', 'a13_framework_cpt' ),
			'param_name' => 'autoplay',
			'description' => esc_html__( 'Enables autoplay mode.', 'a13_framework_cpt' ),
			'value' => array( esc_html__( 'Yes, please', 'a13_framework_cpt' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Hide pagination control', 'a13_framework_cpt' ),
			'param_name' => 'hide_pagination_control',
			'description' => esc_html__( 'If YES pagination control will be removed.', 'a13_framework_cpt' ),
			'value' => array( esc_html__( 'Yes, please', 'a13_framework_cpt' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Hide prev/next buttons', 'a13_framework_cpt' ),
			'param_name' => 'hide_prev_next_buttons',
			'description' => esc_html__( 'If "YES" prev/next control will be removed.', 'a13_framework_cpt' ),
			'value' => array( esc_html__( 'Yes, please', 'a13_framework_cpt' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Slider loop', 'a13_framework_cpt' ),
			'param_name' => 'wrap',
			'description' => esc_html__( 'Enables loop mode.', 'a13_framework_cpt' ),
			'value' => array( esc_html__( 'Yes, please', 'a13_framework_cpt' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'a13_framework_cpt' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'a13_framework_cpt' )
	));





	/* Custom heading
	---------------------------------------------------------- */
	vc_remove_param( 'vc_custom_heading', 'el_class' );

	vc_add_params( 'vc_custom_heading', array(
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Letter spacing', 'a13_framework_cpt' ),
			'param_name'  => 'letter_spacing',
			'description' => esc_html__( 'Enter value. Can be decimal and negative, for example 0.5 or -1.5', 'a13_framework_cpt' ),
		),
		array(
				'type'        => 'checkbox',
				'heading'     => esc_html__( 'Enable writing effect?', 'a13_framework_cpt' ),
				'param_name'  => 'enable_typed',
				'description' => esc_html__( 'When checked use this text format: {write}[font color]|[background color]|[first sentence]|[second sentence]|[third sentence]{/write}. Example: {write}#FFFFFF|#444444|This is custom heading element|And it is animated|And it will end up with this sentence{/write}.', 'a13_framework_cpt' ) . ' ' . esc_html__( 'You can use multiple {write}{/write} block, they will be animated in order.', 'a13_framework_cpt' ),
		),
		array(
				'type'        => 'checkbox',
				'heading'     => esc_html__( 'Loop writing effect?', 'a13_framework_cpt' ),
				'param_name'  => 'loop_typed',
				'description' => '',
				"dependency"  => array(
						"element"   => "enable_typed",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'checkbox',
				'heading'     => esc_html__( 'Enable responsive font size?', 'a13_framework_cpt' ),
				'param_name'  => 'enable_fit',
				'description' => esc_html__( 'Check this option to make heading responsive. It is useful to force heading to fit into single row no matter the screen width, just adjust the compression ratio parameter below.', 'a13_framework_cpt' ),
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Minimum font size', 'a13_framework_cpt' ),
				'param_name'  => 'fit_min_font_size',
				'description' => esc_html__( 'Set here lowest font-size you wish your heading to be. Use just number, value will be treated as pixels.', 'a13_framework_cpt' ),
				"dependency"  => array(
						"element"   => "enable_fit",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Maximum font size', 'a13_framework_cpt' ),
				'param_name'  => 'fit_max_font_size',
				'description' => esc_html__( 'Set here highest font-size you wish your heading to be. Use just number, value will be treated as pixels.', 'a13_framework_cpt' ),
				"dependency"  => array(
						"element"   => "enable_fit",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Compression ratio', 'a13_framework_cpt' ),
				'param_name'  => 'fit_compress',
				'description' => esc_html__( 'Enter value greater than 0, can be decimal. The greater compression is the longer heading will fit into single row.', 'a13_framework_cpt' ),
				"dependency"  => array(
						"element"   => "enable_fit",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Extra class name', 'a13_framework_cpt' ),
				'param_name'  => 'el_class',
				'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'a13_framework_cpt' ),
		)
	) );
}
add_action( 'vc_before_init', 'a13fe_vc_config_map' );




/* add a13 style to tabs shortcode
---------------------------------------------------------- */
function a13fe_vc_modify_shortcodes_params() {
	$vc_tta_tabs_style_param          = WPBMap::getParam( 'vc_tta_tabs', 'style' );
	$vc_tta_tabs_style_param['value'] = array(
		__( 'Classic', 'a13_framework_cpt' )       => 'classic',
		__( 'Modern', 'a13_framework_cpt' )        => 'modern',
		__( 'Flat', 'a13_framework_cpt' )          => 'flat',
		__( 'Outline', 'a13_framework_cpt' )       => 'outline',
		__( 'Theme style', 'a13_framework_cpt' ) => 'a13_framework_tabs',
	);
	vc_update_shortcode_param( 'vc_tta_tabs', $vc_tta_tabs_style_param );
}

add_action( 'vc_after_init', 'a13fe_vc_modify_shortcodes_params' );