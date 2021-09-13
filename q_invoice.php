<?php
/**
 * The plugin bootstrap file
 * 
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * PHP version 5
 * 
 * @category A
 * @package  Invoice
 * @author   Qanuk.io <name@email.de>
 * @license  GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link     qanuk.io
 * @since    1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Q Invoice
 * Plugin URI:        qanuk.io
 * Description:       q Invoice - simple invoice of e-mails to partners 
 *                    / list of e-mails. More features on www.qanuk.io
 * Version:           1.0.0
 * Author URI:        qanuk.io
 * Text Domain:       q-invoice
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Path of this file
define('INVOICE_ROOT_FILE', __FILE__);

/**
 * Currently plugin version.
 * 
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('Q_INVOICE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-invoice-activator.php
 */
if (!function_exists('Qi_QActivateInvoice')) { 
    /**
     * Function Qi_QactivateInvoice
     * 
     * @return void
     */
    function Qi_QActivateInvoice()
    {
        include_once plugin_dir_path(__FILE__). 
        'includes/class-q_invoice-activator.php';
        if (class_exists('QI_Invoice_Activator')) { 
            QI_Invoice_Activator::activate();
        }
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-invoice-deactivator.php
 */
if (!function_exists('Qi_QDeactivateInvoice')) {
    /**
     * Function Qi_QDeactivateInvoice
     * 
     * @return void
     */
    function Qi_QDeactivateInvoice()
    {
        include_once plugin_dir_path(__FILE__). 
        'includes/class-q_invoice-deactivator.php';
        if (class_exists('QI_Invoice_Deactivator')) { 
            QI_Invoice_Deactivator::deactivate();
        }  
    }
}

register_activation_hook(__FILE__, 'Qi_QActivateInvoice');
register_deactivation_hook(__FILE__, 'Qi_QDeactivateInvoice');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-q_invoice.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */

if (!function_exists('Qi_runQInvoice')) {
    /**
     * Function Qi_runQInvoice
     * 
     * @return void
     */
    function Qi_runQInvoice()
    {
        if (class_exists('QI_invoice')) { 
            $plugin = new QI_invoice();
            $plugin->run();
        }
    }
}

if (function_exists('Qi_runQInvoice')) {
    Qi_runQInvoice();
}
