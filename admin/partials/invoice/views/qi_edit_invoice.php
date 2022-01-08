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
    
    <div id="edit-invoice"> 

        <div id="heading-invoice" style="display:flex;">
            <h2 id="formHeaderEdit" style="width:50%; margin-top:0"><?php echo __('Edit Invoice', 'Ev'); ?></h2>
            <h2 id="formHeaderCreate" style="width:50%;"><?php echo __('New Invoice', 'Ev'); ?></h2>
            <div style="text-align: right; width: 50%; align-self: end;">
            <label class="switch switchForPaidStatus large" style="align-self:end; margin: -13.3px 0 1em 0;">
                <input id="invoice_form_paid_toggle" type="checkbox" class="checkboxForPayment">
                <span class="sliderForPayment invoiceSlider round large"></span>
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
            <div id="invoiceInputTables">
                <div id="invoiceInputTableLeft">
                    <table id="invoiceFormInputsLeft">
                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __('Company', 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <div id="input_container"> <input name="company" style="display:none">
                                    <input 
                                        type="text" 
                                        id="company" 
                                        name="company" 
                                        placeholder="<?php 
                                            echo __('Company Name', 'Ev')?>" 
                                        value=""
                                        
                                        autocomplete="new-password"
                                        class="autocompletePossField"

                                        required
                                        
                                    >
                                    
                                    <span style="display:none"
                                        id="inputDashiconCompanyRegister" 
                                        class="dashicons dashicons-admin-users">
                                    </span>
                                </div>
                                
                                

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
                                    autocomplete="none"
                                >
                            </td>
                        </tr>

                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __('Contact', 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <div style="display:flex;">
                                    <div>
                                        <input
                                            class="inputName autocompletePossField"
                                            type="text" 
                                            placeholder="<?php 
                                                echo __("First Name", 'Ev');
                                            ?>" 
                                            id="firstname" 
                                            name="firstname" 
                                            value="" 
                                            autocomplete="none"
                                            required
                                        >
                                        <div id='autocompleteFirstname'  class='autocompleteDIV' style="display:none;">
                                        <strong>Matching Contacts</strong>
                                        </div>

                                    </div>

                                    <div style="flex-grow: 1"></div>

                                    <div>
                                        <input
                                            class="inputName autocompletePossField"
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
                                        <div id='autocompleteLastname'  class='autocompleteDIV' style="display:none;">
                                        <strong>Matching Contacts</strong>
                                        </div>
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
                                    <div>
                                        <input 
                                            placeholder="<?php 
                                                echo __("ZIP Code", 'Ev');
                                            ?>" 
                                            type="text" 
                                            id="zip" 
                                            name="zip"
                                            autocomplete="none" 
                                            value="" 
                                            required
                                        >
                                        
                                    </div>

                                    <div style="flex-grow: 1"></div>

                                    <div>

                                        <input 
                                            placeholder="<?php 
                                                echo __("City", 'Ev');
                                            ?>" 
                                            type="text" 
                                            id="city" 
                                            name="city" 
                                            autocomplete="none"
                                            value="" 
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
                                    style="width: auto; text-align:left;"
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
                                    get_option('qi_settings')['bankName1']
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
                                echo get_option('qi_settings')['bankName2']; 
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

                    <tr class="wp-list-table-qInvcLine">
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
                            <nobr>
                                <input 
                                    type="number"  
                                    class="itemPrice" 
                                    name="itemPrice[]" 
                                    value="" 
                                    step="0.01"
                                    required
                                >
                                <span><?php echo esc_html($currencySymbol) ?></span>
                            </nobr>     
                        </td>

                        <td class="invoiceItemsDiscount">
                            <nobr>
                                
                                <input 
                                    type="number" 
                                    name="itemDiscount[]" 
                                    value="" 
                                    class="itemDiscount" 
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
                                    echo "<option value='".$taxes[$iterator]."'>". 
                                        $taxes[$iterator].
                                        "%".
                                        "</option>";
                                }
                                ?>
                                
                                <option value="0" 
                                    
                                ><?php echo __('None', 'Ev'); ?></option>

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
                </table>
                
                <!-- ----------------------------------------------------------- -->
                
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
                        <tr class="invoiceSums">

                            <td class="qInvc-tota invoiceSumsLabel">
                                <?php echo __('Net', 'Ev'); ?>
                            </td>

                            <td  
                                class="qInvc-total invoiceSumsAccounts">
                                    <span class="qInvc-total-summe"></span> 
                                    <?php echo esc_html($currencySymbol); ?>
                            </td>
                        </tr>

                        
                        
                        
                        <tr class="invoiceSums" id="qInvc-total-gross">
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
            
            <div style="padding: 20px 20px 0 20px">

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

 