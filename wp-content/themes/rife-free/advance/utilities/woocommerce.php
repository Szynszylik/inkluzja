<?php
/**
 * Functions that are connected to handling woocommerce
 */

if(!function_exists('apollo13framework_woocommerce_image_dimensions')){
	/**
	 * Change WC initial images sizes
	 */
	function apollo13framework_woocommerce_image_dimensions() {
        /**
         * Define image sizes
         */
        $catalog = array(
            'width' 	=> '320',	// px
            'height'	=> '426',	// px
            'crop'		=> 1 		// true
        );

        $single = array(
            'width' 	=> '590',	// px
            'height'	=> '810',	// px
            'crop'		=> 1 		// true
        );

        $thumbnail = array(
            'width' 	=> '140',	// px
            'height'	=> '0',	// px
            'crop'		=> 1 		// true
        );

        // Image sizes
        update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
        update_option( 'shop_single_image_size', $single ); 		// Single product image
        update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
    }
}
//overwrite image sizes only when theme is activated but ONLY on theme activation
global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ){
	add_action( 'init', 'apollo13framework_woocommerce_image_dimensions', 1 );
}




/**********************/
/***** BREADCRUMBS ****/
/**********************/

/**
 * remove breadcrumbs from shop page
 */
function apollo13framework_woocommerce_custom_breadcrumbs() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
add_filter('woocommerce_before_main_content','apollo13framework_woocommerce_custom_breadcrumbs');


/**
 * Add product categories to the "Product" breadcrumb in WooCommerce.
 * Get breadcrumbs on product pages that read: Home > Shop > Product category > Product Name
 *
 * @param $trail
 *
 * @return array
 */
function woo_custom_breadcrumbs_trail_add_product_categories ( $trail ) {
	if ( ( get_post_type() === 'product' ) && is_singular() ) {
		global $post;
		$taxonomy = 'product_cat';
		$terms = get_the_terms( $post->ID, $taxonomy );
		$links = array();
		if ( $terms && ! is_wp_error( $terms ) ) {
			$count = 0;
			foreach ( $terms as $c ) {
				$count++;
				$parents = woo_get_term_parents( $c->term_id, $taxonomy, true, ', ', $c->name, array() );
				if ( $parents != '' && ! is_wp_error( $parents ) ) {
					$parents_arr = explode( ', ', $parents );
					foreach ( $parents_arr as $p ) {
						if ( $p != '' ) { $links[] = $p; }
					}
				}
			}
// Add the trail back on to the end.
// $links[] = $trail['trail_end'];
			$trail_end = get_the_title($post->ID);

			// Add the new links, and the original trail's end, back into the trail.
			array_splice( $trail, 2, count( $trail ) - 1, $links );
			$trail['trail_end'] = $trail_end;

			//remove any duplicate breadcrumbs
			$trail = array_unique($trail);
		}
	}
	return $trail;
} // End woo_custom_breadcrumbs_trail_add_product_categories()
add_filter( 'woo_breadcrumbs_trail', 'woo_custom_breadcrumbs_trail_add_product_categories', 20 );


if ( ! function_exists( 'woo_get_term_parents' ) ) {
	/**
	 * Retrieve term parents with separator.
	 *
	 * @param int $id Term ID.
	 * @param string $taxonomy.
	 * @param bool $link        Optional, default is false. Whether to format with link.
	 * @param string $separator Optional, default is '/'. How to separate terms.
	 * @param bool $nice_name Optional, default is false. Whether to use nice name for display.
	 * @param array $visited    Optional. Already linked to terms to prevent duplicates.
	 *
	 * @return string
	 */
	function woo_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nice_name = false, $visited = array() ) {
		$chain = '';
		$parent = &get_term( $id, $taxonomy );
		if ( is_wp_error( $parent ) )
			return $parent;
		if ( $nice_name ) {
			$name = $parent->slug;
		} else {
			$name = $parent->name;
		}
		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$chain .= woo_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nice_name, $visited );
		}
		if ( $link ) {
			$chain .= '<a href="' . esc_url( get_term_link( $parent, $taxonomy ) ) . '" title="' .
			          /* translators: %s - parent term name */
			          esc_attr( sprintf( esc_html__( "View all posts in %s", 'rife-free' ), $parent->name ) )
			          . '">'.$parent->name.'</a>' . $separator;
		} else {
			$chain .= $name.$separator;
		}
		return $chain;
	} // End woo_get_term_parents()
}


/**
 * change breadcrumb delimiter
 *
 * @param $defaults
 *
 * @return mixed
 */
function apollo13framework_wp_change_breadcrumb_delimiter( $defaults ) {
	// Change the breadcrumb delimiter from '/' to '>'
	$defaults['delimiter'] = '<span class="sep">/</span>';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'apollo13framework_wp_change_breadcrumb_delimiter' );



/*************************/
/***** THEME WRAPPERS ****/
/*************************/

if(!function_exists('apollo13framework_woocommerce_theme_wrapper_start')){
	/**
	 * start html of WC templates
	 */
	function apollo13framework_woocommerce_theme_wrapper_start() {
		global $apollo13framework_a13;

		$lazy_load          = $apollo13framework_a13->get_option('shop_lazy_load') === 'on';
		$pagination_class   = $lazy_load && apollo13framework_is_woocommerce_products_list_page()? ' lazy-load-on' : '';
		$custom_thumbs      = $apollo13framework_a13->get_option('product_custom_thumbs') === 'on';
		$thumbnails_class   = '';

		if( $custom_thumbs ){
			add_filter( 'woocommerce_product_thumbnails_columns', 'apollo13framework_wc_single_product_thumbs_columns' );
			$thumbnails_class = ' theme-thumbs';
		}

        add_filter( 'woocommerce_show_page_title', '__return_false');
        apollo13framework_title_bar();
        ?>
	    <article id="content" class="clearfix">
	        <div class="content-limiter">
	            <div id="col-mask">
	                <div class="content-box<?php echo esc_attr($pagination_class.$thumbnails_class); ?>">
	                    <div class="formatter">
        <?php
    }
}
add_action('woocommerce_before_main_content', 'apollo13framework_woocommerce_theme_wrapper_start', 10);
if(!function_exists('apollo13framework_woocommerce_theme_wrapper_end')){
	/**
	 * end html of WC templates
	 */
	function apollo13framework_woocommerce_theme_wrapper_end() {
        ?>
                            <div class="clear"></div>
                        </div>
		            </div>
		            <?php get_sidebar(); ?>
		        </div>
			</div>
		</article>
    <?php
    }
}
add_action('woocommerce_after_main_content', 'apollo13framework_woocommerce_theme_wrapper_end', 10);

//tell WC how our content wrapper should look
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);




/******************************/
/***** GENERAL WOOCOMMERCE ****/
/******************************/



if(!function_exists('apollo13framework_is_woocommerce')){
	/**
	 * is current page one of WC
	 *
	 * @return bool
	 */
	function apollo13framework_is_woocommerce() {
        return (apollo13framework_is_woocommerce_activated() && (is_woocommerce() || is_cart() || is_account_page() || is_checkout() || is_order_received_page()));
    }
}


if(!function_exists( 'apollo13framework_is_woocommerce_products_list_page' )){
	/**
	 * is current page one of WC pages without proper title
	 *
	 * @return bool
	 */
	function apollo13framework_is_woocommerce_products_list_page() {
        return (apollo13framework_is_woocommerce_activated() && (is_shop() || is_product_taxonomy()));
    }
}


if(!function_exists('apollo13framework_is_woocommerce_sidebar_page')){
	/**
	 * is current page one of WC pages where sidebar is useful
	 *
	 * @return bool
	 */
	function apollo13framework_is_woocommerce_sidebar_page() {
        return (apollo13framework_is_woocommerce_activated() && is_woocommerce());
    }
}



if(!function_exists('apollo13framework_is_product_new')){
	/**
	 * is current product new
	 *
	 * @return bool
	 */
	function apollo13framework_is_product_new() {
        global $product;
        return is_object_in_term( apollo13framework_wc_get_product_id($product), 'product_tag', 'new' );
    }
}



function apollo13framework_wc_get_product_id($product){
	return method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
}


//overwrite WC function
/**
 * Function changes number of related products
 */
function woocommerce_output_related_products() {
	global $apollo13framework_a13;

	if($apollo13framework_a13->get_option( 'product_related_products') === 'off'){
		return;
	}

    $args = array(
        'posts_per_page' => 3,
        'columns'        => 3,
    );
    woocommerce_related_products( $args );
}





/************************/
/***** PRODUCTS LIST ****/
/************************/

if(!function_exists('apollo13framework_wc_loop_second_image')){
	/**
	 * add second image, so it can be revealed on hover
	 */
	function apollo13framework_wc_loop_second_image() {
		/* @var $product WC_Product */
        global $product, $apollo13framework_a13;

		if($apollo13framework_a13->get_option( 'shop_products_second_image' ) === 'on') {
			//second thumb
			$attachment_ids   = $product->get_gallery_image_ids();
			$is_enough_images = sizeof( $attachment_ids ) > 0;

			if ( $attachment_ids && $is_enough_images ) {
				$image = wp_get_attachment_image( $attachment_ids[0], 'shop_catalog' );
				if ( strlen( $image ) ) {
					echo '<span class="sec-img">' . wp_kses_post($image) . '</span>';
				}
			}
		}
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'apollo13framework_wc_loop_second_image', 10);



if(!function_exists( 'apollo13framework_wc_single_product_labels' )){
	/**
	 * add labels above to single product
	 */
	function apollo13framework_wc_single_product_labels() {
		/* @var $product WC_Product */
        global $product;

        $html = '';

        //labels
        //out of stock
        if(!$product->is_in_stock()){
            $html .= '<span class="ribbon out-of-stock"><em>'.esc_html__( 'Out of stock', 'rife-free' ).'</em></span>';
        }
        else{
            //sale
            if($product->is_on_sale()){
                $html .= '<span class="ribbon sale"><em>'.esc_html__( 'Sale', 'rife-free' ).'</em></span>';
            }
            //new
            if(apollo13framework_is_product_new()){
                $html .= '<span class="ribbon new"><em>'.esc_html__( 'New', 'rife-free' ).'</em></span>';
            }
        }

		if(strlen($html)){
			echo '<div class="product-labels">'.wp_kses_post($html).'</div>';
		}
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'apollo13framework_wc_single_product_labels', 11);



if(!function_exists( 'apollo13framework_wc_loop_single_product_categories' )){
	/**
	 * display categories of product
	 */
	function apollo13framework_wc_loop_single_product_categories() {
        global $product;

        //categories
		$terms = get_the_terms( apollo13framework_wc_get_product_id($product), 'product_cat' );
		if( sizeof( $terms ) && is_array($terms) ){
			echo '<span class="posted_in">';

			$temp = 1;
			foreach ( $terms as $term ) {
				if($temp > 1){
					echo '<span class="sep">/</span>';
				}
				echo esc_html($term->name);
				$temp++;
			}

			echo '</span>';
		}
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'apollo13framework_wc_loop_single_product_categories', 13);



if(!function_exists( 'apollo13framework_wc_loop_single_product_overlay' )){
	function apollo13framework_wc_loop_single_product_overlay() {
		global $apollo13framework_a13;
		if($apollo13framework_a13->get_option( 'shop_products_variant' ) === 'overlay'){
	        echo '<span class="overlay"></span>';
		}
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'apollo13framework_wc_loop_single_product_overlay', 11);



if(!function_exists( 'apollo13framework_wc_loop_single_product_text_div' )){
	/**
	 * pack text content under thumbnail in div.product-details
	 */
	function apollo13framework_wc_loop_single_product_text_div() {
        echo '<div class="product-details">';
    }
	function apollo13framework_wc_loop_single_product_text_div_close() {
        echo '</div>';
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'apollo13framework_wc_loop_single_product_text_div', 12);
add_action( 'woocommerce_after_shop_loop_item_title', 'apollo13framework_wc_loop_single_product_text_div_close', 21);



if (!function_exists('apollo13framework_wc_loop_shop_per_page')) {
	/**
	 * Change number or products per page
	 *
	 * @return int number of products
	 */
	function apollo13framework_wc_loop_shop_per_page() {
		global $apollo13framework_a13;
		return $apollo13framework_a13->get_option( 'shop_products_per_page');
	}
}
add_filter( 'loop_shop_per_page', 'apollo13framework_wc_loop_shop_per_page', 20 );



if (!function_exists('apollo13framework_wc_loop_columns')) {
	/**
	 * Change number or products per row
	 *
	 * @return int number of columns
	 */
	function apollo13framework_wc_loop_columns() {
		global $apollo13framework_a13;
		return $apollo13framework_a13->get_option( 'shop_products_columns');
	}
}
add_filter('loop_shop_columns', 'apollo13framework_wc_loop_columns');



if ( ! function_exists( 'woocommerce_result_count' ) ) {
	/**
	 * Output the result count text (Showing x - x of x results).
	 */
	function woocommerce_result_count() {
		global $wp_query;

		if ( ! woocommerce_products_will_display() ){
			return;
		}
		echo '<span class="result-count">';
		$paged    = max( 1, $wp_query->get( 'paged' ) );
		$total    = $wp_query->found_posts;
		$last     = min( $total, $wp_query->get( 'posts_per_page' ) * $paged );

		if ( 1 == $total ) {
			echo '1/1';
		} else {
			printf( '%1$d/%2$d', esc_html($last), esc_html($total) );
		}
		echo '</span>';
	}
}



if ( ! function_exists( 'woocommerce_pagination' ) ) {
	/**
	 * Output the pagination.
	 */
	function woocommerce_pagination() {
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		//since WC 3.3.0
		if ( function_exists('wc_get_loop_prop') && wc_get_loop_prop( 'is_shortcode' ) ) {
			$base = esc_url_raw( add_query_arg( 'product-page', '%#%', false ) );
			$format = '?product-page = %#%';
		}
		else {
			$base   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
			$format = '';
		}

		global $wp_query;

		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}

		// Set up paginated links.
		$links = paginate_links( apply_filters( 'woocommerce_pagination_args', array(
			'base'         => $base,
			'format'       => $format,
			'add_args'     => '',
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $wp_query->max_num_pages,
			'prev_text'    => '&larr;',
			'next_text'    => '&rarr;',
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );

		if ( $links ) {
			echo wp_kses_post( _navigation_markup( $links, 'woocommerce-pagination' ) );
		}
	}
}


//change pagination to default wordpress style
add_filter('woocommerce_pagination_args', 'apollo13framework_loop_pagination', 20);



//remove sale badge from loop
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
//move number of results to different place
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
//remove ordering
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );




/*************************/
/***** SINGLE PRODUCT ****/
/*************************/

/**
 * one column of thumbnails in product
 *
 * @return int columns
 */
function apollo13framework_wc_single_product_thumbs_columns(){
	return 1;
}



/**
 * bigger avatars
 * Changes size of avatars in WC
 * @return int
 */
function apollo13framework_wc_single_product_avatars() {
	return 90;
}
add_filter( 'woocommerce_review_gravatar_size', 'apollo13framework_wc_single_product_avatars' );



//product labels
add_action( 'woocommerce_product_thumbnails', 'apollo13framework_wc_single_product_labels', 12);


//remove sale badge
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

//move rating
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 4 );




/***************/
/***** CART ****/
/***************/

/**
 * update cart quantity in theme cart fragment
 *
 * @param $fragments
 *
 * @return mixed
 */
function apollo13framework_wc_header_add_to_cart_fragment( $fragments ){
	global $woocommerce;
	$number = $woocommerce->cart->cart_contents_count;
	$fragments['span#basket-items-count'] = '<span id="basket-items-count"'.($number > 0 ? '' : 'class="zero"' ).'>'.$number.'</span>';
	return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'apollo13framework_wc_header_add_to_cart_fragment');


/**
 * go to shop button when cart is empty
 *
 * @return mixed
 */
function apollo13framework_wc_min_cart_footer(){
	if(WC()->cart->is_empty()):
	?>
	<p class="buttons">
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="button wc-forward"><?php esc_html_e( 'Go to shop', 'rife-free' ); ?></a>
	</p>
	<?php
	endif;
}
add_filter('woocommerce_after_mini_cart', 'apollo13framework_wc_min_cart_footer');


//move cross sells in cart
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );
add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 11 );



/***************/
/*** CHECKOUT **/
/***************/
function apollo13framework_wc_checkout_columns_open($template_name){
	if($template_name === 'checkout/form-login.php'){
		echo '<div class="col-1">';
	}
	elseif($template_name === 'checkout/form-coupon.php'){
		echo '<div class="col-2">';
	}
}
add_action( 'woocommerce_before_template_part', 'apollo13framework_wc_checkout_columns_open', 9, 1 );



function apollo13framework_wc_checkout_columns_close($template_name){
	if($template_name === 'checkout/form-login.php'){
		echo '</div>';
	}
	elseif($template_name === 'checkout/form-coupon.php'){
		echo '</div>';
	}
}
add_action( 'woocommerce_after_template_part', 'apollo13framework_wc_checkout_columns_close', 11, 1 );



function apollo13framework_wc_checkout_notices_open(){
	echo '<div class="col2-set notices-forms">';
}
add_action( 'woocommerce_before_checkout_form', 'apollo13framework_wc_checkout_notices_open', 9 );
function apollo13framework_wc_checkout_notices_close(){
	echo '</div>';
}
add_action( 'woocommerce_before_checkout_form', 'apollo13framework_wc_checkout_notices_close', 11 );
