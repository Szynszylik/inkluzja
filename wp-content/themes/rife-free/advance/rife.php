<?php

class Apollo13Framework_Rifef {
	function __construct(){


		//get Rife Universal first so it could fire its actions first
		/** @noinspection PhpIncludeInspection */
		require_once( get_theme_file_path( 'advance/rife_uni.php' ));
		add_action( 'customize_register', array( $this, 'customizer_pro_section' ) );

		if(is_admin()){
			//check on what page we are
			$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$current_subpage = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : '';

			//always registered in admin
			add_action( 'admin_menu', array( $this, 'admin_help_links' ) );
			add_filter( 'apollo13framework_is_import_allowed', array( $this, 'is_import_allowed' ) );
			add_action( 'apollo13framework_after_designs_list', array( $this, 'more_designs_info' ) );

			//only for import design page
			if( $current_page === 'apollo13_pages' && $current_subpage === 'import' ){
//				add_action( 'apollo13framework_before_designs_list', array( $this, 'more_designs_info' ) );
				add_action( 'admin_init', array( $this, 'designs_import_inline_style' ), 28 );
			}

			//TODO remove when theme gets accepted on WordPress.org repository
			/** @noinspection PhpIncludeInspection */
			require_once( get_theme_file_path( 'advance/inc/theme-update-checker.php' ));
			new ThemeUpdateChecker(
				A13FRAMEWORK_TPL_SLUG,
				'http://apollo13.eu/themes_update/rife/free_info.json'
			);
		}
	}

	function is_import_allowed(){
		return true;
	}

	/**
	 * Adds menu with settings for theme
	 */
	function admin_help_links() {
		//rename License menu position
		global $submenu;
		foreach($submenu['themes.php'] as &$item){
			if($item[2] === 'apollo13_info'){
				$item[0] = esc_html__( 'Rife Free Info', 'rife-free' );
				$item[3] = esc_html__( 'Rife Free Info', 'rife-free' );
				break;
			}
		}
	}

	function more_designs_info(){
		echo '<p>'.esc_html__( 'Faded out Designs are available only in Rife Pro. You can preview them from here by clicking on each Design and then using "Live preview" button.', 'rife-free' ).'</p>';
	}


	function designs_import_inline_style(){
		$css = '
.demo_grid_item[data-categories~=pro] img{
    opacity: .2;
    -webkit-transition: opacity 0.3s ease;
			transition: opacity 0.3s ease;
}
.demo_grid_item[data-categories~=pro]:hover img{
    opacity: 1;
}
.demo_grid_item.open img{
    opacity: 1;
    -webkit-transition: none;
			transition: none;
}
.top_bar_button.try-button {
    right: 20px;
}';
		wp_add_inline_style( 'admin-css', $css );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  WP_Customize_Manager  $manager
	 * @return void
	 */
	public function customizer_pro_section( $manager ) {

		// Load custom section.
		require_once( get_theme_file_path( 'advance/inc/customizer/sections/class-a13-customize-section-pro.php' ) );

		// Register custom section types.
		$manager->register_section_type( 'A13_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new A13_Customize_Section_Pro(
				$manager,
				'rife-pro-theme',
				array(
					'title'    => esc_html__( 'Check Rife Pro', 'rife-free' ),
					'pro_text' => esc_html__( 'Go Pro',         'rife-free' ),
					'pro_url'  => 'https://apollo13themes.com/rife/',
					'priority' => 0
				)
			)
		);
	}
}

//run
new Apollo13Framework_Rifef();