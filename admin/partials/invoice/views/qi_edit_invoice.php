<?php
/**
 * View
 * 
 * PHP version 5
 * 
 * @category   View
 * @package    QInvoice
 * @subpackage Invoice/admin
 * @author     qanuk.io <support@qanuk.io>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @version    CVS: 
 * @link       qanuk.io
 */

$currencySymbol = "€";
if (get_option('qi_settings')['invoiceCurrency'] == "Euro") {
    $currencySymbol = "€";
} else if (get_option('qi_settings')['invoiceCurrency'] == "Dollar") {
    $currencySymbol = "$";
} else if (get_option('qi_settings')['invoiceCurrency'] == "Other") {
    $currencySymbol = get_option('qi_settings')['currencySign'];
}

$taxTypes = get_option('qi_settings')['taxTypes'];
$taxes[] ="";
for ($iterator = 0; $iterator < get_option('qi_settings')['taxTypes']; $iterator++) {
    $taxes[$iterator] = get_option('qi_settings')['tax'.($iterator+1)];
}
?>


<div id="invoiceOverlay" class="overlay" style="left:0;">
    
    <div id="edit-invoice" class="edit-popup"> 

        <div id="heading-invoice" style="display:flex;">
            <h2 id="formHeaderEdit" style="width:50%; margin-top:0"><?php echo __('Edit Invoice', 'Ev'); ?></h2>
            <h2 id="formHeaderCreate" style="width:50%;"><?php echo __('New Invoice', 'Ev'); ?></h2>
            <p id="dunningWarning" style="width:50%; display:none; color:red;"><?php echo __('(Dunning Mode has been activated. The invoice can not be modified.)', 'Ev'); ?></p>
            <div style="text-align: right; width: 50%;">
            <label class="switch switchForPaidStatusWithinForm large" style="align-self:end; margin: 0 0 1em 0;">
                <input id="invoice_form_paid_toggle" type="checkbox" class="checkboxForPayment">
                <span class="sliderForPaymentWithinForm invoiceSlider round large"></span>
            </label>
            </div>
        </div>
        <form id="invoiceForm"  
            action="<?php echo admin_url('admin-ajax.php');?> "
            method="post" 
            name="invoiceForm"
            class=""
            autocomplete="false"
            >

            <input id="popupFormType" name="popupFormType" value="" style="display:none;">

            <div id="invoiceInputTables">
                <div id="invoiceInputTableLeft">
                    <table id="invoiceFormInputsLeft">
                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __('Company', 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <div class="input_container"> 
                                    <input name="company" style="display:none">
                                    <input 
                                        type="text" 
                                        id="company" 
                                        name="company" 
                                        placeholder="<?php 
                                            echo __('Company Name', 'Ev')?>" 
                                        value=""
                                        
                                        autocomplete="off"
                                        class="autocompletePossField checkForModificationField"

                                        required
                                        
                                    >

                                    <div id='autocompleteCompany' class='autocompleteDIV' style="display:none;">
                                        <strong>Matching Contacts</strong>
                                    </div>
                                </div>
                                
                                
                                
                            </td>   
                        </tr>
                       
                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __("Additional", 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <input 
                                    type="text" 
                                    placeholder="<?php 
                                        echo __("Additional", 'Ev');
                                    ?>" 
                                    id="additional" 
                                    name="additional" 
                                    value=""
                                    class="checkForModificationField"
                                    autocomplete="off"
                                >
                            </td>
                        </tr>

                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __('Contact', 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <div class="input_container" style="display:flex; background-color:white;">
                                    <div class="qi_formNames">
                                        <input
                                            class="inputName autocompletePossField checkForModificationField"
                                            type="text" 
                                            placeholder="<?php 
                                                echo __("First Name", 'Ev');
                                            ?>" 
                                            id="firstname" 
                                            name="firstname" 
                                            value="" 
                                            autocomplete="off"
                                            required
                                        >

                                    </div>

                                    <div id='autocompleteFirstname'  class='autocompleteDIV' style="display:none;">
                                            <strong>Matching Contacts</strong>
                                    </div>

                                    <div class="q-invoice-flexPlaceholder" style="flex-grow: 1"></div>

                                    <div class="qi_formNames">
                                        <input
                                            class="inputName autocompletePossField checkForModificationField"
                                            type="text" 
                                            placeholder="<?php 
                                                echo __('Last Name', 'Ev');
                                            ?>"  
                                            id="lastname" 
                                            name="lastname"
                                            autocomplete="none"
                                            value="" 
                                            required
                                        >
                                        
                                    </div>

                                    <div id='autocompleteLastname'  class='autocompleteDIV' style="display:none;">
                                        <strong>Matching Contacts</strong>
                                    </div>
                                </div>
                                

                                
                            </td>
                        </tr>

                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __("Street", 'Ev');?>  
                            </td>

                            <td class="inputsLeftTable">
                                <input 
                                    type="text" 
                                    placeholder="<?php echo __("Street", 'Ev');?>" 
                                    id="street" 
                                    name="street"
                                    autocomplete="none" 
                                    value=""
                                    class="checkForModificationField"
                                    required
                                >
                                
                            </td>
                        </tr>

                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __("Address", 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <div style="display:flex;">
                                    <div class="qi_formZIP">
                                        <input 
                                            placeholder="<?php 
                                                echo __("ZIP Code", 'Ev');
                                            ?>" 
                                            type="text" 
                                            id="zip" 
                                            name="zip"
                                            autocomplete="none" 
                                            value=""
                                            class="checkForModificationField" 
                                            required
                                        >
                                        
                                    </div>

                                    <div class="q-invoice-flexPlaceholder" style="flex-grow: 1"></div>

                                    <div class="qi_formCity">

                                        <input 
                                            placeholder="<?php 
                                                echo __("City", 'Ev');
                                            ?>" 
                                            type="text" 
                                            id="city" 
                                            name="city" 
                                            autocomplete="none"
                                            value=""
                                            class="checkForModificationField"
                                            required
                                        >
                                        
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr style="display:none">
                            <td class="labelsLeftTable">
                                <?php echo __('Customer ID', 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <input 
                                    type="text"
                                    readonly 
                                    id="loc_id" 
                                    name="loc_id"
                                    value="0"  
                                                          
                                    
                                >
                            </td>
                        </tr>
                        <tr id="qinv_saveContactRow" style="display:none">
                            <td class="labelsLeftTable"></td>
                            <td class="labelsLeftTable">
                                <input type="checkbox" name="qinv_saveContactCheckbox" id="qinv_saveContactCheckbox" value="empty" style="margin-bottom: 0;">
                                <label id="qinv_saveContactLabel" for="qinv_saveContactCheckbox"><?php echo __("Save as new Contact?", 'Ev');?></label>
                                <input type="hidden" name="qinv_saveContactHidden" id="qinv_saveContactHidden" value="false">
                                <input type="hidden" name="qinv_saveContactID" id="qinv_saveContactID" value="-1">
                                
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="invoiceInputTableSpacer">
                    
                 
                </div>
            

                <div id="invoiceInputTableRight">

                    <table id="invoiceFormInputsRight">
                        <tr>
                            <td class="labelsRightTable">
                                <?php echo __('Prefix', 'Ev');?>
                            </td>

                            <td class="inputsRightTable">
                                <input type="text" id="prefix" name="prefix" required
                                    <?php if (Interface_Invoices::getRowCountDataBase()) {
                                        echo 
                                        "value='".
                                        get_option('qi_settings')['prefix'].
                                        "' readonly ";
                                    } else {

                                    } if (get_option('qi_settings')['prefix']) {
                                        echo 
                                        "value='".
                                        get_option('qi_settings')['prefix'].
                                        "'";
                                    } else {
                                        echo 'value=""';  
                                    }?>         
                                > 
                            </td> 
                            
                        </tr>
                        
                        <tr>
                            <td class="labelsRightTable">
                                <?php echo __('Invoice ID', 'Ev');?>
                                    
                                
                            </td>

                            <td class="inputsRightTable">
                                <input type="text" 
                                    id="invoice_id" name="invoice_id" size="6"
                                    style="text-align:left;"
                                    <?php if (Interface_Invoices::getRowCountDataBase()) {
                                    echo 
                                    "value='".
                                    get_option('qi_settings')['noStart'].
                                    "' readonly ";
                                } else {

                                } if (get_option('qi_settings')['noStart']) {
                                    echo 
                                    "value='".
                                    get_option('qi_settings')['noStart'].
                                    "'";
                                } else {
                                    echo 'value=""';  
                                }?>
                                >
                            </td>
                        </tr>
                        <tr>
                            <td class="labelsRightTable">
                                <?php echo __('Invoice Date', 'Ev');?>
                
                
                            </td>

                            <td class="inputsRightTable">
                                <input  
                                    type="date" 
                                    id="dateOfInvoice" 
                                    name="dateOfInvoice"
                                    value=""
                                    required
                                    
                                >
                            </td>
                        </tr>
                        <tr>
                            <td class="labelsRightTable">
                                <?php echo __("Delivery Date", 'Ev');?>
                            </td>

                            <td class="inputsRightTable">
                                <input type="date" id="performanceDate" 
                                    name="performanceDate"
                                    value=""
                                >       
                            </td>
                        </tr>
                        <tr id="tableRowBank1">
                            <td class="labelsRightTable">
                                <?php echo (
                                    get_option('qi_settings')['BankName1']
                                );?>  
                            </td>

                            <td 
                                class="inputsRightTable"
                            >
                            
                                <input 
                                    type="radio" 
                                    id="bank1" 
                                    name="bank" 
                                    value="1" 
                                    checked>     
                            </td>
                        </tr>
                       
                        <tr id="tableRowBank2">
                            <td 
                                class="labelsRightTable" 
                            ><?php
                                echo get_option('qi_settings')['BankName2']; 
                            ?></td>
        
                            <td class="inputsRightTable">
                                       
                                <input 
                                    type="radio" 
                                    id="bank2" 
                                    name="bank" 
                                    value="2"> 
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="labelsRightTable">
                                <?php echo __('Paypal Me', 'Ev');?>
                            </td>

                            <td class="inputsRightTable">
                                <input type="text" 
                                    id="paypal_me" name="paypal_me"
                                    value="<?php echo (
                                    get_option('qi_settings')['PayPal']
                                );?>"
                                >       
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div id="qi_allItemsAndSums">
                <div class="invoice-items">
                    <table id="items" class="form-table qInvc-table">
                    <tr id="table-invoice-items-header">

                        <td class="invoiceMoveButton">
                        
                            
                        </td>
                        
                        <td class="invoiceItemsNo" > 
                            <b><?php echo __('No', 'Ev'); ?></b>
                        </td>

                        <td class="invoiceItemsAmount" > 
                            <select 
                                name="itemUnit[]" 
                                class="itemUnit" 
                                > 
                                <?php 
                                
                                $invoiceUnits = ["Amount","Hours","Liter"];
                                foreach ($invoiceUnits as $invoiceUnit) {

                                    echo "<option value='".$invoiceUnit ."'"; 
                                    if ($invoiceUnit == get_option('qi_settings')['invoiceUnit']) {
                                        echo "selected";
                                    }
                                    echo "><b>";
                                    echo $invoiceUnit;
                                        
                                    echo "</b></option>";
                                }
                                ?>
                                
                                

                            </select>
                        </td>

                        <td class="invoiceItemsDescription"> 
                            <b><?php echo __('Description', 'Ev'); ?></b> 
                        </td>

                        <td class="invoiceItemsPrice"> 
                            <b><?php echo __('Price', 'Ev'); ?></b> 
                        </td>

                        <td class="invoiceItemsDiscount"> 
                            <b><?php echo __('Discount', 'Ev'); ?> </b> 
                        </td>

                        <td class="invoiceItemsTax"> 
                            <b><?php echo __('Tax', 'Ev'); ?></b> 
                        </td>

                        <td class="invoiceItemsTotal">
                            <b><?php echo __('Total', 'Ev'); ?></b> 
                        </td>

                        <td class="invoiceItemsButtons">
                            
                        </td>
                    </tr>

                    <tr class="wp-list-table-qInvcLine invoiceSpecificRow">
                        <td class="invoiceMoveButton">
                            <span 
                            class="sortHandle dashicons dashicons-menu"> 
                            </span>
                            
                            
                        </td>
                        <td class="invoiceItemsNo">
                            
                            <span class="qInvc-pos">1</span>
                            <input 
                                style="display:none"
                                type="text" 
                                name="position[]"
                                class="position invoicePositionHidden" 
                                value="">
                            <input type="text"
                                style="display:none;"
                                name="insertInDatabase[]"
                                class="insertInDatabase" value="1">
                            <input type="text" style="display:none;" name="positionTypeIsDunning[]" class="positionTypeIsDunning" value="0"></td>
                        </td>

                        <td class="invoiceItemsAmount">
                            <input type="number"  
                            name="amountOfItems[]" 
                            class="amountOfItems"
                            value=""
                            required>
                        </td>

                        <td class="invoiceItemsDescription">
                            <input 
                                type="text"  
                                name="itemDescription[]" 
                                class="itemDescription"
                                value=""
                                required
                            >
                        </td>

                        <td class="invoiceItemsPrice">
                            <nobr class="q_inv_mobile_flex_mod">
                                <input 
                                    type="text"  
                                    class="itemPrice"
                                    style="width: 80px"
                                    name="itemPrice[]" 
                                    value="" 
                                    step="0.01"
                                    required
                                >
                                <span id="q_imv_item_price_currency"><?php echo esc_html($currencySymbol) ?></span>
                            </nobr>     
                        </td>

                        <td class="invoiceItemsDiscount">
                            <nobr id="qi_amountField">
                                
                                <input 
                                    type="text" 
                                    name="itemDiscount[]" 
                                    value="" 
                                    class="itemDiscount"
                                    style="width: 80px;"
                                    step="0.01"      
                                >
                                
                                <select name="discountType[]" class="discountType">
                                    <option value="discountPercent">%</option>
                                    <option value="discountTotal"><?php 
                                        echo esc_html($currencySymbol);
                                    ?></option>
                                </select>

                                <input 
                                    style="display:none"
                                    type="text" 
                                    name="amountActual[]"
                                    class="amountActual" 
                                    
                                    value="0"
                                    
                                >
                               
                            </nobr>
                        </td>

                        
                        <td class="invoiceItemsTax">
                            <select 
                                name="itemTax[]" 
                                class="itemTax" 
                                required
                                > 
                                <?php 
                                for ($iterator = 0; $iterator < $taxTypes; $iterator++) {
                                    if($taxes[$iterator]!=''){
                                        echo "<option value='".$taxes[$iterator]."'>". 
                                            $taxes[$iterator].
                                            "%".
                                            "</option>";
                                    }
                                }
                                ?>
                                
                                <option value="0"><?php echo "0%"; ?></option>
                                <option value=""></option>

                            </select>

                            <input 
                                style="display:none"
                                type="text" 
                                name="invoiceTax[]"
                                class="invoiceTax" 
                                value="0"
                                
                            >
                        </td>

                        <td class="invoiceItemsTotal" style="text-align: right;"> 
                            
                            <nobr> 
                                <span 
                                    class="qInvcLine-total"> 
                                </span>
                                
                                <?php echo esc_html($currencySymbol) ?>
                            </nobr>

                            <input 
                                style="display:none"
                                type="number" 
                                name="invoiceTotal[]"
                                class="invoiceTotal" 
                                value="0"
                                step="0.01" 
                            > 
                        </td>

                        <td class="invoiceItemsButtons" style="text-align: center;">

                            <span 
                                class="qInvc-delete-line dashicons dashicons-no-alt">
                            </span>
                            
                        </td>

                    </tr>
                    <?php //-------------------------Dunning Rows: 1. Reminder, 2. DunningI, 3. DunningII
                    ?>
                    <tr id="editInvoiceReminderRow" class="wp-list-table-qInvcLine">
                        <td class="invoiceMoveButton"></td>
                        <td class="invoiceItemsNo">
                            <input type="text" style="display:none;" name="insertInDatabase[]" class="insertInDatabase" value="0">
                            <input type="text" style="display:none;" name="positionTypeIsDunning[]" class="positionTypeIsDunning" value="1"></td>
                        </td>
                        <td class="invoiceItemsAmount">
                            <input type="number" style="display:none;" name="amountOfItems[]" class="amountOfItems" value="1"></td>

                        <td class="invoiceItemsDescription">
                            <input 
                                type="text"  
                                name="itemDescription[]" 
                                class="itemDescription"
                                value="Reminder Fee"
                                required
                            >
                        </td>

                        <td class="invoiceItemsPrice">
                            <nobr class="q_inv_mobile_flex_mod">
                                <input 
                                    type="text"  
                                    class="itemPrice"
                                    style="width: 80px"
                                    name="itemPrice[]" 
                                    value="<?php echo get_option('qi_settings')['reminder'];?>" 
                                    step="0.01"
                                    required
                                >
                                <span id="q_imv_item_price_currency"><?php echo esc_html($currencySymbol) ?></span>
                            </nobr>     
                        </td>

                        <td class="invoiceItemsDiscount">
                            <input type="text" name="itemDiscount[]" value="0" class="itemDiscount" style="display:none;" step="0.01">
                            <select name="discountType[]" class="discountType" style="display:none;">
                                    <option value="discountPercent" selected>%</option>
                                </select>

                                <input style="display:none" type="text" name="amountActual[]" class="amountActual" value="0">
                        </td>

                        <td class="invoiceItemsTax"></td>

                        <td class="invoiceItemsTotal" style="text-align: right;"> 
                            
                            <nobr> 
                                <span 
                                    class="qInvcLine-total"> 
                                </span>
                                
                                <?php echo esc_html($currencySymbol) ?>
                            </nobr>

                            <input 
                                style="display:none"
                                type="number" 
                                name="invoiceTotal[]"
                                class="invoiceTotal" 
                                value="0"
                                step="0.01" 
                            > 
                        </td>

                        <td class="invoiceItemsButtons" style="text-align: center;">

                            <span 
                                class="qInvc-delete-line dashicons dashicons-no-alt">
                            </span>
                            
                        </td>

                    </tr>

                    <tr id="editInvoiceDunningIRow" class="wp-list-table-qInvcLine">
                        <td class="invoiceMoveButton"></td>
                        <td class="invoiceItemsNo">
                            <input type="text" style="display:none;" name="insertInDatabase[]" class="insertInDatabase" value="0">
                            <input type="text" style="display:none;" name="positionTypeIsDunning[]" class="positionTypeIsDunning" value="1"></td>
                        </td>
                        <td class="invoiceItemsAmount">
                            <input type="number" style="display:none;" name="amountOfItems[]" class="amountOfItems" value="1"></td>

                        <td class="invoiceItemsDescription">
                            <input 
                                type="text"  
                                name="itemDescription[]" 
                                class="itemDescription"
                                value="First Dunning Fee"
                                required
                            >
                        </td>

                        <td class="invoiceItemsPrice">
                            <nobr class="q_inv_mobile_flex_mod">
                                <input 
                                    type="text"  
                                    class="itemPrice"
                                    style="width: 80px"
                                    name="itemPrice[]" 
                                    value="<?php echo get_option('qi_settings')['dunning1'];?>" 
                                    step="0.01"
                                    required
                                >
                                <span id="q_imv_item_price_currency"><?php echo esc_html($currencySymbol) ?></span>
                            </nobr>     
                        </td>

                        <td class="invoiceItemsDiscount">
                            <input type="text" name="itemDiscount[]" value="0" class="itemDiscount" style="display:none;" step="0.01">
                            <select name="discountType[]" class="discountType" style="display:none;">
                                    <option value="discountPercent" selected>%</option>
                                </select>

                                <input style="display:none" type="text" name="amountActual[]" class="amountActual" value="0">
                        </td>

                        <td class="invoiceItemsTax"></td>

                        <td class="invoiceItemsTotal" style="text-align: right;"> 
                            
                            <nobr> 
                                <span 
                                    class="qInvcLine-total"> 
                                </span>
                                
                                <?php echo esc_html($currencySymbol) ?>
                            </nobr>

                            <input 
                                style="display:none"
                                type="number" 
                                name="invoiceTotal[]"
                                class="invoiceTotal" 
                                value="0"
                                step="0.01" 
                            > 
                        </td>

                        <td class="invoiceItemsButtons" style="text-align: center;">

                            <span 
                                class="qInvc-delete-line dashicons dashicons-no-alt">
                            </span>
                            
                        </td>

                    </tr>

                    <tr id="editInvoiceDunningIIRow" class="wp-list-table-qInvcLine">
                        <td class="invoiceMoveButton"></td>
                        <td class="invoiceItemsNo">
                            <input type="text" style="display:none;" name="insertInDatabase[]" class="insertInDatabase" value="0">
                            <input type="text" style="display:none;" name="positionTypeIsDunning[]" class="positionTypeIsDunning" value="1"></td>
                        </td>
                        <td class="invoiceItemsAmount">
                            <input type="number" style="display:none;" name="amountOfItems[]" class="amountOfItems" value="1"></td>

                        <td class="invoiceItemsDescription">
                            <input 
                                type="text"  
                                name="itemDescription[]" 
                                class="itemDescription"
                                value="Second Dunning Fee"
                                required
                            >
                        </td>

                        <td class="invoiceItemsPrice">
                            <nobr class="q_inv_mobile_flex_mod">
                                <input 
                                    type="text"  
                                    class="itemPrice"
                                    style="width: 80px"
                                    name="itemPrice[]" 
                                    value="<?php echo get_option('qi_settings')['dunning2'];?>" 
                                    step="0.01"
                                    required
                                >
                                <span id="q_imv_item_price_currency"><?php echo esc_html($currencySymbol) ?></span>
                            </nobr>     
                        </td>

                        <td class="invoiceItemsDiscount">
                            <input type="text" name="itemDiscount[]" value="0" class="itemDiscount" style="display:none;" step="0.01">
                            <select name="discountType[]" class="discountType" style="display:none;">
                                    <option value="discountPercent" selected>%</option>
                                </select>

                                <input style="display:none" type="text" name="amountActual[]" class="amountActual" value="0">
                        </td>

                        <td class="invoiceItemsTax"></td>

                        <td class="invoiceItemsTotal" style="text-align: right;"> 
                            
                            <nobr> 
                                <span 
                                    class="qInvcLine-total"> 
                                </span>
                                
                                <?php echo esc_html($currencySymbol) ?>
                            </nobr>

                            <input 
                                style="display:none"
                                type="number" 
                                name="invoiceTotal[]"
                                class="invoiceTotal" 
                                value="0"
                                step="0.01" 
                            > 
                        </td>

                        <td class="invoiceItemsButtons" style="text-align: center;">

                            <span 
                                class="qInvc-delete-line dashicons dashicons-no-alt">
                            </span>
                            
                        </td>

                    </tr>

                    </table>
                </div>
                <!-- ----------------------------------------------------------- -->
                <div id="qi_invoiceFormPositions">
                    <button 
                        type="button"               
                        id="qInvc-add-line">
                        <span style="vertical-align: middle;"> 
                            &#10010; 
                            <?php echo __('New Position', 'Ev'); ?>
                        </span>  
                    </button>
                                

                    <div id="invoice-sums" >
                        <table id="sums" class="form-table">
                            <tr class="invoiceSums qi_mobileFlex">

                                <td class="qInvc-tota invoiceSumsLabel">
                                    <?php echo __('Net', 'Ev'); ?>
                                </td>

                                <td  
                                    class="qInvc-total invoiceSumsAccounts">
                                        <span class="qInvc-total-summe"></span> 
                                        <?php echo esc_html($currencySymbol); ?>
                                </td>
                            </tr>

                                
                                
                                
                            <tr class="invoiceSums qi_mobileFlex" id="qInvc-total-gross">
                                <td class="qInvc-total invoiceSumsLabel">
                                    <?php echo __('Gross', 'Ev'); ?> 
                                </td>
                                
                                <td class="qInvc-total invoiceSumsAccounts">
                                    <span class="qInvc-total-brutto-summe"></span> 
                                    <?php echo esc_html($currencySymbol); ?>
                                </td>
                                
                                
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <input 
                type="hidden" 
                name="action" 
                value="saveInvoiceServerSide"
            >
            
            <!-- TODO Clean. Is this still needed?-->
            <input 
                type="hidden" 
                id="invoiceID"
                name="invoiceID" 
                value="0"
            >

            <div id="nonceFields">
                <div id="saveInvoiceDIV">
                    <?php wp_nonce_field(
                        'saveInvoiceServerSide', 
                        'q_invoice_nonce'
                    ); ?>
                </div>
                
                <div id="updateInvoiceDIV">
                    <?php wp_nonce_field(
                        'updateInvoiceServerSide', 
                        'q_invoice_nonce'
                    ); ?>
                </div>
            </div>
            
            <div id="qi_invoiceFormButtons" style="padding: 20px 20px 0 20px">

                <button 
                    type="button" 
                    class="qInvoiceFormButton cancelButton" 
                    id="cancelInvoiceEdit"
                    name="cancelInvoiceEdit">
                    <?php echo __('Cancel', 'Ev'); ?>
                </button>


                <input 
                    style="display: none; float: right"
                    type="submit"
                    value="<?php echo __('Save', 'Ev'); ?>"
                    name="save"
                    id="saveInvoice"
                    class="qInvoiceFormButton submitButton"
                />

                <input 
                    style="display: none; float: right"
                    type="submit"
                    value="<?php echo __('Update', 'Ev'); ?>"
                    name="update"
                    id="updateInvoice"
                    class="qInvoiceFormButton submitButton"
                />
                
                
            </div>
            
            
        </form>
       
      
    </div>
            
</div>

 