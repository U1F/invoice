<?php
/**
 * This file contains the page content of the 
 * subpage Dunning.
 * 
 * PHP version 5
 * 
 * @category S
 * @package  QInvoice
 * @author   qanuk.io <support@qanuk.io>
 * @license  License example.org
 * @link     a.de 
 */

?>

<div class="q-invoice-page invoice-page">

    <h1 id="qi_dunningHeadline" class="headerline" style="display:flex;">
        <span id="qinv_dunning_title_logo" style="display:flex;">
            <img id="imgSnowflake" 
                src="<?php echo esc_url(
                    plugins_url('../../img/qanuk_snowflake.png', __FILE__)
            );?>">
        </span>
        <span id="qanuk_title" style="margin-left: 10px; line-height:100%"><?php _e('Dunning', 'Ev'); ?></span>
        <span id="qanuk_title_media" style="margin-left: 10px; line-height:100%"><?php _e('Dunning', 'Ev'); ?></span> 
        
    </h1>

</div>