<?php
/**
 * The Template for displaying work items.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

global $apollo13framework_a13;
define( 'A13FRAMEWORK_WORK_PAGE', true );

if ( isset( $_GET['a13-ajax-get'] ) ) {
	get_template_part('single-work-ajax');
	return;
}

the_post();


if(post_password_required()){
	/* don't use the_content() as it also applies filters that we don't need here, if we are using custom password page */
	echo get_the_content();
}
else{
	get_header();
	apollo13framework_title_bar();
?>
	<article id="content" class="clearfix">
        <div class="content-limiter">
            <div id="col-mask">
                <div class="content-box">


	<?php
	// Disable default placement of addtoany widget but only in works
	remove_filter( 'the_content', 'A2A_SHARE_SAVE_add_to_content', 98 );

	$theme           = $apollo13framework_a13->get_meta( '_theme' );
	$id              = get_the_ID();
	$show_desc       = (int) ( get_post_meta( $id, '_enable_desc', true ) === 'on' );
	$is_text_content = strlen( $post->post_content ) > 0;

	if($theme === 'bricks'){
		$lightbox_off        = (int)( get_post_meta( $id, '_bricks_lightbox', true ) === 'off' );
		$cover_color         = get_post_meta( $id, '_slide_cover_color', true );
		$cover_color         = ( $cover_color === '' || $cover_color === false || $cover_color === 'transparent' ) ? '' : $cover_color;
		$bricks_look_classes = ' variant-overlay';
		$bricks_look_classes .= ' bricks-columns-' . get_post_meta( $id, '_brick_columns', true );
		$bricks_look_classes .= $is_text_content ? '' : ' work-content-off';

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

		                <div class="bricks-frame<?php echo esc_attr($bricks_look_classes); ?>">
			                <?php
				            //media collection as first element
			                apollo13framework_make_media_collection();
				            ?>
			                <div id="only-work-items-here"<?php echo ' data-margin="'.esc_attr($brick_margin).'" data-desc="'.esc_attr($show_desc).'" data-lightbox_off="'.esc_attr($lightbox_off).'" data-cover-color="'.esc_attr($cover_color).'"'; ?>>
				                <div class="grid-master"></div>
		                    </div>
	                    </div>
		<?php
	}

	elseif($theme === 'slider'){
		$show_desc      = get_post_meta( $id, '_enable_desc', true);
		$title_color    = get_post_meta( $id, '_slide_title_bg_color', true );
		$title_color    = ( $title_color === '' || $title_color === false || $title_color === 'transparent' ) ? '' : $title_color;
		$thumbs         = $apollo13framework_a13->get_meta( '_thumbs' );
		$thumbs_on_load = $apollo13framework_a13->get_meta( '_thumbs_on_load' );;
		$ken_scale      = $apollo13framework_a13->get_meta( '_ken_scale' );

		//slider proportions
		$slider_width_proportion = get_post_meta( $id, '_slider_width_proportion', true);
		$slider_height_proportion = get_post_meta( $id, '_slider_height_proportion', true);
		//if 0 or some other bad value then set it to 1
		$slider_width_proportion = (int)$slider_width_proportion === 0 ? 1 : $slider_width_proportion;

		$slider_opts = array(
			'autoplay'              => $apollo13framework_a13->get_meta( '_autoplay' ),
			'transition'            => $apollo13framework_a13->get_meta( '_transition' ),
			'fit_variant'           => $apollo13framework_a13->get_meta( '_fit_variant' ),
			'pattern'               => $apollo13framework_a13->get_meta( '_pattern' ),
			'gradient'              => $apollo13framework_a13->get_meta( '_gradient' ),
			'ken_burns_scale'       => strlen($ken_scale) ? $ken_scale : 120,
			'texts'                 => $show_desc,
			'title_color'           => $title_color,
			'transition_time'       => $apollo13framework_a13->get_option( 'work_slider_transition_time' ),
			'slide_interval'        => $apollo13framework_a13->get_option( 'work_slider_slide_interval' ),
			'thumbs'                => $thumbs,
			'thumbs_on_load'        => $thumbs_on_load,
			'socials'               => 'on',
			'window_high'           => $apollo13framework_a13->get_meta( '_slider_window_high' ),
			'ratio'                 => $slider_width_proportion.'/'.$slider_height_proportion,
			'extra_class'           => 'a13-main-slider',
			'main_slider'           => 'on',
		);

		apollo13framework_make_slider($slider_opts);
	}

	apollo13framework_single_work_text_content($is_text_content);

	apollo13framework_similar_works();
    apollo13framework_works_nav();
	?>
                </div>
            </div>
        </div>
    </article>
<?php
    get_footer();
}
