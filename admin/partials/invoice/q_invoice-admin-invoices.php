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
<div class="q-invoice-page invoice-page">
    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
    
    <div id="archiveInvoice" class="overlay dialogOverlay">
        <div class="confirmationBox">
            <div id = "confirmationBoxBox">
                <h3 id = "confirmationBoxHeader3">Do you want to cancel the invoice?</h3>
                <p>This will not delete the invoice. <br> It will be moved to the status "cancelled". </p>
                <div id="confirmationBoxButtons">
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


    <div id="reopenPaidInvoice" class="overlay dialogOverlay">
        <input id="lastClickedInvoice" style="display:none">
        <div class="confirmationBox">
            <div id = "confirmationBoxBox">
                <h3 id = "confirmationBoxHeader3">Do you really reopen the paid invoice?</h3>
                <p> This will be documented somewhere in the database, but you will not be able to find that information on your own <br> You have been warned! </p>
                <div id="confirmationBoxButtons">
                    <button class="qInvoiceFormButton cancelButton" id="cancelReopenInvoice">
                        Cancel
                    </button>
                    <button class="qInvoiceFormButton submitButton" id="confirmReopenInvoice">
                        Reopen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="reopenPaidInvoiceWithinForm" class="overlay dialogOverlay">
        <input id="lastOpenedInvoiceWithinForm" style="display:none">
        <div class="confirmationBox">
            <div id = "confirmationBoxBox">
                <h3 id = "confirmationBoxHeader3">Do you really reopen the paid invoice?</h3>
                <p> This will be documented somewhere in the database, but you will not be able to find that information on your own <br> You have been warned! </p>
                <div id="confirmationBoxButtons">
                    <button class="qInvoiceFormButton cancelButton" id="cancelReopenInvoiceFromWithinForm">
                        Cancel
                    </button>
                    <button class="qInvoiceFormButton submitButton" id="confirmReopenInvoiceFromWithinForm">
                        Reopen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 70px; width: 100%; background-color: yellow; border:dashed 3px;padding:5px;margin:5px 0;">
        <p style="font-size:20px;"><b>Sollten hier Reviews durchgeführt werden: <br>
        Der Dunning Bereich und das Dropdown befinden sich gerade im Umbau und funktionieren ggfalls nicht richtig!</b></p>


    </div>

    <h1 class="headerline" style="display:flex;">

        <span id="qinv_main_title_logo" style="display:flex;">
            <img id="imgSnowflake" 
                src="<?php 
                $snow = '../../img/qanuk_snowflake.png';
                echo esc_url(plugins_url($snow, __FILE__));
            ?>">
        </span>
        
        <span id="qanuk_title"><?php _e('Q Invoice by qanuk.io', 'Ev'); ?></span>
        <span id="qanuk_title_media"><?php _e('Q Invoice', 'Ev'); ?></span>
        
        <div class="qinv_startButtonMod">
            <button 
                class="button-primary q_invoice_outerButton" 
                id="newInvoice"
                ><?php _e('New Invoice')?>
            </button>
        </div>
    </h1>

    <?php 
        //When Rows in database --> prefix and id readonly
        $rowCountDatabase = Interface_Invoices::getRowCountDataBase();
        $dotType = get_option('qi_settings')['invoiceDotType'];
        if($dotType == '1,000.00'){
            $decimalDot = '.';
        } else if($dotType = '1.000,00'){
            $decimalDot = ',';
        }
    ?>
    <p id="q-invoice-new-readonly-dummy" value="<?php echo $decimalDot ?>" style="display: none"><?php echo esc_html($rowCountDatabase);?></p>
    <p id="q-invoice-new-dot-dummy" style="display: none"><?php echo $decimalDot ?></p> 
    
        
        
        
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
        exportInvoice(2, "invoice");          
    }
    
}
?>

