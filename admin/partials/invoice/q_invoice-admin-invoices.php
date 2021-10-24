<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 * 
 * PHP version 5
 * 
 * @category   View
 * @package    QInvoice
 * @subpackage QInvoice/admin/partials
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       https://www.qanuk.io/ 
 * @since      1.0.0
 */

/**
 * Function Invoice_list
 * 
 * @return void
 */
function Invoice_list()
{
    
    ?>
<div class="q-invoice-page invoice-page" style="overflow: auto;">
    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
    <div id="archiveInvoice" class="overlay dialogOverlay">
        <div class="confirmationBox">
            <div style="margin: 1.5em 0 1em 0; text-align: center;">
                <h3 style="color:red;" >Do you want to cancel the invoice?</h3>
                <p>This will not delete the invoice. It will be moved to the status "cancelled". </p>
                <div style="text-align: center; margin: 1em 0;">
                    <button class="qInvoiceFormButton cancelButton" id="cancelRemoveInvoice">
                        Cancel
                    </button>
                    <button class="qInvoiceFormButton submitButton" id="confirmRemoveInvoice">
                        Remove
                    </button>
                </div>
            </div>
        </div>
        
    </div>

    <h1 class="headerline">
        <img id="imgSnowflake" 
            src="<?php 
            $snow = '../../img/qanuk_snowflake.png';
            echo esc_url(plugins_url($snow, __FILE__));
            ?>">
        <span id="qanuk_title"><?php _e('Q invoice by qanuk.io', 'Ev'); ?></span>
        <!--span id="qanuk_title_media"><?php _e('Q', 'Ev'); ?></span-->
        
        <span class="addNewButton">
            <button 
                class="button-primary" 
                id="newInvoice"
                ><?php _e('New Invoice')?>
            </button>  
        </span>
    </h1>

    <?php 
        //When Rows in database make prefix and id readonly
        $rowCountDatabase = Interface_Invoices::getRowCountDataBase();
    ?>
    <p id="q-invoice-new-readonly-dummy" style="display: none"><?php echo $rowCountDatabase;?></p> 
    
        
        
        
    <?php 
        ob_start();
        include_once INVOICE_ROOT_PATH . 
        "/admin/partials/invoice/views/qi_show_all.php";

        showHeader();

        showOpenInvoices();
        
        closeTable();
        $showAll= ob_get_contents();
        ob_end_clean();
        echo $showAll;

        ob_start();
        include_once INVOICE_ROOT_PATH . 
        "/admin/partials/invoice/views/qi_edit_invoice.php"; 
        $editInv= ob_get_contents();
        ob_end_clean();
        echo $editInv;

        //DEBUG TESTING
    if (get_option('wporg_setting_name')) {
        echo "qi_settings:";
        $options = get_option('qi_settings');
        echo '<pre>';
        print_r(get_option('qi_settings'));
        echo '</pre>';
        include_once INVOICE_ROOT_PATH . 
        "/admin/partials/export/export.php";  
        exportInovice(2, "invoice");          
    }
    
}
?>

