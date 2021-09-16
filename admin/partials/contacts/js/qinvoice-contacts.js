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
        $("#updateContactDIV").remove();

    });

    // Contacts

    $("#cancelContactEdit").click(function(event) {
        document.getElementById("overlay").style.display = "none";
    });



    $(".editContact").click(function(event) {

        document.getElementById("overlay").style.display = "block";

        // reset form
        $('#qiContactForm')[0].reset();

        $("#updateContact").css("display", "inline");
        $("#saveContact").css("display", "none");
        $("input[name='action']").val("updateContactServerSide");
        $("#saveContactDIV").remove();

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



    $(".deleteContact").click(function(event) {

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
                // Here we can measure success by own stanards
                // For example a return from a DB uppdate   
                // Also maybe here we can call a notice tu the user
                console.log(data);


            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    }

    

    

});