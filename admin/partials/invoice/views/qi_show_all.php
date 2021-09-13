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
    <div class="tab_content_wrapper">
       
    <table id="tableInvoices" class="wp-list-table widefat">

        <thead id="tableInvoicesHeader">
            <tr>
                <th scope="col" id="id" 
                    class="manage-column fifty_col column-id sortable asc ">
                    <?php _e('#', 'Ev'); ?>
                </th>

                <th scope="col" id="company" 
                    class="manage-column twohundred_col column-company">
                    <?php _e('Company', 'Ev'); ?>
                </th>
                
                <th scope="col" id="name" 
                    class="manage-column hundred_col column-name">
                    <?php _e('Name', 'Ev'); ?>
                </th>
                
                <th scope="col" id="sumNet" 
                    class="manage-column fifty_col column-net">
                    <?php _e('Net', 'Ev'); ?>
                </th>
                
                <th scope="col" id="sumTotal" 
                    class="manage-column eighty_col column-total ">
                    <?php _e('Total', 'Ev'); ?>
                </th>

                <th scope="col" id="paydate" 
                    class="manage-column twohundred_col column-date">
                    <?php _e('Invoice Date', 'Ev'); ?>
                </th>
                
                <th scope="col" id="sumbrutto" 
                    class="manage-column eighty_col column-edit ">
                    <?php _e('', 'Ev'); ?>
                </th>
            </tr>
        </thead>
<?php

showOpenInvoices();

/**
 * Function showOpenInvoices
 * 
 * @return void
 */
function showOpenInvoices()
{
    $table_name = $GLOBALS['wpdb']->prefix . QI_Invoice_Constants::TABLE_QI_HEADER;
    
    $invoice_headers = $GLOBALS['wpdb']->get_results(
        "SELECT * FROM $table_name ORDER BY invoice_date, company, name DESC"
    );

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
                    class="manage-column fifty_col column-id sortable asc">
                    <?php echo esc_attr($count); ?>
                </td>

                <td id="q_invoice_contact_company_<?php echo $invoice_header->id; ?>"
                    class="manage-column twohundred_col column-company">
                    <?php echo $invoice_header->company ?> 
                </td>

                <td id="q_invoice_contact_name_<?php echo $invoice_header->id; ?>"" 
                    class="manage-column hundred_col column-name">
                    <span id="q_invoice_contact_firstname_<?php echo $invoice_header->id; ?>">
                        <?php echo  $invoice_header->firstname ?>
                    </span>
                    
                    <span id="q_invoice_contact_lastname_<?php echo $invoice_header->id; ?>">
                        <?php echo  $invoice_header->name ?>
                    </span>
                </td>

                <td class="manage-column fifty_col column-net">
                    <span id="q_invoice_detail_net_total_<?php echo $invoice_header->id; ?>">
                        <?php echo number_format($netSum, 2, ',', ' ') ." €" ?>
                    </span>
                    
                </td>

                <td class="manage-column fifty_col column-net">
                <span id="q_invoice_detail_total_<?php echo $invoice_header->id; ?>">
                        <?php echo number_format($totalSum, 2, ',', ' ')." €" ?>
                    </span>
                </td>

                <td class="manage-column fifty_col column-date"
                    id="q_invoice_contact_date_<?php echo $invoice_header->id; ?>"
                >
                <?php echo date("d.m.Y", strtotime($invoice_header->invoice_date)); ?>
                </td>

                <td class="manage-column eighty_col column-edit">
                    
                    <!--span style="font-size: 20px"
                        id="<?php echo "edit-".$invoice_header->id;?>"
                        title="edit"
                        class="edit dashicons dashicons-edit">
                    </span-->

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
                        class="loschen dashicons dashicons-no">
                    </span>

                </td>
            </tr>
            <?php 
    }

        

}


?>
        

        </table>   
    </div>
</div>