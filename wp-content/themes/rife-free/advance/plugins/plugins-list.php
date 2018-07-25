<?php
/**
 * TGMPA plugin installer config
 */
function apollo13framework_register_required_plugins() {
	/**
	 * Array of configuration settings. Amend each line as needed.
	 */

	tgmpa(
		array(
			array(
				'name'               => esc_html__( 'Apollo13 Framework Extensions', 'rife-free' ),
				'slug'               => 'apollo13-framework-extensions',
				'required'           => false,
				'version'            => '1.2.1',
				'force_activation'   => false,
				'force_deactivation' => false,
			)
		),
		array(
			'is_automatic' => true, // Automatically activate plugins after installation or not
		)
	);
}


add_action('tgmpa_register', 'apollo13framework_register_required_plugins');