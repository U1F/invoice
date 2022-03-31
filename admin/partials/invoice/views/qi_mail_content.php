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

<?php
$editor_id = 'templateEditor';
$settings = array('media_buttons' => false, 'wpautop' => false);
?>

<!-- POPUP DESIGN
    Pops Up when editing a mail template or insert a new mail template. Consists of:
    - Header which says Neuer Eintrag per default (changed by javascript) and shows a button to close the popup (see bsCloseUebersichtPopup)
    - two buttons to save/update and to go back
-->
<div id="qinv_mail-popup" class="overlay" style="left: 0;">
	<div id="send-invoice-as-mail" class="edit-popup">

		<!--Form with Textfields-
		<form class="uebersicht-form" action="" method="POST">-->

		<fieldset id="qinv_mail-info" class="">

			<div class="qinvc_mailContentRow">
				<label for="qinv_mail-sender" id="qinv_mail-sender-label" class="qinvc_mailContentLabel" style="width:10%;">Sender:</label>
				<input name="qinv_mail-sender" id="qinv_mail-sender" class="qinvc_mailContentInput"></input>
			</div>

			<div class="qinvc_mailContentRow">
				<label for="qinv_mail-recipient" id="qinv_mail-recipient-label" class="qinvc_mailContentLabel" style="width:10%;">Recipient:</label>
				<input name="qinv_mail-recipient" id="qinv_mail-recipient" class="qinvc_mailContentInput"></input>
			</div>

			<div class="qinvc_mailContentRow">
				<label for="qinv_mail-subject" id="qinv_mail-subject-label" class="qinvc_mailContentLabel" style="width:10%;">Subject:</label>
				<input name="qinv_mail-subject" id="qinv_mail-subject" class="qinvc_mailContentInput"></input>
			</div>

			<div class="qinvc_mailContentRow">
				<label for="qinv_mail-header" id="qinv_mail-header-label" class="qinvc_mailContentLabel" style="width:10%;">Header:</label>
				<input name="qinv_mail-header" id="qinv_mail-header" class="qinvc_mailContentInput"></input>
			</div>

			<div class="" style="margin-top:1.4em;">
				<?php wp_editor("", $editor_id, $settings);?>
			</div>

			<div class="qinvc_mail-attachment-row" style="float:right; margin: 1em 0;">
				<a id="qInvMailAttachmentIcon" class="dashicons dashicons-paperclip" href=""></a>
				<a id="qInvMailAttachmentData" href="">Invoice-PDF</a>
			</div>

		</fieldset>

		<div id="qinv_mail-popup-controls" style="padding: 20px 20px 0 20px;">
			<button
				id="qinv_mail-popup-return"
				class="qInvoiceFormButton cancelButton"
				type="button"
				style="">
				Cancel
			</button>
			<button
				id="qinv_mail-popup-submit"
				class="qInvoiceFormButton submitButton"
				style="float: right;"
				type="button"
				name="submit">
				Senden
			</button>
		</div>

		<!--</form>-->

	</div>
</div>