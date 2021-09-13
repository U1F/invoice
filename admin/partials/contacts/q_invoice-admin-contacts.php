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


    <style>
  

</style>

<div class="q-invoice-page invoice-page">
    <h1 class="headerline">
        <img id="img_snowflake" 
            src="<?php echo esc_url(
                plugins_url('../../img/qanuk_snowflake.png', __FILE__)
            );?>">
        <span id="qanuk_title"><?php _e('Q invoice by qanuk.io', 'Ev'); ?></span>
        <span id="qanuk_title_media"><?php _e('Q', 'Ev'); ?></span> 
        <span class="add_new_button">
            <button id="qiNewContact" class="button-primary">
                <?php _e('New Contact')?>
            </button>   
        </span>
        
    </h1>
   


    
    <div id="show-contacts" class='wrap'>
        <table class="wp-list-table widefat" id="contacts">
            <thead id="contactTabkeHeader"> 
                <td>#</td>
                <td>Company</td>
                <td>Name</td>
                <td>City</td>
                <td>Email</td>
                <td></td>  
                <td></td>  
            </thead>
            <?php
            $table_name = $GLOBALS['wpdb']->prefix . 
            QI_Invoice_Constants::TABLE_QI_CONTACTS;
    
            $contacts = $GLOBALS['wpdb']->get_results(
                "SELECT * FROM $table_name ORDER BY id, company, name DESC"
            );
         
            $count = 0;
            foreach ($contacts as $contact) {

                $count++;
                ?>
                <tr>
                <td class="hidden" 
                    id="q_invoice_contact_data_<?php echo $count; ?>">
                    <?php echo "popup"?>
                </td>

                <td 
                    class="manage-column fifty_col column-edit">
                    <?php echo esc_attr($count); ?>
                </td>

                <td id="q_invoice_contact_company_<?php echo $contact->id; ?>"
                    class="manage-column twohundred_col column-edit">
                    <?php echo $contact->company ?> 
                </td>

                <td class="manage-column hundred_col column-edit">
                    
                    <span id="q_invoice_contact_firstname_<?php echo $contact->id; ?>">
                        <?php echo  $contact->firstname; ?>
                    </span>
                    
                    <span id="q_invoice_contact_name_<?php echo $contact->id; ?>">
                        <?php echo  $contact->name; ?>
                    </span>

                </td>

                <td>
                    <span id="q_invoice_detail_net_total_<?php echo $contact->id; ?>">
                        <?php echo $contact->city; ?>
                    </span>
                    
                </td>

                <td>
                <span id="q_invoice_detail_total_<?php echo $contact->id; ?>">
                        <?php echo $contact->email; ?>
                    </span>
                </td>

                <td id="q_invoice_contact_status_<?php echo $contact->id; ?>"
                    class="manage-column fifty_col aktiv column-edit">
                </td>

                <td class="manage-column eighty_col column-edit">
                    
                    <span style="font-size: 20px"
                        id="<?php echo "edit-".$contact->id;?>"
                        title="edit"
                        class="editContact dashicons dashicons-edit">
                    </span>

                    <span style="font-size: 20px"
                        id="<?php echo $contact->id; //should be delete-ID-?>" 
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

    <div id="overlay" style="display:none">
        <div id="qiContactPopup">
            <h2><?php echo __("New Contact", "ev")?></h2>
            <form 
                action="<?php echo admin_url('admin-ajax.php');?> "
                method="post" 
                name="qiContactForm"
                id="qiContactForm">
                <table>
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
                                <?php echo __("Name", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactName"
                                    id="qiContactName"
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
                        
                            <td class="qiContactTableLabel">
                                <?php echo __("Street", "ev")?>
                            </td>
                            <td class="qiContactTableInput">
                                <input 
                                    name="qiContactStreet"
                                    id="qiContactStreet"
                                    class="qiContactInput"
                                    type="text"
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
                <div style="padding: 20px 20px 20px 20px">

                <button 
                    type="button" 
                    class="qiContactFormButton" 
                    id="cancelContactEdit"
                    name="cancelContactEdit">
                    <?php echo __('Cancel', 'Ev'); ?>
                </button>


                <input 
                    style="display: block; float: right"
                    type="submit"
                    value="<?php echo __('Save', 'Ev'); ?>"
                    name="save"
                    id="saveContact"
                    class="qiContactFormButton submitButton"
                />

                <input 
                    style="display: none; float: right"
                    type="submit"
                    value="<?php echo __('Update', 'Ev'); ?>"
                    name="update"
                    id="updateContact"
                    class="qiContactFormButton submitButton"
                />
                
                
            </div>

            </form>
        </div>
    </div>
</div>