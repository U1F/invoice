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

<div id="overlay">
    
    <div id="edit-invoice"> 

        <div id="heading-invoice">
            <h2 id="formHeaderEdit"><?php echo __('Edit Invoice', 'Ev'); ?></h2>
            <h2 id="formHeaderCreate"><?php echo __('New Invoice', 'Ev'); ?></h2>
        </div>
        <form id="invoice-form"  
            action="<?php echo admin_url('admin-ajax.php');?> "
            method="post" 
            name="rechnung"
            class=""
            autocomplete="off"
            autocomplete="false"
            autocomplete="chrome-off"
            >
            <div id="invoiceInputTables">
                <div id="invoiceInputTableLeft">
                    <table id="invoiceFormInputsLeft">
                        <tr>
                            <td class="labelsLeftTable">
                                <?php echo __('Company', 'Ev');?>
                            </td>

                            <td class="inputsLeftTable">
                                <div id="input_container">
                                    <input 
                                        type="text" 
                                        id="firma" 
                                        name="firma" 
                                        placeholder="<?php 
                                            echo __('Company Name', 'Ev').'"'?> 
                                        value=""
                                        autocomplete="none"
                                        
                                    >
                                    
                                    <span 
                                        id="inputDashiconCompanyRegister" 
                                        class="dashicons dashicons-admin-users">
                                    </span>
                                </div>
                                
                                

                                <div id='autocompleteCompany' style="display:none;">
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
                                            type="text" 
                                            placeholder="<?php 
                                                echo __("First Name", 'Ev');
                                            ?>" 
                                            id="firstname" 
                                            name="firstname" 
                                            value="" 
                                            required
                                        >

                                    </div>

                                    <div style="flex-grow: 1"></div>

                                    <div>
                                        <input 
                                            type="text" 
                                            placeholder="<?php 
                                                echo __('Last Name', 'Ev');
                                            ?>"  
                                            id="lastname" 
                                            name="lastname"
                                            value="" 
                                            required
                                        >
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
                                    name="strasse" 
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
                    <span 
                        style="font-size:20px;margin-top: 12px; display:none" 
                        class="lockForContacts dashicons dashicons-lock">
                    </span>
                <!-- unlock -->
                    <div 
                        id="contactRegister" 
                        style="
                            display:none;
                            background-color:white; 
                            width: 480px; 
                            border:solid 1px grey; 
                            border-radius:4px;
                            z-index: 20;
                            position: absolute;
                            margin-left: -60px;
                            "
                        >
                        <table 
                            id="contacts" 
                            style="
                                width:100%;
                                padding-top: 20px;
                                padding-bottom: 20px;
                                
                                "
                            >
                            <thead>
                                <td class="first">Company</td>
                                <td>Name</td>
                                <td class="last">City</td>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            

                <div id="invoiceInputTableRight">

                    <table id="invoiceFormInputsRight">
                        <tr>
                            <td class="labelsRightTable">
                                <?php echo __('Prefix', 'Ev');?>
                            </td>

                            <td class="inputsRightTable">
                                <input type="text" id="prefix" 
                                    readonly name="prefix" 
                                    value=<?php 
                                        echo get_option('qi_settings')['prefix']
                                    ?>
                                > 
                            </td> 
                            
                        </tr>
                        
                        <tr>
                            <td class="labelsRightTable">
                                <?php echo __('Invoice ID', 'Ev');?>
                                    
                                
                            </td>

                            <td class="inputsRightTable">
                                <input type="text" 
                                    id="invoice_id" name="invoice_id"
                                    value="" 
                                    readonly
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

                            <td class="inputsRightTable">
                                <input 
                                    type="radio" 
                                    id="bank1" 
                                    name="bank" 
                                    value="<?php echo (
                                    get_option('qi_settings')['bankName1']
                                );?> " 
                                    checked="checked"
                                >
                            </td>
                        </tr>

                        <tr id="tableRowBank2">
                            <td class="labelsRightTable">
                                <?php echo (
                                    get_option('qi_settings')['bankName2']
                                );?>
                            </td>

                            <td class="inputsRightTable">
                                <input 
                                    type="radio" 
                                    id="bank2" 
                                    name="bank" 
                                    value="<?php echo (
                                    get_option('qi_settings')['bankName2']
                                );?> "
                                >
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
                                    get_option('qi_settings')['PayPal.Me']
                                );?>"
                                >       
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr> <!-- ----------------------------------------------------------- -->

            <div class="invoice-items">
                <table id="items" class="form-table rng-table">
                    <tr id="table-invoice-items-header">

                        <td class="invoiceMoveButton">
                        
                            
                        </td>
                        
                        <td class="invoiceItemsNo" > 
                            <b><?php echo __('No', 'Ev'); ?></b>
                        </td>

                        <td class="invoiceItemsAmount" > 
                            <b><?php echo __('Amount', 'Ev'); ?></b> 
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

                    <tr class="wp-list-table-rng-line">
                        <td class="invoiceMoveButton">
                            <span 
                            class="sortHandle dashicons dashicons-menu"> 
                            </span>
                            
                            
                        </td>
                        <td class="invoiceItemsNo">
                            
                            <span class="rng-pos">1</span>
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
                            value="">
                        </td>

                        <td class="invoiceItemsDescription">
                            <input 
                                type="text"  
                                name="itemDescription[]" 
                                class="itemDescription"
                                value=""
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
                                >
                                <span><?php echo $currencySymbol ?></span>
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
                                        echo $currencySymbol;
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
                                    class="rng-line-total"> 
                                </span>
                                
                                <?php echo $currencySymbol ?>
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
                                class="rng-delete-line dashicons dashicons-no-alt">
                            </span>
                            
                        </td>

                    </tr>
                </table>
                
                <!-- ----------------------------------------------------------- -->
                
                <button 
                    type="button"               
                    id="rng-add-line">
                    <span style="vertical-align: middle;"> 
                        &#10010; 
                        <?php echo __('New Position', 'Ev'); ?>
                    </span>  
                </button>
                   

                <div id="invoice-sums" >
                    <table id="sums" class="form-table">
                        <tr class="invoiceSums">

                            <td class="rng-tota invoiceSumsLabel">
                                <?php echo __('Net', 'Ev'); ?>
                            </td>

                            <td  
                                class="rng-total invoiceSumsAccounts">
                                    <span class="rng-total-summe"></span> 
                                    <?php echo $currencySymbol ?>
                            </td>
                        </tr>

                        
                        
                        
                        <tr class="invoiceSums" id="rng-total-gross">
                            <td class="rng-total invoiceSumsLabel">
                                <?php echo __('Gross', 'Ev'); ?> 
                            </td>
                            
                            <td class="rng-total invoiceSumsAccounts">
                                <span class="rng-total-brutto-summe"></span> 
                                <?php echo $currencySymbol ?>
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
            
            <div style="padding: 20px 20px 20px 20px">

                <button 
                    type="button" 
                    class="invoiceFormButton" 
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
                    class="invoiceFormButton submitButton"
                />

                <input 
                    style="display: none; float: right"
                    type="submit"
                    value="<?php echo __('Update', 'Ev'); ?>"
                    name="update"
                    id="updateInvoice"
                    class="invoiceFormButton submitButton"
                />
                
                
            </div>
            
            
        </form>
       
      
    </div>
            
</div>

 