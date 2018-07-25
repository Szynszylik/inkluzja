<?php
/**
 * Functions that operates in themes footer element
 */

if(!function_exists('apollo13framework_theme_footer')){
	function apollo13framework_theme_footer(){
		global $apollo13framework_a13;

		//Header Footer Elementor Plugin support
		if ( function_exists( 'hfe_render_footer' ) ) {
			hfe_render_footer();
		}

		if( $apollo13framework_a13->get_option( 'footer_switch', 'on' ) === 'off' ){
			//no theme footer
			return;
		}

		$html = '';

		ob_start();
		apollo13framework_footer_widgets();
		apollo13framework_footer_items();

		$output = ob_get_contents();
		ob_end_clean();

		if(strlen($output)){
			$header_type = $apollo13framework_a13->get_option( 'header_type' );
			$to_move     = $header_type === 'vertical' ? '' : 'to-move';
			$width       = ' ' . $apollo13framework_a13->get_option( 'footer_content_width' );
			$style       = ' ' . $apollo13framework_a13->get_option( 'footer_content_style' );
			$separator   = $apollo13framework_a13->get_option( 'footer_separator' ) === 'on' ? ' footer-separator' : '';
			$unravel     = ($header_type === 'horizontal' && $apollo13framework_a13->get_option( 'footer_unravel_effect' ) === 'on') ? ' unravel' : '';

			$footer_class = $to_move.$width.$style.$separator.$unravel;
			$html = '<footer id="footer" class="'.esc_attr($footer_class).'">'.$output.'</footer>';
		}

		print $html;
	}
}


function apollo13framework_footer_css() {
	global $apollo13framework_a13;

	$css = '';

	if ( $apollo13framework_a13->get_option( 'footer_switch' ) === 'on' ) {
		$footer_bg_color          = apollo13framework_make_css_rule( 'background-color', $apollo13framework_a13->get_option_color_rgba( 'footer_bg_color' ) );
		$footer_lower_bg_color    = apollo13framework_make_css_rule( 'background-color', $apollo13framework_a13->get_option_color_rgba( 'footer_lower_bg_color' ) );
		$footer_font_size         = apollo13framework_make_css_rule( 'font-size', $apollo13framework_a13->get_option( 'footer_font_size' ), '%spx' );
		$footer_widgets_font_size = apollo13framework_make_css_rule( 'font-size', $apollo13framework_a13->get_option( 'footer_widgets_font_size' ), '%spx' );
		$footer_font_color        = apollo13framework_make_css_rule( 'color', $apollo13framework_a13->get_option_color_rgba( 'footer_font_color' ) );
		$footer_link_color        = apollo13framework_make_css_rule( 'color', $apollo13framework_a13->get_option_color_rgba( 'footer_link_color' ) );
		$footer_hover_color       = apollo13framework_make_css_rule( 'color', $apollo13framework_a13->get_option_color_rgba( 'footer_hover_color' ) );
		$footer_separator_color   = apollo13framework_make_css_rule( 'border-color', $apollo13framework_a13->get_option_color_rgba( 'footer_separator_color' ) );

		$css .= "
/* ==================
   FOOTER
   ==================*/
#footer{
    $footer_bg_color
    $footer_font_size
}
#footer .widget,
#footer .widget .search-form input[type=\"search\"]{
    $footer_widgets_font_size
}
.foot-items{
    $footer_lower_bg_color
}
.footer-separator .foot-items .foot-content{
    $footer_separator_color
}
.foot-items{
    $footer_font_color
}
.foot-items .foot-text a{
    $footer_link_color
}
.foot-items .foot-text a:hover{
    $footer_hover_color
}";
	}

	return $css;
}

function apollo13framework_footer_partial_css($response) {
	return apollo13framework_prepare_partial_css($response, 'footer_unravel_effect', 'apollo13framework_footer_css');
}
add_filter( 'customize_render_partials_response', 'apollo13framework_footer_partial_css' );


if(!function_exists('apollo13framework_footer_widgets')) {
	/**
	 * Prints out HTML for footer widgets in columns
	 */
	function apollo13framework_footer_widgets() {
		global $apollo13framework_a13;

		if( $apollo13framework_a13->get_option( 'header_type' ) === 'vertical' ){
			/* header widget areas */
	        $custom_sidebar = $default_sidebar = false;
	        $custom_sidebar_name = apollo13framework_theme_header_custom_sidebar_name();

	        if ( is_active_sidebar( 'header-widget-area' ) ) {
				$default_sidebar = true;
	        }

	        if(strlen($custom_sidebar_name) && $custom_sidebar_name !== 'off' && is_active_sidebar( $custom_sidebar_name )) {
	            $custom_sidebar = true;
	        }

			if($custom_sidebar || $default_sidebar) {
	            echo '<div class="header-widgets">';

				if($default_sidebar) {
	                dynamic_sidebar( 'header-widget-area' );
				}
				if($custom_sidebar) {
	                dynamic_sidebar( $custom_sidebar_name );
				}

	            echo '</div>';
			}
		}
		else{
			//is there any widgets
			if ( is_active_sidebar( 'footer-widget-area' ) ) {
				//class for widgets
				$_class = '';
				$columns = (int)$apollo13framework_a13->get_option( 'footer_widgets_columns' );
				if ( $columns === 1 ) {
					$_class = ' one-col';
				} elseif ( $columns === 2 ) {
					$_class = ' two-col';
				} elseif ( $columns === 3 ) {
					$_class = ' three-col';
				} elseif ( $columns === 4 ) {
					$_class = ' four-col';
				} elseif ( $columns === 5 ) {
					$_class = ' five-col';
				}

				//color of sidebar
				$_class .= ' '.$apollo13framework_a13->get_option( 'footer_widgets_color' );

				echo '<div class="foot-widgets' . esc_attr( $_class ) . '">';
	                echo '<div class="foot-content clearfix">';

				dynamic_sidebar( 'footer-widget-area' );

				echo '</div>
	                </div>';
			}
		}


	}
}


if(!function_exists('apollo13framework_footer_items')) {
	/**
	 * Prints out HTML for footer items
	 */
	function apollo13framework_footer_items() {
		global $apollo13framework_a13; ?>
			<div class="foot-items">
				<div class="foot-content clearfix">
	                <?php
	                footer_socials();

	                //footer text
	                $ft = $apollo13framework_a13->get_option( 'footer_text' );
	                $privacy = $apollo13framework_a13->get_option( 'footer_privacy_link' ) === 'on';
	                if(!empty($ft)){
		                echo '<div class="foot-text">';
		                echo nl2br( wp_kses_data( balanceTags( $ft, true ) ) );
		                if ( $privacy && function_exists( 'the_privacy_policy_link' ) ) {
			                the_privacy_policy_link( '<div>', '</div>');
		                }
		                echo '</div>';
	                }
	                ?>
				</div>
			</div>
		<?php
	}
}


function footer_socials(){
	global $apollo13framework_a13;
	if( $apollo13framework_a13->get_option( 'footer_socials' ) === 'on' ){
		echo '<div class="f-links">';
		echo apollo13framework_social_icons($apollo13framework_a13->get_option( 'footer_socials_color' ), $apollo13framework_a13->get_option( 'footer_socials_color_hover' ));
		echo '</div>';
	}
}



if(!function_exists('apollo13framework_footer_for_site_modules')) {
	/**
	 * Prints out HTML for elements needed to be printed after whole site
	 */
	function apollo13framework_footer_for_site_modules() {
		global $apollo13framework_a13;

		$to_top_icon = $apollo13framework_a13->get_option( 'to_top_icon' );
		$to_top_icon = 'fa-'.(strlen($to_top_icon)? $to_top_icon : 'chevron-up');

		//top top and overlay for various things ?>
		<a href="#top" id="to-top" class="to-top fa <?php echo esc_attr($to_top_icon); ?>"></a>
		<div id="content-overlay" class="to-move"></div>
		<?php
	}
}



if(!function_exists('apollo13framework_footer_for_header_modules')) {
	/**
	 * Prints out HTML for elements used in header
	 */
	function apollo13framework_footer_for_header_modules() {
		global $apollo13framework_a13;

		//menu overlay
		apollo13framework_menu_overlay();

		//hidden sidebar
		if( is_active_sidebar( 'side-widget-area' ) ){
			$hidden_sb_classes = ' '.$apollo13framework_a13->get_option( 'hidden_sidebar_widgets_color' );
			$hidden_sb_classes .= ' at-'.$apollo13framework_a13->get_option( 'hidden_sidebar_side' );
		?>
		<nav id="side-menu" class="side-widget-menu<?php echo esc_attr($hidden_sb_classes) ?>">
			<div class="scroll-wrap">
				<?php dynamic_sidebar( 'side-widget-area' ); ?>
			</div>
			<span class="a13icon-cross close-sidebar"></span>
		</nav>
		<?php
		}


		//basket sidebar
		if( apollo13framework_is_woocommerce_activated() && is_active_sidebar( 'basket-widget-area' ) ){
		?>
		<nav id="basket-menu" class="basket-sidebar">
			<?php dynamic_sidebar( 'basket-widget-area' ); ?>
			<span class="a13icon-cross close-sidebar"></span>
		</nav>
		<?php
		}

		//top closable message
		apollo13framework_cookie_message();
	}
}



function apollo13framework_main_demo_flyout(){
	global $apollo13framework_a13;

	$hs = apollo13framework_is_home_server();
	$enabled = $apollo13framework_a13->get_option( 'flyout_box' ) === 'on';

	if( $hs || $enabled ) {

		?>
		<div class="<?php echo esc_attr( $hs ? 'demo-flyout ' : '' ); ?>flyout-box" id="a13-flyout-box">
			<div class="links">
				<?php
				//home server
				$file_path = A13FRAMEWORK_IMPORTER_TMP_DIR.'/'.A13FRAMEWORK_TPL_SLUG.'_fly-out';
				if ( $hs && file_exists( $file_path ) ) {
					/** @noinspection PhpIncludeInspection */
					include( $file_path );
				}
				//enabled in option
				else {
					echo do_shortcode($apollo13framework_a13->get_option( 'flyout_box_content' ));
				}
				?>
			</div>
			<span class="drag-out fa fa-<?php echo esc_attr($apollo13framework_a13->get_option( 'flyout_box_icon', 'info-circle' )); ?>" title="<?php esc_attr_e( 'Secret useful links', 'rife-free' ); ?>"></span>
		</div>
		<?php
	}
}
