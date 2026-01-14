<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://booleanbites.com
 * @since      1.0.0
 *
 * @package    Houzi_Description_Ai
 * @subpackage Houzi_Description_Ai/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Houzi_Description_Ai
 * @subpackage Houzi_Description_Ai/includes
 * @author     BooleanBites Ltd <houzi@booleanbites.com>
 */
class Houzi_Description_Ai_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		if (!get_option('houzi_ai_gemini_model')) {
			update_option('houzi_ai_gemini_model', 'gemini-flash-latest');
		}
	}

}
