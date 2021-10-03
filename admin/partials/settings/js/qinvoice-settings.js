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

  jQuery(document).ready(function ($) {
    const currencySignInput = $('#currencySign').clone()
    $('#currencySign').parent().parent().remove()
    $('select#invoiceCurrency').parent().append(currencySignInput)
  })
})
