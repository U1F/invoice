/* global jQuery */
/* eslint no-undef: "error" */
jQuery(function ($) {

  /**  _        _
   *  | |      | |        
   *  | |_ __ _| |__  ___ 
   *  | __/ _` | '_ \/ __|
   *  | || (_| | |_) \__ \
   *   \__\__,_|_.__/|___/ 
   * 
   * The color effect function for the filter buttons is saved in qinvoice-invoice.js                                               
   */

  $('.filterButtons').on('click', 'div.inactive', function (event) {
    if ($(event.target).parent().attr('id') === 'filterButtons') {
      return;
    }
    //setFilterButtonInactive($('.filterButtons').find('div.active'))
    //setFilterButtonActive($(event.target).parent())

    //hide all Setting Rows
    $('.invoiceSettingsRow').removeClass('activeSetting')

    //show only the clicked setting row
    if ($(event.target).parent().attr('id') === 'showCompanySettings') {
      $('#companySettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showBankSettings') {
      $('#bankSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showMailSettings') {
      $('#mailSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showInvoiceSettings') {
      $('#invoiceSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showDunningSettings') {
      $('#dunningSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showOfferSettings') {
      $('#offerSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showCreditSettings') {
      $('#creditSettingsTable').addClass('activeSetting')
    }
  })

  //on load reset the mobile menu to default position
  $('.mobileFilterButtonsOption[value=company]').prop('selected', true)

  // Manage UI visibility of filter buttons mobile version
  $('#settingsMobileFilterButtonsDropdown').on('change', function (event) {
    //hide all Setting Rows
    $('.invoiceSettingsRow').removeClass('activeSetting')
    //display the selected Setting Row
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'company') {
      $('#companySettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'bank') {
      $('#bankSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'mail') {
      $('#mailSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'invoice') {
      $('#invoiceSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'dunning') {
      $('#dunningSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'offer') {
      $('#offerSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'credit') {
      $('#creditSettingsTable').addClass('activeSetting')
    }
    $('#settingsMobileFilterButtonsDropdown').blur();
  });

  $('textarea').each(function () {
    this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;')
  }).on('input', function () {
    this.style.height = 'auto'
    this.style.height = (this.scrollHeight) + 'px'
  })

  $('select#invoiceCurrency').on('change', function (event) {
    if (this.value === 'Other') {
      $('#currencySign').css('display', 'inline-block')
      $('#currencySign').focus()
      $('#currencySign').select()
    } else {
      $('#currencySign').css('display', 'none')
    }
  })
  /*$('button#showLogoButton').click(function () {
    console.log('mouse was over')
    $('div#popupLogoImage').css('display', 'inline-block')
  })*/

  /*$('#qinvoiceSettings').on('click', function (event) {
    if (!(
      $(event.target).is('img') ||
      $(event.target).is('#popupLogoImage') ||
      $(event.target).is('span.dashicons-format-image') ||
      $(event.target).is('button#showLogoButton'))) {
      $('div#popupLogoImage').css('display', 'none')
    }
  })*/

  //functions to delete the logo and to open the confirmation alert
  $('#qinv_settings_delete_logo').on('click', function (event){
    $('div#qinv_settings_deleteLogoOverlay').css('display', 'block');
  })
  $('#cancelRemoveLogo').on('click', function(event){
    $('div#qinv_settings_deleteLogoOverlay').css('display', 'none');
  })
  $('#confirmRemoveLogo').on('click', function(event){
    //$('#showLogoDiv').css('display', 'none');
    //$('#qinv_settings_uploadLogo').css('display', 'block');
    $('div#qinv_settings_deleteLogoOverlay').css('display', 'none');
    qinv_settings_removeLogoFile();
  })
  /*$('#qinv_settings_uploadLogo').on('click', function(event){
    $('#qinv_settings_logo_message').css('display', 'block');
  })*/

  $('#logoFile').on('change', function(e){
    $('.submit #saveSettings').click();
  })

  jQuery(document).ready(function ($) {
    const currencySignInput = $('#currencySign').clone()
    $('#currencySign').parent().parent().remove()
    $('select#invoiceCurrency').parent().append(currencySignInput)
    if ($('select#invoiceCurrency').val() === 'Other') {
      $('#currencySign').css('display', 'inline-block')
    }

    if ($('#q-invoice-readonly-dummy').text() == "0") {
      $('#prefix').attr('readonly', false)
      $('#noStart').attr('readonly', false)
    } else {
      $('#prefix').attr('readonly', true)
      $('#noStart').attr('readonly', true)
    }
  })

  function qinv_settings_removeLogoFile(){
    jQuery.ajax({
      type: 'POST',

      url: q_invoice_ajaxObject.ajax_url,

      data: {
        action: 'removeLogoServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce
      },
      success: function (data) {
        console.log('Removal Status: ' + data)
        $('.submit #saveSettings').click();
      },
      error: function (errorThrown) {
        console.log('Logo not removed')
        console.log(errorThrown)
      }
    })
  }
})