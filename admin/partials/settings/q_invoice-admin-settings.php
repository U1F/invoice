<?php 
/**
 * View
 * 
 * PHP version 5
 * 
 * @category   View
 * @package    QInvoice
 * @subpackage QInvoice/admin
 * @author     qanuk.io <support@qanuk.io>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @version    CVS: 
 * 
 * @link a.de
 */

?>

<div class='q-invoice-page invoice-page'>

<div id="qinv_settings_deleteLogoOverlay" class="overlay dialogOverlay">
    <div class="confirmationBox">
        <div style="margin: 1.5em 0 1em 0; text-align: center;">
            <h3 style="color:red;" >Do you really want to delete the logo?</h3>
            <p>This can not be undone. <br> You can upload another logo afterwards. </p>
            <div style="text-align: center; margin: 1em 0;">
                <button class="qInvoiceFormButton cancelButton" id="cancelRemoveLogo">
                    Cancel
                </button>
                <button class="qInvoiceFormButton submitButton" id="confirmRemoveLogo">
                    Delete
                </button>
            </div>
        </div>
    </div> 
</div>
<h1 class="headerline">
    <span id="qinv_settings_title_logo" style="display:flex;">  
        <img id="imgSnowflake" 
            src="<?php echo esc_url(
                plugins_url('../../img/qanuk_snowflake.png', __FILE__)
        );?>">
    </span>
        <span id="qanuk_title" style="margin-left: 10px;"><?php _e('Settings', 'Ev'); ?></span>
        <span id="qanuk_title_media" style="margin-left: 10px;"><?php _e('Settings', 'Ev'); ?></span>
        <?php
            //When Rows in database make prefix and id readonly
            $empty = $GLOBALS['wpdb']->get_var("SELECT COUNT(id) FROM ".
            $GLOBALS['wpdb']->prefix . 
            QI_Invoice_Constants::TABLE_QI_HEADER . ";");
        ?>
        <p id="q-invoice-readonly-dummy" style="display: none"><?php echo $empty;?></p> 
       
        
    </h1>
    
    <p id="q-invoice-new-readonly-dummy" style="display: none"><?php 
        echo Interface_Invoices::getRowCountDatabase();
    ?></p> 
    
    <div id="settingsFormWrapper">
        <form 
            id='qinvoiceSettings' 
            action='options.php' 
            enctype='multipart/form-data' 
            method='post' >
        
            
        
        
        
            <section id="settingsTable" class="invoiceSettings">
                
                <div id="firstColumn">
                    <div class="container containerFirst">
                        <?php
                            settings_fields('pluginForm');
                            do_settings_sections('pluginPage'); 
                            settings_fields('logoForm');
                            do_settings_sections('logoPage'); 
                        ?>
                    </div>
        
                    <div class="container containerSecond">
                        <?php
                            settings_fields('invoiceForm');
                            do_settings_sections('invoicePage');
        
                        ?>
                    </div>
        
                    
                </div>
        
                <div id="secondColumn">
                    <div class="container containerFirst">
                        <div id="qi_contactFormHeightMod">
                        <?php
                            settings_fields('contactForm');
                            do_settings_sections('contactPage');
                        ?>
                        </div >
        
                        <div class="container containerSecond">
                        <?php
                            settings_fields('dunningForm');
                            do_settings_sections('dunningPage'); 
                        ?>
                        </div>
                        
                    </div>
                </div>
                
                <div id="thirdColumn">
                    <div class="container containerFirst" >
                    <?php
                    settings_fields('bankForm');
                    do_settings_sections('bankPage');
                    ?>
                    </div>
        
                    <div class="container containerSecond">
                    <?php
                    settings_fields('mailForm');
                    do_settings_sections('mailPage');
                    ?>
                    </div>
                    
        
                    
                </div>
                
                
        
            </section>
            <section class="invoiceSettings">
            <div >
                <?php
                    settings_fields('invoiceTextForm');
                    do_settings_sections('invoiceTextPage');
                    
                ?>
            </div>  
            </section>
            <?php 
               
            
            submit_button($text = "Save Settings", $type = "primary", $name = "saveSettings"); 
            ?>
        
        </form>
    </div>
    
    <?php 
    
    ?>
</div>
<?php

