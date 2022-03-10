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

    <?php
    /**
     *             _           _       
     *       /\   | |         | |      
     *      /  \  | | ___ _ __| |_ ___ 
     *     / /\ \ | |/ _ \ '__| __/ __|
     *    / ____ \| |  __/ |  | |_\__ \
     *   /_/    \_\_|\___|_|   \__|___/                          
     */
    ?>
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

    <?php
    /**
     *   _    _                _           
     *  | |  | |              | |          
     *  | |__| | ___  __ _  __| | ___ _ __ 
     *  |  __  |/ _ \/ _` |/ _` |/ _ \ '__|
     *  | |  | |  __/ (_| | (_| |  __/ |   
     *  |_|  |_|\___|\__,_|\__,_|\___|_|                            
     */
    ?>
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
        ?>
    </p> 

    <div style="height: 70px; width: 100%; background-color: yellow; border:dashed 3px;padding:5px;margin:5px 0;">
        <p style="font-size:20px;"><b>Sollten hier Reviews durchgeführt werden: <br>
        Die Seite befindet sich gerade im Umbau und funktioniert nicht richtig!</b></p>

    </div>

    <?php
    /**
     *   ______ _   _   _            
     *  |  ____(_) | | | |           
     *  | |__   _  | | | |_ ___ _ __ 
     *  |  __| | | | | | __/ _ \ '__|
     *  | |    | | | | | ||  __/ |   
     *  |_|    |_| |_|  \__\___|_|                                                         
     */
    ?>
    <div class="filterButtons settingsFilter">
        <div class="filterButton active" id="showCompanySettings">
            <button class="invoiceButton">
                Company
            </button>
        </div>
        <div class="filterButton inactive" id="showBankSettings" style="border-left: none">
            <button class="invoiceButton">
                Bank
            </button>
        </div>
        <div class="filterButton inactive" id="showMailSettings">
            <button class="invoiceButton">
                Mail
            </button>
        </div>
        <div class="filterButton inactive" id="showInvoiceSettings">
            <button class="invoiceButton">
                Invoice
            </button>
        </div>
    
        <div class="filterButton inactive" id="showDunningSettings">
            <button class="invoiceButton">
                Dunning
            </button>
        </div>

        <div class="filterButton inactive" id="showOfferSettings">
            <button class="invoiceButton">
                Offer
            </button>
        </div>

        <div class="filterButton inactive" id="showMailSettings">
            <button class="invoiceButton">
                Credit
            </button>
        </div>
    
    </div>
    <div class="mobileFilterButtons">
        <select name="mobileFilterButtonsDropdown" id="settingsMobileFilterButtonsDropdown">
            <option class="mobileFilterButtonsOption" value="Company" selected>Company</option>
            <option class="mobileFilterButtonsOption" value="bank">Bank</option>
            <option class="mobileFilterButtonsOption" value="mail">Mail</option>
            <option class="mobileFilterButtonsOption" value="invoice">Invoice</option>
            <option class="mobileFilterButtonsOption" value="dunning">Dunning</option>
            <option class="mobileFilterButtonsOption" value="offer">Offer</option>
            <option class="mobileFilterButtonsOption" value="credit">Credit</option>
        </select>
    </div>  
    <?php
    /**
     *    _____            _             _   
     *   / ____|          | |           | |  
     *  | |     ___  _ __ | |_ ___ _ __ | |_ 
     *  | |    / _ \| '_ \| __/ _ \ '_ \| __|
     *  | |___| (_) | | | | ||  __/ | | | |_ 
     *   \_____\___/|_| |_|\__\___|_| |_|\__|                                                             
     */
    ?>
    <div id="settingsFormWrapper">

        <form 
            id='qinvoiceSettings' 
            action='options.php' 
            enctype='multipart/form-data' 
            method='post' >

            <section id="companySettingsTable" class="invoiceSettingsRow">

                <div class="settingsHalf container">

                    <?php
                        settings_fields('pluginForm');
                        do_settings_sections('pluginPage'); 
                        settings_fields('logoForm');
                        do_settings_sections('logoPage'); 
                    ?>

                </div>

                <div class="settingsHalf container">

                    <?php
                        settings_fields('contactForm');
                        do_settings_sections('contactPage');
                    ?>

                </div>

            </section>

            <section id="bankSettingsTable" class="invoiceSettingsRow">

                <div class="settingsThirds">

                    <div class="container">
                        <?php
                            settings_fields('bankForm');
                            do_settings_sections('bankPage');
                        ?>
                    </div>

                </div>

                <div class="settingsThirds">

                    

                </div>

                <div class="settingsThirds">

                    

                </div>

            </section>

            <section id="mailSettingsTable" class="invoiceSettings">

            </section>

            <section id="invoiceSettingsTable" class="invoiceSettings">

            </section>

            <section id="dunningSettingsTable" class="invoiceSettings">

            </section>

            <section id="offerSettingsTable" class="invoiceSettings">

            </section>

            <section id="creditSettingsTable" class="invoiceSettings">

            </section>
            
        
            <section id="settingsTable" class="invoiceSettings">
                
                <div id="firstColumn">
                    <?php /*<div class="container containerFirst">
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
                    </div>*/?>
        
                    
                </div>
        
                <div id="secondColumn">
                    <div class="container containerFirst">
                        <?php
                            settings_fields('invoiceForm');
                            do_settings_sections('invoicePage');
                        ?>
                        
        
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

