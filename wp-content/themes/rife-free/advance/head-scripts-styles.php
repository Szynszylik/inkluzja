<?php
/* Functions for registering and printing scripts & styles */

add_action( 'wp_enqueue_scripts', 'apollo13framework_theme_scripts', 26 ); //put it later then woocommerce
if(!function_exists('apollo13framework_theme_scripts')){
	/**
	 * Registering frontend theme scripts
	 */
	function apollo13framework_theme_scripts(){
        global $apollo13framework_a13;

        $page_type = apollo13framework_what_page_type_is_it();
        $album     = $page_type['album'];
        $work      = $page_type['work'];

        //Modernizr custom build
        wp_enqueue_script( 'apollo13framework-modernizr-custom', get_theme_file_uri( 'js/modernizr-custom.min.js' ), false, '2.8.3', false);

        /* We add some JavaScript to pages with the comment form
          * to support sites with threaded comments (when in use).
          */
        if ( is_singular() && get_option( 'thread_comments' ) ){
            wp_enqueue_script( 'comment-reply' );
        }

        $script_depends = array( 'apollo13framework-plugins' );

        //fitvids script
        wp_register_script('jquery-fitvids', get_theme_file_uri( 'js/jquery.fitvids.min.js' ), array('jquery'), '1.1', true);
        $script_depends[] = 'jquery-fitvids';

        //fittext script
        wp_register_script('jquery-fittext', get_theme_file_uri( 'js/jquery.fittext.min.js' ), array('jquery'), '1.2', true);
        $script_depends[] = 'jquery-fittext';

        //waypoints script - used in writing effect for example
        wp_register_script('noframework-waypoints', get_theme_file_uri( 'js/noframework.waypoints.js' ), false, '4.0.1', true);

        //plugins used in theme (cheat sheet)
        wp_register_script('apollo13framework-plugins', get_theme_file_uri( 'js/helpers.min.js' ),
            false, //depends
            A13FRAMEWORK_THEME_VER, //version number
            true //in footer
        );


        //Animation library
        wp_register_script( 'tweenmax', get_theme_file_uri( 'js/TweenMax.min.js' ), array('jquery'), '1.20.4', true);
		$script_depends[] = 'tweenmax';

        //APOLLO Slider
        wp_register_script( 'apollo13framework-slider', get_theme_file_uri( 'js/a13-slider.js' ), array('jquery', 'tweenmax'), A13FRAMEWORK_THEME_VER, true);

        //flickity
        wp_register_script( 'flickity', get_theme_file_uri( 'js/flickity.pkgd.min.js' ), array('jquery'), '2.0.9', true);

		//slidesjs
		wp_register_script( 'jquery-slides', get_theme_file_uri( 'js/jquery.slides.min.js' ), array('jquery'), '3.0.4', true);
        $script_depends[] = 'jquery-slides';

		//sticky kit
		wp_register_script( 'jquery-sticky-kit', get_theme_file_uri( 'js/jquery.sticky-kit.min.js' ), array('jquery'), '1.1.2', true);
        $script_depends[] = 'jquery-sticky-kit';

        //mouse scroll support
		wp_register_script( 'jquery-mousewheel', get_theme_file_uri( 'js/jquery.mousewheel.min.js' ), array('jquery'), '3.1.13', true);
        $script_depends[] = 'jquery-mousewheel';

        //mouse scroll support
		wp_register_script( 'jquery-typed', get_theme_file_uri( 'js/typed.min.js' ), array('jquery'), '1.1.4', true);
        $script_depends[] = 'jquery-typed';

		//counter - for counter shortcode
		wp_register_script( 'jquery-countto', get_theme_file_uri( 'js/jquery.countTo.js' ), array('jquery'), '1.0', true);
        //@deprecated since 1.8.0, will be removed in future. Used by Apollo13 Framework Extensions plugin
//		wp_register_script( 'jquery.countTo', get_theme_file_uri( 'js/jquery.countTo.js' ), array('jquery'), '1.0', true);

		//countdown
		wp_register_script( 'jquery-countdown', get_theme_file_uri( 'js/jquery.countdown.min.js' ), array('jquery'), '2.2.0', true);
        //@deprecated since 1.8.0, will be removed in future. Used by Apollo13 Framework Extensions plugin
		wp_register_script( 'jquery.countdown', get_theme_file_uri( 'js/jquery.countdown.min.js' ), array('jquery'), '2.2.0', true);

		//lightGallery lightbox
		wp_register_script( 'jquery-lightgallery', get_theme_file_uri( 'js/light-gallery/js/lightgallery-all.min.js' ), array('jquery'), '1.6.9', true);

	    //modified isotope for bricks layouts
	    wp_register_script( 'apollo13framework-isotope', get_theme_file_uri( 'js/isotope.pkgd.min.js' ), array('jquery'), '3.0.6', true);
	    $script_depends[] = 'apollo13framework-isotope';

		//slider needed
        $how_to_open = $apollo13framework_a13->get_option( 'works_list_work_how_to_open' );
        if (
            //album or work slider
            ( ( $album || $work ) && $apollo13framework_a13->get_meta( '_theme' ) === 'slider' )
            //we need slider script in case of VC post grid
            || ( defined( 'WPB_VC_VERSION' ) && $how_to_open === 'in-lightbox' )
        ) {
            $script_depends[] = 'apollo13framework-slider';
        }

        //flickity needed
        $flickity_themes = array('scroller', 'scroller-parallax');
        if( ( $album || $work ) && in_array($apollo13framework_a13->get_meta( '_theme' ), $flickity_themes)){
            $script_depends[] = 'flickity';
        }

		//A13 STICKY ONE PAGE
		wp_register_script( 'jquery-slimscroll', get_theme_file_uri( 'js/jquery.slimscroll.min.js' ), array('jquery','apollo13framework-plugins'), '1.3.2', true);
		wp_register_script( 'fullPage', get_theme_file_uri( 'js/jquery.fullPage.min.js' ), array('jquery','jquery-slimscroll'), '2.7.6', true);
		if( $apollo13framework_a13->get_meta( '_content_sticky_one_page' ) === 'on' ){
			$script_depends[] = 'fullPage';
		}

		//Image carousel
		wp_register_script( 'jquery-owl-carousel', get_theme_file_uri( 'js/owl.carousel.min.js' ), array('jquery','apollo13framework-plugins'), '2.2.1', true);

        //bricks videos
	    if( ($album || $work) && $apollo13framework_a13->get_meta('_theme') === 'bricks'){
		    $script_depends[] = 'mediaelement';
	    }

	    //lightbox
	    $lightbox = $apollo13framework_a13->get_option( 'apollo_lightbox' );
	    if( $lightbox === 'lightGallery' ){
		    $script_depends[] = 'jquery-lightgallery';
	    }

        //options passed to JS
        $apollo_params = apollo13framework_js_parameters();
        //hand written scripts for theme
        wp_enqueue_script('apollo13framework-scripts', get_theme_file_uri( 'js/script.js' ), $script_depends, A13FRAMEWORK_THEME_VER, true );
        //transfer options
        wp_localize_script( 'apollo13framework-plugins', 'ApolloParams', $apollo_params );
    }
}

if(!function_exists('apollo13framework_js_parameters')){
	/**
	 * Special parameters passed to JavaScript vie Global variable
	 * @return array of all parameters
	 */
	function apollo13framework_js_parameters(){
        global $apollo13framework_a13;

        $allow_mobile_menu = $apollo13framework_a13->get_option( 'header_type' ) === 'vertical'
                             || ($apollo13framework_a13->get_option( 'header_main_menu' ) === 'on' && $apollo13framework_a13->get_option( 'menu_allow_mobile_menu' ) !== 'off');

        $params = array(
            /* GLOBAL OPTIONS */
            'ajaxurl'                   => admin_url('admin-ajax.php'),
            'site_url'                  => site_url().'/',
            'defimgurl'                 => get_theme_file_uri( 'images/holders/photo.png'),
            'options_name'              => A13FRAMEWORK_OPTIONS_NAME,

	        /* MISC */
            'load_more'                 => esc_html__( 'Load more', 'rife-free' ),
            'loading_items'             => esc_html__( 'Loading next items', 'rife-free' ),
            'anchors_in_bar'            => $apollo13framework_a13->get_option( 'anchors_in_bar' ) === 'on',
            'writing_effect_mobile'     => $apollo13framework_a13->get_option( 'writing_effect_mobile' ) === 'on',
            'writing_effect_speed'      => $apollo13framework_a13->get_option( 'writing_effect_speed', 10 ),

            /* HORIZONTAL HEADER */
            'hide_content_under_header' => apollo13framework_content_under_header(),
            'default_header_variant'    => apollo13framework_horizontal_header_color_variant(),
            'header_sticky_top_bar'     => $apollo13framework_a13->get_option( 'header_sticky_top_bar' ) === 'on',
            'header_color_variants'     => $apollo13framework_a13->get_option( 'header_color_variants' ),
            'show_header_at'            => $apollo13framework_a13->get_meta('_horizontal_header_show_header_at' ),

            /* HORIZONTAL HEADER VARIANTS */
            'header_normal_social_colors' => $apollo13framework_a13->get_option( 'header_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'header_socials_color_hover' ).'_hover'.
                                            '|'.$apollo13framework_a13->get_option( 'top_bar_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'top_bar_socials_color_hover' ).'_hover',

            'header_light_social_colors' => $apollo13framework_a13->get_option( 'header_light_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'header_light_socials_color_hover' ).'_hover'.
                                            '|'.$apollo13framework_a13->get_option( 'header_light_top_bar_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'header_light_top_bar_socials_color_hover' ).'_hover',

            'header_dark_social_colors' => $apollo13framework_a13->get_option( 'header_dark_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'header_dark_socials_color_hover' ).'_hover'.
                                            '|'.$apollo13framework_a13->get_option( 'header_dark_top_bar_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'header_dark_top_bar_socials_color_hover' ).'_hover',

            'header_sticky_social_colors' => $apollo13framework_a13->get_option( 'header_sticky_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'header_sticky_socials_color_hover' ).'_hover'.
                                            '|'.$apollo13framework_a13->get_option( 'header_sticky_top_bar_socials_color' ).
                                            '|'.$apollo13framework_a13->get_option( 'header_sticky_top_bar_socials_color_hover' ).'_hover',
            /* MENU */
            'close_mobile_menu_on_click' => $apollo13framework_a13->get_option( 'menu_close_mobile_menu_on_click' ) === 'on',
            'menu_overlay_on_click'      => $apollo13framework_a13->get_option( 'header_menu_overlay_on_click', 'off' ) === 'on',
            'allow_mobile_menu'          => $allow_mobile_menu,
            'submenu_opener'             => 'fa-' . $apollo13framework_a13->get_option( 'submenu_opener' ),
            'submenu_closer'             => 'fa-' . $apollo13framework_a13->get_option( 'submenu_closer' ),
            'submenu_third_lvl_opener'   => 'fa-' . $apollo13framework_a13->get_option( 'submenu_third_lvl_opener' ),
            'submenu_third_lvl_closer'   => 'fa-' . $apollo13framework_a13->get_option( 'submenu_third_lvl_closer' ),

            /* BLOG */
            'posts_brick_margin'         => $apollo13framework_a13->get_option( 'blog_brick_margin' ),
            'posts_layout_mode'          => $apollo13framework_a13->get_option( 'blog_layout_mode' ),

            /* SHOP */
            'products_brick_margin'      => $apollo13framework_a13->get_option( 'shop_brick_margin' ),
            'products_layout_mode'       => $apollo13framework_a13->get_option( 'shop_products_layout_mode' ),

            /* ALBUMS */
            'albums_list_brick_margin'   => $apollo13framework_a13->get_option( 'albums_list_brick_margin' ),
            'albums_list_layout_mode'    => $apollo13framework_a13->get_option( 'albums_list_layout_mode' ),
            'album_bricks_thumb_video'   => $apollo13framework_a13->get_option( 'album_bricks_thumb_video' ) === 'on',

            /* WORKS */
            'works_list_brick_margin'    => $apollo13framework_a13->get_option( 'works_list_brick_margin' ),
            'works_list_layout_mode'     => $apollo13framework_a13->get_option( 'works_list_layout_mode' ),
            'work_bricks_thumb_video'    => $apollo13framework_a13->get_option( 'work_bricks_thumb_video' ) === 'on',

            /* PEOPLE */
            'people_list_brick_margin'    => '10',
            'people_list_layout_mode'     => 'fitRows',

            /* lightGallery lightbox */
            'lg_lightbox_share'          => $apollo13framework_a13->get_option( 'lg_lightbox_share', 'on' ) === 'on',
            'lg_lightbox_controls'       => $apollo13framework_a13->get_option( 'lg_lightbox_controls', 'on' ) === 'on',
            'lg_lightbox_download'       => $apollo13framework_a13->get_option( 'lg_lightbox_download', 'off' ) === 'on',
            'lg_lightbox_counter'        => $apollo13framework_a13->get_option( 'lg_lightbox_counter', 'on' ) === 'on',
            'lg_lightbox_thumbnail'      => $apollo13framework_a13->get_option( 'lg_lightbox_thumbnail', 'on' ) === 'on',
            'lg_lightbox_show_thumbs'    => $apollo13framework_a13->get_option( 'lg_lightbox_show_thumbs', 'off' ) === 'on',
            'lg_lightbox_autoplay'       => $apollo13framework_a13->get_option( 'lg_lightbox_autoplay', 'on' ) === 'on',
            'lg_lightbox_autoplay_open'  => $apollo13framework_a13->get_option( 'lg_lightbox_autoplay_open', 'off' ) === 'on',
            'lg_lightbox_progressbar'    => $apollo13framework_a13->get_option( 'lg_lightbox_progressbar', 'on' ) === 'on',
            'lg_lightbox_full_screen'    => $apollo13framework_a13->get_option( 'lg_lightbox_full_screen', 'on' ) === 'on',
            'lg_lightbox_zoom'           => $apollo13framework_a13->get_option( 'lg_lightbox_zoom', 'on' ) === 'on',
            'lg_lightbox_mode'           => $apollo13framework_a13->get_option( 'lg_lightbox_mode', 'lg-slide' ),
            'lg_lightbox_speed'          => $apollo13framework_a13->get_option( 'lg_lightbox_speed', '600' ),
            'lg_lightbox_preload'        => $apollo13framework_a13->get_option( 'lg_lightbox_preload', '1' ),
            'lg_lightbox_hide_delay'     => $apollo13framework_a13->get_option( 'lg_lightbox_hide_delay', '2000' ),
            'lg_lightbox_autoplay_pause' => $apollo13framework_a13->get_option( 'lg_lightbox_autoplay_pause', '5000' ),
        );

        //add only if proofing is enabled
        $proofing = (int)( $apollo13framework_a13->get_meta( '_proofing' ) === 'on' );
        if($proofing){
            $params['proofing_manual_ids']          = $apollo13framework_a13->get_meta('_proofing_ids' ) === 'manual';
            $params['proofing_add_comment']         = esc_html__( 'Add comment', 'rife-free' );
            $params['proofing_comment_placeholder'] = esc_html__( 'Write your comment here&hellip;', 'rife-free' );
            $params['proofing_mark_item']           = esc_html__( 'Mark item', 'rife-free' );
            $params['proofing_uncheck_item']        = esc_html__( 'Uncheck item', 'rife-free' );
            $params['album_id']                     = get_the_ID();
            $params['proofing_nonce']               = wp_create_nonce( "proofing_ajax" );
        }

        return $params;
    }
}


add_action( 'wp_head', 'apollo13framework_get_webfonts' );
if(!function_exists('apollo13framework_get_webfonts')) {
    function apollo13framework_get_webfonts() {
        global $apollo13framework_a13;

        $standard_fonts = array_keys( $apollo13framework_a13->get_standard_fonts_list() );
        $fonts_js       = array( 'families' => array() );

        $options_fonts = array(
            $apollo13framework_a13->get_option( 'nav_menu_fonts' ),
            $apollo13framework_a13->get_option( 'titles_fonts' ),
            $apollo13framework_a13->get_option( 'normal_fonts' ),
            //default to titles fonts as it was in previous versions
            $apollo13framework_a13->get_option( 'logo_fonts', $apollo13framework_a13->get_option( 'titles_fonts' ) ),
        );

        foreach ( $options_fonts as $font ) {
            //if not standard font create font definition for request
            if ( ! in_array( $font['font-family'], $standard_fonts ) ) {
                //start with font family
                $font_definition = $font['font-family'];


                //add variants
                $variants = false;
                //legacy setting for variants
                if ( isset( $font['font-multi-style'] ) && strlen( $font['font-multi-style'] ) ) {
                    $variants = json_decode( $font['font-multi-style'], true );
                } //new setting for variants
                elseif ( isset( $font['variants'] ) && is_array( $font['variants'] ) ) {
                    $variants = $font['variants'];
                }
                //we got variants finally
                if ( $variants !== false ) {
                    $font_definition .= ':';
                    foreach ( $variants as $index => $variant ) {
                        if ( $index > 0 ) {
                            $font_definition .= ',';
                        }
                        $font_definition .= $variant;
                    }
                }

                //add subsets
                if( isset( $font['subsets'] ) ){
                    //convert subsets to array if legacy setting
                    $font['subsets'] = is_array( $font['subsets'] ) ? $font['subsets'] : array( $font['subsets'] );
                    if ( sizeof( $font['subsets'] ) ) {
                        $font_definition .= ':';
                        foreach ( $font['subsets'] as $index => $subset ) {
                            if ( $index > 0 ) {
                                $font_definition .= ',';
                            }
                            $font_definition .= $subset;
                        }
                    }
                }

                array_push( $fonts_js['families'], $font_definition );
            }
        }

        if ( sizeof( $fonts_js['families'] ) ):
            ?>

            <script>
                // <![CDATA[
                WebFontConfig = {
                    google: <?php echo wp_json_encode( $fonts_js ); ?>,
                    active: function () {
                        //tell listeners that fonts are loaded
                        if (window.jQuery) {
                            jQuery(document.body).trigger('webfontsloaded');
                        }
                    }
                };
                (function (d) {
                    var wf = d.createElement('script'), s = d.scripts[0];
                    wf.src = '<?php echo esc_url( get_theme_file_uri( 'js/webfontloader.js' ) ); ?>';
                    wf.async = 'true';
                    s.parentNode.insertBefore(wf, s);
                })(document);
                // ]]>
            </script>

            <?php
        endif;
    }
}


add_action( 'wp_enqueue_scripts', 'apollo13framework_theme_styles', 26 ); //put it later then woocommerce
if(!function_exists('apollo13framework_theme_styles')){
	/**
	 * Adds CSS files to theme
	 */
	function apollo13framework_theme_styles(){
        global $apollo13framework_a13;

        $user_css_depends = array('a13-main-style');


	    //woocommerce
	    if(apollo13framework_is_woocommerce_activated()){
		    array_push($user_css_depends,'apollo13framework-woocommerce');
		    wp_register_style( 'apollo13framework-woocommerce', get_theme_file_uri( 'css/woocommerce.css' ), array('a13-main-style'), A13FRAMEWORK_THEME_VER);
		    wp_style_add_data( 'apollo13framework-woocommerce', 'rtl', get_theme_file_uri( 'css/woocommerce-rtl.css') );
	    }

	    wp_register_style( 'a13-font-awesome', get_theme_file_uri( 'css/font-awesome.min.css' ), false, '4.7.0');
	    wp_register_style( 'a13-icomoon', get_theme_file_uri( 'css/icomoon.css' ), false, A13FRAMEWORK_THEME_VER);
        wp_register_style( 'a13-main-style', A13FRAMEWORK_TPL_URI . '/style.css', array('a13-font-awesome', 'a13-icomoon'), A13FRAMEWORK_THEME_VER);

		//Image carousel
		wp_register_style( 'jquery-owl-carousel', get_theme_file_uri( 'css/owl.carousel.min.css' ), array(), A13FRAMEWORK_THEME_VER);

        //lightGallery lightbox
	    wp_register_style( 'jquery-lightgallery-transitions', get_theme_file_uri( 'js/light-gallery/css/lg-transitions.min.css' ), false, '1.6.9' );
	    $lg_default_transition = $apollo13framework_a13->get_option( 'lg_lightbox_mode' ) === 'lg-slide';
	    wp_register_style( 'jquery-lightgallery', get_theme_file_uri( 'js/light-gallery/css/lightgallery.min.css' ), ($lg_default_transition ? false : array('jquery-lightgallery-transitions')), '1.6.9' );


	    //lightbox
	    $lightbox = $apollo13framework_a13->get_option( 'apollo_lightbox' );
	    if( $lightbox === 'lightGallery' ){
		    wp_enqueue_style('jquery-lightgallery');
	    }

        $user_css_file = $apollo13framework_a13->user_css_name();
        $last_modified = file_exists( $user_css_file )? filemtime( $apollo13framework_a13->user_css_name() ) : '0';
        wp_register_style('a13-user-css', $apollo13framework_a13->user_css_name(true), $user_css_depends, A13FRAMEWORK_THEME_VER.'_'.$last_modified);

        //in customizer we embed user.css file inline
        if(is_customize_preview()){
            wp_enqueue_style('a13-main-style');
            if(apollo13framework_is_woocommerce_activated()){
                wp_enqueue_style('apollo13framework-woocommerce');
            }
        }
        else{
            wp_enqueue_style('a13-user-css');
        }


        if( class_exists( 'YITH_WCWL' ) ){
            //remove conflicting styles from wishlist plugin
            global $wp_styles;

            $wp_styles->registered['yith-wcwl-font-awesome']->src = get_theme_file_uri( 'css/font-awesome.min.css' );
            $wp_styles->registered['yith-wcwl-font-awesome']->ver = '4.7.0';
        }
    }
}

/**
 * Prints user.css plus its inline styles in footer as inline styles
 */
function apollo13framework_customizer_preview_css(){
    global $wp_styles;
    //CSS
    /** @noinspection PhpIncludeInspection */
    require_once(get_theme_file_path( 'advance/user-css.php'));
    $css = apollo13framework_get_user_css(false);
    //main setting
    echo '<style media="all" id="user-css-inlined">'.$css.'</style>';
    //only custom CSS - so we can update it live
    echo '<style media="all" id="user-custom-css">'.apollo13framework_user_custom_css().'</style>';

    //print inline styles
    $wp_styles->print_inline_style('a13-user-css');
}

//add user css for customizer
if ( is_customize_preview() ){
    add_action( 'wp_footer', 'apollo13framework_customizer_preview_css', 21);
}

//for now deactivated as I am not sure it is needed when AJAX is removed
add_action( 'vc_base_register_front_js', 'apollo13framework_remove_vc_conflicts' );
if(!function_exists('apollo13framework_remove_vc_conflicts')){
	/**
	 * remove some conflicts with Visual Composer
	 */
	function apollo13framework_remove_vc_conflicts(){
		if(defined( 'WPB_VC_VERSION' )){
            /* REMOVE ISOTOPE CONFLICT */
            global $wp_scripts;
            $wp_scripts->registered[ 'isotope' ]->src = get_theme_file_uri( 'js/isotope.pkgd.min.js' );
            $wp_scripts->registered[ 'isotope' ]->ver = '3.0.6';
            $wp_scripts->registered[ 'isotope' ]->deps = array('jquery');
		}
	}
}