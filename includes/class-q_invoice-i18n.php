<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       qanuk.io
 * @since      1.0.0
 *
 * @package    Mailing
 * @subpackage Mailing/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mailing
 * @subpackage Mailing/includes
 * @author     qanuk.io <support@qanuk.io>
 */

if (!class_exists('QI_Invoice_i18n')) {
	class QI_Invoice_i18n
	{


		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain()
		{

			$result = load_plugin_textdomain(
				'Ev',
				false,
				dirname(dirname(plugin_basename(__FILE__))) . '/languages'
			);
		}
	}
}
