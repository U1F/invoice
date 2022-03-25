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
 
    /**
     * PDF Settings and Data
     */
    //TODO - change data input for credit and offer
    if($invoiceType == "offer"){
        $invoiceData = Interface_Invoices::getInvoiceData($invoiceID);
    } else if ($invoiceType == "credit"){
        $invoiceData = Interface_Invoices::getInvoiceData($invoiceID);
    } else {
        $invoiceData = Interface_Invoices::getInvoiceData($invoiceID);
    }

    //prepare currency sign; default €
    $currencySign = "€";
    if (get_option('qi_settings')['invoiceCurrency'] == "Euro") {
        $currencySign = "€";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Dollar") {
        $currencySign = "$";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Other") {
        $currencySign = get_option('qi_settings')['currencySign'];
    }

    //prepare dot type; default: 1.000,00
    $decimalDot = ',';
    $thousandsDot = '.';
    $dotType = get_option('qi_settings')['invoiceDotType'];
    if($dotType == '1,000.00'){
        $decimalDot = '.';
        $thousandsDot = ',';
    } else if($dotType = '1.000,00'){
        $decimalDot = ',';
        $thousandsDot = '.';
    }

    //prepare tax types
    $taxSums=array();
    $numberOfTaxTypes = intval(get_option('qi_settings')['taxTypes']);
    for ($i=0;$i<$numberOfTaxTypes;$i++) {

        $taxSums[get_option('qi_settings')['tax'.strval($i+1)]] = 0;
        

    }
    $taxSums['none'] = 0;

    //prepare invoice details row size by checking if a delivery date has been set
    //$deliveryDateIsSet has to be an int, cause its not only used as a flag
    $deliveryDate = date_parse_from_format(
        "Y-m-d", 
        $invoiceData[0][0]->delivery_date
    );

    $deliveryDateIsSet = 0;
    $tableWidthOfInvoiceHead = 180;
    
    if (checkdate(
        $deliveryDate['month'],
        $deliveryDate['day'], 
        $deliveryDate['year']
    )) {
        $deliveryDateIsSet = 1;
        $tableWidthOfInvoiceHead = 127;
    }

    //prepare logo image from settings or leave it empty on default
    if (get_option('qi_settings')['logoFileUrl']) {

        $logoImageURL = get_option('qi_settings')['logoFileUrl'];
        $logoImageFile = get_option('qi_settings')['logoFileFile'];
        $mimetype =  wp_get_image_mime( $logoImageFile );
        $imagedata = file_get_contents($logoImageFile);
        $base64 = base64_encode($imagedata);
        $logoImageSource = 'data:'. $mimetype .';base64,'.$base64;
        
        $finalImageData = "<img 
                                src='" . $logoImageSource . ".'
                                width='200'
                                style='border:0px; margin-bottom: 10px; margin-top: 10px;'
                            >";

    } else {

        $finalImageData = "<div style='height:100px'></div>";

    }

    //prepare header over receiver
    if (get_option('qi_settings')['company']) {
        $headerOverReceiver = get_option('qi_settings')['company'] . " | " ;
    } else {
        $headerOverReceiver = get_option('qi_settings')['firstName']. " " . get_option('qi_settings')['lastName']. " | ";
    }
    $headerOverReceiver =   $headerOverReceiver . 
                            get_option('qi_settings')['street']. " | ".  
                            get_option('qi_settings')['ZIP'] . " ". 
                            get_option('qi_settings')['city'];
        
    //prepare invoice positions
    $sumOfInvoiceDiscounts = 0;
    $InvoiceHasAtLeastOneDiscount = 0;
    $invoiceDetailDescriptionWidthHeader = "371";  
    $InvoiceDetailDescriptionWidth = "369";
    
    foreach ($invoiceData[1] as $invoiceDetail) {
        $sumOfInvoiceDiscounts += intval($invoiceDetail->discount);
    }
    
    if ($sumOfInvoiceDiscounts > 0) {
        $InvoiceHasAtLeastOneDiscount = 1;
        $invoiceDetailDescriptionWidthHeader = "295";  
        $InvoiceDetailDescriptionWidth = "261";  
        
    }

    //prepare invoice unit
    $invoiceUnit = __("Quantity", "ev");
    if (get_option('qi_settings')['invoiceUnit'] == "Amount") {
        $invoiceUnit = __("Quantity", "ev");
    }
    if (get_option('qi_settings')['invoiceUnit'] == "Hours") {
        $invoiceUnit = __("Hours", "ev");
    }
    if (get_option('qi_settings')['invoiceUnit'] == "Liter") {
        $invoiceUnit = __("Litre", "ev");
    }

    //prepare footer data
    $bankIndex = $invoiceData[0][0]->bank;
    $ibanFromDatabase = get_option('qi_settings')["IBAN{$bankIndex}"];
    // Get rid of spaces if exist
    $ibanArray = explode(' ', $ibanFromDatabase);
    $iban = implode('' , $ibanArray);
    // Seperate blz and kto
    $blz = substr($iban, 4, 8);
    $kto = strVal(intVal(substr($iban, 12, 10))); 
    //Fill with spaces if not exist for IBAN in second line
    $ibanArray = str_split($iban);
    $iban = '';
    $counter = 0;
    for($i = 0; $i < sizeof($ibanArray); $i++){
        $iban = $iban.$ibanArray[$i];
        if($counter == 3){
            $iban = $iban.' ';
            $counter = 0;
        } else{
            $counter++;
        }
    }


    /**
     * PDF TEXTS
     */

    //default Texts
    $heading = __("Invoice", "ev");
    $explainingHeading = "";
    $invoiceTextIntro = __("We are invoicing for the following services:", "ev");
    $invoiceTextOutro =  __("Thank you for the excellent co-operation.", "ev");
    $invoicePaymentDeadline = "";
    $invoiceCustomFooter = "";

    //invoice
    if ($invoiceType == "invoice") {

        //header
        $heading = __("Invoice", "ev");

        //text intro (settings)
        if (get_option('qi_settings')['TextInvoiceIntro']) {
            $invoiceTextIntro = get_option('qi_settings')['TextInvoiceIntro'];   
        } else {
            $invoiceTextIntro = __("We are invoicing for the following services:", "ev");
        }

        //text outro (settings)
        if (get_option('qi_settings')['TextInvoiceOutro']) {
            $invoiceTextOutro = get_option('qi_settings')['TextInvoiceOutro'];   
        } else {
            $invoiceTextOutro = __("Thank you for the excellent co-operation.", "ev");
        }

        //text payment deadlines (settings)
        if (get_option('qi_settings')['TextInvoicePaymentDeadline']) {
            $invoicePaymentDeadline = get_option('qi_settings')['TextInvoicePaymentDeadline'];   
        } else {
            $invoicePaymentDeadline = "";
        }

        //text custom footer (settings)
        if (get_option('qi_settings')['TextInvoiceCustomFooter']) {
            $invoiceCustomFooter = get_option('qi_settings')['TextInvoiceCustomFooter'];   
        } else {
            $invoiceCustomFooter = "";
        }

    } else if ($invoiceType =="reminder") {

        //defaults
        $explainingHeading = __("PAYMENT REMINDER", "ev");
        $invoiceTextIntro = __("Wahrscheinlich ist unsere Rechnung untergegangen - daher möchten wir noch einmal um <br> eine erneute Prüfung bitten.", "ev");
        $invoiceTextOutro = __("Sollten Sie den offenen Betrag bereits beglichen haben, 
        betrachten Sie dieses Schreiben als gegenstandslos.", "ev");
        $invoicePaymentDeadline = "";
        $invoiceCustomFooter = "";

        //text intro (settings)
        if (get_option('qi_settings')['TextReminderIntro']) {
            $invoiceTextIntro = get_option('qi_settings')['TextReminderIntro'];   
        }

        //text outro (settings)
        if (get_option('qi_settings')['TextReminderOutro']) {
            $invoiceTextOutro = get_option('qi_settings')['TextReminderOutro'];   
        }

        //text payment deadlines (settings)
        if (get_option('qi_settings')['TextReminderPaymentDeadline']) {
            $invoicePaymentDeadline = get_option('qi_settings')['TextReminderPaymentDeadline'];   
        }

        //text custom footer (settings)
        if (get_option('qi_settings')['TextReminderCustomFooter']) {
            $invoiceCustomFooter = get_option('qi_settings')['TextReminderCustomFooter'];   
        }

    } else if ($invoiceType =="dunning1" || $invoiceType="dunning2") {

        //defaults
        $explainingHeading = __("DUNNING", "ev");
        $invoiceTextIntro ="Wir bitten folgende Leistung ".
        "unverzüglich zu begleichen.".
        "<br>(auch im Zusammenhang mit dem nächsten gemeinsamen Event)";
        $invoiceTextOutro = __("Sollten Sie den offenen Betrag bereits beglichen haben, 
        betrachten Sie dieses Schreiben als gegenstandslos.", "ev");
        $invoicePaymentDeadline = "";
        $invoiceCustomFooter = "";

        //text intro (settings)
        if (get_option('qi_settings')['TextDunningIntro']) {
            $invoiceTextIntro = get_option('qi_settings')['TextDunningIntro'];   
        }

        //text outro (settings)
        if (get_option('qi_settings')['TextDunningOutro']) {
            $invoiceTextOutro = get_option('qi_settings')['TextDunningOutro'];   
        }

        //text payment deadlines (settings)
        if (get_option('qi_settings')['TextDunningPaymentDeadline']) {
            $invoicePaymentDeadline = get_option('qi_settings')['TextDunningPaymentDeadline'];   
        }

        //text custom footer (settings)
        if (get_option('qi_settings')['TextDunningCustomFooter']) {
            $invoiceCustomFooter = get_option('qi_settings')['TextDunningCustomFooter'];   
        }

    } else if ($invoiceType =="offer") {

        $heading=__("Offer", "ev");
        $invoiceTextIntro ="Folgende Leistungen möchten wir Ihnen anbieten.";
        $invoiceTextOutro = __("Thank you for the excellent co-operation.", "ev");
        $invoicePaymentDeadline = "";
        $invoiceCustomFooter = "";

        //text intro (settings)
        if (get_option('qi_settings')['dunningTextIntro']) {
            $invoiceTextIntro = get_option('qi_settings')['TextOfferIntro'];   
        }

        //text outro (settings)
        if (get_option('qi_settings')['dunningTextOutro']) {
            $invoiceTextOutro = get_option('qi_settings')['TextOfferOutro'];   
        }

        //text payment deadlines (settings)
        if (get_option('qi_settings')['dunningTextPaymentDeadline']) {
            $invoicePaymentDeadline = get_option('qi_settings')['TextOfferPaymentDeadline'];   
        }

        //text custom footer (settings)
        if (get_option('qi_settings')['dunningTextCustomFooter']) {
            $invoiceCustomFooter = get_option('qi_settings')['TextOfferCustomFooter'];   
        }

    } else if ($invoiceType =="credit") {

        $heading = __("Credit", "ev");
        $invoiceTextIntro ="Folgende Leistung schreiben wir Ihnen gut.";
        $invoiceTextOutro = __("Thank you for the excellent co-operation.", "ev");
        $invoicePaymentDeadline = "";
        $invoiceCustomFooter = "";

        //text intro (settings)
        if (get_option('qi_settings')['dunningTextIntro']) {
            $invoiceTextIntro = get_option('qi_settings')['TextCreditIntro'];   
        }

        //text outro (settings)
        if (get_option('qi_settings')['dunningTextOutro']) {
            $invoiceTextOutro = get_option('qi_settings')['TextCreditOutro'];   
        }

        //text payment deadlines (settings)
        if (get_option('qi_settings')['dunningTextPaymentDeadline']) {
            $invoicePaymentDeadline = get_option('qi_settings')['TextCreditPaymentDeadline'];   
        }

        //text custom footer (settings)
        if (get_option('qi_settings')['dunningTextCustomFooter']) {
            $invoiceCustomFooter = get_option('qi_settings')['TextCreditCustomFooter'];   
        }

    }
    
    include_once "invoice-template.php";
    ?>

    <?php
}

        