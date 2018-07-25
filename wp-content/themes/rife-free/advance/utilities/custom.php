<?php
/**
 * Random functions that don't fit in any other category
 */


if ( ! function_exists( 'apollo13framework_current_url' ) ) {
	/**
	 * For getting URL of current page
	 *
	 * @return string   URL
	 */
	function apollo13framework_current_url() {
		global $wp;

		//no permalinks
		if ( $wp->request === null ) {
			$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		} else {
			$current_url = trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) );
		}

		return $current_url;
	}
}


if ( ! function_exists( 'apollo13framework_custom_permalink' ) ) {
	/**
	 * Filter that change default permalinks for posts and custom post types
	 *
	 * Thanks to this function like get_permalink will get link to custom link if one is set in post/work/etc.
	 *
	 * @param string $url  The post URL
	 * @param object $post The post object
	 *
	 * @return string URL
	 * @internal param bool $leave_name Whether to keep the post name or page name
	 *
	 */
	function apollo13framework_custom_permalink( $url, $post ) {
		$custom_link_types = array( 'post', A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM, A13FRAMEWORK_CUSTOM_POST_TYPE_WORK );
		if ( in_array( $post->post_type, $custom_link_types ) ) {
			$custom_url = get_post_meta( $post->ID, '_alt_link', true );
			//use custom link if available
			if ( strlen( $custom_url ) ) {
				return $custom_url;
			}

			return $url;
		}

		return $url;
	}
}
add_filter( 'post_link', 'apollo13framework_custom_permalink', 10, 3 );
add_filter( 'post_type_link', 'apollo13framework_custom_permalink', 10, 3 );


if ( ! function_exists( 'apollo13framework_has_active_sidebar' ) ) {
	/**
	 * Checks if current page has active sidebar
	 * returns false if there is no active sidebar,
	 * if there is active sidebar it returns its name
	 *
	 * @return bool|string
	 */
	function apollo13framework_has_active_sidebar() {
		global $apollo13framework_a13;
		$test              = '';
		$page_type         = apollo13framework_what_page_type_is_it();
		$shop_with_sidebar = apollo13framework_is_woocommerce_sidebar_page();


		if ( $shop_with_sidebar ) {
			$test = 'shop-widget-area';
		} elseif ( apollo13framework_is_woocommerce() ) {
			return false;
		} elseif ( $page_type['blog_type'] ) {
			$test = 'blog-widget-area';
		} elseif ( $page_type['album'] ) {
		} elseif ( $page_type['work'] ) {
		} elseif ( $page_type['post'] ) {
			$test = 'post-widget-area';
		} elseif ( $page_type['page'] ) {
			$test           = 'page-widget-area';
			$meta_id        = get_the_ID();
			$custom_sidebar = $apollo13framework_a13->get_meta( '_sidebar_to_show', $meta_id );
			if ( strlen( $custom_sidebar ) && $custom_sidebar !== 'default' ) {
				$test = $custom_sidebar;
			}

			//if has children nav and it is activated then sidebar is active
			$sidebar_meta = $apollo13framework_a13->get_meta( '_widget_area', $meta_id );
			if ( strrchr( $sidebar_meta, 'nav' ) && apollo13framework_page_menu( true ) ) {
				return $test;
			}
		}

		if ( is_active_sidebar( $test ) ) {
			return $test;
		} else {
			return false;
		}
	}
}


if ( ! function_exists( 'apollo13framework_body_classes' ) ) {
	/**
	 * Add classes for <body> element
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function apollo13framework_body_classes( $classes ) {
		global $apollo13framework_a13;

		$page_type = apollo13framework_what_page_type_is_it();

		//hidden sidebar
		if ( is_active_sidebar( 'side-widget-area' ) ) {
			$side   = $apollo13framework_a13->get_option( 'hidden_sidebar_side' );
			$effect = (int) $apollo13framework_a13->get_option( 'hidden_sidebar_effect' );
			if ( $side === 'right' ) {
				$effect += 6;//right side effects have number bigger by 6
			}

			$classes[] = 'side-menu-eff-' . $effect;
		}

		//widgets top margin
		if($apollo13framework_a13->get_option( 'widgets_top_margin' ) === 'off'){
			$classes[] = 'widgets_margin_top_off';
		}

		//header classes
		$header_type = $apollo13framework_a13->get_option( 'header_type' );
		//header type
		$classes[] = 'header-'.$header_type;

		//header side(vertical only)
		if($header_type === 'vertical'){
			if(is_rtl()){
				$classes[] = 'header-side-'.$apollo13framework_a13->get_option( 'header_side_rtl' );
			}
			else{
				$classes[] = 'header-side-'.$apollo13framework_a13->get_option( 'header_side' );
			}
		}

		//site layout
		$layout_type = $apollo13framework_a13->get_option( 'layout_type' );
		$classes[] = 'site-layout-'.$layout_type;
		if($layout_type === 'bordered'){
			$borders = array( 'top', 'left', 'bottom', 'right' );
			$borders_on = $apollo13framework_a13->get_option( 'theme_borders' );

			if(is_array($borders_on)){
				foreach($borders as $border){
					if(!in_array($border, $borders_on)){
						$classes[] = 'no-border-'.$border;
					}
				}
			}
		}

		//sticky one page
		if( $apollo13framework_a13->get_meta( '_content_sticky_one_page' ) === 'on'){
			$classes[] = 'a13-body-sticky-one-page';
		}

		if ( $page_type['album'] ) {
			$theme = $apollo13framework_a13->get_meta('_theme');
			$theme = $theme === 'scroller-parallax' ? 'scroller' : $theme;
			$classes[] = 'single-album';
			$classes[] = 'single-album-'.$theme;
		}

		if ( $page_type['albums_list'] ) {
			$classes[] = 'albums-list-page';
			$classes[] = 'cpt-list-page';
		}

		if ( $page_type['work'] ) {
			$classes[] = 'single-work';
			$classes[] = 'single-work-'.$apollo13framework_a13->get_meta('_theme');
		}

		if ( $page_type['works_list'] ) {
			$classes[] = 'works-list-page';
			$classes[] = 'cpt-list-page';
		}

		//page with posts list
		if ( $page_type['blog_type'] && ! defined( 'A13FRAMEWORK_NO_RESULTS' ) ) {
			$classes[] = 'posts-list';
		}

		//cart and others not sidebar/title pages of woocommerce
		if ( $page_type['shop'] && ! apollo13framework_is_woocommerce_sidebar_page() ) {
			$classes[] = 'woocommerce-no-major-page';
		}

		//add special class for pages with products list
		if ( apollo13framework_is_woocommerce_products_list_page() ) {
			$classes[] = 'products-list';
		}

		//password protected
		if ( defined( 'A13FRAMEWORK_PASSWORD_PROTECTED' ) ) {
			$classes[] = 'password-protected';
		}

		//custom password page
		if( defined( 'A13FRAMEWORK_CUSTOM_PASSWORD_PROTECTED' ) ){
			$classes[] = 'custom-password-page';
			$classes[] = 'page';
		}

		if( is_archive() && !have_posts() ){
			$classes[] = 'search-no-results';
		}

		if( $page_type['404'] ){
			//custom 404 page
			if( $apollo13framework_a13->get_option( 'page_404_template_type' ) === 'custom' ){
				$classes[] = 'custom404';
				$classes[] = 'page';
			}
			else{
				$classes[] = 'default404';
			}

		}


		return $classes;
	}
}
add_filter( 'body_class', 'apollo13framework_body_classes' );


if ( ! function_exists( 'apollo13framework_get_mid_classes' ) ) {
	/**
	 * Get classes for mid element, depending on context of many things like:
	 * -sidebar availability
	 * -sidebar side
	 * -layout of current page
	 * -type of layout of current page
	 *
	 * @return string   classes of #mid
	 */
	function apollo13framework_get_mid_classes() {
		global $apollo13framework_a13;

		//mid classes for type of layout align and widget area display(on/off)
		$mid_classes = array();

		$page_type  = apollo13framework_what_page_type_is_it();
		$page       = $page_type['page'];
		$post       = $page_type['post'];
		$attachment = $page_type['attachment'];
		$shop       = $page_type['shop'];
		$product    = $page_type['product'];


		/*
		 * content layout classes
		 * */
		$meta_id = get_the_ID();
		//layouts that have space between content and sidebar
		$parted_layouts = array( 'left', 'right', 'left_padding', 'right_padding', 'center' );
		//layouts that sit on one edge of screen
		$edge_layouts = array( 'left', 'right', 'left_padding', 'right_padding' );
		//layouts that have content with fixed width
		$fluid_layouts = array( 'full', 'full_padding' );

		$layout = 'center';

		if ( $attachment ) {
			//nothing, but we add it cause every attachment has also type of post, page or album, depending to which
			//it was attached
		} //albums are Full width
		elseif ( $page_type['albums_list'] || $page_type['album'] ) {
			$layout = 'full';
		}
		//works list is Full width
		elseif ( $page_type['works_list'] ) {
			$layout = 'full';
		}
		//cart and others not sidebar/title pages of woocommerce
		elseif($page_type['shop'] && !apollo13framework_is_woocommerce_sidebar_page()){
			$layout = $apollo13framework_a13->get_option( 'shop_no_major_pages_content_layout' );
		}
		//wish list
		elseif ( class_exists( 'YITH_WCWL' ) && (get_the_ID() === (int)yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ) ) ) {
			$layout = $apollo13framework_a13->get_option( 'shop_no_major_pages_content_layout' );
		} //shop
		elseif ( $page_type['shop'] && ! $page_type['product'] ) {
			$layout = $apollo13framework_a13->get_option( 'shop_content_layout' );

			//only on pages where list of products are displayed
			if ( is_shop() || is_product_taxonomy() ) {
				$mid_classes[] = 'shop-columns-'.$apollo13framework_a13->get_option( 'shop_products_columns' );
			}
		} //product
		elseif ( $page_type['product'] ) {
			$layout = $apollo13framework_a13->get_option( 'product_content_layout' );
		} //page or work
		elseif ( $page_type['work'] || $page ) {
			$layout_option = $apollo13framework_a13->get_meta( '_content_layout', $meta_id );
			$layout        = $layout_option === 'global' ?
				( $page ? $apollo13framework_a13->get_option( 'page_content_layout' ) : $apollo13framework_a13->get_option( 'work_content_layout' ) )
				:
				$layout_option;

			//in content padding
			$top_bottom_padding = $apollo13framework_a13->get_meta('_content_padding');
			if($top_bottom_padding === 'top'){
				$mid_classes[] = 'no-bottom-space';
			}
			elseif($top_bottom_padding === 'bottom'){
				$mid_classes[] = 'no-top-space';
			}
			elseif($top_bottom_padding === 'off'){
				$mid_classes[] = 'no-top-space';
				$mid_classes[] = 'no-bottom-space';
			}

			$side_padding = $apollo13framework_a13->get_meta('_content_side_padding');
			if($side_padding === 'off'){
				$mid_classes[] = 'no-side-space';
			}
		} //single post
		elseif ( $post ) {
			$layout = $apollo13framework_a13->get_option( 'post_content_layout' );
		} //blog type
		elseif ( $page_type['blog_type'] ) {
			$layout = $apollo13framework_a13->get_option( 'blog_content_layout' );

			//in content padding
			$top_bottom_padding = $apollo13framework_a13->get_option( 'blog_content_padding' );
			if($top_bottom_padding === 'top'){
				$mid_classes[] = 'no-bottom-space';
			}
			elseif($top_bottom_padding === 'bottom'){
				$mid_classes[] = 'no-top-padding'; /* padding instead of space to not clash this two different scenarios */
			}
			elseif($top_bottom_padding === 'off'){
				$mid_classes[] = 'no-top-padding';
				$mid_classes[] = 'no-bottom-padding';
			}
		}


		$mid_classes[] = 'layout-' . $layout;
		if ( in_array( $layout, $parted_layouts ) ) {
			$mid_classes[] = 'layout-parted';
		}

		if ( in_array( $layout, $edge_layouts ) ) {
			$mid_classes[] = 'layout-edge';
		}
		else{
			$mid_classes[] = 'layout-no-edge';
		}

		//layouts that sit on edge of screen and have margin
		if ( strpos( $layout, 'padding' ) !== false ) {
			$mid_classes[] = 'layout-padding';
		}

		if ( in_array( $layout, $fluid_layouts ) ) {
			$mid_classes[] = 'layout-fluid';
		}
		else{
			$mid_classes[] = 'layout-fixed';
		}


		/*
		 * sidebar classes
		 * */

		//check if there is active sidebar for current page
		$force_full_width = false;
		if ( $page_type['cpt_list'] || //it is page, so it can gain page sidebar
		     $page_type['cpt'] || //it doesn't have sidebar
		     $attachment ||
		     apollo13framework_has_active_sidebar() === false
		) {
			$force_full_width = true;
		}

		function apollo13framework__inner__set_full_width( &$mid_classes ) {
			define( 'A13FRAMEWORK_NO_SIDEBARS', true ); /* so we don't have to check again in sidebar.php */
			$mid_classes[] = 'no-sidebars';
		}

		function apollo13framework__inner__set_sidebar_class( &$mid_classes, $sidebar ) {
			if ( ( $sidebar == 'off' ) ) {
				apollo13framework__inner__set_full_width( $mid_classes );
			} else {
				$mid_classes[] = 'with-sidebar';
				$mid_classes[] = $sidebar;
			}
		}

		if ( $force_full_width ) {
			apollo13framework__inner__set_full_width( $mid_classes );
		} //shop type
		elseif ( $shop && ! $product ) {
			apollo13framework__inner__set_sidebar_class( $mid_classes, $apollo13framework_a13->get_option( 'shop_sidebar' ) );
		} //product type
		elseif ( $product ) {
			apollo13framework__inner__set_sidebar_class( $mid_classes, $apollo13framework_a13->get_option( 'product_sidebar' ) );
		} //blog type
		elseif ( $page_type['blog_type'] ) {
			apollo13framework__inner__set_sidebar_class( $mid_classes, $apollo13framework_a13->get_option( 'blog_sidebar' ) );
		} //single post
		elseif ( $post ) {
			apollo13framework__inner__set_sidebar_class( $mid_classes, $apollo13framework_a13->get_option( 'post_sidebar' ) );
		} //single page
		elseif ( $page ) {
			//special treatment cause of children menu option
			$sidebar = $apollo13framework_a13->get_meta( '_widget_area', $meta_id );
			if ( strrchr( $sidebar, 'left' ) ) {
				$sidebar = 'left-sidebar';
			} elseif ( strrchr( $sidebar, 'right' ) ) {
				$sidebar = 'right-sidebar';
			}
			apollo13framework__inner__set_sidebar_class( $mid_classes, $sidebar );
		}

		//make class string
		$mid_classes = implode(' ', $mid_classes);

		return $mid_classes;
	}
}


if ( ! function_exists( 'apollo13framework_what_page_type_is_it' ) ) {
	/**
	 * Returns array with types of current page
	 *
	 * @return array
	 */
	function apollo13framework_what_page_type_is_it() {
		global $apollo13framework_a13;
		static $types;

		if ( empty( $types ) ) {
			$types = array(
				'404'         => is_404(),
				'page'        => is_page(),
				'album'       => defined( 'A13FRAMEWORK_ALBUM_PAGE' ),
				'work'        => defined( 'A13FRAMEWORK_WORK_PAGE' ),
				'home'        => is_home(),
				'front_page'  => is_front_page(),
				'archive'     => is_archive(),
				'search'      => is_search(),
				'single'      => is_single(),
				'post'        => is_singular( 'post' ),
				'attachment'  => is_attachment(),
				'albums_list' => defined( 'A13FRAMEWORK_ALBUMS_LIST_PAGE' ),
				'works_list'  => defined( 'A13FRAMEWORK_WORKS_LIST_PAGE' ),
				'shop'        => apollo13framework_is_woocommerce(),
				'product'     => apollo13framework_is_woocommerce() && is_product(),
			);

			$types['singular']          = is_singular();
			$types['singular_not_post'] = $types['singular'] && ! $types['post'];
			$types['cpt']               = $types['album'] || $types['work'];
			$types['cpt_list']          = $types['albums_list'] || $types['works_list'];
			$types['blog_type']         = ( $types['home'] || $types['archive'] || $types['search'] ) && ! $types['cpt_list'] && ! $types['shop'] ;

			$types['page'] = $types['page'] || ($types['404'] && ($apollo13framework_a13->get_option( 'page_404_template_type' ) === 'custom')) || defined('A13FRAMEWORK_CUSTOM_PASSWORD_PROTECTED');
		}

		return $types;
	}
}


if ( ! function_exists( 'apollo13framework_is_no_property_page' ) ) {
	/**
	 * If page is empty search result or 404 it is no property page, and you can read meta fields from it
	 *
	 * @return bool
	 */
	function apollo13framework_is_no_property_page() {
		global $post;

		return ! is_object( $post );
	}
}


if ( ! function_exists( 'apollo13framework_next_posts_link_class' ) ) {
	/**
	 * Adding class for compatibility with Wp-paginate plugin + infinite scroll configuration
	 *
	 * @return string
	 */
	function apollo13framework_next_posts_link_class() {
		return 'class="next"';
	}
}

if ( ! function_exists( 'apollo13framework_prev_posts_link_class' ) ) {
	/**
	 * Adding class for compatibility with Wp-paginate plugin + infinite scroll configuration
	 *
	 * @return string
	 */
	function apollo13framework_prev_posts_link_class() {
		return 'class="prev"';
	}
}
add_filter( 'next_posts_link_attributes', 'apollo13framework_next_posts_link_class' );
add_filter( 'previous_posts_link_attributes', 'apollo13framework_prev_posts_link_class' );


if ( ! function_exists( 'apollo13framework_rss_post_thumbnail' ) ) {
	/**
	 * Adding thumbnails to RSS feed
	 * @return string new content
	 * @internal param string $content current content produced by WordPress
	 *
	 */
	function apollo13framework_rss_post_thumbnail() {
		global $post;
		if ( has_post_thumbnail( $post->ID ) ) {
			$content = '<p>' . get_the_post_thumbnail( $post->ID, 'medium' ) .
			           '</p>' . get_the_excerpt();
		} else {
			$content = get_the_excerpt();
		}

		return $content;
	}
}
add_filter( 'the_excerpt_rss', 'apollo13framework_rss_post_thumbnail' );
add_filter( 'the_content_feed', 'apollo13framework_rss_post_thumbnail' );


if ( ! function_exists( 'apollo13framework_cpt_as_frontpage_title_fix' ) ) {
	/**
	 * Fixes title using settings for front_page from general-template.php ver 4.4.1
	 *
	 * @param $title array actual title parts
	 *
	 * @return array of title parts
	 */
	function apollo13framework_cpt_as_frontpage_title_fix( $title ) {
		$title['title']   = get_bloginfo( 'name', 'display' );
		$title['tagline'] = get_bloginfo( 'description', 'display' );
		$title['site']    = '';

		return $title;
	}
}


if ( ! function_exists( 'apollo13framework_cpt_as_frontpage_menu_fix' ) ) {
	/**
	 * Fixes highlighting homepage menu link when custom post type is set as homepage
	 *
	 * @param $classes array actual CSS classes for menu item
	 *
	 * @param $item object menu item
	 *
	 * @return array of CSS classes
	 */
	function apollo13framework_cpt_as_frontpage_menu_fix( $classes, $item ) {
		if(get_option( 'show_on_front' ) === 'page'){
			$which_page = get_option( 'page_on_front' );
			//link for homepage
			if($item->object_id == $which_page){
				$classes[] = 'current-menu-item';
			}
		}

		// Return the corrected set of classes to be added to the menu item
		return $classes;
	}
}


if ( ! function_exists( 'apollo13framework_vc_change_media_grid_lightbox' ) ) {
	/**
	 * Changes default lightbox in Visual composer media grid element
	 *
	 * @param string $block HTML of whole link for grid item
	 *
	 * @return string
	 */
	function apollo13framework_vc_change_media_grid_lightbox( $block ) {
		global $apollo13framework_a13;

		if($apollo13framework_a13->get_option( 'lightbox_vc_media_grid' ) !== 'off'){
			$block = str_replace(
				array(
					'post_image_url',
					' prettyphoto',
					' data-vc-gitem-zone="prettyphotoLink"',
					' vc-prettyphoto-link',
					'><'
				),
				array(
					'post_image_url::full',
					' a13-lightbox-added',
					' data-sub-html=".vc-mg-item-desc"',
					'',
					'><div class="vc-mg-item-desc"><div class="customHtml"><h4>{{ post_title }}</h4></div></div><'
				),
				$block );
		}

		return $block;
	}
}
add_filter( 'vc_gitem_zone_image_block_link', 'apollo13framework_vc_change_media_grid_lightbox', 11, 1 );



if ( ! function_exists( 'apollo13framework_frontpage' ) ) {
	/**
	 * Function that changes query for front page if user decided to use one of theme features
	 *
	 * @param WP_Query $query
	 */
	function apollo13framework_frontpage( $query ) {
		global $apollo13framework_a13;

		if ( is_admin() || ! $query->is_main_query() ){
			return;
		}

		$theme_decided_home_page = false;

		if ( 'page' == get_option( 'show_on_front') && get_option( 'page_on_front' ) && ($query->query_vars['page_id'] === get_option( 'page_on_front' ) ) ){
			$theme_decided_home_page = true;
		}

		if ( $theme_decided_home_page ) {

			$fp_variant = $apollo13framework_a13->get_option( 'fp_variant' );

			//single custom post type as front page
			if ( $fp_variant == 'single_work' || $fp_variant == 'single_album' ) {

				$post_id = $apollo13framework_a13->get_option( $fp_variant == 'single_work' ? 'fp_work' : 'fp_album' );
				$post_type = $fp_variant == 'single_work' ? A13FRAMEWORK_CUSTOM_POST_TYPE_WORK : A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM;

				//in case of WPML make sure to get localized version
				if(defined( 'ICL_SITEPRESS_VERSION')){
					$post_id = apply_filters( 'wpml_object_id', $post_id, $post_type, true );
				}

				$query->set( 'p', $post_id);
				$query->set( 'post_type' , $post_type );
				$query->set( 'page_id', null );
				$query->is_page = false;
				$query->is_singular = true;
				$query->is_single = true;

				//fix title cause we customized query
				add_filter( 'document_title_parts', 'apollo13framework_cpt_as_frontpage_title_fix' );
				//fix homepage menu item cause we customized query
				add_action( 'nav_menu_css_class','apollo13framework_cpt_as_frontpage_menu_fix', 10, 2  );
			}
		}
	}
}
add_action('pre_get_posts','apollo13framework_frontpage');