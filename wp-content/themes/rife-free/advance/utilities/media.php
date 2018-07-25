<?php
/**
 * Functions that are connected to handling media
 */


if(!function_exists('apollo13framework_get_top_image_video')) {
	/**
	 * Function that return featured image or video for post/page
	 *
	 * @param bool|false $link_it   should image link to post/page
	 * @param string     $args  for bonus options:
	 *                          force_image - even when video is selected, it will display image
	 *                          return_src  - instead of whole html it will return only URL
	 *                          height      - used when for dynamic bricks
     *                          parallax    - image will scroll with parallax effect
     *                          full_size   - will return image in full size
	 *
	 *
	 * @return bool|mixed|string|void
	 */
	function apollo13framework_get_top_image_video( $link_it = false, $args = '' ) {
        global $apollo13framework_a13;

        $html = '';

        $default_args = array(
            'force_image'	=> false,
            'return_src'	=> false,
            'height'        => 0,
            'full_size'     => false,
            'page_type'     => ''
        );

        $args = wp_parse_args($args, $default_args);

        $sizes = array(
            'full'                         => array( 'full', 'full' ),
            'sidebar-size'                 => array( 100, 100 ),
            'apollo-post-thumb'            => array( 800, 0 ),
            'apollo-post-thumb-smaller'    => array( 740, 0 ), //for 700 and 740 layouts
            'apollo-post-thumb-big'        => array( 1080, 0 ),
            'apollo-blog-posts-horizontal' => array( 420, 0 ),
        );

        if(apollo13framework_is_no_property_page()){
            return $html; //empty string
        }
        $page_type = apollo13framework_what_page_type_is_it();
        $is_post = $page_type['post'];
        $is_page = $page_type['page'];
        $is_work = $page_type['work'];
        $is_post_list = $page_type['blog_type'];

        //check if media should be displayed
        if(
            ($is_post && $apollo13framework_a13->get_option( 'post_media') == 'off')
            ||
            ($is_post_list && $apollo13framework_a13->get_option( 'blog_media') == 'off')
        ){
            return $html; //empty string
        }

        $post_id        = get_the_ID();
        $img_or_vid     = get_post_meta($post_id, '_image_or_video', true);
        $img_or_vid     = strlen($img_or_vid)? $img_or_vid : 'post_image'; //default value for albums, or other pages when displayed on search results

        $image_video    = $apollo13framework_a13->get_option( 'blog_videos') === 'off' && $is_post_list;

        $thumb_size = 'apollo-post-thumb'; //default for post

        if($is_page || $is_post || $is_work){
            $layout = $is_page? $apollo13framework_a13->get_meta('_content_layout', $post_id) : $apollo13framework_a13->get_option( 'post_content_layout');
            $full_layouts =  array(
                'full_padding',
                'full',
            );
            $small_layouts =  array(
                'left',
                'left_padding',
                'right',
                'right_padding',
            );

            if($args['full_size']){
                $thumb_size = 'full';
            }
            elseif(in_array($layout, $full_layouts)){
                $thumb_size = 'apollo-post-thumb-big';
            }
            elseif(in_array($layout, $small_layouts)){
                $thumb_size = 'apollo-post-thumb-smaller';
            }
            else{
                if( defined('A13FRAMEWORK_NO_SIDEBARS') || $apollo13framework_a13->get_meta( '_widget_area' ) == 'off'){
                    $thumb_size = 'apollo-post-thumb-big';
                }
            }
        }
        elseif($is_post_list){
            if($apollo13framework_a13->get_option( 'blog_post_look') === 'horizontal'){
                $thumb_size = 'apollo-blog-posts-horizontal';
            }
            else{
                $thumb_size = 'apollo-blog';
                $brick_size         = $apollo13framework_a13->get_meta('_brick_ratio_x', $post_id);
                $columns            = isset($args['brick_columns']) ? (int)$args['brick_columns'] : (int)$apollo13framework_a13->get_option( 'blog_brick_columns' );
                $bricks_max_width   = (int)$apollo13framework_a13->get_option( 'blog_bricks_max_width' );
                $brick_margin       = (int)$apollo13framework_a13->get_option( 'blog_brick_margin' );

                /* brick_size can't be bigger then columns for calculations */
                $brick_size         = strlen($brick_size)? min((int)$brick_size, $columns) : 1;
                $ratio              = $brick_size/$columns;

                //many possible sizes, but one RULE to rule them all
                $image_width =  ceil($ratio * $bricks_max_width - (1-$ratio) * $brick_margin);
                $sizes[$thumb_size] = array($image_width, $args['height']);
            }
        }

        if( $args['force_image'] || $img_or_vid === 'post_image' ){
            $is_parallax = $apollo13framework_a13->get_meta( '_image_parallax' ) === 'on';
            if($args['return_src']){
                $html = apollo13framework_make_post_image($post_id, $sizes[$thumb_size], true);
            }
            elseif($is_parallax){
	            $img_src = apollo13framework_make_post_image($post_id, $sizes[$thumb_size], true);
                if($img_src !== false ){
                    $parallax_height = $apollo13framework_a13->get_meta('_image_parallax_height', $post_id);
                    $html = '<div class="item-image post-media a13-parallax" style="background-image:url('.$img_src.'); height:'.$parallax_height.';" data-a13-parallax-type="tb">';
                    if($link_it){
                        $html .= '<a href="'.esc_url(get_permalink()).'"></a>';
                    }
                    $html .= '</div>';
                }
            }
            else{
                $additional_class = '';
                $src = apollo13framework_make_post_image( $post_id, $sizes['full'], true );
                //check for animated gifs
                $file_type = wp_check_filetype( $src );
                //if it is gif then it is probably animated gif, so lets use original file
                if( $file_type['type'] === 'image/gif'){
                    $img = apollo13framework_make_post_image($post_id, $sizes['full']);
                    $additional_class = ' animated-gif';
                }
                else{
                    $img = apollo13framework_make_post_image($post_id, $sizes[$thumb_size]);
                }

                if( !empty( $img ) ){
                    if($link_it){
                        $img = '<a href="'.esc_url(get_permalink()).'">'.$img.'</a>';
                    }

                    $html = '<div class="item-image post-media'.esc_attr($additional_class).'">'.$img.'</div>';
                }
            }
        }

        elseif( $img_or_vid === 'post_slider' ){
	        if( function_exists('get_post_gallery_ids') ){
		        $slider_images_ids = get_post_gallery_ids($post_id);
		        $number_of_images = sizeof($slider_images_ids);

		        if($number_of_images === 0 || ($number_of_images === 1 && $slider_images_ids[0] === '')){
					//no images for us
			        return false;
		        }

                $quality = (int)$apollo13framework_a13->get_option( 'a13ir_image_quality' );
                $quality = ($quality > 0 && $quality < 100) ? $quality : 90;
                if($sizes[$thumb_size][0] === 'full'){
                    $size = 'full';
                }
                else{
		            $size = array( $sizes[$thumb_size][0], $sizes[$thumb_size][1], 'apollo13_image' => true, 'quality' => $quality );
                }

		        foreach($slider_images_ids as $slide){
			        $attachment = wp_get_attachment_image_src( $slide, $size );
			        $html .= '<img src="'.$attachment[0].'" alt="" />';

		        }

		        $html = '<div class="item-slider post-media">'.$html.'</div>';
	        }
        }

        elseif( $img_or_vid === 'post_video' ){
            //featured image instead of video?
            if($image_video){
	            $html = apollo13framework_get_top_image_video($link_it, array_merge($args, array('force_image' => true )));
            }
            else{
                $src = get_post_meta($post_id, '_post_video', true);
                if( !empty( $src ) ){
                    $html = '<div class="item-video post-media">';

                    $width = $sizes[$thumb_size][0];
                    //in case of "full"
                    if(!is_numeric($width)){
                        global $content_width;
                        $width = $content_width;
                        $height = 0;
                    }
                    else{
                        $height = $sizes[$thumb_size][1];
                    }

                    if( $height == 0){
                        $height = ceil((9/16) * $width);
                    }

                    $media_dimensions = array(
                        'width' => $width,
                        'height' => $height
                    );
                    $v_code = wp_oembed_get($src, $media_dimensions);

                    //if no code, try theme function
                    if($v_code === false){
                        $html .= apollo13framework_get_movie($src, $width, $height);
                    }
                    else{
                        $html .= $v_code;
                    }
                    $html .= '</div>';
                }
            }
        }

        return $html;
    }
}
if(!function_exists('apollo13framework_top_image_video')){
    /**
     * Function that prints featured image or video for post/page
     *
     * @param bool|false $link_it   should image link to post/page
     * @param string     $args  for bonus options:
     *                          force_image - even when video is selected, it will display image
     *                          return_src  - instead of whole html it will return only URL
     *                          height      - used when for dynamic bricks
     *                          parallax    - image will scroll with parallax effect
     *
     */
	function apollo13framework_top_image_video($link_it = false, $args = ''){
        echo apollo13framework_get_top_image_video($link_it, $args);
    }
}


if(!function_exists('apollo13framework_make_post_image')){
	/**
	 * Making featured images
	 *
	 * @param int        $post_id post/page id
	 * @param array      $sizes size of image for resizing script
	 * @param bool|false $only_src should only src of image be returned
	 *
	 * @return bool|mixed|void  src or <img> HTML
	 */
	function apollo13framework_make_post_image( $post_id, $sizes, $only_src = false ){
        global $apollo13framework_a13;

        if(empty($post_id)){
            $post_id = get_the_ID();
        }
        if ( has_post_thumbnail( $post_id) ) {
            if($sizes[0] === 'full'){
                $size = 'full';
            }
            else{
                $quality = (int)$apollo13framework_a13->get_option( 'a13ir_image_quality' );
                $quality = ($quality > 0 && $quality < 100) ? $quality : 90;
                $size = array( $sizes[0], $sizes[1], 'apollo13_image' => true, 'quality' => $quality );
            }

            if($only_src){
                $attachment = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
                return $attachment[0];
            }
            else{
                return get_the_post_thumbnail( $post_id, $size );
            }
        }

        return false;
    }
}


if(!function_exists('apollo13framework_detect_movie')){
	/**
	 * Detection of type of movie
	 *
	 * @param string $src   video link
	 *
	 * @return array        returns array(type, video_id)
	 */
	function apollo13framework_detect_movie($src){
        //used to check if it is audio file
        $parts = pathinfo($src);
        $ext = isset($parts['extension'])? strtolower($parts['extension']) : false;

        //http://www.youtube.com/watch?v=e8Z0YTWDFXI
        if (preg_match("/(youtube\.com\/watch\?)?v=([a-zA-Z0-9\-_]+)&?/s", $src, $matches)){
            $type = 'youtube';
            $video_id = $matches[2];
        }
        //http://youtu.be/e8Z0YTWDFXI
        elseif (preg_match("/(https?:\/\/youtu\.be\/)([a-zA-Z0-9\-_]+)&?/s", $src, $matches)){
            $type = 'youtube';
            $video_id = $matches[2];
        }
        // regexp $src http://vimeo.com/16998178
        elseif (preg_match("/(vimeo\.com\/)([0-9]+)/s", $src, $matches)){
            $type = 'vimeo';
            $video_id = $matches[2];
        }
        elseif(strlen($ext) && in_array($ext, array('mp3', 'ogg', 'm4a'))){
            $type = 'audio';
            $video_id = $src;
        }
        else{
            $type = 'html5';
            $video_id = $src;
        }

        return array(
            'type' => $type,
            'id' => $video_id
        );
    }
}


if(!function_exists('apollo13framework_get_movie_thumb_src')){
	/**
	 * Returns movie thumb(for youtube, vimeo and embeded video)
	 *
	 * @param array     $video_data data received from apollo13framework_detect_movie function
	 * @param string    $thumb      image representing video
	 *
	 * @return bool|string  image src if video type is matched, false otherwise
	 */
	function apollo13framework_get_movie_thumb_src( $video_data, $thumb = '' ){
        if(!empty($thumb)){
            return $thumb;
        }

        $type = $video_data['type'];
        $video_id = $video_data['id'];

        if ( $type == 'youtube' ){
            return 'https://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg';
        }
        elseif ( $type == 'vimeo' ){
            return get_theme_file_uri( 'images/holders/vimeo.png');
        }
        elseif ( $type == 'html5' ){
            return get_theme_file_uri( 'images/holders/video.png');
        }

        return false;
    }
}


if(!function_exists('apollo13framework_get_movie_link')){
	/**
	 * Returns movie link to insert it in iframe
	 *
	 * @param array $video_data data received from apollo13framework_detect_movie function
	 *
	 * @return bool|string iframe src if video type is matched, false otherwise
	 */
	function apollo13framework_get_movie_link( $video_data ){
        $type       = $video_data['type'];
        $video_id   = $video_data['id'];

        if ( $type === 'youtube' ){
            return 'https://www.youtube.com/embed/'.$video_id.'?enablejsapi=1&amp;controls=1&amp;fs=1&amp;hd=1&amp;rel=0&amp;loop=0&amp;rel=0&amp;showinfo=1&amp;showsearch=0&amp;wmode=transparent';
        }
        elseif ( $type === 'vimeo' ){
            return 'https://player.vimeo.com/video/'.$video_id.'?api=1&amp;title=1&amp;loop=0';
        }
        else{
            return false;
        }
    }
}


if(!function_exists('apollo13framework_get_movie')){
	/**
	 * Returns movie iframe or link to movie
	 *
	 * @param string    $src link to movie
	 * @param int       $width  width of movie
	 * @param int       $height height of movie
	 *
	 * @return string   HTML of iframe, or code of wp_video_shortcode
	 */
	function apollo13framework_get_movie( $src, $width = 295, $height = 0 ){
        if( $height == 0){
            $height = ceil((9/16) * $width);
        }

        $video_data  = apollo13framework_detect_movie($src);
        $type       = $video_data['type'];

	    if( $type === 'html5' ){
		    return wp_video_shortcode( array( 'src' =>  $src ) );
	    }
	    else{
	        $link       = apollo13framework_get_movie_link($video_data);

	        return '<iframe data-vid-id="'.$video_data['video_id'].'" id="a13-crazy'.$type . mt_rand() . '" style="height: ' . $height . 'px; width: ' . $width . 'px; border: none;" src="' . esc_url($link) . '" allowfullscreen ></iframe>';
	    }
    }
}



/**
 * based on wp_audio_shortcode function for printing HTML for theme audio player for single audio track
 *
 * @see wp_audio_shortcode()
 *
 * @param array $attr attributes for audio shortcode
 *
 * @return bool|string HTML of audio player or false if can't proceed
 */
function apollo13framework_audio( $attr ) {
	static $instances = 0;
	$instances++;

	$audio = null;
	$post_id = 0;
	$default_types = wp_get_audio_extensions();
	$defaults_atts = array(
		'src'      => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'none'
	);
	foreach ( $default_types as $type ) {
		$defaults_atts[$type] = '';
	}

	$atts = shortcode_atts( $defaults_atts, $attr );

	$primary = false;
	if ( ! empty( $atts['src'] ) ) {
		$type = wp_check_filetype( $atts['src'], wp_get_mime_types() );
		if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
			return sprintf( '<a class="wp-embedded-audio" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
		}
		$primary = true;
		array_unshift( $default_types, 'src' );
	} else {
		foreach ( $default_types as $ext ) {
			if ( ! empty( $atts[ $ext ] ) ) {
				$type = wp_check_filetype( $atts[ $ext ], wp_get_mime_types() );
				if ( strtolower( $type['ext'] ) === $ext ) {
					$primary = true;
				}
			}
		}
	}

	if ( ! $primary ) {
		$audios = get_attached_media( 'audio', $post_id );
		if ( empty( $audios ) ) {
			return false;
		}

		$audio = reset( $audios );
		$atts['src'] = wp_get_attachment_url( $audio->ID );
		if ( empty( $atts['src'] ) ) {
			return false;
		}

		array_unshift( $default_types, 'src' );
	}

	wp_enqueue_style( 'mediaelement' );
	wp_enqueue_script( 'mediaelement' );

	$html_atts = array(
		'class'    => '',
		'id'       => sprintf( 'a13-audio-%d-%d', $post_id, $instances ),
		'loop'     => wp_validate_boolean( $atts['loop'] ),
		'autoplay' => wp_validate_boolean( $atts['autoplay'] ),
		'preload'  => $atts['preload'],
		'style'    => 'width: 100%; visibility: hidden;',
	);

	// These ones should just be omitted altogether if they are blank
	foreach ( array( 'loop', 'autoplay', 'preload' ) as $a ) {
		if ( empty( $html_atts[$a] ) ) {
			unset( $html_atts[$a] );
		}
	}

	$attr_strings = array();
	foreach ( $html_atts as $k => $v ) {
		$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
	}

	/** @noinspection HtmlUnknownAttribute */
	$html = sprintf( '<audio %s controls="controls">', join( ' ', $attr_strings ) );

	$file_url = '';
	$source = '<source type="%s" src="%s" />';
	foreach ( $default_types as $fallback ) {
		if ( ! empty( $atts[ $fallback ] ) ) {
			if ( empty( $file_url ) ) {
				$file_url = $atts[ $fallback ];
			}
			$type = wp_check_filetype( $atts[ $fallback ], wp_get_mime_types() );
			$url = esc_url( add_query_arg( '_', $instances, $atts[ $fallback ] ) );
			$html .= sprintf( $source, $type['type'], esc_url( $url ) );
		}
	}

	$html .= wp_mediaelement_fallback( $file_url );
	$html .= '</audio>';

	return $html;
}


/**
 * based on wp_playlist_shortcode function for printing HTML for theme audio player for many audio tracks
 *
 * @param array $ids IDs of tracks to include in playlist
 *
 * @return string HTML of audio player for many tracks
 */
function apollo13framework_playlist( $ids ) {
	//based on wp_playlist_shortcode
	static $instance = 0;
	$instance++;

	if ( empty( $ids ) ) {
		return '';
	}

	$args = array(
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		'post_mime_type' => 'audio',
		'order' => 'ASC',
		'orderby' => 'post__in',
		'include' => $ids
	);

	$_attachments = get_posts( $args );
	$attachments = array();
	foreach ( $_attachments as $key => $val ) {
		$attachments[$val->ID] = $_attachments[$key];
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id ) . "\n";
		}
		return $output;
	}

	$data = array(
		'type' => 'audio',
		// don't pass strings to JSON, will be true in JS
		'tracklist' => false,
		'tracknumbers' => false,
		'images' => false,
		'artists' => false,
	);

	$tracks = array();
	foreach ( $attachments as $attachment ) {
		$url = wp_get_attachment_url( $attachment->ID );
		$ftype = wp_check_filetype( $url, wp_get_mime_types() );
		$track = array(
			'src' => $url,
			'type' => $ftype['type'],
			'title' => $attachment->post_title,
			'caption' => $attachment->post_excerpt,
			'description' => $attachment->post_content
		);

		$track['meta'] = array();
		$meta = wp_get_attachment_metadata( $attachment->ID );
		if ( ! empty( $meta ) ) {

			foreach ( wp_get_attachment_id3_keys( $attachment ) as $key => $label ) {
				if ( ! empty( $meta[ $key ] ) ) {
					$track['meta'][ $key ] = $meta[ $key ];
				}
			}
		}

		$tracks[] = $track;
	}
	$data['tracks'] = $tracks;

	ob_start();

	if ( 1 === $instance ) {
		add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
		add_action( 'admin_footer', 'wp_underscore_playlist_templates', 0 );
	} ?>
<div class="a13-audio-playlist">
	<audio controls="controls" preload="none" style="visibility: hidden"></audio>
	<div class="playlist-next skip-button"></div>
	<div class="playlist-prev skip-button"></div>
	<noscript>
	<ol><?php
	foreach ( $attachments as $att_id => $attachment ) {
		printf( '<li>%s</li>', wp_get_attachment_link( $att_id ) );
	}
	?></ol>
	</noscript>
	<script type="application/json" class="a13-playlist-script"><?php echo wp_json_encode( $data ) ?></script>
</div>
	<?php
	return ob_get_clean();
}


/**
 * based on wp_video_shortcode function for printing HTML for emeded video
 *
 * @see wp_video_shortcode()
 *
 * @param array $attr                           attributes for audio shortcode
 * @param bool|false $dont_load_video_library   switch to disable loading WP JS for video
 *
 * @return bool|string HTML of video player or false if can't proceed
 */
function apollo13framework_video( $attr, $dont_load_video_library = false ) {
	global $content_width, $apollo13framework_a13;
	$post_id = 0;

	static $instances = 0;
	$instances ++;


	$video = null;

	$default_types = wp_get_video_extensions();
	$defaults_atts = array(
		'src'      => '',
		'poster'   => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'metadata',
		'width'    => 640,
		'height'   => 360,
	);

	foreach ( $default_types as $type ) {
		$defaults_atts[ $type ] = '';
	}

	$atts = shortcode_atts( $defaults_atts, $attr, 'video' );

	// if the video is bigger than the theme
	if ( ! empty( $content_width ) && $atts['width'] > $content_width ) {
		$atts['height'] = round( ( $atts['height'] * $content_width ) / $atts['width'] );
		$atts['width']  = $content_width;
	}

	$yt_pattern = '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#';

	$primary = false;
	if ( ! empty( $atts['src'] ) ) {
		if ( ! preg_match( $yt_pattern, $atts['src'] ) ) {
			$type = wp_check_filetype( $atts['src'], wp_get_mime_types() );
			if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
				return sprintf( '<a class="wp-embedded-video" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
			}
		}
		$primary = true;
		array_unshift( $default_types, 'src' );
	} else {
		foreach ( $default_types as $ext ) {
			if ( ! empty( $atts[ $ext ] ) ) {
				$type = wp_check_filetype( $atts[ $ext ], wp_get_mime_types() );
				if ( strtolower( $type['ext'] ) === $ext ) {
					$primary = true;
				}
			}
		}
	}

	if ( ! $primary ) {
		$videos = get_attached_media( 'video', $post_id );
		if ( empty( $videos ) ) {
			return false;
		}

		$video       = reset( $videos );
		$atts['src'] = wp_get_attachment_url( $video->ID );
		if ( empty( $atts['src'] ) ) {
			return false;
		}

		array_unshift( $default_types, 'src' );
	}

	if(!$dont_load_video_library){
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

    $lightbox = $apollo13framework_a13->get_option( 'apollo_lightbox' );

	$html_atts = array(
		'class'    => $lightbox === 'lightGallery' ? 'lg-video-object lg-html5' : '',
		'id'       => sprintf( 'a13-video-%d-%d', $post_id, $instances ),
		'width'    => absint( $atts['width'] ),
		'height'   => absint( $atts['height'] ),
		'poster'   => esc_url( $atts['poster'] ),
		'loop'     => wp_validate_boolean( $atts['loop'] ),
		'autoplay' => wp_validate_boolean( $atts['autoplay'] ),
		'preload'  => $atts['preload'],
	);

	// These ones should just be omitted altogether if they are blank
	foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $a ) {
		if ( empty( $html_atts[ $a ] ) ) {
			unset( $html_atts[ $a ] );
		}
	}

	$attr_strings = array();
	foreach ( $html_atts as $k => $v ) {
		$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
	}

	/** @noinspection HtmlUnknownAttribute */
	$html = sprintf( '<video %s controls="controls">', join( ' ', $attr_strings ) );

	$fileurl = '';
	$source  = '<source type="%s" src="%s" />';
	foreach ( $default_types as $fallback ) {
		if ( ! empty( $atts[ $fallback ] ) ) {
			if ( empty( $fileurl ) ) {
				$fileurl = $atts[ $fallback ];
			}
			if ( 'src' === $fallback && preg_match( $yt_pattern, $atts['src'] ) ) {
				$type = array( 'type' => 'video/youtube' );
			} else {
				$type = wp_check_filetype( $atts[ $fallback ], wp_get_mime_types() );
			}
			$url = esc_url( add_query_arg( '_', $instances, $atts[ $fallback ] ) );
			$html .= sprintf( $source, $type['type'], esc_url( $url ) );
		}
	}

	if ( ! empty( $content ) ) {
		if ( false !== strpos( $content, "\n" ) ) {
			$content = str_replace( array( "\r\n", "\n", "\t" ), '', $content );
		}
		$html .= trim( $content );
	}

	$html .= $dont_load_video_library? '' : wp_mediaelement_fallback( $fileurl );
	$html .= '</video>';

	$output = sprintf( '<div class="wp-video">%s</div>', $html );

	return $output;
}


