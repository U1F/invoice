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
  $('button#showLogoButton').click(function () {
    console.log('mouse was over')
    $('div#popupLogoImage').css('display', 'inline-block')
  })

  $('#qinvoiceSettings').on('click', function (event) {
    if (!(
      $(event.target).is('img') ||
      $(event.target).is('#popupLogoImage') ||
      $(event.target).is('span.dashicons-format-image') ||
      $(event.target).is('button#showLogoButton'))) {
      $('div#popupLogoImage').css('display', 'none')
    }
  })

  jQuery(document).ready(function ($) {
    const currencySignInput = $('#currencySign').clone()
    $('#currencySign').parent().parent().remove()
    $('select#invoiceCurrency').parent().append(currencySignInput)

    if($('#q-invoice-readonly-dummy').text() == "0"){
      $('#prefix').attr('readonly', false);
      $('#noStart').attr('readonly', false);
    }
  })
})
