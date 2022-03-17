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

    /**
     * PDF TEXTS
     */

    //default
    $heading=__("Invoice", "ev");
    $invoiceTextIntro = __("We are invoicing for the following services:", "ev");
    $invoiceTextOutro =  __("Ein Outro:", "ev");

    /*Folgender Code dient der Bildung von invoiceTextOutro
    if ($invoiceType=="invoice") {
        if (get_option('qi_settings')['invoiceTextOutro']) {
            echo '<div style="font-size:14px;  width: 640px;">';
            echo get_option('qi_settings')['invoiceTextOutro'];
            echo '<br>'. get_option('qi_settings')['invoiceTextPaymentDeadline'];
            echo '</div>';
        } else {
            ?>
            <div style="font-size:14px;  width: 640px;">
            Thank you for the excellent co-operation.<br>
            <br>
            <?php echo get_option('qi_settings')['invoiceTextPaymentDeadline']; ?> 
            </div>
            <?php
        }
    }
    
    if ($invoiceType=="dunning") {
        ?>

    <div style="font-size:12px; display:none; width: 640px;">
        Sollten Sie den offenen Betrag bereits beglichen haben, 
        betrachten Sie dieses Schreiben als gegenstandslos.
    </div>
        <?php
    }*/

    //invoice
    if ($invoiceType =="invoice") {
        //header
        $heading=__("Invoice", "ev");

        //text intro (settings)
        if (get_option('qi_settings')['invoiceTextIntro']) {
            $invoiceTextIntro = get_option('qi_settings')['invoiceTextIntro'];   
        } else {
            $invoiceTextIntro = __("We are invoicing for the following services:", "ev");
        }

        //text outro (settings)
        if (get_option('qi_settings')['invoiceTextIntro']) {
            $invoiceTextIntro = get_option('qi_settings')['invoiceTextIntro'];   
        } else {
            $invoiceTextIntro = __("We are invoicing for the following services:", "ev");
        }


    } else if ($invoiceType =="credit") {
        $heading=__("Credit Note", "ev");
        $invoiceTextIntro ="Folgende Leistung schreiben wir Ihnen gut.";
    } else if ($invoiceType =="dunning1") {
        $heading=__("Payment Reminder", "ev");
        $invoiceTextIntro ="Wahrscheinlich ist unsere Rechnung untergegangen".
        " - daher möchten wir noch einmal um <br>".
        "eine erneute Prüfung bitten.";
    } else if ($invoiceType =="dunning2") {
        $heading=__("Dunning", "ev");
        $invoiceTextIntro ="Wir bitten folgende Leistung ".
        "unverzüglich zu begleichen.".
        "<br>(auch im Zusammenhang mit dem nächsten gemeinsamen Event)";
    }
    echo '<p style="font-size: 24px;"><b>'.$heading.'</b> </p>';

    
    include_once "invoice-template.php";


    

    ?>

    <?php
}

        