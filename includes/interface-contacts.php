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

/**
 * This class has static methods to retrieve, append, delete and update 
 * Contact entries to the database tables.
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
class Interface_Contacts
{
    /**
     * Function saveArrayToDB
     * 
     * @param array $contact_array 
     * 
     * @return void
     */
    static public function saveArrayToDB($contact_array)
    {
        return self::_copyFromArrayToDB($contact_array);           
    }

    /**
     * Function _copyFromArrayToDB
     * 
     * @param array $contact_array        
     * 
     * @return int 
     */
    static private function _copyFromArrayToDB($contact_array)
    {
        $GLOBALS['wpdb']->insert( 
            $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_CONTACTS,
            array(
                
                
                'company'  => $contact_array['qiContactCompany'],
                'additional' => $contact_array['qiContactAdditional'],
                'lastname' => $contact_array['qiContactName'],
                'firstname' => $contact_array['qiContactFirstname'],
                'street'=> $contact_array['qiContactStreet'],
                'zip' => $contact_array['qiContactZIP'],
                'city' => $contact_array['qiContactCity'],
                'email' => $contact_array['qiContactEmail'],
                'date' => "",
                'status' => ""
                
                 
            )
        );
        return $GLOBALS['wpdb']->insert_id;
    }

    /**
     * Function getContactData
     * 
     * @param int $contactID  
     * 
     * @return Object
     */
    static public function getContactData($contactID)
    {
        $data[] = $GLOBALS['wpdb']->get_results(
            "SELECT * FROM ".
            $GLOBALS['wpdb']->prefix . 
            \QI_Invoice_Constants::TABLE_QI_CONTACTS.
            " WHERE id = ".$contactID
        );
        


        return $data;

    }
    /**
     * Function getContactData
     * 
     * @return Object
     */
    static public function getAllContacts()
    {
        $data[] = $GLOBALS['wpdb']->get_results(
            "SELECT * FROM ".
            $GLOBALS['wpdb']->prefix . 
            \QI_Invoice_Constants::TABLE_QI_CONTACTS
        );
        
        return $data;
    }
    /**
     * Function deleteRowFromDB
     * 
     * TODO try and prepare
     * TODO DETAILS
     * 
     * @param int $id 
     * 
     * @return bool
     */
    static public function deleteRowFromDB(int $id)
    {
        $GLOBALS['wpdb']->delete(
            $GLOBALS['wpdb']->prefix . 
            \QI_Invoice_Constants::TABLE_QI_CONTACTS, 
            array( 'id' => $id )
        );

        return 0;
    }

    /**
     * Function updateContactToDB
     * 
     * @param array $contact_array        
     * 
     * @return void
     */
    static public function updateContact($contact_array)
    {
        self::_updateDBwithContact($contact_array);
    }

    /**
     * Function _updateDBwithContact
     * 
     * @param array $contact_array        
     * 
     * @return void
     */
    static private function _updateDBwithContact($contact_array)
    {
        $GLOBALS['wpdb']->show_errors();
        
        $GLOBALS['wpdb']->update( 
            $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_CONTACTS,
            array(
                'company'  => $contact_array['qiContactCompany'],
                'additional' => $contact_array['qiContactAdditional'],
                'lastname' => $contact_array['qiContactName'],
                'firstname' => $contact_array['qiContactFirstname'],
                'street'=> $contact_array['qiContactStreet'],
                'zip' => $contact_array['qiContactZIP'],
                'city' => $contact_array['qiContactCity'],
                'email' => $contact_array['qiContactEmail'],
                'date' => "",
                'status' => ""
                
                
            ),
            array ('id' => $contact_array['qiContactID'])
        );
        
        
        
        
       
        
        
    }

}