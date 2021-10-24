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
     * Function deactivateInvoice
     * 
     * TODO try and prepare
     * TODO DETAILS
     * 
     * @param int $id 
     * 
     * @return bool
     */
    static public function deactivateInvoice(int $id)
    {
        $GLOBALS['wpdb']->update( 
            $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_HEADER,
            array(
                'date_changed'  => date("Y-d-m"),
                'cancellation'  => true,
                'date_cancellation'  => date("Y-d-m")           
            ),
            array ('id' => $id)
        );
        return 0;
    }

    /**
     * Function reactivateInvoice
     * 
     * TODO try and prepare
     * TODO DETAILS
     * 
     * @param int $id 
     * 
     * @return bool
     */
    static public function reactivateInvoice(int $id)
    {
        $GLOBALS['wpdb']->update( 
            $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_HEADER,
            array(
                'date_changed'  => date("Y-d-m"),
                'cancellation'  => false,
                'date_cancellation'  => null           
            ),
            array ('id' => $id)
        );
        return 0;
    }
    /**
     * Function updateInvoiceHeaderItem
     * 
     * TODO try and prepare
     * TODO DETAILS
     * 
     * @param int $id 
     * @param array $data
     * 
     * @return bool
     */
    static public function updateInvoiceHeaderItem(int $id, array $data)
    {
        $GLOBALS['wpdb']->update( 
            $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_HEADER,
            $data,
            array ('id' => $id)
        );
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
     * Function getInvoiceDataItem
     * 
     * @param int $invoiceID
     * @param string $invoiceItem
     * 
     * @return Object
     */
    static public function getInvoiceDataItem($invoiceID, $invoiceItem)
    {
        $data = $GLOBALS['wpdb']->get_var(
            "SELECT ".$invoiceItem." FROM ".
            $GLOBALS['wpdb']->prefix . 
            \QI_Invoice_Constants::TABLE_QI_HEADER.
            " WHERE id = ".$invoiceID
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
        
    
        // Delete old details after looking for their count
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

        // Look for the count of new invoice details
        $arrayLength = count($invoice_array['itemDescription']);

        for ($i = 0; $i < $arrayLength; $i++) {
            $price = intVal($invoice_array['itemPrice'][$i]);
            $amount = intVal($invoice_array['amountOfItems'][$i]);
            $discount = intVal($invoice_array['itemDiscount'][$i]);
            $discountType = $invoice_array['discountType'][$i];
            $discountedPrice = self::discountPrice($price, $discount, $discountType); 
            $total = $amount * $discountedPrice;
            
            $GLOBALS['wpdb']->insert( 
                $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_DETAILS,
                array(
                    'invoice_id' => $invoice_array['invoice_id'],
                    'position' => $i + 1,
                    'description' => $invoice_array['itemDescription'][$i],
                    'amount' => $invoice_array['amountOfItems'][$i],
                    'amount_plan' => $invoice_array['itemPrice'][$i],
                    'discount' => $invoice_array['itemDiscount'][$i],
                    'discount_type' => $invoice_array['discountType'][$i],
                    'amount_actual' => $discountedPrice,
                    'tax' => $invoice_array['itemTax'][$i],
                    'sum' => $total
                )
            );
        }
    }

    /**
     * Function getRowCountDatabase        
     * 
     * @return string
     */
    static public function getRowCountDatabase()
    {
        $dbQuery = "SELECT COUNT(id) FROM ";
        $tableName = $GLOBALS['wpdb']->prefix . 
            QI_Invoice_Constants::TABLE_QI_HEADER;
        return $GLOBALS['wpdb']->get_var($dbQuery . $tableName. ";");
    }

    /**
     * Function discountPrice
     * 
     * @param int $price
     * @param int $discount
     * @param string $discountType       
     * 
     * @return float
     */
    static public function discountPrice (int $price, int $discount, string $discountType)
    {
            $discountedPrice = $price;
            if ($discount){
                if ($discountType == "discountPercent") {
                    if ($discount < 100){
                        $discountedPrice = $price - ($price * $discount / 100);
                    }
                }
                if ($discountType == "discountTotal") {
                    if ($discount < $price) {
                        $discountedPrice = $price - $discount;
                    }
                }
            }
        return $discountedPrice;
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
        if(empty(self::getRowCountDataBase())){

            $GLOBALS['wpdb']->query(
                'ALTER TABLE '.
                $GLOBALS['wpdb']->prefix.
                \QI_Invoice_Constants::TABLE_QI_HEADER.
                ' AUTO_INCREMENT = '. $invoice_array['invoice_id']
            );
            $options = get_option('qi_settings');
            $options['prefix'] = $invoice_array['prefix'];
            $options['noStart'] = $invoice_array['invoice_id'];
            update_option('qi_settings', $options); 
        }
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
            $price = intVal($invoice_array['itemPrice'][$i]);
            $amount = intVal($invoice_array['amountOfItems'][$i]);
            $discount = intVal($invoice_array['itemDiscount'][$i]);
            $discountType = $invoice_array['discountType'][$i];
            $discountedPrice = self::discountPrice($price,$discount, $discountType); 
            $total = $amount * $discountedPrice;

            $GLOBALS['wpdb']->insert( 
                $GLOBALS['wpdb']->prefix . \QI_Invoice_Constants::TABLE_QI_DETAILS,
                array(
                    
                    'invoice_id' => $detailID,
                    'position' => $i + 1,
                    'description' => $invoice_array['itemDescription'][$i],
                    'amount' => $invoice_array['amountOfItems'][$i],
                    'amount_plan' => $invoice_array['itemPrice'][$i],
                    'discount' => $invoice_array['itemDiscount'][$i],
                    'discount_type' => $invoice_array['discountType'][$i],
                    'amount_actual' => $discountedPrice,
                    'tax' => $invoice_array['itemTax'][$i],
                    'sum' => $total
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
        return $GLOBALS['wpdb']->get_var( 
            'SELECT id FROM ' . 
            $GLOBALS['wpdb']->prefix.
            \QI_Invoice_Constants::TABLE_QI_HEADER . 
            ' ORDER BY id DESC LIMIT 1'
        );



    }
}