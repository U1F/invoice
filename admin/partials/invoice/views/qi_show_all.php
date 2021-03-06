<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, _version, and hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * PHP version 5
 * 
 * @category   Class
 * @package    QInvoice
 * @subpackage QInvoice/admin
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       https://www.qanuk.io/
 * @since      1.0.0
 */

?>

 <div class="page_content">
     
     <div class="filterButtons qInvMainSearchable">
        <div class="filterButton active" id="showAllInvoices">
            <button class="invoiceButton">
                All
            </button>
        </div>

        <div class="filterButton inactive" id="showOpenInvoices" style="border-left: none">
            <button class="invoiceButton">
                Open
            </button>
        </div>

        <div class="filterButton inactive" id="showInvoicesWithDunning">
            <button class="invoiceButton">
                Dunning
            </button>
        </div>

        <div class="filterButton inactive" id="showCancelledInvoices">
            <button class="invoiceButton">
                Cancelled
            </button>
        </div>
        
    
        <div class="filterButton inactive" id="showInvoicesPaid">
            <button class="invoiceButton">
                Paid
            </button>
        </div>

        <div class="filterButton q_invoice_headerBorderMod" id="searchInvoices">
            <input type=text>
            </input>
            <span class="dashicons dashicons-search"></span>
        </div>
     </div>
     
    <div class="mobileFilterButtons qInvMainSearchable">
        <select name="mobileFilterButtonsDropdown" id="mobileFilterButtonsDropdown">
            <option class="mobileFilterButtonsOption" value="all" selected>All</option>
            <option class="mobileFilterButtonsOption" value="open">Open</option>
            <option class="mobileFilterButtonsOption" value="dunning">Dunning</option>
            <option class="mobileFilterButtonsOption" value="cancelled">Cancelled</option>
            <option class="mobileFilterButtonsOption" value="paid">Paid</option>
        </select>
        <div id="searchInvoicesMobile">
            <input id="filterButtonMobileSearchInput" type=text>
            </input>
            <span id="qInvMobileSearchIcon" class="dashicons dashicons-search"></span>
        </div>
    </div>
    <div id="q-invoice-TableContentWrapper" class="tab_content_wrapper" style="border-top:none">
<?php 

//Mail Infos for faster on load
$personalName = get_option('qi_settings')['firstName'] . ' ' . get_option('qi_settings')['lastName'];
$companyName = get_option('qi_settings')['company'];
if($companyName){
    $nameNCompany =  $companyName;
} else if ($personalName != ' '){
    $nameNCompany = $personalName;
}
echo '<div id="qinv_mail-sender-onpage" style="display:none;">'.get_option('qi_settings')['email'].'</div>';
echo '<div id="qinv_mail-name-company" style="display:none;">'.$nameNCompany.'</div>';
echo '<div id="qinv_mail-invoice-template" style="display:none;">'.get_option('qi_settings')['TextInvoiceMail'].'</div>';
echo '<div id="qinv_mail-dunning-template" style="display:none;">'.get_option('qi_settings')['TextDunningMail'].'</div>';


/**
 * Function showOpenInvoice 
 * 
 * @return void
 */
function showHeader()
{
    
    ?>
   
    
    <table id="tableInvoices" class="wp-list-table fixed widefat">

        <thead id="tableInvoicesHeader">
            <tr>

                <th scope="col" id="invoiceID" 
                    class="manage-column  columnInvoiceID sortable asc">
                    <?php _e('ID', 'Ev'); ?>
                </th>

                <th scope="col" id="invoiceStatus" 
                    class="manage-column  columnStatus">
                    <?php _e('', 'Ev'); ?>
                </th>
                
                <th scope="col" id="name" 
                    class="manage-column  columnName">
                    <?php _e('Name', 'Ev'); ?>
                </th>

                <th scope="col" id="invoiceDescription" 
                    class="manage-column  columnDescription">
                    <?php _e('Description', 'Ev'); ?>
                </th>

                <th scope="col" id="invoiceDate" 
                    class="manage-column  columnDate">
                    <?php _e('Invoice Date', 'Ev'); ?>
                </th>
                
                <th scope="col" id="sumNet" 
                    class="manage-column  columnNet">
                    <?php _e('Net', 'Ev'); ?>
                </th>
                
                <th scope="col" id="sumTotal" 
                    class="manage-column  columnTotal ">
                    <?php _e('Total', 'Ev'); ?>
                </th>

                <th scope="col" id="invoiceDunning" 
                    class="manage-column  columnDunning">
                    <?php _e('Dunning', 'Ev'); ?>
                </th>

                <th scope="col" id="invoiceStatusPaid" 
                    class="manage-column  columnStatusPaid">
                    <?php _e('Paid', 'Ev'); ?>
                </th>

                <th scope="col" id="invoiceEdit" 
                    class="manage-column fiftyCol columnEdit ">
                    <?php _e('Edit', 'Ev'); ?>
                </th>

                
                
            </tr>
        </thead>
    <?php
    
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
function addWorkingDays($timestamp, $days, $skipdays = array("Saturday", "Sunday"), $skipdates = []) {
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
 * Function showOpenInvoices
 * 
 * @return void
 */
function showOpenInvoices()
{
    /*
    Prepare Variables
    */
    $table_name = $GLOBALS['wpdb']->prefix . QI_Invoice_Constants::TABLE_QI_HEADER;
    $query = "SELECT * FROM $table_name ORDER BY id DESC";
    $invoice_headers = $GLOBALS['wpdb']->get_results($query);

    $openNetto = 0.00;
    $cancelledNetto = 0.00;
    $dunningNetto = 0.00;
    $paidNetto = 0.00;

    $openTotal = 0.00;
    $cancelledTotal = 0.00;
    $dunningTotal = 0.00;
    $paidTotal = 0.00;

    $totalTotalSum = 0.00;
    $nettoTotalSum = 0.00;
    $dunningTotalSum = 0.00;

    /*
    Check Selected Dot Type
    */
    $dotType = get_option('qi_settings')['invoiceDotType'];
    if($dotType == '1,000.00'){
        $decimalDot = '.';
        $thousandsDot = ',';
    } else if($dotType = '1.000,00'){
        $decimalDot = ',';
        $thousandsDot = '.';
    }

    /*
    Check Selected Currency Symbol
    */
    $currencySymbol = "???";
    if (get_option('qi_settings')['invoiceCurrency'] == "Euro") {
        $currencySymbol = "???";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Dollar") {
        $currencySymbol = "$";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Other") {
        $currencySymbol = get_option('qi_settings')['currencySign'];
    }

    /*
    Prepare for each Invoice
    */
    foreach ($invoice_headers as $invoice_header) {
        $paid = 0;
        $dunning = false;
        $cancelled = false;

        /*
        Get Invoice Details
        */
        $invoice_details = $GLOBALS['wpdb']->get_results(
            "SELECT * FROM ".
            $GLOBALS['wpdb']->prefix . 
            QI_Invoice_Constants::TABLE_QI_DETAILS . ' '.
            "WHERE invoice_id = " .
            $invoice_header->id . " " .  
            "ORDER BY position ASC"
        );
        
        /*
        Check if Invoice is already payed
        */
        $paymentDate = date_parse_from_format("Y-m-d", $invoice_header->paydate);
        if (checkdate($paymentDate['month'],$paymentDate['day'], $paymentDate['year'])) {
            $paid=1;
        }
        
        /*
        Check if Invoice has been cancelled
        */
        if ($invoice_header->cancellation) {
            $cancelled=true;
        }

        /*
        Check if Reminder is activated
        */
        $reminderActive = 0;
        $reminderDate = date_parse_from_format("Y-m-d", $invoice_header->date_reminder);
        if (checkdate($reminderDate['month'],$reminderDate['day'], $reminderDate['year'])) {
            $reminderActive=1;
        }
        /*
        Check if dunning 1 is activated
        */
        $dunningIDate = date_parse_from_format("Y-m-d", $invoice_header->date_dunning1);
        $dunningIActive = 0;
        if (checkdate($dunningIDate['month'],$dunningIDate['day'], $dunningIDate['year'])) {
            $dunningIActive=1;
        }
        /*
        Check if dunning 2 is activated
        */
        $dunningIIActive = 0;
        $dunningIIDate = date_parse_from_format("Y-m-d", $invoice_header->date_dunning2);
        if (checkdate($dunningIIDate['month'],$dunningIIDate['day'], $dunningIIDate['year'])) {
            $dunningIIActive=1;
        }

        /*
        Check if Invoice has a Dunning --> Status button will be set into dunning state
        */
        if ($dunningIIActive || $dunningIActive || $reminderActive){//$invoice_header->dunning1) {
            $dunning=true;
        }

        /*
        Reset Line Sums for this Invoice
        */
        $netSum = 0;
        $totalSum = 0;
        $dunningSum = 0;
        /*
        Get Sums for each Position in Invoice
        */
        foreach ($invoice_details as $invoice_detail) {
        
            // Sum Netto for all Positions
            $netSum = $netSum + floatval($invoice_detail->sum);
            // Total Sum is the Netto Sum + the Taxes
            $totalSum = $totalSum + 
                (floatval($invoice_detail->sum) + 
                (floatval($invoice_detail->sum) * intval($invoice_detail->tax) / 100));
            // Dunning Sum is the sum of both Dunnings
            $dunningSum = floatval($invoice_header->dunning1) + floatval($invoice_header->dunning2);
        }
        // Add Invoice Sums to the Total Sums for printing them at the end of the Table
        $totalTotalSum = $totalTotalSum + $totalSum;
        $nettoTotalSum = $nettoTotalSum + $netSum;
        $dunningTotalSum = $dunningTotalSum + $dunningSum;

        //Dunning Calculations
        $circleClass = '';
        $numberOfDunningDays = '';
        $circleClassDummy = '';
        $numberOfDunningDaysDummy = '';
        
        $invoiceActivatedDate = strtotime($invoice_header->invoice_date);
        $currentDate = strtotime(date('Y-m-d'));

        $reminderDays = intVal(get_option('qi_settings')['reminderDayLimit']);
        $reminderDate = addWorkingDays($invoiceActivatedDate, $reminderDays);

        $dunningIDays = intVal(get_option('qi_settings')['dunning1daylimit']);
        $dunningIDate = addWorkingDays($invoiceActivatedDate, $dunningIDays);

        $dunningIIDays = intVal(get_option('qi_settings')['dunning2daylimit']);
        $dunningIIDate = addWorkingDays($invoiceActivatedDate, $dunningIIDays);
        if($dunningIIDate <= $currentDate){
            if(!$paid && !$cancelled){
                $circleClass = 'dunningII';
                $numberOfDunningDays = ceil(abs($currentDate - $dunningIIDate) / 86400) . ' days';
                $dunning = true;
            }
            $circleClassDummy = 'dunningII';
            $numberOfDunningDaysDummy =ceil(abs($currentDate - $dunningIIDate) / 86400) . ' days';;
        } else if($dunningIDate <= $currentDate){
            if(!$paid && !$cancelled){
                $circleClass = 'dunningI';
                $numberOfDunningDays = ceil(abs($currentDate - $dunningIDate) / 86400) . ' days';
                $dunning = true;
            }
            $circleClassDummy = 'dunningI';
            $numberOfDunningDaysDummy = ceil(abs($currentDate - $dunningIDate) / 86400) . ' days'; 
        } else if($reminderDate <= $currentDate){
            if(!$paid && !$cancelled){
                $circleClass = 'reminder';
                $numberOfDunningDays = ceil(abs($currentDate - $reminderDate) / 86400) . ' days';
                $dunning = true;
            }
            $circleClassDummy = 'reminder';
            $numberOfDunningDaysDummy = ceil(abs($currentDate - $reminderDate) / 86400) . ' days';
        }
     

        ?>
        <tr  
                class="<?php //edit = you can open the invoice; paid = shown on page all&paid; dunning = shown on page all&dunning; open = shown on page all&open
         
                    if ($paid) {
                        echo ' paid edit active';
                    } else if ($dunning) {
                        echo ' dunning edit';
                        //cancelled can be combined with the classes above. As long as an invoice is not cancelles it has to be active
                        if ($cancelled) {
                            echo ' cancelled ';
                        }
                        else {
                            echo ' active ';
                        } 
                    } else {
                        echo ' open edit';
                        //cancelled can be combined with the classes above. As long as an invoice is not cancelles it has to be active
                        if ($cancelled) {
                            echo ' cancelled ';
                        }
                        else {
                            echo ' active ';
                        } 
                    }
                         
                ?> q_invoice-content-row" 
                id="edit-<?php echo esc_attr($invoice_header->id);?>"
                value="<?php echo esc_html($invoice_header->id);?>"
            >    

            <td class="manage-column  columnInvoiceID sortable asc">
                <span id="qinv_mainIdSpan">
                        <?php echo esc_html($invoice_header->id); ?>
                </span>
            </td>
            
            <td class="manage-column  columnStatus">
                <div class="circle invoiceStatusIcon<?php 
                        if ($paid) {
                            echo ' paid active';
                        } else {
                            //insert dunning mode
                            if($reminderActive){
                                echo ' reminder';
                            }else if($dunningIActive){
                                echo ' dunningI';
                            }else if($dunningIIActive){
                                echo ' dunningII';
                            }  else{
                                echo ' open ';
                            }
                            
                            if ($cancelled) {
                                echo ' cancelled ';
                            }
                            else {
                                echo ' active ';
                            } 
                        }
                    ?> 
                ">
                </div>
                
            </td>
                

            

                <td
                    class="manage-column  columnName">
                        <?php 
                            if($invoice_header->company){
                                echo esc_html($invoice_header->company); 
                            } else {?>
                                <span class="firstnameSpan">
                                    <?php echo  esc_html($invoice_header->firstname); ?>
                                </span>
                    
                                <span class="lastnameSpan">
                                    <?php echo  esc_html($invoice_header->lastname); ?>
                                </span> <?php
                            }?>
                </td>

                <td class="manage-column  columnDescription">
                    <span>
                        <?php echo  esc_html($invoice_details[0]->description);?>
                    </span>
                    
                </td>

                <td class="manage-column  columnDate"
                    
                >
                <?php 
                    echo date("d.m.Y", strtotime($invoice_header->invoice_date)) 
                ?>
                </td>

                <td class="manage-column  columnNet" >
                    <span class="monetaryAmount">
                        <?php
                        if ($paid) {
                            $paidNetto = $paidNetto + $netSum;
                        } else if ($dunning) {
                            $dunningNetto = $dunningNetto + $netSum;
                        } else if(!$cancelled){
                            $openNetto = $openNetto + $netSum;
                        } 
    
                        if ($cancelled) {
                            $cancelledNetto = $cancelledNetto + $netSum;
                        }
                        echo number_format($netSum, 2, $decimalDot, $thousandsDot) . " " . $currencySymbol;?>
                    </span>
                    
                </td>

                <td class="manage-column  columnTotal">
                <span class="monetaryAmount">
                        <?php
                        if ($paid) {
                            $paidTotal = $paidTotal + $totalSum;
                        } else if ($dunning) {
                            $dunningTotal = $dunningTotal + $totalSum;
                        } else if(!$cancelled){
                            $openTotal = $openTotal + $totalSum;
                        } 
    
                        if ($cancelled) {
                            $cancelledTotal = $cancelledTotal + $totalSum;
                        }
                        echo number_format($totalSum, 2, $decimalDot, $thousandsDot). " " . $currencySymbol; ?>
                    </span>
                </td>

                <td class="manage-column columnDunning" value="<?php echo $circleClassDummy; ?>">                
                    <span value="<?php echo $numberOfDunningDaysDummy; ?>" class="longCircle <?php echo $circleClass; ?>">
                        <?php echo $numberOfDunningDays; ?>
                    </span>
                </td>

                <td class="manage-column  columnStatusPaid">

                    <label class="switch switchForPaidStatus large" style="<?php if($cancelled){echo 'opacity:0;';}?>">
                    <input type="checkbox" class="checkboxForPayment" style="<?php if($cancelled){echo 'opacity:0;';}?>"
                        <?php if ($paid){ echo "checked";} else?>
                    >
                    <span class="sliderForPayment invoiceSlider round large" style="<?php if($cancelled){echo 'opacity:0;';}?>"></span>
                    </label>
                
                </td>


                <td class="manage-column  columnEdit">

                    <div style="width:100%">
                        <a 
                            style="font-size:20px; display:inline" 
                            target="_top"
                            href=<?php 
                            echo "'". plugins_url() .
                                '/q_invoice/pdf/'. 
                                Interface_Export::makeFilename($invoice_header->id, 'invoice').
                                '.pdf'."' "
                            ?>
                            id="<?php 
                                echo 
                                    "download-".
                                    get_option('qi_settings')['prefix'].'-'.
                                    $invoice_header->id;
                                ?>"
                            title="Download Invoice"
                            class="downloadInvoice download dashicons dashicons-download"
                            value="<?php echo esc_html($invoice_header->id);?>"
                            download
                        >
                        </a>
                            
                        <span style="font-size: 20px; <?php
                            if ($paid){echo 'color: #dadce1;';} else{echo 'color: #343a40;';}
                            if ($cancelled){echo ' display: none;';}
                            ?>"
                            id="<?php echo esc_attr($invoice_header->id);?>" 
                            title="Cancel Invoice"
                            class="delete <?php
                            if (!$paid){echo 'deleteRow';}
                            ?> dashicons dashicons-no"
                            value="<?php echo esc_html($invoice_header->id);?>"
                        >
                        </span>

                        <span style="font-size: 20px;<?php 
                            if ($cancelled){
                                echo 'display: inline-block;';
                            } else {
                                echo 'display: none;';
                            }?>"
                            id="<?php echo esc_attr($invoice_header->id);?>" 
                            title="Reactivate Invoice"
                            class="reactivateInvoice reactivate dashicons dashicons-undo"
                            value="<?php echo esc_html($invoice_header->id);?>"
                        >
                        </span>

                        <span style="font-size: 20px;"
                            id="<?php echo esc_attr($invoice_header->id);?>" 
                            title="Send Invoice as Mail"
                            class="mail mailInvoice dashicons dashicons-email-alt"
                            value="<?php echo esc_html($invoice_header->id);?>"
                        >
                        </span>

                        <span style="font-size: 20px;"
                            id="<?php echo esc_attr($invoice_header->id);?>" 
                            title="More Invoice Options"
                            class="moreInvoiceOptions dashicons dashicons-ellipsis"
                            value="<?php echo esc_html($invoice_header->id);?>"
                        >
                        </span>
                    </div>

                    <div class="qinv_moreOptionsDropdownBox">
                        <ul style="margin: 0;">
                            <li class="qinv_mainDropdownElement duplicateInvoice">Duplicate</li>
                            <li id="q_invc_reminderValue" value="<?php echo $invoice_header->reminder; ?>" class="qinv_mainDropdownElement reminderRow<?php if(!($circleClass == 'reminder' || $circleClass == 'dunningI' || $circleClass == 'dunningII') || $paid || $cancelled){ echo ' deactivatedListElement';} ?>">
                                <div id="q_invc_reminderActiveVal" value="<?php echo $reminderActive; ?>" class="<?php if(!($circleClass == 'reminder' || $circleClass == 'dunningI' || $circleClass == 'dunningII') || $paid || $cancelled){ echo 'listPointerEventsMod';} ?>">
                                Reminder 
                                <a 
                                    style="font-size:20px; display:inline" 
                                    target="_top"
                                    href=<?php 
                                    echo "'". plugins_url() .
                                        '/q_invoice/pdf/'. 
                                        Interface_Export::makeFilename($invoice_header->id, 'reminder1').
                                        '.pdf'."' "
                                    ?>
                                    id="<?php 
                                        echo 
                                            "reminderDownload-".
                                            get_option('qi_settings')['prefix'].'-'.
                                            $invoice_header->id;
                                        ?>"
                                    title="Download Reminder"
                                    class="downloadReminder download dashicons dashicons-download <?php if(!$reminderActive){echo 'iconInactiveColor';}?>"
                                    value="<?php echo esc_html($invoice_header->id);?>"
                                    download
                                >
                                </a>

                                <span style="font-size: 20px;"
                                    id="<?php echo esc_attr($invoice_header->id);?>" 
                                    title="Send Reminder as Mail"
                                    class="mail mailReminder dashicons dashicons-email-alt <?php if(!$reminderActive){echo 'iconInactiveColor deactivatedMail';}?>"
                                    value="<?php echo esc_html($invoice_header->id);?>"
                                >
                                </span>
                            </div>
                            </li>
                            <li id="q_invc_dunningIValue" value="<?php echo $invoice_header->dunning1; ?>" class="qinv_mainDropdownElement dunningIRow<?php if(!($circleClass == 'dunningI' || $circleClass == 'dunningII') || $paid || $cancelled){ echo ' deactivatedListElement';} ?>">
                                <div id="q_invc_dunningIActiveVal" value="<?php echo $dunningIActive; ?>" class="<?php if(!($circleClass == 'dunningI' || $circleClass == 'dunningII') || $paid || $cancelled){ echo 'listPointerEventsMod';} ?>">
                                Dunning 1 
                                <a 
                                    style="font-size:20px; display:inline" 
                                    target="_top"
                                    href=<?php 
                                    echo "'". plugins_url() .
                                        '/q_invoice/pdf/'. 
                                        Interface_Export::makeFilename($invoice_header->id, 'reminder2').
                                        '.pdf'."' "
                                    ?>
                                    id="<?php 
                                        echo 
                                            "dunningIDownload-".
                                            get_option('qi_settings')['prefix'].'-'.
                                            $invoice_header->id;
                                        ?>"
                                    title="Download Dunning 1"
                                    class="downloadDunningI download dashicons dashicons-download <?php if(!$dunningIActive){echo 'iconInactiveColor';}?>"
                                    value="<?php echo esc_html($invoice_header->id);?>"
                                    download
                                >
                                </a>

                                <span style="font-size: 20px;"
                                    id="<?php echo esc_attr($invoice_header->id);?>" 
                                    title="Send First Dunning as Mail"
                                    class="mail mailDunningI dashicons dashicons-email-alt <?php if(!$dunningIActive){echo 'iconInactiveColor deactivatedMail';}?>"
                                    value="<?php echo esc_html($invoice_header->id);?>"
                                >
                                </span>
                            </div>
                            </li>
                            <li id="q_invc_dunningIIValue" value="<?php echo $invoice_header->dunning2; ?>" class="qinv_mainDropdownElement dunningIIRow<?php if($circleClass != 'dunningII' || $paid || $cancelled){ echo ' deactivatedListElement';} ?>">
                                <div id="q_invc_dunningIIActiveVal" value="<?php echo $dunningIIActive; ?>" class="<?php if($circleClass != 'dunningII' || $paid || $cancelled){ echo 'listPointerEventsMod';} ?>">
                                Dunning 2 
                                <a 
                                    style="font-size:20px; display:inline" 
                                    target="_top"
                                    href=<?php 
                                    echo "'". plugins_url() .
                                        '/q_invoice/pdf/'. 
                                        Interface_Export::makeFilename($invoice_header->id, 'reminder3').
                                        '.pdf'."' "
                                    ?>
                                    id="<?php 
                                        echo 
                                            "dunningIIDownload-".
                                            get_option('qi_settings')['prefix'].'-'.
                                            $invoice_header->id;
                                        ?>"
                                    title="Download Dunning 2"
                                    class="downloadDunningII download dashicons dashicons-download <?php if(!$dunningIIActive){echo 'iconInactiveColor';}?>"
                                    value="<?php echo esc_html($invoice_header->id);?>"
                                    download
                                >
                                </a>

                                <span style="font-size: 20px;"
                                    id="<?php echo esc_attr($invoice_header->id);?>" 
                                    title="Send Second Dunning as Mail"
                                    class="mail mailDunningII dashicons dashicons-email-alt <?php if(!$dunningIIActive){echo 'iconInactiveColor deactivatedMail';}?>"
                                    value="<?php echo esc_html($invoice_header->id);?>"
                                >
                                </span>
                            </div>
                            </li>
                        </ul>
                    </div>
                </td>    
                    
            </tr>            
            
            <?php 
    }
    ?>
    <tr 
        class="<?php

            echo ' all ';
                    
        ?>" 
        id="q_invoice_totalSums"
        value="<?php echo esc_html($invoice_header->id);?>"
        style="border-top: 2px dashed #dadce1"
    >    

        <td class="manage-column  columnInvoiceID sortable asc"></td>
            
        <td class="manage-column  columnStatus"></td>

        <td class="manage-column  columnName"></td>

        <td class="manage-column  columnDescription"></td>

        <td class="manage-column  columnDate"></td>

        <td class="manage-column  columnNet" >
            <span id="columnNetSum"> 
            <?php 
                echo number_format($nettoTotalSum, 2, $decimalDot, $thousandsDot) . " " . $currencySymbol;
            ?>
            </span>
        </td>

        <td class="manage-column  columnTotal">
            <span id="columnTotalSum"> 
                <?php 
                    echo number_format($totalTotalSum, 2, $decimalDot, $thousandsDot) . " " . $currencySymbol;
                 ?>
            </span>
        </td>

        <td class="manage-column  columnDunning"></td>

        <td class="manage-column  columnStatusPaid"></td>


        <td class="manage-column  columnEdit"></td>
                    
    </tr>
    
    <?php
        

}

/**
 * Function showOpenInvoices
 * 
 * @return void
 */
function closeTable()
{
    ?>
        

            </table>   
        </div>
    </div>
    <div id='paginationButtonRow'>
         <button class='paginationButton operateOnYear' id='subYearForPagination'>-</button>
         <button class='paginationButton' id='selectYearForPagination'>2022</button>
         <button class='paginationButton operateOnYear' id='addYearForPagination'>+</button>
         
     </div>
    <?php
}
