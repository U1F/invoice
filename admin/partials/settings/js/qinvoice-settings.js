/* global jQuery */
/* eslint no-undef: "error" */
jQuery(function ($) {
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