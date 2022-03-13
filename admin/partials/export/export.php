<?php
/**
 * Short
 *
 * Description
 * 
 * PHP version 5
 * 
 * @category   Export
 * @package    QInvoice
 * @subpackage Q_Invoice/admin/partials
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       https://www.qanuk.io/ 
 * @since      1.0.0
 */

/**
 * Function exportInvoice($invoiceID)
 * 
 * @param int    $invoiceID   1
 * @param string $invoiceType -->   currently possible options:
 *                                  - invoice   -> New Invoice for services
 *                                  - credit    -> Credit for already paid items
 *                                  - offer     -> Like an Invoice but not bought yet
 *                                  - reminder  -> Reminder for an pending invoice
 *                                  - dunning1  -> First reminder for an pending invoice with costs
 *                                  - dunning2  -> Second reminder for an pending invoice with further costs
 * 
 * @return void
 */
function exportInvoice($invoiceID, $invoiceType)
{
    $invoiceData = Interface_Invoices::getInvoiceData($invoiceID);

    $currencySign = "€";
    if (get_option('qi_settings')['invoiceCurrency'] == "Euro") {
        $currencySign = "€";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Dollar") {
        $currencySign = "$";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Other") {
        $currencySign = get_option('qi_settings')['currencySign'];
    }

    $taxSums=array();
    $numberOfTaxTypes = intval(get_option('qi_settings')['taxTypes']);
    for ($i=0;$i<$numberOfTaxTypes;$i++) {

        $taxSums[get_option('qi_settings')['tax'.strval($i+1)]] = 0;
        

    }
    $taxSums['none'] = 0;

    $dotType = get_option('qi_settings')['invoiceDotType'];
    if($dotType == '1,000.00'){
        $decimalDot = '.';
        $thousandsDot = ',';
    } else if($dotType = '1.000,00'){
        $decimalDot = ',';
        $thousandsDot = '.';
    }

    
    include_once "invoice-template.php";


    

    ?>

    <?php
}

        