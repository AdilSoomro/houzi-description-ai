<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://booleanbites.com
 * @since      1.0.0
 *
 * @package    Houzi_Description_Ai
 * @subpackage Houzi_Description_Ai/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Houzi_Description_Ai
 * @subpackage Houzi_Description_Ai/includes
 * @author     BooleanBites Ltd <houzi@booleanbites.com>
 */
class Houzi_Description_Ai_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'houzi-description-ai',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
