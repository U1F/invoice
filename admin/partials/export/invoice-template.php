<?php
/**
 * This file contains the template for the auto generated invoices.
 * +invoice, +rechnung, +pdf
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
                <?php 
                
                    $logoImageSource = $_SERVER['DOCUMENT_ROOT']. 
                        dirname($_SERVER['PHP_SELF']). 
                        "files/none_5002.png";
                    
                    if (get_option('qi_settings')['logoFileUrl']) {
                        $logoImageURL = get_option('qi_settings')['logoFileUrl'];
						$logoImageFile = get_option('qi_settings')['logoFileFile'];
                    }
                    $mimetype =  wp_get_image_mime( $logoImageFile );
                    $imagedata = file_get_contents($logoImageFile);
             		$base64 = base64_encode($imagedata);
					$logoImageSource = 'data:'. $mimetype .';base64,'.$base64;
					
                ?>
                <img 
                    src="<?php echo $logoImageSource;?>" 
                    width="250"
                    style="border:0px;">

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
                if ($invoiceType=="dunning") {
                    ?>
                        <p class="invoiceSender" id="ZAHLUNGSERINNERUNG">
                            ZAHLUNGSERINNERUNG
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
                    <?php 
                    if (get_option('qi_settings')['company']) {
                        echo get_option('qi_settings')['company'] . " | " ;
                    } else {
                        echo get_option('qi_settings')['firstName']. " "; 
                        echo get_option('qi_settings')['lastName']. " | ";
                    }

                    echo   
                        get_option('qi_settings')['street']. " | ".  
                        get_option('qi_settings')['ZIP'] . " ". 
                        get_option('qi_settings')['city']; 
                    ?>
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
            <?php 
            $heading=__("Rechnung", "ev");
            $invoiceText = get_option('qi_settings')['invoiceTextIntro'];
            if ($invoiceType =="invoice") {
                $heading=__("Rechnung", "ev");
                if (get_option('qi_settings')['invoiceTextIntro']) {
                    $invoiceText = get_option('qi_settings')['invoiceTextIntro'];   
                } else {
                    $invoiceText = "Folgende Leistung stellen wir Ihnen in Rechnung:";
                }
                
            } else if ($invoiceType =="credit") {
                $heading=__("Gutschrift", "ev");
                $invoiceText ="Folgende Leistung schreiben wir Ihnen gut.";
            } else if ($invoiceType =="dunning1") {
                $heading=__("Payment Reminder", "ev");
                $invoiceText ="Wahrscheinlich ist unsere Rechnung untergegangen".
                " - daher möchten wir noch einmal um <br>".
                "eine erneute Prüfung bitten.";
            } else if ($invoiceType =="dunning2") {
                $heading=__("Dunning", "ev");
                $invoiceText ="Wir bitten folgende Leistung ".
                "unverzüglich zu begleichen.".
                "<br>(auch im Zusammenhang mit dem nächsten gemeinsamen Event)";
            }
            echo '<p style="font-size: 24px;"><b>'.$heading.'</b> </p>';
            ?>
            
        </div>

        <div 
            id=invoiceText" 
            style="font-size: 14px; height: 40px; vertical-align: middle; width: 640px;"
        >
            <p class="invoiceText" id="invoiceTextRegular" style="display:inline;"> 
                <?php echo $invoiceText;?>
            </p>
        </div>


        <?php 
        $deliveryDate = date_parse_from_format(
            "Y-m-d", 
            $invoiceData[0][0]->delivery_date
        );
        
        $deliveryDateIsSet = 0;
        $tableWidthOfInvoiceHead = 180;
        
        if (checkdate(
            $deliveryDate['month'],
            $deliveryDate['day'], 
            $deliveryDate['year']
        )
        ) {
            
            $deliveryDateIsSet = 1;
            $tableWidthOfInvoiceHead = 127;
        }
        ?>

        <table 
            class="table_rg_head" 
            style="border-collapse: collapse; padding: 8px;"
        >

        <!-- Wenn es noch kein Leistungsdatum gibt, soll die Spalte dafür 
        leer sein und die Tabelle entsprechend angepasst-->
        <!-- Die Breite liegt dann bei 127 statt 180, 3 statt 4 (colspan)Spalten -->
        
            <tr id="lineBefore">
                <td 
                    style="border-bottom: solid 1px #000000;" 
                    colspan="<?php echo 3+$deliveryDateIsSet;?>"
                >
                </td>
            </tr>

            <tr>
                <td 
                    class="invoiceInfoHeader" 
                    width="<?php echo $tableWidthOfInvoiceHead;?>"
                >
                    <b><?php echo __("Rechnungsnr.", "ev");?></b><br>
                    <span id="invoiceID"><?php echo $invoiceID;?></span>
                </td>

                <td 
                    class="invoiceInfoHeader" 
                    width="<?php echo $tableWidthOfInvoiceHead;?>"
                >
                    <b><?php echo __("Kundennr.", "ev");?></b><br> 
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
                    <b><?php echo __("Rechnungsdatum", "ev");?></b>
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

                        <b><?php echo __("Leistungsdatum", "ev");?></b><br>
                        
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
        <!-- -------------------------------------------------- -->
        <?php 
        $sumOfInvoiceDiscounts = 0;
        $InvoiceHasAtLeastOneDiscount = false;
        $invoiceDetailDescriptionWidthHeader = "371";  
        $InvoiceDetailDescriptionWidth = "369";
        
        foreach ($invoiceData[1] as $invoiceDetail) {
            $sumOfInvoiceDiscounts += intval($invoiceDetail->discount);
        }
        
        if ($sumOfInvoiceDiscounts > 0) {
            $InvoiceHasAtLeastOneDiscount = true;
            $invoiceDetailDescriptionWidthHeader = "295";  
            $InvoiceDetailDescriptionWidth = "261";  
            
        }
        
        ?>
        
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
                    <?php 
                    if (get_option('qi_settings')['invoiceUnit'] == "Amount") {
                        echo __("Anzahl", "ev");
                    }
                    if (get_option('qi_settings')['invoiceUnit'] == "Hours") {
                        echo __("Stunden", "ev");
                    }
                    if (get_option('qi_settings')['invoiceUnit'] == "Liter") {
                        echo __("Liter", "ev");
                    }
                    ?>

                </td>
                
                <td 
                    class="invoiceItemsHeader" 
                    style="
                        width:<?php echo $invoiceDetailDescriptionWidthHeader;?>px; 
                        font-size:12px;">
                    <?php echo __("Beschreibung", "ev");?>
                </td>
                
                <td 
                    class="invoiceItemsHeader" 
                    style="width:50px;  font-size:12px; text-align: right; ">
                    <?php echo __("Preis", "ev");?>
                </td>

                <?php if ($InvoiceHasAtLeastOneDiscount) {
                    ?>
                        <td 
                        class="invoiceItemsHeader" 
                        style="width:60px;  font-size:12px; text-align: right;">
                        <?php echo __("Rabatt", "ev");?>
                        </td>
                    <?php 
                } ?>
                 
                <td 
                    class="invoiceItemsHeader" 
                    style="width:75px;  font-size:12px;" align="right">
                    <?php echo __("Summe", "ev");?>
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
                        number_format(floatval($invoiceDetail->amount_plan), 2, $separator, '.') 
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
                                    echo number_format(intval($invoiceDetail->discount), 2, $separator, '.');
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
                            number_format(floatval($invoiceDetail->sum), 2, $separator, '.'). 
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
        if ($invoiceType == "dunning2") {
            ?>
            <tr>
                <td align="center" style="font-size:14px;">9</td>

                <td align="center" style="font-size:14px;">1</td>

                <td>
                    <div style="width:371px;font-size:14px; word-wrap: break-word;">
                        <?php echo __("Mahngebühr", "ev");?>
                    </div>
                </td>

                <td style="text-align: center;font-size:14px;">
                    
                    <?php
                        echo get_option('qi_settings')['dunning2'] . " ".
                        $currencySign;
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
                    echo __("Netto", "ev"); ?>
                </td>
                <td style="font-size:10px; text-align:right; padding-right:8px;" >
                    <?php echo 
                        number_format($totalNet, 2, $separator, '.'). 
                        " " . $currencySign;
                    ?>
                </td>
            </tr>
            
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
                            $key."% Mehrwertsteuer aus ".  
                            number_format($taxSums[$key], 2, $separator, '.'). 
                            " ". $currencySign,"&nbsp;netto";?>
                    </td>

                    <td 
                        style="font-size:10px; text-align:right; 
                        padding-right:8px; padding-bottom:0;"> 
                        <?php 
                        $tax = $taxSums[$key] * $key / 100;
                        $taxTotal += $tax;
                        echo number_format($tax, 2, $separator, '.')." ".$currencySign;
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
                    <b>GESAMTBETRAG</b>
                </td>
                
                <td style="padding-right:8px; font-size:14px;" align="right">
                    <b> 
                        <?php 
                            echo number_format($taxTotal+$totalNet, 2, $separator, '.')." ".
                            $currencySign;
                        ?>
                    </b>
                </td>
            </tr>
        </table>

        <br><br><br>
        <?php if ($invoiceType=="invoice") {
            if (get_option('qi_settings')['invoiceTextOutro']) {
                echo '<div style="font-size:14px;  width: 640px;">';
                echo get_option('qi_settings')['invoiceTextOutro'];
                echo '<br>'. get_option('qi_settings')['invoiceTextPaymentDeadline'];
                echo '</div>';
            } else {
                ?>
                <div style="font-size:14px;  width: 640px;">
                Danke für die gute Zusammenarbeit!<br>
                <br>
                <?php echo get_option('qi_settings')['invoiceTextPaymentDeadline']; ?> 
                </div>
                <?php
            }
        }
        
        if ($invoiceType=="dunning") {
            ?>

        <div style="font-size:12px; display:none; width: 640px;">
            Sollten Sie den offenen Betrag bereits beglichen haben, 
            betrachten Sie dieses Schreiben als gegenstandslos.
        </div>
            <?php
        }
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
                
                $bankIndex = $invoiceData[0][0]->bank;
                
                $ibanFromDatabase = get_option('qi_settings')["IBAN{$bankIndex}"];

                // Get rid of spaces if exist
                $ibanArray = explode(' ', $ibanFromDatabase);
                $iban = implode('' , $ibanArray);

                // Seperate blz and kto
                $blz = substr($iban, 4, 8);
                $kto = strVal(intVal(substr($iban, 12, 10))); 

                //Fill with spaces if not exist for IBAN in second line
                $ibanArray = str_split($iban);
                $iban = '';
                $counter = 0;
                for($i = 0; $i < sizeof($ibanArray); $i++){
                    $iban = $iban.$ibanArray[$i];
                    if($counter == 3){
                        $iban = $iban.' ';
                        $counter = 0;
                    } else{
                        $counter++;
                    }

                }

                echo 
                    __("Bankverbindung: ", "ev"). 
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