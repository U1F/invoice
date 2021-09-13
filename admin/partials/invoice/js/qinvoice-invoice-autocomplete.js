jQuery(function($) {
    customerFormFields= [
        'firma',
        'additional',
        'firstname',
        'lastname',
        'street',
        'zip',
        'city',
        'loc_id'
    ];

    fetchContacts();
    
    $("#firma").keyup(function() {
        console.log(contactData);
        companyNames = [];
        companyIDs = [];
        matching = false;
        companyNameCount = 0;
        matchedNames = [];
        matchedIDs = [];
        if ($(this).val().length > 0) {
            for (i = 0; i < contactData[0].length; i++) {
                console.log(contactData[0][i].company);
                companyNames[i] = contactData[0][i].company;
                companyIDs[i] = contactData[0][i].id;
                if (companyNames[i].toLowerCase().includes($(this).val().toLowerCase(), 0)) {
                    matching = true;
                    matchedNames[companyNameCount] = companyNames[i];
                    matchedIDs[companyNameCount] = companyIDs[i];
                    companyNameCount++;

                }
            }
        } else {

            $('#autocompleteCompany').css('display', 'none');
        }
        if (matching) {


            $('#autocompleteCompany').css('display', 'block');


            showTextAreaForAutocompleteOfContactNames(matchedNames, matchedIDs);
        } else {
            $('#autocompleteCompany').css('display', 'none');
        }
    });

    function showTextAreaForAutocompleteOfContactNames(matchedNames, matchedIDs) {


        htmlData = "<p><strong> Matching: </strong></p>";
        for (i = 0; i < matchedNames.length; i++) {
            htmlData +=
                "<div " +
                "class='autocompleteButton' " +

                "id='" + matchedIDs[i] + "'" +
                "name='" + matchedNames[i] + "'" +
                "> " +
                matchedNames[i] +
                "</div>";

        }
        $('div#autocompleteCompany').html(htmlData);
    }

    function storeContactData(Contacts) {
        contactData = JSON.parse(Contacts);
    }
    function fetchContacts() {
        jQuery.ajax({
            type: 'POST',
            url: q_invoice_ajaxObject.ajax_url,

            data: {
                action: 'fetchContactsServerSide',
                _ajax_nonce: q_invoice_ajaxObject.nonce,
            },
            success: function(response, textStatus, XMLHttpRequest) {

                storeContactData(response);


            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }

        });

    }

    $("#autocompleteCompany").on("click", "div.autocompleteButton", function() {

        id = $(this).attr('id');
        fillContactDataInInvoiceForm(id)
        lockContactFieldInInvoiceForm();
        showLocker();
        $('div#autocompleteCompany').html("");
        $('#autocompleteCompany').css('display', 'none');

    });

    function fillContactDataInInvoiceForm(id){
        for (i = 0; i < contactData[0].length; i++) {
            if (id == contactData[0][i].id) {
                
                $('#firma').val(contactData[0][i].company);
                $('#additional').val(contactData[0][i].additional);
                $('#firstname').val(contactData[0][i].firstname);
                $('#lastname').val(contactData[0][i].name);
                $('#street').val(contactData[0][i].street);
                $('#zip').val(contactData[0][i].zip);
                $('#city').val(contactData[0][i].city);
                $('#loc_id').val(contactData[0][i].id);
    
            }
    
    
        }
    }
    function lockInputField(name){
        $('#'+name).attr("readonly", true);
    };

    function unlockInputField(name){
        $('#'+name).attr("readonly", false);
    };
    
    function showLocker() {
        $(".lockForContacts").css("display","block");
    }

    function clearInputField(name){
        $('#'+name).val("");
    }

    function lockContactFieldInInvoiceForm () {
        
        customerFormFields.forEach(element => {
            lockInputField (element);
            console.log(element);
        });
        
        
    }

    function unlockContactFieldInInvoiceForm () {
        customerFormFields.forEach(element => {
            unlockInputField (element);
            console.log(element);
        });

        customerFormFields.forEach(element => {
            clearInputField (element);
            console.log(element);
        });
    }

    $("#edit-invoice").click(function(event) {

        if ($(event.target).parents("#contactRegister").length) {
            //("#contactRegister").css("display", "block");
            console.log(event.target);
            if ($(event.target).is(".contactRegisterData")){
                id= $(event.target).parent().attr('id');
                console.log("ID:"+ id);
                fillContactDataInInvoiceForm(id)
                showLocker();
                lockContactFieldInInvoiceForm();
                $("#contactRegister").css("display", "none");
            }
        }
        else if ($(event.target).is("#inputDashiconCompanyRegister")){
            //$("#contactRegister").css("display", "block");
        }
        else {
            $("#contactRegister").css("display", "none");
        }
    });

    $(".lockForContacts").click(function(){

        unlockContactFieldInInvoiceForm();
        $(".lockForContacts").css("display","none");
    });
 
     $("#inputDashiconCompanyRegister").click(function(event) {
         $("#contacts > tbody").empty();
         $("#contactRegister").css("display", "block");
         for (i = 0; i < contactData[0].length; i++) {
            
             
             $("#contacts > tbody").append(
                 "<tr "+
                     "id='"+ contactData[0][i].id+"'"+
                     "class='contactRegisterRow'>"+
                     "<td class='first contactRegisterData'>" + 
                         contactData[0][i].company +
                     "</td>"+
                     "<td class='contactRegisterData'>" + 
                         contactData[0][i].firstname + " " +
                         contactData[0][i].name +
                     "</td>" +
                     "<td class='last contactRegisterData'>" + 
                         contactData[0][i].city +
                     "</td>"+
                 "</tr>");
             
         }
 
     });
 
    
    



});