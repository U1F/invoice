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

            include_once \QI_Invoice_Constants::PART_PATH_QI .
                "/admin/partials/invoice/include/" .
                "interface-invoices.php";

                include_once \QI_Invoice_Constants::PART_PATH_QI .
                "/admin/partials/invoice/include/" .
                "interface-contacts.php";
        }

        /**
         * Function qiSettingsInit TESTING SETTINGS
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function qiSettingsInit()
        {
            
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
            
             
            add_settings_field(
                'qi_settings' ."logoFileUrl", 
                null, 
                [$this, 'hideInput'],
                "pluginPage",
                'qi_'.'pluginPage'.'_section',
                $array = [
                    "name" => "logo File Url",
                    "type" => "text"
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
                    "type" => "text"
                ]
            );

            add_settings_field(
                'qi_settingsLogoFile', 
                'Logo', 
                [$this, 'showInputForLogo'],
                'pluginPage',
                'qi_pluginPage_section'
            );
            

            // SETTINGS SECTION INVOICE
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

            add_settings_field(
                'qi_settingsInvoiceCurrency', 
                'Invoice Currency', 
                [$this, 'showInputForinvoiceCurrency'],
                'invoicePage',
                'qi_invoicePage_section'
            );
            $options = get_option('qi_settings');

            if ($options["invoiceCurrency"] == "Other") {
                $this->addSettingsField("currency Sign", "text", "invoicePage", 1);
            }
            //$this->addSettingsField("tax Types", "number", "invoicePage");
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
            
            $this->addSettingsField("unit", "text", "invoicePage");
            
           

            // SETTINGS SECTION CONTACTS 
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

            // SETTINGS SECTION DUNNING
            register_setting('dunningForm', 'qi_settings');

            add_settings_section(
                'qi_dunningPage_section',
                __('Dunning Fees', 'ev'),
                [$this, 'qiSettingsSectionDunningCallback'],
                'dunningPage'
            );
            $this->addSettingsField("reminder", "text", "dunningPage");
            $this->addSettingsField("dunning 1", "text", "dunningPage");
            $this->addSettingsField("dunning 2", "text", "dunningPage");
            

            // SETTINGS SECTION BANK 
            register_setting('bankForm', 'qi_settings');

            add_settings_section(
                'qi_bankPage_section',
                __('Bank Details', 'ev'),
                [$this, 'qiSettingsSectionBankCallback'],
                'bankPage'
            );
            $this->addSettingsField("IBAN 1", "text", "bankPage", 1);
            $this->addSettingsField("BIC 1", "text", "bankPage", 1);
            $this->addSettingsField("bank Name 1", "text", "bankPage", 1);
            
            add_settings_field(
                'qi_settings' ."BankSpacer1", 
                null, 
                [$this, 'addSpacerForSetting'],
                "bankPage",
                'qi_'.'bankPage'.'_section',
                $array = [
                    "name" => "BankSpacer1",
                    "type" => "hidden"
                ]
            );
            
            $this->addSettingsField("IBAN 2", "text", "bankPage");
            $this->addSettingsField("BIC 2", "text", "bankPage");
            $this->addSettingsField("bank Name 2", "text", "bankPage");


            add_settings_field(
                'qi_settings' ."BankSpacer2", 
                "", 
                [$this, 'addSpacerForSetting'],
                "bankPage",
                'qi_'.'bankPage'.'_section',
                $array = [
                    "name" => "BankSpacer2",
                    "type" => "hidden"
                ]
            );

            $this->addSettingsField("PayPal.Me", "text", "bankPage");

            
            // SETTINGS SECTION EMAIL 
            register_setting('mailForm', 'qi_settings');

            add_settings_section(
                'qi_mailPage_section',
                __('Mail Server Details', 'ev'),
                [$this, 'qiSettingsSectionMailCallback'],
                'mailPage'
            );
            
            $this->addSettingsField("email", "text", "mailPage");
            $this->addSettingsField("server", "text", "mailPage");
            $this->addSettingsField("port", "number", "mailPage");       
            $this->addSettingsField("password", "password", "mailPage");


            // SETTINGS SECTION INVOICE TEXTS 
            register_setting(
                'invoiceTextForm', 
                'qi_settings'
            );

            add_settings_section(
                'qi_invoiceTextPage_section',
                __('Invoice Text Details', 'ev'),
                null,
                'invoiceTextPage'
            );
        
            $this->addSettingsField("invoice Text Intro", "textarea", "invoiceTextPage");
            $this->addSettingsField("invoice Text Outro", "textarea", "invoiceTextPage");
            $this->addSettingsField("custom Footer", "textarea", "invoiceTextPage");

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
        public function addSettingsField($name, $type, $page, $required=0)
        {
            $callback = "showInputForSetting";
            if ($type=="textarea") {
                $callback = "showTextareaForSetting";
            }
            add_settings_field(
                'qi_settings' .$name, 
                __(ucfirst($name), 'ev'), 
                [$this, $callback],
                $page,
                'qi_'.$page.'_section',
                $array = [
                    "name" => $name,
                    "type" => $type,
                    "required" => $required,
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
            $additionalInputAttributes = " ";

            $options = get_option('qi_settings');
            print "<input "
                .$additionalInputAttributes
                ."id='".str_replace(' ', '', $arguments['name'])."'"
                ."name='qi_settings[".str_replace(' ', '', $arguments['name'])."]'"
                ."type='".$arguments['type']."'"
                ."value='".$options[str_replace(' ', '', $arguments['name'])]."'"
                ."class='' ";
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
                ."class='' ";
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
                ."<input type='hidden' value='emtpy'"
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
            
            echo 

                "<label ".
                    "class='fileUpload'".
                    "style='".
                        "border:solid 1px #c0c0c0; ".
                        "border-radius:4px; ".
                        "padding:7px; ".
                        "min-height:32px; ".
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
            //file_put_contents('error.txt', file_get_contents('error.txt').print_r($option, true));

            
            //file_put_contents('error.txt', file_get_contents('error.txt').print_r($_FILES, true));
            
            if (!empty($_FILES['logoFile']["tmp_name"])) {
               
                $urls = wp_handle_upload(
                    $_FILES['logoFile'],
                    array('test_form' => false)
                );

                //file_put_contents('error.txt', file_get_contents('error.txt').print_r($urls, true));

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
            echo "WE WERE HERE0";
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
                    ."id='qi_settings[invoiceCurrency]'" 
                    ."name='qi_settings[invoiceCurrency]'>"
                    ."<option value='Euro' selected='selected'>Euro</option>"  
                    ."</select>";

            } else {
                $items = array("Euro", "Dollar", "Other");
                echo "<select ".
                    "id='qi_settings[invoiceCurrency]'" .
                    "name='qi_settings[invoiceCurrency]'>";
                
                foreach ($items as $item) {
                
                    $selected = ($options['invoiceCurrency']==$item) ? 'selected="selected"' : ''; 
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
                ["name" => "invoice-autocomplete", "partial" => "invoice", "dependencies" => []],
                ["name" => "contacts", "partial" => "contacts", "dependencies" => []],
                ["name" => "settings", "partial" => "settings", "dependencies" => []],
                ["name" => "export", "partial" => "export", "dependencies" => []],
                
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
            echo 1 + Interface_Invoices::getLastID();

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
            include_once \QI_Invoice_Constants::PART_PATH_QI . 
            "/admin/partials/export/export.php";  
            exportInovice($invoiceID, "invoice");             
            $exportInv= ob_get_contents();
            ob_end_clean();
            include  \QI_Invoice_Constants::PART_PATH_QI . 
            '/admin/partials/export/html2pdf.class.php';
            try {
                $html2pdf = new HTML2PDF('P', 'A4', 'de');
                $html2pdf->writeHTML($exportInv, isset($_GET['vuehtml']));
                $html2pdf->Output(
                    \QI_Invoice_Constants::PART_PATH_QI . 
                    'pdf/'.
                    "Invoice".
                    $invoiceID.
                    '.pdf', 'F'
                );
            } catch (HTML2PDF_exception $e) {
                echo $e;
                exit;
            }
            

            
            
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

                $this->printInvoiceTemplate($invoiceID);
                // NOT TESTING
                $response['success'] = true;
                echo json_encode($response);
                // TEST:
                // echo json_encode($_POST);
                
                wp_die();
            }
            wp_die();
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
            if (wp_verify_nonce($_POST['q_invoice_nonce'], $_POST['action'])) {

                Interface_Invoices::updateArrayInDB($_POST);
                
                $this->printInvoiceTemplate($_POST['invoice_id']);
                $response['success'] = true;
                $response['data'] = $_POST;
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
        public function deleteInvoiceServerSide()
        {
            //check_ajax_referer($this->_plugin_name."_nonce");

            
            Interface_Invoices::deleteRowFromDB($_POST['id']);
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
            $invoice_adminurl = admin_url() . '?page=q_invoice';

            include_once plugin_dir_path(__FILE__) .
                'partials/invoice/q_invoice-admin-invoices.php';


            if (isset($_GET['action'])) {
                if ($_POST && ($_GET["action"]) == "new") {
                    // process the posted data and display summary page 
                    //- not pretty :(
                    //q_invoice_contact_save($_POST);
                } else if ($_POST && ($_GET["action"]) == "q_invoice_final") {

                    //q_invoice_send($_POST);
                }
            }
            
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
