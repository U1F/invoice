jQuery(function($) {
    $("#qiNewContact").click(function() {
        document.getElementById("overlay").style.display = "block";

        // reset form
        $('#qiContactForm')[0].reset();

        // prepare form for Ajax Action "save"
        $("h2#formHeaderEdit").css("display", "none");
        $("h2#formHeaderCreate").css("display", "block");

        $("#updateContact").css("display", "none");
        $("#saveContact").css("display", "inline");
        $("input[name='action']").val("saveContactServerSide");
        

        $("#saveContactDIV").remove();
        $("#updateContactDIV").remove();
        $("#nonceFields").append(saveContactDiv);

    });

    // Contacts

    $("#cancelContactEdit").click(function(event) {
        document.getElementById("overlay").style.display = "none";
    });


    $("table#contacts > tbody").on( "click", ".editContact", function(event) {

        document.getElementById("overlay").style.display = "block";

        // reset form
        $('#qiContactForm')[0].reset();

        $("#updateContact").css("display", "inline");
        $("#saveContact").css("display", "none");

        $("input[name='action']").val("updateContactServerSide");
        
        $("#saveContactDIV").remove();
        $("#updateContactDIV").remove();
        $("#nonceFields").append(updateContactDiv);

        //fetch id from span attribute id="edit-n", where  n = id of invoice
        id = jQuery(this).attr("id").split('-');

        // Ajax Call for Invoice Data

        editContact(id[1]);
    });


    function editContact(contactId) {
        jQuery.ajax({
            type: 'POST',
            url: q_invoice_ajaxObject.ajax_url,
            data: {
                action: 'editContactServerSide',
                _ajax_nonce: q_invoice_ajaxObject.nonce,
                id: contactId
            },
            success: function(response, textStatus, XMLHttpRequest) {

                console.log(response);

                document.getElementById("overlay").style.display = "block";

                var obj = JSON.parse(response);

                //TODO: array/map
                $('#qiContactID').val(obj[0][0]["id"]);
                $('#qiContactCompany ').val(obj[0][0]["company"]);
                $('#qiContactAdditional').val(obj[0][0]["additional"]);
                $('#qiContactName').val(obj[0][0]["lastname"]);
                $('#qiContactFirstname').val(obj[0][0]["firstname"]);
                $('#qiContactStreet').val(obj[0][0]["street"]);
                $('#qiContactZIP').val(obj[0][0]["zip"]);
                $('#qiContactCity').val(obj[0][0]["city"]);
                $('#qiContactEmail').val(obj[0][0]["email"]);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

    }

    function changeUpdatedContactRow (contact)  {
        //Change Infos in the right table row.
        $("table#contacts > tbody > tr[value="+contact['qiContactID']+"]").find("td.columnCompany").text(contact['qiContactCompany']);
        $("table#contacts > tbody > tr[value="+contact['qiContactID']+"]").find("span.columnFirstName").text(contact['qiContactFirstname']);
        $("table#contacts > tbody > tr[value="+contact['qiContactID']+"]").find("span.columnLastName").text(contact['qiContactName']);
        $("table#contacts > tbody > tr[value="+contact['qiContactID']+"]").find("td.columnCity").text(contact['qiContactCity']);
        $("table#contacts > tbody > tr[value="+contact['qiContactID']+"]").find("td.columnEmail").text(contact['qiContactEmail']);
        
    

    }
    
    function addNewContactRow (contact, id)  {
        
            clone = $("table#contacts > tbody").find("tr").last().clone();
            
            clone.find("td.columnRowID").text(1+parseInt(clone.find("td.columnRowID").text()));
            clone.find("td.columnCompany").text(contact['qiContactCompany']);
            clone.find("span.columnFirstName").text(contact['qiContactFirstname']);
            clone.find("span.columnLastName").text(contact['qiContactName']);
            clone.find("td.columnCity").text(contact['qiContactCity']);
            clone.find("td.columnEmail").text(contact['qiContactEmail']);
            
            clone.find("span.editContact").attr("id","edit-" + id);
            clone.find("span.deleteContact").attr("id", id);
            $("table#contacts > tbody").append(clone);
        

    }
    
    $("table#contacts > tbody").on( "click", ".deleteContact", function(event) {

        $(this).closest('tr').remove();
        deleteContact(event.target.id);

    });

    function deleteContact(contactId) {
        jQuery.ajax({
            type: 'POST',
            url: q_invoice_ajaxObject.ajax_url,

            data: {
                action: 'deleteContactServerSide',
                _ajax_nonce: q_invoice_ajaxObject.nonce,
                id: contactId
            },
            success: function(data, textStatus, XMLHttpRequest) {
                
                console.log(data);


            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    }
    jQuery(document).ready(function($) {
        

        $('#qiContactForm').ajaxForm({
            success: function(response) {
                console.log("response:");
                console.log(response);
                $("#qiContactForm").trigger('reset');
                document.getElementById("overlay").style.display = "none";

                // TODO Julian nach Success Meldung fragen
                $('#wpbody-content').
                prepend('<div class="qinvoiceMessage messageSuccess">' +
                    '<span>Contact successfully saved!</span>' +
                    '</div>');
                
                $(".messageSuccess").delay(1000).fadeOut(800);

                obj=JSON.parse(response);
                if (obj['type'] =="save") {
                    addNewContactRow(JSON.parse(response)["contactData"],JSON.parse(response)["id"]);
                } 
                else if (obj['type'] =="update") {
                    changeUpdatedContactRow(JSON.parse(response)["contactData"],JSON.parse(response)["id"]);
                }
                
                

                
            }
        });

        saveContactDiv = $("#saveContactDIV").clone();
        updateContactDiv = $("#updateContactDIV").clone();



        

        

        
    });

    

    

});