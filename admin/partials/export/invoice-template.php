<?php
/**
 * This file contains the template for the auto generated
 * invoices/reminder/dunning/offer/credit.
 */
?>

<style>

.body {
    background: #FFFFFF;
    border-collapse: collapse;
    font-family: Helvetica, Arial;
    font-size: 12px;
}

.table_rg_head td {
    padding: 8px;
}

.table_rg_content td {
    padding: 4px;
}

.page_footer {
    width: 100%;
    border: none;
    background-color: #DDDDFF;
    border-top: solid 1mm #AAAADD;
    padding: 2mm
}

div.niveau {
    padding-left: 5mm;
}

p.invoiceSender {
    margin-bottom: 0px;
    margin-top: 0px;
}

p.receiverAddress {
    margin-bottom: 0px;
    margin-top: 0px;
}

td.invoiceInfoHeader {
    border-top: solid 1px #000000;
    border-bottom: solid 1px #000000;
    font-size: 14px;
}

td.invoiceItemsHeader {
    font-size: 12px;
}

</style>

<page>
    <div style="position: relative; margin-left:70px;">
        <table width="100%" style="margin-top:80px;">
            <tr>
                <td style="font-size:8px; width: 350px; height:40px;"></td>
                
                <td 
                    rowspan="3" 
                    style="width: 270px; text-align: right; font-size:14px;"
                >
                    <?php //insert logo image
                    echo $finalImageData; ?>                        
                    
                    <br>

                    <?php // Sender Adress from Settings - Top right corner ?>
                    <p class="invoiceSender" id="senderCompany">
                        <?php echo get_option('qi_settings')['company'];?>
                    </p>

                    <p class="invoiceSender" id="senderName">
                        <?php 
                        echo 
                            get_option('qi_settings')['firstName']. " ". 
                            get_option('qi_settings')['lastName'];
                        ?>
                    </p>

                    <p class="invoiceSender" id="senderAdditional">
                        <?php echo get_option('qi_settings')['additional'];?>
                    </p>

                    <p class="invoiceSender" id="senderAddress">
                        <?php echo get_option('qi_settings')['street'];?>
                    </p>

                    <p class="invoiceSender" id="senderAddress2"> 
                        <?php 
                        echo get_option('qi_settings')['ZIP'];
                        echo " ";
                        echo get_option('qi_settings')['city'];
                        ?>
                    </p>

                    <p class="invoiceSender" id="senderPlaceholder1"></p>
                    <br>
                    <br>
                    <p class="invoiceSender" id="senderEmail">
                        <?php echo get_option('qi_settings')['mail'];?>
                    </p>
                    
                    <p class="invoiceSender" id="senderWebsite">
                        <?php echo get_option('qi_settings')['website'];?>
                    </p>

                    <p class="invoiceSender" id="senderFB">

                        <?php 
                        if (get_option('qi_settings')['facebook']) {
                            echo "www.fb/".get_option('qi_settings')['facebook'];
                        }
                        ?>
                    </p>
                    
                    <p class="invoiceSender" id="senderInstagram">

                        <?php 
                        if (get_option('qi_settings')['instagram']) {
                            echo "www.instagram/".get_option('qi_settings')['instagram'];
                        }
                        ?>
                    </p>
                    
                    <p class="invoiceSender" id="senderPaypal">

                        <?php  
                        if (get_option('qi_settings')['PayPal']) {
                            echo "paypal.me/".get_option('qi_settings')['PayPal'];
                        }
                        ?>
                    </p>
                    
                    <?php 
                    if ($invoiceType == "reminder" || $invoiceType == "dunning1" || $invoiceType == "dunning2") {
                        ?>
                            <p class="invoiceSender" id="explainingHeading">
                                <?php echo $explainingHeading; ?>
                            </p>
                        <?php 
                    }
                    ?>
                        
                </td>
            </tr>

            <?php //Header over receiver ?>
            <tr>
                <td 
                    id="small" 
                    style="font-size:9px; vertical-align: bottom;">
                    <u>
                    <?php echo $headerOverReceiver; ?>
                    </u>
                </td>
            </tr>

            <?php // receiver details ?>
            <tr>
                <td 
                    id="invoicereceiverAddress" 
                    rowspan="3" 
                    style="vertical-align: top; font-size:14px;"
                >
                <?php if ($invoiceData[0][0]->company){?> 
                    <p class="receiverAddress" id=receiverAddressCompany>
                        <?php echo $invoiceData[0][0]->company; ?>
                    </p>
                <?php } ?>
                <?php if ($invoiceData[0][0]->additional){?> 
                    <p class="receiverAddress" id=receiverAddressAdditional>
                        <?php echo $invoiceData[0][0]->additional; ?>
                    </p>
                <?php } ?>
                    <p class="receiverAddress" id=receiverAddressName>
                        <?php echo 
                            $invoiceData[0][0]->firstname." ". 
                            $invoiceData[0][0]->lastname; 
                        ?>
                    </p>

                    <p class="receiverAddress" id=receiverAddress>
                        <?php echo $invoiceData[0][0]->street;?>
                    </p>
                    <p class="receiverAddress" id=receiverAddress2>
                        <?php echo 
                            $invoiceData[0][0]->zip." ".
                            $invoiceData[0][0]->city;
                        ?>
                    </p>
                </td>
        
            </tr>
           
        </table>
        <br><br><br><br><br><br>

        <?php //invoice content ?>
        <div 
            id="invoiceHeader" 
            style="height:100px; vertical-align: bottom; font-size: 40px;"
        >
            <p style="font-size: 24px;">
                <b> <?php echo $heading;?> </b>
            </p>
            
        </div>

        <div 
            id="invoiceText" 
            style="font-size: 14px; height: 40px; vertical-align: middle; width: 640px;"
        >
            <p class="invoiceText" id="invoiceTextRegular" style="display:inline;"> 
                <?php echo $invoiceTextIntro;?>
            </p>
        </div>

        <table 
            class="table_rg_head" 
            style="border-collapse: collapse; padding: 8px;"
        >

        <!-- Wenn es noch kein Lieferungsdatum gibt, soll die Spalte dafür 
        leer sein und die Tabelle entsprechend angepasst.
        Die Breite liegt dann bei 127 statt 180, 3 statt 4 (colspan) Spalten -->
        
            <tr id="lineBefore">
                <td 
                    style="border-bottom: solid 1px #000000;" 
                    colspan="<?php echo 3 + $deliveryDateIsSet;?>"
                >
                </td>
            </tr>

            <tr>
                <td 
                    class="invoiceInfoHeader" 
                    width="<?php echo $tableWidthOfInvoiceHead;?>"
                >
                    <b><?php echo $heading . ' ID';?></b><br>
                    <span id="invoiceID"><?php echo $invoiceID;?></span>
                </td>

                <td 
                    class="invoiceInfoHeader" 
                    width="<?php echo $tableWidthOfInvoiceHead;?>"
                >
                    <b><?php echo __("Customer ID", "ev");?></b><br> 
                    <span id="CustomerID"><?php
                        if($invoiceData[0][0]->customerID != 0){
                            echo $invoiceData[0][0]->customerID;
                        }else{
                            echo '-';
                        }
                        ?>
                    </span>
                </td>
                
                <td 
                    class="invoiceInfoHeader" 
                    width="<?php echo $tableWidthOfInvoiceHead;?>"
                >
                    <b><?php echo __("Invoice Date", "ev");?></b>
                    <br> 
                    <span id="orderDate">
                        <?php 
                            $date = new DateTime($invoiceData[0][0]->invoice_date);
                            echo $date->format('d.m.y');
                        ?>
                    </span>
                </td>
                <?php 
                if ($deliveryDateIsSet) {
                    ?>
                    <td 
                        class="invoiceInfoHeader" 
                        width="<?php echo $tableWidthOfInvoiceHead;?>"
                    >

                        <b><?php echo __("Delivery Date", "ev");?></b><br>
                        
                        <span id="serviceDate">
                            <?php
                                $date = new DateTime($invoiceData[0][0]->delivery_date);
                                echo $date->format('d.m.y');
                            ?>
                        </span>
                    </td>
                    <?php
                } 
                ?>
            </tr>
            <tr>
                <td>
                    <br><br>
                </td>
            </tr>
        </table>

        <!-- List of invoice positions ------------------------------------>
        
        <table width="100%" class="table_rg_content" style="padding:4px;">
            <tr>
                <td 
                    class="invoiceItemsHeader" 
                    style="width:15px;  font-size:12px; text-align: right;">
                    <?php echo __("#", "ev");?>
                </td>

                <td 
                    class="invoiceItemsHeader" 
                    style="width:35px;  font-size:12px; text-align: right;">
                    <?php echo $invoiceUnit; ?>

                </td>
                
                <td 
                    class="invoiceItemsHeader" 
                    style="
                        width:<?php echo $invoiceDetailDescriptionWidthHeader;?>px; 
                        font-size:12px;">
                    <?php echo __("Description", "ev");?>
                </td>
                
                <td 
                    class="invoiceItemsHeader" 
                    style="width:50px;  font-size:12px; text-align: right; ">
                    <?php echo __("Price", "ev");?>
                </td>

                <?php if ($InvoiceHasAtLeastOneDiscount) {
                    ?>
                        <td 
                        class="invoiceItemsHeader" 
                        style="width:60px;  font-size:12px; text-align: right;">
                        <?php echo __("Discount", "ev");?>
                        </td>
                    <?php 
                } ?>
                 
                <td 
                    class="invoiceItemsHeader" 
                    style="width:75px;  font-size:12px;" align="right">
                    <?php echo __("Amount", "ev");?>
                </td>
                
                
            </tr>

            <tr>
                <td 
                colspan="<?php echo 5 + $InvoiceHasAtLeastOneDiscount;?>" 
                style="border-top: 1px solid; height:0px"></td>
            </tr>


            <?php 
            $totalNet = 0;
            
            foreach ($invoiceData[1] as $invoiceDetail) {
            
                ?>
                <tr>
                    <td 
                        name="invoiceItemPosition" 
                        
                        style="font-size:12px; text-align: right;"> 
                        <?php echo $invoiceDetail->position;?>
                    </td>

                    <td 
                        name="invoiceItemAmount" 
                        
                        style="font-size:12px; text-align: right;"> 
                        <?php echo $invoiceDetail->amount;?>    
                    </td>
                    
                    <td name="invoiceItemDescription">
                        <div 
                            name="itemDescription" 
                            style="
                                font-size: 12px; 
                                width:<?php 
                                    echo $InvoiceDetailDescriptionWidth;
                                ?>px; 
                                word-wrap: break-word;
                                "
                            >

                            <?php echo $invoiceDetail->description;?>
                        </div>
                    </td>

                    <td 
                        name="invoiceItemPrice" 
                        style="text-align: right; font-size:12px;"> 
                        <?php 
                        echo
                        number_format(floatval($invoiceDetail->amount_plan), 2, $decimalDot, $thousandsDot) 
                        . " ".
                        $currencySign;
                        ?>
                    </td> 

                    <?php 
                    if ($InvoiceHasAtLeastOneDiscount) {
                        ?>
                            <td 
                                name="invoiceItemDiscount" 
                                style="text-align: right; font-size:12px;">
                                <?php 
                                
                                if ($invoiceDetail->discount > 0) {
                                    echo number_format(intval($invoiceDetail->discount), 2, $decimalDot, $thousandsDot);
                                    if ($invoiceDetail->discount_type == "discountPercent") {
                                        echo " %";
                                    } else { 
                                        echo " ".$currencySign;
                                    }
                                }
                                ?>
                            </td>
                        <?php 
                    } ?>

                    <td 
                        name="invoiceItemTotal" 
                        
                        style="
                            text-align: right; 
                            font-size:12px; 
                            padding-right:8px;
                            "
                        >

                        <?php 
                            echo
                            number_format(floatval($invoiceDetail->sum), 2, $decimalDot, $thousandsDot). 
                            " ".
                            $currencySign;
                        ?>
                    </td>
            

                </tr>
                <?php 
                $totalNet += $invoiceDetail->sum;
                $taxSums[strval($invoiceDetail->tax)] += intval($invoiceDetail->sum);
                   
            }
            ?>
            

        <?php
        $totalDunningFee = 0;
        if ($invoiceType == "dunning1" ||  $invoiceType == "dunning2" || $invoiceType == "reminder") {
            ?>
            <tr>
                <td align="center" style="font-size:14px;">R</td>

                <td align="center" style="font-size:14px;">1</td>

                <td>
                    <div style="width:371px;font-size:14px; word-wrap: break-word;">
                        <?php echo __("Reminder Fee", "ev");?>
                    </div>
                </td>

                <td style="text-align: center;font-size:14px;">
                    
                    <?php
                        echo get_option('qi_settings')['reminder'] . " ".
                        $currencySign;
                        $totalDunningFee = $totalDunningFee + get_option('qi_settings')['reminder'];
                    ?>
                </td>

                <td style="text-align: center;font-size:14px; display:none"></td>

                <td style="text-align: right;font-size:14px; padding-right:8px;">
                    <?php
                        echo get_option('qi_settings')['reminder'] . " ".
                        $currencySign;
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>

        <?php 
        if ($invoiceType == "dunning1" ||  $invoiceType == "dunning2") {
            ?>
            <tr>
                <td align="center" style="font-size:14px;">D1</td>

                <td align="center" style="font-size:14px;">1</td>

                <td>
                    <div style="width:371px;font-size:14px; word-wrap: break-word;">
                        <?php echo __("First Dunning Fee", "ev");?>
                    </div>
                </td>

                <td style="text-align: center;font-size:14px;">
                    
                    <?php
                        echo get_option('qi_settings')['dunning1'] . " ".
                        $currencySign;
                        $totalDunningFee = $totalDunningFee + get_option('qi_settings')['reminder'];
                    ?>
                </td>

                <td style="text-align: center;font-size:14px; display:none"></td>

                <td style="text-align: right;font-size:14px; padding-right:8px;">
                    <?php
                        echo get_option('qi_settings')['dunning1'] . " ".
                        $currencySign;
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>

        <?php 
        if ($invoiceType == "dunning2") {
            ?>
            <tr>
                <td align="center" style="font-size:14px;">D2</td>

                <td align="center" style="font-size:14px;">1</td>

                <td>
                    <div style="width:371px;font-size:14px; word-wrap: break-word;">
                        <?php echo __("Second Dunning Fee", "ev");?>
                    </div>
                </td>

                <td style="text-align: center;font-size:14px;">
                    
                    <?php
                        echo get_option('qi_settings')['dunning2'] . " ".
                        $currencySign;
                        $totalDunningFee = $totalDunningFee + get_option('qi_settings')['reminder'];
                    ?>
                </td>

                <td style="text-align: center;font-size:14px; display:none"></td>

                <td style="text-align: right;font-size:14px; padding-right:8px;">
                    <?php
                        echo get_option('qi_settings')['dunning2'] . " ".
                        $currencySign;
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
            
        <tr>
            <td 
                colspan="<?php echo 5 + $InvoiceHasAtLeastOneDiscount;?>" 
                style="border-bottom: 1px solid; height:0px">
            </td>
        </tr>
        <tr>
            <td 
                style="font-size:10px;" 
                colspan="<?php echo 4 + $InvoiceHasAtLeastOneDiscount;?>"><?php 
                echo __("Subtotal without Tax", "ev"); ?>
            </td>
            <td style="font-size:10px; text-align:right; padding-right:8px;" >
                <?php echo 
                    number_format($totalNet, 2, $decimalDot, $thousandsDot). 
                    " " . $currencySign;
                ?>
            </td>
        </tr>

        <?php
        if ( $invoiceType == 'reminder' || $invoiceType == 'dunning1' ||$invoiceType == 'dunning2'){
        ?>
            <tr>
                <td 
                    style="font-size:10px;" 
                    colspan="<?php echo 4 + $InvoiceHasAtLeastOneDiscount;?>"><?php 
                    echo __("Dunning Fees", "ev"); ?>
                </td>
                <td style="font-size:10px; text-align:right; padding-right:8px;" >
                    <?php echo 
                        number_format($totalDunningFee, 2, $decimalDot, $thousandsDot). 
                        " " . $currencySign;
                    ?>
                </td>
            </tr>
        <?php
        } ?>
            
        <?php 
        $taxTotal = 0;
        foreach (array_keys($taxSums) as $index=>$key) {
            if (intval($taxSums[$key]) > 0 & $key!="none" ) {
                ?>
                <tr>
                    <td 
                        style="font-size:10px; padding-bottom:0;" 
                        colspan="<?php echo 4 + $InvoiceHasAtLeastOneDiscount;?>"
                    >
                        <?php 
                        echo 
                            $key."% Tax of ".  
                            number_format($taxSums[$key], 2, $decimalDot, $thousandsDot). 
                            " ". $currencySign;?>
                    </td>

                    <td 
                        style="font-size:10px; text-align:right; 
                        padding-right:8px; padding-bottom:0;"> 
                        <?php 
                        $tax = $taxSums[$key] * $key / 100;
                        $taxTotal += $tax;
                        echo number_format($tax, 2, $decimalDot, $thousandsDot)." ".$currencySign;
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>

            <tr>
                <td 
                    colspan="<?php echo 5 + $InvoiceHasAtLeastOneDiscount;?>" 
                    style="border-bottom: 1px solid; height:0px">
                </td>
            </tr>

            <tr>
                <td 
                    style="font-size:14px" 
                    colspan="<?php echo 4 + $InvoiceHasAtLeastOneDiscount;?>"
                >
                    <b>TOTAL</b>
                </td>
                
                <td style="padding-right:8px; font-size:14px;" align="right">
                    <b> 
                        <?php 
                            echo number_format($taxTotal+$totalNet+$totalDunningFee, 2, $decimalDot, $thousandsDot)." ".
                            $currencySign;
                        ?>
                    </b>
                </td>
            </tr>
        </table>

        <br><br><br>
        <?php 
        echo $invoiceTextOutro;
        ?>
         
    </div>
    <page_footer>
        <div class="footer" style=" margin-left:70px; margin-right:70px;">
            <div 
                style="
                    height:4px; 
                    border-top: 1px solid #000000; 
                    margin-top:30px;">
            </div>
            <div style="font-size:12px; text-align: center;"> 
            <?php 
            echo 
                __("Bank Details: ", "ev"). 
                get_option('qi_settings')["bankName{$bankIndex}"].
                ' (BLZ '.$blz. ', Kto '. $kto . ')'.
                '<br>'.
                'IBAN: '. $iban.
                ' - '.
                'BIC: '.  get_option('qi_settings')["BIC{$bankIndex}"].
                '<br>';
            
            if (get_option('qi_settings')['invoiceTextCustomFooter']) {
                echo get_option('qi_settings')['invoiceTextCustomFooter'];
            }

            ?>
            </div>
        </div>
    </page_footer>
    
</page>