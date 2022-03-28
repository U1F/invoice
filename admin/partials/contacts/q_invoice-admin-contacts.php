<?php
/**
 * This file.
 * 
 * Is there more?
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

    <div id="deleteContact" class="overlay dialogOverlay">
        <div class="confirmationBox">
            <div id = "confirmationBoxBox">
                <h3 id = "confirmationBoxHeader3">Do you really want to delete the Contact?</h3>
                <p>This can not be undone. </p>
                <div id="confirmationBoxButtons">
                    <button class="qInvoiceFormButton cancelButton" id="cancelDeleteContact">
                        Cancel
                    </button>
                    <button class="qInvoiceFormButton submitButton" id="confirmDeleteContact">
                        Delete
                    </button>
                </div>
            </div>
        </div>
        
    </div>

    <h1 id="qi_contactsHeadline" class="headerline" style="display:flex;">
        <span id="qinv_contact_title_logo" style="display:flex;">
            <img id="imgSnowflake" 
                src="<?php echo esc_url(
                    plugins_url('../../img/qanuk_snowflake.png', __FILE__)
            );?>">
        </span>

        <span id="qanuk_title" style="margin-left: 10px; line-height:100%"><?php _e('Contacts', 'Ev'); ?></span>
        <span id="qanuk_title_media" style="margin-left: 10px; line-height:100%"><?php _e('Contacts', 'Ev'); ?></span> 
        <div class="qinv_startButtonMod">
            <button id="qiNewContact" class="button-primary q_invoice_outerButton">
                <?php _e('New Contact')?>
            </button>   
        </div>
        
    </h1>
   


    
    <div id="show-contacts">
        <table class="wp-list-table widefat" id="contacts">
            <thead id="contactTableHeader"> 
                <td class="check-column contactsColumnRowID">#</td>
                <td class="check-column contactsColumnCompany contactContentColumn">Company</td>
                <td class="check-column contactsColumnName contactContentColumn">Name</td>
                <td class="check-column contactsColumnCity contactContentColumn">City</td>
                <td class="check-column contactsColumnEmail contactContentColumn">Email</td>
                <td class="check-column contactsColumnStatus"></td>  
                <td class="check-column contactsColumnEdit"></td>  
            </thead>
            <?php
            $table_name = $GLOBALS['wpdb']->prefix . 
            QI_Invoice_Constants::TABLE_QI_CONTACTS;
    
            $contacts = $GLOBALS['wpdb']->get_results(
                "SELECT * FROM $table_name ORDER BY id, company, lastname DESC"
            );
         
            $count = 0;
            foreach ($contacts as $contact) {

                $count++;
                ?>
                <tr value="<?php echo esc_html($contact->id);?>">
               

                <td class="check-column manage-column contactColumnRowID">
                    <?php echo esc_html($count); ?>
                </td>

                <td class="check-column manage-column contactColumnCompany contactContentColumn">
                    <?php echo esc_html($contact->company); ?> 
                </td>

                <td class="check-column manage-column contactColumnName contactContentColumn">
                    
                    <span class="columnFirstName">
                        <?php echo  esc_html($contact->firstname); ?>
                    </span>
                    
                    <span class="columnLastName">
                        <?php echo  esc_html($contact->lastname); ?>
                    </span>

                </td>

                <td class="check-column manage-column contactColumnCity contactContentColumn">
                    <span>
                        <?php echo esc_html($contact->city); ?>
                    </span>
                    
                </td>

                <td class="check-column manage-column contactColumnEmail contactContentColumn">
                <span>
                        <?php echo esc_html($contact->email); ?>
                    </span>
                </td>

                <td class="check-column manage-column contactColumnStatus">
                </td>

                <td class="check-column manage-column contactColumnEdit">
                    
                    <span style="font-size: 20px"
                        id="<?php echo "edit-".$contact->id;?>"
                        title="edit"
                        class="editContact dashicons dashicons-edit">
                    </span>

                    <span style="font-size: 20px"
                        id="<?php echo esc_attr($contact->id); //should be delete-ID-?>" 
                        title="delete"
                        class="deleteContact dashicons dashicons-no">
                    </span>

                </td>
            </tr>
                <?php
            }
            ?>
            
            
        </table>
    </div>

    <div id="contactOverlay" class="overlay" style="display:none;">
        <div id="qiContactPopup">
            <h2><?php echo __("New Contact", "ev")?></h2>
            <form 
                action="<?php echo admin_url('admin-ajax.php');?> "
                method="post" 
                name="qiContactForm"
                id="qiContactForm">
                <table id="qiContactFormTable">
                    <thead>

                    </thead>
                    <tbody>
                        <tr>
                            <td class="qiContactTableLabel">
                                <?php echo __("ID", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactID"
                                    id="qiContactID"
                                    Company="qiContactID"
                                    class="qiContactInput"
                                    type="text"
                                    style="width: 60px" 
                                    value=""
                                    readonly>
                            </td>
                        </tr>
                        <tr>
                                

                            <td class="qiContactTableLabel">
                                <?php echo __("Company", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactCompany"
                                    id="qiContactCompany"
                                    class="qiContactInput"
                                    type="text"
                                    
                                    >
                            </td>
                            </tr>
                        <tr>
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("Additional", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactAdditional"
                                    id="qiContactAdditional"
                                    class="qiContactInput"
                                    type="text"
                                    >
                            </td>
                            </tr>
                        
                        <tr>
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("Firstname", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactFirstname"
                                    id="qiContactFirstname"
                                    class="qiContactInput"
                                    type="text"
                                    >
                            </td>
                            </tr>
                        <tr>
                        <tr>
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("Name", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactName"
                                    id="qiContactName"
                                    class="qiContactInput"
                                    type="text"
                                    required
                                    >   
                            </td>
                            </tr>
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("Street", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactStreet"
                                    id="qiContactStreet"
                                    class="qiContactInput"
                                    type="text"
                                    required
                                    >
                            </td>
                            </tr>
                        <tr>
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("ZIP", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactZIP"
                                    id="qiContactZIP"
                                    class="qiContactInput"
                                    type="number"
                                    style="width: 90px" 
                                    required
                                    >
                            </td>
                            </tr>
                        <tr>
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("City", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactCity"
                                    id="qiContactCity"
                                    class="qiContactInput"
                                    type="text"
                                    required
                                    >
                            </td>
                            </tr>
                        <tr>
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("Email", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactEmail"
                                    id="qiContactEmail"
                                    class="qiContactInput"
                                    type="email"
                                    >
                            </td>
                        </tr>
                    </tbody>
                </table>

                <input 
                type="hidden" 
                name="action" 
                value="saveContactServerSide"
                >
            
            
                <div id="nonceFields">
                    <div id="saveContactDIV">
                        <?php wp_nonce_field(
                            'saveContactServerSide', 
                            'q_invoice_nonce'
                        ); ?>
                    </div>
                    
                    <div id="updateContactDIV">
                        <?php wp_nonce_field(
                            'updateContactServerSide', 
                            'q_invoice_nonce'
                        ); ?>
                    </div>
                </div>
                <div id="contactFormButtons" style="padding: 20px 20px 20px 20px; display:flex;">

                <button 
                    type="button" 
                    class="qInvoiceFormButton cancelButton" 
                    id="cancelContactEdit"
                    name="cancelContactEdit">
                    <?php echo __('Cancel', 'Ev'); ?>
                </button>


                <input 
                    type="submit"
                    value="<?php echo __('Save', 'Ev'); ?>"
                    name="save"
                    id="saveContact"
                    class="qInvoiceFormButton submitButton"
                />

                <input 
                    type="submit"
                    value="<?php echo __('Update', 'Ev'); ?>"
                    name="update"
                    id="updateContact"
                    class="qInvoiceFormButton submitButton"
                />
                
                
            </div>

            </form>
        </div>
    </div>
</div>