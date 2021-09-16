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
 * invoice entries to the database tables.
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
class Interface_Invoices
{
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
            \QI_Invoice_Constants::TABLE_QI_HEADER, 
            array( 'id' => $id )
        );

        $rowcount = $GLOBALS['wpdb']->get_var(
            "SELECT COUNT(*) FROM ".
            $GLOBALS['wpdb']->prefix . 
            \QI_Invoice_Constants::TABLE_QI_DETAILS.
            " WHERE invoice_id = ".
            $id
        );
        
        for ($i = 0 ; $i < $rowcount; $i++ ) {
            echo "This is the ".$i.". entry to delete "; 
            $GLOBALS['wpdb']->query(
                "DELETE FROM ". 
                $GLOBALS['wpdb']->prefix . 
                \QI_Invoice_Constants::TABLE_QI_DETAILS . 
                " WHERE invoice_id = " . 
                $id 
            );
        }
        return 0;
    }

    /**
     * Function getInvoiceData
     * 
     * @param int $invoiceID  
     * 
     * @return Object
     */
    static public function getInvoiceData($invoiceID)
    {
        $data[] = $invoice_headers = $GLOBALS['wpdb']->get_results(
            "SELECT * FROM ".
            $GLOBALS['wpdb']->prefix . 
            \QI_Invoice_Constants::TABLE_QI_HEADER.
            " WHERE id = ".$invoiceID
        );
        

        $data[] = $invoice_details = $GLOBALS['wpdb']->get_results(
            "SELECT * FROM ".
            $GLOBALS['wpdb']->prefix. 
            \QI_Invoice_Constants::TABLE_QI_DETAILS.         
            " WHERE invoice_id = ".$invoiceID. 
            " ORDER BY position ASC"
        );

        return $data;

    }

    
    

    /**
     * Function _updateDBwithInvoice
     * 
     * @param array $invoice_array        
     * 
     * @return void
     */
    static private function _updateDBwithInvoice($invoice_array)
    {
        $GLOBALS['wpdb']->show_errors();
        
        $GLOBALS['wpdb']->update( 
            $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_HEADER,
            array(
                
                'prefix' => $invoice_array['prefix'],
                'invoice_date' => $invoice_array['dateOfInvoice'], 
                'delivery_date' => $invoice_array['performanceDate'],
                'company' => $invoice_array['company'],
                'additional' => $invoice_array['additional'],
                'firstname' =>  $invoice_array['firstname'],
                'lastname' =>  $invoice_array['lastname'],
                'street'  => $invoice_array['street'],
                'zip'  => $invoice_array['zip'],
                'city'  => $invoice_array['city'],
                'email'  => "",
                'bank' => $invoice_array['bank'],
                'date_changed'  => "",
                'dunning1'  => "",
                'date_dunning1'  => "",
                'dunning2'  => "",
                'date_dunning2'  => "",
                'cancellation'  => "",
                'date_cancellation'  => "",
                'paydate'  => ""
                
            ),
            array ('id' => $invoice_array['invoice_id'])
        );
        
    
        $arrayLength = count($invoice_array['itemDescription']);

        $rowcount = $GLOBALS['wpdb']->get_var(
            "SELECT COUNT(*) FROM ".
            $GLOBALS['wpdb']->prefix . 
            \QI_Invoice_Constants::TABLE_QI_DETAILS.
            " WHERE invoice_id = ".
            $invoice_array['invoice_id']
        );

        for ($i = 0 ; $i < $rowcount; $i++ ) {
            $GLOBALS['wpdb']->query(
                "DELETE FROM ". 
                $GLOBALS['wpdb']->prefix . 
                \QI_Invoice_Constants::TABLE_QI_DETAILS . 
                " WHERE invoice_id = " . 
                $invoice_array['invoice_id']
            );
        }

        for ($i = 0; $i < $arrayLength; $i++) {
            
            
            $GLOBALS['wpdb']->insert( 
                $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_DETAILS,
                array(
                    'invoice_id' => $invoice_array['invoice_id'],
                    'position' => $invoice_array['position'][$i],
                    'description' => $invoice_array['itemDescription'][$i],
                    'amount' => $invoice_array['amountOfItems'][$i],
                    'amount_plan' => $invoice_array['itemPrice'][$i],
                    'discount' => $invoice_array['itemDiscount'][$i],
                    'discount_type' => $invoice_array['discountType'][$i],
                    'amount_actual' => $invoice_array['amountActual'][$i],
                    'tax' => $invoice_array['itemTax'][$i],
                    'sum' => $invoice_array['invoiceTotal'][$i] 
                )
            );
        }
       
        
        
    }

  

    /**
     * Function _copyFromArrayToDB
     * 
     * @param array $invoice_array        
     * 
     * @return invoiceID
     */
    static private function _copyFromArrayToDB($invoice_array)
    {
        $GLOBALS['wpdb']->show_errors();
        
        $GLOBALS['wpdb']->insert( 
            $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_HEADER,
            array(
                
                'prefix' => $invoice_array['prefix'],
                'invoice_date' => $invoice_array['dateOfInvoice'], 
                'delivery_date' => $invoice_array['performanceDate'],
                'company' => $invoice_array['company'],
                'customerID' => $invoice_array['loc_id'],
                'additional' => $invoice_array['additional'],
                'firstname' =>  $invoice_array['firstname'],
                'lastname' =>  $invoice_array['lastname'],
                'street'  => $invoice_array['street'],
                'zip'  => $invoice_array['zip'],
                'city'  => $invoice_array['city'],
                'email'  => "",
                'bank' => $invoice_array['bank'],
                'date_changed'  => "",
                'dunning1'  => "",
                'date_dunning1'  => "",
                'dunning2'  => "",
                'date_dunning2'  => "",
                'cancellation'  => "",
                'date_cancellation'  => "",
                'paydate'  => ""      
            )
        );
        
        
        $arrayLength = count($invoice_array['itemDescription']);

        $detailID = $GLOBALS['wpdb']->insert_id;
        
        for ($i = 0; $i < $arrayLength; $i++) {
            $GLOBALS['wpdb']->show_errors();
          
            $GLOBALS['wpdb']->insert( 
                $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_DETAILS,
                array(
                    
                    'invoice_id' => $detailID,
                    'position' => $invoice_array['position'][$i],
                    'description' => $invoice_array['itemDescription'][$i],
                    'amount' => $invoice_array['amountOfItems'][$i],
                    'amount_plan' => $invoice_array['itemPrice'][$i],
                    'discount' => $invoice_array['itemDiscount'][$i],
                    'discount_type' => $invoice_array['discountType'][$i],
                    'amount_actual' => $invoice_array['amountActual'][$i],
                    'tax' => $invoice_array['itemTax'][$i],
                    'sum' => $invoice_array['invoiceTotal'][$i] 
                )
            );
              
        }
        return $detailID;
    }

    /**
     * Function saveArrayToDB
     * 
     * @param array $invoice_array 
     * 
     * @return int
     */
    static public function saveArrayToDB($invoice_array)
    {
        return self::_copyFromArrayToDB($invoice_array);           
    }

    /**
     * Function updateArrayInDB
     * 
     * @param array $invoice_array 
     * 
     * @return void
     */
    static public function updateArrayInDB($invoice_array)
    {
        
        self::_updateDBwithInvoice($invoice_array); 

    }

    /**
     * Function getLastID
     * 
     * @return int
     */
    static public function getLastID()
    {
        return self::_lookForLastID();
    }
    

    /**
     * Function lookForLastID
     * 
     * @return int
     */
    static private function _lookForLastID()
    {
        $GLOBALS['wpdb']->get_var( 
            'SELECT id FROM ' . 
            $GLOBALS['wpdb']->prefix.
            \QI_Invoice_Constants::TABLE_QI_HEADER . 
            ' ORDER BY id DESC LIMIT 1'
        );

    }
}