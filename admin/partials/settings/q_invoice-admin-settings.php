<?php 
/**
 * View
 * 
 * PHP version 5
 * 
 * @category   View
 * @package    Invoice
 * @subpackage Invoice/admin
 * @author     Qanuk.io <support@qanuk.io>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @version    CVS: 
 * 
 * @link a.de
 */

?>

<div class='wrap q-invoice-page invoice-page'>
    <h1 class="headerline">
        <?php _e("Settings", 'Ev'); ?>   
    </h1>
    <form 
        id='qinvoiceSettings' 
        action='options.php' 
        enctype='multipart/form-data' 
        method='post' >

        
   


        <section id="settingsTable">
            
            <div id="firstColumn">
                <div class="container containerFirst">
                    <?php
                        settings_fields('pluginForm');
                        do_settings_sections('pluginPage'); 
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

                    <div>
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
           
        <?php 
        
        
        submit_button(); 
        ?>
    
    </form>
    
    <?php 
    
            //print_r(get_option('qi_settings'));
    ?>
</div>
<?php

