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

        <div class="filterButton inactive" id="showCreditSettings">
            <button class="invoiceButton">
                Credit
            </button>
        </div>
    
    </div>
    <div class="mobileFilterButtons">
        <select name="settingsMobileFilterButtonsDropdown" id="settingsMobileFilterButtonsDropdown">
            <option class="mobileFilterButtonsOption" value="company" selected>Company</option>
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

            <?php
            /**
             * ###########################################
             * 1. Company Settings
             * --> 50/50 Page: Left Company Details / Right Contact Details
             * ###########################################
             */
            ?>
            <section id="companySettingsTable" class="invoiceSettingsRow activeSetting">

                <div class="settingsHalf container">

                    <?php
                        settings_fields('pluginForm');
                        do_settings_sections('pluginPage');
                    ?>

                </div>

                <div class="settingsHalf container">

                    <?php
                        settings_fields('contactForm');
                        do_settings_sections('contactPage');
                    ?>

                </div>

            </section>

            <?php
            /**
             * ###########################################
             * 2. Bank Settings
             * --> 33/33/33 Page: Bank 1 / Bank 2 / Further
             * ###########################################
             */
            ?>
            <section id="bankSettingsTable" class="invoiceSettingsRow">

                <div class="settingsThirds container">

                    <?php
                        settings_fields('bankIForm');
                        do_settings_sections('bankIPage');
                    ?>

                </div>

                <div class="settingsThirds">

                    <?php
                        settings_fields('bankIIForm');
                        do_settings_sections('bankIIPage');
                    ?>

                </div>

                <div class="settingsThirds">

                    <?php
                        settings_fields('bankIIIForm');
                        do_settings_sections('bankIIIPage');
                    ?>

                </div>

            </section>

            <?php
            /**
             * ###########################################
             * 3. Mail Settings
             * --> 100 - Mail Data top; Templates bottom;
             * ###########################################
             */
            ?>
            <section id="mailSettingsTable" class="invoiceSettingsRow">

                <div class="settingsFull">

                    <?php
                        settings_fields('mailForm');
                        do_settings_sections('mailPage');

                        settings_fields('mailTemplateForm');
                        do_settings_sections('mailTemplatePage');
                    ?>

                </div>

            </section>

            <?php
            /**
             * ###########################################
             * 4. Invoice Settings
             * --> 33/66 - Invoice Settings / Invoice PDF Templates
             * ###########################################
             */
            ?>
            <section id="invoiceSettingsTable" class="invoiceSettingsRow">

                <div class="settingsThirds">

                    <?php
                        settings_fields('invoiceForm');
                        do_settings_sections('invoicePage');
                    ?>

                </div>

                <div class="settingsTwoThirds">

                    <?php
                        settings_fields('invoiceTextForm');
                        do_settings_sections('invoiceTextPage');
                    ?>

                </div>

            </section>

            <?php
            /**
             * ###########################################
             * 5. Dunning Settings
             * --> 33/66 - Dunning Settings / Dunning PDF Templates
             * ###########################################
             */
            ?>
            <section id="dunningSettingsTable" class="invoiceSettingsRow">

                <div class="settingsThirds">

                    <?php
                        settings_fields('dunningForm');
                        do_settings_sections('dunningPage');
                    ?>

                </div>

                <div class="settingsTwoThirds">

                    <?php
                        settings_fields('dunningTextForm');
                        do_settings_sections('dunningTextPage');
                    ?>

                </div>

            </section>

            <?php
            /**
             * ###########################################
             * 6. Offer Settings
             * --> 100 PDF Template Texts
             * ###########################################
             */
            ?>
            <section id="offerSettingsTable" class="invoiceSettingsRow">

                <div class="settingsFull">

                    <?php
                        settings_fields('offerTemplateForm');
                        do_settings_sections('offerTemplatePage');
                    ?>

                </div>

            </section>

            <?php
            /**
             * ###########################################
             * 7. Credit Settings
             * --> 100 PDF Template Texts
             * ###########################################
             */
            ?>
            <section id="creditSettingsTable" class="invoiceSettingsRow">

                <div class="settingsFull">

                    <?php
                        settings_fields('creditTemplateForm');
                        do_settings_sections('creditTemplatePage');
                    ?>

                </div>

            </section>
            
            <?php
            /**
             * ###########################################
             * SUBMIT Button
             * ###########################################
             */
            submit_button($text = "Save Settings", $type = "primary", $name = "saveSettings"); 
            ?>
        
        </form>
    </div>
    
    <?php 
    
    ?>
</div>
<?php

