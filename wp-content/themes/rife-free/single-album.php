<?php
/**
 * The Template for displaying album items.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

global $apollo13framework_a13;
define( 'A13FRAMEWORK_ALBUM_PAGE', true );

the_post();

if(post_password_required()){
	/* don't use the_content() as it also applies filters that we don't need here, if we are using custom password page */
	echo get_the_content();
}
else{
	get_header();
	apollo13framework_title_bar();

	$theme              = $apollo13framework_a13->get_meta('_theme');
	$id                 = get_the_ID();
	$cover_color        = get_post_meta( $id, '_slide_cover_color', true);
	$cover_color        = ($cover_color === '' || $cover_color === false || $cover_color === 'transparent')? '' : $cover_color;

	if($theme === 'bricks'){
		$show_desc           = (int)( get_post_meta( $id, '_enable_desc', true) === 'on' );
		$proofing            = (int)( get_post_meta( $id, '_proofing', true ) === 'on' );
		$lightbox_off        = (int)( get_post_meta( $id, '_bricks_lightbox', true ) === 'off' );
		$bricks_look_classes = ' variant-overlay';
		$content_column      = $apollo13framework_a13->get_meta( '_album_content' );
		$bricks_look_classes .= ' bricks-columns-' . get_post_meta( $id, '_brick_columns', true );
		$bricks_look_classes .= ' album-content-' . ( $content_column === 'off' ? 'off' : 'on album-content-on-the-' . $content_column );
		//position
		$title_position = explode('_', $apollo13framework_a13->get_meta( '_bricks_title_position' ) );
		$bricks_look_classes .= (is_array($title_position) && sizeof($title_position) === 2 )? ' title-'.$title_position[0].' title-'.$title_position[1] : '';

		//hover effect
		$hover_effect       = get_post_meta( $id, '_bricks_hover', true);
		$bricks_look_classes .= ' hover-effect-'.$hover_effect;

		//cover - not hovering
		if( $apollo13framework_a13->get_meta( '_bricks_overlay_cover' ) === 'on' ){
			$bricks_look_classes .= ' cover-no-hover';
		}

		//cover - hovering
		if( $apollo13framework_a13->get_meta( '_bricks_overlay_cover_hover' ) === 'on' ){
			$bricks_look_classes .= ' cover-hover';
		}

		//gradient - not hovering
		if( $apollo13framework_a13->get_meta( '_bricks_overlay_gradient' ) === 'on' ){
			$bricks_look_classes .= ' gradient-no-hover';
		}

		//gradient - hovering
		if( $apollo13framework_a13->get_meta( '_bricks_overlay_gradient_hover' ) === 'on' ){
			$bricks_look_classes .= ' gradient-hover';
		}

		//texts visibility - not hovering
		if( $apollo13framework_a13->get_meta( '_bricks_overlay_texts' ) === 'on' ){
			$bricks_look_classes .= ' texts-no-hover';
		}

		//texts visibility - hovering
		if( $apollo13framework_a13->get_meta( '_bricks_overlay_texts_hover' ) === 'on' ){
			$bricks_look_classes .= ' texts-hover';
		}

		$brick_margin       = (int)get_post_meta( $id, '_brick_margin', true);//no px
		?>
	<article id="content" class="clearfix">
        <div class="content-limiter">
            <div id="col-mask">
                <div class="content-box">
	                <?php
	                //filter
	                if($apollo13framework_a13->get_option( 'album_bricks_filter' ) === 'on'){
		                apollo13framework_print_media_filters();
	                }
	                //proofing filter
	                if($proofing){
		                apollo13framework_print_proofing_filters();
	                }

	                ?>
	                <div class="bricks-frame<?php echo esc_attr($bricks_look_classes); ?>">
		                <?php if( $content_column !== 'off'){ ?>
			            <div class="album-content">
				            <div class="inside">
					            <?php
					            apollo13framework_albums_nav();

					            if( $apollo13framework_a13->get_option( 'album_content_categories') === 'on'){
						            echo '<div class="album-categories">'.apollo13framework_album_posted_in(', ').'</div>';
					            }

					            if( $apollo13framework_a13->get_option( 'album_content_title') === 'on'){
						            echo '<h2 class="post-title">'.get_the_title().'</h2>';
					            }
					            ?>
				                <div class="real-content">
				                    <?php
				                    add_filter( 'the_content', 'apollo13framework_cpt_meta_data', 20 );
				                    the_content(); ?>
				                </div>
				            </div>
			            </div>
		                <?php } ?>
						<?php
			            //media collection as first element
		                apollo13framework_make_media_collection();
			            ?>
		                <div id="only-album-items-here"<?php echo ' data-margin="'.esc_attr($brick_margin).
		                                                          '" data-desc="'.esc_attr($show_desc).
		                                                          '" data-proofing="'.esc_attr($proofing).
		                                                          '" data-lightbox_off="'.esc_attr($lightbox_off).
		                                                          '" data-cover-color="'.esc_attr($cover_color).'"'; ?>>
			                <div class="grid-master"></div>
	                    </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
		<?php
	}

	elseif($theme === 'slider'){
		$show_desc      = get_post_meta( $id, '_enable_desc', true);
		$title_color    = get_post_meta( $id, '_slide_title_bg_color', true );
		$title_color    = ( $title_color === '' || $title_color === false || $title_color === 'transparent' ) ? '' : $title_color;
		$thumbs         = $apollo13framework_a13->get_meta( '_thumbs' );
		$thumbs_on_load = $apollo13framework_a13->get_meta( '_thumbs_on_load' );
		$ken_scale      = $apollo13framework_a13->get_meta( '_ken_scale' );

		$slider_opts = array(
			'autoplay'              => $apollo13framework_a13->get_meta( '_autoplay' ),
			'transition'            => $apollo13framework_a13->get_meta( '_transition' ),
			'fit_variant'           => $apollo13framework_a13->get_meta( '_fit_variant' ),
			'pattern'               => $apollo13framework_a13->get_meta( '_pattern' ),
			'gradient'              => $apollo13framework_a13->get_meta( '_gradient' ),
			'ken_burns_scale'       => strlen($ken_scale) ? $ken_scale : 120,
			'texts'                 => $show_desc,
			'title_color'           => $title_color,
			'transition_time'       => $apollo13framework_a13->get_option( 'album_slider_transition_time' ),
			'slide_interval'        => $apollo13framework_a13->get_option( 'album_slider_slide_interval' ),
			'thumbs'                => $thumbs,
			'thumbs_on_load'        => $thumbs_on_load,
			'socials'               => 'on',
			'window_high'           => 'on',
			'main_slider'           => 'on'
		);

		apollo13framework_make_slider($slider_opts);
	}

	elseif($theme === 'scroller' || $theme === 'scroller-parallax'){
		//collect all options
		$flickity_options = array();

		//from album
		$flickity_options['wrapAround']         = get_post_meta( $id, '_scroller_wrap_around', true ) === 'on';
		$flickity_options['contain']            = get_post_meta( $id, '_scroller_contain', true ) === 'on';
		$flickity_options['freeScroll']         = get_post_meta( $id, '_scroller_free_scroll', true ) === 'on';
		$flickity_options['prevNextButtons']    = get_post_meta( $id, '_scroller_arrows', true ) === 'on';
		$flickity_options['pageDots']           = get_post_meta( $id, '_scroller_dots', true ) === 'on';
		$flickity_options['autoPlay']           = get_post_meta( $id, '_scroller_autoplay', true ) === 'on';
		$flickity_options['a13Effect']          = get_post_meta( $id, '_scroller_effect', true );
		$flickity_options['a13CellWidth']       = get_post_meta( $id, '_scroller_cell_width', true );
		$flickity_options['a13CellWidthMobile'] = get_post_meta( $id, '_scroller_cell_width_mobile', true );
		$flickity_options['a13ShowDesc']        = get_post_meta( $id, '_enable_desc', true) === 'on';
		$flickity_options['a13Parallax']        = $theme === 'scroller-parallax';
		$flickity_options['a13MainSlider']      = true;
		$flickity_options['a13WindowHigh']      = true;
		if ( $flickity_options['autoPlay'] ) {
			$time                                     = (float) get_post_meta( $id, '_scroller_autoplay_time', true ) * 1000;
			$flickity_options['autoPlay']             = $time;
			$flickity_options['pauseAutoPlayOnHover'] = get_post_meta( $id, '_scroller_pause_autoplay', true ) === 'on';
		}

		//media collection as first element
		apollo13framework_make_scroller($flickity_options);
	}

    get_footer();
}
