/* global q_invoice_ajaxObject, jQuery */
/* eslint no-undef: "error" */
let updateContactDiv = ''
let saveContactDiv = ''
let contactIDtoDelete = ''
let rowOfDeletedContact = ''
jQuery(function ($) {

  function displaySuccess () {
    $('#wpbody-content').prepend(
      '<div class="qinvoiceMessage messageSuccess">' +
      '<span> Contact succesfully saved! </span>' +
      '</div>')

    $('.messageSuccess').delay(5000).fadeOut(800)
  }

  function displayFail (details, duration) {
    $('#wpbody-content').prepend(
      '<div class="qinvoiceMessage messageFail">' +
      '<span> Something went wrong! <br>' + details + ' <br><br> Please refresh the page.</span>' +
      '</div>')

    $('.messageFail').delay(duration).fadeOut(800)
  }

  jQuery(document).ready(function ($) {
    $('#qiContactForm').ajaxForm({
      beforeSerialize: function($form, options) {
        $('#saveContact').attr('disabled', 'disabled');
      },
      success: function (response) {
        console.log('response:')
        console.log(response)
        $('#qiContactForm').trigger('reset')
        document.getElementById('contactOverlay').style.display = 'none'

        const obj = JSON.parse(response)
        if (obj.type === 'save') {
          addNewContactRow(JSON.parse(response).contactData, JSON.parse(response).id)
        } else if (obj.type === 'update') {
          changeUpdatedContactRow(JSON.parse(response).contactData, JSON.parse(response).id)
        }

        $('#saveContact').prop('disabled', false);

        displaySuccess();
      },
      error: function (response){
        $('#saveContact').prop('disabled', false);
        $('#qiContactForm').trigger('reset')
        document.getElementById('contactOverlay').style.display = 'none'
        displayFail("Can't save Contact Data.", 5000);
      }
    })

    saveContactDiv = $('#saveContactDIV').clone()
    updateContactDiv = $('#updateContactDIV').clone()
  })
  $('#qiNewContact').click(function () {
    document.getElementById('contactOverlay').style.display = 'block'

    //reset required field error color
    $('.qiContactTableInput input[required]').each(function(e){
      $(this).removeClass('error')
    })


    // reset form
    $('#qiContactForm')[0].reset()

    // prepare form for Ajax Action "save"
    $('h2#formHeaderEdit').css('display', 'none')
    $('h2#formHeaderCreate').css('display', 'block')

    $('#updateContact').css('display', 'none')
    $('#saveContact').css('display', 'inline')
    $("input[name='action']").val('saveContactServerSide')

    $('#saveContactDIV').remove()
    $('#updateContactDIV').remove()
    $('#nonceFields').append(saveContactDiv)
  })

  // Contacts

  $('#cancelContactEdit').click(function (event) {
    document.getElementById('contactOverlay').style.display = 'none'
  })

  $('table#contacts > tbody').on('click', '.editContact', function (event) {
    document.getElementById('contactOverlay').style.display = 'block'

    // reset form
    $('#qiContactForm')[0].reset()

    $('#updateContact').css('display', 'inline')
    $('#saveContact').css('display', 'none')

    $("input[name='action']").val('updateContactServerSide')

    $('#saveContactDIV').remove()
    $('#updateContactDIV').remove()
    $('#nonceFields').append(updateContactDiv)

    // fetch id from span attribute id="edit-n", where  n = id of invoice
    // Ajax Call for Invoice Data

    editContact(jQuery(this).attr('id').split('-')[1])
  })

  function editContact (contactId) {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,
      data: {
        action: 'editContactServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        id: contactId
      },
      success: function (response, textStatus, XMLHttpRequest) {
        console.log(response)

        document.getElementById('contactOverlay').style.display = 'block'

        const obj = JSON.parse(response)

        // TODO: array/map
        $('#qiContactID').val(obj[0][0].id)
        $('#qiContactCompany ').val(obj[0][0].company)
        $('#qiContactAdditional').val(obj[0][0].additional)
        $('#qiContactName').val(obj[0][0].lastname)
        $('#qiContactFirstname').val(obj[0][0].firstname)
        $('#qiContactStreet').val(obj[0][0].street)
        $('#qiContactZIP').val(obj[0][0].zip)
        $('#qiContactCity').val(obj[0][0].city)
        $('#qiContactEmail').val(obj[0][0].email)
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log(errorThrown)
      }
    })
  }

  function changeUpdatedContactRow (contact) {
    // Change Infos in the right table row.
    $('table#contacts > tbody > tr[value=' + contact.qiContactID + ']').find('td.contactColumnCompany').text(contact.qiContactCompany)
    $('table#contacts > tbody > tr[value=' + contact.qiContactID + ']').find('span.columnFirstName').text(contact.qiContactFirstname)
    $('table#contacts > tbody > tr[value=' + contact.qiContactID + ']').find('span.columnLastName').text(contact.qiContactName)
    $('table#contacts > tbody > tr[value=' + contact.qiContactID + ']').find('td.contactColumnCity').text(contact.qiContactCity)
    $('table#contacts > tbody > tr[value=' + contact.qiContactID + ']').find('td.contactColumnEmail').text(contact.qiContactEmail)
  }

  function addNewContactRow (contact, id) {
    const clone = $('table#contacts > tbody').find('tr').last().clone()

    clone.find('td.contactColumnRowID').text(1 + parseInt(clone.find('td.contactColumnRowID').text()))
    clone.find('td.contactColumnCompany').text(contact.qiContactCompany)
    clone.find('span.columnFirstName').text(contact.qiContactFirstname)
    clone.find('span.columnLastName').text(contact.qiContactName)
    clone.find('td.contactColumnCity').text(contact.qiContactCity)
    clone.find('td.contactColumnEmail').text(contact.qiContactEmail)
    //Check Point
    console.log(id.toString())
    console.log(clone.val())
    clone.attr('value', id.toString())
    clone.find('span.editContact').attr('id', 'edit-' + id)
    clone.find('span.deleteContact').attr('id', id)
   
    $('table#contacts > tbody').append(clone)
  }

  $('table#contacts > tbody').on('click', '.deleteContact', function (event) {
    $('div#deleteContact').css('display', 'block')
    rowOfDeletedContact = $(this).closest('tr')
    contactIDtoDelete = event.target.id
  })

  $('#confirmDeleteContact').on('click', function (event) {
    rowOfDeletedContact.remove()
    deleteContact(contactIDtoDelete)
    $('div#deleteContact').css('display', 'none')
  })

  $('#cancelDeleteContact').on('click', function (event) {
    $('div#deleteContact').css('display', 'none')
  })

  $(document).on('keydown', function (e) {
    if (e.keyCode === 27) { 
      $('#contactOverlay').css('display', 'none')
      $('div#deleteContact').css('display', 'none')
    }
  })

  $('#contactOverlay').click(function (event) {
    if ($(event.target).is('.overlay')) {
      $('#contactOverlay').css('display', 'none')
    }
    if ($(event.target).is('.cancelButton')) {
      $('#contactOverlay').css('display', 'none')
    }
  })

  function deleteContact (contactId) {
    jQuery.ajax({
      type: 'POST',
      url: q_invoice_ajaxObject.ajax_url,

      data: {
        action: 'deleteContactServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce,
        id: contactId
      },
      success: function (data, textStatus, XMLHttpRequest) {
        console.log(data)
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown)
      }
    })
  }
})
