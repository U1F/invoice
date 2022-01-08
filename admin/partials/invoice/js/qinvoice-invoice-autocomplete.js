/* global jQuery, q_invoice_ajaxObject */
/* eslint no-undef: "error" */
jQuery(function ($) {
  let contactData = []

  fetchContacts()

  $('#invoiceFormInputsLeft').on('keyup', '.inputsLeftTable', function (event) {
    const field =
            $(event.target)[0].id[0].toUpperCase() +
            $(event.target)[0].id.slice(1)

    let nameCount = 0
    const matchedNames = []
    const matchedIDs = []

    if ($(event.target).val().length > 0) {
      for (let i = 0; i < contactData[0].length; i++) {
        if (contactData[0][i][$(event.target)[0].id].toLowerCase().includes($(event.target).val().toLowerCase(), 0)) {
          if (field == "Company") {
            matchedNames[nameCount] = contactData[0][i].company
          } else if (field == "Firstname" || field == "Lastname"){
            matchedNames[nameCount] = contactData[0][i].firstname + ' ' + contactData[0][i].lastname + ' (' + contactData[0][i].company + ')'
          }
          matchedIDs[nameCount] = contactData[0][i].id
          nameCount++
        }
      }
    }
    if (nameCount) {
      $('#autocomplete' + field).css('display', 'block')
      showTextAreaForAutocompleteOfContactFields(matchedNames, matchedIDs, field)
    } else {
      $('#autocomplete' + field).css('display', 'none')
    }
  })

  function showTextAreaForAutocompleteOfContactFields (matchedNames, matchedIDs, fieldName) {
    let htmlData = ''
    var noBorderOnLastItem = 'border-bottom: 1px solid #dadce1;';
    for (let i = 0; i < matchedNames.length; i++) {
      if(i + 1 == matchedNames.length){
        noBorderOnLastItem = 'border-bottom: none;';
      } else{
        noBorderOnLastItem = 'border-bottom: 1px solid #dadce1;';
      }
      console.log("here");
      htmlData +=
                '<div ' +
                "class='autocompleteButton' " +
                "id='" + matchedIDs[i] + "'" +
                "name='" + fieldName + "'" +
                "style='" + noBorderOnLastItem + "'" +
                '> ' +
                matchedNames[i] +
                '</div>'
    }
    $('div#autocomplete' + fieldName).html(htmlData)
  }

  //hide autocomplete suggestions when clicking out of the box
  $('.autocompletePossField').blur(function (event) {
    const field = $(event.target)[0].id[0].toUpperCase() + $(event.target)[0].id.slice(1);
    $('#autocomplete' + field).css('display', 'none');
  });

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

  $('#invoiceFormInputsLeft').on('click', 'div.autocompleteButton', function () {
    fillContactDataInInvoiceForm($(this).attr('id'))

    $('div#autocompleteCompany').html('')
    $('#autocomplete' + $(this).attr('name')).css('display', 'none')
  })

  $('#invoiceFormInputsLeft').on('mouseover', 'div.autocompleteButton', function () {
    fillContactDataInInvoiceForm($(this).attr('id'))
  })

  function fillContactDataInInvoiceForm (id) {
    for (let i = 0; i < contactData[0].length; i++) {
      if (id === contactData[0][i].id) {
        $('#company').val(contactData[0][i].company)
        $('#additional').val(contactData[0][i].additional)
        $('#firstname').val(contactData[0][i].firstname)
        $('#lastname').val(contactData[0][i].lastname)
        $('#street').val(contactData[0][i].street)
        $('#zip').val(contactData[0][i].zip)
        $('#city').val(contactData[0][i].city)
        $('#loc_id').val(contactData[0][i].id)
      }
    }
  }

  $('#edit-invoice').click(function (event) {
    if ($(event.target).parents('#contactRegister').length) {
      // ("#contactRegister").css("display", "block");
      console.log(event.target)
      if ($(event.target).is('.contactRegisterData')) {
        const id = $(event.target).parent().attr('id')
        console.log('ID:' + id)
        fillContactDataInInvoiceForm(id)

        $('#contactRegister').css('display', 'none')
      }
    } else if ($(event.target).is('#inputDashiconCompanyRegister')) {
      // $("#contactRegister").css("display", "block");
    } else {
      $('#contactRegister').css('display', 'none')
    }
  })

  $('#inputDashiconCompanyRegister').click(function (event) {
    $('#contacts > tbody').empty()
    $('#contactRegister').css('display', 'block')
    for (let i = 0; i < contactData[0].length; i++) {
      $('#contacts > tbody').append(
        '<tr ' +
                     "id='" + contactData[0][i].id + "'" +
                     "class='contactRegisterRow'>" +
                     "<td class='first contactRegisterData'>" +
                         contactData[0][i].company +
                     '</td>' +
                     "<td class='contactRegisterData'>" +
                         contactData[0][i].firstname + ' ' +
                         contactData[0][i].name +
                     '</td>' +
                     "<td class='last contactRegisterData'>" +
                         contactData[0][i].city +
                     '</td>' +
                 '</tr>')
    }
  })
})
