<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   CategoryName
 * @package    QInvoice
 * @author     qanuk.io <support@qanuk.io>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */

/**
 * Fired during plugin deactivation
 *
 * @category   Category
 * @link       https://www.qanuk.io/
 * @since      1.0.0
 * @package    QInvoice
 * @subpackage Mailing/includes 
 * @author     qanuk.io <support@qanuk.io>
 */

if (!class_exists('QI_Invoice_Deactivator')) {
    /**
     * Fired during plugin deactivation
     *
     * @category   Category
     * @package    QInvoice
     * @subpackage Mailing/Includes
     * @author     qanuk.io <support@qanuk.io>
     * @license    License 
     * @link       https://www.qanuk.io/
     * @since      1.0.0
     */
    class QI_Invoice_Deactivator
    {

        /**
         * Short Description. (use period)
         *
         * Long Description.
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public static function deactivate()
        {
            self::qiSaveSettings();
        }

        /**
         * Short Description. (use period)
         *
         * Long Description.
         * 
         * @return void
         *
         * @since 1.0.0
         */
        public static function qiSaveSettings()
        {   
            $options = get_option("qi_settings");
            $currentSettings = array(
                'company' => $options['company'],
                'additional' => $options['additional'], 
                'firstName' => $options['firstName'], 
                'lastName' => $options['lastName'], 
                'street' => $options['street'], 
                'ZIP' => $options['ZIP'],
                'city' => $options['city'],
                //'logoFileUrl' => $options['logoFileUrl'],
                //'logoFileFile' => $options['logoFileFile'],
                'prefix' => $options['prefix'],
                'noStart' => $options['noStart'],
                'invoiceCurrency' => $options['invoiceCurrency'],
                'taxTypes' => $options['taxTypes'],
                'tax1' => $options['tax1'],
                'tax2' => $options['tax2'],
                'invoiceUnit' => $options['invoiceUnit'],
                'mail' => $options['mail'],
                'phone' => $options['phone'],
                'website' => $options['website'],
                'facebook' => $options['facebook'],
                'instagram' => $options['instagram'],
                'reminder' => $options['reminder'],
                'dunning1' => $options['dunning1'],
                'dunning2' => $options['dunning2'],
                'IBAN1' => $options['IBAN1'],
                'BIC1' => $options['BIC1'],
                'bankName1' => $options['BankName1'],
                'BankSpacer1' => $options['BankSpacer1'],
                'IBAN2' => $options['IBAN2'],
                'BIC2' => $options['BIC2'],
                'bankName2' => $options['BankName2'],
                'BankSpacer2' => $options['BankSpacer2'],
                'PayPal' => $options['PayPal'],
                'email' => $options['email'],
                'server' => $options['server'],
                'port' => $options['port'],
                'password' => $options['password'],
                'invoiceTextIntro' => $options['invoiceTextIntro'],
                'invoiceTextOutro' => $options['invoiceTextOutro'],
                'invoiceTextPaymentDeadline' => $options['invoiceTextPaymentDeadline'], 
                'customFooter' => $options['customFooter'] 
                  
                   
            );
            
            
            do_action('qm/debug', 'Deactivate happened!');
            $GLOBALS['wpdb']->show_errors();
            

            $GLOBALS['wpdb']->insert( 
                $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_SETTINGS,
                $options
            );
           
            
          
        
            
            
            
        }
    }

}
