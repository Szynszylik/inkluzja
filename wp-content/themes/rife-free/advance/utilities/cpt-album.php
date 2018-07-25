<?php
/* Functions used by custom post type album */
if(!function_exists('apollo13framework_album_posted_in')){
	/**
	 * For printing categories(taxonomies) of album
	 *
	 * @param string $separator string separating terms
	 *
	 * @return string HTML
	 */
	function apollo13framework_album_posted_in( $separator = '<span>/</span>' ) {
		$term_list = wp_get_post_terms(get_the_ID(), A13FRAMEWORK_CPT_ALBUM_TAXONOMY, array("fields" => "all"));;
		$count_terms = count( $term_list );
		$html = '';
		$iteration = 1;
		if( $count_terms ){
			foreach($term_list as $term) {
				$html .= '<a href="' . esc_url(get_term_link($term)) . '">' . $term->name . '</a>';
				if( $count_terms != $iteration ){
					$html .= $separator;
				}
				$iteration++;
			}
		}

		return $html;
	}
}



if(!function_exists('apollo13framework_albums_nav')){
    /**
     * Navigation through album post type
     */
    function apollo13framework_albums_nav() {
        global $apollo13framework_a13;
        $show_back_btn = true;
        $title = $href = '';
        $navigate_through_categories = $apollo13framework_a13->get_option( 'album_navigate_by_categories' ) === 'on';

        if($apollo13framework_a13->get_option( 'album_navigation') === 'off'){
            //nothing to do
            return;
        }

        if($navigate_through_categories){
            $term_list = wp_get_post_terms(get_the_ID(), A13FRAMEWORK_CPT_ALBUM_TAXONOMY, array("fields" => "all"));
            $count_terms = count( $term_list );
            if($count_terms > 0){
                $term = $term_list[0];
	            /* translators: %s: page title */
                $title = sprintf(esc_html__( 'Back to %s', 'rife-free' ), $term->name);
                $href = get_term_link($term);
            }
            else{
                $show_back_btn = false;
            }
        }
        else{
            $albums_id = $apollo13framework_a13->get_option( 'albums_list_page' );
	        /* translators: %s: page title */
            $title = sprintf(esc_html__( 'Back to %s', 'rife-free' ), get_the_title( $albums_id ));
            if($albums_id !== '0'){
                $href = get_permalink($albums_id);
            }
            //albums list as front page
            elseif($apollo13framework_a13->get_option( 'fp_variant' ) === 'albums_list'){
                $href = home_url( '/' );
            }
            else{
                $show_back_btn = false;
            }
        }

        echo '<div class="albums-nav">';

        if( $navigate_through_categories ) {
	        next_post_link( '%link', '<span class="fa fa-long-arrow-left" title="%title - %date"></span>', true, '', A13FRAMEWORK_CPT_ALBUM_TAXONOMY );
        }
	    else{
		    next_post_link( '%link', '<span class="fa fa-long-arrow-left" title="%title - %date"></span>' );
	    }

	    echo $show_back_btn? '<a href="'.esc_url($href).'" title="'.esc_attr($title).'" class="to-cpt-list fa fa-th"></a>' : '';

	    if( $navigate_through_categories ) {
            previous_post_link( '%link', '<span class="fa fa-long-arrow-right" title="%title - %date"></span>', true, '', A13FRAMEWORK_CPT_ALBUM_TAXONOMY );
        }
	    else{
		    previous_post_link( '%link', '<span class="fa fa-long-arrow-right" title="%title - %date"></span>' );
	    }

        echo '</div>';
    }
}



if(!function_exists('apollo13framework_make_album_image')){
	/**
	 * Making cover for albums in Albums list
	 *
	 * @param int          $album_id
	 * @param string|array $sizes
	 * @param int|bool     $columns
	 *
	 * @return string HTML of image
	 */
    function apollo13framework_make_album_image( $album_id, $sizes = '', $columns = false ){
        global  $apollo13framework_a13;

        if(empty($album_id)){
            $album_id = get_the_ID();
        }

        if( !is_array($sizes) ){
            $brick_size         = $apollo13framework_a13->get_meta('_brick_ratio_x', $album_id);
            $columns            = $columns === false? (int)$apollo13framework_a13->get_option( 'albums_list_brick_columns' ) : (int)$columns;
            $bricks_max_width   = (int)$apollo13framework_a13->get_option( 'albums_list_bricks_max_width' );
            $brick_margin       = (int)$apollo13framework_a13->get_option( 'albums_list_brick_margin' );
	        $brick_proportion   = $apollo13framework_a13->get_option( 'albums_list_bricks_proportions_size' );

            /* brick_size can't be bigger then columns for calculations */
            $brick_size         = strlen($brick_size)? min((int)$brick_size, $columns) : 1;
            $ratio              = $brick_size/$columns;

	        //many possible sizes, but one RULE to rule them all
	        $image_width =  ceil($ratio * $bricks_max_width - (1-$ratio) * $brick_margin);

	        $height_proportion = apollo13framework_calculate_height_proportion($brick_proportion);

	        $image_height = $image_width*$height_proportion;

            $sizes = array($image_width, $image_height);
        }


        $src = apollo13framework_make_post_image( $album_id, $sizes, true );
        if ( $src === false ) {
            $src = get_theme_file_uri( 'images/holders/photo.png');
        }
        else{
	        //check for animated gifs
	        $file_type = wp_check_filetype( $src );
	        //if it is gif then it is probably animated gif, so lets use original file
	        if( $file_type['type'] === 'image/gif'){
		        $src = apollo13framework_make_post_image( $album_id, array('full'), true );
	        }
        }

	    $image_alt = '';
	    $image_title = '';
	    $image_id = get_post_thumbnail_id( $album_id );
	    if($image_id){
	        $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true);
	        $image_title = get_the_title( $image_id );
	    }

        return '<img src="'.esc_url($src).'" alt="'.esc_attr($image_alt).'"'.($image_title? ' title="'.esc_attr($image_title).'"' : '').' />';
    }
}



if(!function_exists('apollo13framework_album_individual_look')){
	/**
	 * Prepares CSS specially for each album
	 */
	function apollo13framework_album_individual_look(){
		//checks if page can have meta fields
		if(!apollo13framework_is_no_property_page()){
			$css = '';
			$page_type = apollo13framework_what_page_type_is_it();
			$album = $page_type['album'];
			
			if($album){
				$id    = get_the_ID();
				$theme = get_post_meta( $id, '_theme', true );

				if($theme === 'bricks') {
					$bricks_max_width = 'max-width:' . get_post_meta( $id, '_bricks_max_width', true ) . ';';
					$brick_margin     = get_post_meta( $id, '_brick_margin', true );

					$css .= '
.single-album .bricks-frame{
	' . $bricks_max_width . '
}
#only-album-items-here{
	margin-right: -' . $brick_margin . ';
}
.album-content-on-the-right #only-album-items-here{
	margin-right: calc(460px - ' . $brick_margin . ');
}

/* 6 columns */
.single-album .bricks-columns-6 .archive-item,
.single-album .bricks-columns-6 .grid-master{
	width: -webkit-calc(16.6666666% - ' . $brick_margin . ');
	width:         calc(16.6666666% - ' . $brick_margin . ');
}
.single-album .bricks-columns-6 .archive-item.w2{
	width: -webkit-calc(33.3333333% - ' . $brick_margin . ');
	width:         calc(33.3333333% - ' . $brick_margin . ');
}
.single-album .bricks-columns-6 .archive-item.w3{
	width: -webkit-calc(50% - ' . $brick_margin . ');
	width:         calc(50% - ' . $brick_margin . ');
}
.single-album .bricks-columns-6 .archive-item.w4{
	width: -webkit-calc(66.6666666% - ' . $brick_margin . ');
	width:         calc(66.6666666% - ' . $brick_margin . ');
}
.single-album .bricks-columns-6 .archive-item.w5{
	width: -webkit-calc(83.3333333% - ' . $brick_margin . ');
	width:         calc(83.3333333% - ' . $brick_margin . ');
}

/* 5 columns */
.single-album .bricks-columns-5 .archive-item,
.single-album .bricks-columns-5 .grid-master{
	width: -webkit-calc(20% - ' . $brick_margin . ');
	width:         calc(20% - ' . $brick_margin . ');
}
.single-album .bricks-columns-5 .archive-item.w2{
	width: -webkit-calc(40% - ' . $brick_margin . ');
	width:         calc(40% - ' . $brick_margin . ');
}
.single-album .bricks-columns-5 .archive-item.w3{
	width: -webkit-calc(60% - ' . $brick_margin . ');
	width:         calc(60% - ' . $brick_margin . ');
}
.single-album .bricks-columns-5 .archive-item.w4{
	width: -webkit-calc(80% - ' . $brick_margin . ');
	width:         calc(80% - ' . $brick_margin . ');
}

/* 4 columns */
.single-album .bricks-columns-4 .archive-item,
.single-album .bricks-columns-4 .grid-master{
	width: -webkit-calc(25% - ' . $brick_margin . ');
	width:         calc(25% - ' . $brick_margin . ');
}
.single-album .bricks-columns-4 .archive-item.w2{
	width: -webkit-calc(50% - ' . $brick_margin . ');
	width:         calc(50% - ' . $brick_margin . ');
}
.single-album .bricks-columns-4 .archive-item.w3{
	width: -webkit-calc(75% - ' . $brick_margin . ');
	width:         calc(75% - ' . $brick_margin . ');
}

/* 3 columns */
.single-album .bricks-columns-3 .archive-item,
.single-album .bricks-columns-3 .grid-master{
	width: -webkit-calc(33.3333333% - ' . $brick_margin . ');
	width:         calc(33.3333333% - ' . $brick_margin . ');
}
.single-album .bricks-columns-3 .archive-item.w2{
	width: -webkit-calc(66.6666666% - ' . $brick_margin . ');
	width:         calc(66.6666666% - ' . $brick_margin . ');
}

/* 2 columns */
.single-album .bricks-columns-2 .archive-item,
.single-album .bricks-columns-2 .grid-master{
	width: -webkit-calc(50% - ' . $brick_margin . ');
	width:         calc(50% - ' . $brick_margin . ');
}

/* 100% width bricks */
.single-album .bricks-columns-1 .grid-master,
.single-album .bricks-columns-1 .archive-item,
.single-album .bricks-columns-2 .archive-item.w2,
.single-album .bricks-columns-3 .archive-item.w3,
.single-album .bricks-columns-4 .archive-item.w4,
.single-album .bricks-columns-5 .archive-item.w5,
.single-album .bricks-columns-6 .archive-item.w6{
	width: -webkit-calc(100% - ' . $brick_margin . ');
	width:         calc(100% - ' . $brick_margin . ');
}


/* responsive rules */
@media only screen and (max-width: 1279px){
	/* fluid layout columns */

	/* 3 columns */
	.single-album .layout-fluid .bricks-columns-6 .grid-master,
	.single-album .layout-fluid .bricks-columns-6 .archive-item,
	.single-album .layout-fluid .bricks-columns-6 .archive-item.w2,
	.single-album .layout-fluid .bricks-columns-5 .grid-master,
	.single-album .layout-fluid .bricks-columns-5 .archive-item,
	.single-album .layout-fluid .bricks-columns-5 .archive-item.w2,
	.single-album .layout-fluid .bricks-columns-4 .grid-master,
	.single-album .layout-fluid .bricks-columns-4 .archive-item{
		width: -webkit-calc(33.3333333% - ' . $brick_margin . ');
		width:         calc(33.3333333% - ' . $brick_margin . ');
	}
	.single-album .layout-fluid .bricks-columns-6 .archive-item.w3,
	.single-album .layout-fluid .bricks-columns-6 .archive-item.w4,
	.single-album .layout-fluid .bricks-columns-5 .archive-item.w3,
	.single-album .layout-fluid .bricks-columns-4 .archive-item.w2{
		width: -webkit-calc(66.6666666% - ' . $brick_margin . ');
		width:         calc(66.6666666% - ' . $brick_margin . ');
	}
	.single-album .layout-fluid .bricks-columns-6 .archive-item.w5,
	.single-album .layout-fluid .bricks-columns-5 .archive-item.w4,
	.single-album .layout-fluid .bricks-columns-4 .archive-item.w3{
		width: -webkit-calc(100% - ' . $brick_margin . ');
		width:         calc(100% - ' . $brick_margin . ');
	}

	/* 2 columns - when vertical header and sidebar are present */
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-6 .grid-master,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-6 .archive-item,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-6 .archive-item.w2,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-6 .archive-item.w3,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-6 .archive-item.w4,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-5 .grid-master,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-5 .archive-item,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-5 .archive-item.w2,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-5 .archive-item.w3,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-4 .grid-master,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-4 .archive-item,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-4 .archive-item.w2,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-3 .grid-master,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-3 .archive-item{
		width: -webkit-calc(50% - ' . $brick_margin . ');
		width:         calc(50% - ' . $brick_margin . ');
	}
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-6 .archive-item.w5,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-5 .archive-item.w4,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-4 .archive-item.w3,
	.header-vertical.single-album .layout-fluid .album-content-on.bricks-columns-3 .archive-item.w2{
		width: -webkit-calc(100% - ' . $brick_margin . ');
		width:         calc(100% - ' . $brick_margin . ');
	}
}

@media only screen and (max-width: 800px){
	/* fluid layout columns */

	/* 2 columns */
	.single-album .layout-fluid .bricks-columns-6 .grid-master,
	.single-album .layout-fluid .bricks-columns-6 .archive-item,
	.single-album .layout-fluid .bricks-columns-6 .archive-item.w2,
	.single-album .layout-fluid .bricks-columns-6 .archive-item.w3,
	.single-album .layout-fluid .bricks-columns-6 .archive-item.w4,
	.single-album .layout-fluid .bricks-columns-5 .grid-master,
	.single-album .layout-fluid .bricks-columns-5 .archive-item,
	.single-album .layout-fluid .bricks-columns-5 .archive-item.w2,
	.single-album .layout-fluid .bricks-columns-5 .archive-item.w3,
	.single-album .layout-fluid .bricks-columns-4 .grid-master,
	.single-album .layout-fluid .bricks-columns-4 .archive-item,
	.single-album .layout-fluid .bricks-columns-4 .archive-item.w2,
	.single-album .layout-fluid .bricks-columns-3 .grid-master,
	.single-album .layout-fluid .bricks-columns-3 .archive-item{
		width: -webkit-calc(50% - ' . $brick_margin . ');
		width:         calc(50% - ' . $brick_margin . ');
	}
	/* 6 and 5 done already on bigger limits */
	.single-album .layout-fluid .bricks-columns-4 .archive-item.w3,
	.single-album .layout-fluid .bricks-columns-3 .archive-item.w2{
		width: -webkit-calc(100% - ' . $brick_margin . ');
		width:         calc(100% - ' . $brick_margin . ');
	}
}

@media only screen and (max-width: 480px) {
	#only-album-items-here{
		margin-right: 0;
	}
	html[dir=\"rtl\"] #only-album-items-here{
        margin-left: 0;
    }

	/* all layouts */

	/* 1 column */
	.single-album #mid .bricks-columns-6 .grid-master,
	.single-album #mid .bricks-columns-6 .archive-item,
	.single-album #mid .bricks-columns-6 .archive-item.w2,
	.single-album #mid .bricks-columns-6 .archive-item.w3,
	.single-album #mid .bricks-columns-6 .archive-item.w4,
	.single-album #mid .bricks-columns-6 .archive-item.w5,
	.single-album #mid .bricks-columns-6 .archive-item.w6,
	.single-album #mid .bricks-columns-5 .grid-master,
	.single-album #mid .bricks-columns-5 .archive-item,
	.single-album #mid .bricks-columns-5 .archive-item.w2,
	.single-album #mid .bricks-columns-5 .archive-item.w3,
	.single-album #mid .bricks-columns-5 .archive-item.w4,
	.single-album #mid .bricks-columns-5 .archive-item.w5,
	.single-album #mid .bricks-columns-4 .grid-master,
	.single-album #mid .bricks-columns-4 .archive-item,
	.single-album #mid .bricks-columns-4 .archive-item.w2,
	.single-album #mid .bricks-columns-4 .archive-item.w3,
	.single-album #mid .bricks-columns-4 .archive-item.w4,
	.single-album #mid .bricks-columns-3 .grid-master,
	.single-album #mid .bricks-columns-3 .archive-item,
	.single-album #mid .bricks-columns-3 .archive-item.w2,
	.single-album #mid .bricks-columns-3 .archive-item.w3,
	.single-album #mid .bricks-columns-2 .grid-master,
	.single-album #mid .bricks-columns-2 .archive-item,
	.single-album #mid .bricks-columns-2 .archive-item.w2,
	.single-album #mid .bricks-columns-1 .grid-master,
	.single-album #mid .bricks-columns-1 .archive-item{
		width: 100%;
	}
}
';
				}
				elseif($theme === 'scroller' || $theme === 'scroller-parallax'){
					$cell_margin     = get_post_meta( $id, '_scroller_cell_margin', true );

					$css = '
.a13-main-scroller .carousel-cell {
    margin-right: ' . $cell_margin . ';
}
';
				}
			}

			//if we have some CSS then add it
			if(strlen($css)){
				wp_add_inline_style( 'a13-user-css', $css );
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'apollo13framework_album_individual_look', 28 );



if(!function_exists('apollo13framework_display_items_from_query_album_list')) {
	/**
	 * @param bool|WP_Query $query
	 * @param array         $args
	 */
	function apollo13framework_display_items_from_query_album_list($query = false, $args = array()){
		global $apollo13framework_a13;

		if($query === false){
			global $wp_query;
			$query = $wp_query;
			$displayed_in = 'album-list';
		}
		else{
			$displayed_in = 'shortcode';
		}

		$default_args = array(
			'columns' => $apollo13framework_a13->get_option( 'albums_list_brick_columns' ),
			'filter' => false,
		);

		$args = wp_parse_args($args, $default_args);

		/* show filter? */
		if($args['filter']){
			$query_args = array(
				'hide_empty' => true,
				'object_ids' => wp_list_pluck( $query->posts, 'ID' ),
				'taxonomy'   => A13FRAMEWORK_CPT_ALBUM_TAXONOMY,
			);

			/** @noinspection PhpInternalEntityUsedInspection */
			$terms = get_terms( $query_args );

			apollo13framework_make_post_grid_filter($terms, 'albums-filter');
		}


		/* If there are no posts to display, such as an empty archive page */
		if ( ! $query->have_posts() ):
			?>
			<div class="formatter">
				<div class="real-content empty-blog">
					<?php
					echo '<p>'.esc_html__( 'Apologies, but no results were found for the requested archive.', 'rife-free' ).'</p>';
					get_template_part( 'no-content');
					?>
				</div>
			</div>
			<?php
		/* If there ARE some posts */
		else:
			$ajax_call = isset( $_GET['a13-ajax-get'] );
			$albums_list_page = defined( 'A13FRAMEWORK_ALBUMS_LIST_PAGE');

			if(!$ajax_call){
				?>
				<div class="bricks-frame albums-bricks<?php echo esc_attr( apollo13framework_albums_list_look_classes($args['columns']) ); ?>">
				<div class="albums-grid-container"<?php
				//lazy load on
				if($albums_list_page){
					$lazy_load        = $apollo13framework_a13->get_option( 'albums_list_lazy_load' ) === 'on';
					$lazy_load_mode   = $apollo13framework_a13->get_option( 'albums_list_lazy_load_mode' );
					echo ' data-lazy-load="' . esc_attr( $lazy_load ) . '" data-lazy-load-mode="' . esc_attr( $lazy_load_mode ) . '"';
				}
				?>>
				<div class="grid-master"></div>
				<?php
			}

			while ( $query->have_posts() ) :
				echo apollo13framework_albums_list_item($query, $displayed_in, $args['columns']);
			endwhile;

			if ( ! $ajax_call ) { ?>
				</div>
				</div>
				<div class="clear"></div>
				<?php
			}
		endif;
	}
}



if(!function_exists('apollo13framework_albums_list_look_classes')) {
	/**
	 * Return classes for bricks container of albums list
	 *
	 * @param int|null $columns number of columns in container
	 *
	 * @return string classes
	 */
	function apollo13framework_albums_list_look_classes( $columns = null ) {
		global $apollo13framework_a13;

		//items design variables
		$albums_look = $apollo13framework_a13->get_option( 'albums_list_album_look' );
		$bricks_look_classes = ' variant-'.$albums_look;
		if ( $columns !== null ) {
			$bricks_look_classes .= ' albums-columns-' . $columns;
		}

		//hover effect
		$hover_effect   = $apollo13framework_a13->get_option( 'albums_list_bricks_hover' );
		$bricks_look_classes .= ' hover-effect-'.$hover_effect;

		if($albums_look === 'overlay'){
			//position
			$title_position = explode('_', $apollo13framework_a13->get_option( 'albums_list_album_overlay_title_position' ) );
			$bricks_look_classes .= (is_array($title_position) && sizeof($title_position) === 2 )? ' title-'.$title_position[0].' title-'.$title_position[1] : '';

			//cover - not hovering
			if( $apollo13framework_a13->get_option( 'albums_list_album_overlay_cover' ) === 'on' ){
				$bricks_look_classes .= ' cover-no-hover';
			}

			//cover - hovering
			if( $apollo13framework_a13->get_option( 'albums_list_album_overlay_cover_hover' ) === 'on' ){
				$bricks_look_classes .= ' cover-hover';
			}

			//gradient - not hovering
			if( $apollo13framework_a13->get_option( 'albums_list_album_overlay_gradient' ) === 'on' ){
				$bricks_look_classes .= ' gradient-no-hover';
			}

			//gradient - hovering
			if( $apollo13framework_a13->get_option( 'albums_list_album_overlay_gradient_hover' ) === 'on' ){
				$bricks_look_classes .= ' gradient-hover';
			}

			//texts visibility - not hovering
			if( $apollo13framework_a13->get_option( 'albums_list_album_overlay_texts' ) === 'on' ){
				$bricks_look_classes .= ' texts-no-hover';
			}

			//texts visibility - hovering
			if( $apollo13framework_a13->get_option( 'albums_list_album_overlay_texts_hover' ) === 'on' ){
				$bricks_look_classes .= ' texts-hover';
			}
		}
		else{
			$title_position = $apollo13framework_a13->get_option( 'albums_list_album_under_title_position' );
			$bricks_look_classes .= ' title-'.$title_position;
		}

		return $bricks_look_classes;
	}
}



if(!function_exists('apollo13framework_albums_list_item')) {
	/**
	 * Prints HTML for item or items(when query is passed) of album item
	 *
	 * @param WP_Query|null $query        Query with list of post. If not given, will use global $post
	 *
	 * @param string        $displayed_in where item is displayed
	 *
	 * @param bool|int      $columns
	 *
	 * @return string HTML of items
	 *
	 */
	function apollo13framework_albums_list_item( $query = null, $displayed_in = 'album-list', $columns = false ) {
		global $apollo13framework_a13, $post;

		$album_list = $displayed_in === 'album-list';
		$shortcode = $displayed_in === 'shortcode';

		$number_of_posts = 1; //if it is WP Bakery post grid, then we don't have whole query
		if ( is_object( $query ) ) {
			$number_of_posts = $query->post_count;
		}

		$html = '';

		for ( $post_number = 0; $post_number < $number_of_posts; $post_number ++ ) {
			if ( is_object( $query ) ) {
				$query->the_post();
				$post_id = get_the_ID();
			} else {
				$post_id = $post->ID;
			}

			$href = get_the_permalink( $post_id );
			$category_string = '';
			$album_classes = '';

			//special thing when used in albums list
			if ( $album_list || $shortcode ) {
				//get work categories
				$terms = wp_get_post_terms( $post_id, A13FRAMEWORK_CPT_ALBUM_TAXONOMY, array( "fields" => "all" ) );

				//get all genres that item belongs to
				if ( count( $terms ) ) {
					foreach ( $terms as $term ) {
						$category_string .= ' data-category-' . esc_attr($term->term_id) . '="1"';
					}
				}

				//size of brick
				$brick_size = $apollo13framework_a13->get_meta( '_brick_ratio_x' );
				$album_classes = strlen( $brick_size ) ? ' w' . $brick_size : '';
			}

			$html .= '<div class="archive-item object-item' . esc_attr( $album_classes ) . '"' . $category_string/* escaped while preparing */ . ($album_list? ' id="album-' . esc_attr( $post_id ) . '"' : '').'>';

			//simple for albums list or shortcode
			if ( $album_list || $shortcode ) {
				$html .= apollo13framework_make_album_image( $post_id, '', $columns );
			} //fixed for other place
			else {
				//prepare image in proportion
				$image_width       = 800;/* 800 - not depending on current theme settings for albums list */
				$brick_proportion  = $apollo13framework_a13->get_option( 'albums_list_bricks_proportions_size' );
				$height_proportion = apollo13framework_calculate_height_proportion( $brick_proportion );
				$image_height      = $image_width * $height_proportion;

				$html .= apollo13framework_make_album_image( $post_id, array( $image_width, $image_height ) );
			}

			$cover_color = $apollo13framework_a13->get_meta( '_cover_color' );
			if ( $cover_color === '' || $cover_color === false || $cover_color === 'transparent' ) {
				//no color - default to CSS value
				$html .= '<div class="cover"></div>';
			} else {
				$html .= '<div class="cover" style="background-color:' . esc_attr( $cover_color ) . ';"></div>';
			}

			$html .= '<div class="covering-image"></div>';
			$html .= '<div class="icon a13icon-plus"></div>';

			$html .= '<div class="caption">';

			if ( post_password_required( $post_id ) ) {

				$html .= '<div class="texts_group">';
				$html .= '<h2 class="post-title">';
				$html .= '<span class="fa fa-lock"></span>' . esc_html__( 'This content is password protected', 'rife-free' );
				$html .= '</h2>';

				$html .= '<div class="excerpt">';
				$html .= '<p>' . esc_html__( 'Click and enter your password to view content', 'rife-free' ) . '</p>';
				$html .= '</div>';
				$html .= '</div>';

			} else {

				$html .= '<div class="texts_group">';

				//return taxonomy for albums
				if ( $apollo13framework_a13->get_option( 'albums_list_categories' ) === 'on' ) {
					$html .= '<div class="album-categories">' . apollo13framework_album_posted_in( ', ' ) . '</div>';
				}
				//title
				$html .= the_title( '<h2 class="post-title">', '</h2>', false );

				$html .= '<div class="excerpt">';
				$html .= esc_html( $apollo13framework_a13->get_meta( '_subtitle' ) );
				$html .= '</div>';
				$html .= '</div>';

			}
			$html .= '</div>'; //.caption

			$html .= '<a href="' . esc_url($href) . '"></a>';
			$html .= apollo13framework_cpt_social($href, get_the_title());
			$html .= '</div>';
		}

		return $html;
	}
}