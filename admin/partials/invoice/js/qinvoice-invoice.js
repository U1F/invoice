/* global q_invoice_ajaxObject, jQuery */
/* eslint no-undef: "error" */

jQuery(function ($) {

  const formatterDE = new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 2
  });
  const formatterEN = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2
  });
  let currencySign = ''
  let lastInvoiceIDtoDelete = 0
  let currentInvoiceID = 0
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

  function removeCurrencySign(value){

    value = value.toString();
    //Add Currency Symbols here, that will be added with any currency Formatter like formatterDE or formatterEN
    value = value.replace("€", "");
    value = value.replace("$", "");
    value = value.replace(/\s+/g, '');
    return value;

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

        if($('#q-invoice-new-dot-dummy').text() == ','){
          var valueTotal = removeCurrencySign(formatterDE.format(parseFloat(item[1])));
        } else{
          var valueTotal = removeCurrencySign(formatterEN.format(parseFloat(item[1])));
        }

        $('table#sums tr.invoiceSums:first').after(
          "<tr id='qInvc-total-mwst" + item[0] + "-summe'" +
                    "class='invoiceTaxSums qi_mobileFlex'" +
                    '>' +

                    "<td class='qInvc-total invoiceSumsLabel'>" +
                    'Tax (' + item[0] + '%)' +
                    '</td>' +

                    "<td class='qInvc-total invoiceSumsAccounts'>" +
                    valueTotal +
                    ' ' +
                    currencySign +
                    '</td>' +
                    '</tr>'
        )
      }
    })
    totalSum = netSum + taxSum
    if($('#q-invoice-new-dot-dummy').text() == ','){
      var formattedNet = removeCurrencySign(formatterDE.format(parseFloat(netSum)));
      var formattedTotal = removeCurrencySign(formatterDE.format(parseFloat(totalSum)));
    } else{
      var formattedNet = removeCurrencySign(formatterEN.format(parseFloat(netSum)));
      var formattedTotal = removeCurrencySign(formatterEN.format(parseFloat(totalSum)));
    }
    // Write formatted Sums to the form
    $('.qInvc-total-summe').eq(0).text(formattedNet);
    $('.qInvc-total-brutto-summe').eq(0).text(formattedTotal);
  }

  /**
     * Function recalcLineSum
     */
  function recalcLineSum () {
    let lineTax = 0
    $('.wp-list-table-qInvcLine').each(function () {
      const amountOfItems = parseInt($(this).find('input.amountOfItems').val())
      
      //get item Price and handle different writing systems like 1.000 = 1000
      var itemPrice = 0
      var itemPriceArray = $(this).find('input.itemPrice').val().replace(',','.').split(".")
      if(itemPriceArray.length > 2){
        itemPriceString = ''
        for(i = 0; i < itemPriceArray.length - 1; i++){
          itemPriceString = itemPriceString + itemPriceArray[i]
        }
        itemPrice = parseFloat(itemPriceString + '.' + itemPriceArray[itemPriceArray.length - 1])
      } else {
        if(itemPriceArray[itemPriceArray.length - 1].length > 2){
          itemPrice = parseFloat($(this).find('input.itemPrice').val().replace(',','').replace('.', ''))
        } else{
          itemPrice = parseFloat($(this).find('input.itemPrice').val().replace(',','.'))
        }
      }
      
      const discountOnItem = parseFloat($(this).find('input.itemDiscount').val().replace(',', '.'))
      let discountedPrice = 0
      let lineSum = 0
      if (isNaN(amountOfItems) || isNaN(itemPrice)) {
        if($('#q-invoice-new-dot-dummy').text() == ','){
          var formattedZero = removeCurrencySign(formatterDE.format(0));
        } else{
          var formattedZero = removeCurrencySign(formatterEN.format(0));
        }
        $(this).find('.qInvcLine-total').text(formattedZero)
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
      discountedPrice = discountedPrice.toFixed(2);
      $(this).find('input.amountActual').attr('value', discountedPrice)
      lineSum = amountOfItems * discountedPrice
      //format linesum
      if($('#q-invoice-new-dot-dummy').text() == ','){
        var formattedLineSum = removeCurrencySign(formatterDE.format(lineSum));
      } else{
        var formattedLineSum = removeCurrencySign(formatterEN.format(lineSum));
      }
      lineTax = $(this).find('select.itemTax').val()
      $(this).find('input.invoiceTax').attr('value', lineSum * lineTax / 100)
      $(this).find('.qInvcLine-total').text(formattedLineSum)
      $(this).find('input.invoiceTotal').attr('value', lineSum)
      $(this).find('.invoiceItemsTotal nobr').css('display', 'inline')
    })
  }

  /**
   * Function to add X Working Days on a start Date Y
   * 
   * @param {Start date on which the working days have to be added --> use strtotime of the Startdate for example} $timestamp 
   * @param {Number of Wokring Days that have to be added} $days 
   * @param {Week Days that have to be skipped --> array (Monday-Sunday) eg. array("Saturday","Sunday")} $skipdays 
   * @param {Further Dates that have to be skipped --> array (YYYY-mm-dd) eg. array("2012-05-02","2015-08-01")} $skipdates 
   * @returns date("Y-m-d, $newTime")
   */
  function addWorkingDays($timestamp, $days, $skipdays = array("Saturday", "Sunday"), $skipdates = NULL) {
    $i = 1;

    while ($days >= $i) {
        $timestamp = strtotime("+1 day", $timestamp);
        if ( (in_array(date("l", $timestamp), $skipdays)) || (in_array(date("Y-m-d", $timestamp), $skipdates)) ){
            $days++;
        }
        $i++;
    }
    return $timestamp;
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

  $(".filterButtons").on("click",function(){
    console.log(".filterButtons: 'You clicked me!'")
    //  paginate()
  })
  // sum has to be omitted 
  // For most rows we need a special sort function.
  // Sums would have to be added below
  // Every Caption would need its own algorithm (comparer)
    $('th').click(function(){
      return; // for now, I do not want this function to work
      var table = $(this).parents('table').eq(0)
      var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
      this.asc = !this.asc
      if (!this.asc){rows = rows.reverse()}
      for (var i = 0; i < rows.length; i++){table.append(rows[i])}
    })

    function comparer(index) {
        return function(a, b) {
            var valA = getCellValue(a, index), valB = getCellValue(b, index)
            return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
        }
    }

    function getCellValue(row, index){ return $(row).children('td').eq(index).text() }

  function markInvoice (invoiceID, data) {
    updateInvoiceHeaderItem(invoiceID, data)
  }

  function unmarkInvoice (invoiceID, data) {
    updateInvoiceHeaderItem(invoiceID, data)
  }

  // Clicking on the slider Paid/Unpaid changes UI functionality and updates database
  $('.columnStatusPaid').on('click', '.sliderForPayment', function (event) {
    // get key elements and save them to variables for later use
    const sliderBox = $(event.target).parent()
    const invoiceRow = sliderBox.parent().parent()

    if(invoiceRow.hasClass('cancelled')){
      return;
    }

    // if the slider gets, but was not checked..
    if (!sliderBox.find('input').prop('checked')) {
      // .. set a paydate to mark as paid
      const data = { paydate: formatDate(new Date()) }
      markInvoice(getRowNumber(event.target), data)

      // and mark that row as paid instead of open
      invoiceRow.removeClass('open')
      invoiceRow.addClass('paid')
      invoiceRow.find('.invoiceStatusIcon').addClass('paid')
      invoiceRow.find('.invoiceStatusIcon').removeClass('open')
      invoiceRow.addClass('paid')
      invoiceRow.removeClass('open')
      // paid invoices should not look and be editable
      invoiceRow.find('.columnEdit').find('.delete').css('color', '#dadce1')
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

  // Ich denke, das folgende ist nicht mehr nötig:
  $('.columnStatusPaid').on('click', '.markAsPaid', function (event) {
    $(event.target).closest('tr').find('.sliderForPayment').click()
  }) // Bis hier hin.

  function getRowNumber (eventsOriginalTarget) {
    return $(eventsOriginalTarget).closest('tr').attr('value')
  }

  // Escape key closes Overlay
  $(document).on('keydown', function (e) {
    if (e.keyCode === 27) {
      if ($('.dialogOverlay').css('display') === 'block') {
        $('.dialogOverlay').hide()
      } else {
        $('#invoiceOverlay').hide()
      }
    }
  })

  // Clicking outside of the form or cancel button also closes Overlay
  $('#invoiceOverlay').click(function (event) {
    if ($(event.target).is('.overlay')) {
      $('#invoiceOverlay').hide()
    }
    if ($(event.target).is('.cancelButton')) {
      $('#invoiceOverlay').hide()
    }
  })

  // Closing PopUp "Archive Invoice"
  $('.dialogOverlay').click(function (event) {
    if ($(event.target).is('.overlay')) {
      $('.dialogOverlay').hide()
    }
  })

  function setFilterButtonActive (target) {
    
    target.attr('class', 'filterButton active')
    
  }

  function setFilterButtonInactive (target) {
    
    target.attr('class', 'filterButton inactive')
  
  }

  function showPayToggle(doShow){
    if (doShow) {
      $('.switch').show()
    }
    else {
      $('.switch').hide()
    }
  }

  function showDeleteButton(doShow){
    if (doShow) {
      $('.delete').show()
    }
    else {
      $('.delete').hide()
    }
  }

  function showReactivationButton(doShow){
    if (doShow) {
      $('.reactivateInvoice').show()
    }
    else {
      $('.reactivateInvoice').hide()
    }
  }

  function filterInvoices(invoiceCategory){
    $('#tableInvoices tbody tr').hide()
    $('#q_invoice_totalSums span').hide()
    
    switch (invoiceCategory){
      case "all":
        showDeleteButton(true)
        showReactivationButton(false)
        showPayToggle(true)
        $('#tableInvoices tbody tr').slice(0, invoicesOnPage).show();
        $('#qi_totalSumNetto').show()
        $('#qi_totalSumTotal').show()
        $('#qi_totalSumDunning').show()
        q_invoice_modify_cancelled_reactivation_icon();
        break

      case "open":
        showDeleteButton(false)
        showReactivationButton(false)
        showPayToggle(true)
        // The next 2 need work:
        $('#tableInvoices tbody tr.open').slice(0, invoicesOnPage).show()
        $('#tableInvoices tbody tr.dunnung').slice(0, invoicesOnPage).show()
        $('#qi_openSumNetto').show()
        $('#qi_openSumTotal').show()
        $('#qi_openSumDunning').show()
        break

      case "cancelled":
        showDeleteButton(false)
        showReactivationButton(true)
        showPayToggle(true)
        $('#tableInvoices tbody tr.cancelled').slice(0, invoicesOnPage).show()
        $('#qi_cancelledSumNetto').show()
        $('#qi_cancelledSumTotal').show()
        $('#qi_cancelledSumDunning').show()
        break

      case "dunning":
        showDeleteButton(false)
        showReactivationButton(false)
        showPayToggle(false)
        $('#tableInvoices tbody tr.dunning').slice(0, invoicesOnPage).show();
        $('#qi_dunningSumNetto').show()
        $('#qi_dunningSumDunning').show()
        $('#qi_dunningSumTotal').show()
        break

      case "paid":
        showDeleteButton(false)
        showReactivationButton(false)
        showPayToggle(false)
        $('#tableInvoices tbody tr.paid').slice(0, invoicesOnPage).show();
        $('#qi_paidSumDunning').show()
        $('#qi_paidSumTotal').show()
        $('#qi_paidSumNetto').show()
        break
    }

    q_invoice_RecalcSums(0,0,0);
    $('#q_invoice_totalSums').show()

  }

  function q_invoice_modify_cancelled_reactivation_icon(){
    $('tr.cancelled').find('.dashicons-no').hide();
    $('tr.cancelled').find('.dashicons-undo').css('display', 'inline-block');
  }

  // Manage UI visibility of filter buttons mobile version
  $('#mobileFilterButtonsDropdown').on('change', function (event) {
    if ($('#mobileFilterButtonsDropdown option:selected').val() === 'all') {
      filterInvoices('all')
    }
    if ($('#mobileFilterButtonsDropdown option:selected').val() === 'open') {
      filterInvoices('open')
    }
    if ($('#mobileFilterButtonsDropdown option:selected').val() === 'paid') {
      filterInvoices('paid')
    }
    if ($('#mobileFilterButtonsDropdown option:selected').val() === 'cancelled') {
      filterInvoices('cancelled')
    }
    if ($('#mobileFilterButtonsDropdown option:selected').val() === 'dunning') {
      filterInvoices('dunning')
    }
    $('#mobileFilterButtonsDropdown').blur();
  });

  // Manage UI visibility of filter buttons
  $('.filterButtons').on('click', 'div.inactive', function (event) {
    if ($(event.target).parent().attr('id') === 'filterButtons') {
      return;
    }
    setFilterButtonInactive($('.filterButtons').find('div.active'))
    setFilterButtonActive($(event.target).parent())
    if ($(event.target).parent().attr('id') === 'showAllInvoices') {
      filterInvoices('all')
    }

    if ($(event.target).parent().attr('id') === 'showOpenInvoices') {
      filterInvoices('open')
    }

    if ($(event.target).parent().attr('id') === 'showCancelledInvoices') {
      filterInvoices('cancelled')
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesWithDunning') {
      filterInvoices('dunning')
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesPaid') {
      filterInvoices('paid')
    }
  })

  $('#qInvMobileSearchIcon').on('click', function (event){
    $('#filterButtonMobileSearchInput').css('display', 'block');
    $('#filterButtonMobileSearchInput').focus();
  });

  $('#filterButtonMobileSearchInput').on('blur', function (event){
    $('#filterButtonMobileSearchInput').css('display', 'none');
  });

  /**
   * Function for the search input field #searchInvoices on the Invoices overview Page
   * You can search for all occurences of any kind of text. When you remove the text, all invoices will be shown again
   */
  $('.qInvMainSearchable').on('keyup', 'input', function (event) {
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
          $(this).hide()
        }
      })
    }

  })

  // Is this implemented?
  $('#items').on('click', '.discountTypeSwitch', function () {
    if ($(this).val() === 'Euro') {
      $(this).val('Percent')
      $(this).html('%')
    } else if ($(this).val() === 'Percent') {
      $(this).val('Euro')
      $(this).html(currencySign)
    }
  })

  /*$('#items').on('change', 'input.itemDiscount', function () {
    $(this).val(parseFloat($(this).val()).toFixed(2))
  })*/

  $('.qInvc-table').eq(0).on('change', 'select.itemTax, select.discountType', function () {
    recalcLineSum()
    recalcTotalSum()
  })

  $('.qInvc-table').eq(0).on('keyup', 'input.amountOfItems, input.itemPrice, input.itemDiscount', function () {
    recalcLineSum()
    recalcTotalSum()
  })

  // Calculate Positions of invoice items
  function recalcPos () {
    $('.wp-list-table-qInvcLine').each(function (index) {
      $(this).find('.qInvc-pos').text(index + 1)
      $(this).find('.invoicePositionHidden').attr('value', index + 1)
    })
  }

  // Add a new line for invoice item
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
    Clone.find('.invoiceItemsTotal nobr').hide()
    Clone.insertAfter(Row)

    recalcPos()
  })

  // Remove invoice item
  $('.qInvc-table').eq(0).on('click', '.qInvc-delete-line', function (e) {
    e.preventDefault()

    const parent = $(this).parent().parent()

    if ($('.wp-list-table-qInvcLine').length > 1) {
      parent.remove()
    }

    recalcPos()
    recalcTotalSum()
  })

  // Provides a handle to sort items
  $('.qInvc-table').eq(0).sortable({
    items: 'tr.wp-list-table-qInvcLine',
    handle: '.sortHandle',
    stop: function (event, ui) {
      recalcPos()
    }
  })

  // Only show two bank items, if two banks have been set up in settings
  function checkIfBanksHaveBeenSetupinSettings () {
    if ($('tr#tableRowBank2 td.labelsRightTable').text()) {
      $('tr#tableRowBank2').css('display', 'table-row')
      $('tr#tableRowBank1').css('display', 'table-row')
    } else {
      $('tr#tableRowBank1').hide()
      $('tr#tableRowBank2').hide()
    }
  }

  /**
   * Handles the on click action on the more-ellepsis dashicon:
   * Open a dropdown to shwo urther options. Save the clicked invoice ID to get specific data.
   */
   $('table#tableInvoices').on('click', '.moreInvoiceOptions', function (event) {
    $(this).parent().parent().find('div.qinv_moreOptionsDropdownBox').css('display', 'block')
    currentInvoiceID = event.target.id
  })

  /**
   * Close Dropdown when clicked anywhere but on the Dropdown itself
   */
  $(document).on('mouseup', function(e){
    if(!$('div.qinv_moreOptionsDropdownBox').is(e.target) && $('div.qinv_moreOptionsDropdownBox').has(e.target).length === 0){
      $('div.qinv_moreOptionsDropdownBox').css('display', 'none');
    }
  })

  /**
   * Duplicate the current Invoice but put it in the New Invoice Form to insert it as a new one
   */
  $('.duplicateInvoice').on('click', function (event) {

    duplicateInvoice();

  })

  /**
   * Extracted as function to bind this funciton later on when creating a new row
   */
  function duplicateInvoice(){
    //close the dropdown
    $('div.qinv_moreOptionsDropdownBox').css('display', 'none');

    //prepare Form as new Invoice
    $('h2#formHeaderEdit').hide()
    $('h2#formHeaderCreate').css('display', 'block')
    $('#heading-invoice').find('.switchForPaidStatus').hide()
    $('#loc_id').prop('readonly', false)
    $('#updateInvoice').hide()
    $('#saveInvoice').css('display', 'inline')
    $("input[name='action']").val('saveInvoiceServerSide')

    //hide paid bar
    $('#heading-invoice').find('.switchForPaidStatus').hide()
    
    // Common Task for openning the invoice form
    reopenInvoiceForm()
    //Only the nonce for saving is needed
    $('div#nonceFields').html('')
    $('div#nonceFields').prepend(nonceFieldForSaving)


    // fetch id from span attribute id="edit-n", where  n = id of invoice
    editInvoice(currentInvoiceID, true)
  }

  /**
   * Handles the on click action on the delete dashicon:
   * Open an alert to confirm if a invoice should really be archived.
   */
  $('table#tableInvoices').on('click', '.deleteRow', function (event) {
    $('div#archiveInvoice').css('display', 'block')
    lastInvoiceIDtoDelete = event.target.id
  })

  $('table#tableInvoices').on('click', '.reactivateInvoice', function (event) {
    const lastInvoiceIDtoDelete = event.target.id
    const targetRow = $('tr.edit' + '[value=' + lastInvoiceIDtoDelete + ']')
    const statusIcon = targetRow.find('.invoiceStatusIcon')
    if ($('#showCancelledInvoices').hasClass('active')) {
      targetRow.fadeOut("slow")
    }
    reactivateInvoice(lastInvoiceIDtoDelete)
    $('div#archiveInvoice').hide()
    statusIcon.addClass('active')
    statusIcon.removeClass('cancelled')
    targetRow.removeClass('cancelled')
    targetRow.addClass('active')
    targetRow.find('.reactivateInvoice').hide()
    targetRow.find('.deleteRow').css('display', 'inline-block')
    targetRow.find('.switchForPaidStatus').css('opacity', '100')
    targetRow.find('.switchForPaidStatus > *').css('opacity', '100')
    q_invoice_RecalcSums(0,0,0);
  })

  $('div#archiveInvoice').on('click', '#cancelRemoveInvoice', function () {
    $('div#archiveInvoice').hide()
  })

  $('div#archiveInvoice').on('click', '#confirmRemoveInvoice', function (event) {
    const targetRow = $('tr.edit' + '[value=' + lastInvoiceIDtoDelete + ']')
    const statusIcon = targetRow.find('.invoiceStatusIcon')
    if ($('#showOpenInvoices').hasClass('active')) {
      targetRow.fadeOut("slow")
    }
    deleteInvoice(lastInvoiceIDtoDelete)
    $('div#archiveInvoice').hide()
    statusIcon.addClass('cancelled')
    statusIcon.removeClass('active')
    targetRow.removeClass('active')
    targetRow.addClass('cancelled')
    targetRow.find('.deleteRow').hide()
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

  /**
   * click handler when a new invoice should be set: Clicking opens an empty form
   * 
   **/
  $('#newInvoice').click(function () {
    reopenInvoiceForm()

    // prepare form for Ajax Action "save"
    $('h2#formHeaderEdit').hide()
    $('h2#formHeaderCreate').css('display', 'block')
    $('#heading-invoice').find('.switchForPaidStatus').hide()
    $('#loc_id').prop('readonly', false)
    $('#updateInvoice').hide()
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
    //reset the form sums
    $('#sums').find('.qInvc-total-summe').text('0,00');
    $('#sums').find('.qInvc-total-brutto-summe').text('0,00');
    $('tr.invoiceTaxSums').remove();

    //reset the newContact Row
    $('#qinv_saveContactCheckbox').prop('checked', false);
    $('#qinv_saveContactHidden').val("false");
    $('#qinv_saveContactID').val('-1');
    $('#qinv_saveContactLabel').text('Save as new Contact?');
    $('#qinv_saveContactRow').hide();
    $('#qinv_saveContactCheckbox').val('empty');
    //if an paid invoice has been opened before, set all input fields enabled
    $('#invoiceForm   *').prop('disabled', false );
    
  })


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

  //Save existing Contacts for further usage
  let contactData = [];
  fetchContacts();
  /**
   * Load invoice Data by ajax and prepare form on success
   * @param {Invoice to be edited} invoiceId 
   */
  function editInvoice (invoiceId, duplicate = false) {
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
        
        if(duplicate){
          fetchLastInvoiceID()
        }else{
          writeInvoiceHeadertoFormField('#invoice_id', 'id')
        }
        
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
        if (obj[0][0]['paydate'] != '0000-00-00'){
          $('#invoice_form_paid_toggle').prop("checked", true);
        } else{
          $('#invoice_form_paid_toggle').prop("checked", false);
        }
        writeInvoiceHeadertoFormField('#loc_id', 'customerID')

        writeInvoiceDetailstoFormField('input.amountOfItems', 'amount', 0)
        writeInvoiceDetailstoFormField('input.itemDescription', 'description', 0)
        //write in formatted prices and discounts
        if($('#q-invoice-new-dot-dummy').text() == ','){
          var currencyPlanDE = removeCurrencySign(formatterDE.format(obj[1][0]['amount_plan']))
          $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemPrice').val(currencyPlanDE.slice(0, currencyPlanDE.length - 1))
          $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemDiscount').val(removeCurrencySign(formatterDE.format(obj[1][0]['discount'].replace(',', '.'))))
        } else{
          $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemPrice').val(removeCurrencySign(formatterEN.format(obj[1][0]['amount_plan'])))
          $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemDiscount').val(removeCurrencySign(formatterEN.format(obj[1][0]['discount'])))
        }
        writeInvoiceDetailstoFormField('select.discountType', 'discount_type', 0)

        //If the Tax has been set up in settings, it can be selected from the dropdown. If not or if the user has deleted it afterwards it will be shown by an extra options field
        var taxTypes = $('.itemTax').eq(0).find('option')
        var taxExists = false;
        for(i=0; i < taxTypes.length-2; i++){
          if(obj[1][0]['tax'] == taxTypes[i].value){
            taxExists = true;
          }
        }
        //if yes just enter the data; if not show the specific options field with row specific data (not saved in wp-settings)
        if(taxExists){
          writeInvoiceDetailstoFormField('select.itemTax', 'tax', 0);
          taxTypes[taxTypes.length-1].style.display = 'none';
        } else {
          taxTypes[taxTypes.length-1].style.display = 'block';
          taxTypes[taxTypes.length-1].value = obj[1][0]['tax'];
          taxTypes[taxTypes.length-1].innerText = obj[1][0]['tax'] + '%';
          taxTypes[taxTypes.length-1].selected = true;
        }
        
        for (let i = 1; i < obj[1].length; i++) {
          $('tr.wp-list-table-qInvcLine').eq(i - 1).clone().insertAfter($('tr.wp-list-table-qInvcLine').eq(i - 1))
          writeInvoiceDetailstoFormField('input.amountOfItems', 'amount', i)
          writeInvoiceDetailstoFormField('input.itemDescription', 'description', i)
          //write in formatted prices and discounts
          if($('#q-invoice-new-dot-dummy').text() == ','){
            var currencyPlanDE = removeCurrencySign(formatterDE.format(obj[1][i]['amount_plan']))
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemPrice').val(currencyPlanDE.slice(0, currencyPlanDE.length - 1))
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemDiscount').val(removeCurrencySign(formatterDE.format(obj[1][i]['discount'].replace(',', '.'))))
          } else{
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemPrice').val(removeCurrencySign(formatterEN.format(obj[1][i]['amount_plan'])))
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemDiscount').val(removeCurrencySign(formatterEN.format(obj[1][i]['discount'])))
          }
          writeInvoiceDetailstoFormField('select.discountType', 'discount_type', i)
          
          //If the Tax has been set up in settings, it can be selected from the dropdown. If not or if the user has deleted it afterwards it will be shown by an extra options field
          taxTypes = $('.itemTax').eq(i).find('option')
          taxExists = false;
          for(j=0; j < taxTypes.length-2; j++){
            if(obj[1][i]['tax'] == taxTypes[j].value){
              taxExists = true;
            }
          }
          //if yes just enter the data; if not show the specific options field with row specific data (not saved in wp-settings)
          if(taxExists){
            writeInvoiceDetailstoFormField('select.itemTax', 'tax', i);
            taxTypes[taxTypes.length-1].style.display = 'none';
          } else {
            taxTypes[taxTypes.length-1].style.display = 'block';
            taxTypes[taxTypes.length-1].value = obj[1][i]['tax'];
            taxTypes[taxTypes.length-1].innerText = obj[1][i]['tax'] + '%';
            taxTypes[taxTypes.length-1].selected = true;
          }
        }

        fetchInvoiceCurrency()
        recalcPos()
        recalcLineSum()
        recalcTotalSum()

        /*$('input.itemDiscount').each(function () {
          $(this).val(parseFloat($(this).val()).toFixed(2))
        })*/

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

        //prepare the newContact Row
        $('#qinv_saveContactCheckbox').prop('checked', false);
        $('#qinv_saveContactHidden').val("false");

        //check if Contact exists in Database
        var eI_isContact = false;
        for(i = 0; i < contactData[0].length; i++){
          //check for each contact if the full adress and name is like the data in the form
          if(contactData[0][i].street == obj[0][0]['street'] 
            && contactData[0][i].zip == obj[0][0]['zip'] 
            && contactData[0][i].city == obj[0][0]['city'] 
            && (contactData[0][i].company == obj[0][0]['company'] || contactData[0][i].firstname == obj[0][0]['firstname']))
          {
            eI_isContact = true;
            var eI_ID = contactData[0][i].id;
          }
        }
        //if there is a matching contact prepare for update, if not prepare for add new
        if(eI_isContact){
          $('#qinv_saveContactID').val(eI_ID);
          $('#qinv_saveContactCheckbox').val('old');
          $('#qinv_saveContactLabel').text('Update Contact?');
          $('#qinv_saveContactRow').hide();
          $('#qinv_saveContactCheckbox').prop('checked', true);
          $('#qinv_saveContactHidden').val("true");
        }else{
          $('#qinv_saveContactID').val("-1");
          $('#qinv_saveContactLabel').text('Save as new Contact?');
          $('#qinv_saveContactRow').css('display', 'table-row');
          $('#qinv_saveContactCheckbox').val('new');
        }
        if (obj[0][0].paydate == "0000-00-00" || duplicate){
          $('#invoiceForm   *').prop('disabled', false )
        }
        else {
          $('#invoiceForm   *').prop('disabled', true )
          $('#updateInvoice').css('display', 'none')
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log(errorThrown)
      }
    })
  }

  /**
   * Next two functions are copied from qinvice-invoice-autocomplete
   * calling the method using the other file has taken lots of time
   * @param {Contcts to parse} Contacts 
   */
  function storeContactData (Contacts) {
    contactData = JSON.parse(Contacts)
  }
  function fetchContacts () {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,

      data: {
        action: 'fetchContactsServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce
      },
      success: function (response, textStatus, XMLHttpRequest) {
        storeContactData(response)
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown)
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
    if ($(event.target).is('.columnEdit div > *')) { return }
    if ($(event.target).is('.columnStatusPaid')) { return }
    if ($(event.target).is('.columnStatusPaid > *')) { return }
    if ($(event.target).is('.qinv_mainDropdownElement')) { return }

    //check if clicked line is a cancelled invoice. On yes prevent form from setting paid status by removing the paid toggle in the form
    if($(this).hasClass('cancelled')){
      $('#heading-invoice').find('.switchForPaidStatus').hide()
    } else{
      $('#heading-invoice').find('.switchForPaidStatus').css('display', 'inline-block')
    }
    
    // Common Task for openning the invoice form
    reopenInvoiceForm()

    // Show the header for Editing only
    $('h2#formHeaderEdit').css('display', 'block')
    $('h2#formHeaderCreate').hide()
    // Show the button for Editing only
    $('#updateInvoice').css('display', 'inline')
    $('#saveInvoice').hide()
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
    //hold contact list up to date
    fetchContacts()
    q_invoice_RecalcSums(
      q_invoice_cleanUpNumber(row.find('td.columnTotal').text()),
      q_invoice_cleanUpNumber(row.find('td.columnNet').text()),
      0.0      
    );
  }

/**
 * Function to clean a String value retrieved from the main Invoice table. This should have the Pattern (N=Number, D=Decimal) NNN[.||,]NNN[.||,]DD[SPACE][€||$].
 * @return The number as a float value without Thousand dots or currency sign
 * @param {String from Invoice Main Page holding a number with Currency Sign like: 123.123,44[SPACE][Currency_Sign]} dirtyNumber 
 */
  function q_invoice_cleanUpNumber(dirtyNumber){
    var cleanNumber = 0.0
    dirtyNumber = dirtyNumber.toString()
    //remove space and currency Sign --> 123.123,44
    dirtyNumber = removeCurrencySign(dirtyNumber);
    //make all dot types to '.' --> 123.123.44
    dirtyNumber = dirtyNumber.replace(',', '.')
    //split the array and investigate the pattern of the number -- [123][123][44]
    var dirtyNumberArray = dirtyNumber.split('.')
    //min one dot seperator means min two array elements -->  thousands seperated by a dot or decimals seperated by a dot or both
    if(dirtyNumberArray.length > 1){
      //last array Element is a thousands seperated when it contains more than two digits or decimals seperated otherwise
      if(dirtyNumberArray[dirtyNumberArray.length - 1].length > 2){
        dirtyNumber = dirtyNumberArray[0]
        for(i = 1; i < dirtyNumberArray.length; i++){
          dirtyNumber = dirtyNumber + dirtyNumberArray[i]
        }
        cleanNumber = parseFloat(dirtyNumber)
      }else{
        dirtyNumber = dirtyNumberArray[0]
        for(i = 1; i < dirtyNumberArray.length - 1; i++){
          dirtyNumber = dirtyNumber + dirtyNumberArray[i]
        }
        dirtyNumber = dirtyNumber + '.' + dirtyNumberArray[dirtyNumberArray.length - 1]
        cleanNumber = parseFloat(dirtyNumber)
      }
    } else{
      cleanNumber = parseFloat(dirtyNumber)
    }
    return cleanNumber;
    
  }

  /**
   * Function to recalc the Sum in the last row for each of "All, Open, Dunning, Cancelled, Paid"
   * @param {*} modifiedTotal 
   * @param {*} modifiedNet 
   * @param {*} modifiedDun 
   */
  function q_invoice_RecalcSums(modifiedTotal, modifiedNet, modifiedDun){
    //Rows haben folgende Attribute: (paid edit active) || [(dunning edit || open edit) && (cancelled || active)]

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

    var allTotalRows = $(".q_invoice-content-row td.columnTotal span");
    var allNetRows = $(".q_invoice-content-row td.columnNet span");
    var allDunRows = $(".q_invoice-content-row td.columnDun span");

    var newOpenTotalNet = modifiedNet;
    var newOpenTotalTotal = modifiedTotal;
    var newOpenTotalDun = modifiedDun;
    for(var i = 0; i < openTotalRows.length; i++){
      newOpenTotalTotal = newOpenTotalTotal + q_invoice_cleanUpNumber(openTotalRows[i].innerHTML);
      newOpenTotalNet = newOpenTotalNet + q_invoice_cleanUpNumber(openNetRows[i].innerHTML);
      //newOpenTotalDun = newOpenTotalDun + q_invoice_cleanUpNumber(openDunRows[i].innerHTML);
    }

    var newCancelledTotalNet = 0.00;
    var newCancelledTotalTotal = 0.00;
    var newCancelledTotalDun = 0.00;
    for(var i = 0; i < cancelledTotalRows.length; i++){
      newCancelledTotalTotal = newCancelledTotalTotal + q_invoice_cleanUpNumber(cancelledTotalRows[i].innerHTML);
      newCancelledTotalNet = newCancelledTotalNet + q_invoice_cleanUpNumber(cancelledNetRows[i].innerHTML);
      //newCancelledTotalDun = newCancelledTotalDun + q_invoice_cleanUpNumber(cancelledDunRows[i].innerHTML);
    }

    var newDunningTotalNet = 0.00;
    var newDunningTotalTotal = 0.00;
    var newDunningTotalDun = 0.00;
    for(var i = 0; i < dunningTotalRows.length; i++){
      newDunningTotalTotal = newDunningTotalTotal + q_invoice_cleanUpNumber(dunningTotalRows[i].innerHTML);
      newDunningTotalNet = newDunningTotalNet + q_invoice_cleanUpNumber(dunningNetRows[i].innerHTML);
      //newDunningTotalDun = newDunningTotalDun + q_invoice_cleanUpNumber(dunningDunRows[i].innerHTML);
    }

    var newPaidTotalNet = 0.00;
    var newPaidTotalTotal = 0.00;
    var newPaidTotalDun = 0.00;
    for(var i = 0; i < paidTotalRows.length; i++){
      newPaidTotalTotal = newPaidTotalTotal + q_invoice_cleanUpNumber(paidTotalRows[i].innerHTML);
      newPaidTotalNet = newPaidTotalNet + q_invoice_cleanUpNumber(paidNetRows[i].innerHTML);
      //newPaidTotalDun = newPaidTotalDun + q_invoice_cleanUpNumber(paidDunRows[i].innerHTML);
    }

    var newAllTotalNet = modifiedNet;
    var newAllTotalDun = modifiedDun;
    var newAllTotalTotal = modifiedTotal;
    for(var i = 0; i < allTotalRows.length; i++){
      newAllTotalTotal = newAllTotalTotal + q_invoice_cleanUpNumber(allTotalRows[i].innerHTML);
      newAllTotalNet = newAllTotalNet + q_invoice_cleanUpNumber(allNetRows[i].innerHTML);
      //newAllTotalDun = newAllTotalDun + q_invoice_cleanUpNumber(allDunRows[i].innerHTML);
    }

    if($('#q-invoice-new-dot-dummy').text() == ','){
      $('#qi_totalSumNetto').html(removeCurrencySign(formatterDE.format(newAllTotalNet)) + ' ' + currencySign);
      $('#qi_openSumNetto').html(removeCurrencySign(formatterDE.format(newOpenTotalNet)) + ' ' + currencySign);
      $('#qi_cancelledSumNetto').html(removeCurrencySign(formatterDE.format(newCancelledTotalNet)) + ' ' + currencySign);
      $('#qi_dunningSumNetto').html(removeCurrencySign(formatterDE.format(newDunningTotalNet)) + ' ' + currencySign);
      $('#qi_paidSumNetto').html(removeCurrencySign(formatterDE.format(newPaidTotalNet)) + ' ' + currencySign);
      $('#qi_totalSumTotal').html(removeCurrencySign(formatterDE.format(newAllTotalTotal)) + ' ' + currencySign);
      $('#qi_openSumTotal').html(removeCurrencySign(formatterDE.format(newOpenTotalTotal)) + ' ' + currencySign);
      $('#qi_cancelledSumTotal').html(removeCurrencySign(formatterDE.format(newCancelledTotalTotal)) + ' ' + currencySign);
      $('#qi_dunningSumTotal').html(removeCurrencySign(formatterDE.format(newDunningTotalTotal)) + ' ' + currencySign);
      $('#qi_paidSumTotal').html(removeCurrencySign(formatterDE.format(newPaidTotalTotal)) + ' ' + currencySign);
      $('#qi_totalSumDunning').html(removeCurrencySign(formatterDE.format(newAllTotalDun)) + ' ' + currencySign);
      $('#qi_openSumDunning').html(removeCurrencySign(formatterDE.format(newOpenTotalDun)) + ' ' + currencySign);
      $('#qi_cancelledSumDunning').html(removeCurrencySign(formatterDE.format(newCancelledTotalDun)) + ' ' + currencySign);
      $('#qi_dunningSumDunning').html(removeCurrencySign(formatterDE.format(newDunningTotalDun)) + ' ' + currencySign);
      $('#qi_paidSumDunning').html(removeCurrencySign(formatterDE.format(newPaidTotalDun)) + ' ' + currencySign);
    } else{
      $('#qi_totalSumNetto').html(removeCurrencySign(formatterEN.format(newAllTotalNet)) + ' ' + currencySign);
      $('#qi_openSumNetto').html(removeCurrencySign(formatterEN.format(newOpenTotalNet)) + ' ' + currencySign);
      $('#qi_cancelledSumNetto').html(removeCurrencySign(formatterEN.format(newCancelledTotalNet)) + ' ' + currencySign);
      $('#qi_dunningSumNetto').html(removeCurrencySign(formatterEN.format(newDunningTotalNet)) + ' ' + currencySign);
      $('#qi_paidSumNetto').html(removeCurrencySign(formatterEN.format(newPaidTotalNet)) + ' ' + currencySign);
      $('#qi_totalSumTotal').html(removeCurrencySign(formatterEN.format(newAllTotalTotal)) + ' ' + currencySign);
      $('#qi_openSumTotal').html(removeCurrencySign(formatterEN.format(newOpenTotalTotal)) + ' ' + currencySign);
      $('#qi_cancelledSumTotal').html(removeCurrencySign(formatterEN.format(newCancelledTotalTotal)) + ' ' + currencySign);
      $('#qi_dunningSumTotal').html(removeCurrencySign(formatterEN.format(newDunningTotalTotal)) + ' ' + currencySign);
      $('#qi_paidSumTotal').html(removeCurrencySign(formatterEN.format(newPaidTotalTotal)) + ' ' + currencySign);
      $('#qi_totalSumDunning').html(removeCurrencySign(formatterEN.format(newAllTotalDun)) + ' ' + currencySign);
      $('#qi_openSumDunning').html(removeCurrencySign(formatterEN.format(newOpenTotalDun)) + ' ' + currencySign);
      $('#qi_cancelledSumDunning').html(removeCurrencySign(formatterEN.format(newCancelledTotalDun)) + ' ' + currencySign);
      $('#qi_dunningSumDunning').html(removeCurrencySign(formatterEN.format(newDunningTotalDun)) + ' ' + currencySign);
      $('#qi_paidSumDunning').html(removeCurrencySign(formatterEN.format(newPaidTotalDun)) + ' ' + currencySign);
    }
  }

  function formatDate (date) {
    const unformattedDate = new Date(date)
    const year = unformattedDate.getFullYear()
    let month = '0'
    let day = '0'

    if (parseInt(unformattedDate.getMonth()) < 10) {
      month = '0' + (unformattedDate.getMonth() + 1)
    } else {
      month = unformattedDate.getMonth() + 1
    }

    if (parseInt(unformattedDate.getDate()) < 10) {
      day = '0' + unformattedDate.getDate()
    } else {
      day = unformattedDate.getMonth()
    }

    return year + '-' + month + '-' + day
  }

  /**
   * Adds a new invoice Row in invoice main table
   * @param {Invoice Details of the new invoice} invoice 
   * @param {Id of the new Invoice} id 
   */
  function addNewInvoiceRow (invoice, id) {

    if ($('#q-invoice-new-readonly-dummy').text() === '0') {
      location.reload();
    } else{
      const clone = $('table#tableInvoices > tbody').find('tr').first().clone()
      clone.attr('id', 'edit-' + id)
      clone.attr('value', id)
      clone.find('td.columnInvoiceID span').text(id)
      clone.find('div.invoiceStatusIcon').removeClass('paid')
      clone.find('div.invoiceStatusIcon').addClass('open')
      if (invoice.company) {
        var pdfName = invoice.company;
        pdfName = pdfName.replace(/\/+/g, "_");
        pdfName = pdfName.replace(/\:+/g, "_");
        pdfName = pdfName.replace(/\?+/g, "_");
        pdfName = pdfName.replace(/\"+/g, "_");
        pdfName = pdfName.replace(/\<+/g, "_");
        pdfName = pdfName.replace(/\>+/g, "_");
        pdfName = pdfName.replace(/\|+/g, "_");
        pdfName = pdfName.replace(/\.+/g, "_");
        pdfName = pdfName.replace(/\s+/g, "");
        clone.find('td.columnName').text(invoice.company)
      } else {
        var pdfName = invoice.firstname + invoice.lastname;
        clone.find('td.columnName').text(invoice.firstname + ' ' + invoice.lastname)
      }

      clone.find('td.columnDescription').text(invoice.itemDescription[0])

      const date = invoice.dateOfInvoice
      // change to german date format
      const formattedDate = date.slice(8, 10) + '.' + date.slice(5, 7) + '.' + date.slice(0, 4)
      clone.find('td.columnDate').text(formattedDate)

      clone.find('td.columnNet').text($('.qInvc-total-summe').eq(0).text() + ' ' + currencySign)
      clone.find('td.columnTotal').text($('.qInvc-total-brutto-summe').eq(0).text() + ' ' + currencySign)
      document.getElementById('qi_totalSumTotal').value = document.getElementById('qi_totalSumTotal').value + parseInt($('.qInvc-total-brutto-summe').eq(0).text());
      document.getElementById('qi_totalSumTotal').innerHTML = document.getElementById('qi_totalSumNetto').value + ' ' + currencySign;

      clone.find('td.columnStatusPaid label input').prop('checked', false)

      //modify download button
      clone.find('a.download').attr('id', 'download-' + id)
      clone.find('a.download').attr('value', id)
      var datePieces = formattedDate.split('.');
      var datePDF = datePieces[2] + '_' + datePieces[1] + '_' + datePieces[0];
      clone.find('a.download').attr('href', '/wp-content/plugins/q_invoice/pdf/Invoice-' + invoice.prefix + id + '-' + pdfName + '-' + datePDF + '.pdf');

      clone.find('span.deleteRow').attr('id', id)
      clone.find('span.deleteRow').attr('value', id)
      clone.find('span.deleteRow').css('display', 'inline-block')

      clone.find('span.reactivateInvoice').css('display', 'none')

      clone.find('span.moreInvoiceOptions').attr('id', id)
      clone.find('li.duplicateInvoice').on('click', function(e){
        duplicateInvoice();
      })

      q_invoice_RecalcSums(
        q_invoice_cleanUpNumber(clone.find('td.columnTotal').text()),
        q_invoice_cleanUpNumber(clone.find('td.columnNet').text()),
        0.0
      );

      $('table#tableInvoices > tbody').prepend(clone)
      //keep invoice contacts up to date
      fetchContacts()
    }
  

  }

  function displaySuccess () {
    $('#wpbody-content').prepend(
      '<div class="qinvoiceMessage messageSuccess">' +
      '<span> Invoice succesfully saved! </span>' +
      '</div>')

    $('.messageSuccess').delay(1000).fadeOut(800)
  }

  function displayFail (details, duration) {
    $('#wpbody-content').prepend(
      '<div class="qinvoiceMessage messageFail">' +
      '<span> Something went wrong! <br>' + details + ' <br><br> Please refresh the page.</span>' +
      '</div>')

    $('.messageFail').delay(duration).fadeOut(800)
  }


  /**
   * Handle the Submit event by clicking on Save or Update
   */
  jQuery(document).ready(function ($) {
    $('#invoiceForm').ajaxForm({
      beforeSerialize: function($form, options) {
        $('#saveInvoice').attr('disabled', 'disabled');
      },
      success: function (response) {
        console.log (response)
        var serverResponse = JSON.parse(response).data
        var invoiceID = JSON.parse(response).id

        if (serverResponse.action === 'updateInvoiceServerSide') {
          changeUpdatedInvoiceRow(serverResponse)
        }
        if (serverResponse.action === 'saveInvoiceServerSide') {
          addNewInvoiceRow(serverResponse, invoiceID)
        }

        $('#invoiceOverlay').hide()

        // $('#invoiceForm').trigger('reset')
        $('#saveInvoice').prop('disabled', false);

        displaySuccess()
      },
      error: function (response){
        $('#saveInvoice').prop('disabled', false);
        $('#invoiceOverlay').hide();
        displayFail('Data has not been saved completely.', 5000);
      }
    })

    saveInvoiceNonces()

    checkIfBanksHaveBeenSetupinSettings()

    // Ajax Call for Invoice Data
    currencySign = '€'
    fetchInvoiceCurrency()

    $('.invoicePaid').hide()

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

    // Supress client-side error-functions on invalid form fields
    $('input').bind('invalid', function () { return false })

    // Prevent chrome to autofill&autocomplete
    $('#invoiceInputTables input').on('focus', function (e) {
      $(this).attr('autocomplete', 'off')
    })

    invoicesOnPage =999

    filterInvoices('all')
    
    
  })

  /**
   * Handle the requirements in the invoice form:
   * Either the company name is required or the First- and Lastname
   */
  jQuery(document).ready(function ($) {
    $('#company').on('blur', function(){
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
    $('.inputName').on('blur', function(){
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

  $('#invoice_form_paid_toggle').on('change', function(){
    //if invoice was not paid and is toggled to paid
    if (this.checked){

      var id = $('#invoice_id').val();

      $('#edit-'+id).find('.sliderForPayment').click();
      $('#invoiceForm   *').prop('disabled', true );
      $('#updateInvoice').css('display', 'none');
      
      /*setTimeout(function(){
        $('#invoiceOverlay').hide();
        $('#edit-'+id).find('.sliderForPayment').click();
      },800);*/

    } else if (!this.checked){

      var id = $('#invoice_id').val();

      $('#edit-'+id).find('.sliderForPayment').click();
      $('#invoiceForm   *').prop('disabled', false );
      $('#updateInvoice').css('display', 'block');
      
      

    }
  });

  /**
   * Function to set the "Save as new Contact" / "Update Contact on Save" row visible, which includes some text and a checkbox
   */

  $('.checkForModificationField').on('change', function(){
    if(!$(this).val()){
      var cFMFallEmpty = true;
      var all = $('.checkForModificationField').each(function(){
        if ($(this).val()){
          cFMFallEmpty = false;
        }
      });
      if(cFMFallEmpty){
        $('#qinv_saveContactRow').hide();
        $('#qinv_saveContactCheckbox').val('empty');
        $('#qinv_saveContactID').val('-1');
        $('#qinv_saveContactCheckbox').prop('checked', false);
        $('#qinv_saveContactHidden').val("false");
      } else if($('#qinv_saveContactCheckbox').val() == 'old'){
        $('#qinv_saveContactLabel').text('Update Contact?');
        $('#qinv_saveContactRow').css('display', 'table-row');
        $('#qinv_saveContactCheckbox').val('update');
        $('#qinv_saveContactCheckbox').prop('checked', true);
        $('#qinv_saveContactHidden').val("true");
      }
    } else{
      if ($('#qinv_saveContactCheckbox').val() == 'empty'){
        $('#qinv_saveContactLabel').text('Save as new Contact?');
        $('#qinv_saveContactRow').css('display', 'table-row');
        $('#qinv_saveContactCheckbox').val('new');
        $('#qinv_saveContactID').val('-1');
        $('#qinv_saveContactCheckbox').prop('checked', false);
        $('#qinv_saveContactHidden').val("false");
      } else if ($('#qinv_saveContactCheckbox').val() == 'old'){
        $('#qinv_saveContactLabel').text('Update Contact?');
        $('#qinv_saveContactRow').css('display', 'table-row');
        $('#qinv_saveContactCheckbox').val('update');
        $('#qinv_saveContactCheckbox').prop('checked', true);
        $('#qinv_saveContactHidden').val("true");
      }
    }
  });

  $('#qinv_saveContactCheckbox').click(function(){
    if($('#qinv_saveContactCheckbox').prop("checked")){
      $('#qinv_saveContactHidden').val("true");
    } else if(!$('#qinv_saveContactCheckbox').prop("checked")){
      $('#qinv_saveContactHidden').val("false");
    }
  });


  /**
   * Function to simulate a dynamic ID size depending on the ID length:
   * Each Number will receive 7px + 11px for the first
   */

   jQuery(document).ready(function ($) {
    
    var id_length = $("tbody tr:first td:first span").text().replace(/\s+/g, '').length;
    switch (id_length){
      case 1: var id_width = '15px';
        break;
      case 2: var id_width = '15px';
        break;
      case 3: var id_width = '21px';
        break;
      case 4: var id_width = '27px';
        break;
      case 5: var id_width = '34px';
        break;
      case 6: var id_width = '41px';
        break;
      case 7: var id_width = '48px';
        break;
      default:var id_width = '20px';
        break;
    }
  
    $(".q-invoice-page table#tableInvoices .columnInvoiceID").css("width", id_width);
    
   })

})
