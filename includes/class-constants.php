<?php

if ( ! class_exists( 'QI_Invoice_Constants' ) ) { 
  class QI_Invoice_Constants
  {
    const table_i_header 	= "q_invoice_header";
	const table_i_details 	= "q_invoice_details";
	const table_i_contacts 	= "q_invoice_contacts";
	const table_cn_header 	= "q_credit_note_header";
	const table_cn_details 	= "q_credit_note_details";
    const table_offer_header = "q_offer_header";
	const table_offer_details = "q_offer_details";
	const table_settings 	= "q_invoice_settings";  
	const table_template 	= "q_invoice_template";  

  } // end class
} // endif class exists