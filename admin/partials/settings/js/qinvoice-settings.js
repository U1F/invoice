/* global jQuery */
/* eslint no-undef: "error" */
jQuery(function ($) {

  /**  _        _
   *  | |      | |        
   *  | |_ __ _| |__  ___ 
   *  | __/ _` | '_ \/ __|
   *  | || (_| | |_) \__ \
   *   \__\__,_|_.__/|___/ 
   * 
   * The color effect function for the filter buttons is saved in qinvoice-invoice.js                                               
   */

  /**
   * Load the new Settings content by clicking on a filter button
   */
  $('.filterButtons').on('click', 'div.inactive', function (event) {
    //if clicked between the filter buttons (prevents strange css bug)
    if ($(event.target).parent().attr('id') === 'filterButtons') {
      return;
    }

    //hide all Setting Rows
    $('.invoiceSettingsRow').removeClass('activeSetting')

    //show only the clicked setting row
    if ($(event.target).parent().attr('id') === 'showCompanySettings') {
      $('#companySettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showBankSettings') {
      $('#bankSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showMailSettings') {
      $('#mailSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showInvoiceSettings') {
      $('#invoiceSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showDunningSettings') {
      $('#dunningSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showOfferSettings') {
      $('#offerSettingsTable').addClass('activeSetting')
    }

    if ($(event.target).parent().attr('id') === 'showCreditSettings') {
      $('#creditSettingsTable').addClass('activeSetting')
    }
  })

  //on load reset the mobile menu to default position
  $('.mobileFilterButtonsOption[value=company]').prop('selected', true)

  /**
   * Load the new settings content (mobile) by choosing an option from the mobile dropdown
   */
  $('#settingsMobileFilterButtonsDropdown').on('change', function (event) {
    //hide all Setting Rows
    $('.invoiceSettingsRow').removeClass('activeSetting')
    //display the selected Setting Row
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'company') {
      $('#companySettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'bank') {
      $('#bankSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'mail') {
      $('#mailSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'invoice') {
      $('#invoiceSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'dunning') {
      $('#dunningSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'offer') {
      $('#offerSettingsTable').addClass('activeSetting')
    }
    if ($('#settingsMobileFilterButtonsDropdown option:selected').val() === 'credit') {
      $('#creditSettingsTable').addClass('activeSetting')
    }
    //remove the selection color from the selct box
    $('#settingsMobileFilterButtonsDropdown').blur();
  });

  /**
   *  _                   
   * | |                  
   * | | ___   __ _  ___  
   * | |/ _ \ / _` |/ _ \ 
   * | | (_) | (_| | (_) |
   * |_|\___/ \__, |\___/ 
   *           __/ |      
   *          |___/  
   */

  //When clicking on cross to delete the logo open the confirmation alert
  $('#qinv_settings_delete_logo').on('click', function (event){
    $('div#qinv_settings_deleteLogoOverlay').css('display', 'block');
  })

  //When deniing the delete process close the confirmation alert
  $('#cancelRemoveLogo').on('click', function(event){
    $('div#qinv_settings_deleteLogoOverlay').css('display', 'none');
  })

  //On Submitting the delete process close the alert and start the delete process
  $('#confirmRemoveLogo').on('click', function(event){
    $('div#qinv_settings_deleteLogoOverlay').css('display', 'none');
    $('label#qinv_settings_uploadLogo').show()
    $('div#showLogoDiv').hide()

    //$('#q1nv0-preview-image').attr('src', '')
    
    $('input#companyLogo').attr('value', '')
    $('input#lastFilePathLogo').attr('value', '')

    qinv_settings_removeLogoFile();
    
    //console.log('Deleted URL?')
    //console.log($('#q1nv0-preview-image').attr('src'))
  })

  function handleUploadedImage (selectedImage){

      const imageID = selectedImage.id
      const imageURL = selectedImage.url   

      const logoImageContainer = $('#q1nv0-preview-image-container')
      let clone = $(logoImageContainer).find('img').clone()

      $('input#companyLogo').attr('value', imageID)
      $('input#lastFilePathLogo').attr('value', imageURL)
      
      clone.removeAttr("src").attr('src', imageURL)
      clone.removeAttr("srcset")
      
      $('#q1nv0-preview-image-container').empty()
      $(logoImageContainer).prepend(clone)
      
      $('label#qinv_settings_uploadLogo').hide()
      $('div#showLogoDiv').show()
      
      //console.log("('#q1nv0-preview-image').attr('src')")
      //console.log($('#q1nv0-preview-image').attr('src'))
      
      qinv_settings_updateLogo(imageID, imageURL)
  }

  $('input#uploadLogoButton').on('click', (event) => {
    event.preventDefault()
    //tb_show('Upload a logo', 'media-upload.php?referer=wptuts-settings&type=image&TB_iframe=true&post_id=0', false);
    var logoFrame
    
    const title = 'Select Logo Image'
    logoFrame = wp.media({
      title: title,
      multiple : false,
      library : {
           type : 'image',
       }
     })

     logoFrame.on('close', () => {  
      const selectedImage = logoFrame.state().get('selection').first().toJSON()
      handleUploadedImage(selectedImage)
     })

     logoFrame.on('open',function() {
      let selection =  logoFrame.state().get('selection')
      const id = $('input#companyLogo').val()
      const attachment = wp.media.attachment(id)
      attachment.fetch()
      selection.add( attachment ? [ attachment ] : [] )
    })
    
    logoFrame.open();

  })


  /**
   * Function to remove the logo from server and save data by clicking the submit button
   */
  function qinv_settings_removeLogoFile(){
    jQuery.ajax({
      type: 'POST',

      url: q_invoice_ajaxObject.ajax_url,

      data: {
        action: 'removeLogoServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce
      },
      success: function (data) {
        
        $('#uploadLogoButton').show()
        
      },
      error: function (errorThrown) {
      
        console.log(errorThrown)

      }
    })
  }

  /**
   * Function to remove the logo from server and save data by clicking the submit button
   */
   function qinv_settings_updateLogo(id, logoFilepath){
    jQuery.ajax({
      type: 'POST',

      url: q_invoice_ajaxObject.ajax_url,

      data: {
        id: id,
        logoFilepath: logoFilepath,
        action: 'updateLogoServerSide',
        _ajax_nonce: q_invoice_ajaxObject.nonce
      },
      success: function (data) {
        
        //$('#uploadLogoButton').show()
        console.log('Removal Status: ' + data)
        
      },
      error: function (errorThrown) {

        console.log(errorThrown)

      }
    })
  }


  

  /**
   *   __            _   _               
   *  / _|          | | | |              
   * | |_ _   _ _ __| |_| |__   ___ _ __ 
   * |  _| | | | '__| __| '_ \ / _ \ '__|
   * | | | |_| | |  | |_| | | |  __/ |   
   * |_|  \__,_|_|   \__|_| |_|\___|_|  
   */

  $('textarea').each(function () {
    this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;')
  }).on('input', function () {
    this.style.height = 'auto'
    this.style.height = (this.scrollHeight) + 'px'
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

  
})