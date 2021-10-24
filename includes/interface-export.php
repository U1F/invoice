<?php 
class Interface_Export {
/**
         * Function makeFilename
         * 
         * @param int $invoiceID 
         * 
         * @return string
         *
         * @since 1.0.0
         */
        static function makeFilename($invoiceID) 
        {
            $invoiceDate = Interface_Invoices::getInvoiceDataItem($invoiceID, "invoice_date");
            $company = Interface_Invoices::getInvoiceDataItem($invoiceID, "company");
            $lastName = Interface_Invoices::getInvoiceDataItem($invoiceID, "lastname");
            $firstName = Interface_Invoices::getInvoiceDataItem($invoiceID, "firstname");
            $customerName = $firstName. "_" .$lastName;    
            if ($company) {
                $customerName = $company;
            }

            $filename = 
                "Invoice-".
                get_option('qi_settings')['prefix'].
                $invoiceID. "_".
                $customerName. "_".
                $invoiceDate;

            return $filename;
        }

        /**
         * Function printInvoiceTemplate
         * 
         * @param int $invoiceID 
         * 
         * @return void
         *
         * @since 1.0.0
         */
        static function printInvoiceTemplate($invoiceID)
        {
            
            ob_start();
            include_once INVOICE_ROOT_PATH . 
            "/admin/partials/export/export.php";  
            exportInovice($invoiceID, "invoice");             
            $exportInv= ob_get_contents();
            ob_end_clean();
            include  INVOICE_ROOT_PATH . 
            '/admin/partials/export/html2pdf.class.php';
            try {
                $html2pdf = new HTML2PDF('P', 'A4', 'de');
                $html2pdf->writeHTML($exportInv, isset($_GET['vuehtml']));
                // PDF Name : Invoice/Dunning/etc-$prefix$no-Customername_$datum
                $html2pdf->Output(
                    INVOICE_ROOT_PATH . 
                    '/pdf/'.
                    self::makeFilename($invoiceID).
                    '.pdf', 'F'
                );
            } catch (HTML2PDF_exception $e) {
                echo $e;
                exit;
            }
            
        }

   
    }