jQuery(function($) {
    customerFormFields= [
        'company',
        'additional',
        'firstname',
        'lastname',
        'street',
        'zip',
        'city',
        'loc_id'
    ];

    fetchContacts();
    
    $("#company2").keyup(function() {
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

    $("#additional2").keyup(function() {
        
        additonalNames = [];
        additionalIDs = [];
        matching = false;
        additionalNameCount = 0;
        matchedNames = [];
        matchedIDs = [];
        if ($(this).val().length > 0) {
            for (i = 0; i < contactData[0].length; i++) {
                
                additonalNames[i] = contactData[0][i].additional;
                additionalIDs[i] = contactData[0][i].id;
                if (additonalNames[i].toLowerCase().includes($(this).val().toLowerCase(), 0)) {
                    matching = true;
                    matchedNames[additionalNameCount] = additonalNames[i];
                    matchedIDs[additionalNameCount] = additionalIDs[i];
                    additionalNameCount++;

                }
            }
        } else {

            $('#autocompleteAdditional').css('display', 'none');
        }
        if (matching) {
            $('#autocompleteAdditional').css('display', 'block');


            showTextAreaForAutocompleteOfAdditionalNames(matchedNames, matchedIDs);
        } else {
            $('#autocompleteAdditional').css('display', 'none');
        }
    });
    $("#invoiceFormInputsLeft").on("keyup", ".inputsLeftTable", function(event) {
        
        field= 
            $(event.target)[0].id[0].toUpperCase() +
            $(event.target)[0].id.slice(1);

        nameCount = 0;
        matchedNames = [];
        matchedIDs = [];

        if ($(event.target).val().length > 0) {
            
            for (i = 0; i < contactData[0].length; i++) {
                
                if (contactData[0][i][$(event.target)[0].id].toLowerCase().includes($(event.target).val().toLowerCase(), 0)) {
                    
                    matchedNames[nameCount] = contactData[0][i].company;
                    matchedIDs[nameCount] = contactData[0][i].id;
                    nameCount++;
                }
            }
        } 
        if (nameCount) {
            $('#autocomplete'+field).css('display', 'block');
            showTextAreaForAutocompleteOfContactFields(matchedNames, matchedIDs, field);
        } else {
            $('#autocomplete'+field).css('display', 'none');
        }
    });

    function showTextAreaForAutocompleteOfContactFields(matchedNames, matchedIDs, fieldName) {

        htmlData = "<p><strong> Matching: </strong></p>";
        for (i = 0; i < matchedNames.length; i++) {
            htmlData +=
                "<div " +
                "class='autocompleteButton' " +
                "id='" + matchedIDs[i] + "'" +
                "name='" + fieldName + "'" +
                "> " +
                matchedNames[i] + 
                "</div>";
        }
        $('div#autocomplete'+fieldName).html(htmlData);
    }


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

    function showTextAreaForAutocompleteOfAdditionalNames(matchedNames, matchedIDs) {
        //DEBUG
            console.log(
                matchedNames + 
                " + "
                +matchedIDs + " " 
            +    matchedNames.length);
        //DEBUG_END

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
        $('div#autocompleteAdditional').html(htmlData);
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

    $("#invoiceFormInputsLeft").on("click", "div.autocompleteButton", function() {   
        fillContactDataInInvoiceForm($(this).attr('id'))
      
     
        $('div#autocompleteCompany').html("");
        $('#autocomplete'+$(this).attr('name')).css('display', 'none');

    });

    $("#invoiceFormInputsLeft").on("mouseover", "div.autocompleteButton", function() { 
        fillContactDataInInvoiceForm($(this).attr('id'));
    });

    function fillContactDataInInvoiceForm(id){
        for (i = 0; i < contactData[0].length; i++) {
            if (id == contactData[0][i].id) {
                
                $('#company').val(contactData[0][i].company);
                $('#additional').val(contactData[0][i].additional);
                $('#firstname').val(contactData[0][i].firstname);
                $('#lastname').val(contactData[0][i].lastname);
                $('#street').val(contactData[0][i].street);
                $('#zip').val(contactData[0][i].zip);
                $('#city').val(contactData[0][i].city);
                $('#loc_id').val(contactData[0][i].id);
    
            }
    
    
        }
    }
   

 
    
    

   

   

    

    $("#edit-invoice").click(function(event) {

        if ($(event.target).parents("#contactRegister").length) {
            //("#contactRegister").css("display", "block");
            console.log(event.target);
            if ($(event.target).is(".contactRegisterData")){
                id= $(event.target).parent().attr('id');
                console.log("ID:"+ id);
                fillContactDataInInvoiceForm(id)
             
               
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