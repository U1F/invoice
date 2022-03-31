<?php
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
class Interface_Settings
{

    static public function removeLogo(){

        $optionsArray = get_option('qi_settings');

        $optionsArray['companyLogo'] = '';

        update_option('qi_settings', $optionsArray);

        return 1;

    }

    static public function updateLogo($id){

        $optionsArray = get_option('qi_settings');

        $optionsArray['companyLogo'] = $id;

        update_option('qi_settings', $optionsArray);

        return 1;

    }

}

