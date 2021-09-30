<?php
/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 * 
 * PHP version 5
 * 
 * @category   Class_For_Plugin_Activation
 * @package    Mailing
 * @subpackage Mailing/Includes
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       qanuk.io
 * @since      1.0.0
 */

if (!class_exists('QI_Invoice_Activator')) {
    /**
     * Class QI_Invoice_Activator
     * 
     * @category Category
     * @package  QI_Invoice/Activator
     * @author   qanuk.io <support@qanuk.io>
     * @license  License example.org
     * @link     https://www.qanuk.io/
     * 
     * @return void
     */
    class QI_Invoice_Activator
    {
        private $_TABLE_QI_CONTACTS;
        private $_table_i_header;
        private $_table_i_details;
        public $defaultOptions = array(    
            "company" => "testHoersaal",
            "additional" => "testQanuk",
            "firstName" => "testBob",
            "lastName" => "testMüller",
            "street" => "testStraße",
            "ZIP" => "99999",
            "city" => "testStadt",
            "logoFileUrl" => "",
            "logoFileFile" => "",
            "prefix" => "test",
            "noStart" => "1",
            "invoiceCurrency" => "",
            "invoiceUnit" => "Amount",
            "taxTypes" => 2,
            "tax1" => 19,
            "tax2" => 7,
            "mail" => "support@qanuk.io",
            "phone" => "01719999999",
            "website" => "testWebsite",
            "facebook" => "testFacebook",
            "instagram" => "testInsta",
            "reminder" => 2,
            "dunning1" => 8,
            "dunning2" => 100,
            "IBAN1" => "DE998888888887777777",
            "BIC1" => "TESTBIC1XX",
            "bankName1" => "testBank",
            "BankSpacer1" => "",
            "IBAN2" => "",
            "BIC2" => "",
            "bankName2" => "",
            "BankSpacer2" => "",
            "PayPal" => "testPp",
            "server" => "testServer",
            "port" => 42,
            "email" => "testuser@testServer.org",
            "password" => "testPasswort",
            "customFooter" => "Steuernummer XXXXXXXX ST-ID XXXXXXXXX",
            "invoiceTextIntro" => "Folgende Leistung stellen wir Ihnen in Rechnung:",
            "invoiceTextOutro" => "Danke für die gute Zusammenarbeit!",
            "invoiceTextPaymentDeadline" => "Zahlungsziel: 10 Tage ohne Abzug."
        );

        /**
         * Function activate
         *
         * Defines tables, which are needed
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public static function activate()
        {
            $this_object = new self;
            $this_object->_defineTableNames();
            $this_object->_qInvoiceCreateDbInvoiceH();
            $this_object->_qInvoiceCreateDbInvoiceD();
            $this_object->_qInvoiceCreateDbContacts();
            $this_object->_setDefaultSettings();

            $this_object->_invoiceCreateDbSettings();
            // Daniel: 26.06. - hier müssen noch 
            // alle weiteren Tabellen erzeugt werden
            // $this_object->q_invoice_create_db_template();
            
           
        }
        /**
         * Function _setDefaultSettings
         *
         * Set Default Settings
         *
         * @since 1.0.0
         * 
         * @return void
         */
        private function _setDefaultSettings()
        {
            do_action('qm/debug', 'Activate happened!');
            
            update_option('qi_settings', $this->defaultOptions);
            
        }

        /**
         * Function _defineTableNames
         *
         * Defines table names
         *
         * @since 1.0.0
         * 
         * @return void
         */
        private function _defineTableNames()
        {
            include_once plugin_dir_path(__FILE__) . 
            'class-q_invoice-constants.php';
            
            global $wpdb;

            $this->_TABLE_QI_CONTACTS = $wpdb->prefix . 
                QI_Invoice_Constants::TABLE_QI_CONTACTS;

            $this->_table_i_header = $wpdb->prefix . 
                QI_Invoice_Constants::TABLE_QI_HEADER;
            
            $this->_table_i_details = $wpdb->prefix . 
                QI_Invoice_Constants::TABLE_QI_DETAILS;
            
            $this->_table_settings = $wpdb->prefix . 
                QI_Invoice_Constants::TABLE_SETTINGS;
            
            // Daniel: 26.06. - hier müssen noch 
            // alle weiteren Tabellen erzeugt werden
        }

        /**
         * Function _q_InvoiceCreateDbContacts
         *
         * Creates Invoice Contacts database table
         *
         * @since 1.0.0
         * 
         * @return void
         */
        private function _qInvoiceCreateDbContacts()
        {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->_TABLE_QI_CONTACTS;

            $sql = "CREATE TABLE $table_name (
                id int(12) NOT NULL AUTO_INCREMENT,
                company varchar(128) NOT NULL,
                additional varchar(128) NOT NULL,
                lastname varchar(128) NOT NULL,
                firstname varchar(32) NOT NULL,
                street varchar(128) NOT NULL,
                zip int(5) NOT NULL,
                city varchar(64) NOT NULL,
                email varchar(256) NOT NULL,
                date DATETIME DEFAULT CURRENT_TIMESTAMP,
                status int(1),
                PRIMARY KEY (id)
            ) $charset_collate;";

            include_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }

        /**
         * Function _qInvoiceCreateDbInvoiceH
         *
         * Creates Invoice database tables
         *
         * @since 1.0.0
         * 
         * @return void
         */
        private function _qInvoiceCreateDbInvoiceH()
        {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->_table_i_header;

            $sql = "CREATE TABLE $table_name (
                id int(12) NOT NULL AUTO_INCREMENT,
                prefix varchar(8),
                invoice_date date,
                delivery_date date,
                customerID int(12),
                company varchar(128),
                additional varchar(128) NOT NULL,
                lastname varchar(128) NOT NULL,
                firstname varchar(32) NOT NULL,
                street varchar(128) NOT NULL,
                zip int(5) NOT NULL,
                city varchar(64) NOT NULL,
                email varchar(256) NOT NULL,
                bank varchar(64),
                date_changed datetime NOT NULL,
                dunning1 int(1),
                date_dunning1 datetime,
                dunning2 int(2),
                date_dunning2 datetime,
                cancellation int(1),
                date_cancellation datetime,
                paydate date,
                PRIMARY KEY (id)
            ) $charset_collate;";

            include_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }

        /**
         * Function _qInvoiceCreateDbInvoiceD
         *
         * Creates Invoice database tables
         *
         * @since 1.0.0
         * 
         * @return void
         */
        private function _qInvoiceCreateDbInvoiceD()
        {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->_table_i_details;

            $sql = "CREATE TABLE $table_name (
                id int(12) NOT NULL AUTO_INCREMENT,
                invoice_id int(12) NOT NULL,
                position int(3) NOT NULL,
                description varchar(256) NOT NULL,
                amount varchar(10) NOT NULL,
                amount_plan varchar(10) NOT NULL,
                discount varchar(8) NOT NULL,
                discount_type varchar(20) NOT NULL,
                amount_actual varchar(10) NOT NULL,
                tax varchar(8),
                sum varchar(10),		
                PRIMARY KEY (id)
            ) $charset_collate;";
                
            
            include_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }

        

        /** 
         * Function _invoiceCreateDbSettings
         *
         * Creates Invoice database tables
         *
         * @since 1.0.0
         * 
         * @return void
         */
        private function _invoiceCreateDbSettings()
        {  
            global $wpdb;
            
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->_table_settings;
            
            $sql_delete = "DROP TABLE IF EXISTS $table_name";
            
            $wpdb->query($sql_delete);

            // dbDelta($sql);
            //
            $sql = "CREATE TABLE $table_name (
                settingsProfile varchar (64),
                company varchar(64),
                additional varchar(64), 
                firstName varchar(64), 
                lastName varchar(64), 
                street varchar(64), 
                ZIP int(6),
                city varchar(64),
                logoFileUrl varchar(256),
                logoFileFile varchar(64),
                prefix varchar(64),
                noStart int(5),
                invoiceCurrency varchar(64),
                taxTypes varchar(64),
                tax1 int(5),
                tax2 int(5),
                invoiceUnit varchar(64),
                mail varchar(64),
                phone varchar(64),
                website varchar(64),
                facebook varchar(64),
                instagram varchar(64),
                reminder int(5),
                dunning1 int(5),
                dunning2 int(5),
                IBAN1 varchar(64),
                BIC1 varchar(64),
                bankName1 varchar(64),
                BankSpacer1 varchar(8),
                IBAN2 varchar(64),
                BIC2 varchar(64),
                bankName2 varchar(64),
                BankSpacer2 varchar(8),
                PayPal varchar(64),
                email varchar(64),
                server varchar(64),
                port int(5),
                password varchar(64),
                invoiceTextIntro varchar(256),
                invoiceTextOutro varchar(256),
                invoiceTextPaymentDeadline varchar(256), 
                customFooter varchar(256) 
                
            ) $charset_collate;";
            //PRIMARY KEY (settingsProfile)
            //include_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $wpdb->query($sql);

            dbDelta($sql);
            
            $GLOBALS['wpdb']->insert( 
                $GLOBALS['wpdb']->$table_name,
                array(
                    
                    'settingsProfile' => 'settings'
                         
                )
            );
            
        }

    
        
        /** 
         * Function _mailingCreateDbSettings
         *
         * Creates Invoice database tables
         *
         * @since 1.0.0
         * 
         * @return void
         */ 
        private function _mailingCreateTemplateContent()
        {
            /**
             * Daniel 26.06.2021: muss nachträglich noch eingestellt werden
             * Creates Mailing Template Content for first row on Database
             */

            /* 
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->table_q_template;
            
            $sqldata_create = array();
                
            $sqldata_create = "1";
            $sqldata_create['subject'] = 
                "Template No 1";

            $sqldata_create['subject'] = 
                "Welcome to Q Mailing by www.qanuk.io - delete me";
            
            $sqldata_create['header'] = 
                "Please insert a picture in the expected with or leave it blank";
            
            $sqldata_create['content'] = 
                "Insert Content here as you want - feel free".
                " to create tables etc with html code";
            
            $sqldata_create['footer'] = 
                "Please insert your footer in here";
            
            $sqldata_create['width'] = "0";
            
            $rows_affected = $wpdb->insert( $table_name, $sqldata_create);
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $rows_affected );
            */

        }
        
    
    } // end class
} // endif class exists