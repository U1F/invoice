<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, _version, and hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * PHP version 5
 * 
 * @category Class
 * 
 * @package    QInvoice
 * @subpackage QInvoice/admin
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       https://www.qanuk.io/
 * @since      1.0.0
 */

require_once plugin_dir_path(__FILE__) .
    '../includes/class-q_invoice-constants.php';

if (!class_exists('QI_Q_Invoice_Admin')) {
    /**
     * Class QI_Q_Invoice_Admin
     * 
     * @category Plugin
     * @package  QInvoice
     * @author   qanuk.io <support@qanuk.io>
     * @license  License license.org
     * @link     example.org
     */
    class QI_Q_Invoice_Admin
    {
        /**
         * The ID of this plugin.
         *
         * @since  1.0.0
         * @access private
         * @var    string    $_plugin_name    The ID of this plugin.
         */
        private $_plugin_name;

        /**
         * The _version of this plugin.
         *
         * @since  1.0.0
         * @access private
         * @var    string    $_version    The current _version of this plugin.
         */
        private $_version;


        /**
         * Initialize the class and set its properties.
         *
         * @param string $plugin_name The name of this plugin.
         * @param string $version     The _version of this plugin.
         * 
         * @since 1.0.0
         */
        public function __construct($plugin_name, $version)
        {
            $this->_plugin_name = $plugin_name;
            $this->_version = $version;

            include_once INVOICE_ROOT_PATH.
                "/includes/" .
                "interface-invoices.php";

            include_once INVOICE_ROOT_PATH .
                "/includes/" .
                "interface-contacts.php";
            
            include_once INVOICE_ROOT_PATH .
                "/includes/" .
                "interface-export.php";

            include_once INVOICE_ROOT_PATH .
                "/includes/" .
                "interface-settings.php";
        }

          /**
         * Function qiOptionsPage TESTING SETTINGS
         * 
         * @param string $fileName        !
         * @param string $partialName     !
         * @param bool   $developmentMode ?
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function enqueuePartialCss($fileName, $partialName, $developmentMode = false)
        {
            if ($developmentMode) {
                wp_enqueue_style(
                
                    $this->_plugin_name . '_' . $fileName .'_css',
                    plugin_dir_url(__FILE__) .
                        'partials/'. $partialName .'/css/qinvoice-' . $fileName .'.css',
                    array(),
                    filemtime(
                        plugin_dir_path(__FILE__) .
                            'partials/'. $partialName .'/css/qinvoice-' . $fileName .'.css'
                    ),
                    'all'
                );
                
            } else {
                wp_enqueue_style( 
                    $this->_plugin_name . '_' . $fileName .'_css',
                    plugin_dir_url(__FILE__) .
                        'partials/'. $partialName .'/css/qinvoice-' . $fileName .'.css',
                    array(),
                    $this->_version, 
                    'all'
                );

            }
        }


        /**
         * Register the stylesheets for the admin area.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function enqueueStyles()
        {
            wp_enqueue_style(
                'q-jquery-ui',
                plugin_dir_url(__FILE__) . 'css/jquery-ui/jquery-ui.css',
                array(),
                $this->_version,
                'all'
            );

            wp_enqueue_style(
                'q-mtimepicker',
                plugin_dir_url(__FILE__) . 'css/timepicker/mtimepicker.css',
                array(),
                $this->_version,
                'all'
            );

            wp_enqueue_style(
                $this->_plugin_name . 'q_invoice-admin.css',
                plugin_dir_url(__FILE__) .
                    'css/q_invoice-admin.css',
                array(),
                filemtime(
                    plugin_dir_path(__FILE__) .
                        'css/q_invoice-admin.css'
                ),
                //$this->_version, 
                'all'
            );

            wp_enqueue_style(
                $this->_plugin_name . 'qinvoice_buttons.css',
                plugin_dir_url(__FILE__) .
                    'css/qinvoice_buttons.css',
                array(),
                filemtime(
                    plugin_dir_path(__FILE__) .
                        'css/qinvoice_buttons.css'
                ),
                //$this->_version, 
                'all'
            );

            $developmentMode = true;

            $partialStyles = [
                ["name" => "invoice", "partial" => "invoice"],
                ["name" => "invoice-overview", "partial" => "invoice"],
                ["name" => "invoice-form", "partial" => "invoice"],
                ["name" => "invoice-mail", "partial" => "invoice"],
                ["name" => "dunning",  "partial" => "dunning"],
                ["name" => "settings","partial" => "settings"],
                ["name" => "export",  "partial" => "export"],
                ["name" => "contacts",  "partial" => "contacts"]
                ];
            
            foreach ($partialStyles as $partStyle) {
                $this->enqueuePartialCss(
                    $partStyle['name'], 
                    $partStyle["partial"], 
                    $developmentMode
                );
            }

            // Obsolete, but may be used later instead of root css
            /*
            wp_enqueue_style(
                $this->_plugin_name.'-qi_admin.css', 
                plugin_dir_url(__FILE__) . 
                'partials/invoice/css/qi_admin.css', 
                array(), 
                filemtime(
                    plugin_dir_path(__FILE__) . 
                    'partials/invoice/css/qi_admin.css'
                ),
                //$this->_version, 
                'all'
            );
            */
            wp_enqueue_style('wp-jquery-ui-dialog');
        }
        /**
         * Register the JavaScript for the admin area.
         * 
         * @param string $scriptName      obligatory 
         * @param string $partialName     obligatory 
         * @param array  $dependencies    optional default = []  
         * @param book   $developmentMode optional default:false 
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function enqueuePartialJS(
            $scriptName, 
            $partialName, 
            $dependencies = [], 
            $developmentMode = false
        ) {
            if ($developmentMode) {
                wp_enqueue_script(
                    $this->_plugin_name .
                        $scriptName,
                    plugin_dir_url(__FILE__) .
                        'partials/'. $partialName .'/js/qinvoice-'.$scriptName.'.js',
                    $dependencies,
                    filemtime(
                        plugin_dir_path(__FILE__) .
                        'partials/'. $partialName .'/js/qinvoice-'.$scriptName.'.js'
                    ),
                    false
                );

            } else {
                wp_enqueue_script(
                    $this->_plugin_name .
                        $scriptName,
                    plugin_dir_url(__FILE__) .
                        'partials/'. $partialName .'/js/qinvoice-'.$scriptName.'.js',
                    $dependencies,
                    $this->_version,
                    false
                );
            }
            
        }
        /**
         * Register the JavaScript for the admin area.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function enqueueScripts()
        {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('jquery-ui-dialog'); 
            wp_enqueue_script('jquery-ui-core');

            $partialScripts =[
                ["name" => "invoice", "partial" => "invoice", "dependencies" => []],
                ["name" => "dunning", "partial" => "dunning", "dependencies" => []],
                ["name" => "invoice-autocomplete", "partial" => "invoice", "dependencies" => []],
                ["name" => "contacts", "partial" => "contacts", "dependencies" => []],
                ["name" => "settings", "partial" => "settings", "dependencies" => []],
                ["name" => "export", "partial" => "export", "dependencies" => []]
                
            ];
            foreach ($partialScripts as $partialScript) {
                $this->enqueuePartialJS(
                    $partialScript['name'], 
                    $partialScript['partial'], 
                    $partialScript['dependencies'], 
                    true
                );
            }
            
            
            wp_enqueue_script(
                $this->_plugin_name .
                    "_adminAjax",
                plugin_dir_url(__FILE__) .
                    'js/q_invoice-admin-ajax.js',
                array('jquery'),
                filemtime(
                    plugin_dir_path(__FILE__) .
                        'js/q_invoice-admin-ajax.js'
                ),
                //$this->_version, 
                false
            );
            
            wp_localize_script(
                $this->_plugin_name .
                    "_adminAjax",
                $this->_plugin_name .
                    '_ajaxObject',
                [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce($this->_plugin_name . "_nonce")
                ]
            );

            wp_enqueue_media();
        }



// ............................................................................................
// ............................................................................................
// .............####...######..######..######..######..##..##...####....####...................
// ............##......##........##......##......##....###.##..##......##......................
// .............####...####......##......##......##....##.###..##.###...####...................
// ................##..##........##......##......##....##..##..##..##......##..................
// .............####...######....##......##....######..##..##...####....####...................
// ............................................................................................
// ............................................................................................


        /**
         * Function qiSettingsInit TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsInit()
        {
            
            /**
             * ###########################################
             * Company Settings
             * 
             * ###########################################
             */
            register_setting(
                'pluginForm', 
                'qi_settings',
                array( 'sanitize_callback' => array( $this, 'handleFileUploadForLogo' ))
            );
            //

            add_settings_section(
                'qi_pluginPage_section',
                __('Company Details', 'ev'),              
                [$this, 'qiSettingsSectionCompanyCallback'],
                'pluginPage'
            );  

            $this->addSettingsField("company", "text", "pluginPage");
            $this->addSettingsField("additional", "text", "pluginPage");
            $this->addSettingsField("first Name", "text", "pluginPage");
            $this->addSettingsField("last Name", "text", "pluginPage");
            $this->addSettingsField("street", "text", "pluginPage", 1);
            $this->addSettingsField("ZIP", "number", "pluginPage", 1);
            $this->addSettingsField("city", "text", "pluginPage", 1);
            
             /*
            add_settings_field(
                'qi_settings' ."logoFileUrl", 
                null, 
                [$this, 'hideInput'],
                "pluginPage",
                'qi_'.'pluginPage'.'_section',
                $array = [
                    "name" => "logo File Url",
                    "type" => "text",
                    "class" => "hide_settings_field"
                ]
            );

            add_settings_field(
                'qi_settings' ."logoFileFile", 
                null,
                [$this, 'hideInput'],
                "pluginPage",
                'qi_'.'pluginPage'.'_section',
                $array = [
                    "name" => "logo File File",
                    "type" => "text",
                    "class" => "hide_settings_field"
                ]
            );*/

            add_settings_field(
                'qi_settingsLogoFile', 
                'Logo', 
                [$this, 'showInputForLogo'],
                'pluginPage',
                'qi_pluginPage_section'
            );

            /**
             * ###########################################
             * Contact Settings
             * 
             * ###########################################
             */
            register_setting('contactForm', 'qi_settings');

            add_settings_section(
                'qi_contactPage_section',
                __('Contact Details', 'ev'),
                [$this, 'qiSettingsSectionContactCallback'],
                'contactPage'
            );
            $this->addSettingsField("mail", "email", "contactPage");
            $this->addSettingsField("phone", "tel", "contactPage");
            $this->addSettingsField("website", "text", "contactPage");
            $this->addSettingsField("facebook", "text", "contactPage");
            $this->addSettingsField("instagram", "text", "contactPage");
            
            /**
             * ###########################################
             * Bank Settings
             * 
             * ###########################################
             */
            register_setting('bankIForm', 'qi_settings');

            add_settings_section(
                'qi_bankIPage_section',
                __('Bank 1 Details', 'ev'),
                [$this, 'qiSettingsSectionBankCallback'],
                'bankIPage'
            );

            $this->addSettingsField("IBAN 1", "text", "bankIPage");
            $this->addSettingsField("BIC 1", "text", "bankIPage");
            $this->addSettingsField("Bank Name 1", "text", "bankIPage");

            register_setting('bankFormII', 'qi_settings');

            add_settings_section(
                'qi_bankIIPage_section',
                __('Bank 2 Details', 'ev'),
                [$this, 'qiSettingsSectionBankCallback'],
                'bankIIPage'
            );

            $this->addSettingsField("IBAN 2", "text", "bankIIPage");
            $this->addSettingsField("BIC 2", "text", "bankIIPage");
            $this->addSettingsField("Bank Name 2", "text", "bankIIPage");
            
            register_setting('bankIIIForm', 'qi_settings');

            add_settings_section(
                'qi_bankIIIPage_section',
                __('Further Details', 'ev'),
                [$this, 'qiSettingsSectionBankCallback'],
                'bankIIIPage'
            );

            $this->addSettingsField("PayPal", "text", "bankIIIPage");

            /**
             * ###########################################
             * Mail Settings
             * 
             * ###########################################
             */
            register_setting('mailForm', 'qi_settings');

            add_settings_section(
                'qi_mailPage_section',
                __('Mail Server Details', 'ev'),
                [$this, 'qiSettingsSectionMailCallback'],
                'mailPage'
            );
            
            $this->addSettingsField("email", "text", "mailPage");

            register_setting('mailTemplateForm', 'qi_settings');

            add_settings_section(
                'qi_mailTemplatePage_section',
                __('Mail Templates', 'ev'),
                [$this, 'qiSettingsSectionMailCallback'],
                'mailTemplatePage'
            );
            
            $this->addSettingsField(
                "Text Invoice Mail", 
                "textarea", 
                "mailTemplatePage",
                0,
                "You can add a full E-Mail template text. This will prefill the Mail field if you want to send an invoice to a customer.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Dunning Mail", 
                "textarea", 
                "mailTemplatePage",
                0,
                "You can add a full E-Mail template text. This will prefill the Mail field if you want to send a dunning to a customer.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Offer Mail", 
                "textarea", 
                "mailTemplatePage",
                0,
                "You can add a full E-Mail template text. This will prefill the Mail field if you want to send an offer to a customer.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Credit Mail", 
                "textarea", 
                "mailTemplatePage",
                0,
                "You can add a full E-Mail template text. This will prefill the Mail field if you want to send a credit to a customer.",
                "text_details_input_mod"
            );

            /**
             * ###########################################
             * Invoice Settings
             *
             * ###########################################
             */
            register_setting(
                'invoiceForm', 
                'qi_settings', 
                'handleInputForInvoiceSettings'
            );

            add_settings_section(
                'qi_invoicePage_section',
                __('Invoice Details', 'ev'),
                [$this, 'qiSettingsSectionInvoiceCallback'],
                'invoicePage'
            );

            $this->addSettingsField("prefix", "text", "invoicePage", 1);
            $this->addSettingsField("no Start", "number", "invoicePage", 1);

            //Currency
            add_settings_field(
                'qi_settingsInvoiceCurrency', 
                'Invoice Currency', 
                [$this, 'showInputForinvoiceCurrency'],
                'invoicePage',
                'qi_invoicePage_section'
            );
            
            if (get_option('qi_settings')['invoiceCurrency'] == 'Other') {
                $this->addSettingsField("currency Sign", "text", "invoicePage", 1);
            }

            //Taxes
            add_settings_field(
                'qi_settingsTaxTypes', 
                'Tax Types', 
                [$this, 'showInputForTaxTypes'],
                'invoicePage',
                'qi_invoicePage_section'
            );
            
            for ($iterator = 0; $iterator < get_option('qi_settings')['taxTypes']; $iterator++) {
                $this->addSettingsField("tax ".($iterator+1), "number", "invoicePage");
            }

            //Units
            add_settings_field(
                'qi_settingsInvoiceUnit', 
                'Unit', 
                [$this, 'showInputForinvoiceUnit'],
                'invoicePage',
                'qi_invoicePage_section'
            );

            //Dot Types
            add_settings_field(
                'qi_settingsInvoiceDotType', 
                'Dottype', 
                [$this, 'showInputForinvoiceDotType'],
                'invoicePage',
                'qi_invoicePage_section'
            );

            //PDF Text Templates
            register_setting(
                'invoiceTextForm', 
                'qi_settings'
            );

            add_settings_section(
                'qi_invoiceTextPage_section',
                __('Invoice PDF Templates', 'ev'),
                null,
                'invoiceTextPage'
            );
        
            $this->addSettingsField(
                "Text Invoice Intro", 
                "textarea", 
                "invoiceTextPage",
                0,
                "This text will be shown above invoice details.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Invoice Outro", 
                "textarea", 
                "invoiceTextPage",
                0,
                "This text will be shown below invoice details.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Invoice Payment Deadline", 
                "textarea", 
                "invoiceTextPage",
                0,
                "You can announce how many days you will wait for payment.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Invoice Custom Footer", 
                "textarea", 
                "invoiceTextPage",
                0,
                "You can add details like your tax ID.",
                "text_details_input_mod"
            );

            /**
             * ###########################################
             * Dunning Settings
             * 
             * ###########################################
             */
            register_setting('dunningForm', 'qi_settings');

            add_settings_section(
                'qi_dunningPage_section',
                __('Dunning Details', 'ev'),
                [$this, 'qiSettingsSectionDunningCallback'],
                'dunningPage'
            );
            $this->addSettingsField("reminder", "text", "dunningPage");
            $this->addSettingsField("dunning 1", "text", "dunningPage");
            $this->addSettingsField("dunning 2", "text", "dunningPage");

            $this->addSettingsField("reminder Day Limit", "text", "dunningPage");
            $this->addSettingsField("dunning 1 day limit", "text", "dunningPage");
            $this->addSettingsField("dunning 2 day limit", "text", "dunningPage");

            //PDF Text Templates
            register_setting(
                'dunningTextForm', 
                'qi_settings'
            );

            add_settings_section(
                'qi_dunningTextPage_section',
                __('Dunning PDF Templates', 'ev'),
                null,
                'dunningTextPage'
            );

            $this->addSettingsField(
                "Text Reminder Intro", 
                "textarea", 
                "dunningTextPage",
                0,
                "This text will be shown above reminder details.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Reminder Outro", 
                "textarea", 
                "dunningTextPage",
                0,
                "This text will be shown below reminder details.",
                "text_details_input_mod"
            );
            
            $this->addSettingsField(
                "Text Reminder Payment Deadline", 
                "textarea", 
                "dunningTextPage",
                0,
                "You can announce how many further days you will wait for payment.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Reminder Custom Footer", 
                "textarea", 
                "dunningTextPage",
                0,
                "You can add details like your tax ID.",
                "text_details_input_mod"
            );
        
            $this->addSettingsField(
                "Text Dunning Intro", 
                "textarea", 
                "dunningTextPage",
                0,
                "This text will be shown above dunning details.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Dunning Outro", 
                "textarea", 
                "dunningTextPage",
                0,
                "This text will be shown below dunning details.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Dunning Payment Deadline", 
                "textarea", 
                "dunningTextPage",
                0,
                "You can announce how many further days you will wait for payment.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Dunning Custom Footer", 
                "textarea", 
                "dunningTextPage",
                0,
                "You can add details like your tax ID.",
                "text_details_input_mod"
            );

            /**
             * ###########################################
             * Offer Settings
             * 
             * ###########################################
             */

            //PDF Text Templates
            register_setting(
                'offerTemplateForm', 
                'qi_settings'
            );

            add_settings_section(
                'qi_offerTemplatePage_section',
                __('Offer PDF Templates', 'ev'),
                null,
                'offerTemplatePage'
            );
        
            $this->addSettingsField(
                "Text Offer Intro", 
                "textarea", 
                "offerTemplatePage",
                0,
                "This text will be shown above offer details.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Offer Outro", 
                "textarea", 
                "offerTemplatePage",
                0,
                "This text will be shown below offer details.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Offer Payment Deadline", 
                "textarea", 
                "offerTemplatePage",
                0,
                "You can announce how many further days you will wait for payment.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Offer Custom Footer", 
                "textarea", 
                "offerTemplatePage",
                0,
                "You can add details like your tax ID.",
                "text_details_input_mod"
            );

            /**
             * ###########################################
             * Credit Settings
             * 
             * ###########################################
             */

            //PDF Text Templates
            register_setting(
                'creditTemplateForm', 
                'qi_settings'
            );

            add_settings_section(
                'qi_creditTemplatePage_section',
                __('Credit PDF Templates', 'ev'),
                null,
                'creditTemplatePage'
            );
        
            $this->addSettingsField(
                "Text Credit Intro", 
                "textarea", 
                "creditTemplatePage",
                0,
                "This text will be shown above credit details.",
                "text_details_input_mod"
            );

            $this->addSettingsField(
                "Text Credit Outro", 
                "textarea", 
                "creditTemplatePage",
                0,
                "This text will be shown below credit details.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Credit Payment Deadline", 
                "textarea", 
                "creditTemplatePage",
                0,
                "You can announce how many further days you will wait for payment.",
                "text_details_input_mod"
            );
            $this->addSettingsField(
                "Text Credit Custom Footer", 
                "textarea", 
                "creditTemplatePage",
                0,
                "You can add details like your tax ID.",
                "text_details_input_mod"
            );

        }

        //        
        //TESTING
        //
        /**
         * Function addSettingsField
         * 
         * @param string $name 
         * @param string $type 
         * @param string $page 
         * @param bool   $required 
         * 
         * @return void
         */
        public function addSettingsField(
            $name, 
            $type, 
            $page, 
            $required=0,
            $placeholder="", 
            $class=""
        )
        {
            $callback = "showInputForSetting";
            $optionLabel = $name;
            if ($type=="textarea") {
                $callback = "showTextareaForSetting";
                $optionLabel=strstr(strstr($name, 'Text'), ' ');
            }
            add_settings_field(
                'qi_settings' .$name, 
                __(ucfirst($optionLabel), 'ev'), 
                [$this, $callback],
                $page,
                'qi_'.$page.'_section',
                [
                    "name" => $name,
                    "type" => $type,
                    "placeholder" => $placeholder,
                    "required" => $required,
                    "class" => $class
                ]
            );
        }

        /**
         * Function hideInput
         * 
         * @param array $arguments 
         * 
         * @return void
         */
        public function hideInput(array $arguments)
        {
            
            $options = get_option('qi_settings');
            echo "<input "
                ."id='".str_replace(' ', '', $arguments['name'])."'"
                ."name='qi_settings[".str_replace(' ', '', $arguments['name'])."]'"
                ."type='".$arguments['type']."'"
                ."value='".$options[str_replace(' ', '', $arguments['name'])]."'"
                ."class='' "
                ."style='display:none'"
                ."/>";
                
        }


        /**
         * Function showInputForSetting
         * 
         * @param array $arguments 
         * 
         * @return void
         */
        public function showInputForSetting(array $arguments)
        {
            

            $options = get_option('qi_settings');
            print "<input "
                ."id='".str_replace(' ', '', $arguments['name'])."'"
                ."name='qi_settings[".str_replace(' ', '', $arguments['name'])."]'"
                ."type='".$arguments['type']."'"
                ."value='".$options[str_replace(' ', '', $arguments['name'])]."'"
                ."class='' "
                ."placeholder='".$arguments['placeholder']."'";
            if ($arguments['required']) {
                print " required ";
            };
            print "/>";
                
        }

        /**
         * Function showTextareaForSetting
         * 
         * @param array $arguments 
         * 
         * @return void
         */
        public function showTextareaForSetting(array $arguments)
        {
            $options = get_option('qi_settings');
            
           
            print "<textarea "
                
                ."id='".str_replace(' ', '', $arguments['name'])."'"
                ."name='qi_settings[".str_replace(' ', '', $arguments['name'])."]'"
                ."value='".$options[str_replace(' ', '', $arguments['name'])]."'"
                ."class='' "
                ."placeholder='".$arguments['placeholder']."'";
            if ($arguments['required']) {
                print " required ";
            };
            print ">".
            $options[str_replace(' ', '', $arguments['name'])]
            ."</textarea>";
               
        }

        /**
         * Function addSpacerForSetting
         * 
         * @param array $arguments 
         * 
         * @return void
         */
        public function addSpacerForSetting(array $arguments)
        {
            
            
            print "<div class='tableSpacer'>"
                ."<input type='hidden' value='empty'"
                ." name='qi_settings[".str_replace(' ', '', $arguments['name'])."]'"
                ."> <div>";
                
        }

        /**
         * Function showInputForLogo
         * 
         * @return void
         */
        public function showInputForLogo()
        {
            echo "<script>console.log('B:". get_option('qi_settings')['logoFileUrl'] ."');</script>";
            //if an logog file has already been uploaded
            if (get_option('qi_settings')['logoFileUrl'] != '') {
                //set the upload field to display:none, because their is already file
                echo 
                "<label ".
                    "id='qinv_settings_uploadLogo'".
                    "class='fileUpload'".
                    "style='".
                        "display:none; ".
                        "border:solid 1px #dadce1; ".
                        "border-radius:4px; ".
                        "padding:7px; ".
                    "'".
                ">".
                    "<input ".
                        "id='logoFile' ".
                        "name='logoFile' ".
                        "type='file' ".
                        "style='display:none'".
                        "value='".         
                    "' />".

                    "Upload".
                "</label>";
                //grab the logo image source
                $logoImageSource = get_option('qi_settings')['logoFileUrl'];
                echo $logoImageSource;
                ?>
                <div id='showLogoDiv'>
                    <div class="qinv_settings_logo_tbBuffer">
                        <span
                            id="qinv_settings_delete_logo" 
                            title="Delete Logo"
                            class="delete dashicons dashicons-no"
                        >
                    </span>
                    </div>
                    <img style="width: 220px;" src="<?php echo $logoImageSource;?>">
                    <div class="qinv_settings_logo_tbBuffer" style="height:10px;">
                    </div>
                </div>
                <?php
            } else{
                //if no file has been uploaded the upload field has to be visible
                echo 
                "<label ".
                    "id='qinv_settings_uploadLogo'".
                    "class='fileUpload'".
                    "style='".
                        "border:solid 1px #dadce1; ".
                        "border-radius:4px; ".
                        "padding:7px; ".
                    "'".
                ">".
                    "<input ".
                        "id='logoFile' ".
                        "name='logoFile' ".
                        "type='file' ".
                        "style='display:none'".
                        "value='".         
                    "' />".

                    "Upload".
                "</label>";
                //grab the default image source
                //$logoImageSource = plugin_dir_url(__FILE__).
                //"/files/none_5002.png";

                ?>
                <div id='showLogoDiv' style='display:none;'>
                <div class="qinv_settings_logo_tbBuffer">
                        <span style="display:none;"
                            id="qinv_settings_delete_logo" 
                            title="Delete Logo"
                            class="delete dashicons dashicons-no"
                        >
                    </span>
                    </div>
                    <img style="width: 220px;" src="<?php ?>">
                    <div class="qinv_settings_logo_tbBuffer" style="height:10px;">
                    </div>
                </div>
                <br>
                <br>
                <p id="qinv_settings_logo_message" style="color: red; display:none; margin-bottom:-30px;">Press 'Save Settings' to submit your logo.</p>
                <?php
            }
                
        } 

        /**
         * Function handleFileUploadForLogo
         * 
         * @param string $option a
         * 
         * @return $option
         */
        public function handleFileUploadForLogo($option)
        {
            
            if (!empty($_FILES['logoFile']["tmp_name"])) {
               
                $urls = wp_handle_upload(
                    $_FILES['logoFile'],
                    array('test_form' => false)
                );

                $option['logoFileUrl'] = $urls["url"];
                $option['logoFileFile'] = $urls["file"];
                
            }

            return $option;
        }

        /**
         * Function handleInputForInvoiceSettings
         * 
         * @return void
         */
        public function handleInputForInvoiceSettings()
        {
            
            $GLOBALS['wpdb']->query(
                'ALTER TABLE '.
                $GLOBALS['wpdb']->prefix.
                \QI_Invoice_Constants::TABLE_QI_DETAILS.
                ' AUTO_INCREMENT = 1000'
            );
        }

        /**
         * Function showInputForinvoiceCurrency
         * 
         * @return void
         */
        public function showInputForinvoiceCurrency()
        {
            $options = get_option('qi_settings');
            
            if (empty($options['invoiceCurrency'])) {
                
                echo "<select "
                    ."id='invoiceCurrency'" 
                    ."name='qi_settings[invoiceCurrency]'>"
                    ."<option value='Euro' selected='selected'>Euro</option>"  
                    ."<option value='Dollar'>Dollar</option>"  
                    ."<option value='Other'>Other</option>"  
                    ."</select>";

            } else {
                $items = array("Euro", "Dollar", "Other");
                echo "<select ".
                    "id='invoiceCurrency'". 
                    "name='qi_settings[invoiceCurrency]'>";
                
                foreach ($items as $item) {
                
                    $selected = ($options['invoiceCurrency']==$item) ? 'selected="selected"' : ''; 
                    echo "<option value=".$item." ".$selected.">".$item."</option>";
                }

                echo "</select>";
            }
        }

        /**
         * Function showInputForinvoiceUnit
         * 
         * @return void
         */
        public function showInputForinvoiceUnit()
        {
            $options = get_option('qi_settings');
            
            if (empty($options['invoiceUnit'])) {
                
                echo "<select "
                    ."id='qi_settings[invoiceUnit]'" 
                    ."name='qi_settings[invoiceUnit]'>"
                    ."<option value='Amount' selected='selected'>Amount</option>"  
                    ."<option value='Hours'>Hours</option>"  
                    ."<option value='Liter'>Liter</option>"  
                    ."</select>";

            } else {
                $items = array("Amount", "Hours", "Liter");
                echo "<select ".
                    "id='qi_settings[invoiceUnit]'" .
                    "name='qi_settings[invoiceUnit]'>";
                
                foreach ($items as $item) {
                
                    $selected = ($options['invoiceUnit']==$item) ? 'selected="selected"' : ''; 
                    echo "<option value=".$item." ".$selected.">".$item."</option>";
                }

                echo "</select>";
            }
        }

        /**
         * Function showInputForinvoiceDotType
         * 
         * @return void
         */
        public function showInputForinvoiceDotType()
        {
            $options = get_option('qi_settings');
            
            if (empty($options['invoiceDotType'])) {
                
                echo "<select "
                    ."id='qi_settings[invoiceDotType]'" 
                    ."name='qi_settings[invoiceDotType]'>"
                    ."<option value='1.000,00' selected='selected'>1.000,00</option>"  
                    ."<option value='1,000.00'>1,000.00</option>"
                    ."</select>";
            } else {
                $items = array("1,000.00", "1.000,00");
                echo "<select ".
                    "id='qi_settings[invoiceDotType]'" .
                    "name='qi_settings[invoiceDotType]'>";
                
                foreach ($items as $item) {
                
                    $selected = ($options['invoiceDotType']==$item) ? 'selected="selected"' : ''; 
                    echo "<option value=".$item." ".$selected.">".$item."</option>";
                }

                echo "</select>";
            }
        }

        /**
         * Function showInputForTaxTypes
         * 
         * @return void
         */
        public function showInputForTaxTypes()
        {
            $options = get_option('qi_settings');
            
            if (empty($options['taxTypes'])) {
                
                echo "<select "
                    ."id='qi_settings[taxTypes]'" 
                    ."name='qi_settings[taxTypes]'>"
                    ."<option value='2' selected='selected'>2</option>"  
                    ."</select>";

            } else {
                $items = array("1", "2", "3", "4", "5", "6", "7", "8");
                echo "<select ".
                    "id='qi_settings[taxTypes]'" .
                    "name='qi_settings[taxTypes]'>";
                
                foreach ($items as $item) {
                
                    $selected = ($options['taxTypes']==$item) ? 'selected="selected"' : ''; 
                    echo "<option value=".$item." ".$selected.">".$item."</option>";
                }

                echo "</select>";
            }
        }


        /**
         * Function qiSettingsSectionCompanyCallback TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsSectionCompanyCallback()
        {

            //echo __('Fill in details of your company ..', 'ev');
        }

        /**
         * Function qiSettingsSectionCompanyCallback TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsSectionInvoiceCallback()
        {

            // echo __('Fill in Invocie details ..', 'ev');
        }

        /**
         * Function qiSettingsSectionContactCallback TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsSectionContactCallback()
        {

            // echo __('Fill in Contact details ..', 'ev');
        }

        /**
         * Function qiSettingsSectionDunningCallback TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsSectionDunningCallback()
        {

            //echo __('Fill in Dunning details ..', 'ev');
        }

        /**
         * Function qiSettingsSectionCompanyCallback TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsSectionBankCallback()
        {

            // echo __('Fill in details of your bank accounts ..', 'ev');
        }

        /**
         * Function qiSettingsSectionCompanyCallback TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsSectionMailCallback()
        {

            // echo __('Fill in details of your Mail Account ..', 'ev');
        }


        /**
         * Function qiOptionsPage TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiOptionsPage()
        {
            include_once plugin_dir_path(__FILE__) .
                'partials/settings/q_invoice-admin-settings.php';
        }
        
      

// ............................................................................
// ............................................................................
// ........................####...######...####...##..##.......................
// .......................##..##......##..##..##...####........................
// .......######..######..######......##..######....##....######..######.......
// .......................##..##..##..##..##..##...####........................
// .......................##..##...####...##..##..##..##.......................
// ............................................................................
// ............................................................................
// ............................................................................


        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function removeLogoServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");
            $data = Interface_Settings::removeLogo();
            echo $data;

            wp_die();
        }


        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function fetchLastIDServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");
            if (Interface_Invoices::getLastID()) {
                echo 1 + Interface_Invoices::getLastID();    
            } else {
                echo get_option('qi_settings')['noStart'];
            }

            wp_die();
        }

        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function fetchCurrencyServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");
            
            $currencySymbol = "€";
            if (get_option('qi_settings')['invoiceCurrency'] == "Euro") {
                $currencySymbol = "€";
            } else if (get_option('qi_settings')['invoiceCurrency'] == "Dollar") {
                $currencySymbol = "$";
            } else if (get_option('qi_settings')['invoiceCurrency'] == "Other") {
                $currencySymbol = get_option('qi_settings')['currencySign'];
            }

            echo $currencySymbol;
            wp_die();
        }

        /**
         * Sends given content with wp_send
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function sendMailServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");

            $recipient = 'schunke30@gmail.com';//$_POST['recipient'];
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $headers = $_POST['headers'];
            $attachments = $_POST['attachments'];

            //$content_type = function() { return 'text/html'; };
            //add_filter( 'wp_mail_content_type', $content_type );
            //add_filter( 'wp_mail_from', $headers );
            
            $success = wp_mail($recipient, $subject, $message, $headers, $attachments);

            echo $success;
            wp_die();
        }

        /**
         * Function fetchContactsServerSide
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function fetchContactsServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");
            
            $data = Interface_Contacts::getAllContacts();
            echo json_encode($data);
            wp_die();
        }

        /**
         * Function makeFilename
         * 
         * @param int $invoiceID 
         * 
         * @return string
         *
         * @since 1.0.0
         */
        public function makeFilename($invoiceID, $fileType='invoice') 
        {
            $invoiceDate = Interface_Invoices::getInvoiceDataItem($invoiceID, "invoice_date");
            $invoiceDate = str_replace('-', '_', $invoiceDate);
            $company = Interface_Invoices::getInvoiceDataItem($invoiceID, "company");
            $lastName = Interface_Invoices::getInvoiceDataItem($invoiceID, "lastname");
            $firstName = Interface_Invoices::getInvoiceDataItem($invoiceID, "firstname");
            $customerName = $firstName.$lastName;    
            if ($company) {
                $company = str_replace('/', '_', $company);
                $company = str_replace(':', '_', $company);
                $company = str_replace('?', '_', $company);
                $company = str_replace('"', '_', $company);
                $company = str_replace('<', '_', $company);
                $company = str_replace('>', '_', $company);
                $company = str_replace('|', '_', $company);
                $company = str_replace('.', '_', $company);
                $company = str_replace(' ', '', $company);
                $customerName = $company;
            }

            $filename = 
                "Invoice-".
                get_option('qi_settings')['prefix'].
                $invoiceID. "-".
                $customerName. "-".
                $invoiceDate;
            if($fileType != 'invoice'){
                $filename = $filename."-".$fileType;
            }
            
            return $filename;
        }

        /**
         * Function printInvoiceTemplate
         * 
         * @param int $invoiceID 
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function printInvoiceTemplate($invoiceID)
        {
            
            ob_start();
            include_once INVOICE_ROOT_PATH . 
            "/admin/partials/export/export.php";  
            exportInvoice($invoiceID, "invoice");             
            $exportInv= ob_get_contents();
            ob_end_clean();
            include  INVOICE_ROOT_PATH . 
            //'/admin/partials/export/html2pdf.class.php';
            '/admin/partials/export/html2pdf/vendor/autoload.php';
            try {
                $html2pdf = new Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'de');
                $html2pdf->writeHTML($exportInv, isset($_GET['vuehtml']));
                // PDF Name : Invoice/Dunning/etc-$prefix$no-Customername_$datum
                $html2pdf->Output(
                    INVOICE_ROOT_PATH . 
                    '/pdf/'.
                    $this->makeFilename($invoiceID, 'invoice').
                    '.pdf', 'F'
                );
            } catch (Spipu\Html2Pdf\Exception\Html2PdfException $e) {
                echo $e;
                return 'fail';
                //exit;
            }

            return 'success';
            
        }

        /**
         * Function printDunningTemplate
         * 
         * @param int $invoiceID 
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function printDunningTemplate($invoiceID, $dunningType)
        {
            
            ob_start();
            include_once INVOICE_ROOT_PATH . 
            "/admin/partials/export/export.php";
            exportInvoice($invoiceID, $dunningType);             
            $exportInv= ob_get_contents();
            ob_end_clean();
            
            include  INVOICE_ROOT_PATH . 
            //'/admin/partials/export/html2pdf.class.php';
            '/admin/partials/export/html2pdf/vendor/autoload.php';
            $nameExtension = 'reminder';
            if($dunningType == 'reminder'){
                $nameExtension = 'reminder1';
            } else if($dunningType == 'dunningI'){
                $nameExtension = 'reminder2';
            } else if($dunningType == 'dunningII'){
                $nameExtension = 'reminder3';
            }
            try {
                $html2pdf = new Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'de');
                $html2pdf->writeHTML($exportInv, isset($_GET['vuehtml']));
                // PDF Name : Invoice/Dunning/etc-$prefix$no-Customername_$datum
                $html2pdf->Output(
                    INVOICE_ROOT_PATH . 
                    '/pdf/'.
                    $this->makeFilename($invoiceID, $nameExtension).
                    '.pdf', 'F'
                );
            } catch (Spipu\Html2Pdf\Exception\Html2PdfException $e) {
                echo $e;
                return 'fail';
                //exit;
            }

            return 'success';
            
        }
        
        /**
         * Function saveInvoiceServerSide.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function saveInvoiceServerSide()
        {
            // TODO Nonce in Form? How?
            //check_ajax_referer("q_invoice_nonce");
            //$this->_plugin_name.
            if (wp_verify_nonce($_POST['q_invoice_nonce'], $_POST['action'])) {
                
                $invoiceID = Interface_Invoices::saveArrayToDB($_POST);

                $pdfSuccess = $this->printInvoiceTemplate($invoiceID);
                $dunningData = $this->getDunningDays(strtotime($_POST['dateOfInvoice']));
                $dunningDays = $dunningData[1];
                $dunningClass = $dunningData[0];
                // NOT TESTING
                $response['success'] = true;
                $response['data'] = $_POST;
                $response['id'] = $invoiceID;
                $response['pdf'] = $pdfSuccess;
                $response['dunningclass'] = $dunningClass;
                $response['dunningdays'] = $dunningDays;
                //$response['type'] = $_POST['action'];
                
                echo json_encode($response);
                
                
                
                wp_die();
            }
            wp_die();
        }

        public function printConsoleLog($text){
            echo "<script>console.log('".$text."');";
        }

        /**
         * Function getDunningDays.
         * 
         * @param string $invoiceDate
         * @return array(dunningClass, numberOfDunningDays)
         *
         * @since 1.0.0
         */
        function getDunningDays($invoiceDate){
            $currentDate = strtotime(date('Y-m-d'));
            $reminderDays = intVal(get_option('qi_settings')['reminderDayLimit']);
            $reminderDate = $this->addWorkingDays($invoiceDate, $reminderDays);
            $dunningIDays = intVal(get_option('qi_settings')['dunning1daylimit']);
            $dunningIDate = $this->addWorkingDays($invoiceDate, $dunningIDays);
            $dunningIIDays = intVal(get_option('qi_settings')['dunning2daylimit']);
            $dunningIIDate = $this->addWorkingDays($invoiceDate, $dunningIIDays);

            $circleClass = '';
            $numberOfDunningDays = '';
            if($dunningIIDate <= $currentDate){
                $circleClass = 'dunningII';
                $numberOfDunningDays = ceil(abs($currentDate - $dunningIIDate) / 86400);
            } else if($dunningIDate <= $currentDate){
                $circleClass = 'dunningI';
                $numberOfDunningDays = ceil(abs($currentDate - $dunningIDate) / 86400);
            } else if($reminderDate <= $currentDate){
                $circleClass = 'reminder';
                $numberOfDunningDays = ceil(abs($currentDate - $reminderDate) / 86400);
            }
            return array($circleClass, $numberOfDunningDays);
        }

        /**
         * Function to add X Working Days on a start Date Y
         * 
         * @param {Start date on which the working days have to be added --> use strtotime of the Startdate for example} $timestamp 
         * @param {Number of Wokring Days that have to be added} $days 
         * @param {Week Days that have to be skipped --> array (Monday-Sunday) eg. array("Saturday","Sunday")} $skipdays 
         * @param {Further Dates that have to be skipped --> array (YYYY-mm-dd) eg. array("2012-05-02","2015-08-01")} $skipdates 
         * @returns date("Y-m-d, $newTime")
         */
        function addWorkingDays($timestamp, $days, $skipdays = array("Saturday", "Sunday"), $skipdates = array("")) {
            $i = 1;
            while ($days >= $i) {
                $timestamp = strtotime("+1 day", $timestamp);
                if ( (in_array(date("l", $timestamp), $skipdays)) || (in_array(date("Y-m-d", $timestamp), $skipdates)) ){
                    $days++;
                }
                $i++;
            }
            return $timestamp;
          }

        /**
         * Describe this
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function updateInvoiceServerSide()
        {
            $dunningData = $this->getDunningDays(strtotime($_POST['dateOfInvoice']));
            $extraDunningPDFArray = $_POST['insertInDatabase'];
            $reminderPDF = $extraDunningPDFArray[sizeof($extraDunningPDFArray)-3];
            $dunIPDF = $extraDunningPDFArray[sizeof($extraDunningPDFArray)-2];
            $dunIIPDF = $extraDunningPDFArray[sizeof($extraDunningPDFArray)-1];
            $dunningDays = $dunningData[1];
            $dunningClass = $dunningData[0];

            $response['dunningclass'] = $dunningClass;
            $response['dunningdays'] = $dunningDays;

            if (wp_verify_nonce($_POST['q_invoice_nonce'], $_POST['action'])) {

                Interface_Invoices::updateArrayInDB($_POST);
                
                if($reminderPDF){
                    $this->printDunningTemplate($_POST['invoice_id'], 'reminder');
                } else if($dunIPDF){
                    $this->printDunningTemplate($_POST['invoice_id'], 'dunningI');
                } else if($dunIIPDF){
                    $this->printDunningTemplate($_POST['invoice_id'], 'dunningII');
                } else{
                    $this->printInvoiceTemplate($_POST['invoice_id']);
                }
                $response['success'] = true;
                $response['data'] = $_POST;
                echo json_encode($response);


                wp_die();
            }
            $response['success'] = false;
            $response['data'] = $_POST;
            echo json_encode($response);
            wp_die();
        }

        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * Testing TODO : js/q_invoice-admin-ajax.js file is
         * "always on"
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function deleteInvoiceServerSide()
        {
            //check_ajax_referer($this->_plugin_name."_nonce");

            Interface_Invoices::deactivateInvoice($_POST['id']);
            //Interface_Invoices::deleteRowFromDB($_POST['id']);
            $response['success'] = true;
            echo json_encode($response);
            wp_die();
        }

        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * Testing TODO : js/q_invoice-admin-ajax.js file is
         * "always on"
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function reactivateInvoiceServerSide()
        {
            //check_ajax_referer($this->_plugin_name."_nonce");

            Interface_Invoices::reactivateInvoice($_POST['id']);
            //Interface_Invoices::deleteRowFromDB($_POST['id']);
            $response['success'] = true;
            echo json_encode($response);
            wp_die();
        }
        
        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * Testing TODO : js/q_invoice-admin-ajax.js file is
         * "always on"
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function editInvoiceServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");

            $response = Interface_Invoices::getInvoiceData($_POST["id"]);
            
            echo json_encode($response);

            wp_die();
        }

        /**
         * Function checkInvoiceServerSide
         * 
         * This might be obsolete 
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function checkInvoiceServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");

            $response = Interface_Invoices::getInvoiceDataItem(
                $_POST["id"], 
                $_POST["item"]
            );
        
            echo $response;

            wp_die();
        }
        /**
         * Function updateInvoiceHeaderItem
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function updateInvoiceHeaderServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");
            
            if (json_encode($_POST["data"]) == '{"paydate":""}') {
                //echo json_encode(Interface_Invoices::getInvoiceData($_POST["id"]));
                $random = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(5/strlen($x)) )),1,5);
                $newArchivedInvoice = fopen(plugin_dir_path(__FILE__)."00-". $random . "-archivedInvoice-". $_POST["id"] .".txt", "w") or die("Unable to open file!");
                $txt = json_encode(Interface_Invoices::getInvoiceData($_POST["id"]));
                fwrite($newArchivedInvoice, $txt);
                fclose($newArchivedInvoice);

            
                
            } else {
                
                echo json_encode($_POST["data"]);
                //echo "success";
            }

            Interface_Invoices::updateInvoiceHeaderItem(
                $_POST["id"], 
                $_POST["data"]
            );
             
            wp_die();
        }

        /**
         * Function saveContactServerSide.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function saveContactServerSide()
        {
            // TODO Nonce in Form? How?
            //check_ajax_referer("q_invoice_nonce");
            //$this->_plugin_name.
            if (wp_verify_nonce($_POST['q_invoice_nonce'], $_POST['action'])) {

                $response['id'] = Interface_Contacts::saveArrayToDB($_POST);

                // NOT TESTING
                $response['success'] = true;
                $response['type'] = "save";
                $response['contactData'] = $_POST;
                echo json_encode($response);   

                wp_die();

            } else {
                echo "error: Nonce is not accepted!";
                $response['success'] = false;
                echo json_encode($response);

                wp_die();
            } 
        }

        /**
         * Describe this
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function updateContactServerSide()
        {
            if (wp_verify_nonce($_POST['q_invoice_nonce'], $_POST['action'])) {

                Interface_Contacts::updateContact($_POST);
                
                
                $response['success'] = true;
                $response['type'] = "update";
                $response['contactData'] = $_POST;
                echo json_encode($response);


                wp_die();
            }
            $response['success'] = false;
            echo json_encode($response);
            wp_die();
        }

        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * Testing TODO : js/q_invoice-admin-ajax.js file is
         * "always on"
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function editContactServerSide()
        {
            check_ajax_referer($this->_plugin_name . "_nonce");

            $response = Interface_Contacts::getContactData($_POST["id"]);

            echo json_encode($response);

            wp_die();
        }

        /**
         * TESTIG Register the Ajax for the admin area.
         * 
         * Testing TODO : js/q_invoice-admin-ajax.js file is
         * "always on"
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public function deleteContactServerSide()
        {
            //check_ajax_referer($this->_plugin_name."_nonce");

            
            Interface_Contacts::deleteRowFromDB($_POST['id']);
            $response['id'] = $_POST['id'];
            $response['success'] = true;
            echo json_encode($response);
            wp_die();
        }
        

// .............................................................................
// .............................................................................
// .......................##...##..######..##..##..##..##.......................
// .......................###.###..##......###.##..##..##.......................
// .......######..######..##.#.##..####....##.###..##..##..######..######.......
// .......................##...##..##......##..##..##..##.......................
// .......................##...##..######..##..##...####........................
// .............................................................................
// .............................................................................
// .............................................................................


        /**
         * Function addQInvoiceAdminMenus
         * 
         * @return void
         */
        public function addQInvoiceAdminMenus()
        {
            add_menu_page(
                __('Q Invoice', 'Ev'),
                __('Q Invoice', 'Ev'),
                'manage_options',
                'q_invoice',
                array($this, 'loadInvoicePage'),
                'dashicons-money',
                30
            );

            add_submenu_page(
                'q_invoice',
                __('Invoices', 'Ev'),
                __('Invoices', 'Ev'),
                'manage_options',
                'q_invoice',
                array($this, 'loadInvoicePage')
            );

            add_submenu_page(
                'q_invoice',
                __('Contacts', 'Ev'),
                __('Contacts', 'Ev'),
                'manage_options',
                'q_contacts',
                array($this, 'loadContactsPage')
            );

            add_submenu_page(
                'q_invoice',
                __('Settings', 'Ev'),
                __('Settings', 'Ev'),
                'manage_options',
                'qi_settings',
                array($this, 'qiOptionsPage')
            );

        }



        /**
         * Function loadInvoicePage
         * 
         * Load the plugin invoice page partial.
         * 
         * @return void         
         */
        public function loadInvoicePage()
        {

            include_once plugin_dir_path(__FILE__) .
                'partials/invoice/q_invoice-admin-invoices.php';

            invoice_list(); 

        }

        /**
         * Function loadContactsPage
         * 
         * @return void
         */
        public function loadContactsPage()
        {
            include_once plugin_dir_path(__FILE__) .
                'partials/contacts/q_invoice-admin-contacts.php';
        }
       

        /**
         * Function loadSettingsPage
         * 
         * @return void
         */
        public function loadSettingsPage()
        {
            include_once plugin_dir_path(__FILE__) .
                'partials/settings/q_invoice-admin-settings.php';
        }

    
        /**
         * Function loadUpgradeInvoicePageContent
         * 
         * Load the plugin Update 2 PRO page partial.
         * 
         * @return void
         */
        public function loadUpgradeInvoicePageContent()
        {
            include_once plugin_dir_path(__FILE__) .
                'partials/invoice-admin-upgrade.php';
        }
    } // end class
}  // endif class exists

if (!function_exists('Qi_cssStyle')) {
    /** 
     * Add special Color to "upgrade to pro"
     * 
     * @return void
     */
    function Qi_cssStyle()
    {
        wp_enqueue_style(
            'my-admin-style',
            get_stylesheet_directory_uri() .
                '/css/q_invoice-admin.css'
        );
    }
}
add_action('admin_enqueueScripts', 'Qi_cssStyle');
