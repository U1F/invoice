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
    // Generate an associative Array with the tax values as indexes
    Array.from(document.querySelector('select.itemTax').options).forEach(function (optionElement) {
      taxes.push([parseInt(optionElement.value), 0.0])
    })

    // For each line in the form do:
    $('.wp-list-table-qInvcLine').each(function () {
      // Get the selected tax type
      const taxType = parseInt($(this).find('select.itemTax option:selected').val())
      // Get the computed product of discounted items
      const linePrice = parseFloat($(this).find('.qInvcLine-total').text())

      // Save the Sum of taxes for each tax type and the sum of all taxTypes as well as the sum without taxation
      if ($.isNumeric(linePrice) && $.isNumeric(taxType)) {
        taxes.forEach(function (item) {
          if ($.isNumeric(item[0]) && item[0] === taxType) {
            item[1] = item[1] + (linePrice * taxType / 100)
          }
        })
      }
      netSum = netSum + linePrice
    })
    // Remove obsolete tax lines
    $('tr.invoiceTaxSums').remove()
    // add a Tax line for each tax type
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
    // Write formatted Sums to the form
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

  function markInvoice (invoiceID, data) {
    updateInvoiceHeaderItem(invoiceID, data)
  }

  function unmarkInvoice (invoiceID, data) {
    updateInvoiceHeaderItem(invoiceID, data)
  }

  $('.columnStatusPaid').on('click', '.sliderForPayment', function (event) {
    // IS ARCHIVED OR PAID MORE IMPORTANT OR SAME TIME
    const sliderBox = $(event.target).parent()
    const invoiceRow = sliderBox.parent().parent()

    if (!sliderBox.find('input').prop('checked')) {
      const data = { paydate: formatDate(new Date()) }
      markInvoice(getRowNumber(event.target), data)

      invoiceRow.find('.invoiceStatusIcon').addClass('paid')
      invoiceRow.find('.invoiceStatusIcon').removeClass('open')
      invoiceRow.addClass('paid')
      invoiceRow.removeClass('open')
      invoiceRow.find('.columnEdit').find('.delete').css('color', 'lightgrey')
      invoiceRow.find('.columnEdit').find('.delete').removeClass('deleteRow')
    } else {
      const data = { paydate: '' }
      unmarkInvoice(getRowNumber(event.target), data)

      invoiceRow.find('.invoiceStatusIcon').removeClass('paid')
      invoiceRow.find('.invoiceStatusIcon').addClass('open')
      invoiceRow.removeClass('paid')
      invoiceRow.addClass('open')
      invoiceRow.find('.columnEdit').find('.delete').css('color', 'black')
      invoiceRow.find('.columnEdit').find('.delete').addClass('deleteRow')
    }
  })

  $('.columnStatusPaid').on('click', '.markAsPaid', function (event) {
    $(event.target).closest('tr').find('.sliderForPayment').click()
  })

  function getRowNumber (eventsOriginalTarget) {
    return $(eventsOriginalTarget).closest('tr').attr('value')
  }

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
    if ($(event.target).is('.cancelButton')) {
      $('#invoiceOverlay').css('display', 'none')
    }
  })

  $('.dialogOverlay').click(function (event) {
    if ($(event.target).is('.overlay')) {
      $('.dialogOverlay').css('display', 'none')
    }
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
    if ($(event.target).parent().attr('id') === 'showAllInvoices') {
      // BOILER-PLATE CODE AHEAD
      $('tr.cancelled').css('display', 'table-row')
      $('tr.open').css('display', 'table-row')
      $('tr.dunning').css('display', 'table-row')
      $('tr.paid').css('display', 'table-row')
      // Only show relevant Invoices
      $('.delete').css('display', 'inline-block')
      $('.reactivateInvoice').css('display', 'none')
      // $('.dashicons-archive').css('display', 'inline-block  ')
      $('.switch').css('display', 'inline-block')
      $('.invoicePaid').css('display', 'none')
    }

    if ($(event.target).parent().attr('id') === 'showOpenInvoices') {
      $('tr.open').css('display', 'table-row')
      $('tr.cancelled').css('display', 'none')
      $('tr.dunning').css('display', 'table-row')
      $('tr.paid').css('display', 'none')

      $('.delete').css('display', 'inline-block')
      $('.reactivateInvoice').css('display', 'none')
      // $('.dashicons-archive').css('display', 'none')
      $('.switch').css('display', 'inline-block')
      $('.invoicePaid').css('display', 'none')
    }

    if ($(event.target).parent().attr('id') === 'showCancelledInvoices') {
      $('tr.cancelled').css('display', 'table-row')
      $('tr.active').css('display', 'none')

      $('.delete').css('display', 'none')
      $('.reactivateInvoice').css('display', 'inline-block')
      // $('.dashicons-archive').css('display', 'none')
      $('.switch').css('display', 'none')
      $('.invoicePaid').css('display', 'none')
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesWithDunning') {
      $('tr.cancelled').css('display', 'none')
      $('tr.open').css('display', 'none')
      $('tr.dunning').css('display', 'table-row')
      $('tr.paid').css('display', 'none')

      $('.delete').css('display', 'none')
      $('.reactivateInvoice').css('display', 'none')
      // $('.dashicons-archive').css('display', 'none')
      $('.switch').css('display', 'none')
      $('.invoicePaid').css('display', 'inline-block')
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesPaid') {
      $('tr.cancelled').css('display', 'none')
      $('tr.open').css('display', 'none')
      $('tr.dunning').css('display', 'none')
      $('tr.paid').css('display', 'table-row')

      $('.delete').css('display', 'none')
      $('.reactivateInvoice').css('display', 'none')
      // $('.dashicons-archive').css('display', 'none')
      $('.switch').css('display', 'none')
      $('.invoicePaid').css('display', 'none')
    }
  })

  $('#filterButtons').on('keyup', 'input', function (event) {
    const searchPattern = $(this).val()

    $('table#tableInvoices tbody').find('tr').each(function (index) {
      if ($(this).find('td').text().toLowerCase().includes(searchPattern.toLowerCase()) && searchPattern) {
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
    Clone.find('input.invoicepositionHidden').val(Clone.find('.invoiceItemsNo > span').text())
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
    if ($('tr#tableRowBank2 input#bank2').val()) {
      $('tr#tableRowBank2').css('display', 'table-row')
      $('tr#tableRowBank1').css('display', 'table-row')
    } else {
      $('tr#tableRowBank1').css('display', 'none')
      $('tr#tableRowBank2').css('display', 'none')
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

  function checkInvoice (invoiceId, item) {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,
      data: {
        action: 'checkInvoiceServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        id: invoiceId,
        item: item
      },
      success: function (response) {
        console.log(response)
      },
      error: function (errorThrown) {
        console.log(errorThrown)
      }
    })
  }

  function updateInvoiceHeaderItem (invoiceId, data) {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,
      data: {
        action: 'updateInvoiceHeaderServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        id: invoiceId,
        data: data
      },
      success: function (response) {
        console.log(response)
      },
      error: function (errorThrown) {
        console.log(errorThrown)
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
    reopenInvoiceForm()

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
    if($('#q-invoice-new-readonly-dummy').text() == "0"){
      $('#prefix').attr('readonly', false);
      $('#invoice_id').attr('readonly', false);
    }
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
        obj = JSON.parse(response)
        console.log(obj)

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
        console.log(errorThrown)
      }
    })
  }

  function reopenInvoiceForm () {
    // Remove error class from previously invalid form input fields
    $('input').removeClass('error')
    // Reset Form Data
    $('#invoiceForm')[0].reset()
    // Remove old form details from previously edited Invoices
    $('.wp-list-table-qInvcLine:not(:first)').remove()
    // Show the Form
    $('#invoiceOverlay').css('display', 'block')
    // Set Customer ID to readonly
    $('#loc_id').prop('readonly', true)
    // Empty nonce fields to update them according to Edit/New Invoice function
    $('div#nonceFields').html('')
  }

  $('table#tableInvoices').on('click', '.edit', function (event) {
    // Do nothing if clicking on specific UI Elements
    if ($(event.target).is('.switch')) { return }
    if ($(event.target).is('.switch > *')) { return }
    if ($(event.target).is('.columnEdit')) { return }
    if ($(event.target).is('.columnEdit > *')) { return }
    if ($(event.target).is('.columnStatusPaid')) { return }
    if ($(event.target).is('.columnStatusPaid > *')) { return }
    // Common Task for openning the invoice form
    reopenInvoiceForm()

    // Show the header for Editing only
    $('h2#formHeaderEdit').css('display', 'block')
    $('h2#formHeaderCreate').css('display', 'none')
    // Show the button for Editing only
    $('#updateInvoice').css('display', 'inline')
    $('#saveInvoice').css('display', 'none')
    // Prepare AJAX Function
    $("input[name='action']").val('updateInvoiceServerSide')
    // Only the nonce for saving is needed
    $('div#nonceFields').prepend(nonceFieldForUpdating)
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

  function formatDate (date) {
    const unformattedDate = new Date(date)
    const year = unformattedDate.getFullYear()
    let month = '0'
    let day = '0'

    if (parseInt(unformattedDate.getMonth()) < 10) {
      month = '0' + unformattedDate.getMonth()
    } else {
      month = unformattedDate.getMonth()
    }

    if (parseInt(unformattedDate.getDate()) < 10) {
      day = '0' + unformattedDate.getDate()
    } else {
      day = unformattedDate.getMonth()
    }

    return year + '-' + month + '-' + day
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
        const serverResponse = JSON.parse(response).data
        console.log(serverResponse)
        const invoiceID = JSON.parse(response).id

        if (serverResponse.action === 'updateInvoiceServerSide') {
          changeUpdatedInvoiceRow(serverResponse)
        }
        if (serverResponse.action === 'saveInvoiceServerSide') {
          addNewInvoiceRow(serverResponse, invoiceID)
        }

        // $('#invoiceForm').trigger('reset')

        $('#invoiceOverlay').css('display', 'none')

        // DIE MELDUNG ALS FUNKTION?
        $('#wpbody-content')
          .prepend('<div class="qinvoiceMessage messageSuccess">' +
                    '<span> Invoice succesfully saved! </span>' +
                    '</div>')

        $('.messageSuccess').delay(1000).fadeOut(800)
      }
    })

    saveInvoiceNonces()

    checkPrefixStatus()

    checkNoStartStatus()

    checkIfBanksHaveBeenSetupinSettings()

    // Ajax Call for Invoice Data
    currencySign = '€'
    fetchInvoiceCurrency()

    $('.deleteRow').css('display', 'inline-block')
    $('.reactivateInvoice').css('display', 'none')
    $('.invoicePaid').css('display', 'none')

    // After submit add error class to invalid input fields
    inputs.forEach(input => {
      input.addEventListener(
        'invalid',
        event => {
          input.classList.add('error')
        },
        false
      )
    })

    // Does not work as intended
    // inputs.forEach(input => {
    //   $(input)[0].setCustomValidity('')
    // })

    checkInvoice(1, 'zip')

    // Prevent chrome to autofill&autocomplete
    $('input[type=text], input[type=number], input[type=email], input[type=password]').focus(function (e) {
      $(this).attr('autocomplete', 'new-password')
    })
  })
})
