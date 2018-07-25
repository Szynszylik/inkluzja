<?php
/* Functions used by custom post type people */
if(!function_exists('apollo13framework_make_people_image')){
	/**
	 * Making cover for people in People list
	 *
	 * @param int          $item_id
	 * @param string|array $sizes
	 * @param bool|int        $columns
	 *
	 * @return string HTML of image
	 */
	function apollo13framework_make_people_image( $item_id, $sizes = '', $columns = false ){
        global  $apollo13framework_a13;

        if(empty($item_id)){
            $item_id = get_the_ID();
        }

        if( !is_array($sizes) ){
            $brick_size         = 1; //$apollo13framework_a13->get_meta('_brick_ratio_x', $item_id);
            $columns            = $columns === false? 3 : (int)$columns;
            $bricks_max_width   = 1920;//(int)$apollo13framework_a13->get_option( 'people_list_bricks_max_width' );
            $brick_margin       = 10;//(int)$apollo13framework_a13->get_option( 'people_list_brick_margin' );
	        $brick_proportion   = '1/1';$apollo13framework_a13->get_option( 'people_list_bricks_proportions_size' );

            /* brick_size can't be bigger then columns for calculations */
            $brick_size         = strlen($brick_size)? min((int)$brick_size, $columns) : 1;
            $ratio              = $brick_size/$columns;

	        //many possible sizes, but one RULE to rule them all
	        $image_width =  ceil($ratio * $bricks_max_width - (1-$ratio) * $brick_margin);

	        $height_proportion = apollo13framework_calculate_height_proportion($brick_proportion);

	        $image_height = $image_width*$height_proportion;

            $sizes = array($image_width, $image_height);
        }


        $src = apollo13framework_make_post_image( $item_id, $sizes, true );
        if ( $src === false ) {
            $src = get_theme_file_uri( 'images/holders/photo.png');
        }
        else{
	        //check for animated gifs
	        $file_type = wp_check_filetype( $src );
	        //if it is gif then it is probably animated gif, so lets use original file
	        if( $file_type['type'] === 'image/gif'){
		        $src = apollo13framework_make_post_image( $item_id, array('full'), true );
	        }
        }

	    $image_alt = '';
	    $image_title = '';
	    $image_id = get_post_thumbnail_id( $item_id );
	    if($image_id){
	        $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true);
	        $image_title = get_the_title( $image_id );
	    }

        return '<img src="'.esc_url($src).'" alt="'.esc_attr($image_alt).'"'.($image_title? ' title="'.esc_attr($image_title).'"' : '').' />';
    }
}



if(!function_exists('apollo13framework_display_items_from_query_people_list')) {
	/**
	 * @param bool|WP_Query $query
	 * @param array         $args
	 */
	function apollo13framework_display_items_from_query_people_list($query = false, $args = array()){
		if($query === false){
			global $wp_query;
			$query = $wp_query;
			$displayed_in = 'people-list';
		}
		else{
			$displayed_in = 'shortcode';
		}

		$default_args = array(
			'columns' => 3,
			'filter' => false,
		);

		$args = wp_parse_args($args, $default_args);

		/* show filter? */
		if($args['filter']){
			$query_args = array(
				'hide_empty' => true,
				'object_ids' => wp_list_pluck( $query->posts, 'ID' ),
				'taxonomy'   => A13FRAMEWORK_CPT_PEOPLE_TAXONOMY,
			);

			/** @noinspection PhpInternalEntityUsedInspection */
			$terms = get_terms( $query_args );

			apollo13framework_make_post_grid_filter($terms, 'people-filter');
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
			$people_list_page = defined( 'A13FRAMEWORK_PEOPLE_LIST_PAGE');

			if(!$ajax_call){
				?>
				<div class="bricks-frame people-bricks<?php echo esc_attr( apollo13framework_people_list_look_classes($args['columns']) ); ?>">
				<div class="people-grid-container"<?php
				//lazy load on
				if($people_list_page){
					$lazy_load        = false;
					$lazy_load_mode   = false;
					echo ' data-lazy-load="' . esc_attr( $lazy_load ) . '" data-lazy-load-mode="' . esc_attr( $lazy_load_mode ) . '"';
				}
				?>>
				<div class="grid-master"></div>
				<?php
			}

			while ( $query->have_posts() ) :
				echo apollo13framework_people_list_item($query, $displayed_in, $args['columns']);
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



if(!function_exists('apollo13framework_people_list_look_classes')) {
	/**
	 * Return classes for bricks container of people list
	 *
	 * @param int|null $columns number of columns in container
	 *
	 * @return string classes
	 */
	function apollo13framework_people_list_look_classes( $columns = null ) {
		$bricks_look_classes = ' variant-overlay cover-hover title-mid title-center texts-hover hover-effect-shift';
		$bricks_look_classes .= ' people-columns-' . $columns;

		return $bricks_look_classes;
	}
}



if(!function_exists('apollo13framework_people_list_item')) {
	/**
	 * Prints HTML for item or items(when query is passed) of people item
	 *
	 * @param WP_Query|null $query        Query with list of post. If not given, will use global $post
	 *
	 * @param string        $displayed_in where item is displayed
	 *
	 * @param bool|int          $columns
	 *
	 * @return string HTML of items
	 *
	 */
	function apollo13framework_people_list_item( $query = null, $displayed_in = 'people-list', $columns = false ) {
		global $apollo13framework_a13, $post;

		$people_list = $displayed_in === 'people-list';
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

			$category_string = '';
			$people_classes = '';

			//special thing when used in people list
			if ( $people_list || $shortcode ) {
				//get work categories
				$terms = wp_get_post_terms( $post_id, A13FRAMEWORK_CPT_PEOPLE_TAXONOMY, array( "fields" => "all" ) );

				//get all genres that item belongs to
				if ( count( $terms ) ) {
					foreach ( $terms as $term ) {
						$category_string .= ' data-category-' . esc_attr($term->term_id) . '="1"';
					}
				}

				//size of brick
				$brick_size = 1;//$apollo13framework_a13->get_meta( '_brick_ratio_x' );
				$people_classes = strlen( $brick_size ) ? ' w' . $brick_size : '';
			}

			$html .= '<div class="archive-item object-item' . esc_attr( $people_classes ) . '"' . $category_string/* escaped while preparing */ . ($people_list? ' id="people-' . esc_attr( $post_id ) . '"' : '').'>';

			//simple for people list or shortcode
			if ( $people_list || $shortcode ) {
				$html .= apollo13framework_make_people_image( $post_id, '', $columns );
			} //fixed for other place
			else {
				//prepare image in proportion
				$image_width       = 800;/* 800 - not depending on current theme settings for people list */
				$brick_proportion  = 1/1;//$apollo13framework_a13->get_option( 'people_list_bricks_proportions_size' );
				$height_proportion = apollo13framework_calculate_height_proportion( $brick_proportion );
				$image_height      = $image_width * $height_proportion;

				$html .= apollo13framework_make_people_image( $post_id, array( $image_width, $image_height ) );
			}

			$cover_color = $apollo13framework_a13->get_meta( '_overlay_bg_color' );
			if ( $cover_color === '' || $cover_color === false || $cover_color === 'transparent' ) {
				//no color - default to CSS value
				$html .= '<div class="cover"></div>';
			} else {
				$html .= '<div class="cover" style="background-color:' . esc_attr( $cover_color ). ';"></div>';
			}


			$text_color = $apollo13framework_a13->get_meta( '_overlay_font_color' );
			if ( $text_color === '' || $text_color === false || $text_color === 'transparent' ) {
				//no color - default to CSS value
				$text_style = '';
			} else {
				$text_style = ' style="color:' . $text_color . ';"';
			}

			$html .= '<div class="covering-image"></div>';

			$html .= '<div class="caption">';

			if ( post_password_required( $post_id ) ) {

				$html .= '<div class="texts_group"'.$text_style.'>';
				$html .= '<h2 class="post-title"'.$text_style.'>';
				$html .= '<span class="fa fa-lock"></span>' . esc_html__( 'This content is password protected', 'rife-free' );
				$html .= '</h2>';

				$html .= '<div class="excerpt"'.$text_style.'>';
				$html .= '<p>' . esc_html__( 'Click and enter your password to view content', 'rife-free' ) . '</p>';
				$html .= '</div>';
				$html .= '</div>';

			} else {

				$html .= '<div class="texts_group"'.$text_style.'>';

				//return taxonomy for people
				$html .= '<div class="subtitle">' . esc_html( $apollo13framework_a13->get_meta( '_subtitle' ) ) . '</div>';

				//title
				$html .= the_title( '<h2 class="post-title"'.$text_style.'>', '</h2>', false );

				$html .= '<div class="people-desc"'.$text_style.'>';
				$html .= get_the_content();
				$html .= '</div>';

				//social icons
				$all_meta    = get_post_meta( $post->ID );
				$socials_list = $apollo13framework_a13->get_social_icons_list('empty');
				foreach( $socials_list as $id=>$social){
					$socials_list[$id] = isset($all_meta['_'.$id])? $all_meta['_'.$id][0] : '';
				}
				$html .= apollo13framework_social_icons( $apollo13framework_a13->get_option( 'people_socials_color' ), $apollo13framework_a13->get_option( 'people_socials_color_hover' ), $socials_list );

				$html .= '</div>';

			}
			$html .= '</div>'; //.caption

			$html .= '</div>';
		}

		return $html;
	}
}