<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 * 
 * PHP version 5
 *
 * @category   Core_Plugin_Class
 * @package    Q_Invoice
 * @subpackage Q_Invoice/includes
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       https://www.qanuk.io/
 * @since      1.0.0
 */

if (!class_exists('QI_Invoice')) {
    /**
     * The core plugin class.
     *
     * This is used to define internationalization, admin-specific hooks, and
     * public-facing site hooks.
     *
     * Also maintains the unique identifier of this plugin as well as the current
     * version of the plugin.
     *
     * @category   Class
     * @package    Q_Invoice
     * @subpackage Q_Invoice/includes
     * @author     qanuk.io <support@qanuk.io>
     *             - In cooperation with <deftcoders@gmail.com>
     * @license    License example.org
     * @link       https://www.qanuk.io/
     * @since      1.0.0
     */
    class QI_Invoice
    {
        /**
         * The unique identifier of this plugin.
         *
         * @since  1.0.0
         * @access protected
         * @var    string $plugin_name <The string used to uniquely 
         *                identify this plugin.
         */
        protected $plugin_name;

        /**
         * The current version of the plugin.
         *
         * @since  1.0.0
         * @access protected
         * @var    string    $version    The current version of the plugin.
         */
        protected $version;

        /**
         * The loader that's responsible for maintaining and 
         * registering all hooks that power the plugin.
         *
         * @since  1.0.0
         * @access protected
         * @var    QI_Invoice_Loader $loader Maintains and 
         *                           registers all hooks for the plugin.
         */
        protected $loader;

        /**
         * Define the core functionality of the plugin.
         *
         * Set the plugin name and the plugin version 
         * that can be used throughout the plugin.
         * Load the dependencies, define the locale, 
         * and set the hooks for the admin area and
         * the public-facing side of the site.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            if (defined('Q_INVOICE_VERSION')) {
                $this->version = Q_INVOICE_VERSION;
            } else {
                $this->version = '1.0.0';
            }

            $this->plugin_name = 'q_invoice';

            $this->_loadDependencies();
            $this->_setLocale();
            $this->_defineAdminHooks();
        }

        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - QI_Invoice_Loader. Orchestrates the hooks of the plugin.
         * - QI_Invoice_i18n. Defines internationalization functionality.
         * - QI_Invoice_Admin. Defines all hooks for the admin area..
         *
         * Create an instance of the loader which will be used to register the hooks
         * with WordPress.
         *
         * @since  1.0.0
         * @access private
         * 
         * @return void
         */
        private function _loadDependencies()
        {
            /**
             * The class responsible for orchestrating the actions and filters of the
             * core plugin.
             */
            include_once plugin_dir_path(dirname(__FILE__)) . 
            'includes/class-q_invoice-loader.php';

            /**
             * The class responsible for defining internationalization functionality
             * of the plugin.
             */
            include_once plugin_dir_path(dirname(__FILE__)) . 
            'includes/class-q_invoice-i18n.php';

            /**
             * The class responsible for defining all actions 
             * that occur in the admin area.
             */
            include_once plugin_dir_path(dirname(__FILE__)) . 
            'admin/class-q_invoice-admin.php';

            $this->loader = new QI_Q_Invoice_Loader();
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the QI_Invoice_i18n class in order 
         * to set the domain and to register the hook
         * with WordPress.
         *
         * @since  1.0.0
         * @access private
         * 
         * @return void
         */
        private function _setLocale()
        {
            $plugin_i18n = new QI_Invoice_i18n();

            $this->loader->addAction(
                'plugins_loaded', 
                $plugin_i18n, 
                'load_plugin_textdomain'
            );
        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         *
         * @since  1.0.0
         * @access private
         * @return void
         */
        private function _defineAdminHooks()
        {
            $plugin_admin = new QI_Q_Invoice_Admin(
                $this->getPluginName(), 
                $this->getVersion()
            );

            $this->loader->addAction(
                'admin_enqueue_scripts', 
                $plugin_admin, 
                'enqueueStyles'
            );

            $this->loader->addAction(
                'admin_enqueue_scripts', 
                $plugin_admin, 
                'enqueueScripts'
            );

            $this->loader->addAction(
                'admin_menu', 
                $plugin_admin, 
                'addQInvoiceAdminMenus'
            );
            
            $this->loader->addAction(
                'admin_init', 
                $plugin_admin, 
                'qiSettingsInit'
            );
        
            $ajaxFunctionNames = array();
            $ajaxFunctionNames = [
                'saveInvoice',
                'updateInvoice',
                'deleteInvoice', // This is Deactivation ATM; should be changed
                'reactivateInvoice',
                'editInvoice',
                'checkInvoice',
                'updateInvoiceHeader',
                'fetchLastID',        
                'fetchCurrency',
                'saveContact',                
                'fetchContacts',
                'fetchCurrency',
                'updateContact',
                'deleteContact',
                'editContact', 
                'removeLogo',
                'printInvoiceTemplate',
                'printDunningTemplate',
            ];

            foreach ($ajaxFunctionNames as $ajaxFunctionName) {
                $this->_addAjaxFunction($ajaxFunctionName, $plugin_admin);
            }

        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @param string $functionName 
         * @param array  $pluginRef 
         * 
         * @since 1.0.0
         * 
         * @return void
         */
        private function _addAjaxFunction($functionName, $pluginRef) 
        {
            $this->loader->addAction(
                'wp_ajax_'.$functionName.'ServerSide', 
                $pluginRef, 
                $functionName.'ServerSide'
            );
        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public function run()
        {
            $this->loader->run();
        }

        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @since  1.0.0
         * @return string    The name of the plugin.
         */
        public function getPluginName()
        {
            return $this->plugin_name;
        }

        /**
         * The reference to the class that orchestrates the hooks with the plugin.
         *
         * @since  1.0.0
         * @return QI_Invoice_Loader    Orchestrates the hooks of the plugin.
         */
        public function getLoader()
        {
            return $this->loader;
        }

        /**
         * Retrieve the version number of the plugin.
         *
         * @since  1.0.0
         * @return string    The version number of the plugin.
         */
        public function getVersion()
        {
            return $this->version;
        }
    } // end class
} // endif class exists