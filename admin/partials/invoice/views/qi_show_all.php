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
         <button class="filterButton invoiceButton   active" id="showOpenInvoices">open</button>
         <button class="filterButton invoiceButton inactive" id="showCancelledInvoices">cancelled</button>
         <button class="filterButton invoiceButton inactive" id="showInvoicesWithDunning">dunning</button>
     </div>
    <div class="tab_content_wrapper">
<?php 



/**
 * Function showOpenInvoices
 * 
 * @param string $type 
 * 
 * @return void
 */
function showHeader($type)
{
    if ($type=="open") {
        
        $title = "OPEN";
    }
    if ($type=="cancelled") {
       
        $title = "CANCELLED";
    }
    ?>
   
    <h3><?php echo $title; ?></h3>  
    <table id="tableInvoices" class="wp-list-table widefat <?php echo $type; ?>">

        <thead id="tableInvoicesHeader">
            <tr>
                <th scope="col" id="id" 
                    class="manage-column fiftyCol columnId sortable asc ">
                    <?php _e('#', 'Ev'); ?>
                </th>

                <th scope="col" id="companyName" 
                    class="manage-column twohundredCol columnCompany">
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
                <th scope="col" id="invoiceID" 
                    class="manage-column fiftyCol columnInvoiceID">
                    <?php _e('Invoice ID', 'Ev'); ?>
                </th>

                <th scope="col" id="invoiceStatus" 
                    class="manage-column fiftyCol columnStatus ">
                    <?php _e('', 'Ev'); ?>
                </th>

                
                
            </tr>
        </thead>
    <?php
    showOpenInvoices("$type");
}




/**
 * Function showOpenInvoices
 * 
 * @param string $type 
 * 
 * @return void
 */
function showOpenInvoices($type)
{
    $table_name = $GLOBALS['wpdb']->prefix . QI_Invoice_Constants::TABLE_QI_HEADER;
    if ($type=="open") {
        $query = "SELECT * FROM $table_name WHERE cancellation = false ORDER BY id DESC";
        
    }
    if ($type=="cancelled") {
        $query = "SELECT * FROM $table_name WHERE cancellation = true ORDER BY id DESC";
       
    }
    $invoice_headers = $GLOBALS['wpdb']->get_results($query);

    $count = 0;

    $netSum = 0;

    foreach ($invoice_headers as $invoice_header) {

        $count++;

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
        
            $netSum = $netSum + $invoice_detail->sum;
            $totalSum = $totalSum + 
                ($invoice_detail->sum + 
                ($invoice_detail->sum * $invoice_detail->tax / 100));
        }     

        ?>
            <tr class="edit" id="edit-<?php echo $invoice_header->id; ?>">
                <td 
                    class="manage-column fiftyCol columnId sortable asc">
                    <?php echo esc_attr($count); ?>
                </td>

                <td
                    class="manage-column twohundredCol columnCompany">
                    <?php echo $invoice_header->company ?> 
                </td>

                <td
                    class="manage-column hundredCol columnName">
                    <span>
                        <?php echo  $invoice_header->firstname ?>
                    </span>
                    
                    <span>
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

                <td class="manage-column fiftyCol columnInvoiceID">
                <span>
                        <?php echo $invoice_header->id ?>
                    </span>
                </td>

                <td class="manage-column eightyCol columnEdit">
                    
                    <a 
                        style="font-size:20px; display:inline" 
                        target="_top"
                        href=
                        <?php 
                        echo "'".plugins_url('q_invoice/pdf/Invoice'. $invoice_header->id.'.pdf')."'    "
                        ?>                        "
                        id="<?php echo "'download-".$invoice_header->id."'";?>"
                        title="Download Invoice"
                        class="download dashicons dashicons-download"
                        download
                    >
                    </a>

                    <span style="font-size: 20px"
                        id="<?php echo $invoice_header->id; //should be delete-ID-?>" 
                        title="delete"
                        class="loschen dashicons dashicons-no"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>

                </td>
            </tr>
            
            
            <?php 
    }
    ?>
</table>       
    <?php
        

}


?>
        

        
    </div>
</div>