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
    qinv_settings_removeLogoFile();
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
        console.log('Removal Status: ' + data)
        $('.submit #saveSettings').click();
      },
      error: function (errorThrown) {
        console.log('Logo not removed')
        console.log(errorThrown)
      }
    })
  }

  //Whenever a logo has been uploaded with the upload function click on submit automatically
  $('#logoFile').on('change', function(e){
    $('.submit #saveSettings').click();
  })

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
  $('input#removeLogo').on('click', (event) => {
    event.preventDefault()
    $('#companyLogo').val('')
    $('#uploadLogoButton').show()
    $('img.attachment-medium').hide()
    $(event.currentTarget).hide()
  })

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
      
      var selectedImage = logoFrame.state().get('selection').first().toJSON();
      jQuery('input#companyLogo').val(selectedImage.id);
      // would be nice to refresh images
   })

     logoFrame.on('open',function() {
      var selection =  logoFrame.state().get('selection')
      var id = jQuery('input#companyLogo').val()
      var attachment = wp.media.attachment(id)
      attachment.fetch()
      selection.add( attachment ? [ attachment ] : [] )
    })
    
    logoFrame.open();

  })

})