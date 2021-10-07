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
        <img id="imgSnowflake" 
            src="<?php echo esc_url(
                plugins_url('../../img/qanuk_snowflake.png', __FILE__)
            );?>">
        <span id="qanuk_title"><?php _e('Q invoice by qanuk.io', 'Ev'); ?></span>
        <span id="qanuk_title_media"><?php _e('Contacts', 'Ev'); ?></span> 
        <span class="addNewButton">
            <button id="qiNewContact" class="button-primary">
                <?php _e('New Contact')?>
            </button>   
        </span>
        
    </h1>
   


    
    <div id="show-contacts">
        <table class="wp-list-table widefat" id="contacts">
            <thead id="contactTabkeHeader"> 
                <td class="columnRowID fiftyCol">#</td>
                <td class="columnCompany twohundredCol">Company</td>
                <td class="columnName hundredCol">Name</td>
                <td class="columnCity fiftyCol">City</td>
                <td class="columnEmail fiftyCol">Email</td>
                <td class="columnStatus fiftyCol"></td>  
                <td class="columnEdit eightyCol"></td>  
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
                <tr value="<?php echo $contact->id;?>">
               

                <td class="manage-column fiftyCol columnRowID">
                    <?php echo esc_attr($count); ?>
                </td>

                <td class="manage-column twohundredCol columnCompany">
                    <?php echo $contact->company ?> 
                </td>

                <td class="manage-column hundredCol columnName">
                    
                    <span class="columnFirstName">
                        <?php echo  $contact->firstname; ?>
                    </span>
                    
                    <span class="columnLastName">
                        <?php echo  $contact->lastname; ?>
                    </span>

                </td>

                <td class="manage-column fiftyCol columnCity">
                    <span>
                        <?php echo $contact->city; ?>
                    </span>
                    
                </td>

                <td class="manage-column fiftyCol columnEmail">
                <span>
                        <?php echo $contact->email; ?>
                    </span>
                </td>

                <td class="manage-column fiftyCol columnStatus">
                </td>

                <td class="manage-column eightyCol columnEdit">
                    
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

    <div id="contactOverlay" class="overlay" style="display:none">
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
                                    required
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
                <div style="padding: 20px 20px 20px 20px">

                <button 
                    type="button" 
                    class="qInvoiceFormButton cancelButton" 
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
                    class="qInvoiceFormButton submitButton"
                />

                <input 
                    style="display: none; float: right"
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