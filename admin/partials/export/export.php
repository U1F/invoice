<?php
/**
 * Short
 *
 * Description
 * 
 * PHP version 5
 * 
 * @category   Export
 * @package    QInvoice
 * @subpackage Q_Invoice/admin/partials
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       https://www.qanuk.io/ 
 * @since      1.0.0
 */

/**
 * Function exportInovice($invoiceID)
 * 
 * @param int    $invoiceID   1
 * @param string $invoiceType 1
 * 
 * @return void
 */
function exportInovice($invoiceID, $invoiceType)
{
    $invoiceData = Interface_Invoices::getInvoiceData($invoiceID);

    $currencySign = "€";
    if (get_option('qi_settings')['invoiceCurrency'] == "Euro") {
        $currencySign = "€";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Dollar") {
        $currencySign = "$";
    } else if (get_option('qi_settings')['invoiceCurrency'] == "Other") {
        $currencySign = get_option('qi_settings')['currencySign'];
    }

    $taxSums=array();
    $numberOfTaxTypes = intval(get_option('qi_settings')['taxTypes']);
    for ($i=0;$i<$numberOfTaxTypes;$i++) {

        $taxSums[get_option('qi_settings')['tax'.strval($i+1)]] = 0;
        

    }
    $taxSums['none'] = 0;
    
    


    $separator =","
    
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
        <table width="100%">
            <tr>
                <td style="font-size:8px; width: 350px; height:40px;"></td>
                
                <td 
                    rowspan="3" 
                    style="width: 270px; text-align: right; font-size:12px;"
                >
                <?php 
                $logoImageSource = plugin_dir_url(__FILE__).
                "files/none_5002.png";
                
                if (get_option('qi_settings')['logoFileUrl']) {
                    $logoImageSource = get_option('qi_settings')['logoFileUrl'];
                }
                ?>
                
                <img 
                    src="<?php echo $logoImageSource;?>" 
                    width="250" 
                    style="border:0px;">
                        
                    <br>

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
                    
                    <p class="invoiceSender" id="senderEmail">
                        <?php echo get_option('qi_settings')['mail'];?>
                    </p>
                    
                    <p class="invoiceSender" id="senderWebsite">
                        <?php echo get_option('qi_settings')['website'];?>
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

            <tr>
                <td 
                    id="small" 
                    style="font-size:9px; vertical-align: bottom;">
                    <u>
                    <?php echo  
                    
                        get_option('qi_settings')['firstName']. " ". 
                        get_option('qi_settings')['lastName']. " | ". 
                        get_option('qi_settings')['street']. " | ".  
                        get_option('qi_settings')['ZIP'] . " ". 
                        get_option('qi_settings')['city']; 
                    ?>
                    </u>
                </td>
            </tr>
            <tr>
                <td 
                    id="invoicereceiverAddress" 
                    rowspan="3" 
                    style="vertical-align: top; font-size:14px;"
                >
                    <p class="receiverAddressAddress" id=receiverAddressCompany>
                        <?php echo $invoiceData[0][0]->company; ?>
                    </p>

                    <p class="receiverAddress" id=receiverAddressLocNar>
                        <?php echo $invoiceData[0][0]->additional; ?>
                    </p>

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
        <br><br>

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
            style="font-size: 14px; height: 40px; vertical-align: middle"
        >
            <p class="invoiceText" id="invoiceTextRegular" style="display:inline"> 
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
            $deliveryDate['day'], 
            $deliveryDate['month'], 
            $deliveryDate['year']
        )
        ) {
            //DEBUG echo "Date ".$invoiceData[0][0]->delivery_date." is ok";
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
                        echo $invoiceData[0][0]->customerID;?>
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
                    <?php echo __("Anzahl", "ev");?>
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
            $detailPosition= 0;
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
                        number_format($invoiceDetail->amount_plan, 2, $separator, '') 
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
                                    echo number_format(intval($invoiceDetail->discount), 2, $separator, '');
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
                            number_format($invoiceDetail->sum, 2, $separator, ''). 
                            " ".
                            $currencySign;
                        ?>
                    </td>
            

                </tr>
                <?php 
                $totalNet += $invoiceDetail->sum;
                $taxSums[strval($invoiceDetail->tax)] += $invoiceDetail->sum;
                
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
                        number_format($totalNet, 2, $separator, ''). 
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
                            number_format($taxSums[$key], 2, $separator, ''). 
                            " ". $currencySign," netto";?>
                    </td>

                    <td 
                        style="font-size:10px; text-align:right; 
                        padding-right:8px; padding-bottom:0;"> 
                        <?php 
                        $tax = $taxSums[$key] * $key / 100;
                        $taxTotal += $tax;
                        echo number_format($tax, 2, $separator, '')." ".$currencySign;
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
                            echo number_format($taxTotal+$totalNet, 2, $separator, '')." ".
                            $currencySign;
                        ?>
                    </b>
                </td>
            </tr>
        </table>

        <br><br><br>
        <?php if ($invoiceType=="invoice") {
            if (get_option('qi_settings')['invoiceTextOutro']) {
                echo '<div style="font-size:12px">';
                echo get_option('qi_settings')['invoiceTextOutro'];
                echo '</div>';
            } else {
                ?>
                <div style="font-size:12px">
                Danke für die gute Zusammenarbeit!<br>
                <br>Zahlungsziel: 10 Tage ohne Abzug.
                </div>
                <?php
            }
        }
        
        if ($invoiceType=="dunning") {
            ?>

        <div style="font-size:12px; display:none;">
            Sollten Sie den offenen Betrag bereits beglichen haben, 
            betrachten Sie dieses Schreiben als gegenstandslos.
        </div>
            <?php
        }
        ?>
        

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
                    __("Bankverbindung: ", "ev"). 
                    get_option('qi_settings')['bankName1'].' '.
                    'IBAN: '. get_option('qi_settings')['IBAN1']. ' - '.
                    'BIC: '.  get_option('qi_settings')['BIC1']. '<br>';
                    
                if (get_option('qi_settings')['customFooter']) {
                    echo get_option('qi_settings')['customFooter'];
                }
    
                ?>
                </div>
            </div>
        </page_footer>
    </div>
</page>
    <?php
}
