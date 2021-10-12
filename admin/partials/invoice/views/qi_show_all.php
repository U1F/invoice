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
                Archive / Cancelled
            </button>
        </div>
        
        <div class="filterButton inactive" id="showInvoicesWithDunning">
            <button class="invoiceButton">
                Dunning
            </button>
        </div>
        <div class="filterButton inactive" id="showInvoicesPaid">
            <button class="invoiceButton">
                Paid
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
        
            $netSum = $netSum + intval($invoice_detail->sum);
            $totalSum = $totalSum + 
                (intval($invoice_detail->sum) + 
                (intval($invoice_detail->sum) * intval($invoice_detail->tax) / 100));
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
                         
                ?>" 
                id="edit-<?php echo $invoice_header->id;?>"
                value="<?php echo $invoice_header->id;?>"
            >    

            <td class="manage-column  columnInvoiceID sortable asc">
                <span>
                        <?php echo $invoice_header->id ?>
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
                                echo $invoice_header->company; 
                            } else {?>
                                <span class="firstnameSpan">
                                    <?php echo  $invoice_header->firstname ?>
                                </span>
                    
                                <span class="lastnameSpan">
                                    <?php echo  $invoice_header->lastname ?>
                                </span> <?php
                            }?>
                </td>

                <td class="manage-column  columnDescription">
                    <span>
                        <?php echo  $invoice_details[0]->description ?>
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
                        <?php echo number_format($netSum, 2, ',', '.') ." €" ?>
                    </span>
                    
                </td>

                <td class="manage-column  columnTotal">
                <span>
                        <?php echo number_format($totalSum, 2, ',', '.')." €" ?>
                    </span>
                </td>

                <td class="manage-column  columnDunning">
                <span>
                        <?php 
                        $dunningFee1 = intVal(get_option('qi_settings')['dunning1']);
                        $dunningFee2 = intVal(get_option('qi_settings')['dunning2']);
                            if(intVal($invoice_header->dunning1)){
                                if(intVal($invoice_header->dunning2)){
                                    echo number_format($dunningFee1 + $dunningFee2, 2, ',', '.')." €";
                                } else {
                                    echo number_format($dunningFee1, 2, ',', '.')." €";
                                }
                            }
                         ?>
                    </span>
                </td>

                <td class="manage-column  columnStatusPaid">
                <span style="font-size: 20px; <?php if($cancelled){echo 'opacity:0;';}?>"
                        id="<?php echo $invoice_header->id;?>" 
                        title="Mark As Paid"
                        class="invoicePaid markAsPaid dashicons dashicons-money-alt"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>

                    <label class="switch switchForPaidStatus" style="<?php if($cancelled){echo 'opacity:0;';}?>">
                    <input type="checkbox" class="checkboxForPayment" style="<?php if($cancelled){echo 'opacity:0;';}?>"
                        <?php if ($paid){ echo "checked";}?>
                    >
                    <span class="sliderForPayment invoiceSlider round" style="<?php if($cancelled){echo 'opacity:0;';}?>"></span>
                    </label>
                
                </td>


                <td class="manage-column  columnEdit">

                    <a 
                        style="font-size:20px; display:inline" 
                        target="_top"
                        href=<?php 
                        echo "'". plugins_url(
                            'q_invoice/pdf/Invoice-'. 
                            get_option('qi_settings')['prefix'].'-'.
                            $invoice_header->id.'.pdf')."'    "
                        ?>"
                        id="<?php 
                            echo 
                                "download-".
                                get_option('qi_settings')['prefix'].'-'.
                                $invoice_header->id;
                            ?>"
                        title="Download Invoice"
                        class="downloadInvoice download dashicons dashicons-download"
                        value="<?php echo $invoice_header->id?>"
                        download
                    >
                    </a>
                    

                    <span style="font-size: 20px; <?php
                        if ($paid){echo 'color: lightgrey;';}
                        if ($cancelled){echo ' display: none;';}
                        ?>"
                        id="<?php echo $invoice_header->id;?>" 
                        title="Cancel Invoice"
                        class="delete <?php
                        if (!$paid){echo 'deleteRow';}
                        ?> dashicons dashicons-no"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>

                    <span style="font-size: 20px;<?php 
                        if ($cancelled){
                            echo 'display: inline-block;';
                        } else {
                            echo 'display: none;';
                        }?>"
                        id="<?php echo $invoice_header->id;?>" 
                        title="Reactivate Invoice"
                        class="reactivateInvoice reactivate dashicons dashicons-undo"
                        value="<?php echo $invoice_header->id;?>"
                    >
                    </span>
                    
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
