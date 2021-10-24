<?php
/**
 * Short description for file class_q_invoice_constants.php
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
if (!class_exists('QI_Invoice_Constants')) {
    /**
     * Short Description
     * 
     * Long Description
     * 
     * PHP version 5
     * 
     * @category  CategoryName
     * @package   QInvoice
     * @author    qanuk.io <support@qanuk.io>
     * @author    Another Author <another@example.com>
     * @copyright 1997-2005 The PHP Group
     * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
     * @version   Release: @package_version@
     * @link      http://pear.php.net/package/PackageName
     */ 
    class QI_Invoice_Constants
    {
        //const INVOICE_ROOT_PATH        = ABSPATH . "wp-content/plugins/qInvoice/";
        const TABLE_QI_CONTACTS   = "q_invoice_contacts";
        const TABLE_QI_DETAILS    = "q_invoice_details";
        const TABLE_QI_HEADER     = "q_invoice_header";
        const TABLE_CN_HEADER     = "q_credit_note_header";
        const TABLE_CN_DETAILS    = "q_credit_note_details";
        const TABLE_OFFER_HEADER  = "q_offer_header";
        const TABLE_OFFER_DETAILS = "q_offer_details";
        const TABLE_SETTINGS      = "q_invoice_settings";  
        const TABLE_TEMPLATE      = "q_invoice_template";  
    } // end class
} // endif class exists