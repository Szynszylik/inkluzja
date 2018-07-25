<?php

function apollo13framework_meta_boxes_post() {
	global $apollo13framework_a13;

	$header_sidebars = array(
		'G'   => __( 'Global settings', 'rife-free' ),
		'off' => __( 'Off', 'rife-free' ),
	);

	$custom_sidebars = $apollo13framework_a13->get_option( 'custom_sidebars' );
	$custom_sidebars = is_array($custom_sidebars)? $custom_sidebars : array($custom_sidebars);
	$sidebars_count  = count( $custom_sidebars );
	if ( is_array( $custom_sidebars ) && $sidebars_count > 0 ) {
		foreach ( $custom_sidebars as $sidebar ) {
			$header_sidebars[ $sidebar['id'] ] = $sidebar['name'];
		}
	}

	$meta = array(
		/*
		 *
		 * Tab: Posts list
		 *
		 */
		array(
			'name' => __('Posts list', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-list'
		),
		array(
			'name'        => __( 'Alternative Link', 'rife-free' ),
			'description' => __( 'If you fill this then when selecting post from Blog(post list), it will lead to this link instead of opening post.', 'rife-free' ),
			'id'          => 'alt_link',
			'default'     => '',
			'type'        => 'text',
		),
		array(
			'name'        => __( 'Size of brick', 'rife-free' ),
			'description' => __( 'How many bricks area should take this post in posts list.', 'rife-free' ),
			'id'          => 'brick_ratio_x',
			'default'     => 1,
			'unit'        => '',
			'min'         => 1,
			'max'         => 4,
			'type'        => 'slider'
		),


		/*
		 *
		 * Tab: Featured media
		 *
		 */
		array(
			'name' => __('Featured media', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-star'
		),
		array(
			'name'        => __( 'Post media', 'rife-free' ),
			'description' => __( 'Choose between Image, Video and Sliders. For image use Featured Image Option. For <strong>Images slider</strong> you need plugin <a href="https://wordpress.org/plugins/featured-galleries/" target="_blank">Featured galleries</a>.', 'rife-free' ),
			'id'          => 'image_or_video',
			'default'     => 'post_image',
			'options'     => array(
				'post_image'  => __( 'Image', 'rife-free' ),
				'post_slider' => __( 'Images slider', 'rife-free' ),
				'post_video'  => __( 'Video', 'rife-free' ),
			),
			'type'        => 'radio',
		),
		array(
			'name'        => __( 'Featured image parallax', 'rife-free' ),
			'description' => __( 'It will limit image height, so parallax could take effect.', 'rife-free' ),
			'id'          => 'image_parallax',
			'default'     => 'off',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'required'    => array( 'image_or_video', '=', 'post_image' ),
		),
		array(
			'name'     => __( 'Parallax area height', 'rife-free' ),
			'id'       => 'image_parallax_height',
			'default'  => '260',
			'unit'     => 'px',
			'min'      => 100,
			'max'      => 600,
			'type'     => 'slider',
			'required' => array(
				array( 'image_or_video', '=', 'post_image' ),
				array( 'image_parallax', '=', 'on' ),
			)
		),
		array(
			'name'              => __( 'Link to video', 'rife-free' ),
			'description'       => __( 'Insert here link to your video file or upload it. You can also add video from youtube or vimeo by pasting here link to movie.', 'rife-free' ),
			'id'                => 'post_video',
			'default'           => '',
			'type'              => 'upload',
			'button_text'       => __( 'Upload media file', 'rife-free' ),
			'media_button_text' => __( 'Insert media file', 'rife-free' ),
			'media_type'        => 'video', /* 'audio,video' */
			'required'          => array( 'image_or_video', '=', 'post_video' ),
		),

		/*
		 *
		 * Tab: Header
		 *
		 */
		array(
			'name' => __('Header', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-cogs'
		),
		array(
			'name'          => __( 'Hide content under header', 'rife-free' ),
			'description'   => __( 'Only valid when using horizontal header.', 'rife-free' ),
			'id'            => 'content_under_header',
			'global_value'  => 'G',
			'default'       => 'G',
			'parent_option' => 'post_content_under_header',
			'type'          => 'select',
			'options'       => array(
				'G'       => __( 'Global settings', 'rife-free' ),
				'content' => __( 'Yes hide content', 'rife-free' ),
				'title'   => __( 'Yes hide content and add top padding to outside title bar.', 'rife-free' ),
				'off'     => __( 'Turn it off', 'rife-free' ),
			),
		),

		/*
		 *
		 * Tab: Title bar
		 *
		 */
		array(
			'name' => __('Title bar', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-text-width'
		),
		array(
			'name'        => __( 'Title bar look', 'rife-free' ),
			'description' => __( 'You can use global settings or overwrite them here', 'rife-free' ),
			'id'          => 'title_bar_settings',
			'default'     => 'global',
			'type'        => 'radio',
			'options'     => array(
				'global' => __( 'Global settings', 'rife-free' ),
				'custom' => __( 'Use custom settings', 'rife-free' ),
				'off'    => __( 'Turn it off', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Position', 'rife-free' ),
			'id'          => 'title_bar_position',
			'default'     => 'outside',
			'type'        => 'radio',
			'options'     => array(
				'outside' => __( 'Before content', 'rife-free' ),
				'inside'  => __( 'Inside content', 'rife-free' ),
			),
			'description' => __( 'To set background image for "Before content" option, use <strong>featured image</strong>.', 'rife-free' ),
			'required'    => array( 'title_bar_settings', '=', 'custom' ),
		),
		array(
			'name'        => __( 'Variant', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_variant',
			'default'     => 'classic',
			'options'     => array(
				'classic'  => __( 'Classic(to side)', 'rife-free' ),
				'centered' => __( 'Centered', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Width', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_width',
			'default'     => 'full',
			'options'     => array(
				'full'  => __( 'Full', 'rife-free' ),
				'boxed' => __( 'Boxed', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'     => __( 'How to fit background image', 'rife-free' ),
			'id'       => 'title_bar_image_fit',
			'default'  => 'repeat',
			'options'  => array(
				'cover'    => __( 'Cover', 'rife-free' ),
				'contain'  => __( 'Contain', 'rife-free' ),
				'fitV'     => __( 'Fit Vertically', 'rife-free' ),
				'fitH'     => __( 'Fit Horizontally', 'rife-free' ),
				'center'   => __( 'Just center', 'rife-free' ),
				'repeat'   => __( 'Repeat', 'rife-free' ),
				'repeat-x' => __( 'Repeat X', 'rife-free' ),
				'repeat-y' => __( 'Repeat Y', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Enable parallax?', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_parallax',
			'default'     => 'off',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Parallax type', 'rife-free' ),
			'description' => __( 'It defines how image will scroll in background while page is scrolled down.', 'rife-free' ),
			'id'          => 'title_bar_parallax_type',
			'default'     => 'tb',
			'options'     => array(
				"tb"   => __( 'top to bottom', 'rife-free' ),
				"bt"   => __( 'bottom to top', 'rife-free' ),
				"lr"   => __( 'left to right', 'rife-free' ),
				"rl"   => __( 'right to left', 'rife-free' ),
				"tlbr" => __( 'top-left to bottom-right', 'rife-free' ),
				"trbl" => __( 'top-right to bottom-left', 'rife-free' ),
				"bltr" => __( 'bottom-left to top-right', 'rife-free' ),
				"brtl" => __( 'bottom-right to top-left', 'rife-free' ),
			),
			'type'        => 'select',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
				array( 'title_bar_parallax', '=', 'on' ),
			)
		),
		array(
			'name'        => __( 'Parallax speed', 'rife-free' ),
			'description' => __( 'It will be only used for background that are repeated. If background is set to not repeat this value will be ignored.', 'rife-free' ),
			'id'          => 'title_bar_parallax_speed',
			'default'     => '1.00',
			'type'        => 'text',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
				array( 'title_bar_parallax', '=', 'on' ),
			)
		),
		array(
			'name'        => __( 'Overlay color', 'rife-free' ),
			'description' => __( 'It will be put above image(if used)', 'rife-free' ),
			'id'          => 'title_bar_bg_color',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'     => __( 'Titles color', 'rife-free' ),
			'id'       => 'title_bar_title_color',
			'default'  => '',
			'type'     => 'color',
			'required' => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Second elements color', 'rife-free' ),
			'description' => __( 'Used in breadcrumbs.', 'rife-free' ),
			'id'          => 'title_bar_color_1',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Space in top and bottom', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_space_width',
			'default'     => '40px',
			'unit'        => 'px',
			'min'         => 0,
			'max'         => 600,
			'type'        => 'slider',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Breadcrumbs', 'rife-free' ),
			'description' => '',
			'id'          => 'breadcrumbs',
			'default'     => 'on',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'required'    => array( 'title_bar_settings', '=', 'custom' ),
		),

		/*
		 *
		 * Tab: Page background
		 *
		 */
		array(
			'name' => __('Page background', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-picture-o'
		),
		array(
			'name'        => __( 'Page background', 'rife-free' ),
			'description' => __( 'You can use global settings or overwrite them here', 'rife-free' ),
			'id'          => 'page_bg_settings',
			'default'     => 'global',
			'type'        => 'radio',
			'options'     => array(
				'global' => __( 'Global settings', 'rife-free' ),
				'custom' => __( 'Use custom settings', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Page Background image file', 'rife-free' ),
			'id'          => 'page_image',
			'default'     => '',
			'button_text' => __( 'Upload Image', 'rife-free' ),
			'type'        => 'upload',
			'required'    => array( 'page_bg_settings', '=', 'custom' ),
		),
		array(
			'name'     => __( 'How to fit background image', 'rife-free' ),
			'id'       => 'page_image_fit',
			'default'  => 'cover',
			'options'  => array(
				'cover'    => __( 'Cover', 'rife-free' ),
				'contain'  => __( 'Contain', 'rife-free' ),
				'fitV'     => __( 'Fit Vertically', 'rife-free' ),
				'fitH'     => __( 'Fit Horizontally', 'rife-free' ),
				'center'   => __( 'Just center', 'rife-free' ),
				'repeat'   => __( 'Repeat', 'rife-free' ),
				'repeat-x' => __( 'Repeat X', 'rife-free' ),
				'repeat-y' => __( 'Repeat Y', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'page_bg_settings', '=', 'custom' ),
		),
		array(
			'name'     => __( 'Page Background color', 'rife-free' ),
			'id'       => 'page_bg_color',
			'default'  => '',
			'type'     => 'color',
			'required' => array( 'page_bg_settings', '=', 'custom' ),
		),
	);

	return $meta;
}



function apollo13framework_meta_boxes_page() {
	global $apollo13framework_a13;
	$sidebars        = array(
		'default' => __( 'Default for pages', 'rife-free' ),
	);
	$header_sidebars = array(
		'G'   => __( 'Global settings', 'rife-free' ),
		'off' => __( 'Off', 'rife-free' ),
	);

	$custom_sidebars = $apollo13framework_a13->get_option( 'custom_sidebars' );
	$custom_sidebars = is_array($custom_sidebars)? $custom_sidebars : array($custom_sidebars);
	$sidebars_count  = count( $custom_sidebars );
	if ( is_array( $custom_sidebars ) && $sidebars_count > 0 ) {
		foreach ( $custom_sidebars as $sidebar ) {
			$sidebars[ $sidebar['id'] ]        = $sidebar['name'];
			$header_sidebars[ $sidebar['id'] ] = $sidebar['name'];
		}
	}

	$meta = array(
		/*
		 *
		 * Tab: Layout &amp; Sidebar
		 *
		 */
		array(
			'name' => __('Layout &amp; Sidebar', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-wrench'
		),
		array(
			'name'          => __( 'Content Layout', 'rife-free' ),
			'id'            => 'content_layout',
			'default'       => 'global',
			'global_value'  => 'global',
			'parent_option' => 'page_content_layout',
			'type'          => 'select',
			'options'       => array(
				'global'        => __( 'Global settings', 'rife-free' ),
				'center'        => __( 'Center fixed width', 'rife-free' ),
				'left'          => __( 'Left fixed width', 'rife-free' ),
				'left_padding'  => __( 'Left fixed width + padding', 'rife-free' ),
				'right'         => __( 'Right fixed width', 'rife-free' ),
				'right_padding' => __( 'Right fixed width + padding', 'rife-free' ),
				'full_fixed'    => __( 'Full width + fixed content', 'rife-free' ),
				'full_padding'  => __( 'Full width + padding', 'rife-free' ),
				'full'          => __( 'Full width', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Content top/bottom padding', 'rife-free' ),
			'description' => __( 'Enable or disable top and bottom padding of content. It is helpful in achieving some neat layout effects:-).', 'rife-free' ),
			'id'          => 'content_padding',
			'default'     => 'both',
			'type'        => 'select',
			'options'     => array(
				'both'   => __( 'Both on', 'rife-free' ),
				'top'    => __( 'Only top', 'rife-free' ),
				'bottom' => __( 'Only bottom', 'rife-free' ),
				'off'    => __( 'Both off', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Content side padding', 'rife-free' ),
			'description' => __( 'It is helpful in achieving some neat layout effects:-).', 'rife-free' ),
			'id'          => 'content_side_padding',
			'default'     => 'both',
			'type'        => 'radio',
			'options'     => array(
				'both'   => __( 'Both on', 'rife-free' ),
				'off'    => __( 'Both off', 'rife-free' ),
			),
		),
		array(
			'name'          => __( 'Sidebar', 'rife-free' ),
			'description'   => __( 'If turned off, content will take full width.', 'rife-free' ),
			'id'            => 'widget_area',
			'global_value'  => 'G',
			'default'       => 'G',
			'parent_option' => 'page_sidebar',
			'options'       => array(
				'G'                     => __( 'Global settings', 'rife-free' ),
				'left-sidebar'          => __( 'Sidebar on the left', 'rife-free' ),
				'left-sidebar_and_nav'  => __( 'Children Navigation + sidebar on the left', 'rife-free' ),
				'left-nav'              => __( 'Only children Navigation on the left', 'rife-free' ),
				'right-sidebar'         => __( 'Sidebar on the right', 'rife-free' ),
				'right-sidebar_and_nav' => __( 'Children Navigation + sidebar on the right', 'rife-free' ),
				'right-nav'             => __( 'Only children Navigation on the right', 'rife-free' ),
				'off'                   => __( 'Off', 'rife-free' ),
			),
			'type'          => 'select',
		),
		array(
			'name'     => __( 'Sidebar to show', 'rife-free' ),
			'id'       => 'sidebar_to_show',
			'default'  => 'default',
			'options'  => $sidebars,
			'type'     => 'select',
			'required' => array( 'widget_area', '!=', 'off' ),
		),

		/*
		 *
		 * Tab: Header
		 *
		 */
		array(
			'name' => __('Header', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-cogs'
		),
		array(
			'name'          => __( 'Hide content under header', 'rife-free' ),
			'description'   => __( 'Only valid when using horizontal header.', 'rife-free' ),
			'id'            => 'content_under_header',
			'global_value'  => 'G',
			'default'       => 'G',
			'parent_option' => 'page_content_under_header',
			'type'          => 'select',
			'options'       => array(
				'G'       => __( 'Global settings', 'rife-free' ),
				'content' => __( 'Yes hide content', 'rife-free' ),
				'title'   => __( 'Yes hide content and add top padding to outside title bar.', 'rife-free' ),
				'off'     => __( 'Turn it off', 'rife-free' ),
			),
		),
		array(
			'name'          => __( 'Show header from Nth row', 'rife-free' ),
			'description'   => __( 'Only valid when using horizontal header. <br />If you use Elementor or WPBakery Page Builder, then you can decide to show header after content is scrolled to Nth row. Thanks to that you can have clean welcome screen.', 'rife-free' ),
			'id'            => 'horizontal_header_show_header_at',
			'default'       => '0',
			'type'          => 'select',
			'options'       => array(
				'0' => __( 'Show always', 'rife-free' ),
				'1' => __( 'from 1st row', 'rife-free' ),
				'2' => __( 'from 2nd row', 'rife-free' ),
				'3' => __( 'from 3rd row', 'rife-free' ),
				'4' => __( 'from 4th row', 'rife-free' ),
				'5' => __( 'from 5th row', 'rife-free' ),
			),
		),

		/*
		 *
		 * Tab: Title bar
		 *
		 */
		array(
			'name' => __('Title bar', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-text-width'
		),
		array(
			'name'    => __( 'Subtitle', 'rife-free' ),
			'id'      => 'subtitle',
			'default' => '',
			'type'    => 'text'
		),
		array(
			'name'        => __( 'Title bar look', 'rife-free' ),
			'description' => __( 'You can use global settings or overwrite them here', 'rife-free' ),
			'id'          => 'title_bar_settings',
			'default'     => 'global',
			'type'        => 'radio',
			'options'     => array(
				'global' => __( 'Global settings', 'rife-free' ),
				'custom' => __( 'Use custom settings', 'rife-free' ),
				'off'    => __( 'Turn it off', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Position', 'rife-free' ),
			'id'          => 'title_bar_position',
			'default'     => 'outside',
			'type'        => 'radio',
			'options'     => array(
				'outside' => __( 'Before content', 'rife-free' ),
				'inside'  => __( 'Inside content', 'rife-free' ),
			),
			'description' => __( 'To set background image for "Before content" option, use <strong>featured image</strong>.', 'rife-free' ),
			'required'    => array( 'title_bar_settings', '=', 'custom' ),
		),
		array(
			'name'        => __( 'Variant', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_variant',
			'default'     => 'classic',
			'options'     => array(
				'classic'  => __( 'Classic(to side)', 'rife-free' ),
				'centered' => __( 'Centered', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Width', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_width',
			'default'     => 'full',
			'options'     => array(
				'full'  => __( 'Full', 'rife-free' ),
				'boxed' => __( 'Boxed', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'     => __( 'How to fit background image', 'rife-free' ),
			'id'       => 'title_bar_image_fit',
			'default'  => 'repeat',
			'options'  => array(
				'cover'    => __( 'Cover', 'rife-free' ),
				'contain'  => __( 'Contain', 'rife-free' ),
				'fitV'     => __( 'Fit Vertically', 'rife-free' ),
				'fitH'     => __( 'Fit Horizontally', 'rife-free' ),
				'center'   => __( 'Just center', 'rife-free' ),
				'repeat'   => __( 'Repeat', 'rife-free' ),
				'repeat-x' => __( 'Repeat X', 'rife-free' ),
				'repeat-y' => __( 'Repeat Y', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Enable parallax?', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_parallax',
			'default'     => 'off',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Parallax type', 'rife-free' ),
			'description' => __( 'It defines how image will scroll in background while page is scrolled down.', 'rife-free' ),
			'id'          => 'title_bar_parallax_type',
			'default'     => 'tb',
			'options'     => array(
				"tb"   => __( 'top to bottom', 'rife-free' ),
				"bt"   => __( 'bottom to top', 'rife-free' ),
				"lr"   => __( 'left to right', 'rife-free' ),
				"rl"   => __( 'right to left', 'rife-free' ),
				"tlbr" => __( 'top-left to bottom-right', 'rife-free' ),
				"trbl" => __( 'top-right to bottom-left', 'rife-free' ),
				"bltr" => __( 'bottom-left to top-right', 'rife-free' ),
				"brtl" => __( 'bottom-right to top-left', 'rife-free' ),
			),
			'type'        => 'select',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
				array( 'title_bar_parallax', '=', 'on' ),
			)
		),
		array(
			'name'        => __( 'Parallax speed', 'rife-free' ),
			'description' => __( 'It will be only used for background that are repeated. If background is set to not repeat this value will be ignored.', 'rife-free' ),
			'id'          => 'title_bar_parallax_speed',
			'default'     => '1.00',
			'type'        => 'text',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
				array( 'title_bar_parallax', '=', 'on' ),
			)
		),
		array(
			'name'        => __( 'Overlay color', 'rife-free' ),
			'description' => __( 'It will be put above image(if used)', 'rife-free' ),
			'id'          => 'title_bar_bg_color',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'     => __( 'Titles color', 'rife-free' ),
			'id'       => 'title_bar_title_color',
			'default'  => '',
			'type'     => 'color',
			'required' => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Second elements color', 'rife-free' ),
			'description' => __( 'Used in breadcrumbs.', 'rife-free' ),
			'id'          => 'title_bar_color_1',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Space in top and bottom', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_space_width',
			'default'     => '40px',
			'unit'        => 'px',
			'min'         => 0,
			'max'         => 600,
			'type'        => 'slider',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Breadcrumbs', 'rife-free' ),
			'description' => '',
			'id'          => 'breadcrumbs',
			'default'     => 'on',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'required'    => array( 'title_bar_settings', '=', 'custom' ),
		),

		/*
		 *
		 * Tab: Featured media
		 *
		 */
		array(
			'name' => __('Featured media', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-star'
		),
		array(
			'name'        => __( 'Post media', 'rife-free' ),
			'description' => __( 'Choose between Image, Video and Sliders. For image use Featured Image Option. For <strong>Images slider</strong> you need plugin <a href="https://wordpress.org/plugins/featured-galleries/" target="_blank">Featured galleries</a>.', 'rife-free' ),
			'id'          => 'image_or_video',
			'default'     => 'post_image',
			'options'     => array(
				'post_image'  => __( 'Image', 'rife-free' ),
				'post_slider' => __( 'Images slider', 'rife-free' ),
				'post_video'  => __( 'Video', 'rife-free' ),
			),
			'type'        => 'radio',
		),
		array(
			'name'        => __( 'Featured image parallax', 'rife-free' ),
			'description' => __( 'It will limit image height, so parallax could take effect.', 'rife-free' ),
			'id'          => 'image_parallax',
			'default'     => 'off',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'required'    => array( 'image_or_video', '=', 'post_image' ),
		),
		array(
			'name'     => __( 'Parallax area height', 'rife-free' ),
			'id'       => 'image_parallax_height',
			'default'  => '260',
			'unit'     => 'px',
			'min'      => 100,
			'max'      => 600,
			'type'     => 'slider',
			'required' => array(
				array( 'image_or_video', '=', 'post_image' ),
				array( 'image_parallax', '=', 'on' ),
			)
		),
		array(
			'name'              => __( 'Link to video', 'rife-free' ),
			'description'       => __( 'Insert here link to your video file or upload it. You can also add video from youtube or vimeo by pasting here link to movie.', 'rife-free' ),
			'id'                => 'post_video',
			'default'           => '',
			'type'              => 'upload',
			'button_text'       => __( 'Upload media file', 'rife-free' ),
			'media_button_text' => __( 'Insert media file', 'rife-free' ),
			'media_type'        => 'video', /* 'audio,video' */
			'required'          => array( 'image_or_video', '=', 'post_video' ),
		),

		/*
		 *
		 * Tab: Page background
		 *
		 */
		array(
			'name' => __('Page background', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-picture-o'
		),
		array(
			'name'        => __( 'Page background', 'rife-free' ),
			'description' => __( 'You can use global settings or overwrite them here', 'rife-free' ),
			'id'          => 'page_bg_settings',
			'default'     => 'global',
			'type'        => 'radio',
			'options'     => array(
				'global' => __( 'Global settings', 'rife-free' ),
				'custom' => __( 'Use custom settings', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Page Background image file', 'rife-free' ),
			'id'          => 'page_image',
			'default'     => '',
			'button_text' => __( 'Upload Image', 'rife-free' ),
			'type'        => 'upload',
			'required'    => array( 'page_bg_settings', '=', 'custom' ),
		),
		array(
			'name'     => __( 'How to fit background image', 'rife-free' ),
			'id'       => 'page_image_fit',
			'default'  => 'cover',
			'options'  => array(
				'cover'    => __( 'Cover', 'rife-free' ),
				'contain'  => __( 'Contain', 'rife-free' ),
				'fitV'     => __( 'Fit Vertically', 'rife-free' ),
				'fitH'     => __( 'Fit Horizontally', 'rife-free' ),
				'center'   => __( 'Just center', 'rife-free' ),
				'repeat'   => __( 'Repeat', 'rife-free' ),
				'repeat-x' => __( 'Repeat X', 'rife-free' ),
				'repeat-y' => __( 'Repeat Y', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'page_bg_settings', '=', 'custom' ),
		),
		array(
			'name'     => __( 'Page Background color', 'rife-free' ),
			'id'       => 'page_bg_color',
			'default'  => '',
			'type'     => 'color',
			'required' => array( 'page_bg_settings', '=', 'custom' ),
		),

		/*
		 *
		 * Tab: Sticky one page mode
		 *
		 */
		array(
			'name' => __('Sticky one page mode', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-anchor'
		),
		array(
			'name'        => __( 'Sticky One Page mode', 'rife-free' ),
			'description' => __( 'This works only when page is designed with WPBakery Page Builder. By enabling this the page will turn into vertical full-page slider and each row of content created by WPBakery Page Builder is a single slide.', 'rife-free' ),
			'id'          => 'content_sticky_one_page',
			'default'     => 'off',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
		),
		array(
			'name'     => __( 'Sticky One Page mode - color of navigation bullets', 'rife-free' ),
			'id'       => 'content_sticky_one_page_bullet_color',
			'default'  => 'rgba(0,0,0,1)',
			'type'     => 'color',
			'required' => array(
				array( 'content_sticky_one_page', '=', 'on' )
			)
		),
		array(
			'name'        => __( 'Sticky One Page mode - default bullets icon', 'rife-free' ),
			'description' => __( 'Select icon by clicking on input.', 'rife-free' ),
			'id'          => 'content_sticky_one_page_bullet_icon',
			'default'     => '',
			'type'        => 'text',
			'input_class' => 'a13-fa-icon a13-full-class',
			'required'    => array(
				array( 'content_sticky_one_page', '=', 'on' )
			)
		),
	);

	return $meta;
}



function apollo13framework_meta_boxes_album() {
	global $apollo13framework_a13;

	$header_sidebars = array(
		'G'   => __( 'Global settings', 'rife-free' ),
		'off' => __( 'Off', 'rife-free' ),
	);

	$custom_sidebars = $apollo13framework_a13->get_option( 'custom_sidebars' );
	$custom_sidebars = is_array($custom_sidebars)? $custom_sidebars : array($custom_sidebars);
	$sidebars_count  = count( $custom_sidebars );
	if ( is_array( $custom_sidebars ) && $sidebars_count > 0 ) {
		foreach ( $custom_sidebars as $sidebar ) {
			$header_sidebars[ $sidebar['id'] ] = $sidebar['name'];
		}
	}

	$meta = array(
		/*
		 *
		 * Tab: Albums list
		 *
		 */
		array(
			'name' => __('Albums list', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-list'
		),
		array(
			'name'        => __( 'Alternative Link', 'rife-free' ),
			'description' => __( 'If you fill this then clicking in your album on albums list will not lead to single album page but to link from this field.', 'rife-free' ),
			'id'          => 'alt_link',
			'default'     => '',
			'type'        => 'text',
		),
		array(
			'name'    => __( 'Subtitle', 'rife-free' ),
			'id'      => 'subtitle',
			'default' => '',
			'type'    => 'text'
		),
		array(
			'name'        => __( 'Size of brick', 'rife-free' ),
			'description' => __( 'How many bricks area should take this album in albums list.', 'rife-free' ),
			'id'          => 'brick_ratio_x',
			'default'     => 1,
			'unit'        => '',
			'min'         => 1,
			'max'         => 4,
			'type'        => 'slider'
		),
		array(
			'name'        => __( 'Cover color', 'rife-free' ),
			'id'          => 'cover_color',
			'description' => __( 'Works only when titles are displayed over images in Albums list.', 'rife-free' ),
			'default'     => 'rgba(0,0,0, 0.7)',
			'type'        => 'color'
		),
		array(
			'name'        => __( 'Exclude from Albums list page', 'rife-free' ),
			'description' => __( 'If enabled, then this album wont be listed on Albums list page, but you can still select it for front page or in other places.', 'rife-free' ),
			'id'          => 'exclude_in_albums_list',
			'default'     => 'off',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
		),

		/*
		 *
		 * Tab: Album media
		 *
		 */
		array(
			'name' => __('Album media', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-th',
		),
		array(
			'name'        => __( 'Items order', 'rife-free' ),
			'description' => __( 'It will display your images/videos from first to last, or another way.', 'rife-free' ),
			'id'          => 'order',
			'default'     => 'ASC',
			'options'     => array(
				'ASC'    => __( 'First on list, first displayed', 'rife-free' ),
				'DESC'   => __( 'First on list, last displayed', 'rife-free' ),
				'random' => __( 'Random', 'rife-free' ),
			),
			'type'        => 'select',
		),
		array(
			'name'        => __( 'Show title and description of album items', 'rife-free' ),
			'description' => __( 'If enabled, then it will affect displaying in bricks and slider option, and also in lightbox.', 'rife-free' ),
			'id'          => 'enable_desc',
			'default'     => 'on',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
		),
		array(
			'name'    => __( 'Present media in:', 'rife-free' ),
			'description'   => __( 'Slider and Bricks work with images and videos.', 'rife-free' ),
			'id'      => 'theme',
			'default' => 'bricks',
			'options' => array(
				'bricks' => __( 'Bricks', 'rife-free' ),
				'slider' => __( 'Slider', 'rife-free' ),
			),
			'type'    => 'radio',
		),
		array(
			'name'          => __( 'Content column', 'rife-free' ),
			'description'   => __( 'This will display separate block with title and text about album.', 'rife-free' ),
			'id'            => 'album_content',
			'default'       => 'G',
			'global_value'  => 'G',
			'parent_option' => 'album_content',
			'options'       => array(
				'G'     => __( 'Global settings', 'rife-free' ),
				'left'  => __( 'Show on left', 'rife-free' ),
				'right' => __( 'Show on right', 'rife-free' ),
				'off'   => __( 'Do not display it', 'rife-free' ),
			),
			'type'          => 'select',
			'required'      => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'        => __( 'Bricks columns', 'rife-free' ),
			'id'          => 'brick_columns',
			'default'     => '3',
			'unit'        => '',
			'min'         => 1,
			'max'         => 6,
			'type'        => 'slider',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'        => __( 'Max width of bricks content.', 'rife-free' ),
			'description' => __( 'Depending on actual screen width, available space for bricks might be smaller, but newer greater then this number.', 'rife-free' ),
			'id'          => 'bricks_max_width',
			'default'     => '1920px',
			'unit'        => 'px',
			'min'         => 200,
			'max'         => 2500,
			'type'        => 'slider',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'     => __( 'Brick margin', 'rife-free' ),
			'id'       => 'brick_margin',
			'default'  => '10px',
			'unit'     => 'px',
			'min'      => 0,
			'max'      => 100,
			'type'     => 'slider',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'     => __( 'Choose brick proportion', 'rife-free' ),
			'description' => __( 'Works only for images. If you switch theme option "Display thumbs instead of video", then for videos that you will upload image it will also work.', 'rife-free' ),
			'id'       => 'bricks_proportions_size',
			'default'  => '0',
			'options' => array(
				'0'    => __( 'Original size', 'rife-free' ),
				'1/1'  => __( '1:1', 'rife-free' ),
				'2/3'  => __( '2:3', 'rife-free' ),
				'3/2'  => __( '3:2', 'rife-free' ),
				'3/4'  => __( '3:4', 'rife-free' ),
				'4/3'  => __( '4:3', 'rife-free' ),
				'9/16' => __( '9:16', 'rife-free' ),
				'16/9' => __( '16:9', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_lightbox',
			'type'     => 'radio',
			'name'     => __( 'Open bricks to lightbox', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'on',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'        => __( 'Color of cover', 'rife-free' ),
			'description' => __( 'Leave empty to not set any background', 'rife-free' ),
			'id'          => 'slide_cover_color',
			'default'     => 'rgba(0,0,0, 0.7)',
			'type'        => 'color',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'     => __( 'Hover effect', 'rife-free' ),
			'id'       => 'bricks_hover',
			'default'  => 'cross',
			'options'  => array(
				'cross'  => __( 'Show cross', 'rife-free' ),
				'drop'   => __( 'Drop', 'rife-free' ),
				'shift'  => __( 'Shift', 'rife-free' ),
				'pop'    => __( 'Pop', 'rife-free' ),
				'border' => __( 'Border', 'rife-free' ),
				'none'   => __( 'None', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_title_position',
			'type'     => 'select',
			'name'     => __( 'Texts position', 'rife-free' ),
			'options'  => array(
				'top_left'      => __( 'Top left', 'rife-free' ),
				'top_center'    => __( 'Top center', 'rife-free' ),
				'top_right'     => __( 'Top right', 'rife-free' ),
				'mid_left'      => __( 'Middle left', 'rife-free' ),
				'mid_center'    => __( 'Middle center', 'rife-free' ),
				'mid_right'     => __( 'Middle right', 'rife-free' ),
				'bottom_left'   => __( 'Bottom left', 'rife-free' ),
				'bottom_center' => __( 'Bottom center', 'rife-free' ),
				'bottom_right'  => __( 'Bottom right', 'rife-free' ),
			),
			'default'  => 'top_left',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_overlay_cover',
			'type'     => 'radio',
			'name'     => __( 'Show cover when not hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'on',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_overlay_cover_hover',
			'type'     => 'radio',
			'name'     => __( 'Show cover when hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'off',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'          => 'bricks_overlay_gradient',
			'type'        => 'radio',
			'name'        => __( 'Show gradient when not hovering', 'rife-free' ),
			'description' => __( 'Its main function is to make texts more visible', 'rife-free' ),
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'     => 'on',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'          => 'bricks_overlay_gradient_hover',
			'type'        => 'radio',
			'name'        => __( 'Show gradient when hovering', 'rife-free' ),
			'description' => __( 'Its main function is to make texts more visible', 'rife-free' ),
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'     => 'off',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_overlay_texts',
			'type'     => 'radio',
			'name'     => __( 'Show texts when not hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'on',
			'required' => array(
				array( 'theme', '=', 'bricks' ),
				array( 'enable_desc', '=', 'on' )
			),
		),
		array(
			'id'       => 'bricks_overlay_texts_hover',
			'type'     => 'radio',
			'name'     => __( 'Show texts when hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'on',
			'required' => array(
				array( 'theme', '=', 'bricks' ),
				array( 'enable_desc', '=', 'on' )
			),
		),
		array(
			'name'        => __( 'Fit images', 'rife-free' ),
			'description' => __( 'How will images fit area. <strong>Fit when needed</strong> is best for small images, that should not be stretched to bigger sizes, only to smaller(to keep them visible).', 'rife-free' ),
			'id'          => 'fit_variant',
			'default'     => '0',
			'options'     => array(
				'0' => __( 'Fit always', 'rife-free' ),
				'1' => __( 'Fit landscape', 'rife-free' ),
				'2' => __( 'Fit portrait', 'rife-free' ),
				'3' => __( 'Fit when needed', 'rife-free' ),
				'4' => __( 'Cover whole screen', 'rife-free' ),
			),
			'type'        => 'select',
			'required'    => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'          => __( 'Autoplay', 'rife-free' ),
			'description'   => __( 'If autoplay is on, slider items will start sliding on page load', 'rife-free' ),
			'id'            => 'autoplay',
			'default'       => 'G',
			'global_value'  => 'G',
			'parent_option' => 'album_slider_autoplay',
			'options'       => array(
				'G'   => __( 'Global settings', 'rife-free' ),
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'          => 'select',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'          => __( 'Transition type', 'rife-free' ),
			'description'   => __( 'Animation between slides.', 'rife-free' ),
			'id'            => 'transition',
			'default'       => '-1',
			'global_value'  => '-1',
			'parent_option' => 'album_slider_transition_type',
			'options'       => array(
				'-1' => __( 'Global settings', 'rife-free' ),
				'0'  => __( 'None', 'rife-free' ),
				'1'  => __( 'Fade', 'rife-free' ),
				'2'  => __( 'Carousel', 'rife-free' ),
				'3'  => __( 'Zooming', 'rife-free' ),
			),
			'type'          => 'select',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Scale in %', 'rife-free' ),
			'description' => __( 'How big zooming effect will be', 'rife-free' ),
			'id'          => 'ken_scale',
			'default'     => 120,
			'unit'        => '%',
			'min'         => 100,
			'max'         => 200,
			'type'        => 'slider',
			'required'    => array(
				array( 'theme', '=', 'slider' ),
				array( 'transition', '=', '3' ),
			)
		),
		array(
			'name'        => __( 'Gradient above photos', 'rife-free' ),
			'description' => __( 'Good for better readability of slide titles.', 'rife-free' ),
			'id'          => 'gradient',
			'default'     => 'on',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Color under title', 'rife-free' ),
			'description' => __( 'Leave empty to not set any background', 'rife-free' ),
			'id'          => 'slide_title_bg_color',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'enable_desc', '=', 'on' ),
				array( 'theme', '=', 'slider' )
			)
		),
		array(
			'name'     => __( 'Pattern above photos', 'rife-free' ),
			'id'       => 'pattern',
			'default'  => '0',
			'options'  => array(
				'0' => __( 'None', 'rife-free' ),
				'1' => __( 'Type 1', 'rife-free' ),
				'2' => __( 'Type 2', 'rife-free' ),
				'3' => __( 'Type 3', 'rife-free' ),
				'4' => __( 'Type 4', 'rife-free' ),
				'5' => __( 'Type 5', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'          => __( 'List of Thumbs', 'rife-free' ),
			'id'            => 'thumbs',
			'default'       => 'G',
			'global_value'  => 'G',
			'parent_option' => 'album_slider_thumbs',
			'options'       => array(
				'G'   => __( 'Global settings', 'rife-free' ),
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'          => 'select',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Display thumbs opened', 'rife-free' ),
			'description' => __( 'If thumbs are enabled, should they be open by default?', 'rife-free' ),
			'id'          => 'thumbs_on_load',
			'default'     => 'off',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array( 'theme', '=', 'slider' ),
		),



		/*
		 *
		 * Tab: Album info
		 *
		 */
		array(
			'name' => __('Album info', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-info-circle'
		),
		array(
			'name'        => __( 'Internet address', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed.', 'rife-free' ),
			'id'          => 'www',
			'default'     => '',
			'placeholder' => 'http://link-to-somewhere.com',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 1', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_1',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 2', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_2',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 3', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_3',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 4', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_4',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 5', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_5',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),



		/*
		 *
		 * Tab: Header
		 *
		 */
		array(
			'name' => __('Header', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-cogs'
		),
		array(
			'name'          => __( 'Hide content under header', 'rife-free' ),
			'description'   => __( 'Only valid when using horizontal header.', 'rife-free' ),
			'id'            => 'content_under_header',
			'global_value'  => 'G',
			'default'       => 'G',
			'parent_option' => 'album_content_under_header',
			'type'          => 'select',
			'options'       => array(
				'G'       => __( 'Global settings', 'rife-free' ),
				'content' => __( 'Yes hide content', 'rife-free' ),
				'off'     => __( 'Turn it off', 'rife-free' ),
			),
		),
	);

	return $meta;
}



function apollo13framework_meta_boxes_images_manager() {
	$meta = array(
		array(
			'name' => '',
			'type' => 'fieldset'
		),
		array(
			'name'       => __( 'Multi upload', 'rife-free' ),
			'id'         => 'images_n_videos',
			'type'       => 'multi-upload',
			'default'    => '[]', //empty JSON
			'media_type' => 'image,video', /* 'audio,video' */
		),
		array(
			'name'         => '',
			'type'         => 'fieldset',
			'is_prototype' => true,
			'id'           => 'mu-prototype-image',
		),
		array(
			'name'        => __( 'Tags', 'rife-free' ),
			'description' => __( 'Separate tags with commas', 'rife-free' ),
			'id'          => 'image_tags',
			'default'     => '',
			'type'        => 'tag_media',
		),
		array(
			'name'        => __( 'Link', 'rife-free' ),
			'description' => __( 'Alternative link', 'rife-free' ),
			'id'          => 'image_link',
			'default'     => '',
			'type'        => 'text',
		),
		array(
			'name'    => __( 'Color under photo', 'rife-free' ),
			'id'      => 'image_bg_color',
			'default' => '',
			'type'    => 'color'
		),
		array(
			'name'        => __( 'RatioX in bricks theme', 'rife-free' ),
			'description' => __( 'How many bricks area should take this image.', 'rife-free' ),
			'id'          => 'image_ratio_x',
			'default'     => 1,
			'unit'        => '',
			'min'         => 1,
			'max'         => 6,
			'type'        => 'slider'
		),
		array(
			'name'         => '',
			'type'         => 'fieldset',
			'is_prototype' => true,
			'id'           => 'mu-prototype-video',
		),
		array(
			'name'        => __( 'Tags', 'rife-free' ),
			'description' => __( 'Separate tags with commas', 'rife-free' ),
			'id'          => 'video_tags',
			'default'     => '',
			'type'        => 'tag_media',
		),
		array(
			'name'        => __( 'Autoplay video', 'rife-free' ),
			'description' => __( 'Works only in slider', 'rife-free' ),
			'id'          => 'video_autoplay',
			'default'     => '0',
			'options'     => array(
				'1' => __( 'On', 'rife-free' ),
				'0' => __( 'Off', 'rife-free' ),
			),
			'type'        => 'radio',
		),
		array(
			'name'        => __( 'RatioX in bricks theme', 'rife-free' ),
			'description' => __( 'How many bricks area should take this video.', 'rife-free' ),
			'id'          => 'video_ratio_x',
			'default'     => 1,
			'unit'        => '',
			'min'         => 1,
			'max'         => 6,
			'type'        => 'slider'
		),
		array(
			'name'         => '',
			'type'         => 'fieldset',
			'is_prototype' => true,
			'id'           => 'mu-prototype-audio',
		),
		array(
			'name'        => __( 'Tags', 'rife-free' ),
			'description' => __( 'Separate tags with commas', 'rife-free' ),
			'id'          => 'audio_tags',
			'default'     => '',
			'type'        => 'tag_media',
		),
		array(
			'name'    => __( 'Autoplay audio', 'rife-free' ),
			'id'      => 'audio_autoplay',
			'default' => '0',
			'options' => array(
				'1' => __( 'On', 'rife-free' ),
				'0' => __( 'Off', 'rife-free' ),
			),
			'type'    => 'radio',
		),
		array(
			'name'        => __( 'RatioX in bricks theme', 'rife-free' ),
			'description' => __( 'How many bricks area should take this audio.', 'rife-free' ),
			'id'          => 'audio_ratio_x',
			'default'     => 1,
			'unit'        => '',
			'min'         => 1,
			'max'         => 6,
			'type'        => 'slider'
		),
		array(
			'name'         => '',
			'type'         => 'fieldset',
			'is_prototype' => true,
			'id'           => 'mu-prototype-videolink',
		),
		array(
			'name'        => __( 'Link to video', 'rife-free' ),
			'description' => __( 'Insert here link to your  youtube/vimeo video.', 'rife-free' ),
			'id'          => 'videolink_link',
			'default'     => '',
			'type'        => 'text',
		),
		array(
			'name'             => __( 'Video Thumb', 'rife-free' ),
			'description'      => __( 'Displayed instead of video placeholder in some cases. If none, placeholder will be used(for youtube movies default thumbnail will show).', 'rife-free' ),
			'id'               => 'videolink_poster',
			'default'          => '',
			'button_text'      => __( 'Upload Image', 'rife-free' ),
			'attachment_field' => 'videolink_attachment_id',
			'type'             => 'upload'
		),
		array(
			'name'    => __( 'Attachment id', 'rife-free' ),
			'id'      => 'videolink_attachment_id',
			'default' => '',
			'type'    => 'hidden'
		),
		array(
			'name'        => __( 'Tags', 'rife-free' ),
			'description' => __( 'Separate tags with commas', 'rife-free' ),
			'id'          => 'videolink_tags',
			'default'     => '',
			'type'        => 'tag_media',
		),
		array(
			'name'        => __( 'Autoplay video', 'rife-free' ),
			'description' => __( 'Works only in slider', 'rife-free' ),
			'id'          => 'videolink_autoplay',
			'default'     => '0',
			'options'     => array(
				'1' => __( 'On', 'rife-free' ),
				'0' => __( 'Off', 'rife-free' ),
			),
			'type'        => 'radio',
		),
		array(
			'name'        => __( 'RatioX in bricks theme', 'rife-free' ),
			'description' => __( 'How many bricks area should take this video.', 'rife-free' ),
			'id'          => 'videolink_ratio_x',
			'default'     => 1,
			'unit'        => '',
			'min'         => 1,
			'max'         => 6,
			'type'        => 'slider'
		),
		array(
			'name'    => __( 'Title', 'rife-free' ),
			'id'      => 'videolink_title',
			'default' => '',
			'type'    => 'text'
		),
		array(
			'name'    => __( 'Description', 'rife-free' ),
			'id'      => 'videolink_desc',
			'default' => '',
			'type'    => 'textarea',
		),
	);

	return $meta;
}



function apollo13framework_get_socials_array() {
	global $apollo13framework_a13;

	$tmp_arr = array();
	$socials = $apollo13framework_a13->get_social_icons_list();
	foreach ( $socials as $id => $social ) {
		array_push( $tmp_arr, array( 'name' => $social, 'id' => $id, 'type' => 'text' ) );
	}
	return $tmp_arr;
}



function apollo13framework_meta_boxes_people() {
	$meta =
		array_merge(
			array(
				/*
				 *
				 * Tab: General
				 *
				 */
				array(
					'name' => __('General', 'rife-free'),
					'type' => 'fieldset',
					'tab'   => true,
					'icon'  => 'fa fa-wrench'
				),
				array(
						'name'        => __( 'Subtitle', 'rife-free' ),
						'description' => __( 'You can use HTML here.', 'rife-free' ),
						'id'          => 'subtitle',
						'default'     => '',
						'type'        => 'text'
				),
				array(
						'name'    => __( 'Testimonial', 'rife-free' ),
						'desc'    => '',
						'id'      => 'testimonial',
						'default' => '',
						'type'    => 'textarea'
				),
				array(
						'name'        => __( 'Overlay color', 'rife-free' ),
						'description' => __( 'Use valid CSS <code>color</code> property values( <code>green, #33FF99, rgb(255,128,0), rgba(222,112,12,0.5)</code> ), or get your color with color picker tool. Left empty to use default theme value.', 'rife-free' ),
						'id'          => 'overlay_bg_color',
						'default'     => 'rgba(0,0,0,0.5)',
						'type'        => 'color'
				),
				array(
						'name'        => __( 'Overlay font color', 'rife-free' ),
						'description' => __( 'Use valid CSS <code>color</code> property values( <code>green, #33FF99, rgb(255,128,0), rgba(222,112,12,0.5)</code> ), or get your color with color picker tool. Left empty to use default theme value.', 'rife-free' ),
						'id'          => 'overlay_font_color',
						'default'     => 'rgba(255,255,255,1)',
						'type'        => 'color'
				),

				/*
				 *
				 * Tab: Socials
				 *
				 */
				array(
					'name' => __('Socials', 'rife-free'),
					'type' => 'fieldset',
					'tab'   => true,
					'icon'  => 'fa fa-facebook-official'
				),
			), apollo13framework_get_socials_array()
		);

	return $meta;
}



function apollo13framework_meta_boxes_work() {
	global $apollo13framework_a13;

	$header_sidebars = array(
		'G'   => __( 'Global settings', 'rife-free' ),
		'off' => __( 'Off', 'rife-free' ),
	);

	$custom_sidebars = $apollo13framework_a13->get_option( 'custom_sidebars' );
	$custom_sidebars = is_array($custom_sidebars)? $custom_sidebars : array($custom_sidebars);
	$sidebars_count  = count( $custom_sidebars );
	if ( is_array( $custom_sidebars ) && $sidebars_count > 0 ) {
		foreach ( $custom_sidebars as $sidebar ) {
			$header_sidebars[ $sidebar['id'] ] = $sidebar['name'];
		}
	}

	$meta = array(
		/*
		 *
		 * Tab: Works list
		 *
		 */
		array(
			'name' => __('Works list', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-list'
		),
		array(
			'name'        => __( 'Alternative Link', 'rife-free' ),
			'description' => __( 'If you fill this then clicking in your work on works list will not lead to single work page but to link from this field.', 'rife-free' ),
			'id'          => 'alt_link',
			'default'     => '',
			'type'        => 'text',
		),
		array(
			'name'    => __( 'Subtitle', 'rife-free' ),
			'id'      => 'subtitle',
			'default' => '',
			'type'    => 'text'
		),
		array(
			'name'        => __( 'Size of brick', 'rife-free' ),
			'description' => __( 'How many bricks area should take this work in works list.', 'rife-free' ),
			'id'          => 'brick_ratio_x',
			'default'     => 1,
			'unit'        => '',
			'min'         => 1,
			'max'         => 4,
			'type'        => 'slider'
		),
		array(
			'name'        => __( 'Cover color', 'rife-free' ),
			'id'          => 'cover_color',
			'description' => __( 'Works only when titles are displayed over images in Works list.', 'rife-free' ),
			'default'     => 'rgba(0,0,0, 0.7)',
			'type'        => 'color'
		),
		array(
			'name'        => __( 'Exclude from Works list page', 'rife-free' ),
			'description' => __( 'If enabled, then this work wont be listed on works list page, but you can still select it for front page or in other places.', 'rife-free' ),
			'id'          => 'exclude_in_works_list',
			'default'     => 'off',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
		),

		/*
		 *
		 * Tab: Work media
		 *
		 */
		array(
			'name' => __('Work media', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-th'
		),
		array(
			'name'        => __( 'Items order', 'rife-free' ),
			'description' => __( 'It will display your images/videos from first to last, or another way.', 'rife-free' ),
			'id'          => 'order',
			'default'     => 'ASC',
			'options'     => array(
				'ASC'    => __( 'First on list, first displayed', 'rife-free' ),
				'DESC'   => __( 'First on list, last displayed', 'rife-free' ),
				'random' => __( 'Random', 'rife-free' ),
			),
			'type'        => 'select',
		),
		array(
			'name'        => __( 'Show title and description of work items', 'rife-free' ),
			'description' => __( 'If enabled, then it will affect displaying in bricks and slider option, and also in lightbox.', 'rife-free' ),
			'id'          => 'enable_desc',
			'default'     => 'off',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
		),
		array(
			'name'    => __( 'Present media in:', 'rife-free' ),
			'id'      => 'theme',
			'default' => 'bricks',
			'options' => array(
				'bricks' => __( 'Bricks', 'rife-free' ),
				'slider' => __( 'Slider', 'rife-free' ),
				'off' => __( 'Do not display', 'rife-free' ),
			),
			'type'    => 'radio',
		),
		array(
			'name'        => __( 'Bricks columns', 'rife-free' ),
			'id'          => 'brick_columns',
			'default'     => '3',
			'unit'        => '',
			'min'         => 1,
			'max'         => 6,
			'type'        => 'slider',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'        => __( 'Max width of bricks content.', 'rife-free' ),
			'description' => __( 'Depending on actual screen width, available space for bricks might be smaller, but newer greater then this number.', 'rife-free' ),
			'id'          => 'bricks_max_width',
			'default'     => '1920px',
			'unit'        => 'px',
			'min'         => 200,
			'max'         => 2500,
			'type'        => 'slider',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'     => __( 'Brick margin', 'rife-free' ),
			'id'       => 'brick_margin',
			'default'  => '0px',
			'unit'     => 'px',
			'min'      => 0,
			'max'      => 100,
			'type'     => 'slider',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'     => __( 'Choose brick proportion', 'rife-free' ),
			'description' => __( 'Works only for images. If you switch theme option "Display thumbs instead of video", then for videos that you will upload image it will also work.', 'rife-free' ),
			'id'       => 'bricks_proportions_size',
			'default'  => '0',
			'options' => array(
				'0'    => __( 'Original size', 'rife-free' ),
				'1/1'  => __( '1:1', 'rife-free' ),
				'2/3'  => __( '2:3', 'rife-free' ),
				'3/2'  => __( '3:2', 'rife-free' ),
				'3/4'  => __( '3:4', 'rife-free' ),
				'4/3'  => __( '4:3', 'rife-free' ),
				'9/16' => __( '9:16', 'rife-free' ),
				'16/9' => __( '16:9', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_lightbox',
			'type'     => 'radio',
			'name'     => __( 'Open bricks to lightbox', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'on',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'        => __( 'Color of cover', 'rife-free' ),
			'description' => __( 'Leave empty to not set any background', 'rife-free' ),
			'id'          => 'slide_cover_color',
			'default'     => 'rgba(0,0,0, 0.7)',
			'type'        => 'color',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'name'     => __( 'Hover effect', 'rife-free' ),
			'id'       => 'bricks_hover',
			'default'  => 'cross',
			'options'  => array(
				'cross'  => __( 'Show cross', 'rife-free' ),
				'drop'   => __( 'Drop', 'rife-free' ),
				'shift'  => __( 'Shift', 'rife-free' ),
				'pop'    => __( 'Pop', 'rife-free' ),
				'border' => __( 'Border', 'rife-free' ),
				'none'   => __( 'None', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_title_position',
			'type'     => 'select',
			'name'     => __( 'Texts position', 'rife-free' ),
			'options'  => array(
				'top_left'      => __( 'Top left', 'rife-free' ),
				'top_center'    => __( 'Top center', 'rife-free' ),
				'top_right'     => __( 'Top right', 'rife-free' ),
				'mid_left'      => __( 'Middle left', 'rife-free' ),
				'mid_center'    => __( 'Middle center', 'rife-free' ),
				'mid_right'     => __( 'Middle right', 'rife-free' ),
				'bottom_left'   => __( 'Bottom left', 'rife-free' ),
				'bottom_center' => __( 'Bottom center', 'rife-free' ),
				'bottom_right'  => __( 'Bottom right', 'rife-free' ),
			),
			'default'  => 'bottom_center',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_overlay_cover',
			'type'     => 'radio',
			'name'     => __( 'Show cover when not hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'off',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_overlay_cover_hover',
			'type'     => 'radio',
			'name'     => __( 'Show cover when hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'on',
			'required' => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'          => 'bricks_overlay_gradient',
			'type'        => 'radio',
			'name'        => __( 'Show gradient when not hovering', 'rife-free' ),
			'description' => __( 'Its main function is to make texts more visible', 'rife-free' ),
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'     => 'off',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'          => 'bricks_overlay_gradient_hover',
			'type'        => 'radio',
			'name'        => __( 'Show gradient when hovering', 'rife-free' ),
			'description' => __( 'Its main function is to make texts more visible', 'rife-free' ),
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'     => 'off',
			'required'    => array( 'theme', '=', 'bricks' ),
		),
		array(
			'id'       => 'bricks_overlay_texts',
			'type'     => 'radio',
			'name'     => __( 'Show texts when not hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'off',
			'required' => array(
				array( 'theme', '=', 'bricks' ),
				array( 'enable_desc', '=', 'on' )
			),
		),
		array(
			'id'       => 'bricks_overlay_texts_hover',
			'type'     => 'radio',
			'name'     => __( 'Show texts when hovering', 'rife-free' ),
			'options'  => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'default'  => 'on',
			'required' => array(
				array( 'theme', '=', 'bricks' ),
				array( 'enable_desc', '=', 'on' )
			),
		),

		array(
			'name'          => __( 'Stretch slider to be window high', 'rife-free' ),
			'description'   => __( 'If there is enough space(more then 100px), slider will be stretched in height to take available space, in regards to header and title bar if they are present.', 'rife-free' ),
			'id'            => 'slider_window_high',
			'default'     => 'off',
			'options'       => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'          => 'radio',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		/* we don't hide proportion options cause they are fallback if there is not enough space for full height slider */
		array(
			'name'        => __( 'Slider - width proportion', 'rife-free' ),
			'id'          => 'slider_width_proportion',
			'default'     => '16',
			'unit'        => '',
			'min'         => 1,
			'max'         => 20,
			'type'        => 'slider',
			'required'    => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Slider - height proportion', 'rife-free' ),
			'id'          => 'slider_height_proportion',
			'default'     => '9',
			'unit'        => '',
			'min'         => 1,
			'max'         => 20,
			'type'        => 'slider',
			'required'    => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Fit images', 'rife-free' ),
			'description' => __( 'How will images fit area. <strong>Fit when needed</strong> is best for small images, that should not be stretched to bigger sizes, only to smaller(to keep them visible).', 'rife-free' ),
			'id'          => 'fit_variant',
			'default'     => '4',
			'options'     => array(
				'0' => __( 'Fit always', 'rife-free' ),
				'1' => __( 'Fit landscape', 'rife-free' ),
				'2' => __( 'Fit portrait', 'rife-free' ),
				'3' => __( 'Fit when needed', 'rife-free' ),
				'4' => __( 'Cover whole screen', 'rife-free' ),
			),
			'type'        => 'select',
			'required'    => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'          => __( 'Autoplay', 'rife-free' ),
			'description'   => __( 'If autoplay is on, slider items will start sliding on page load', 'rife-free' ),
			'id'            => 'autoplay',
			'default'       => 'G',
			'global_value'  => 'G',
			'parent_option' => 'work_slider_autoplay',
			'options'       => array(
				'G'   => __( 'Global settings', 'rife-free' ),
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'          => 'select',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'          => __( 'Transition type', 'rife-free' ),
			'description'   => __( 'Animation between slides.', 'rife-free' ),
			'id'            => 'transition',
			'default'       => '-1',
			'global_value'  => '-1',
			'parent_option' => 'work_slider_transition_type',
			'options'       => array(
				'-1' => __( 'Global settings', 'rife-free' ),
				'0'  => __( 'None', 'rife-free' ),
				'1'  => __( 'Fade', 'rife-free' ),
				'2'  => __( 'Carousel', 'rife-free' ),
				'3'  => __( 'Zooming', 'rife-free' ),
			),
			'type'          => 'select',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Scale in %', 'rife-free' ),
			'description' => __( 'How big zooming effect will be', 'rife-free' ),
			'id'          => 'ken_scale',
			'default'     => 120,
			'unit'        => '%',
			'min'         => 100,
			'max'         => 200,
			'type'        => 'slider',
			'required'    => array(
				array( 'theme', '=', 'slider' ),
				array( 'transition', '=', '3' ),
			)
		),
		array(
			'name'        => __( 'Gradient above photos', 'rife-free' ),
			'description' => __( 'Good for better readability of slide titles.', 'rife-free' ),
			'id'          => 'gradient',
			'default'     => 'off',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Color under title', 'rife-free' ),
			'description' => __( 'Leave empty to not set any background', 'rife-free' ),
			'id'          => 'slide_title_bg_color',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'enable_desc', '=', 'on' ),
				array( 'theme', '=', 'slider' )
			)
		),
		array(
			'name'     => __( 'Pattern above photos', 'rife-free' ),
			'id'       => 'pattern',
			'default'  => '0',
			'options'  => array(
				'0' => __( 'None', 'rife-free' ),
				'1' => __( 'Type 1', 'rife-free' ),
				'2' => __( 'Type 2', 'rife-free' ),
				'3' => __( 'Type 3', 'rife-free' ),
				'4' => __( 'Type 4', 'rife-free' ),
				'5' => __( 'Type 5', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'          => __( 'List of Thumbs', 'rife-free' ),
			'id'            => 'thumbs',
			'default'       => 'G',
			'global_value'  => 'G',
			'parent_option' => 'work_slider_thumbs',
			'options'       => array(
				'G'   => __( 'Global settings', 'rife-free' ),
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'          => 'select',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'        => __( 'Display thumbs opened', 'rife-free' ),
			'description' => __( 'If thumbs are enabled, should they be open by default?', 'rife-free' ),
			'id'          => 'thumbs_on_load',
			'default'     => 'off',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'     => __( 'Slider background color', 'rife-free' ),
			'id'       => 'slider_bg_color',
			'default'  => '',
			'type'     => 'color',
			'required'      => array( 'theme', '=', 'slider' ),
		),
		array(
			'name'     => __( 'Media top margin', 'rife-free' ),
			'id'       => 'media_margin_top',
			'default'  => '0px',
			'unit'     => 'px',
			'min'      => 0,
			'max'      => 100,
			'type'     => 'slider',
			'required' => array( 'theme', '!=', 'off' ),
		),
		array(
			'name'     => __( 'Media bottom margin', 'rife-free' ),
			'id'       => 'media_margin_bottom',
			'default'  => '0px',
			'unit'     => 'px',
			'min'      => 0,
			'max'      => 100,
			'type'     => 'slider',
			'required' => array( 'theme', '!=', 'off' ),
		),

		/*
		 *
		 * Tab: Work info
		 *
		 */
		array(
			'name' => __('Work info', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-info-circle'
		),
		array(
			'name'        => __( 'Internet address', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed.', 'rife-free' ),
			'id'          => 'www',
			'default'     => '',
			'placeholder' => 'http://link-to-somewhere.com',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 1', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_1',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 2', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_2',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 3', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_3',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 4', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_4',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),
		array(
			'name'        => __( 'Custom info 5', 'rife-free' ),
			'description' => __( 'If empty it will not be displayed. Use pattern <strong>Field name: Field value</strong>. Colon(:) is most important to get full result.', 'rife-free' ),
			'id'          => 'custom_5',
			'default'     => '',
			'placeholder' => 'Label: value',
			'type'        => 'text'
		),

		/*
		 *
		 * Tab: Layout
		 *
		 */
		array(
			'name' => __('Layout', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-wrench'
		),
		array(
			'name'          => __( 'Content Layout', 'rife-free' ),
			'id'            => 'content_layout',
			'default'       => 'global',
			'global_value'  => 'global',
			'parent_option' => 'work_content_layout',
			'type'          => 'select',
			'options'       => array(
				'global'        => __( 'Global settings', 'rife-free' ),
				'center'        => __( 'Center fixed width', 'rife-free' ),
				'left'          => __( 'Left fixed width', 'rife-free' ),
				'left_padding'  => __( 'Left fixed width + padding', 'rife-free' ),
				'right'         => __( 'Right fixed width', 'rife-free' ),
				'right_padding' => __( 'Right fixed width + padding', 'rife-free' ),
				'full_fixed'    => __( 'Full width + fixed content', 'rife-free' ),
				'full_padding'  => __( 'Full width + padding', 'rife-free' ),
				'full'          => __( 'Full width', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Content top/bottom padding', 'rife-free' ),
			'description' => __( 'Enable or disable top and bottom padding of content. It is helpful in achieving some neat layout effects:-).', 'rife-free' ),
			'id'          => 'content_padding',
			'default'     => 'both',
			'type'        => 'select',
			'options'     => array(
				'both'   => __( 'Both on', 'rife-free' ),
				'top'    => __( 'Only top', 'rife-free' ),
				'bottom' => __( 'Only bottom', 'rife-free' ),
				'off'    => __( 'Both off', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Content side padding', 'rife-free' ),
			'description' => __( 'It is helpful in achieving some neat layout effects:-).', 'rife-free' ),
			'id'          => 'content_side_padding',
			'default'     => 'both',
			'type'        => 'radio',
			'options'     => array(
				'both'   => __( 'Both on', 'rife-free' ),
				'off'    => __( 'Both off', 'rife-free' ),
			),
		),

		/*
		 *
		 * Tab: Header
		 *
		 */
		array(
			'name' => __('Header', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-cogs'
		),
		array(
			'name'          => __( 'Hide content under header', 'rife-free' ),
			'description'   => __( 'Only valid when using horizontal header.', 'rife-free' ),
			'id'            => 'content_under_header',
			'global_value'  => 'G',
			'default'       => 'G',
			'parent_option' => 'work_content_under_header',
			'type'          => 'select',
			'options'       => array(
				'G'       => __( 'Global settings', 'rife-free' ),
				'content' => __( 'Yes hide content', 'rife-free' ),
				'title'   => __( 'Yes hide content and add top padding to outside title bar.', 'rife-free' ),
				'off'     => __( 'Turn it off', 'rife-free' ),
			),
		),

		/*
		 *
		 * Tab: Title bar
		 *
		 */
		array(
			'name' => __('Title bar', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-text-width'
		),
		array(
			'name'        => __( 'Title bar look', 'rife-free' ),
			'description' => __( 'You can use global settings or overwrite them here', 'rife-free' ),
			'id'          => 'title_bar_settings',
			'default'     => 'global',
			'type'        => 'radio',
			'options'     => array(
				'global' => __( 'Global settings', 'rife-free' ),
				'custom' => __( 'Use custom settings', 'rife-free' ),
				'off'    => __( 'Turn it off', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Position', 'rife-free' ),
			'id'          => 'title_bar_position',
			'default'     => 'outside',
			'type'        => 'hidden',//no switching in options, but we leave it as option so it will be future ready, and to not make exceptions for Works
			'options'     => array(
				'outside' => __( 'Before content', 'rife-free' ),
				'inside'  => __( 'Inside content', 'rife-free' ),
			),
			'description' => __( 'To set background image for "Before content" option, use <strong>featured image</strong>.', 'rife-free' ),
			'required'    => array( 'title_bar_settings', '=', 'custom' ),
		),
		array(
			'name'        => __( 'Variant', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_variant',
			'default'     => 'classic',
			'options'     => array(
				'classic'  => __( 'Classic(to side)', 'rife-free' ),
				'centered' => __( 'Centered', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Width', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_width',
			'default'     => 'full',
			'options'     => array(
				'full'  => __( 'Full', 'rife-free' ),
				'boxed' => __( 'Boxed', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'     => __( 'How to fit background image', 'rife-free' ),
			'id'       => 'title_bar_image_fit',
			'default'  => 'repeat',
			'options'  => array(
				'cover'    => __( 'Cover', 'rife-free' ),
				'contain'  => __( 'Contain', 'rife-free' ),
				'fitV'     => __( 'Fit Vertically', 'rife-free' ),
				'fitH'     => __( 'Fit Horizontally', 'rife-free' ),
				'center'   => __( 'Just center', 'rife-free' ),
				'repeat'   => __( 'Repeat', 'rife-free' ),
				'repeat-x' => __( 'Repeat X', 'rife-free' ),
				'repeat-y' => __( 'Repeat Y', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Enable parallax?', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_parallax',
			'default'     => 'off',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'type'        => 'radio',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Parallax type', 'rife-free' ),
			'description' => __( 'It defines how image will scroll in background while page is scrolled down.', 'rife-free' ),
			'id'          => 'title_bar_parallax_type',
			'default'     => 'tb',
			'options'     => array(
				"tb"   => __( 'top to bottom', 'rife-free' ),
				"bt"   => __( 'bottom to top', 'rife-free' ),
				"lr"   => __( 'left to right', 'rife-free' ),
				"rl"   => __( 'right to left', 'rife-free' ),
				"tlbr" => __( 'top-left to bottom-right', 'rife-free' ),
				"trbl" => __( 'top-right to bottom-left', 'rife-free' ),
				"bltr" => __( 'bottom-left to top-right', 'rife-free' ),
				"brtl" => __( 'bottom-right to top-left', 'rife-free' ),
			),
			'type'        => 'select',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
				array( 'title_bar_parallax', '=', 'on' ),
			)
		),
		array(
			'name'        => __( 'Parallax speed', 'rife-free' ),
			'description' => __( 'It will be only used for background that are repeated. If background is set to not repeat this value will be ignored.', 'rife-free' ),
			'id'          => 'title_bar_parallax_speed',
			'default'     => '1.00',
			'type'        => 'text',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
				array( 'title_bar_parallax', '=', 'on' ),
			)
		),
		array(
			'name'        => __( 'Overlay color', 'rife-free' ),
			'description' => __( 'It will be put above image(if used)', 'rife-free' ),
			'id'          => 'title_bar_bg_color',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'     => __( 'Titles color', 'rife-free' ),
			'id'       => 'title_bar_title_color',
			'default'  => '',
			'type'     => 'color',
			'required' => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Second elements color', 'rife-free' ),
			'description' => __( 'Used in breadcrumbs.', 'rife-free' ),
			'id'          => 'title_bar_color_1',
			'default'     => '',
			'type'        => 'color',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Space in top and bottom', 'rife-free' ),
			'description' => '',
			'id'          => 'title_bar_space_width',
			'default'     => '40px',
			'unit'        => 'px',
			'min'         => 0,
			'max'         => 600,
			'type'        => 'slider',
			'required'    => array(
				array( 'title_bar_settings', '=', 'custom' ),
				array( 'title_bar_position', '!=', 'inside' ),
			)
		),
		array(
			'name'        => __( 'Breadcrumbs', 'rife-free' ),
			'description' => '',
			'id'          => 'breadcrumbs',
			'default'     => 'on',
			'type'        => 'radio',
			'options'     => array(
				'on'  => __( 'Enable', 'rife-free' ),
				'off' => __( 'Disable', 'rife-free' ),
			),
			'required'    => array( 'title_bar_settings', '=', 'custom' ),
		),

		/*
		 *
		 * Tab: Content
		 *
		 */
		array(
			'name' => __('Content', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-align-left'
		),
		array(
			'name'          => __( 'Categories in content', 'rife-free' ),
			'id'            => 'content_categories',
			'default'       => 'G',
			'global_value'  => 'G',
			'parent_option' => 'work_content_categories',
			'type'          => 'radio',
			'options'       => array(
				'G'   => __( 'Global settings', 'rife-free' ),
				'on'  => __( 'On', 'rife-free' ),
				'off' => __( 'Off', 'rife-free' ),
			),
		),

		/*
		 *
		 * Tab: Page background
		 *
		 */
		array(
			'name' => __('Page background', 'rife-free'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-picture-o'
		),
		array(
			'name'        => __( 'Page background', 'rife-free' ),
			'description' => __( 'You can use global settings or overwrite them here', 'rife-free' ),
			'id'          => 'page_bg_settings',
			'default'     => 'global',
			'type'        => 'radio',
			'options'     => array(
				'global' => __( 'Global settings', 'rife-free' ),
				'custom' => __( 'Use custom settings', 'rife-free' ),
			),
		),
		array(
			'name'        => __( 'Page Background image file', 'rife-free' ),
			'id'          => 'page_image',
			'default'     => '',
			'button_text' => __( 'Upload Image', 'rife-free' ),
			'type'        => 'upload',
			'required'    => array( 'page_bg_settings', '=', 'custom' ),
		),
		array(
			'name'     => __( 'How to fit background image', 'rife-free' ),
			'id'       => 'page_image_fit',
			'default'  => 'cover',
			'options'  => array(
				'cover'    => __( 'Cover', 'rife-free' ),
				'contain'  => __( 'Contain', 'rife-free' ),
				'fitV'     => __( 'Fit Vertically', 'rife-free' ),
				'fitH'     => __( 'Fit Horizontally', 'rife-free' ),
				'center'   => __( 'Just center', 'rife-free' ),
				'repeat'   => __( 'Repeat', 'rife-free' ),
				'repeat-x' => __( 'Repeat X', 'rife-free' ),
				'repeat-y' => __( 'Repeat Y', 'rife-free' ),
			),
			'type'     => 'select',
			'required' => array( 'page_bg_settings', '=', 'custom' ),
		),
		array(
			'name'     => __( 'Page Background color', 'rife-free' ),
			'id'       => 'page_bg_color',
			'default'  => '',
			'type'     => 'color',
			'required' => array( 'page_bg_settings', '=', 'custom' ),
		),
	);

	return $meta;
}
