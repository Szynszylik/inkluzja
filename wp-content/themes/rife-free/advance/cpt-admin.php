<?php
/**
 * Prepares attachments so they can be used in admin and front end to show all media from gallery
 *
 * @param string $value json encoded string with list of all attachments
 *
 * @return array
 */
function apollo13framework_prepare_gallery_attachments( $value ){
	global $apollo13framework_a13;

	$attachments = array();
	if ( ! empty( $value ) ) {
		$images_videos_array = json_decode( $value, true );
		$media_count         = count( $images_videos_array );

		$proofing_enabled = $apollo13framework_a13->get_meta( '_proofing' ) === 'on';

		if($proofing_enabled){
			$proofing_meta  = get_post_meta( get_the_ID(), '_images_n_videos_proofing', true );
			$proofing_array = strlen( $proofing_meta ) === 0 ? array() : json_decode( $proofing_meta, true );
		}

		if ( $media_count ) {
			//collect all ids
			//and filter out external media(video links, audio links)
			$ids       = array();
			$externals = array();
			for ( $i = 0; $i < $media_count; $i ++ ) {
				$id = $images_videos_array[ $i ]['id'];
				if ( $id === 'external' ) {
					$externals[] = $images_videos_array[ $i ];
				}

				if(defined( 'ICL_SITEPRESS_VERSION') ){
					$ids[] = apply_filters( 'wpml_object_id', $images_videos_array[ $i ]['id'], 'post', true );
				}
				else{
					$ids[] = $images_videos_array[ $i ]['id'];
				}
			}

			//process items from media library
			$args = array(
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'post_parent'    => null,
				'post__in'       => $ids,
				'orderby'        => 'post__in'
			);
			$attachments = get_posts( $args );
			$attachments = array_map( 'wp_prepare_attachment_for_js', $attachments );
			//remove any empty, false elements
			$attachments = array_filter( $attachments );
			wp_reset_postdata();

			//process items from external links
			apollo13framework_prepare_external_media( $externals );

			//combine internal and external media back again
			//also check for deleted items
			for ( $i = 0; $i < $media_count; $i ++ ) {
				$id = $images_videos_array[ $i ]['id'];
				//wpml? get proper ID
				if(defined( 'ICL_SITEPRESS_VERSION') ){
					$id = apply_filters( 'wpml_object_id', $id, 'post', true );
				}

				if ( $id === 'external' ) {
					//first we push around to make space for us
					array_splice( $attachments, $i, 0, 'whatever' );
					//and now we push our thing
					$attachments[ $i ] = array_shift( $externals );
				} elseif ( ! isset( $attachments[ $i ] ) || ( (int) $id !== (int) $attachments[ $i ]['id'] ) ) {
					//there is something wrong, probably media was deleted
					array_splice( $attachments, $i, 0, 'deleted' );
				} else{
					//we push additional info to real attachments
					//These are options from theme
					$type = $images_videos_array[ $i ][ 'type' ];
					if( $type === 'image' ){
						$attachments[ $i ][ 'bg_color' ] = $images_videos_array[ $i ][ 'image_bg_color' ];
						$attachments[ $i ][ 'ratio_x' ] = $images_videos_array[ $i ][ 'image_ratio_x' ];
						$attachments[ $i ][ 'alt_link' ] = $images_videos_array[ $i ][ 'image_link' ];
						$attachments[ $i ][ 'product_id' ] = isset($images_videos_array[ $i ][ 'image_product_id' ])? $images_videos_array[ $i ][ 'image_product_id' ] : '';
						$attachments[ $i ][ 'filter_tags' ] = isset($images_videos_array[ $i ][ 'image_tags' ])? $images_videos_array[ $i ][ 'image_tags' ] : '';
					} elseif( $type === 'video' ){
						$attachments[ $i ][ 'autoplay' ] = $images_videos_array[ $i ][ 'video_autoplay' ];
						$attachments[ $i ][ 'ratio_x' ] = $images_videos_array[ $i ][ 'video_ratio_x' ];
						$attachments[ $i ][ 'filter_tags' ] = isset($images_videos_array[ $i ][ 'video_tags' ])? $images_videos_array[ $i ][ 'video_tags' ] : '';
					}

					if($proofing_enabled) {
						//settings from admin settings
						$attachments[ $i ]['proofing_id']      = isset( $images_videos_array[ $i ][$type.'_proofing_id'] ) ? $images_videos_array[ $i ][$type.'_proofing_id'] : '';
						//settings provided by user
						$proofing_record                       = isset( $proofing_array[ $images_videos_array[ $i ]['id'] ] ) ? $proofing_array[ $images_videos_array[ $i ]['id'] ] : null;
						$attachments[ $i ]['proofing_checked'] = ( isset( $proofing_record ) && array_key_exists( 'approved', $proofing_record ) ) ? $proofing_record['approved'] : 0;
						$attachments[ $i ]['proofing_comment'] = ( isset( $proofing_record ) && array_key_exists( 'comment', $proofing_record ) ) ? $proofing_record['comment'] : '';
					}
				}
			}
		}
	}

	return $attachments;
}



/**
 * Prepares external attachments
 *
 * @param array $items external items list
 */
function apollo13framework_prepare_external_media(&$items){
	global $apollo13framework_a13;

	$proofing_enabled = $apollo13framework_a13->get_meta( '_proofing' ) === 'on';
	$proofing_array = array();

	if($proofing_enabled){
		$proofing_meta  = get_post_meta( get_the_ID(), '_images_n_videos_proofing', true );
		$proofing_array = strlen( $proofing_meta ) === 0 ? array() : json_decode( $proofing_meta, true );
	}


	/** @noinspection PhpUnusedLocalVariableInspection */
	$audio_icon = wp_mime_type_icon('audio');
	/** @noinspection PhpUnusedLocalVariableInspection */
	$video_icon = wp_mime_type_icon('video');

	foreach($items as &$item){
		$type   = $item['type'];
		$mime   = substr($type, 0, -4); //-'link', result in "video" or "audio"
		$title  = $item[$type.'_title'];
		$link   = $item[$type.'_link'];
		$id     = $item[$type.'_attachment_id'];

		//prepare args that will be used to generate gallery HTML
		$item['filename']   = (empty($title)? $link : $title); //title is more favorable
		//CAUTION! overwrite of type here!
		$item['type']       = $mime; //type and subtype are switched kind of in compare to default WP Media library
		$item['subtype']    = $type;
		$item['icon']       = ${$mime.'_icon'};

		//thumb of item
		if(!empty($id)){
			list( $src, $width, $height ) = wp_get_attachment_image_src( $id, 'thumbnail' );
			$item['thumb'] = compact( 'src', 'width', 'height' );
		}
		else{
			$width = 48;
			$height = 64;
			$src = $item['icon'];
			$item['thumb'] = compact( 'src', 'width', 'height' );
		}

		if($proofing_enabled) {
			//settings provided by user
			$proofing_record          = isset( $proofing_array[ $link ] ) ? $proofing_array[ $link ] : null;
			$item['proofing_checked'] = ( isset( $proofing_record ) && array_key_exists( 'approved', $proofing_record ) ) ? $proofing_record['approved'] : 0;
			$item['proofing_comment'] = ( isset( $proofing_record ) && array_key_exists( 'comment', $proofing_record ) ) ? $proofing_record['comment'] : '';
		}
	}
	unset($item);
}



/**
 * Prepares admin gallery ready to display
 *
 * @param array $attachments
 *
 * @return string HTML of all items
 */
function apollo13framework_prepare_admin_gallery_html($attachments){
	if ( $attachments ) {
		foreach ( $attachments as $item ) {
			$src = '';
			if( !is_array($item) && $item === 'deleted' ){
				$file_name = 'File deleted?';
				$item_class = 'attachment-preview image deleted';
				$src = get_theme_file_uri( 'images/holders/deleted.png');
				$img_class = 'thumbnail';
			}
			else{
				//thumbnail src
				if(isset($item['thumb'])){
					$src = $item['thumb']['src'];
				}
				else{
					if(isset($item['sizes'])){
						if(isset($item['sizes']['thumbnail'])){
							$src = $item['sizes']['thumbnail']['url'];
						}
						//image is very small or just don't have thumbnail yet
						else{
							$src = $item['sizes']['full']['url'];
						}
					}
					elseif(isset($item['icon'])){
						$src = $item['icon'];
					}
				}

				//classes of item
				$item_class = 'attachment-preview'
				              .' type-'.$item['type']
				              .' subtype-'.$item['subtype']
				              .( isset($item['orientation'])? ' '.$item['orientation'] : '' )
				;

				//icon & filename for no image types
				$img_class = "thumbnail";
				$file_name = false;
				if($item['type'] !== 'image'){
					if( isset($item['thumb']) && $src === $item['icon'] ){
						$img_class = 'icon';
					}
					elseif( isset($item['icon']) && $src === $item['icon'] ){
						$img_class = 'icon';
					}
					$file_name = $item['filename'];
				}
			}

			apollo13framework_admin_gallery_item_html($item_class, $img_class, $src, $file_name );
		}
	}
}



/**
 * Helper to prepare each gallery item to display in admin
 *
 * @param string     $item_class    classes for current item
 * @param string     $img_class     classes for image of item
 * @param string     $src           image path
 * @param bool|string $file_name    file name for external attachments
 */
function apollo13framework_admin_gallery_item_html($item_class, $img_class, $src, $file_name = false ){
	?>
	<li class="mu-item attachment">
	<div class="<?php echo esc_attr($item_class); ?>">
		<div class="thumbnail">
			<div class="centered">
				<img class="<?php echo esc_attr($img_class); ?>" src="<?php echo esc_url($src); ?>">
			</div>

			<?php if($file_name !== false): ?>
				<div class="filename">
					<div><?php echo esc_html($file_name); ?></div>
				</div>
			<?php endif; ?>
		</div>
		<span class="mu-item-edit fa fa-pencil" title="<?php esc_attr_e( 'Edit', 'rife-free' ); ?>"></span>
		<span class="mu-item-remove fa fa-times" title="<?php esc_attr_e( 'Remove item', 'rife-free' ); ?>"></span>
		<div class="mu-item-drag"></div>
	</div>
	</li>
<?php
}
