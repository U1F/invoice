/* global q_invoice_ajaxObject, jQuery */
/* eslint no-undef: "error" */

jQuery(function ($) {
  const columnNetSum = $('span#columnNetSum')
  const columnTotalSum = $('span#columnTotalSum')

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

  $('.itemPrice').on('change', function(e){
    fillTargetValue(e.target);
  })

  $('.itemDiscount').on('change', function(e){
    fillTargetValue(e.target);
  })

  function fillTargetValue(thisTarget){

    var currentValue = parseFloat(thisTarget.value.replace(',','.')).toFixed(2);
    if($('#q-invoice-new-dot-dummy').text() == ','){
      var formattedValue = removeCurrencySign(formatterDE.format(currentValue));
    } else{
      var formattedValue = removeCurrencySign(formatterEN.format(currentValue));
    }
    thisTarget.value = formattedValue;

  }

  function moneySumToInt(moneySum){
    return parseInt(moneySum.replace(/[^0-9-]+/g,""))
  }


  function recalcNetSumsOfInvoices(){
    const nets = $('tbody tr:visible').find('.columnNet span.monetaryAmount').toArray()
    
    
    let netSum = 0
    nets.forEach(function(element) {
      netSum = netSum + moneySumToInt($(element).html())
    })  
  
    return netSum     
  }

  function recalcTotalSumsOfInvoices(){
    const tots = $('tbody tr:visible').find('.columnTotal span.monetaryAmount').toArray()
    
    
    let totSum = 0
    tots.forEach(function(element) {
      totSum = totSum + moneySumToInt($(element).html())
      
    })

    return totSum     
    
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
  //const q1nv0_invoiceTable = $("#tableInvoices")
  //const q1nv0_invoiceTableHeader = $("#tableInvoices thead")
  const q1nv0_invoiceTableBody = $("#tableInvoices tbody")
  
  let q1nv0_activeInvoiceTableFilter = "all"
  
  let q1nv0_activeYear = new Date().getFullYear()
  const q1nv0_activeYearButton = $('button.paginationButton#selectYearForPagination')
  
  function hidetableBodyContent(){

    $("#tableInvoices tbody tr.q_invoice-content-row").hide()

  }

  function recalcInvoiceTableSums() {

    const totalSums = recalcTotalSumsOfInvoices()
    const netSums = recalcNetSumsOfInvoices()
    
    const formattedTotalSum = removeCurrencySign( formatterDE.format( totalSums/100 ) ) + ' ' + currencySign
    const formattedNetSum = removeCurrencySign( formatterDE.format( netSums/100 ) ) + ' ' + currencySign
    
    $(columnTotalSum).html( formattedTotalSum)
    $(columnNetSum).html( formattedNetSum )
    
  }

  
  filterInvoicesByYear = (selectedYear) => {

    hidetableBodyContent()

    q1nv0_invoiceTableBody.find("td:contains(" + selectedYear +")").closest('tr').show()

    recalcInvoiceTableSums()

  }

  $('.operateOnYear').on('click', (event) => {
   
    const clickedButton = event.currentTarget
    const operateOnYear = $(clickedButton).html()

    if (operateOnYear === '+') { q1nv0_activeYear += 1 }
    
    if (operateOnYear === '-') { q1nv0_activeYear -= 1 }

    q1nv0_activeYearButton.html(q1nv0_activeYear)

    filterInvoices(q1nv0_activeInvoiceTableFilter)
  })


  function getRowNumber (eventsOriginalTarget) {

    return $(eventsOriginalTarget).closest('tr').attr('value')

  }

  
  function markInvoiceAsPaidInDB(clickedTarget){

    const data = { paydate: formatDate(new Date()) }

    updateInvoiceHeaderItem( getRowNumber('tr#'+clickedTarget), data )

  }

  function markInvoiceAsOpenInDB(clickedTarget){

    const data = { paydate: '' }

    updateInvoiceHeaderItem( getRowNumber('tr#'+clickedTarget), data )

  }

  /**
   * Change the invoce Row AND database entry to paid status
   *
   * @param {string} x The row id to make changes.
   */
  function setInvoiceToPaid (clickedTarget){
    const invoiceRow = $('tr#'+clickedTarget)
    const invoiceStatusIcon = invoiceRow.find('.invoiceStatusIcon')
    
    // and mark that row as paid instead of open
    invoiceRow.removeClass('open')
    invoiceStatusIcon.removeClass('open')
    
    invoiceRow.addClass('paid')
    invoiceStatusIcon.addClass('paid') 

    // .. set a paydate to mark as paid in database
    markInvoiceAsPaidInDB(clickedTarget)

    //hide dunning circle
    invoiceRow.find('.columnDunning').find('.longCircle').fadeOut()
  }

  /**
   * Change the invoce Row AND database entry to open (unpaid) status
   *
   * @param {string} x The row id to make changes.
   */
  function setInvoiceToUnpaid (clickedTarget){
    const invoiceRow = $('tr#'+clickedTarget)
    const invoiceStatusIcon = invoiceRow.find('.invoiceStatusIcon')
    const dunningType = invoiceRow.find('td.columnDunning').attr('value')
    const dunningDays = invoiceRow.find('td.columnDunning span').attr('value')
    
    invoiceRow.removeClass('paid')
    invoiceStatusIcon.removeClass('paid')
    
    invoiceRow.addClass('open')
    invoiceStatusIcon.addClass('open')

    // Paid Invoices are not to be deleted    
    //invoiceRow.find('.columnEdit').find('.delete').css('color', '#50575e') // do this within a class?
    //invoiceRow.find('.columnEdit').find('.delete').addClass('deleteRow')
    
    invoiceRow.find('td.columnDunning span').removeClass()
    invoiceRow.find('td.columnDunning span').addClass('longCircle ' + dunningType)
    
    invoiceRow.find('td.columnDunning span').text(dunningDays)
    invoiceRow.find('td.columnDunning span').fadeIn()
    
    // remove paydate, mark as open in database
    markInvoiceAsOpenInDB(clickedTarget)
  }
 
  
  /**
   * Clicking on the slider Paid/Unpaid changes UI functionality 
   * and updates database
   *
   * @param {event} x We use the target of the event to make changes 
   * on the row that got clicked
   */
  $('#tableInvoices').on('click', '.sliderForPayment', function (event) {

    // We want to click the button from within the confirm dialog in a purely cosmetic sense:
    if ($("#reopenPaidInvoiceWithinForm").is(":visible")) {return}
    if ($("#reopenPaidInvoice").is(":visible")) {return}
    
    const clickTarget = event.currentTarget
    const sliderBox = $(clickTarget).parent()
    const invoiceRow = $(clickTarget).closest('tr')
    
    // We do not want to make it possible for cancelled invoices
    if(invoiceRow.hasClass('cancelled')){
      return;
    }

    // For already paid invoices a dialoge pops up to ask if the invoice should be reverted to open
    if (sliderBox.find('input').prop('checked') === false) {

      setInvoiceToPaid(invoiceRow.attr('id'))        

    } else {

      // This is a workaround for using "promises"
      $("#lastClickedInvoice").val($(clickTarget).closest("tr").attr('id'))

      // Oprn Dialog
      $("#reopenPaidInvoice").show()

      // We want to postpone the change on the slider until user decide what they want to do in the popup
      event.preventDefault()

    }

    recalcInvoiceTableSums()

  })
  /**
   * Escape key closes Overlay
   * 
   * @param {event} x We check if Escape has been pressed
   */
  $(document).on('keydown', function (e) {

    if (e.keyCode === 27) {

      if($('#qinv_mail-popup').css('display') == 'block'){

        $('#qinv_mail-popup').hide()

      }

      if ($('.dialogOverlay').css('display') === 'block') {

        $('.dialogOverlay').hide()

      } else {

        $('#invoiceOverlay').hide()

      }
    }
  })

  /**
   * Clicking outside of the form or cancel button also closes Overlay
   * 
   * @param {event} x We check if the user clicked outside the form 
   * or the cancel button
   */
   $('#qinv_mail-popup').on( 'click', function (event) {
    
    if ($(event.target).is('.overlay')) {

      $('#qinv_mail-popup').hide()

    }
    if ($(event.target).is('.cancelButton')) {

      $('#qinv_mail-popup').hide()

    }

  })

  
  /**
   * Clicking outside of the form or cancel button also closes Overlay
   * 
   * @param {event} x We check if the user clickd outside the form 
   * or the cancel button
   */
  $('#invoiceOverlay').on('click', function (event) {

    if ($(event.target).is('.overlay')) {

      $('#invoiceOverlay').hide()

    }
    if ($(event.target).is('.cancelButton')) {

      $('#invoiceOverlay').hide()

    }
  })

  /**
   * Clicking outside of the dialog or closes it
   * 
   * @param {event} x We check if the user clicked outside the form 
   */
  $('.dialogOverlay').on('click', function (event) {

    if ($(event.target).is('.overlay')) {

      $('.dialogOverlay').hide()

    }
  })

  /**
   * Clicking "OK" in the dialog re-opens the invoice
   */
  $("#reopenPaidInvoice").on("click", ".submitButton", function(){
    const currentRow = $("#reopenPaidInvoice #lastClickedInvoice").val()
    setInvoiceToUnpaid(currentRow)
    
    $('#'+currentRow).find('td.columnStatusPaid input').prop('checked', false)
    $("#reopenPaidInvoice").hide()
    displaySuccess("Old Inoice Data sucessfully saved")
    
  })
  /**
   * Clicking "Cancel" in the dialog just hides it
   */
  $("#reopenPaidInvoice").on("click", ".cancelButton", function(){
    $("#reopenPaidInvoice").hide()
  })


  /**
   * Clicking "OK" in the dialog re-opens the invoice
   */
  $("#reopenPaidInvoiceWithinForm").on("click", ".submitButton", function(){
    const currentRow = $("#reopenPaidInvoiceWithinForm #lastOpenedInvoiceWithinForm").val()
    // In this case we have to prepend 'edit-' because setInvoiceToPaid expects it.
    setInvoiceToUnpaid('edit-'+currentRow)
    $('#edit-'+currentRow).find('td.columnStatusPaid input').prop('checked', false)
    $('#invoice_form_paid_toggle').prop('checked', false)
    $("#reopenPaidInvoiceWithinForm").hide()
    displaySuccess ("Old Invoice Data saved!")
    
  })
  
  /**
   * Clicking "Cancel" in the dialog just hides it
   */
  $("#reopenPaidInvoiceWithinForm").on("click", ".cancelButton", function(){
    $("#reopenPaidInvoiceWithinForm").hide()
  })

  /**
   * Helper function that sets the class of the pressed button to active
   *  
   * @param {object} x We submit the target to change 
   */
  function setFilterButtonActive (target) { 

    target.attr('class', 'filterButton active')

  }


   /**
   * Helper function that sets the class of the pressed button to inactive
   *  
   * @param {object} x We submit the target to change 
   */
  function setFilterButtonInactive (target) { 

    target.attr('class', 'filterButton inactive')

  }


  /**
   * Helper function that either shows or hides the switch to mark invocies as paid
   * @param {boolean} x True=Show, False=Hide 
   */
  function showPayToggle(doShow){
    if (doShow) {
      $('.switch').show()
    }
    else {
      $('.switch').hide()
    }
  }

  /**
   * Helper function that either shows or hides the button to mark invoices as deactivated/archived
   * @param {boolean} x True=Show, False=Hide 
   */
  function showDeleteButton(doShow){
    if (doShow) {
      $('.delete').show()
    }
    else {
      $('.delete').hide()
    }
  }

  /**
   * Helper function that either shows or hides the button to mark invoices as active/open
   * @param {boolean} x True=Show, False=Hide 
   */
  function showReactivationButton(doShow){
    if (doShow) {
      $('.reactivateInvoice').show()
    }
    else {
      $('.reactivateInvoice').hide()
    }
  }

  
  /**
   * Filters invoices and changes UI to that view
   * @param {string} x We submit the target to change 
   */
  function filterInvoices(invoiceCategory){
    // First we hide everything
    hidetableBodyContent()
    
    //$('#q_invoice_totalSums span').hide()
    showDeleteButton(false)
    showReactivationButton(false)
    showPayToggle(false)
    const dunningInvoices = q1nv0_invoiceTableBody.find('tr.dunning')

    switch (invoiceCategory){
      case "all":
        showDeleteButton(true)
        showPayToggle(true)
        q1nv0_invoiceTableBody.find("td:contains(" + q1nv0_activeYear +")").closest('tr').show();
        q_invoice_modify_cancelled_reactivation_icon();
        break

      case "open":
        showDeleteButton(true)
        showReactivationButton(true)
        showPayToggle(true)
        // The next 2 need work:
        
        const openInvoices = q1nv0_invoiceTableBody.find('tr.open')
        openInvoices.find("td:contains(" + q1nv0_activeYear +")").parent().show()
        dunningInvoices.find("td:contains(" + q1nv0_activeYear +")").parent().show()
        
        break

      case "cancelled":
        showReactivationButton(true)
        showPayToggle(true)

        const cancelledInvoices = q1nv0_invoiceTableBody.find('tr.cancelled')
        cancelledInvoices.find("td:contains(" + q1nv0_activeYear +")").parent().show()
        
        break

      case "dunning":
        showDeleteButton(true)
        showReactivationButton(true)
        showPayToggle(true)

        
        dunningInvoices.find("td:contains(" + q1nv0_activeYear +")").parent().show()
        
        break

      case "paid":
        showPayToggle(true)

        const paidInvoices = q1nv0_invoiceTableBody.find('tr.paid')
        paidInvoices.find("td:contains(" + q1nv0_activeYear +")").parent().show()
        
        break
    }
    
    
    recalcInvoiceTableSums()
    


  }
  /*
   * This modifies cancelled reactivation icons?!?
   */
  function q_invoice_modify_cancelled_reactivation_icon(){
    $('tr.cancelled').find('.dashicons-no').hide();
    $('tr.cancelled').find('.dashicons-undo').css('display', 'inline-block');
  }

  // Manage UI visibility of filter buttons mobile version
  $('#mobileFilterButtonsDropdown').on('change', function (event) {

    const filterType = $('#mobileFilterButtonsDropdown option:selected').val()

    filterInvoices(filterType)

    q1nv0_activeInvoiceTableFilter = filterType

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
      q1nv0_activeInvoiceTableFilter = 'all'
      filterInvoices('all')
    }

    if ($(event.target).parent().attr('id') === 'showOpenInvoices') {
      q1nv0_activeInvoiceTableFilter = 'open'
      filterInvoices('open')
    }

    if ($(event.target).parent().attr('id') === 'showCancelledInvoices') {
      q1nv0_activeInvoiceTableFilter = 'cancelled'
      filterInvoices('cancelled')
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesWithDunning') {
      q1nv0_activeInvoiceTableFilter = 'dunning'
      filterInvoices('dunning')
    }

    if ($(event.target).parent().attr('id') === 'showInvoicesPaid') {
      q1nv0_activeInvoiceTableFilter = 'paid'
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
  $('#qInvc-add-line').on('click', function (e) {
    e.preventDefault()

    const Row = $('.wp-list-table-qInvcLine:first')
    const Clone = Row.clone()
  
    Clone.find('input:text').val('')
    Clone.find('input').val('')
    Clone.find('input.position').val(parseInt($('.wp-list-table-qInvcLine:first').find('input.position')) + 1)
    Clone.find('input.insertInDatabase').val('1')
    Clone.find('input.positionTypeIsDunning').val('0')
    Clone.find('input.invoiceTax').val(Clone.find('select.itemTax option:selected').val())
    Clone.find('input.invoiceTotal').val(Clone.find('select.itemTax option:selected').val())
    Clone.find('select.itemTax').val(Row.find('select.itemTax').val())
    Clone.find('input.invoicepositionHidden').val(Clone.find('.invoiceItemsNo > span').text())
    // Clone.find('span.qInvcLine-total').text('');
    // Clone.find('.invoiceItemsTotal nobr').html('<span class="qInvcLine-total"></span>');
    Clone.find('.invoiceItemsTotal nobr').hide()
    
    if($('#q-invoice-new-dot-dummy').text() == ','){
      Clone.find('.itemDiscount').val(removeCurrencySign(formatterDE.format(parseFloat(0.00).toFixed(2))));
    } else{
      Clone.find('.itemDiscount').val(removeCurrencySign(formatterEN.format(parseFloat(0.00).toFixed(2))));
    }
    Clone.find('.itemPrice').on('change', function(e){fillTargetValue(e.target)})
    Clone.find('.itemDiscount').on('change', function(e){fillTargetValue(e.target)})

    Clone.insertAfter(Row)

    recalcPos()
  })

  // Remove invoice item
  $('.qInvc-table').eq(0).on('click', '.qInvc-delete-line', function (e) {
    e.preventDefault()

    const parent = $(this).parent().parent()

    if ($('.wp-list-table-qInvcLine').length > 1) {
      if(parent.attr('id') == ('editInvoiceReminderRow') || parent.attr('id') == ('editInvoiceDunningIRow') || parent.attr('id') == ('editIneditInvoiceDunningIIRowvoiceReminderRow')){
        parent.removeClass('wp-list-table-qInvcLine');
        parent.find('.insertInDatabase').attr('value', '0');
      } else{
        parent.remove()
      }
      
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
    $('.qinv_moreOptionsDropdownBox').css('display', 'none')
    if($(this).parent().parent().find('div.qinv_moreOptionsDropdownBox').css('display') == 'block'){
      $(this).parent().parent().find('div.qinv_moreOptionsDropdownBox').css('display', 'none')
    } else{
      $(this).parent().parent().find('div.qinv_moreOptionsDropdownBox').css('display', 'block')
      currentInvoiceID = event.target.id
    }
  })

  /**
   * Close Dropdown when clicked anywhere but on the Dropdown itself
   */
  $(document).on('mouseup', function(e){
    if(!$('div.qinv_moreOptionsDropdownBox').is(e.target) && $('div.qinv_moreOptionsDropdownBox').has(e.target).length === 0){
      //return if clcked on ellipsis icon to close the dropdown on second click
      if($(e.target).hasClass('moreInvoiceOptions')){return;}
      $('div.qinv_moreOptionsDropdownBox').css('display', 'none');
    }
  })

  /**
   * Open an edit invoice form but add the reminder row. When this form is submitted the ivoice is now in state dunning.
   */
   $('.reminderRow').on('click', function (event) {

    openReminderPopup(event);

  })

  function openReminderPopup(event){
    if ($(event.target).is('.listPointerEventsMod')) { return }
    if ($(event.target).is('.downloadReminder')) { return }
    if ($(event.target).is('.mailReminder')) { return }
    if ($(event.target).is('.downloadReminder > *')) { return }
    if ($(event.target).is('.mailReminder > *')) { return }
    if ($(this).find('div').is('.listPointerEventsMod')) { return }

    $('#popupFormType').val('reminder');

    //close the dropdown
    $('div.qinv_moreOptionsDropdownBox').css('display', 'none');

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

    editInvoice(currentInvoiceID, false, 'rem');
  }

  /**
   * Open an edit invoice form but add the dunning1 and reminder row. When this form is submitted the ivoice is now in state dunning.
   */
   $('.dunningIRow').on('click', function (event) {

    openDunningIPopup(event);

  })

  function openDunningIPopup(event){

    if ($(event.target).is('.listPointerEventsMod')) { return }
    if ($(event.target).is('.downloadDunningI')) { return }
    if ($(event.target).is('.mailDunningI')) { return }
    if ($(this).find('div').is('.listPointerEventsMod')) { return }

    //close the dropdown
    $('div.qinv_moreOptionsDropdownBox').css('display', 'none');

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

    editInvoice(currentInvoiceID, false, 'd1');

  }

  /**
   * Open an edit invoice form but add the dunning 2 and dunning1 and reminder row. When this form is submitted the ivoice is now in state dunning.
   */
   $('.dunningIIRow').on('click', function (event) {

    openDunningIIPopup(event);

  })

  function openDunningIIPopup(event){

    if ($(event.target).is('.listPointerEventsMod')) { return }
    if ($(event.target).is('.downloadDunningII')) { return }
    if ($(event.target).is('.mailDunningII')) { return }
    if ($(this).find('div').is('.listPointerEventsMod')) { return }

    //close the dropdown
    $('div.qinv_moreOptionsDropdownBox').css('display', 'none');

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

    editInvoice(currentInvoiceID, false, 'd2');
    
  }

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
    editInvoice(currentInvoiceID, true, '')
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
    targetRow.find('.columnDunning').find('.longCircle').css('display', 'inline-block')
    targetRow.find('td.columnDunning span').removeClass()
    targetRow.find('td.columnDunning span').addClass('longCircle ' + targetRow.find('td.columnDunning').attr('value'))
    targetRow.find('td.columnDunning span').text(targetRow.find('td.columnDunning span').attr('value'))
    
    recalcInvoiceTableSums()
    
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
    targetRow.find('.columnDunning').find('.longCircle').css('display', 'none')
    
    recalcInvoiceTableSums()
    
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
  $('#newInvoice').on('click', function () {
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

    $('input.itemDiscount').val()
    if($('#q-invoice-new-dot-dummy').text() == ','){
      $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemDiscount').val(removeCurrencySign(formatterDE.format(0.0)))
    } else{
      $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemDiscount').val(removeCurrencySign(formatterEN.format(0.0)))
    }

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

    //hide dunning rows
    $('#editInvoiceReminderRow').removeClass('wp-list-table-qInvcLine');
    $('#editInvoiceDunningIRow').removeClass('wp-list-table-qInvcLine');
    $('#editInvoiceDunningIIRow').removeClass('wp-list-table-qInvcLine');
    
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
  function editInvoice(invoiceId, duplicate = false, dunning = '') {
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
        $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemDiscount').val(removeCurrencySign(formatterEN.format(0.00)))
        if($('#q-invoice-new-dot-dummy').text() == ','){
          $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemPrice').val(removeCurrencySign(formatterDE.format(parseFloat(obj[1][0]['amount_plan'].replace(',','.')).toFixed(2))))
          $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemDiscount').val(removeCurrencySign(formatterDE.format(obj[1][0]['discount'].replace(',', '.'))))
        } else{
          $('tr.wp-list-table-qInvcLine').eq(0).find('input.itemPrice').val(removeCurrencySign(formatterEN.format(parseFloat(obj[1][0]['amount_plan'].replace(',','.')).toFixed(2))))
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
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemDiscount').val(removeCurrencySign(formatterDE.format(0.00)))
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemPrice').val(removeCurrencySign(formatterDE.format(parseFloat(obj[1][i]['amount_plan'].replace(',','.')).toFixed(2))))
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemDiscount').val(removeCurrencySign(formatterDE.format(obj[1][i]['discount'].replace(',', '.'))))
          } else{
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemDiscount').val(removeCurrencySign(formatterEN.format(0.00)))
            $('tr.wp-list-table-qInvcLine').eq(i).find('input.itemPrice').val(removeCurrencySign(formatterEN.format(parseFloat(obj[1][i]['amount_plan'].replace(',','.')).toFixed(2))))
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

        /*
        * Show the reminder / dunningI / dunningII row only if the dunning button is clicked or has already been clicked
        */
        var currInvoiceID = '#edit-' + obj[1][0]['invoice_id']
        //retrieve dunning fee from database if exist
        if($(currInvoiceID).find('#q_invc_reminderActiveVal').attr('value') == '1'){
          $('#editInvoiceReminderRow').find('.invoiceItemsPrice input').val($(currInvoiceID).find('#q_invc_reminderValue').attr('value'));
        }
        if($(currInvoiceID).find('#q_invc_dunningIActiveVal').attr('value') == '1'){
          $('#editInvoiceDunningIRow').find('.invoiceItemsPrice input').val($(currInvoiceID).find('#q_invc_dunningIValue').attr('value'));
        }
        if($(currInvoiceID).find('#q_invc_dunningIIActiveVal').attr('value') == '1'){
          $('#editInvoiceDunningIIRow').find('.invoiceItemsPrice input').val($(currInvoiceID).find('#q_invc_dunningIIValue').attr('value'));
        }

        //show specific dunning rows if dunning has been activated or dunning edit button is clicked
        $('#editInvoiceReminderRow').removeClass('wp-list-table-qInvcLine');
        $('#editInvoiceDunningIRow').removeClass('wp-list-table-qInvcLine');
        $('#editInvoiceDunningIIRow').removeClass('wp-list-table-qInvcLine');
        $('#editInvoiceDunningIIRow').find('.insertInDatabase').attr('value', '0');
        $('#editInvoiceDunningIRow').find('.insertInDatabase').attr('value', '0');
        $('#editInvoiceReminderRow').find('.insertInDatabase').attr('value', '0');
        if($(currInvoiceID).find('#q_invc_dunningIIActiveVal').attr('value') == '1' || (dunning == 'd2')){
          $('#editInvoiceDunningIIRow').addClass('wp-list-table-qInvcLine');
          $('#editInvoiceDunningIRow').addClass('wp-list-table-qInvcLine');
          $('#editInvoiceReminderRow').addClass('wp-list-table-qInvcLine');
          $('#editInvoiceDunningIIRow').find('.insertInDatabase').attr('value', '1');
          $('#editInvoiceDunningIRow').find('.insertInDatabase').attr('value', '1');
          $('#editInvoiceReminderRow').find('.insertInDatabase').attr('value', '1');
        } else if($(currInvoiceID).find('#q_invc_dunningIActiveVal').attr('value') == '1' || (dunning == 'd2' || dunning == 'd1')){
          $('#editInvoiceDunningIRow').addClass('wp-list-table-qInvcLine');
          $('#editInvoiceReminderRow').addClass('wp-list-table-qInvcLine');
          $('#editInvoiceDunningIRow').find('.insertInDatabase').attr('value', '1');
          $('#editInvoiceReminderRow').find('.insertInDatabase').attr('value', '1');
        } else if($(currInvoiceID).find('#q_invc_reminderActiveVal').attr('value') == '1' || (dunning == 'd2' || dunning == 'd1' || dunning == 'rem')){
          $('#editInvoiceReminderRow').addClass('wp-list-table-qInvcLine');
          $('#editInvoiceReminderRow').find('.insertInDatabase').attr('value', '1');
        }

        fetchInvoiceCurrency()
        recalcPos()
        recalcLineSum()
        recalcTotalSum()

        //set required fields
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


        //disable form if dunning has been activated or the invoice is paied
        if(obj[0][0].paydate != "0000-00-00" || $('#edit-'+invoiceId).find('div.invoiceStatusIcon').is('.reminder') || $('#edit-'+invoiceId).find('div.invoiceStatusIcon').is('.dunningI') || $('#edit-'+invoiceId).find('div.invoiceStatusIcon').is('.dunningII')){
          disableInvoiceForm();
          $('#dunningWarning').show();
        } else{
          enableInvoiceForm();
          $('#dunningWarning').hide();
        }
        if(duplicate){
          enableInvoiceForm();
        }

        //dunning edit button has been clicked
        if(dunning == 'rem'){
          $('#popupFormType').val('reminder');
          if(!$('#edit-'+invoiceId).find('div.invoiceStatusIcon').is('.dunningI') && !$('#edit-'+invoiceId).find('div.invoiceStatusIcon').is('.dunningII')){
            enableInvoiceForm();
          }
        } else if(dunning == 'd1'){
          if(!$('#edit-'+invoiceId).find('div.invoiceStatusIcon').is('.dunningII')){
            enableInvoiceForm();
          }
          $('#popupFormType').val('dunningI');
        } else if(dunning == 'd2'){
          enableInvoiceForm();
          $('#popupFormType').val('dunningII');
        } else{
          $('#popupFormType').val('invoice');
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
    $('.wp-list-table-qInvcLine.invoiceSpecificRow:not(:first)').remove()
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
    if ($(event.target).is('.columnEdit div ul li > *')) { return }
    if ($(event.target).is('.columnEdit div ul li div > *')) { return }
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

    var invoiceId = jQuery(this).attr('id').split('-')[1];
    
    // fetch id from span attribute id="edit-n", where  n = id of invoice
    editInvoice(invoiceId, false, '')
  })

  function changeUpdatedInvoiceRow (invoice, dunningData) {
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
    
    //modify dunning
    //dunning status circle
    row.find('td.columnDunning span').removeClass()
    row.find('td.columnDunning span').addClass('longCircle ' + dunningData[0])
    row.find('td.columnDunning span').text(dunningData[1] + ' days')
    //dunning dropdown values
    var numberOfItems = invoice.itemPrice.length
    row.find('#q_invc_reminderValue').attr('value', invoice.itemPrice[numberOfItems - 3])
    row.find('#q_invc_dunningIValue').attr('value', invoice.itemPrice[numberOfItems - 2])
    row.find('#q_invc_dunningIIValue').attr('value', invoice.itemPrice[numberOfItems - 1])
    row.find('#q_invc_reminderActiveVal').attr('value', invoice.insertInDatabase[numberOfItems - 3])
    row.find('#q_invc_dunningIActiveVal').attr('value', invoice.insertInDatabase[numberOfItems - 2])
    row.find('#q_invc_dunningIIActiveVal').attr('value', invoice.insertInDatabase[numberOfItems - 1])
    //dunning dropdown download status
    if(invoice.insertInDatabase[numberOfItems - 3] == '1'){
      row.find('.downloadReminder').removeClass('iconInactiveColor');
    } else {
      row.find('.downloadReminder').addClass('iconInactiveColor');
    }
    if(invoice.insertInDatabase[numberOfItems - 2] == '1'){
      row.find('.downloadDunningI').removeClass('iconInactiveColor');
    } else {
      row.find('.downloadDunningI').addClass('iconInactiveColor');
    }
    if(invoice.insertInDatabase[numberOfItems - 1] == '1'){
      row.find('.downloadDunningII').removeClass('iconInactiveColor');
    } else {
      row.find('.downloadDunningII').addClass('iconInactiveColor');
    }

    
    //activate mail when pdf is generated
    if(dunningData[2] == 'dunningII'){
      row.find('td.columnEdit .mailDunningII').removeClass('deactivatedMail');
      row.find('div.invoiceStatusIcon').addClass('dunningII')
      row.find('div.invoiceStatusIcon').removeClass('dunningI')
      row.find('div.invoiceStatusIcon').removeClass('reminder')
      row.find('div.invoiceStatusIcon').removeClass('open')
    }
    if(dunningData[2] == 'dunningI'){
      row.find('td.columnEdit .mailDunningI').removeClass('deactivatedMail');
      row.find('div.invoiceStatusIcon').addClass('dunningI')
      row.find('div.invoiceStatusIcon').removeClass('dunningII')
      row.find('div.invoiceStatusIcon').removeClass('reminder')
      row.find('div.invoiceStatusIcon').removeClass('open')
    }
    if(dunningData[2] == 'reminder'){
      row.find('td.columnEdit .mailReminder').removeClass('deactivatedMail');
      row.find('div.invoiceStatusIcon').addClass('reminder')
      row.find('div.invoiceStatusIcon').removeClass('dunningI')
      row.find('div.invoiceStatusIcon').removeClass('dunningII')
      row.find('div.invoiceStatusIcon').removeClass('open')
    }


    const date = invoice.dateOfInvoice
    // change to german date format
    const formattedDate = date.slice(8, 10) + '.' + date.slice(5, 7) + '.' + date.slice(0, 4)
    row.find('td.columnDate').text(formattedDate)
    $('table#tableInvoices tr:last').remove()
    $('table#tableInvoices tr:last').after(sumRowBackup)
    //hold contact list up to date
    fetchContacts()
    recalcInvoiceTableSums()
    
  }

  /*function getWorkingDays(startDate, endDate) {
      let count = 0;
      var currentDate = new Date(startDate.getTime());
      while (currentDate <= endDate) {
          const dayOfWeek = currentDate.getDay();
          if(dayOfWeek !== 0 && dayOfWeek !== 6) count++;
          currentDate.setDate(currentDate.getDate() + 1);
      }
      console.log(count);
      return count;
  }*/

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


  function formatDate (date) {
    const unformattedDate = new Date(date)
    const year = unformattedDate.getFullYear()
    let month = '0'
    let day = '0'

    if (parseInt(unformattedDate.getMonth()) < 10) {
      month = '0' + parseInt(unformattedDate.getMonth() + 1)
    } else {
      month = unformattedDate.getMonth() + 1
    }
    if (parseInt(unformattedDate.getDate()) < 10) {
      day = '0' + parseInt(unformattedDate.getDate())
    } else {
      day = parseInt(unformattedDate.getDate())
    }
    return year + '-' + month + '-' + day
  }

  /**
   * Adds a new invoice Row in invoice main table
   * @param {Invoice Details of the new invoice} invoice 
   * @param {Id of the new Invoice} id 
   * @param {Array with Dunning Class and Days, calculated server side} dunningData
   */
  function addNewInvoiceRow (invoice, id, dunningData) {

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
      //document.getElementById('qi_totalSumTotal').value = document.getElementById('qi_totalSumTotal').value + parseInt($('.qInvc-total-brutto-summe').eq(0).text());
      //document.getElementById('qi_totalSumTotal').innerHTML = document.getElementById('qi_totalSumNetto').value + ' ' + currencySign;
      recalcInvoiceTableSums()
      clone.find('td.columnStatusPaid label input').prop('checked', false)

      //modify download button
      clone.find('a.download').attr('id', 'download-' + id)
      clone.find('a.download').attr('value', id)
      var datePieces = formattedDate.split('.');
      var datePDF = datePieces[2] + '_' + datePieces[1] + '_' + datePieces[0];
      clone.find('a.downloadInvoice').attr('href', '/wp-content/plugins/q_invoice/pdf/Invoice-' + invoice.prefix + id + '-' + pdfName + '-' + datePDF + '.pdf');
      clone.find('a.downloadReminder').attr('href', '/wp-content/plugins/q_invoice/pdf/Invoice-' + invoice.prefix + id + '-' + pdfName + '-' + datePDF + '-reminder1.pdf');
      clone.find('a.downloadDunningI').attr('href', '/wp-content/plugins/q_invoice/pdf/Invoice-' + invoice.prefix + id + '-' + pdfName + '-' + datePDF + '-reminder2.pdf');
      clone.find('a.downloadDunningII').attr('href', '/wp-content/plugins/q_invoice/pdf/Invoice-' + invoice.prefix + id + '-' + pdfName + '-' + datePDF + '-reminder3.pdf');

      clone.find('span.deleteRow').attr('id', id)
      clone.find('span.deleteRow').attr('value', id)
      clone.find('span.deleteRow').css('display', 'inline-block')

      clone.find('span.reactivateInvoice').css('display', 'none')

      clone.find('span.moreInvoiceOptions').attr('id', id)
      clone.find('li.duplicateInvoice').on('click', function(e){
        duplicateInvoice();
      })

      //add dunning
      //add dunning status
      clone.find('td.columnDunning span').removeClass()
      clone.find('td.columnDunning span').addClass('longCircle ' + dunningData[0])
      clone.find('td.columnDunning span').text(dunningData[1] + ' days')
      //deactivate and activate dunning in dropwdown
      clone.find('td.columnEdit li#q_invc_reminderValue').addClass('deactivatedListElement');
      clone.find('td.columnEdit li#q_invc_dunningIValue').addClass('deactivatedListElement');
      clone.find('td.columnEdit li#q_invc_dunningIIValue').addClass('deactivatedListElement');
      if(dunningData[0] == 'reminder'){
        clone.find('td.columnEdit li#q_invc_reminderValue').removeClass('deactivatedListElement');
        clone.find('td.columnEdit li#q_invc_reminderValue').on('click', function(event){
          openReminderPopup(event);
        });
      } else if(dunningData[0] == 'dunningI'){
        clone.find('td.columnEdit li#q_invc_reminderValue').removeClass('deactivatedListElement');
        clone.find('td.columnEdit li#q_invc_dunningIValue').removeClass('deactivatedListElement');
        clone.find('td.columnEdit li#q_invc_reminderValue').on('click', function(event){
          openReminderPopup(event);
        });
        clone.find('td.columnEdit li#q_invc_dunningIValue').on('click', function(event){
          openDunningIPopup(event);
        });
      } else if(dunningData[0] == 'dunningII'){
        clone.find('td.columnEdit li#q_invc_reminderValue').removeClass('deactivatedListElement');
        clone.find('td.columnEdit li#q_invc_dunningIValue').removeClass('deactivatedListElement');
        clone.find('td.columnEdit li#q_invc_dunningIIValue').removeClass('deactivatedListElement');
        clone.find('td.columnEdit li#q_invc_reminderValue').on('click', function(event){
          openReminderPopup(event);
        });
        clone.find('td.columnEdit li#q_invc_dunningIValue').on('click', function(event){
          openDunningIPopup(event);
        });
        clone.find('td.columnEdit li#q_invc_dunningIIValue').on('click', function(event){
          openDunningIIPopup(event);
        });
      }
      //deactivate Mail button
      clone.find('td.columnEdit .mailReminder').addClass('deactivatedMail');
      clone.find('td.columnEdit .mailDunningI').addClass('deactivatedMail');
      clone.find('td.columnEdit .mailDunningII').addClass('deactivatedMail');

      recalcInvoiceTableSums()
      
      $('table#tableInvoices > tbody').prepend(clone)
      //keep invoice contacts up to date
      fetchContacts()
    }
  }

  function displaySuccess (messageContent) {
    // remove older Messages if they exist
    if ($('.messageSuccess').length == true) {
      $('.messageSuccess').detach()
    }
    
    
    $('#wpbody-content').prepend(
      '<div class="messageSuccess qinvoiceMessage"> ' +
        '<span>' + messageContent + '</span>' +
      '</div>'
    )
       
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
        var dunningData = [JSON.parse(response).dunningclass, JSON.parse(response).dunningdays, JSON.parse(response).updateType]
        var invoiceID = JSON.parse(response).id

        if (serverResponse.action === 'updateInvoiceServerSide') {
          changeUpdatedInvoiceRow(serverResponse, dunningData)
        }
        if (serverResponse.action === 'saveInvoiceServerSide') {
          addNewInvoiceRow(serverResponse, invoiceID, dunningData)
        }

        $('#invoiceOverlay').hide()

        // $('#invoiceForm').trigger('reset')
        $('#saveInvoice').prop('disabled', false);

        displaySuccess('Invoice succesfully saved!')
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
    $('input').on('invalid', function () { return false })

    // Prevent chrome to autofill&autocomplete
    $('#invoiceInputTables input').on('focus', function (e) {
      $(this).attr('autocomplete', 'off')
    })

    

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

  function disableInvoiceForm(){
    $('#invoiceForm   *').prop('disabled', true );
    $('#updateInvoice').css('display', 'none');
  }

  function enableInvoiceForm(){
    $('#invoiceForm   *').prop('disabled', false );
    $('#updateInvoice').css('display', 'block');
  }
   /*
   * Switch function for toggle in Invoice Form:
   * 
   * - The toggle is unchecked if the Invoice Form is shown (Invoice Form Popup 
   *   will be deactivated if the Invoice is already Paid --> Toggle would be checked)
   * 
   * - When the Toggle is checked the Invoice Form can not be modified until you 
   *   uncheck the paid toggle in the list --> checking the toggle closes 
   *   the Popup and prohibits further modifications
   * 
   * - The toggle will be observed by an on change evnt listener
   * 
   * - If this listener detects the Toggle to be checked, a click action 
   *   in the paid toggle in the overview 
   *   is simulated and the popup will be closed
   *  */
  $(".switch").on('click', '.sliderForPaymentWithinForm', function (event) {
    if ($("#reopenPaidInvoiceWithinForm").is(":visible")) {return}
    const invoiceID = $('#invoice_id').val()
    const sliderBox = $(event.target).parent()
    
    if (sliderBox.find('input').prop('checked') == false){ 
      disableInvoiceForm(invoiceID)
      

      $('#edit-'+invoiceID).find('.sliderForPayment').click()
      markInvoiceAsPaidInDB('edit-'+invoiceID)

    } 
    
    else {
      enableInvoiceForm(invoiceID)
      // show dialog
      $("#reopenPaidInvoiceWithinForm").show()
      $("#reopenPaidInvoiceWithinForm").css('zIndex', 9999);
      $("#lastOpenedInvoiceWithinForm").val(invoiceID)
      event.preventDefault()

    }
  })

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

  $('#qinv_saveContactCheckbox').on('click', function(){
    if($('#qinv_saveContactCheckbox').prop("checked")){
      $('#qinv_saveContactHidden').val("true");
    } else if(!$('#qinv_saveContactCheckbox').prop("checked")){
      $('#qinv_saveContactHidden').val("false");
    }
  });

  // Wordpress Editor functionality gets modified (used for mail)
  function qpModifyWPEditor(id){
    if(document.getElementById(id+"-tmce")){
      document.getElementById(id+"-tmce").click();
      qpModifyWPEditorCSS(id);
      document.getElementById(id+"-html").click();
      let vis = document.getElementById(id+"-tmce");
      document.getElementById("qt_"+id+"_toolbar").appendChild(vis);
      document.getElementById(id+"-tmce").click();
      let txt = document.getElementById(id+"-html");
      document.getElementById("mceu_28-body").appendChild(txt);
      document.getElementById("mceu_27").appendChild(txt);
      document.getElementById("mceu_29-body").appendChild(txt); // Louis: text-to-visual button is inserted into the toolbar
    }
  }

  // Wordpress Editor style gets modified (used for mail)

  function qpModifyWPEditorCSS(id){
    document.getElementById(id+"-tmce").click();
    document.getElementById("mceu_27").classList.add("bs-white-background", "bs-no-border");
    document.getElementById("mceu_26").classList.add("bs-white-background", "bs-no-border");
    document.getElementById("qt_"+id+"_toolbar").classList.add("bs-white-background");
    document.getElementById("mceu_5").classList.add("bs-hide-element");
    document.getElementById("mceu_10").classList.add("bs-hide-element");
    document.getElementById("mceu_11").classList.add("bs-hide-element");
    document.getElementById("mceu_12").classList.add("bs-hide-element");
    document.getElementById(id+"-html").classList.add("bs-margin-mod");
    document.getElementById(id+"-tmce").classList.add("bs-margin-mod");
    document.getElementById(id+"_ifr").style.minHeight = "16em"; //for height of WP-Editor-field
  }

  /**
   * Handles the on click action on the mail dashicon:
   */
   $('table#tableInvoices').on('click', '.mailInvoice', function (event) {
    qpModifyWPEditor('templateEditor');
    getMailPopupData($(this).attr('id'), 'Invoice');
    $('div#qinv_mail-popup').css('display', 'block');
  })

  /**
   * Handles the on click action on the reminder mail dashicon:
   */
   $('table#tableInvoices').on('click', '.mailReminder', function (event) {
    if ($(event.target).is('.deactivatedMail')) { return }
    getMailPopupData($(this).attr('id'), 'Reminder');
    $('div#qinv_mail-popup').css('display', 'block');
  })

  /**
   * Handles the on click action on the dunning i mail dashicon:
   */
   $('table#tableInvoices').on('click', '.mailDunningI', function (event) {
    if ($(event.target).is('.deactivatedMail')) { return }
    getMailPopupData($(this).attr('id'), 'DunningI');
    $('div#qinv_mail-popup').css('display', 'block');
  })

  /**
   * Handles the on click action on the dunning ii mail dashicon:
   */
   $('table#tableInvoices').on('click', '.mailDunningII', function (event) {
    if ($(event.target).is('.deactivatedMail')) { return }
    getMailPopupData($(this).attr('id'), 'DunningII');
    $('div#qinv_mail-popup').css('display', 'block');
  })

  /**
   * Retrievs data from page and inserts it into the popup. 
   * @param {invocie} id 
   * @param {dunning or invoice} type 
   */
  function getMailPopupData(id, type){

    $('#edit-' + id).find('div.qinv_moreOptionsDropdownBox').css('display', 'none');

    $('#qinv_mail-recipient').val('');
    $('#qinv_mail-subject').val('');
    var wpEditorID = $('#qinv_mail-popup').find('.wp-editor-wrap').attr('id').split('-')[1];
    tinyMCE.get(wpEditorID).execCommand('mceNewDocument');

    fetchContacts();
    
    var name = $('#edit-' + id).find('td.columnName').text().replace(/\s+/g, ' ').slice(1,-1);
    var nameArray = name.split(' ');
    var salutation = '<p>Hello ' + name + ',<br></p><p>';
    var recipientMail = '';
    var companyName = $('#qinv_mail-name-company').text();
    

    for(i = 0; i < contactData[0].length; i++){
      //check for each contact if the full adress and name is like the data in the form
      if(contactData[0][i].company == name || (contactData[0][i].firstname == nameArray[0] && contactData[0][i].lastname == nameArray[1]))
      {
        recipientMail = contactData[0][i].email;
      }
    }
    $('#qinv_mail-recipient').val(recipientMail);
    $('#qinv_mail-subject').val(type + '_ID' + id + '_' + name.replace(' ', '-'));
    if(type == 'Invoice'){
      var attachmentRef = $('#edit-' + id).find('td.columnEdit a.download').attr('href');
      var template = $('#qinv_mail-invoice-template').text();
    }else if(type == 'Reminder'){
      var attachmentRef = $('#edit-' + id).find('td.columnEdit div.qinv_moreOptionsDropdownBox a.downloadReminder').attr('href');
      var template = $('#qinv_mail-dunning-template').text();
    }else if(type == 'DunningI'){
      var attachmentRef = $('#edit-' + id).find('td.columnEdit div.qinv_moreOptionsDropdownBox a.downloadDunningI').attr('href');
      var template = $('#qinv_mail-dunning-template').text();
    }else if(type == 'DunningII'){
      var attachmentRef = $('#edit-' + id).find('td.columnEdit div.qinv_moreOptionsDropdownBox a.downloadDunningII').attr('href');
      var template = $('#qinv_mail-dunning-template').text();
    }
    
    var fullMail = salutation + template + '</p><p>Best Regards<br><br>' + companyName + '</p>';
    tinyMCE.get(wpEditorID).execCommand('mceInsertContent', true, fullMail);
    $('#qInvMailAttachmentIcon').attr('href', attachmentRef);
    $('#qInvMailAttachmentData').text(type+'-PDF');
    $('#qInvMailAttachmentData').attr('href', attachmentRef);

  }

  /**
   * Sends E-Mail with given content on Click on Send
   */
  $('#qinv_mail-popup-submit').on('click', function(e){
    var wpEditorID = $('#qinv_mail-popup').find('.wp-editor-wrap').attr('id').split('-')[1];
    var messageBody = tinymce.get(wpEditorID).getContent();

    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,
      data: {
        action: 'sendMailServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        recipient: $('#qinv_mail-recipient').val(),
        subject: $('#qinv_mail-subject').val(),
        message: messageBody,
        attachments: $('#qInvMailAttachmentData').attr('href').split('/pdf/')[1]
      },
      success: function (response) {
        if(response){
          $('#qinv_mail-popup').hide();
          displaySuccess ("E-Mail has been send!");
        }
      },
      error: function (errorThrown) {
        $('#qinv_mail-popup').hide();
        displayFail ("E-Mail has not been send!");
        console.log(errorThrown);
      }
    })
  })



  /**
   * Function to simulate a dynamic ID size depending on the ID length:
   * Each Number will receive 7px + 11px for the first
   */
  fireOnPageLoad = () => {
    
    filterInvoices('all')
    
  }

   jQuery(document).ready(function ($) {
     fireOnPageLoad()
    
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

    //qpModifyWPEditor('templateEditor');

    
    
   })

})
