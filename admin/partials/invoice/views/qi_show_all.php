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
     <div id="filterButtons">
        <div class="filterButton active" id="showAllInvoices">
            <button class="invoiceButton">
                All
            </button>
        </div>

        <div class="filterButton inactive" id="showOpenInvoices" style="margin-left: -1px;">
            <button class="invoiceButton">
                Open
            </button>
        </div>

        <div class="filterButton inactive" id="showInvoicesWithDunning" style="margin-left: -1px;">
            <button class="invoiceButton">
                Dunning
            </button>
        </div>

        <div class="filterButton inactive" id="showCancelledInvoices" style="margin-left: -1px;">
            <button class="invoiceButton">
                Cancelled
            </button>
        </div>
        
    
        <div class="filterButton inactive" id="showInvoicesPaid" style="margin-left: -1px;">
            <button class="invoiceButton">
                Paid
            </button>
        </div>

        <div class="filterButton q_invoice_headerBorderMod" id="searchInvoices" style="margin-left: -1px;">
            <input type=text>
            </input>
            <span class="dashicons dashicons-search"></span>
        </div>
        

     </div>
    <div class="tab_content_wrapper">
<?php 



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

                <th scope="col" id="invoiceDate" 
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
 * Function showOpenInvoices
 * 
 * @return void
 */
function showOpenInvoices()
{
    $table_name = $GLOBALS['wpdb']->prefix . QI_Invoice_Constants::TABLE_QI_HEADER;
    $query = "SELECT * FROM $table_name ORDER BY id DESC";
    $invoice_headers = $GLOBALS['wpdb']->get_results($query);

    $count = 0;

    $netSum = 0;

    $totalTotal = 0;
    $totalNetto = 0;
    $totalDunning = 0;

    $openNetto = 0;
    $cancelledNetto = 0;
    $dunningNetto = 0;
    $paidNetto = 0;
    $openTotal = 0;
    $cancelledTotal = 0;
    $dunningTotal = 0;
    $paidTotal = 0;
    $openDunning = 0;
    $cancelledDunning = 0;
    $dunningDunning = 0;
    $paidDunning = 0;

    
    

    foreach ($invoice_headers as $invoice_header) {
        $paid = 0;
        $dunning = false;
        $cancelled = false;

        $invoice_details = $GLOBALS['wpdb']->get_results(
            "SELECT * FROM ".
            $GLOBALS['wpdb']->prefix . 
            QI_Invoice_Constants::TABLE_QI_DETAILS . ' '.
            "WHERE invoice_id = " .
            $invoice_header->id . " " .  
            "ORDER BY position ASC"
        );
        
        $paymentDate = date_parse_from_format(
            "Y-m-d", 
            $invoice_header->paydate
        );

        if (checkdate($paymentDate['month'],$paymentDate['day'], $paymentDate['year'])) {
            $paid=1;

        }
        

        $count++;
        
        if ($invoice_header->cancellation) {
            $cancelled=true;
        }

        $netSum = 0;
        $totalSum= 0;
        foreach ($invoice_details as $invoice_detail) {
        
            $netSum = $netSum + floatval($invoice_detail->sum);
            $totalSum = $totalSum + 
                (floatval($invoice_detail->sum) + 
                (floatval($invoice_detail->sum) * intval($invoice_detail->tax) / 100));
        }
        
        ?>
            <tr 
                class="<?php
         
                    if ($paid) {
                        echo ' paid ';
                    } else if ($dunning) {
                        echo ' dunning edit';
                    } else {
                        echo ' open edit';
                    } 

                    if ($cancelled) {
                        echo ' cancelled ';
                    }
                    else {
                        echo ' active ';
                    } 
                         
                ?> q_invoice-content-row" 
                id="edit-<?php echo esc_attr($invoice_header->id);?>"
                value="<?php echo esc_html($invoice_header->id);?>"
            >    

            <td class="manage-column  columnInvoiceID sortable asc">
                <span>
                        <?php echo esc_html($invoice_header->id); ?>
                </span>
            </td>
            
            <td class="manage-column  columnStatus">
                <div class="circle invoiceStatusIcon<?php 
                        if ($paid) {
                            echo ' paid ';
                        } else if ($dunning) {
                            echo ' dunning ';
                        } else {
                            echo ' open ';
                        } 
    
                        if ($cancelled) {
                            echo ' cancelled ';
                        }
                        else {
                            echo ' active ';
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
                    <span>
                        <?php
                        if ($paid) {
                            $paidNetto = $paidNetto + $netSum;
                        } else if ($dunning) {
                            $dunningNetto = $dunningNetto + $netSum;
                        } else if($cancelled == false){
                            $openNetto = $openNetto + $netSum;
                        } 
    
                        if ($cancelled) {
                            $cancelledNetto = $cancelledNetto + $netSum;
                        }
                        $totalNetto = $totalNetto + $netSum;
                        echo number_format($netSum, 2, ',', '.') ." €"; ?>
                    </span>
                    
                </td>

                <td class="manage-column  columnTotal">
                <span>
                        <?php
                        if ($paid) {
                            $paidTotal = $paidTotal + $totalSum;
                        } else if ($dunning) {
                            $dunningTotal = $dunningTotal + $totalSum;
                        } else if($cancelled == false){
                            $openTotal = $openTotal + $totalSum;
                        } 
    
                        if ($cancelled) {
                            $cancelledTotal = $cancelledTotal + $totalSum;
                        }
                        $totalTotal = $totalTotal + $totalSum;
                        echo number_format($totalSum, 2, ',', '.')." €"; ?>
                    </span>
                </td>

                <td class="manage-column  columnDunning">
                <span>
                        <?php 
                        $dunningFee1 = intVal(get_option('qi_settings')['dunning1']);
                        $dunningFee2 = intVal(get_option('qi_settings')['dunning2']);
                            if(intVal($invoice_header->dunning1)){
                                if(intVal($invoice_header->dunning2)){
                                    if ($paid) {
                                        $paidDunning = $paidDunning + $dunningFee1 + $dunningFee2;
                                    } else if ($dunning) {
                                        $dunningDunning = $dunningDunning + $dunningFee1 + $dunningFee2;
                                    } else if($cancelled == false){
                                        $openDunning = $openDunning + $dunningFee1 + $dunningFee2;
                                    } 
                
                                    if ($cancelled) {
                                        $cancelledDunning = $cancelledDunning + $dunningFee1 + $dunningFee2;
                                    }
                                    $totalDunning = $totalDunning + $dunningFee1 + $dunningFee2;
                                    echo number_format($dunningFee1 + $dunningFee2, 2, ',', '.')." €";
                                } else {
                                    if ($paid) {
                                        $paidDunning = $paidDunning + $dunningFee1;
                                    } else if ($dunning) {
                                        $dunningDunning = $dunningDunning + $dunningFee1;
                                    } else {
                                        $openDunning = $openDunning + $dunningFee1;
                                    } 
                
                                    if ($cancelled) {
                                        $cancelledDunning = $cancelledDunning + $dunningFee1;
                                    }
                                    $totalDunning = $totalDunning + $dunningFee1;
                                    echo number_format($dunningFee1, 2, ',', '.')." €";
                                }
                            }
                         ?>
                    </span>
                </td>

                <td class="manage-column  columnStatusPaid">
                <span style="font-size: 20px; <?php if($cancelled){echo 'opacity:0;';}?>"
                        id="<?php echo esc_attr($invoice_header->id);?>" 
                        title="Mark As Paid"
                        class="invoicePaid markAsPaid dashicons dashicons-money-alt"
                        value="<?php echo esc_html($invoice_header->id);?>"
                    >
                    </span>

                    <label class="switch switchForPaidStatus large" style="<?php if($cancelled){echo 'opacity:0;';}?>">
                    <input type="checkbox" class="checkboxForPayment" style="<?php if($cancelled){echo 'opacity:0;';}?>"
                        <?php if ($paid){ echo "checked";}?>
                    >
                    <span class="sliderForPayment invoiceSlider round large" style="<?php if($cancelled){echo 'opacity:0;';}?>"></span>
                    </label>
                
                </td>


                <td class="manage-column  columnEdit">

                    <a 
                        style="font-size:20px; display:inline" 
                        target="_top"
                        href=<?php 
                        echo "'". plugins_url() .
                            '/q_invoice/pdf/'. 
                            Interface_Export::makeFilename($invoice_header->id).
                            '.pdf'."' "
                        ?>"
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
                        if ($paid){echo 'color: lightgrey;';}
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
        style="border-top: 2px dashed lightgray"
    >    

        <td class="manage-column  columnInvoiceID sortable asc"></td>
            
        <td class="manage-column  columnStatus"></td>

        <td class="manage-column  columnName"></td>

        <td class="manage-column  columnDescription"></td>

        <td class="manage-column  columnDate"></td>

        <td class="manage-column  columnNet" >
            <span id="qi_totalSumNetto">
                <?php 
                echo number_format($totalNetto, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_openSumNetto" style="display: none;">
                <?php 
                echo number_format($openNetto, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_cancelledSumNetto" style="display: none;">
                <?php 
                echo number_format($cancelledNetto, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_dunningSumNetto" style="display: none;">
                <?php 
                echo number_format($dunningNetto, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_paidSumNetto" style="display: none;">
                <?php 
                echo number_format($paidNetto, 2, ',', '.') ." €";
                 ?>
            </span>
        </td>

        <td class="manage-column  columnTotal">
            <span id="qi_totalSumTotal">
                <?php 
                echo number_format($totalTotal, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_openSumTotal" style="display: none;">
                <?php 
                echo number_format($openTotal, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_cancelledSumTotal" style="display: none;">
                <?php 
                echo number_format($cancelledTotal, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_dunningSumTotal" style="display: none;">
                <?php 
                echo number_format($dunningTotal, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_paidSumTotal" style="display: none;">
                <?php 
                echo number_format($paidTotal, 2, ',', '.') ." €";
                 ?>
            </span>
        </td>

        <td class="manage-column  columnDunning">
            <span id="qi_totalSumDunning">
                <?php 
                echo number_format($totalDunning, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_openSumDunning" style="display: none;">
                <?php 
                echo number_format($openDunning, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_cancelledSumDunning" style="display: none;">
                <?php 
                echo number_format($cancelledDunning, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_dunningSumDunning" style="display: none;">
                <?php 
                echo number_format($dunningDunning, 2, ',', '.') ." €";
                 ?>
            </span>
            <span id="qi_paidSumDunning" style="display: none;">
                <?php 
                echo number_format($paidDunning, 2, ',', '.') ." €";
                 ?>
            </span>
        </td>

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
    <?php
}
