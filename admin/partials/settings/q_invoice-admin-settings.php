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
<h1 class="headerline">
        <img id="imgSnowflake" 
            src="<?php echo esc_url(
                plugins_url('../../img/qanuk_snowflake.png', __FILE__)
            );?>">
        <span id="qanuk_title"><?php _e('Q invoice by qanuk.io', 'Ev'); ?></span>
        <span id="qanuk_title_media"><?php _e('Settings', 'Ev'); ?></span> 
       
        
    </h1>
    
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
                    <div style="height:450px;">
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
        <div>
                    <?php
                        settings_fields('invoiceTextForm');
                        do_settings_sections('invoiceTextPage');
                        
                    ?>
                </div>  
        <?php 
           
        
        submit_button($text = "Save Settings", $type = "primary", $name = "saveSettings"); 
        ?>
    
    </form>
    
    <?php 
    
            //print_r(get_option('qi_settings'));
    ?>
</div>
<?php

