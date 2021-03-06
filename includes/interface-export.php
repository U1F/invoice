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
        static function makeFilename($invoiceID, $type='invoice') 
        {
            $invoiceDate = Interface_Invoices::getInvoiceDataItem($invoiceID, "invoice_date");
            $invoiceDate = str_replace('-', '_', $invoiceDate);
            $company = Interface_Invoices::getInvoiceDataItem($invoiceID, "company");
            $lastName = Interface_Invoices::getInvoiceDataItem($invoiceID, "lastname");
            $firstName = Interface_Invoices::getInvoiceDataItem($invoiceID, "firstname");
            $customerName = $firstName.$lastName;    
            if ($company) {
                $company = str_replace('/', '_', $company);
                $company = str_replace(':', '_', $company);
                $company = str_replace('?', '_', $company);
                $company = str_replace('"', '_', $company);
                $company = str_replace('<', '_', $company);
                $company = str_replace('>', '_', $company);
                $company = str_replace('|', '_', $company);
                $company = str_replace('.', '_', $company);
                $company = str_replace(' ', '', $company);
                $customerName = $company;
            }

            $filename = 
                "Invoice-".
                get_option('qi_settings')['prefix'].
                $invoiceID. "-".
                $customerName. "-".
                $invoiceDate;
                
            if($type != 'invoice'){
                $filename = $filename."-".$type;
            }
            

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
            exportInvoice($invoiceID, "invoice");             
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

        /**
         * Function printDunningTemplate
         * 
         * @param int $invoiceID 
         * 
         * @return void
         *
         * @since 1.0.0
         */
        static function printDunningTemplate($invoiceID, $dunningType)
        {
            
            ob_start();
            include_once INVOICE_ROOT_PATH . 
            "/admin/partials/export/export.php";  
            exportInvoice($invoiceID, $dunningType);             
            $exportDun= ob_get_contents();
            ob_end_clean();

            include  INVOICE_ROOT_PATH . 
            '/admin/partials/export/html2pdf.class.php';
            $nameExtension = 'reminder';
            if($dunningType == 'reminder'){
                $nameExtension = 'reminder1';
            } else if($dunningType == 'dunningI'){
                $nameExtension = 'reminder2';
            } else if($dunningType == 'dunningII'){
                $nameExtension = 'reminder3';
            }
            try {
                $html2pdf = new HTML2PDF('P', 'A4', 'de');
                $html2pdf->writeHTML($exportDun, isset($_GET['vuehtml']));
                // PDF Name : Invoice/Dunning/etc-$prefix$no-Customername_$datum
                $html2pdf->Output(
                    INVOICE_ROOT_PATH . 
                    '/pdf/'.
                    self::makeFilename($invoiceID, $nameExtension).
                    '.pdf', 'F'
                );
            } catch (HTML2PDF_exception $e) {
                echo $e;
                exit;
            }
            
        }

   
    }