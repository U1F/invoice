<?php
/**
 * Mail Content
 * 
 * PHP version 5
 * 
 * @category   View
 * @package    QInvoice
 * @subpackage Invoice/admin
 * @author     qanuk.io <support@qanuk.io>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @version    CVS: 
 * @link       qanuk.io
 */
?>

<!-- POPUP DESIGN
    Pops Up when editing a mail template or insert a new mail template. Consists of:
    - Header which says Neuer Eintrag per default (changed by javascript) and shows a button to close the popup (see bsCloseUebersichtPopup)
    -
    - two buttons to save/update and to go back
-->
<div class="overlay" id="qinv_mail-popup" style="left:0;">
	<div class="edit-popup">
		
		<div class="popup-content">
		    <div class="dashicons dashicons-no-alt qinv_close-icon-mod" onClick="qinvCloseMailPopup()"></div>

		    	<!--Form with Textfields-
		    	<form class="uebersicht-form" action="" method="POST">-->

		    		<fieldset id="qinv_mail-info" class="justify-left lbl-pre-w100">

		    			<div class="border-bottom-grey flex-cont">
		    				<label for="qinv_mail-name" id="qinv_mail-name-label" class="form-label-pre form-label-font" style="width:10%;">Name:</label>
		    				<input name="mail-name" id="qinv_mail-name" class="backend-einstellungen-input-style form-input bsCheckableInput bs-ninty100"></input>
		    			</div>

		    			<div class="border-bottom-grey flex-cont">
		    				<label for="qinv_mail-subject" id="qinv_mail-subject-label" class="form-label-pre form-label-font" style="width:10%;">Betreff:</label>
		    				<input name="mail-subject" id="qinv_mail-subject" class="backend-einstellungen-input-style form-input bsCheckableInput bs-ninty100"></input>
		    			</div>

		    			<div class="" style="margin-top:1.4em;">
		    				<?php wp_editor("", $editor_id, $settings);?>
		    			</div>

		    		</fieldset>

		    		<div id="mail-popup-controls">
		    			<button id="qinv_mail-popup-return" class="button button-primary bs-mobile-primary-mod" style="@media screen and (max-width: 1500px){margin:0em 1.4em 0em 1.4em!important}" onClick="bsCloseMailPopup()">Zurück</button>
		    			<button id="qinv_mail-popup-submit" class="button button-primary bs-popup-yes bs-mobile-primary-mod" style="@media screen and (max-width: 1500px){margin:0em 1.4em 0em 1.4em!important}" type="submit" name="submit">Speichern</button>
		    		</div>

		    	<!--</form>-->

		    </div>
	</div>
</div>