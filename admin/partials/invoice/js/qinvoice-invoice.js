/* global q_invoice_ajaxObject, jQuery */
/* eslint no-undef: "error" */

jQuery(function ($) {
  let currencySign = ''
  let lastInvoiceIDtoDelete = 0
  let nonceFieldForSaving = ''
  let nonceFieldForUpdating = ''
  let obj = []
  const inputs = document.querySelectorAll('input, select, textarea')

  //    .........................................................................
  //    .........................................................................
  //    .........##..##..##..##..##...##..#####...######..#####....####..........
  //    .........###.##..##..##..###.###..##..##..##......##..##..##.............
  //    .........##.###..##..##..##.#.##..#####...####....#####....####..........
  //    .........##..##..##..##..##...##..##..##..##......##..##......##.........
  //    .........##..##...####...##...##..#####...######..##..##...####..........
  //    .........................................................................
  //    .........................................................................

  function addPointToThousands (num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
  }

  /**
     *
     * @param {*} num
     * @returns num
     */
  function currencyFormatDE (num) {
    return (
      num
        .toFixed(2)
        .replace('.', ',') // replace decimal point character with ,
      // .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
        .replace(/(\d)(?=(\d{6})+(?!\d))/g, '$1.')
    )
  }

  function recalcTotalSum () {
    const taxes = []
    let netSum = 0
    let taxSum = 0.0
    let totalSum = 0

    Array.from(document.querySelector('select.itemTax').options).forEach(function (optionElement) {
      taxes.push([optionElement.value, 0.0])
    })

    $('.wp-list-table-qInvcLine').each(function () {
      const taxType = parseInt($(this).find('select.itemTax option:selected').val())

      const linePrice = parseFloat($(this).find('.qInvcLine-total').text())

      if (linePrice && taxType) {
        taxes.forEach(function (item) {
          if (item[0] === taxType) {
            item[1] = item[1] + (linePrice * taxType / 100)
          }
        })
      }

      netSum = netSum + linePrice
    })

    $('tr.invoiceTaxSums').remove()

    taxes.forEach(function (item) {
      if (item[1]) {
        taxSum = taxSum + item[1]

        $('table#sums tr.invoiceSums:first').after(
          "<tr id='qInvc-total-mwst" + item[0] + "-summe'" +
                    "class='invoiceTaxSums'" +
                    '>' +

                    "<td class='qInvc-total invoiceSumsLabel'>" +
                    'Tax (' + item[0] + '%)' +
                    '</td>' +

                    "<td class='qInvc-total invoiceSumsAccounts'>" +
                    currencyFormatDE(item[1]) +
                    ' ' +
                    currencySign +
                    '</td>' +
                    '</tr>'
        )
      }
    })
    totalSum = netSum + taxSum

    $('.qInvc-total-summe').eq(0).text(addPointToThousands(currencyFormatDE(netSum)))
    $('.qInvc-total-brutto-summe').eq(0).text(addPointToThousands(currencyFormatDE(totalSum)))
  }

  /**
     * Function recalcLineSum
     */
  function recalcLineSum () {
    let lineTax = 0
    $('.wp-list-table-qInvcLine').each(function () {
      const amountOfItems = parseInt($(this).find('input.amountOfItems').val())
      const itemPrice = parseFloat($(this).find('input.itemPrice').val())
      const discountOnItem = parseFloat($(this).find('input.itemDiscount').val())
      let discountedPrice = 0
      let lineSum = 0
      if (isNaN(amountOfItems) || isNaN(itemPrice)) {
        $(this).find('.qInvcLine-total').text(currencyFormatDE(0))
        return
      }
      discountedPrice = itemPrice
      if (discountOnItem) {
        // $(this).find("input.itemDiscount").removeClass("qInvc-error");
        if ($(this).find('select.discountType').val() === 'discountPercent') {
          if (discountOnItem < 100) {
            discountedPrice = itemPrice - itemPrice * discountOnItem / 100
          }
        } else if ($(this).find('select.discountType').val() === 'discountTotal') {
          if (discountOnItem < itemPrice) {
            discountedPrice = itemPrice - discountOnItem
          }
        }
      }
      $(this).find('input.amountActual').attr('value', discountedPrice)
      lineSum = amountOfItems * discountedPrice
      lineTax = $(this).find('select.itemTax').val()
      $(this).find('input.invoiceTax').attr('value', lineSum * lineTax / 100)
      $(this).find('.qInvcLine-total').text(currencyFormatDE(lineSum))
      $(this).find('input.invoiceTotal').attr('value', lineSum)
      $(this).find('.invoiceItemsTotal nobr').css('display', 'inline')
    })
  }

  // ............................................................................................................................
  // ............................................................................................................................
  // ......##..##...####...######..#####...........######..##..##..######..######..#####...######...####....####...######........
  // ......##..##..##......##......##..##............##....###.##....##....##......##..##..##......##..##..##..##..##............
  // ......##..##...####...####....#####.............##....##.###....##....####....#####...####....######..##......####..........
  // ......##..##......##..##......##..##............##....##..##....##....##......##..##..##......##..##..##..##..##............
  // .......####....####...######..##..##..........######..##..##....##....######..##..##..##......##..##...####...######........
  // ............................................................................................................................
  // ............................................................................................................................

  $(document).keydown(function (e) {
    if (e.keyCode === 27) {
      if ($('.dialogOverlay').css('display') === 'block') {
        $('.dialogOverlay').css('display', 'none')
      } else {
        $('#invoiceOverlay').css('display', 'none')
      }
    }
  })

  $('#invoiceOverlay').click(function (event) {
    if ($(event.target).is('.overlay')) {
      $('#invoiceOverlay').css('display', 'none')
    }
  })

  $('.dialogOverlay').click(function (event) {
    if ($(event.target).is('.overlay')) {
      $('.dialogOverlay').css('display', 'none')
    }
  })

  $('#cancelInvoiceEdit').click(function (event) {
    $('#invoiceOverlay').css('display', 'none')
  })

  function setFilterButtonActive (target) {
    target.css('background-color', 'rgb(34, 113, 177)')
    target.find('button').css('background-color', 'rgb(34, 113, 177)')
    target.css('border', '1px solid #c0c0c0;')
    target.find('button').css('color', 'white')
    target.attr('class', 'filterButton active')
  }

  function setFilterButtonInactive (target) {
    target.css('background-color', 'white')
    target.find('button').css('background-color', 'white')
    target.css('border', '1px solid #c0c0c0;')
    target.find('button').css('color', '#3c434a')
    target.attr('class', 'filterButton inactive')
  }

  $('#filterButtons').on('click', 'div.inactive', function (event) {
    setFilterButtonInactive($('#filterButtons').find('div.active'))
    // console.log($(event.target).parent())
    setFilterButtonActive($(event.target).parent())

    if ($(event.target).parent().attr('id') === 'showOpenInvoices') {
      $('tr.open').css('display', 'table-row')
      $('tr.cancelled').css('display', 'none')
    }

    if ($(event.target).parent().attr('id') === 'showCancelledInvoices') {
      $('tr.cancelled').css('display', 'table-row')
      $('tr.open').css('display', 'none')
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesWithDunning') {
      $('tr.cancelled').css('display', 'none')
      $('tr.open').css('display', 'none')
    }

    if ($(event.target).parent().attr('id') === 'showAllInvoices') {
      $('tr.cancelled').css('display', 'table-row')
      $('tr.open').css('display', 'table-row')
    }
  })

  $('#filterButtons').on('keyup', 'input', function (event) {
    let searchPattern = ''
    // console.log($(this).val().toLowerCase())

    // if (additonalNames[i].toLowerCase().includes($(this).val().toLowerCase(), 0)) {
    // if ($("tr").value()==){$(this).closest("tr").css("display","table-row");}
    searchPattern = $(this).val()

    $('table#tableInvoices tbody').find('tr').each(function (index) {
      // console.log(index)
      // console.log($(this).find('td.columnCompany').text())
      if ($(this).find('td').text().includes(searchPattern.toLowerCase()) && searchPattern) {
        // console.log($(this))
        $(this).css('display', 'table-row')
      } else {
        $(this).css('display', 'none')
      }
    })
  })

  $('#items').on('click', '.discountTypeSwitch', function () {
    if ($(this).val() === 'Euro') {
      $(this).val('Percent')
      $(this).html('%')
    } else if ($(this).val() === 'Percent') {
      $(this).val('Euro')
      $(this).html(currencySign)
    }
  })

  $('#items').on('change', 'input.itemDiscount', function () {
    $(this).val(parseFloat($(this).val()).toFixed(2))
  })

  $('#items').on('change', 'input.itemPrice', function () {
    $(this).val(parseFloat($(this).val()).toFixed(2))
  })

  $('.qInvc-table').eq(0).on('change', 'select.itemTax, select.discountType', function () {
    recalcLineSum()
    recalcTotalSum()
  })

  $('.qInvc-table').eq(0).on('keyup', 'input.amountOfItems, input.itemPrice, input.itemDiscount', function () {
    recalcLineSum()
    recalcTotalSum()
  })

  // Manage Invoice Detail Form Fields

  function recalcPos () {
    $('.wp-list-table-qInvcLine').each(function (index) {
      $(this).find('.qInvc-pos').text(index + 1)
      $(this).find('.invoicePositionHidden').attr('value', index + 1)
    })
  }

  $('#qInvc-add-line').click(function (e) {
    e.preventDefault()

    const Row = $('.wp-list-table-qInvcLine:last-child')
    const Clone = Row.clone()

    Clone.find('input:text').val('')
    Clone.find('input').val('')
    Clone.find('select.itemTax').val('0')
    // Clone.find('span.qInvcLine-total').text('');
    // Clone.find('.invoiceItemsTotal nobr').html('<span class="qInvcLine-total"></span>');
    Clone.find('.invoiceItemsTotal nobr').css('display', 'none')
    Clone.insertAfter(Row)

    recalcPos()
  })

  $('.qInvc-table').eq(0).on('click', '.qInvc-delete-line', function (e) {
    e.preventDefault()

    const parent = $(this).parent().parent()

    if ($('.wp-list-table-qInvcLine').length > 1) {
      parent.remove()
    }

    recalcPos()
    recalcTotalSum()
  })

  $('.qInvc-table').eq(0).sortable({
    items: 'tr.wp-list-table-qInvcLine',
    handle: '.sortHandle',
    stop: function (event, ui) {
      recalcPos()
    }
  })

  function checkIfBanksHaveBeenSetupinSettings () {
    if (checkIfStringIsEmpty($('tr#tableRowBank2 > td.inputsRightTable > input#bank2').val())) {
      $('tr#tableRowBank2').css('display', 'none')
      $('tr#tableRowBank1').css('display', 'none')
    } else {
      $('tr#tableRowBank1').css('display', 'block')
      $('tr#tableRowBank2').css('display', 'block')
    }
  }

  function checkPrefixStatus () {
    if ($('input#prefix').val()) {
      $('input#prefix').prop('readonly', true)
    } else {
      $('input#prefix').prop('readonly', false)
    }
  }

  function checkNoStartStatus () {
    if ($('input#noStart').val()) {
      $('input#noStart').prop('readonly', true)
    } else {
      $('input#noStart').prop('readonly', false)
    }
  }

  function checkIfStringIsEmpty (stringPattern) {
    // console.log(stringPattern)
    return /[^a-zA-Z0-9]/g.test(stringPattern)
  }

  // ...............................................
  // ...............................................
  // ........####...######...####...##..##..........
  // .......##..##......##..##..##...####...........
  // .......######......##..######....##............
  // .......##..##..##..##..##..##...####...........
  // .......##..##...####...##..##..##..##..........
  // ...............................................
  // ...............................................

  function deleteInvoice (invoiceId) {
    jQuery.ajax({
      type: 'POST',

      url: q_invoice_ajaxObject.ajax_url,

      data: {
        action: 'deleteInvoiceServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        id: invoiceId
      },
      success: function (data) {
        // Here we can measure success by own stanards
        // For example a return from a DB uppdate
        // Also maybe here we can call a notice tu the user
        // console.log(data)
      },
      error: function (errorThrown) {
        // console.log(errorThrown)
      }
    })
  }

  function fetchLastInvoiceID () {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,
      data: {
        action: 'fetchLastIDServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce
      },
      success: function (response, textStatus, XMLHttpRequest) {
        // console.log('Fetched the last ID of invocies and it was :')
        // console.log(response)
        $('input#invoice_id').val(response)
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        // console.log('Error : ' + errorThrown)
      }
    })
  }

  function fetchInvoiceCurrency () {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,
      data: {
        action: 'fetchCurrencyServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce
      },
      success: function (response, textStatus, XMLHttpRequest) {
        // console.log(response)
        currencySign = response
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        // console.log(errorThrown)
      }
    })
  }

  $('table#tableInvoices').on('click', '.deleteRow', function (event) {
    $('div#archiveInvoice').css('display', 'block')
    lastInvoiceIDtoDelete = event.target.id
  })

  $('div#archiveInvoice').on('click', '#cancelRemoveInvoice', function () {
    $('div#archiveInvoice').css('display', 'none')
  })

  $('div#archiveInvoice').on('click', '#confirmRemoveInvoice', function (event) {
    $('tr.edit' + '[value=' + lastInvoiceIDtoDelete + ']').attr('class', 'cancelled edit')
    if ($('#showOpenInvoices').hasClass('active')) {
      $('tr.edit' + '[value=' + lastInvoiceIDtoDelete + ']').css('display', 'none')
      deleteInvoice(lastInvoiceIDtoDelete)
    }
    $('div#archiveInvoice').css('display', 'none')
  })

  function writeInvoiceDetailstoFormField (inputName, dataName, position) {
    $('tr.wp-list-table-qInvcLine').eq(position).find(inputName).val(obj[1][position][dataName])
  }

  function writeInvoiceHeadertoFormField (inputName, dataName) {
    $(inputName).val(obj[0][0][dataName])
  }

  function saveInvoiceNonces () {
    nonceFieldForSaving = $('div#saveInvoiceDIV').clone()
    nonceFieldForUpdating = $('div#updateInvoiceDIV').clone()
  }

  $('#newInvoice').click(function () {
    $('#invoiceOverlay').css('display', 'block')
    // reset form
    $('#invoiceForm')[0].reset()
    $('.wp-list-table-qInvcLine:not(:first)').remove()

    // prepare form for Ajax Action "save"
    $('h2#formHeaderEdit').css('display', 'none')
    $('h2#formHeaderCreate').css('display', 'block')
    $('#loc_id').prop('readonly', false)
    $('#updateInvoice').css('display', 'none')
    $('#saveInvoice').css('display', 'inline')
    $("input[name='action']").val('saveInvoiceServerSide')

    // Only the nonce for saving is needed so we empty the DIV and fill it with the saved nonce
    $('div#nonceFields').html('')
    $('div#nonceFields').prepend(nonceFieldForSaving)

    fetchLastInvoiceID()
    fetchInvoiceCurrency()

    recalcPos()
    recalcLineSum()
    recalcTotalSum()
  })

  function editInvoice (invoiceId) {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,
      data: {
        action: 'editInvoiceServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        id: invoiceId
      },
      success: function (response, textStatus, XMLHttpRequest) {
        // console.log(response)

        $('#invoiceOverlay').css('display', 'block')
        obj = JSON.parse(response)

        writeInvoiceHeadertoFormField('#invoice_id', 'id')
        writeInvoiceHeadertoFormField('#prefix ', 'prefix')
        writeInvoiceHeadertoFormField('#company', 'company')
        writeInvoiceHeadertoFormField('#additional', 'additional')
        writeInvoiceHeadertoFormField('#firstname', 'firstname')
        writeInvoiceHeadertoFormField('#lastname', 'lastname')
        writeInvoiceHeadertoFormField('#street', 'street')
        writeInvoiceHeadertoFormField('#zip', 'zip')
        writeInvoiceHeadertoFormField('#city', 'city')
        writeInvoiceHeadertoFormField('#dateOfInvoice', 'invoice_date')
        writeInvoiceHeadertoFormField('#performanceDate', 'delivery_date')
        writeInvoiceHeadertoFormField('#loc_id', 'customerID')

        writeInvoiceDetailstoFormField('input.amountOfItems', 'amount', 0)
        writeInvoiceDetailstoFormField('input.itemDescription', 'description', 0)
        writeInvoiceDetailstoFormField('input.itemPrice', 'amount_plan', 0)
        writeInvoiceDetailstoFormField('input.itemDiscount', 'discount', 0)
        writeInvoiceDetailstoFormField('select.discountType', 'discount_type', 0)
        writeInvoiceDetailstoFormField('select.itemTax', 'tax', 0)

        for (let i = 1; i < obj[1].length; i++) {
          $('tr.wp-list-table-qInvcLine').eq(i - 1).clone().insertAfter($('tr.wp-list-table-qInvcLine').eq(i - 1))
          writeInvoiceDetailstoFormField('input.amountOfItems', 'amount', i)
          writeInvoiceDetailstoFormField('input.itemDescription', 'description', i)
          writeInvoiceDetailstoFormField('input.itemPrice', 'amount_plan', i)
          writeInvoiceDetailstoFormField('input.itemDiscount', 'discount', i)
          writeInvoiceDetailstoFormField('select.discountType', 'discount_type', i)
          writeInvoiceDetailstoFormField('select.itemTax', 'tax', i)
        }

        fetchInvoiceCurrency()
        recalcPos()
        recalcLineSum()
        recalcTotalSum()

        $('input.itemDiscount').each(function () {
          $(this).val(parseFloat($(this).val()).toFixed(2))
        })

        $('input.itemPrice').each(function () {
          $(this).val(parseFloat($(this).val()).toFixed(2))
        })
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        // console.log(errorThrown)
      }
    })
  }

  // Form Handling
  $('table#tableInvoices').on('click', '.edit', function (event) {
    if ($(event.target).is('.download')) { return }
    if ($(event.target).is('.deleteRow')) { return }

    $('#invoiceOverlay').css('display', 'block')

    // reset form
    $('#invoiceForm')[0].reset()
    $('.wp-list-table-qInvcLine:not(:first)').remove()

    // prepare form for Ajax Action "update"
    $('h2#formHeaderEdit').css('display', 'block')
    $('h2#formHeaderCreate').css('display', 'none')
    $('#loc_id').prop('readonly', true)

    $('#updateInvoice').css('display', 'inline')
    $('#saveInvoice').css('display', 'none')
    $("input[name='action']").val('updateInvoiceServerSide')

    // Only the nonce for saving is needed so we empty the DIV and fill it with the saved nonce
    $('div#nonceFields').html('')
    $('div#nonceFields').prepend(nonceFieldForUpdating)

    // Ajax Call for Invoice Data
    currencySign = '€'
    fetchInvoiceCurrency()
    // fetch id from span attribute id="edit-n", where  n = id of invoice
    editInvoice(jQuery(this).attr('id').split('-')[1])
  })

  function changeUpdatedInvoiceRow (invoice) {
    // Find the right row
    let row = ''
    row = $('table#tableInvoices > tbody > tr[value=' + invoice.invoice_id + ']')
    row.attr('class', ' edit open')
    // Change Infos in the right table row.
    row.find('td.columnCompany').text(invoice.company)
    row.find('td.columnName').text(invoice.firstname + ' ' + invoice.lastname)
    row.find('td.columnNet').text($('.qInvc-total-summe').eq(0).text() + ' ' + currencySign)
    row.find('td.columnTotal').text($('.qInvc-total-brutto-summe').eq(0).text() + ' ' + currencySign)

    const date = invoice.dateOfInvoice
    // change to german date format
    const formattedDate = date.slice(8, 10) + '.' + date.slice(5, 7) + '.' + date.slice(0, 4)
    row.find('td.columnDate').text(formattedDate)
    row.attr('class', 'edit open')
  }

  function addNewInvoiceRow (invoice, id) {
    const clone = $('table#tableInvoices > tbody').find('tr').first().clone()
    clone.attr('id', 'edit-' + id)
    clone.attr('value', id)
    clone.find('td.columnRowID').text(1 + parseInt(clone.find('td.columnRowID').text()))
    clone.find('td.columnCompany').text(invoice.company)
    clone.find('span.firstnameSpan').text(invoice.firstname)
    clone.find('span.lastnameSpan').text(invoice.lastname)
    clone.find('td.columnNet').text($('.qInvc-total-summe').eq(0).text() + ' ' + currencySign)
    clone.find('td.columnTotal').text($('.qInvc-total-brutto-summe').eq(0).text() + ' ' + currencySign)

    const date = invoice.dateOfInvoice
    // change to german date format
    const formattedDate = date.slice(8, 10) + '.' + date.slice(5, 7) + '.' + date.slice(0, 4)
    clone.find('td.columnDate').text(formattedDate)

    clone.find('td.columnInvoiceID span').text(id)

    clone.find('a.download').attr('id', 'download-' + id)
    clone.find('a.download').attr('value', id)

    clone.find('a.download').attr('href', '/wp-content/plugins/q_invoice/pdf/Invoice' + id + '.pdf')

    clone.find('span.deleteRow').attr('id', id)
    clone.find('span.deleteRow').attr('value', id)

    $('table#tableInvoices > tbody').prepend(clone)
  }

  jQuery(document).ready(function ($) {
    $('#invoiceForm').ajaxForm({
      success: function (response) {
        // console.log(response)

        const serverResponse = JSON.parse(response).data
        const invoiceID = JSON.parse(response).id

        // console.log(serverResponse)

        if (serverResponse.action === 'updateInvoiceServerSide') {
          changeUpdatedInvoiceRow(serverResponse)
        }
        if (serverResponse.action === 'saveInvoiceServerSide') {
          addNewInvoiceRow(serverResponse, invoiceID)
        }

        $('#invoiceForm').trigger('reset')

        $('#invoiceOverlay').css('display', 'none')

        $('#wpbody-content')
          .prepend('<div class="qinvoiceMessage messageSuccess">' +
                    '<span> Invoice succesfully saved! </span>' +
                    '</div>')

        $('.messageSuccess').delay(5000).fadeOut(800)
      }
    })

    saveInvoiceNonces()

    checkPrefixStatus()

    checkNoStartStatus()

    checkIfBanksHaveBeenSetupinSettings()

    inputs.forEach(input => {
      input.addEventListener(
        'invalid',
        event => {
          input.classList.add('error')
        },
        false
      )
    })

    $('input[type=text], input[type=number], input[type=email], input[type=password]').focus(function (e) {
      $(this).attr('autocomplete', 'new-password')
    })
  })
})
