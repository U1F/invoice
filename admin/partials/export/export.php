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
 * Function exportInovice($invoiceID)
 * 
 * @param int    $invoiceID   1
 * @param string $invoiceType 1
 * 
 * @return void
 */
function exportInovice($invoiceID, $invoiceType)
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
    
    

    $separator =",";
    
    include_once "invoice-template.php";
    ?>

    <?php
}

        