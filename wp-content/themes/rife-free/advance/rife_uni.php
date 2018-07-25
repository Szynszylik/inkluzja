<?php
class Apollo13Framework_Rife_Uni{
	function __construct(){
		add_filter( 'apollo13framework_docs_address', array( $this, 'docs_link' ), 10, 2 );
		add_filter( 'apollo13framework_docs_locations', array( $this, 'docs_locations' ), 10, 2 );
		add_action( 'apollo13framework_before_export_theme_options_section', array( $this, 'before_export_theme_options_section' ), 10 );
	}

	function docs_link() {
		return 'https://rifetheme.com/help/';
	}

	function docs_locations() {
		return array(
			'license-code'           => 'docs/getting-started/where-i-can-find-license-code/',
			'header-color-variants'  => 'docs/customizing-the-theme/header/variant-light-dark-overwrites/',
			'importer-configuration' => 'docs/installation-updating/importing-designs/importer-configuration/',
			'export'                 => 'docs/installation-updating/exporting-theme-options/',
			'support-forum'          => 'docs/getting-started/support-forum/',
		);
	}

	function before_export_theme_options_section(){
		$other_themes_settings = array(
			'apollo13_option' => 'FatMoon',
			'apollo13_option_starter' => 'Starter',
			'apollo13_option_a13agency' => 'A13Agency',
			'apollo13_option_photoproof' => 'PhotoProof',
			'apollo13_option_onelander' => 'A13 OneLander',
		);

		$html = '';

		foreach($other_themes_settings as $option_name => $theme_name){
			$save_option = get_option($option_name);
			if($save_option !== false ){
				$html .= '<p style="text-align: center;">';
				/* translators: %s: theme name */
				$html .= '<button class="button button-primary import-theme-settings" data-import-field="theme_options_'.esc_attr( $option_name ).'" type="submit">'. sprintf( esc_html__( 'Import settings from %s theme', 'rife-free' ), '<strong>'.$theme_name.'</strong>' ) . '</button>';
				$html .= '<textarea style="display: none;" rows="10" cols="20" class="large-text" id="theme_options_'.esc_attr( $option_name ).'" readonly>'.esc_textarea( wp_json_encode( $save_option ) ).'</textarea>';
				$html .= '</p>';
			}
		}
		if( strlen($html) ){
		?>
			<h2><?php echo esc_html__( 'Import theme options to Rife theme', 'rife-free' ); ?></h2>
			<p style="text-align: center;"><?php echo esc_html__( 'Use below button to import theme settings from one of the compatible themes, that were previously installed.', 'rife-free' ); ?></p>
			<p style="text-align: center;"><?php echo esc_html__( 'No content will be removed. Do not worry.', 'rife-free' ); ?></p>
			<p style="text-align: center;"><?php echo esc_html__( 'Only theme settings of Rife Theme will be overwritten with those from the chosen theme.', 'rife-free' ); ?></p>
			<?php echo $html;//escaped above ?>
			<hr />
		<?php
		}
	}
}

new Apollo13Framework_Rife_Uni();






