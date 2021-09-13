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
 * @subpackage QInvoice/admin/partials
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link      https://www.qanuk.io/ 
 * @since      1.0.0
 */


/**
 * Function exportInovice($invoiceID)
 * 
 * @param int $invoiceID 
 * 
 * @return void
 */
function exportInovice($invoiceID)
{
    ?>
<style type="text/css">
.body
{
    background: #FFFFFF;
    border-collapse: collapse;
    font-family: Helvetica, Arial;
    font-size: 14px;
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
    padding: 2mm}

div.niveau
{
    padding-left: 5mm;
}
p.invoiceSender 
{
    margin-bottom: 0px;
    margin-top: 0px;
}

p.receiverAddressAdress 
{
    margin-bottom: 0px;
    margin-top: 0px;
}
td.invoiceInfoHeader
{
    border-top: solid 1px #000000; 
    border-bottom: solid 1px #000000; 
    font-size:14px;
}
td.invoiceItemsHeader
{
    font-size: 14px;
}

</style>
<button id="openWindow" class="openWindow">OPEN WINDOW</button>
</button> 

<page  style="font-size: 12px;">

<div id="modal-content" class="hidden"> YEAH</div>

<div style="position: relative; margin-left:70px;">
<!--<div style="position: relative; margin-left:70px; ">-->
    <table width="100%">
        <tr>
            <td style="font-size:8px; width: 350px; height:40px;"></td>
            <td 
                rowspan="3" 
                style="width: 270px; text-align: right; font-size:12px;"
            >
                <img 
                    src="https://bierbachelor.com/hrsl_500.png" 
                    width="250" 
                    style="border:0px;"
                >
                <br>
                <p class="invoiceSender" id="senderAdditional">Additional</p>
                <p class="invoiceSender" id="senderAddress">Adress</p>
                <p class="invoiceSender" id="senderAddress2">ZIP City</p>
                <p class="invoiceSender" id="senderPlaceholder1"></p>
                <p class="invoiceSender" id="senderEmail">email</p>
                <p class="invoiceSender" id="senderWebsite">website</p>                 
                <p class="invoiceSender" id="ZAHLUNGSERINNERUNG">ZAHLUNGSERINNERUNG</p>
                        
            </td>
        </tr>
        <tr>
            <td id="smallAdress" style="font-size:9px; vertical-align: bottom;"><u>
                <?php echo "Zusatz | Adresse | PLZ Ort";?></u></td>
        </tr>
        <tr>
            <td id="invoicereceiverAddressAdress" 
            rowspan="3" style="vertical-align: top; font-size:14px;">
            <p class="receiverAddressAdress" id=receiverAddressCompany>Company</p>
            <p class="receiverAddressAdress" id=receiverAddressLocNar>location_nar</p>
            <p class="receiverAddressAdress" id=receiverAddressName>First Name</p>
            <p class="receiverAddressAdress" id=receiverAddressAdress>Street No</p>
            <p class="receiverAddressAdress" id=receiverAddressAdress2>ZIP City</p>
    
        </tr>
        <tr>
            <td><br><br></td>
        </tr>
    </table>


    <div id="invoiceHeader" style="height:100px; vertical-align: bottom; font-size: 40px;">
        <p style="font-size: 24px;"><b> Rechnung/Gutschrift/Zahlungserinnerung/Mahnung</b> </p>
    </div>

    <div id=invoiceText" style="font-size: 14px; height: 40px; vertical-align: middle">
        <p class="invoiceText" id="invoiceTextRegular" style="display:inline"> 
            Folgende Leistung stellen wir Ihnen in Rechnung: 
        </p>
        
        <p class="invoiceText" id="invoiceTextCredt" style="display:none"> 
            Folgende Leistung schreiben wir Ihnen gut.
        </p>
        
        <p class="invoiceText" id="invoiceTextDunning1" style="display:none"> 
            Wahrscheinlich ist unsere Rechnung untergegangen - daher möchten wir noch einmal um <br>eine erneute Prüfung bitten.
        </p>
        
        <p class="invoiceText" id="invoiceTextDunning2" style="display:none"> 
            Wir bitten folgende Leistung unverzüglich zu begleichen.<br>(auch im Zusammenhang mit dem nächsten gemeinsamen Event)
        </p>
    </div>
    

    <table class="table_rg_head" style="border-collapse: collapse;">

    <!-- Wenn es noch kein Leistungsdatum gibt, soll die Spalte dafür leer sein und die Tabelle entsprechend angepasst-->
    <!-- Die Breite liegt dann bei 127 statt 180, 3 statt 4 (colspan)Spalten -->
    
        <tr id="lineBefore">
            <td colspan="4" style="border-bottom: solid 1px #000000;"></td>
            <!-- colspan to 3 if no service date has been set -->
        </tr>

        <tr>
            <td class="invoiceInfoHeader" width="127">
                <b>INVOICE ID</b><br>
                <span id="invoiceID"><?php echo $invoiceID;?><span>
            </td>

            <td class="invoiceInfoHeader" width="127">
                <b>CUSTOMER ID</b><br> 
                <span id="customerID">99</span>
            </td>
            
            <td class="invoiceInfoHeader" width="127">
                <b>ORDER DATE</b><br> 
                <span id="orderDate">1.1.2000</span>
            </td>
            
            <td class="invoiceInfoHeader" width="127">
                <b>SERVICEDATE</b><br>
                <span id="serviceDate">1.1.2000</span>
            </td>
            
        </tr>

        <tr>
            <td>
                <br>
                <br>
            </td>
        </tr>

    </table>


    <?php  
    /*
    if ($rabattVorhanden) {$rabatt_spalte = 1; $beschreibung_breite = "295"; $beschreibung_breite_bw = "261"; } 
    else {$rabatt_spalte = 0; $beschreibung_breite = "371"; $beschreibung_breite_bw = "369";} 
    */
    ?>
    
    <table width="100%" class="table_rg_content">
        <thead>
            <td 
                class="invoiceItemsHeader" 
                style="width:15px;  font-size:14px;">
                <?php echo __("#", "ev");?>
            </td>

            <td 
                class="invoiceItemsHeader" 
                style="width:35px;  font-size:14px;">
                <?php echo __("Amount", "ev");?>
            </td>
            
            <td 
                class="invoiceItemsHeader" 
                style="width:371px; font-size:14px;">
                <?php echo __("Description", "ev");?>
            </td>
            
            <td 
                class="invoiceItemsHeader" 
                style="width:50px;  font-size:14px; text-align:center; ">
                <?php echo __("Price", "ev");?>
            </td>
            
            <td 
                class="invoiceItemsHeader" 
                style="width:60px;  font-size:14px; text-align:center; display:none">
                <?php echo __("Discount", "ev");?>
            </td>  
            
            <td 
                class="invoiceItemsHeader" 
                style="width:75px;  font-size:14px;" align="right">
                <?php echo __("Total'", "ev");?>
            </td>
        </thead>

        <tr>
            <td colspan="5" style="border-top: 1px solid; height:0px"></td>
            <!-- colspan +1 wenn Rabatt-->
        </tr>

        
        <tr>
            <td 
                name="invoiceItemPosition" 
                align="center" 
                style="font-size:14px;"> 
                1 
            </td>

            <td 
                name="invoiceItemAmount" 
                align="center" 
                style="font-size:14px;"> 
                99 </td>
            
            <td name="invoiceItemDescription">
                <div 
                    name="itemDescription" 
                    style="font-size: 14px; width:369px; word-wrap: break-word;">
                </div>
            </td>

            <td 
                name="invoiceItemDescription" 
                style="text-align: right; font-size:14px;"> 
            </td> 

            <td 
                name="invoiceItemDiscount" 
                style="text-align: right; font-size:14px; display:none">
            </td>
            
            <td 
                name="invoiceItemTotal" 
                style="text-align: right; font-size:14px; padding-right:8px;">
                
            </td>
        

        </tr>


        <tr>
            <td align="center" style="font-size:14px;">9</td>

            <td align="center" style="font-size:14px;">1</td>

            <td>
                <div style="width:371px;font-size:14px; word-wrap: break-word;">Mahngebühr</div>
            </td>

            <td style="text-align: center;font-size:14px;">8 €</td>
            
            <td style="text-align: center;font-size:14px; display:none"> </td>
            
            <td style="text-align: right;font-size:14px; padding-right:8px;">8 €</td>
        </tr>	
    

        
        <tr>
            <td colspan="5" style="border-bottom: 1px solid; height:0px"></td>
        </tr>

        <tr>
            <td style="font-size:10px;" colspan="4">Netto</td>
            <td style="font-size:10px; text-align:right; padding-right:8px;" > 100 €</td>
        </tr>
        
        
        <tr>
            <td style="font-size:10px; padding-bottom:0;" colspan="4">19% Mehrwertsteuer aus summe19 € netto</td>
            <td style="font-size:10px; text-align:right; padding-right:8px; padding-bottom:0;"> tax19 €</td>
        </tr>
        
        
        
        <tr>
            <td colspan="5" style="border-bottom: 1px solid; height:0px"></td>
        </tr>
        <tr>
            <td style="font-size:14px" colspan="4"><b>GESAMTBETRAG</b></td>
            
            <td style="padding-right:8px; font-size:14px;" align="right">
                <b> 199 €</b>
            </td>
        </tr>
    </table>

    <br><br><br>
    
    <div style="font-size:12px">
        Danke für die gute Zusammenarbeit!<br>
        <br>Zahlungsziel: 10 Tage ohne Abzug.</div>
    <div style="font-size:12px; display:none">
        Sollten Sie den offenen Betrag bereits beglichen haben, 
        betrachten Sie dieses Schreiben als gegenstandslos.</div>
    </div>

    <page_footer>
        <div class="footer" style=" margin-left:70px; margin-right:70px;">
            <div style="height:4px; border-top: 1px solid #000000; margin-top:30px;"></div>
                <div style="font-size:12px; text-align: center;"> 
                <?php echo 
                    __("Bank Details: ", "ev"). 
                    get_option('qi_settings')['bankName1'].' '.
                    
                    'IBAN: '. get_option('qi_settings')['IBAN1']. ' - '.
                    
                    'BIC: '.  get_option('qi_settings')['BIC1']. '<br>'.
                    
                    'Steuernummer: STNR- USt-IdNr.: ID';
                ?>
                </div>
        </div>
    </page_footer>
</page>
    <?php
}
exportInovice(81, "invoice");