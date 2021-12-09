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
      const linePrice = parseFloat($(this).find('.invoiceTotal').attr('value'))
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
                    addPointToThousands(currencyFormatDE(item[1])) +
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
        $(this).find('.qInvcLine-total').text(addPointToThousands(currencyFormatDE(0)))
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
      $(this).find('.qInvcLine-total').text(addPointToThousands(currencyFormatDE(lineSum)))
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
    // get key elements and save them to variables for later use
    const sliderBox = $(event.target).parent()
    const invoiceRow = sliderBox.parent().parent()

    // if the slider gets, but was not checked..
    if (!sliderBox.find('input').prop('checked')) {
      // .. set a paydate to mark as paid
      const data = { paydate: formatDate(new Date()) }
      markInvoice(getRowNumber(event.target), data)

      // and mark that row as paid instead of open
      invoiceRow.removeClass('open edit')
      invoiceRow.addClass('paid')
      invoiceRow.find('.invoiceStatusIcon').addClass('paid')
      invoiceRow.find('.invoiceStatusIcon').removeClass('open')
      invoiceRow.addClass('paid')
      invoiceRow.removeClass('open')
      // paid invoices should not look and be editable
      invoiceRow.find('.columnEdit').find('.delete').css('color', 'lightgrey')
      invoiceRow.find('.columnEdit').find('.delete').removeClass('deleteRow')
    } else {
      // remove paydate, mark as open and make editable
      const data = { paydate: '' }
      unmarkInvoice(getRowNumber(event.target), data)
      invoiceRow.removeClass('paid')
      invoiceRow.addClass('open edit')
      invoiceRow.find('.invoiceStatusIcon').removeClass('paid')
      invoiceRow.find('.invoiceStatusIcon').addClass('open')
      invoiceRow.removeClass('paid')
      invoiceRow.addClass('open')
      invoiceRow.find('.columnEdit').find('.delete').css('color', '#50575e')
      invoiceRow.find('.columnEdit').find('.delete').addClass('deleteRow')
    }

    q_invoice_RecalcSums(0,0,0);
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

  function showAllInvoices () {
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

    $('#qi_totalSumNetto').css('display', 'block')
    $('#qi_openSumNetto').css('display', 'none')
    $('#qi_cancelledSumNetto').css('display', 'none')
    $('#qi_dunningSumNetto').css('display', 'none')
    $('#qi_paidSumNetto').css('display', 'none')
    $('#qi_totalSumTotal').css('display', 'block')
    $('#qi_openSumTotal').css('display', 'none')
    $('#qi_cancelledSumTotal').css('display', 'none')
    $('#qi_dunningSumTotal').css('display', 'none')
    $('#qi_paidSumTotal').css('display', 'none')
    $('#qi_totalSumDunning').css('display', 'block')
    $('#qi_openSumDunning').css('display', 'none')
    $('#qi_cancelledSumDunning').css('display', 'none')
    $('#qi_dunningSumDunning').css('display', 'none')
    $('#qi_paidSumDunning').css('display', 'none')

    q_invoice_RecalcSums(0,0,0);
    q_invoice_modify_cancelled_reactivation_icon();
  }

  function showOpenInvoices () {
    $('tr.open').css('display', 'table-row')
    $('tr.cancelled').css('display', 'none')
    $('tr.dunning').css('display', 'table-row')
    $('tr.paid').css('display', 'none')

    $('.delete').css('display', 'inline-block')
    $('.reactivateInvoice').css('display', 'none')
    // $('.dashicons-archive').css('display', 'none')
    $('.switch').css('display', 'inline-block')
    $('.invoicePaid').css('display', 'none')

    $('#qi_totalSumNetto').css('display', 'none')
    $('#qi_openSumNetto').css('display', 'block')
    $('#qi_cancelledSumNetto').css('display', 'none')
    $('#qi_dunningSumNetto').css('display', 'none')
    $('#qi_paidSumNetto').css('display', 'none')
    $('#qi_totalSumTotal').css('display', 'none')
    $('#qi_openSumTotal').css('display', 'block')
    $('#qi_cancelledSumTotal').css('display', 'none')
    $('#qi_dunningSumTotal').css('display', 'none')
    $('#qi_paidSumTotal').css('display', 'none')
    $('#qi_totalSumDunning').css('display', 'none')
    $('#qi_openSumDunning').css('display', 'block')
    $('#qi_cancelledSumDunning').css('display', 'none')
    $('#qi_dunningSumDunning').css('display', 'none')
    $('#qi_paidSumDunning').css('display', 'none')

    q_invoice_RecalcSums(0,0,0);
  }

  function showCancelledInvoices () {
    $('tr.cancelled').css('display', 'table-row')
    $('tr.active').css('display', 'none')

    $('.delete').css('display', 'none')
    $('.reactivateInvoice').css('display', 'inline-block')
    // $('.dashicons-archive').css('display', 'none')
    $('.switch').css('display', 'none')
    $('.invoicePaid').css('display', 'none')

    $('#qi_totalSumNetto').css('display', 'none')
    $('#qi_openSumNetto').css('display', 'none')
    $('#qi_cancelledSumNetto').css('display', 'block')
    $('#qi_dunningSumNetto').css('display', 'none')
    $('#qi_paidSumNetto').css('display', 'none')
    $('#qi_totalSumTotal').css('display', 'none')
    $('#qi_openSumTotal').css('display', 'none')
    $('#qi_cancelledSumTotal').css('display', 'block')
    $('#qi_dunningSumTotal').css('display', 'none')
    $('#qi_paidSumTotal').css('display', 'none')
    $('#qi_totalSumDunning').css('display', 'none')
    $('#qi_openSumDunning').css('display', 'none')
    $('#qi_cancelledSumDunning').css('display', 'block')
    $('#qi_dunningSumDunning').css('display', 'none')
    $('#qi_paidSumDunning').css('display', 'none')

    q_invoice_RecalcSums(0,0,0);
  }
  function showInvoicesWithDunning () {
    $('tr.cancelled').css('display', 'none')
    $('tr.open').css('display', 'none')
    $('tr.dunning').css('display', 'table-row')
    $('tr.paid').css('display', 'none')

    $('.delete').css('display', 'none')
    $('.reactivateInvoice').css('display', 'none')
    // $('.dashicons-archive').css('display', 'none')
    $('.switch').css('display', 'none')
    $('.invoicePaid').css('display', 'inline-block')

    $('#qi_totalSumNetto').css('display', 'none')
    $('#qi_openSumNetto').css('display', 'none')
    $('#qi_cancelledSumNetto').css('display', 'none')
    $('#qi_dunningSumNetto').css('display', 'block')
    $('#qi_paidSumNetto').css('display', 'none')
    $('#qi_totalSumTotal').css('display', 'none')
    $('#qi_openSumTotal').css('display', 'none')
    $('#qi_cancelledSumTotal').css('display', 'none')
    $('#qi_dunningSumTotal').css('display', 'block')
    $('#qi_paidSumTotal').css('display', 'none')
    $('#qi_totalSumDunning').css('display', 'none')
    $('#qi_openSumDunning').css('display', 'none')
    $('#qi_cancelledSumDunning').css('display', 'none')
    $('#qi_dunningSumDunning').css('display', 'block')
    $('#qi_paidSumDunning').css('display', 'none')

    q_invoice_RecalcSums(0,0,0);
  }
  function showPaidInvoices () {
    $('tr.cancelled').css('display', 'none')
    $('tr.open').css('display', 'none')
    $('tr.dunning').css('display', 'none')
    $('tr.paid').css('display', 'table-row')

    $('.delete').css('display', 'none')
    $('.reactivateInvoice').css('display', 'none')
    // $('.dashicons-archive').css('display', 'none')
    $('.switch').css('display', 'none')
    $('.invoicePaid').css('display', 'none')

    $('#qi_totalSumNetto').css('display', 'none')
    $('#qi_openSumNetto').css('display', 'none')
    $('#qi_cancelledSumNetto').css('display', 'none')
    $('#qi_dunningSumNetto').css('display', 'none')
    $('#qi_paidSumNetto').css('display', 'block')
    $('#qi_totalSumTotal').css('display', 'none')
    $('#qi_openSumTotal').css('display', 'none')
    $('#qi_cancelledSumTotal').css('display', 'none')
    $('#qi_dunningSumTotal').css('display', 'none')
    $('#qi_paidSumTotal').css('display', 'block')
    $('#qi_totalSumDunning').css('display', 'none')
    $('#qi_openSumDunning').css('display', 'none')
    $('#qi_cancelledSumDunning').css('display', 'none')
    $('#qi_dunningSumDunning').css('display', 'none')
    $('#qi_paidSumDunning').css('display', 'block')

    q_invoice_RecalcSums(0,0,0);
  }

  function q_invoice_modify_cancelled_reactivation_icon(){
    $('tr.cancelled').find('.dashicons-no').css('display', 'none');
    $('tr.cancelled').find('.dashicons-undo').css('display', 'inline-block');
  }

  $('#filterButtons').on('click', 'div.inactive', function (event) {
    setFilterButtonInactive($('#filterButtons').find('div.active'))
    setFilterButtonActive($(event.target).parent())
    if ($(event.target).parent().attr('id') === 'showAllInvoices') {
      showAllInvoices()
    }

    if ($(event.target).parent().attr('id') === 'showOpenInvoices') {
      showOpenInvoices()
    }

    if ($(event.target).parent().attr('id') === 'showCancelledInvoices') {
      showCancelledInvoices()
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesWithDunning') {
      showInvoicesWithDunning()
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesPaid') {
      showPaidInvoices()
    }
  })

  /**
   * Function for the search input field #searchInvoices on the Invoices overview Page
   * You can search for all occurences of any kind of text. When you remove the text, all invoices will be shown again
   */
  $('#filterButtons').on('keyup', 'input', function (event) {
    const searchPattern = $(this).val()

    if ($(this).val() == ""){
      $('table#tableInvoices tbody').find('tr').each(function (index) {
        $(this).css('display', 'table-row')
      })
    } else {
      $('table#tableInvoices tbody').find('tr').each(function (index) {
        if ($(this).find('td').text().toLowerCase().includes(searchPattern.toLowerCase()) && searchPattern) {
          $(this).css('display', 'table-row')
        } else {
          $(this).css('display', 'none')
        }
      })
    }

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
    Clone.find('select.itemTax').val(Row.find('select.itemTax').val())
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
    if ($('tr#tableRowBank2 td.labelsRightTable').text()) {
      $('tr#tableRowBank2').css('display', 'table-row')
      $('tr#tableRowBank1').css('display', 'table-row')
    } else {
      $('tr#tableRowBank1').css('display', 'none')
      $('tr#tableRowBank2').css('display', 'none')
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
        console.log('Archived Invoice succesfully')
      },
      error: function (errorThrown) {
        console.log(errorThrown)
      }
    })
  }

  function reactivateInvoice (invoiceId) {
    jQuery.ajax({
      type: 'POST',

      url: q_invoice_ajaxObject.ajax_url,

      data: {
        action: 'reactivateInvoiceServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        id: invoiceId
      },
      success: function (data) {
        console.log('Reactivated Invoice succesfully')
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
        if (response) {
          console.log(response)
        }
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
      success: function (invoiceID) {
        $('input#invoice_id').val(invoiceID)
      },
      error: function (errorThrown) {
        console.log('Error : ' + errorThrown)
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
      success: function (response) {
        currencySign = response
      },
      error: function (errorThrown) {
        console.log(errorThrown)
      }
    })
  }

  $('table#tableInvoices').on('click', '.deleteRow', function (event) {
    $('div#archiveInvoice').css('display', 'block')
    lastInvoiceIDtoDelete = event.target.id
  })

  $('table#tableInvoices').on('click', '.reactivateInvoice', function (event) {
    const lastInvoiceIDtoDelete = event.target.id
    const targetRow = $('tr.edit' + '[value=' + lastInvoiceIDtoDelete + ']')
    const statusIcon = targetRow.find('.invoiceStatusIcon')
    if ($('#showCancelledInvoices').hasClass('active')) {
      targetRow.css('display', 'none')
    }
    reactivateInvoice(lastInvoiceIDtoDelete)
    $('div#archiveInvoice').css('display', 'none')
    statusIcon.addClass('active')
    statusIcon.removeClass('cancelled')
    targetRow.removeClass('cancelled')
    targetRow.addClass('active')
    targetRow.find('.reactivateInvoice').css('display', 'none')
    targetRow.find('.deleteRow').css('display', 'inline-block')
    targetRow.find('.switchForPaidStatus').css('opacity', '100')
    targetRow.find('.switchForPaidStatus > *').css('opacity', '100')
    q_invoice_RecalcSums(0,0,0);
  })

  $('div#archiveInvoice').on('click', '#cancelRemoveInvoice', function () {
    $('div#archiveInvoice').css('display', 'none')
  })

  $('div#archiveInvoice').on('click', '#confirmRemoveInvoice', function (event) {
    const targetRow = $('tr.edit' + '[value=' + lastInvoiceIDtoDelete + ']')
    const statusIcon = targetRow.find('.invoiceStatusIcon')
    if ($('#showOpenInvoices').hasClass('active')) {
      targetRow.css('display', 'none')
    }
    deleteInvoice(lastInvoiceIDtoDelete)
    $('div#archiveInvoice').css('display', 'none')
    statusIcon.addClass('cancelled')
    statusIcon.removeClass('active')
    targetRow.removeClass('active')
    targetRow.addClass('cancelled')
    targetRow.find('.deleteRow').css('display', 'none')
    targetRow.find('.reactivateInvoice').css('display', 'inline-block')
    targetRow.find('.switchForPaidStatus').css('opacity', '0')
    targetRow.find('.switchForPaidStatus > *').css('opacity', '0')
    targetRow.find('.reactivateInvoice').attr('class', 'reactivateInvoice reactivate dashicons dashicons-undo')
    q_invoice_RecalcSums(0,0,0);
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
    $('#heading-invoice').find('.switchForPaidStatus').css('display', 'none')
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
    if ($('#q-invoice-new-readonly-dummy').text() === '0') {
      $('#prefix').attr('readonly', false)
      $('#invoice_id').attr('readonly', false)
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
        if (obj[0][0].bank === '2') {
          $('td.inputsRightTable input#bank2').attr('checked', 'true')
        }
        if (!(obj[0][0]['paydate'] == '0000-00-00')){
          $('#invocie_form_paid_toggle').prop("checked", true);
        } else{
          $('#invocie_form_paid_toggle').prop("checked", false);
        }
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
        $('#lastname').prop('required', false);
        $('#firstname').prop('required', false);
        $('#company').prop('required', false);
    
        if(!$("#company").val() && (!$("#firstname").val() || !$("#firstname").val())){
          $('#company').prop('required', true);
          $('#lastname').prop('required', true);
          $('#firstname').prop('required', true);
        } else if(!$("#company").val()){
          $('#lastname').prop('required', true);
          $('#firstname').prop('required', true);
        }else{
          $('#company').prop('required', true);
        }
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
    // backup sum row, to avoid first ID bug
    let sumRowBackup = ''
    sumRowBackup = $('table#tableInvoices > tbody > tr#q_invoice_totalSums').clone()
    // Find the right row
    let row = ''
    row = $('table#tableInvoices > tbody > tr[value=' + invoice.invoice_id + ']')
    var rowAttributes = $('table#tableInvoices > tbody > tr[value=' + invoice.invoice_id + ']').attr('class')
    row.attr('class', rowAttributes) // Vorher: row.attr('class', ' edit open') --> Warum war das wichtig?
    // Change Infos in the right table row.
    if (invoice.company) {
      row.find('td.columnName').text(invoice.company)
    } else {
      row.find('td.columnName').text(invoice.firstname + ' ' + invoice.lastname)
    }
    row.find('td.columnDescription').text(invoice.itemDescription[0])
    row.find('td.columnNet').text($('.qInvc-total-summe').eq(0).text() + ' ' + currencySign)
    row.find('td.columnTotal').text($('.qInvc-total-brutto-summe').eq(0).text() + ' ' + currencySign)

    const date = invoice.dateOfInvoice
    // change to german date format
    const formattedDate = date.slice(8, 10) + '.' + date.slice(5, 7) + '.' + date.slice(0, 4)
    row.find('td.columnDate').text(formattedDate)
    $('table#tableInvoices tr:last').remove()
    $('table#tableInvoices tr:last').after(sumRowBackup)

    

    q_invoice_RecalcSums(
      parseFloat(((row.find('td.columnTotal').text()).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.')),
      parseFloat(((row.find('td.columnNet').text()).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.')),
      0.0
    );
  }

  /**
   * Function to recalc the Sum in the last row for each of "All, Open, Dunning, Cancelled, Paid"
   * @param {*} modifiedTotal 
   * @param {*} modifiedNet 
   * @param {*} modifiedDun 
   */
  function q_invoice_RecalcSums(modifiedTotal, modifiedNet, modifiedDun){
    //Rows haben folgende Attribute: (paid || dunning edit || open edit) && (cancelled || active)

    //get all rows, that exist on current page for each class attribute
    var openTotalRows = $(".open.active td.columnTotal span");
    var openNetRows = $(".open.active td.columnNet span");
    var openDunRows = $(".open.active td.columnDunning span");
    var cancelledTotalRows = $(".cancelled td.columnTotal span");
    var cancelledNetRows = $(".cancelled td.columnNet span");
    var cancelledDunRows = $(".cancelled td.columnDunning span");
    var dunningTotalRows = $(".dunning td.columnTotal span");
    var dunningNetRows = $(".dunning td.columnNet span");
    var dunningDunRows = $(".dunning td.columnDunning span");
    var paidTotalRows = $(".paid td.columnTotal span");
    var paidNetRows = $(".paid td.columnNet span");
    var paidDunRows = $(".paid td.columnDunning span");

    var newOpenTotalNet = modifiedNet;
    var newOpenTotalTotal = modifiedTotal;
    var newOpenTotalDun = modifiedDun;
    for(var i = 0; i < openTotalRows.length; i++){
      newOpenTotalTotal = newOpenTotalTotal + parseFloat(((openTotalRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      newOpenTotalNet = newOpenTotalNet + parseFloat(((openNetRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      //newOpenTotalDun = newOpenTotalDun + parseFloat(((openDunRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
    }

    var newCancelledTotalNet = 0.00;
    var newCancelledTotalTotal = 0.00;
    var newCancelledTotalDun = 0.00;
    for(var i = 0; i < cancelledTotalRows.length; i++){
      newCancelledTotalTotal = newCancelledTotalTotal + parseFloat(((cancelledTotalRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      newCancelledTotalNet = newCancelledTotalNet + parseFloat(((cancelledNetRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      //newCancelledTotalDun = newCancelledTotalDun + parseFloat(((cancelledDunRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
    }

    var newDunningTotalNet = 0.00;
    var newDunningTotalTotal = 0.00;
    var newDunningTotalDun = 0.00;
    for(var i = 0; i < dunningTotalRows.length; i++){
      newDunningTotalTotal = newDunningTotalTotal + parseFloat(((dunningTotalRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      newDunningTotalNet = newDunningTotalNet + parseFloat(((dunningNetRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      //newDunningTotalDun = newDunningTotalDun + parseFloat(((dunningDunRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
    }

    var newPaidTotalNet = 0.00;
    var newPaidTotalTotal = 0.00;
    var newPaidTotalDun = 0.00;
    for(var i = 0; i < paidTotalRows.length; i++){
      newPaidTotalTotal = newPaidTotalTotal + parseFloat(((paidTotalRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      newPaidTotalNet = newPaidTotalNet + parseFloat(((paidNetRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
      //newPaidTotalDun = newPaidTotalDun + parseFloat(((paidDunRows[i].innerHTML).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.'));
    }

    var newAllTotalNet = newPaidTotalNet + newDunningTotalNet + newOpenTotalNet + newCancelledTotalNet;
    var newAllTotalDun = newPaidTotalDun + newDunningTotalDun + newOpenTotalDun + newCancelledTotalDun;

    var newAllTotalTotal = newPaidTotalTotal + newDunningTotalTotal + newOpenTotalTotal + newCancelledTotalTotal;
    var totalTotalArray = (newAllTotalTotal.toString() + '.00').replace('.', ',').split(",");
    var newAllTotalTotalString = totalTotalArray[0] + "," + totalTotalArray[1].substring(0,2) + " " + currencySign;


    $('#qi_totalSumNetto').html(newAllTotalNet.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_openSumNetto').html(newOpenTotalNet.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_cancelledSumNetto').html(newCancelledTotalNet.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_dunningSumNetto').html(newDunningTotalNet.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_paidSumNetto').html(newPaidTotalNet.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_totalSumTotal').html(newAllTotalTotalString);
    $('#qi_openSumTotal').html(newOpenTotalTotal.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_cancelledSumTotal').html(newCancelledTotalTotal.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_dunningSumTotal').html(newDunningTotalTotal.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_paidSumTotal').html(newPaidTotalTotal.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_totalSumDunning').html(newAllTotalDun.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_openSumDunning').html(newOpenTotalDun.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_cancelledSumDunning').html(newCancelledTotalDun.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_dunningSumDunning').html(newDunningTotalDun.toString().replace('.', ',') + ' ' + currencySign);
    $('#qi_paidSumDunning').html(newPaidTotalDun.toString().replace('.', ',') + ' ' + currencySign);
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

    if ($('#q-invoice-new-readonly-dummy').text() === '0') {
      location.reload();
    } else{
      const clone = $('table#tableInvoices > tbody').find('tr').first().clone()
      clone.attr('id', 'edit-' + id)
      clone.attr('value', id)
      clone.find('td.columnRowID').text(1 + parseInt(clone.find('td.columnRowID').text()))
      if (invoice.company) {
        var pdfName = invoice.company;
        clone.find('td.columnName').text(invoice.company)
      } else {
        var pdfName = invoice.firstname + "_" + invoice.lastname;
        clone.find('td.columnName').text(invoice.firstname + ' ' + invoice.lastname)
      }

      clone.find('td.columnDescription').text(invoice.itemDescription[0])
      clone.find('td.columnNet').text($('.qInvc-total-summe').eq(0).text() + ' ' + currencySign)
      clone.find('td.columnTotal').text($('.qInvc-total-brutto-summe').eq(0).text() + ' ' + currencySign)
      document.getElementById('qi_totalSumTotal').value = document.getElementById('qi_totalSumTotal').value + parseInt($('.qInvc-total-brutto-summe').eq(0).text());
      document.getElementById('qi_totalSumTotal').innerHTML = document.getElementById('qi_totalSumNetto').value + ' ' + currencySign;

      const date = invoice.dateOfInvoice
      // change to german date format
      const formattedDate = date.slice(8, 10) + '.' + date.slice(5, 7) + '.' + date.slice(0, 4)
      clone.find('td.columnDate').text(formattedDate)

      clone.find('td.columnInvoiceID span').text(id)

      clone.find('a.download').attr('id', 'download-' + id)
      clone.find('a.download').attr('value', id)

      var datePieces = formattedDate.split('.');
      var datePDF = datePieces[2] + '-' + datePieces[1] + '-' + datePieces[0];

      console.log(datePDF);
      clone.find('a.download').attr('href', '/wp-content/plugins/q_invoice/pdf/Invoice-' + invoice.prefix + id + '_' + pdfName + '_' + datePDF + '.pdf');

      clone.find('span.deleteRow').attr('id', id)
      clone.find('span.deleteRow').attr('value', id)

      q_invoice_RecalcSums(
        parseFloat(((clone.find('td.columnTotal').text()).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.')),
        parseFloat(((clone.find('td.columnNet').text()).replace(/\s+/g, '').split(currencySign, 1)[0]).replace(',', '.')),
        0.0
      );

      $('table#tableInvoices > tbody').prepend(clone)
    }
  

  }

  function displaySuccess () {
    // DIE MELDUNG ALS FUNKTION?
    $('#wpbody-content').prepend(
      '<div class="qinvoiceMessage messageSuccess">' +
      '<span> Invoice succesfully saved! </span>' +
      '</div>')

    $('.messageSuccess').delay(1000).fadeOut(800)
  }

  jQuery(document).ready(function ($) {
    $('#invoiceForm').ajaxForm({
      success: function (response) {
        var serverResponse = JSON.parse(response).data
        var invoiceID = JSON.parse(response).id

        if (serverResponse.action === 'updateInvoiceServerSide') {
          changeUpdatedInvoiceRow(serverResponse)
        }
        if (serverResponse.action === 'saveInvoiceServerSide') {
          addNewInvoiceRow(serverResponse, invoiceID)
        }

        // $('#invoiceForm').trigger('reset')

        $('#invoiceOverlay').css('display', 'none')
        displaySuccess()
      }
    })

    saveInvoiceNonces()

    checkIfBanksHaveBeenSetupinSettings()

    // Ajax Call for Invoice Data
    currencySign = '€'
    fetchInvoiceCurrency()

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

    $('input').bind('invalid', function () { return false })

    // Prevent chrome to autofill&autocomplete
    $('#invoiceInputTables input').focus(function (e) {
      $(this).attr('autocomplete', 'new-password')
    })
  })

  /**
   * Handle the requirements in the invoice form:
   * Either the company name is required or the First- and Lastname
   */
  jQuery(document).ready(function ($) {
    $('#company').blur(function(){
      if(!$(this).val() && (!$('#lastname').val() || !$('#firstname').val())){
        $('#firstname').prop('required', true);
        $('#lastname').prop('required', true);
        $('#company').prop('required', true);
      }else if(!!$('#lastname').val() && !!$('#firstname').val()){
        $('#company').prop('required', false);
      } else{
        $('#firstname').prop('required', false);
        $('#lastname').prop('required', false);
      }
    });
    $('.inputName').blur(function(){
      if((!$(this).val() || !$('#lastname').val() || !$('#firstname').val()) && !$('#company').val()){
        $('#firstname').prop('required', true);
        $('#lastname').prop('required', true);
        $('#company').prop('required', true);
      }else if(!!$('#company').val()){
        $('#firstname').prop('required', false);
        $('#lastname').prop('required', false);
      } else{
        $('#company').prop('required', false);
      }
    });
  })

  /**
   * Switch function for toggle in Invoice Form:
   * 
   * - The toggle is unchecked if the Invoice Form is shown (Invoice Form Popup will be deactivated if the Invoice is already Paid --> Toggle would be checked)
   * - When the Toggle is checked the Invoice Form can not be modified until you uncheck the paid toggle in the list --> checking the toggle closes the Popup and prohibits further modifications
   * - The toggle will be observed by an on change evnt listener
   * - If this listener detects the Toggle to be checked, a click action in the paid toggle in the overview is simulated and the popup will be closed
   *  */

  $('#invocie_form_paid_toggle').change(function(){
    if (this.checked){

      var id = $('#invoice_id').val();
      
      setTimeout(function(){
        $('#invoiceOverlay').css('display', 'none');
        $('#edit-'+id).find('.sliderForPayment').click();
      },800);

    }
  });

  /**
   * Function to simulate a dynamic ID size depending on the ID length:
   * Each Number will receive 7px + 11px for the first
   */

   jQuery(document).ready(function ($) {
    
    var id_length = $("tbody tr:first td:first span").text().replace(/\s+/g, '').length;
    if (id_length > 1){
      var id_width = 11 + ((id_length - 1) * 7);
    } else{
      var id_width = 13;
    }
    $(".q-invoice-page table#tableInvoices .columnInvoiceID").css("width", id_width);
    
   })

})
