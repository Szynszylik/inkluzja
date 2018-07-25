<?php
function apollo13framework_apollo13_pages() {
	//check is such subpage is registered
	if ( isset( $_GET['subpage'] ) ) {
		$function_name = 'apollo13framework_apollo13_'.sanitize_text_field( wp_unslash( $_GET['subpage'] ) );
		if( $function_name !== __FUNCTION__ && function_exists( $function_name )){
			//process with subpage
			$function_name();
		}
		else{
			//go to default page
			apollo13framework_apollo13_info();
		}
	}
	else{
		//go to default page
		apollo13framework_apollo13_info();
	}
}



/**
 * Collect data for site export
 *
 * @return array site settings
 */
function apollo13framework_collect_site_data() {
	$export = array();

	//export widgets
	global $wp_registered_widgets;
	$widgets_types = array();


	//we collect all registered widgets and check if we can get their id_base
	foreach ( $wp_registered_widgets as $widget ) {
		$temp_callback = $widget['callback'];
		if ( is_array( $temp_callback ) ) {
			$widgets_types[] = 'widget_' . $temp_callback[0]->id_base;
		}
	}

	//remove duplicates
	$widgets_types = array_unique( $widgets_types );

	//collect export info only
	$export_widgets = array();
	foreach ( $widgets_types as $type ) {
		$temp_type = get_option( $type );
		if ( $temp_type !== false ) {
			$export_widgets[ $type ] = $temp_type;
		}
	}

	//our export value
	$export['widgets'] = $export_widgets;


	//export sidebars
	$export['sidebars'] = get_option( 'sidebars_widgets' );


	//export frontpage
	$fp_options = array(
		'show_on_front'  => get_option( 'show_on_front' ),
		'page_on_front'  => get_option( 'page_on_front' ),
		'page_for_posts' => get_option( 'page_for_posts' )
	);

	//our export value
	$export['frontpage'] = $fp_options;


	//export menus
	$menu_locations = get_nav_menu_locations();
	foreach ( $menu_locations as $key => $id ) {
		if ( $id === 0 ) {
			continue;
		}
		$obj = get_term( $id, 'nav_menu' );
		//instead of id save slug of menu
		$menu_locations[ $key ] = $obj->slug;
	}

	$export['menus'] = $menu_locations;


	//export plugins settings
	//AddToAny
	$plugins_settings = array();
	if ( function_exists( 'A2A_SHARE_SAVE_init' ) ) {
		$plugins_settings['addtoany_options'] = get_option( 'addtoany_options' );
	}

	//Elementor
	if( defined( 'ELEMENTOR_VERSION' ) ){
		$plugins_settings['elementor_cpt_support'] = get_option( 'elementor_cpt_support' );
		$plugins_settings['elementor_scheme_color'] = get_option( 'elementor_scheme_color' );
		$plugins_settings['elementor_scheme_typography'] = get_option( 'elementor_scheme_typography' );
	}


	//WPForms
	if( class_exists( 'WPForms') ){
		$plugins_settings['wpforms_settings'] = get_option( 'wpforms_settings' );
	}

	//WPGMAPS
	if( defined( 'WPGMAPS' ) ){
		$wpgmza_google_maps_api_key = get_option( 'wpgmza_google_maps_api_key' );
		$plugins_settings['wpgmza_google_maps_api_key']  = $wpgmza_google_maps_api_key ? $wpgmza_google_maps_api_key : 'AIzaSyA2a2tu7HNPEFEVAd6vzc6qSeRrqY6Qc1c';
	}
	$export['plugins_configs'] = $plugins_settings;


	//Woocommerce
	if ( apollo13framework_is_woocommerce_activated() ) {

		$options_to_export = array(
			'woocommerce_shop_page_id',
			'woocommerce_cart_page_id',
			'woocommerce_checkout_page_id',
			'woocommerce_myaccount_page_id',
			'shop_thumbnail_image_size',
			'shop_catalog_image_size',
			'shop_single_image_size',
		);

		$wc_options = array();

		foreach ( $options_to_export as $name ) {
			$wc_options[ $name ] = get_option( $name );
		}

		//wishlist settings
		if ( class_exists( 'YITH_WCWL' ) ) {
			$wc_options['yith_wcwl_wishlist_page_id'] = get_option( 'yith_wcwl_wishlist_page_id' );
		}

		//our export value
		$export['woocommerce'] = $wc_options;
	}
	return wp_json_encode( $export );
}



/**
 * Retruns theme settings in form of JSON string
 *
 * @return string theme settings
 */
function apollo13framework_export_theme_setting() {
	return wp_json_encode( get_option(A13FRAMEWORK_OPTIONS_NAME) );
}



/**
 * @return bool|array list of demos or false on error
 */
function apollo13framework_get_demo_list() {

	$demos_definition = array();

	$response = wp_remote_get( A13FRAMEWORK_IMPORT_SERVER . '/definitions/' . A13FRAMEWORK_TPL_SLUG . '_demos_definition.php', array('timeout' => 20) );
	if( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 200 ){
		$demos_definition = json_decode( wp_remote_retrieve_body( $response ), true );
	}

	if(!isset($demos_definition['demos'])){
		return false;
	}

	return $demos_definition;
}



function apollo13framework_get_demo_importer_content() {
	global $apollo13framework_a13;
	$demos_definition = apollo13framework_get_demo_list();
	$demos            = $demos_definition['demos'];
	$demo_count       = $demos_definition === false ? 0 : count( $demos );
	$all_categories   = array();
	$available_demos  = array();

	$available_demos_number = 0;
	if($demos_definition !== false){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		foreach ( $demos as $demo ) {
			//check if demo is available for this configuration
			if( isset( $demo['must_have_plugins'] ) && is_array( $demo['must_have_plugins'] ) ){
				$not_available = false;
				foreach( $demo['must_have_plugins'] as $plugin ) {
					if( ! is_plugin_active( $plugin ) ){
						$not_available = true;
					}
				}
				if( $not_available ){
					//skip this demo
					continue;
				}
			}

			//count this demo
			$available_demos_number++;
			$available_demos[] = $demo;

			//collect categories
			$all_categories = array_merge( $all_categories, $demo['categories'] );
		}
	}
	?>




		<div class="demo_import_wrapper">
			<?php
			if($demos === false){
				$demos_list_url = A13FRAMEWORK_IMPORT_SERVER . '/definitions/' . A13FRAMEWORK_TPL_SLUG . '_demos_definition.php';
				echo '<p class="info_box">'.
				     sprintf(
						/* translators: %1$s: URL */
						esc_html__( 'There is problem with getting list of available demos for import. Either our server is down, and you should be able to import demo at later time, or your server has problem with reaching our server. Just in case ask your server admin if your server has any problems reaching this URL %1$s - usually server DNS issue or Firewall is blocking it.', 'rife-free'),
						'<a target="_blank" href="'.esc_url( $demos_list_url ).'">'.esc_html( $demos_list_url ).'</a>'
				     ).
					'</p>';
			}

			//we have some demos
			if($demo_count){
				?>

			<div id="a13-import-step-1" data-step="1" class="import-step step-1">
				<h2><?php echo esc_html__( 'Designs to choose from:', 'rife-free' ); ?> <strong><?php echo intval($available_demos_number); ?></strong></h2>
				<p class="center"><?php echo esc_html__( 'Please select design for import to move to next step.', 'rife-free' ); ?></p>

				<?php if($demo_count > 1){ ?>
					<span class="demo_search_wrap">
					<label><i class="fa fa-search"></i><?php echo esc_html__( 'Search designs', 'rife-free' ); ?>
						<input class="demo_search" type="search" value="" name="demo_search" placeholder="<?php esc_attr_e( 'At least 3 chars: name, category', 'rife-free' ); ?>" />
					</label>
					</span>

					<div class="filter_wrapper">
						<?php
						//categories
						if(count($all_categories) > 1){
							$all_categories_unique = array_unique( $all_categories );
							sort( $all_categories_unique );

							echo '<ul class="demo_filter_categories">';
							echo '<li data-filter="*" class="active"> ' . esc_html__( 'All', 'rife-free' ) . ' </li>';
							foreach ( $all_categories_unique as $category ) {
								echo '<li data-filter="' . esc_attr( str_replace( ' ', '_', strtolower( $category ) ) ) . '"> ' . esc_html( $category ). ' </li>';
							}
							echo '</ul>';
						}
						?>
					</div>
				<?php }
				do_action('apollo13framework_before_designs_list');
				?>

				<div id="a13_demo_grid" class="demo_grid">
				<?php
				foreach ( $available_demos as $demo ) {
					//check for setting telling proper path to thumbnails
					if(isset( $demos_definition['settings'] ) && isset($demos_definition['settings']['files_path'])  ){
						$files_directory = A13FRAMEWORK_IMPORT_SERVER . '/files/'.$demos_definition['settings']['files_path'].'/demo_data/' . $demo['id'] . '/';
					}
					else{
						$files_directory = A13FRAMEWORK_IMPORT_SERVER . '/files/' . A13FRAMEWORK_TPL_SLUG . '/demo_data/' . $demo['id'] . '/';
					}

					apollo13framework_importer_grid_item( $files_directory, $demo );
				}
				?>
				</div>

			<?php
				do_action('apollo13framework_after_designs_list');
			?>
			</div>

			<div id="a13-import-step-2" data-step="2" class="import-step step-2 hidden">
				<h2><?php echo esc_html__( 'About Design Importer', 'rife-free' ); ?></h2>
				<?php
				echo '<p>'.
				     esc_html__( 'This importer can be used to import whole demo look &amp; content to your site. Use below configuration and designs to achieve desired results.', 'rife-free').
				     ' <a href="'.esc_url( $apollo13framework_a13->get_docs_link('importer-configuration') ).'">'.esc_html__( 'Read more about using Design Importer.', 'rife-free').'</a>'.
				     '</p>';
				echo '<p>'.
				     esc_html__( 'While using Design Importer feature some data will be stored on our server. These are: Date of action, your site URL, IP address, imported Design name. All these data is used for statistic and for protection against abuse of our services. These data are not shared with any third party.', 'rife-free').
				     '</p>';
				?>

				<h2><?php echo esc_html__( 'Configuration &amp; Requirements', 'rife-free' ); ?></h2>

				<div class="config-tables clearfix">
					<?php
					apollo13framework_theme_import_configuration();
					apollo13framework_theme_requirements_table();
					?>
				</div>

				<div class="import-navigation">
					<button class="button previous-step"><?php echo esc_html__( 'Previous step', 'rife-free' ); ?></button>
					<button class="button button-primary button-hero next-step"><?php echo esc_html__( 'Next step', 'rife-free' ); ?></button>
				</div>
			</div>

			<div id="a13-import-step-3" data-step="3" class="import-step step-3 hidden">
				<h2><?php echo esc_html__( 'You are about to import:', 'rife-free' ); ?></h2>
				<div class="import-summary">
					<h3 class="design-name"></h3>
					<img src="" alt="<?php echo esc_attr( __( 'Design Preview', 'rife-free' ) ); ?>" />
				</div>

				<div class="status_info">
					<strong id="demo_data_import_status"><?php esc_html_e( 'The Importer is ready to start.', 'rife-free' ); ?></strong>
					<a id="a13_import_demo_data_log_link" href="#"><?php esc_html_e( 'Show/hide log', 'rife-free' ); ?></a>
				</div>

				<div class="import_progress_bar">
					<div class="import_progress"></div>
				</div>

				<div id="demo_data_import_log">
					<p class="info"><?php esc_html_e( 'This log is only usable for developers, so please don\'t interpret it on your own. Most of the notices displayed here have nothing to do with problems that you might encounter while importing demo data.', 'rife-free' ); ?></p>
					<div></div>
				</div>

				<div class="import-navigation">
					<button class="button previous-step"><?php echo esc_html__( 'Previous step', 'rife-free' ); ?></button>
					<button class="button button-primary button-hero" id="start-demo-import" data-confirm="<?php echo esc_attr( __( 'Do you want to import selected Design?', 'rife-free' ) ); ?>" data-confirm-remove-content="<?php echo esc_attr( __( 'All your current content will be removed prior to import!', 'rife-free' ) ); ?>"><?php echo esc_html__( 'Start Design Import', 'rife-free' ); ?></button>
				</div>
			</div>
				<?php
			}
			?>
		</div>
	<?php
}


function apollo13framework_get_demo_exporter_content(){
	global $apollo13framework_a13;

	do_action('apollo13framework_before_export_theme_options_section');
?>
	<h2><?php echo esc_html__( 'Export &amp; Import theme options', 'rife-free' ); ?></h2>
	<p style="text-align: center;"><a href="<?php echo esc_url( $apollo13framework_a13->get_docs_link('export') ); ?>"><?php echo esc_html__( 'Check the documentation for instructions about using Export Area.', 'rife-free' ); ?></a></p>
	<label for="export_theme_options_field"><?php echo esc_html__( 'Export theme settings', 'rife-free' ); ?></label>
	<textarea rows="10" cols="20" class="large-text" id="export_theme_options_field" readonly><?php echo esc_textarea( apollo13framework_export_theme_setting() );?></textarea>
	<button class="button button-secondary copy-content" type="submit"><?php echo esc_html__( 'Copy to clipboard', 'rife-free' ); ?></button>

	<hr />

	<label for="import_theme_options_field"><?php echo esc_html__( 'Import theme settings', 'rife-free' ); ?></label>
	<textarea rows="10" cols="20" class="large-text" id="import_theme_options_field"></textarea>
	<button class="button button-primary import-theme-settings" data-import-field="import_theme_options_field" type="submit"><?php echo esc_html__( 'Import theme settings', 'rife-free' ); ?></button>
	<div class="attention"><?php echo esc_html__( 'Attention! It will overwrite your current theme settings.', 'rife-free' ); ?></div>

<?php
	//export demo data field
	if ( apollo13framework_is_home_server() ){
		?>
		<hr />
		<label for="export_options_field"><?php echo esc_html__( 'Export demo data options(site_config file)', 'rife-free' ); ?></label>
		<textarea rows="10" cols="20" class="large-text" id="export_options_field" readonly><?php echo esc_textarea( apollo13framework_collect_site_data() );?></textarea>
		<button class="button button-secondary copy-content" type="submit"><?php echo esc_html__( 'Copy to clipboard', 'rife-free' ); ?></button>
	<?php }
}



function apollo13framework_theme_import_configuration(){
	global $apollo13framework_a13;
	?>
	<div class="import-config">
		<table class="status_table widefat" cellspacing="0">
			<thead>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Import configuration', 'rife-free' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td><label for="import-clear-content"><strong style="color: #ca2121;"><?php esc_html_e( 'Remove current content', 'rife-free' ); ?>:</strong></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( '<p>In order to achieve import results as close as possible to original demo, the importer will have to remove all your current content.</p><p>If you are on fresh WordPress install and you want to get best import results you should check this option.</p><p>If however you are just updating your existing site, then stay away from this option:-)</p>', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="clear_content" id="import-clear-content" /><label for="import-clear-content"><?php esc_html_e( 'Caution!', 'rife-free' ); ?></label></td>
			</tr>

			<tr>
				<td><label for="import-install-plugins"><?php esc_html_e( 'Install plugins', 'rife-free' ); ?>:</label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( '<p>It will install plugins that are needed to reproduce selected demo.</p><p>Not installing plugins may prohibit some content from importing.</p>', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="install_plugins" id="import-install-plugins" value="off" checked /></td>
			</tr>
			<tr>
				<td><label for="import-install-shop"><?php esc_html_e( 'Import shop', 'rife-free' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( '<p>If you plan to use shop on your page then check this option. It will install WooCommerce plugin and settings for it(if used in selected demo).</p><p>Leaving this option not checked will make your site faster while import and after it, as it will need much less memory - each active plugin makes your site slower.</p>', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="import_shop" id="import-install-shop" /></td>
			</tr>

			<tr>
				<td><label for="import-install-content"><?php esc_html_e( 'Import demo content', 'rife-free' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'It installs all the content created for the selected demo. These are pages, posts, works, albums and content from other "post types".', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="install_content" id="import-install-content" checked />
				<?php if ( !apollo13framework_is_home_server() ){ ?>
					<input class="hidden" type="checkbox" name="install-attachments" id="import-install-attachments" checked />
				<?php } ?>
				</td>
			</tr>
			<?php if ( apollo13framework_is_home_server() ){ ?>
			<tr>
				<td><label for="import-install-attachments"><?php esc_html_e( 'Import media attachments', 'rife-free' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'It import images from our demo content.', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="install-attachments" id="import-install-attachments" checked /></td>
			</tr>
			<?php } ?>

			<?php if ( $apollo13framework_a13->check_for_valid_license() ){ ?>
			<tr>
				<td><label for="import-install-sliders"><?php esc_html_e( 'Import sliders', 'rife-free' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'Imports sliders created with "Slider Revolution" plugin that is used in demo content.', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="install_revo_sliders" id="import-install-sliders" checked /></td>
			</tr>
			<?php } ?>

			<tr>
				<td><label for="import-site-settings"><?php esc_html_e( 'Import site settings', 'rife-free' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( '<p>Site settings are various setting mostly not dependent on a theme you use. These are: permalinks and front page.</p><p>Partly theme dependent settings are menus and sidebars with widgets.</p>', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="install_site_settings" id="import-site-settings" checked /></td>
			</tr>

			<tr>
				<td><label for="import-theme-settings"><?php esc_html_e( 'Import theme settings', 'rife-free' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( '<p>The theme settings are all settings that you can later change on in Customizer.</p><p>If you wish only to change the look of your existing site to one from our demos, then mark only this option.</p>', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="install_theme_settings" id="import-theme-settings" checked /></td>
			</tr>

			<tr class="readonly">
				<td><label for="import-cleanup"><?php esc_html_e( 'Clean up after import', 'rife-free' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'It deletes all downloaded demo data files, and clean up some entries in the database that were used for the import process.', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="clean" id="import-cleanup" checked /></td>
			</tr>

			<tr class="readonly">
				<td><label for="import-download"><?php esc_html_e( 'Download demo files', 'rife-free' ); ?>:</label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'The importer will download demo data files that are needed to start the import process.', 'rife-free' ) ); ?></td>
				<td><input type="checkbox" name="download_files" id="import-download" checked /></td>
			</tr>
			</tbody>
		</table>
	</div>

	<?php
}



function apollo13framework_theme_requirements_table(){
	?>
	<div class="server-config">
		<table class="status_table widefat" cellspacing="0">
			<thead>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Server/WordPress Environment', 'rife-free' ); ?></th>
			</tr>
			</thead>

			<tbody>
			<tr>
				<td><?php esc_html_e( 'Upload Directory Writable', 'rife-free' ); ?>:</td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'The directory must be writable so downloaded demo data could be saved for the import process.', 'rife-free' ) ); ?></td>
				<td><?php
					//prepare directory for demo data
					if ( !is_writable( A13FRAMEWORK_IMPORTER_TMP_DIR ) ) {
						wp_mkdir_p(A13FRAMEWORK_IMPORTER_TMP_DIR);
					}

					if ( is_writable( A13FRAMEWORK_IMPORTER_TMP_DIR ) ) {
						echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> <code>' . esc_html( A13FRAMEWORK_IMPORTER_TMP_DIR ) . '</code></mark> ';
					} else {
						/* translators: %s: directory name */
						printf( '<mark class="error"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'To allow import, make %s writable.', 'rife-free' ) . '</mark>', '<code>'.esc_html( A13FRAMEWORK_IMPORTER_TMP_DIR ).'</code>' );
					}
					?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'File system method', 'rife-free' ); ?>:</td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'This is the type of method that your server uses to write to files. If it is something else then "direct" you will have to define connection details in <code>wp-config.php</code> file, while you are using importer.', 'rife-free' ) ); ?></td>
				<td><?php
					$fs_method = get_filesystem_method();

					//direct
					if( $fs_method === 'direct' ){

						echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> ' . esc_html( $fs_method ) . '</mark>';
					}
					//FTP
					elseif ( $fs_method === 'ftpext' || $fs_method === 'ftpsockets' ) {
						//no data or not complete data
						if(!defined( 'FTP_USER' ) || !defined( 'FTP_PASS') || !defined( 'FTP_HOST' )) {
							echo '<mark class="no"><span class="dashicons dashicons-no"></span> ' . sprintf(
									/* translators:
										%1$s - method,
										%2$s - "WordPress can not connect to file system.",
										%3$s - FTP_USER
										%4$s - FTP_PASS
										%5$s - FTP_HOST
										%6$s - wp-config.php
										%7$s - link to "WordPress Upgrade Constants" article */
									esc_html__( '%1$s - %2$s Be sure you have defined proper values for %3$s, %4$s and %5$s in %6$s file. Read more how to do it: %7$s', 'rife-free' ),
									esc_html( $fs_method ),
									'',
									'<code>FTP_USER</code>',
									'<code>FTP_PASS</code>',
									'<code>FTP_HOST</code>',
									'<code>wp-config.php</code>',
									'<a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">' . esc_html__( 'WordPress Upgrade Constants', 'rife-free' ) . '</a>' ) . '</mark>';
						}
						//complete data
						else {
							//check if can connect with data provided
							$creds = request_filesystem_credentials('', '', false, false, null);
							if(WP_Filesystem($creds)){
								/* translators: %1$s - filesystem method, %2$s - wp-config.php */
								echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> ' . sprintf( esc_html__( '%1$s - You have defined proper data in %2$s file.', 'rife-free' ), esc_html( $fs_method ), '<code>wp-config.php</code>' ) . '</mark>';
							}
							else{
								echo '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(
										/* translators:
										%1$s - method,
										%2$s - "WordPress can not connect to file system.",
										%3$s - FTP_USER
										%4$s - FTP_PASS
										%5$s - FTP_HOST
										%6$s - wp-config.php
										%7$s - link to "WordPress Upgrade Constants" article */
										esc_html__( '%1$s - %2$s Be sure you have defined proper values for %3$s, %4$s and %5$s in %6$s file. Read more how to do it: %7$s', 'rife-free' ),
										esc_html( $fs_method ),
										esc_html__('WordPress can not connect to file system.', 'rife-free' ),
										'<code>FTP_USER</code>',
										'<code>FTP_PASS</code>',
										'<code>FTP_HOST</code>',
										'<code>wp-config.php</code>',
										'<a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">' . esc_html__( 'WordPress Upgrade Constants', 'rife-free' ) . '</a>' ) . '</mark>';
							}
						}
					}
					//SSH2
					elseif ( $fs_method === 'ssh2' ) {
						/* translators:  %1$s - filesystem method, %2$s - wp-config.php, %3$s is link to "WordPress Upgrade Constants" article */
						echo '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - Be sure you have defined proper values for this file system method in %2$s file. Read more how to do it: %3$s ', 'rife-free' ), esc_html( $fs_method ), '<code>wp-config.php</code>', '<a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">' . esc_html__( 'WordPress Upgrade Constants', 'rife-free' ) . '</a>' ) . '</mark>';
					}
					//different method
					else{
						echo '<mark>' . esc_html( $fs_method ) . '</mark>';
					}
					?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'WP Memory Limit', 'rife-free' ); ?>:</td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'The maximum amount of memory (RAM) that your site can use at one time.', 'rife-free' ) ); ?></td>
				<td><?php
//					$memory = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );

					$system_memory = wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) );
					$memory        = $system_memory;//max( $memory, $system_memory );

					if ( $memory <= 0 ) {//0MB
						/* translators:  %1$s is memory available and %2$s is link to "Increasing memory allocated to PHP" article */
						echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We can not determine the true memory limit that is set on your server. It is possible that your server admin blocked manipulating this limit. See: %2$s', 'rife-free' ), esc_html( size_format( $memory ) ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . esc_html__( 'Increasing memory allocated to PHP', 'rife-free' ) . '</a>' ) . '</mark>';
					}
					elseif ( $memory < 100663296 ) {//96MB
						/* translators:  %1$s is memory available and %2$s is link to "Increasing memory allocated to PHP" article */
						echo '<mark class="error"><span class="dashicons dashicons-no"></span> ' . sprintf( esc_html__( '%1$s - Having memory lower than 96 MB(we recommend 128 MB or more) can produce errors while importing demo data, depending on how many plugins you have active. See: %2$s', 'rife-free' ), esc_html( size_format( $memory ) ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . esc_html__( 'Increasing memory allocated to PHP', 'rife-free' ) . '</a>' ) . '</mark>';
					}
					elseif ( $memory < 134217728 ) {//128MB
						/* translators:  %1$s is memory available and %2$s is link to "Increasing memory allocated to PHP" article */
						echo '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - You should be fine with so much memory, however, depending on how many plugins you have active you should change it to 128 MB or more. See: %2$s', 'rife-free' ), esc_html( size_format( $memory ) ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . esc_html__( 'Increasing memory allocated to PHP', 'rife-free' ) . '</a>' ) . '</mark>';
					}
					else {
						echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> ' . esc_html( size_format( $memory ) ) . '</mark>';
					}
					?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'PHP Version', 'rife-free' ); ?>:</td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'The version of PHP installed on your hosting server.', 'rife-free' ) ); ?></td>
				<td><?php
					// Check if phpversion function exists.
					if ( function_exists( 'phpversion' ) ) {
						$php_version = phpversion();

						if ( version_compare( $php_version, '5.3', '<' ) ) {
							/* translators:  %1$s is PHP version number and %2$s is link to "How to update your PHP version" article */
							echo '<mark class="error"><span class="dashicons dashicons-no"></span> ' . sprintf( esc_html__( '%1$s - We recommend a minimum PHP version of 5.6. Having version 7 or higher is even better. See: %2$s', 'rife-free' ), esc_html( $php_version ), '<a href="https://docs.woocommerce.com/document/how-to-update-your-php-version/" target="_blank">' . esc_html__( 'How to update your PHP version', 'rife-free' ) . '</a>' ) . '</mark>';
						}
						elseif ( version_compare( $php_version, '5.6', '<' ) ) {
							/* translators:  %1$s is PHP version number and %2$s is link to "How to update your PHP version" article */
							echo '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend a minimum PHP version of 5.6. Having version 7 or higher is even better. See: %2$s', 'rife-free' ), esc_html( $php_version ), '<a href="https://docs.woocommerce.com/document/how-to-update-your-php-version/" target="_blank">' . esc_html__( 'How to update your PHP version', 'rife-free' ) . '</a>' ) . '</mark>';
						}
						else {
							echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> ' . esc_html( $php_version ) . '</mark>';
						}
					} else {
						esc_html_e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'rife-free' );
					}
					?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'PHP Time Limit', 'rife-free' ); ?>:</td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups). Recommended 60 seconds or more for the import process.', 'rife-free' ) ); ?></td>
				<td><?php
					$max_execution_time = intval( ini_get( 'max_execution_time' ) );

					if ( $max_execution_time > 0 ){
						if ( $max_execution_time < 30 ) {
							/* translators: %1$s - time in seconds, %2$s - max_execution_time */
							echo '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - Having %2$s less than 30 can hurt the import process. We recommend setting it to at least 60 if possible, even if only for the import process.', 'rife-free' ), esc_html( $max_execution_time ), '<code>max_execution_time</code>' ) . '</mark>';
						}
						elseif($max_execution_time < 60){
							echo '<mark>' . esc_html( $max_execution_time ) . '</mark>';
						}
						else {
							echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> ' . esc_html( $max_execution_time ) . '</mark>';
						}
					}
					else{
						echo '<mark>' . esc_html( $max_execution_time ) . '</mark>';
					}
					?>
				</td>
			</tr>
			<tr>
				<td data-export-label="PHP Post Max Size"><?php esc_html_e( 'PHP Post Max Size', 'rife-free' ); ?>:</td>
				<td class="help"><?php apollo13framework_input_help_tip( esc_html__( 'The largest filesize that can be contained in one post. Recommended 64 MB or more.', 'rife-free' ) ); ?></td>
				<td><?php
					$post_max_size = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );

					if ( $post_max_size < 33554432 ) {//32MB
						/* translators: %s: Post size limit value */
						echo '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%s - too low value for this setting might cause problems. Recommended 64MB or more.', 'rife-free' ), esc_html( size_format( $post_max_size ) ) ) . '</mark>';
					}
					else {
						echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> ' . esc_html( size_format( $post_max_size ) ) . '</mark>';
					}
					?></td>
			</tr>
			<tr>
				<td data-export-label="Max Upload Size"><?php esc_html_e( 'Max Upload Size', 'rife-free' ); ?>:</td>
				<td class="help"><?php apollo13framework_input_help_tip( esc_html__( 'The largest filesize that can be uploaded to your WordPress installation. Recommended 64 MB or more.', 'rife-free' ) ); ?></td>
				<td><?php
					$max_upload_size = wp_convert_hr_to_bytes( wp_max_upload_size() );

					if ( $max_upload_size < 33554432 ) {//32MB
						/* translators: %s: Max upload size limit value */
						echo '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%s - too low value for this setting might cause problems. Recommended 64MB or more.', 'rife-free' ), esc_html( size_format( $max_upload_size ) ) ) . '</mark>';
					}
					else {
						echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> ' . esc_html( size_format( $max_upload_size ) ) . '</mark>';
					}
					?></td>
			</tr>
			</tbody>
		</table>
	</div>
	<?php
}