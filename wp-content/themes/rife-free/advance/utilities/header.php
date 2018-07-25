<?php
/**
 * Functions that operates in themes header element
 */


if ( ! function_exists( 'apollo13framework_header_logo' ) ) {
	/**
	 * Prints logo of site
	 */
	function apollo13framework_header_logo() {
		global $apollo13framework_a13;
		$logo_sources       = array(
			'normal' => $apollo13framework_a13->get_option_media_url( 'logo_image' ),
			'sticky' => $apollo13framework_a13->get_option_media_url( 'header_sticky_logo_image' ),
			'dark'   => $apollo13framework_a13->get_option_media_url( 'header_dark_logo_image' ),
			'light'  => $apollo13framework_a13->get_option_media_url( 'header_light_logo_image' )
		);
		$img_logo           = $apollo13framework_a13->get_option( 'logo_type' ) === 'image' && strlen( $logo_sources['normal'] );
		$is_horizontal      = $apollo13framework_a13->get_option( 'header_type' ) === 'horizontal';
		$color_variant      = apollo13framework_horizontal_header_color_variant();
		$logo_from_variants = $apollo13framework_a13->get_option( 'logo_from_variants' ) === 'on' && $apollo13framework_a13->get_option( 'header_color_variants', 'on' ) === 'on';

		$hidden = (
			//not horizontal header
			!$is_horizontal ||
			//or logos from other variants are disabled
			!$logo_from_variants ||
			//or it is text logo
			!$img_logo ||
			//or horizontal header with normal color
			( $is_horizontal && $color_variant === 'normal' ) ||
			//or horizontal header with different color variant but not set logo
			( $is_horizontal && $color_variant !== 'normal' && ( isset($logo_sources[ $color_variant ]) && ! strlen( $logo_sources[ $color_variant ] ) ) )
		)?
			//then don't hide normal logo
			'' :
			//otherwise - hide normal logo
			' hidden-logo';

		$html = '<a class="'. esc_attr('logo ' . ($img_logo ? 'image-logo' : 'text-logo') . ' normal-logo'.$hidden) .'" href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home">';

		if ( $img_logo ) {
			$html .= '<img src="' . esc_url( $logo_sources['normal'] ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';
		} else {
			$logo_text = esc_html( $apollo13framework_a13->get_option( 'logo_text' ) );
			//try site name if no text
			$logo_text = strlen($logo_text) > 0 ? $logo_text : get_bloginfo('name');

			$html .= $logo_text;
		}

		$html .= '</a>';

		//we add other logo variants only for image logo
		if ( $img_logo && $is_horizontal && $logo_from_variants ) {
			foreach($logo_sources as $variant => $src){
				if($variant === 'normal'){
					//we already printed it out
					continue;
				}

				//print logo variant if there is any source
				if(strlen($src)){
					$html .= '<a class="'.esc_attr('logo image-logo '.$variant.'-logo'.($color_variant === $variant? '' : ' hidden-logo')).'" href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home">';
					$html .= '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';
					$html .= '</a>';
				}
			}
		}

		echo wp_kses_post( $html );
	}
}


if ( ! function_exists( 'apollo13framework_header_search' ) ) {
	/**
	 * Prints out search form usually used in header
	 *
	 * @return string   HTML
	 */
	function apollo13framework_header_search() {
		global $apollo13framework_a13;

		if($apollo13framework_a13->get_option( 'header_search' ) === 'on'){
			return
				'<div class="search-container">' .
				'<div class="search">' .
				'<span class="a13icon-search"></span>' .
				apollo13framework_search_form( '', true ) .
				'<span class="a13icon-cross close"></span>' .
				'</div>' .
				//only if plugin "SearchWP Live Ajax Search" is activated
				( class_exists('SearchWP_Live_Search')? '<div id="search-results-header"></div>' : '' ) .
				'</div>';
		}
		return '';
	}
}



if ( ! function_exists( 'apollo13framework_header_menu' ) ) {
	/**
	 * Prints main menu usually located in header
	 *
	 * @param string $walker type of walker we should run this menu with
	 */
	function apollo13framework_header_menu( $walker = '' ) {
		/* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.
		 * The menu assigned to the primary position is the one used.
		 * If none is assigned, the menu with the lowest ID is used.
		 */

		global $apollo13framework_a13;

		$menu_hover_effect = $apollo13framework_a13->get_option('menu_hover_effect');
		$menu_classes = 'top-menu';
		$menu_classes .= (strlen($menu_hover_effect) && $menu_hover_effect !== 'none') ? ' with-effect menu--'.$menu_hover_effect : '';
		$menu_classes .= $apollo13framework_a13->get_option('submenu_open_icons') === 'on' ? ' opener-icons-on' : ' opener-icons-off';

		//choose proper walker for menu
		if ( $walker === 'one-line-left' ) {
			$menu_classes .= ' left-part';
			$params = array(
				'container'      => false,
				'link_before'    => '<span>',
				'link_after'     => '</span>',
				'menu_class'     => $menu_classes,
				'theme_location' => 'header-menu',
				'walker'         => new A13FRAMEWORK_menu_one_line_left_walker,
				'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
			);
		} elseif ( $walker === 'one-line-right' ) {
			$menu_classes .= ' right-part';
			$params = array(
				'container'      => false,
				'link_before'    => '<span>',
				'link_after'     => '</span>',
				'menu_class'     => $menu_classes,
				'theme_location' => 'header-menu',
				'walker'         => new A13FRAMEWORK_menu_one_line_right_walker,
				'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
			);
		} else {
			$params = array(
				'container'       => 'div',
				'container_class' => 'menu-container',
				'link_before'     => '<span>',
				'link_after'      => '</span>',
				'menu_class'      => $menu_classes,
				'theme_location'  => 'header-menu',
				'walker'          => new A13FRAMEWORK_menu_walker,
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			);
		}

		if ( has_nav_menu( 'header-menu' ) ):
			wp_nav_menu( $params );
		else:
			//no walker
			if($walker === ''){
				echo '<div class="menu-container">';
			}
			echo '<ul class="'.esc_attr($menu_classes).'">';

			$args = array(
				'depth'        => 1,
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'title_li'    => ''
			);

			if ( $walker === 'one-line-left' ) {
				$args['walker'] = new A13FRAMEWORK_page_tree_one_line_left_walker;
			}
			elseif ( $walker === 'one-line-right' ) {
				$args['walker'] = new A13FRAMEWORK_page_tree_one_line_right_walker;
			}

			wp_list_pages($args);

			echo '</ul>';
			//no walker
			if($walker === ''){
				echo '</div>';
			}
		endif;
	}
}


if ( ! function_exists( 'apollo13framework_get_header_toolbar' ) ) {
	/**
	 * Prints out header tools
	 *
	 * @param int $icons taken by reference so it can be used back in place of call
	 *
	 * @return string   HTML
	 */
	function apollo13framework_get_header_toolbar( &$icons ) {
		global $apollo13framework_a13, $woocommerce;

		$hidden_sidebar    = is_active_sidebar( 'side-widget-area' );
		$basket_sidebar    = apollo13framework_is_woocommerce_activated() && is_active_sidebar( 'basket-widget-area' );
		$header_search     = $apollo13framework_a13->get_option( 'header_search' ) === 'on';
		$allow_mobile_menu = $apollo13framework_a13->get_option( 'header_type' ) === 'vertical'
		                     || ($apollo13framework_a13->get_option( 'header_main_menu' ) === 'on' && $apollo13framework_a13->get_option( 'menu_allow_mobile_menu' ) !== 'off');
		$menu_overlay      = $apollo13framework_a13->get_option( 'header_menu_overlay' ) === 'on';
		$button            = $apollo13framework_a13->get_option( 'header_button' );
		$button_link       = $apollo13framework_a13->get_option( 'header_button_link' );
		$button_new_tab    = $apollo13framework_a13->get_option( 'header_button_link_target' ) === 'on' ? ' target="_blank"' : '';
		$button_on_mobile  = $apollo13framework_a13->get_option( 'header_button_display_on_mobile' ) === 'off' ? ' hide_on_mobile' : '';
		$is_button         = strlen( $button );
		$icons             = 4;

		//default, custom or animated icons
		$mm_type    = $apollo13framework_a13->get_option( 'header_tools_mobile_menu_icon_type' );
		$hs_type    = $apollo13framework_a13->get_option( 'header_tools_hidden_sidebar_icon_type' );
		$mo_type    = $apollo13framework_a13->get_option( 'header_tools_menu_overlay_icon_type' );
		$bs_type    = $apollo13framework_a13->get_option( 'header_tools_basket_sidebar_icon_type' );
		$hsrch_type = $apollo13framework_a13->get_option( 'header_tools_header_search_icon_type' );

		//mobile menu icon
		if($mm_type === 'animated'){
			$mobile_menu_icon = 'hamburger hamburger--ef'.$apollo13framework_a13->get_option( 'header_tools_mobile_menu_effect_active');
		}
		else{
			$mobile_menu_icon = $mm_type === 'custom' ?  'fa fa-'.$apollo13framework_a13->get_option( 'header_tools_mobile_menu_icon' ) : 'a13icon-menu';
		}
		//hidden sidebar icon
		if($hs_type === 'animated'){
			$hidden_sidebar_icon = 'hamburger hamburger--ef'.$apollo13framework_a13->get_option( 'header_tools_hidden_sidebar_effect_active');
		}
		else{
			$hidden_sidebar_icon = $hs_type === 'custom' ?  'fa fa-'.$apollo13framework_a13->get_option( 'header_tools_hidden_sidebar_icon' ) : 'a13icon-add-to-list';
		}
		//menu overaly icon
		if($mo_type === 'animated'){
			$menu_overlay_icon = 'hamburger hamburger--ef'.$apollo13framework_a13->get_option( 'header_tools_menu_overlay_effect_active');
		}
		else{
			$menu_overlay_icon = $mo_type === 'custom' ?  'fa fa-'.$apollo13framework_a13->get_option( 'header_tools_menu_overlay_icon' ) : 'a13icon-menu';
		}
		//icons with no option for animation
		$basket_sidebar_icon = $bs_type === 'custom' ?  'fa fa-'.$apollo13framework_a13->get_option( 'header_tools_basket_sidebar_icon' ) : 'a13icon-cart';
		$header_search_icon = $hsrch_type === 'custom' ?  'fa fa-'.$apollo13framework_a13->get_option( 'header_tools_header_search_icon' ) : 'a13icon-search';

		//count how many icons are used
		if ( ! $hidden_sidebar ) {
			$icons --;
		}
		if ( ! $basket_sidebar ) {
			$icons --;
		}
		if ( ! $header_search ) {
			$icons --;
		}
		if ( ! $menu_overlay ) {
			$icons --;
		}

		$classes = ' icons-' . $icons;

		//check if only mobile menu is used
		if($icons === 0 && !$is_button && $allow_mobile_menu){
			$classes .= ' only-menu';
		}

		//prepare icons HTML
		$html = '';

		if($icons > 0 || $allow_mobile_menu || $is_button){
			$html = '
				<div id="header-tools" class="' . esc_attr( $classes ) . '">' .
			        ( $basket_sidebar ? '<div id="basket-menu-switch" class="'.esc_attr($basket_sidebar_icon).' tool" title="' . esc_attr( esc_html__( 'Shop sidebar', 'rife-free' ) ) . '"><span id="basket-items-count" class="zero">' . esc_html( $woocommerce->cart->cart_contents_count ) . '</span></div>' : '' ) .
			        ( $header_search ? '<div id="search-button" class="'.esc_attr($header_search_icon).' tool" title="' . esc_attr( esc_html__( 'Search', 'rife-free' ) ) . '"></div>' : '' ) .
			        ( $hidden_sidebar ? '<div id="side-menu-switch" class="'.esc_attr($hidden_sidebar_icon).' tool" title="' . esc_attr( esc_html__( 'Hidden sidebar', 'rife-free' ) ) . '">'.($hs_type === 'animated'? '<span>'.esc_html__( 'Hidden sidebar', 'rife-free' ).'</span>' : '').'</div>' : '' ) .
			        ( $menu_overlay ? '<div id="menu-overlay-switch" class="'.esc_attr($menu_overlay_icon).' tool" title="' . esc_attr( esc_html__( 'Main menu', 'rife-free' ) ) . '">'.($mo_type === 'animated'? '<span>'.esc_html__( 'Main menu', 'rife-free' ).'</span>' : '').'</div>' : '' ) .
			        ( $allow_mobile_menu ? '<div id="mobile-menu-opener" class="'.esc_attr($mobile_menu_icon).' tool" title="' . esc_attr( esc_html__( 'Main menu', 'rife-free' ) ) . '">'.($mm_type === 'animated'? '<span>'.esc_html__( 'Main menu', 'rife-free' ).'</span>' : '').'</div>' : '' ) .
			        ( $is_button? '<a class="tools_button'.esc_attr($button_on_mobile).'" href="'.esc_url($button_link).'" '.$button_new_tab.'>'.$button.'</a>' : '' ).
		        '</div>';
		}

		return $html;
	}
}


if ( ! function_exists( 'apollo13framework_theme_header_custom_sidebar_name' ) ) {
	/**
	 * Checks which custom sidebar should be returned
	 */
	function apollo13framework_theme_header_custom_sidebar_name() {
		global $apollo13framework_a13;

		$page_type = apollo13framework_what_page_type_is_it();

		$global_value = $apollo13framework_a13->get_option( 'header_custom_sidebar' );
		$sidebar      = $global_value;

		//albums list - first cause it is also page type!
		if ( $page_type['albums_list'] ) {
			$sidebar = $apollo13framework_a13->get_option( 'albums_list_header_custom_sidebar' );

			//if it point to global value
			if ( $sidebar === 'G' ) {
				$sidebar = $global_value;
			}
		}
		//works list - before page cause it is also page type!
		elseif ( $page_type['works_list'] ) {
			$sidebar = $apollo13framework_a13->get_option( 'works_list_header_custom_sidebar' );

			//if it point to global value
			if ( $sidebar === 'G' ) {
				$sidebar = $global_value;
			}
		}
		 //pages, posts, albums, works
		elseif ( $page_type['page'] || $page_type['album'] || $page_type['work'] || $page_type['post'] ) {
			$sidebar = $apollo13framework_a13->get_meta( '_header_custom_sidebar', get_the_ID() );
		} //shop
		elseif ( $page_type['shop'] ) {
			$sidebar = $apollo13framework_a13->get_option( 'shop_header_custom_sidebar' );

			//if it point to global value
			if ( $sidebar === 'G' ) {
				$sidebar = $global_value;
			}
		} //blog
		elseif ( $page_type['blog_type'] ) {
			$sidebar = $apollo13framework_a13->get_option( 'blog_header_custom_sidebar' );

			//if it point to global value
			if ( $sidebar === 'G' ) {
				$sidebar = $global_value;
			}
		}

		return $sidebar;
	}
}


if ( ! function_exists( 'apollo13framework_content_under_header' ) ) {
	/**
	 * Checks if for current page content should be hidden under header
	 */
	function apollo13framework_content_under_header() {
		global $apollo13framework_a13;

		$page_type = apollo13framework_what_page_type_is_it();
		$value = 'off';

		//albums list - first cause it is also page type!
		if ( $page_type['albums_list'] ) {
			$value = $apollo13framework_a13->get_option( 'albums_list_content_under_header' );
		}
		//works list - before page cause it is also page type!
		elseif ( $page_type['works_list'] ) {
			$value = $apollo13framework_a13->get_option( 'works_list_content_under_header' );
		}
		//pages, posts, albums, work
		elseif ( $page_type['page'] || $page_type['album'] || $page_type['work'] || $page_type['post'] ) {
			$value = $apollo13framework_a13->get_meta('_content_under_header', get_the_ID() );
		}
		//shop
		elseif ( $page_type['shop'] ) {
			$value = $apollo13framework_a13->get_option( 'shop_content_under_header' );
		}
		//blog
		elseif ( $page_type['blog_type'] ) {
			$value = $apollo13framework_a13->get_option( 'blog_content_under_header' );
		}

		return $value;
	}
}



if ( ! function_exists( 'apollo13framework_theme_header' ) ) {
	/**
	 * Print whole header
	 */
	function apollo13framework_theme_header() {
		global $apollo13framework_a13;

		//Header Footer Elementor Plugin support
		if ( function_exists( 'hfe_render_header' ) ) {
			hfe_render_header();
		}

		if( $apollo13framework_a13->get_option( 'header_switch', 'on' ) === 'off' ){
			//no theme header
			return;
		}

		$header_type    = $apollo13framework_a13->get_option( 'header_type' );
		$header_subtype = '';
		if ( $header_type === 'horizontal' ) {
			$header_variant = $apollo13framework_a13->get_option( 'header_horizontal_variant' );

			if(strpos($header_variant, 'one_line') !== false){
				$header_subtype = 'one-line';
			}
			else{
				$header_subtype = 'multi-line';
			}

		} elseif ( $header_type === 'vertical' ) {
			$header_variant = $apollo13framework_a13->get_option( 'header_vertical_variant' );
			$header_subtype = $header_variant;
		}

		get_template_part( 'header-variants/' . $header_type, $header_subtype );
	}
}


if ( ! function_exists( 'apollo13framework_options_header_type_filter' ) ) {
	/**
	 * Changes option of header type if boxed layout is used
	 *
	 * @param $value
	 *
	 * @return string
	 */
	function apollo13framework_options_header_type_filter($value) {
		global $apollo13framework_a13;
		if($apollo13framework_a13->get_option( 'layout_type' ) === 'boxed') {
			if($value === 'vertical'){
				$value = 'horizontal';
			}
		}

		return $value;
	}
}
add_filter('a13_options_header_type', 'apollo13framework_options_header_type_filter' );


function apollo13framework_header_button() {
	global $apollo13framework_a13;

	$button            = $apollo13framework_a13->get_option( 'header_button' );
	$button_link       = $apollo13framework_a13->get_option( 'header_button_link' );
	$button_new_tab    = $apollo13framework_a13->get_option( 'header_button_link_target' ) === 'on';
	$button_on_mobile  = $apollo13framework_a13->get_option( 'header_button_display_on_mobile' ) === 'off' ? ' hide_on_mobile' : '';

	echo '<a class="tools_button'.esc_attr($button_on_mobile).'" href="'.esc_url($button_link).'" '.
	     ( $button_new_tab ? ' target="_blank"' : '' ).'>'.esc_html( $button ).'</a>';
}

function apollo13framework_header_button_css() {
	global $apollo13framework_a13;

	$header_tools_color         = apollo13framework_make_css_rule( 'color', $apollo13framework_a13->get_option_color_rgba( 'header_tools_color' ) );
	$header_tools_color_hover   = apollo13framework_make_css_rule( 'color', $apollo13framework_a13->get_option_color_rgba( 'header_tools_color_hover' ) );
	$header_button_font_size          = apollo13framework_make_css_rule( 'font-size', $apollo13framework_a13->get_option( 'header_button_font_size' ), '%spx' );
	$header_button_weight             = apollo13framework_make_css_rule( 'font-weight', $apollo13framework_a13->get_option( 'header_button_weight' ) );
	$header_button_bg_color           = apollo13framework_make_css_rule( 'background-color', $apollo13framework_a13->get_option_color_rgba( 'header_button_bg_color' ) );
	$header_button_bg_color_hover     = apollo13framework_make_css_rule( 'background-color', $apollo13framework_a13->get_option_color_rgba( 'header_button_bg_color_hover' ) );
	$header_button_border_color       = apollo13framework_make_css_rule( 'border-color', $apollo13framework_a13->get_option_color_rgba( 'header_button_border_color' ) );
	$header_button_border_color_hover = apollo13framework_make_css_rule( 'border-color', $apollo13framework_a13->get_option_color_rgba( 'header_button_border_color_hover' ) );

	$css = "
.tools_button{
    $header_button_font_size
    $header_button_weight
    $header_tools_color
    $header_button_bg_color
    $header_button_border_color
}
.tools_button:hover{
	$header_tools_color_hover
	$header_button_bg_color_hover
    $header_button_border_color_hover
}";

	return $css;
}

function apollo13framework_header_button_partial_css($response) {
	return apollo13framework_prepare_partial_css($response, 'header_button', 'apollo13framework_header_button_css');
}
add_filter( 'customize_render_partials_response', 'apollo13framework_header_button_partial_css' );