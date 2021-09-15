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

    <h1 class="headerline">
        <img id="imgSnowflake" 
            src="<?php 
            $snow = '../../img/qanuk_snowflake.png';
            echo esc_url(plugins_url($snow, __FILE__));
            ?>">
        <span id="qanuk_title"><?php _e('Q invoice by qanuk.io', 'Ev'); ?></span>
        <!--span id="qanuk_title_media"><?php _e('Q', 'Ev'); ?></span-->
        
        <span class="add_new_button">
            <button 
                class="button-primary" 
                id="newInvoice"
                ><?php _e('New Invoice')?>
            </button>  
        </span>
    </h1>
        
        
        
    <?php 
        ob_start();
        include_once \QI_Invoice_Constants::PART_PATH_QI . 
        "/admin/partials/invoice/views/qi_show_all.php";
        $showAll= ob_get_contents();
        ob_end_clean();
        echo $showAll;

        ob_start();
        include_once \QI_Invoice_Constants::PART_PATH_QI . 
        "/admin/partials/invoice/views/qi_edit_invoice.php"; 
        $editInv= ob_get_contents();
        ob_end_clean();
        echo $editInv;

        //DEBUG TESTING
        /*
        include_once \QI_Invoice_Constants::PART_PATH_QI . 
        "/admin/partials/export/export.php";  
        exportInovice(91, "invoice");          
        */
    
}
?>
