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

        <div class="filterButton inactive" id="showOpenInvoices">
            <button class="invoiceButton">
                Open
            </button>
        </div>

        <div class="filterButton inactive" id="showCancelledInvoices">
            <button class="invoiceButton">
                Cancelled
            </button>
        </div>
        
        <div class="filterButton inactive" id="showInvoicesWithDunning">
            <button class="invoiceButton">
                Dunning
            </button>
        </div>
        
        <div class="filterButton" id="searchInvoices">
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
                    class="manage-column fiftyCol columnInvoiceID sortable asc">
                    <?php _e('Invoice ID', 'Ev'); ?>
                </th>

                <th scope="col" id="companyName" 
                    class="manage-column hundredCol columnCompany">
                    <?php _e('Company', 'Ev'); ?>
                </th>
                
                <th scope="col" id="name" 
                    class="manage-column hundredCol columnName">
                    <?php _e('Name', 'Ev'); ?>
                </th>
                
                <th scope="col" id="sumNet" 
                    class="manage-column fiftyCol columnNet">
                    <?php _e('Net', 'Ev'); ?>
                </th>
                
                <th scope="col" id="sumTotal" 
                    class="manage-column fiftyCol columnTotal ">
                    <?php _e('Total', 'Ev'); ?>
                </th>

                <th scope="col" id="paydate" 
                    class="manage-column fiftyCol columnDate">
                    <?php _e('Invoice Date', 'Ev'); ?>
                </th>
                

                <th scope="col" id="invoiceStatus" 
                    class="manage-column hundredCol columnEdit ">
                    <?php _e('', 'Ev'); ?>
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

    
    

    foreach ($invoice_headers as $invoice_header) {

        $count++;
        $type="open";
        if ($invoice_header->cancellation) {
            $type="cancelled";
        }

        $invoice_details = $GLOBALS['wpdb']->get_results(
            "SELECT * FROM ".
            $GLOBALS['wpdb']->prefix . 
            QI_Invoice_Constants::TABLE_QI_DETAILS . ' '.
            "WHERE invoice_id = " .
            $invoice_header->id . " " .  
            "ORDER BY position ASC"
        );
        
        $netSum = 0;
        $totalSum= 0;
        foreach ($invoice_details as $invoice_detail) {
        
            $netSum = $netSum + intval($invoice_detail->sum);
            $totalSum = $totalSum + 
                (intval($invoice_detail->sum) + 
                (intval($invoice_detail->sum) * intval($invoice_detail->tax) / 100));
        }     
        
        ?>
            <tr 
                class="edit <?php echo $type?>" 
                id="edit-<?php echo $invoice_header->id;?>"
                value="<?php echo $invoice_header->id;?>"
            >
                

                <td class="manage-column fiftyCol columnInvoiceID sortable asc">
                <span>
                        <?php echo $invoice_header->id ?>
                    </span>
                </td>

                <td
                    class="manage-column hundredCol columnCompany">
                    <?php echo $invoice_header->company ?> 
                </td>

                <td
                    class="manage-column hundredCol columnName">
                    <span class="firstnameSpan">
                        <?php echo  $invoice_header->firstname ?>
                    </span>
                    
                    <span class="lastnameSpan">
                        <?php echo  $invoice_header->lastname ?>
                    </span>
                </td>

                <td class="manage-column fiftyCol columnNet">
                    <span>
                        <?php echo number_format($netSum, 2, ',', ' ') ." €" ?>
                    </span>
                    
                </td>

                <td class="manage-column fiftyCol columnTotal">
                <span>
                        <?php echo number_format($totalSum, 2, ',', ' ')." €" ?>
                    </span>
                </td>

                <td class="manage-column fiftyCol columnDate"
                    
                >
                <?php echo date("d.m.Y", strtotime($invoice_header->invoice_date)); ?>
                </td>

               

                <td class="manage-column hundredCol columnEdit">

              
                    
                    <div class="circle">
                    </div>

                    <a 
                        style="font-size:20px; display:inline" 
                        target="_top"
                        href=
                        <?php 
                        echo "'".plugins_url('q_invoice/pdf/Invoice'. $invoice_header->id.'.pdf')."'    "
                        ?>                        "
                        id="<?php echo "download-".$invoice_header->id;?>"
                        title="Download Invoice"
                        class="downloadInvoice download dashicons dashicons-download"
                        value="<?php echo $invoice_header->id?>"
                        download
                    >
                    </a>

                    <span style="font-size: 20px"
                        id="<?php echo $invoice_header->id; //should be delete-ID-?>" 
                        title="Cancel Invoice"
                        class="deleteRow delete dashicons dashicons-no"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>

                    <span style="font-size: 20px;"
                        id="<?php echo $invoice_header->id;?>" 
                        title="Reactivate Invoice"
                        class="reactivateInvoice reactivate dashicons dashicons-undo"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>
                    <span style="font-size: 20px;"
                        id="<?php echo $invoice_header->id;?>" 
                        title="Archive/Canelled"
                        class="archiveSwitchLabel dashicons dashicons-archive"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>

                    <label class="switch">
                    <input type="checkbox">
                    <span class="sliderForCancellation slider round"></span>
                    </label>

                    <span style="font-size: 20px;"
                        id="<?php echo $invoice_header->id;?>" 
                        title="Mark As Paid"
                        class="invoicePaid markAsPaid dashicons dashicons-money-alt"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>

                    <label class="switch">
                    <input type="checkbox">
                    <span class="sliderForPayment slider round"></span>
                    </label>

                

                    
            </tr>
            
            
            <?php 
    }
    ?>
    
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
