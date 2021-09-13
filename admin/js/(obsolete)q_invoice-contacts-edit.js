{
    jQuery(function($) {
        
        
        
    });

    /**
     * 
     * @param {Element Id of edit contacts popup} popupId 
     * @returns 
     */
    function qMailingTogglePopupVisibility(popupId) {
        let popup = document.getElementById(popupId);

        if ( popup === null || popup === undefined ) {
            console.error("qMailingTogglePopupVisibility(): there is no popup element.");
            return;
        }
        popup.style.display = (popup.style.display !== 'flex' ) ?  "flex" : "none";
    }

    /**
     * 
     * @param {id of the current contact } id 
     */
    function loadContactDataIntoPopup(id) {
        
        let event = JSON.parse( atob( document.getElementById("q_mailing_contact_data_" + id).innerText ) );

        let inputNames = Object.keys( event );

        let currentElement; 

        inputNames.forEach( name => {
            currentElement = document.getElementById("q_mailing_popup_" + name);

            if ( currentElement === null || currentElement === undefined ) {
                console.error("loadContactDataIntoPopup(): cant access " + escape(name) + " element. Check popup layout.");
                return;
            }
            
            if ( name !== 'status' ) {
                currentElement.value = event[name].trim();
            } else {
                currentElement.checked = ( event[name] === "1" || event[name] === 1 ) ? true : false;
            }

        } );

        
    }

    /**
     * 
     * @returns void
     */
    function sendEditContactData() {

        let data_elements = document.getElementsByClassName("q_mailing_contact_data");

        let ajax_data = {};

        let current_element;
        let current_id;
        for ( let i=0; i<data_elements.length; i++ ) {

            current_element = data_elements[i];

            if ( current_element === null || current_element === undefined ) {
                console.error("sendEditContactData(): contacts popup contains invalid data element");
                continue;
            }

            current_id = current_element.id.replace("q_mailing_popup_", "");

            switch(current_element.type) {

                case "text":
                    ajax_data[current_id] = current_element.value;
                    break;
                    case "email":
                    ajax_data[current_id] = current_element.value;
                    break;
                case "checkbox":
                    ajax_data[current_id] = current_element.checked ? 1 : 0;
                    break;

            }

        }

        let nonce = "q_invoice_nonce";
        /*
        if ( nonce === null || nonce === undefined ) {
            console.error("sendEditContactData(): sendEditContactData(): no nonce element for edit contacts");
            return;
        }
*/
        let destination = window.location.origin + "/wp-admin/admin-ajax.php";

        let data = "action=q_edit_contact&data=" + JSON.stringify(ajax_data) + "&nonce=" + nonce.value;

        let callback = ( response ) => {

            response = JSON.parse(response);

            showResponseMsg( response.msg, response.success );

            if ( ! response.success ) {   
                return;
            }

            let contactDataSource = document.getElementById("q_mailing_contact_data_" + ajax_data.id);
            
            if ( contactDataSource === null || contactDataSource === undefined ) {
                console.error("Looks like the contact table structure is compromised. Cant update row content. Reload the page.");
                return;
            }

            contactDataSource.innerText = btoa(JSON.stringify(ajax_data));

            updateUpdatedContactRow( ajax_data );

            let activeContainerVisibility = document.getElementById("Active").style.display;

            if ( ajax_data.status === 1 && activeContainerVisibility === "none" || ajax_data.status === 0 && activeContainerVisibility !== "none" ) {

                let from = "active";
                let to = "archive"

                if ( activeContainerVisibility === "none" ) {
                    to = "active";
                    from = "archive"
                } 

                moveContactRow(ajax_data.id, from, to);
            }

            qMailingTogglePopupVisibility('q_mailing_edit_contacts_popup_container');

        }

        sendOverAjax(destination, data, callback);

    }

    /**
     * Sends a ajax request to destinations and call callback after response.
     * @param {Ajax url} destination 
     * @param {POST data} data 
     * @param {Fcn. to handle response} callback 
     * @returns 
     */
    function sendOverAjax(destination, data, callback=null) {

        if ( data === null || data === undefined ) {
            console.error("sendOverAjax(): data is not valid");
            return;
        }

        if ( destination === null || destination === undefined ) {
            console.error("sendOverAjax(): destination is not valid");
            return;
        }

        let xmlRequest = new XMLHttpRequest();

        if ( callback !== null ) {

            xmlRequest.onload = () => {
                callback(xmlRequest.responseText);
            }

        }

        xmlRequest.open("POST", destination);

        xmlRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xmlRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xmlRequest.send( data );

    }


    function showResponseMsg(msg, success) {

        let msg_container = document.getElementById("q_mailing_response_container");

        if ( msg_container === null || msg_container === undefined ) {

            msg_container = document.createElement("div");
            msg_container.id = "q_mailing_response_container";
            msg_container.style.display = "none";
            msg_container.classList.add("q_mailing_ajax_response_container");
            document.getElementById("q_mailing_edit_contacts_popup_container").parentElement.append(msg_container);

        }

        msg_container.innerText = msg;

        if ( success ) {
            msg_container.classList.remove("bg-error");
            msg_container.classList.add("bg-success");
        } else {
            msg_container.classList.add("bg-error");
            msg_container.classList.remove("bg-success");
        }

        msg_container.style.display = "block";

        window.setTimeout(() => {
            msg_container.style.display = "none";
        }, 2000);

    }

    /**
     * After updating the contact over the popup, this function loads the new data into the affected table row.
     * @param {data send to server} ajax_server
     */
    function updateUpdatedContactRow(ajax_data) {

        let keys = Object.keys(ajax_data);

        let currentTableCell;
        keys.forEach( key => {

            if ( key === "id" ) {
                return;
            }

            currentTableCell = document.getElementById("q_mailing_contact_" +  key + "_" + ajax_data.id );

            
            if ( currentTableCell === null || currentTableCell === undefined ) {
                return;
            }
            

            if ( key !== "status" ) {
                currentTableCell.innerText = ajax_data[key];
            } 
        } )

    }

    /**
     * Moves a contact row from archiv into active or vice versa after changing the contact state.
     * @param {id of the contact which as to be moved. } id 
     * @param {source archive or active} from
     * @param {destination archive or active} to
     */
    function moveContactRow(id, from, to) {

        let rowToBeMoved = document.getElementById("q_mailing_contact_data_" + id);

        if ( rowToBeMoved === null || rowToBeMoved === undefined ) {
            console.error("moveContactRowIntoArchiv(): Cant find the row, which has to be moved into archive.");
            return;
        }

        rowToBeMoved = rowToBeMoved.parentElement;

        let fromTableContainer = null;
        let toTableContainer = null;
        
        if ( from === "archive" ) {
            fromTableContainer =  document.getElementById("Archive");
            toTableContainer =  document.getElementById("Active");
        } else {
            toTableContainer =  document.getElementById("Archive");
            fromTableContainer =  document.getElementById("Active");
        }


        if ( toTableContainer === null || toTableContainer === undefined ) {
            console.error("moveContactRow(): Cant find the " + to + " container.");
            return;
        }

        if ( fromTableContainer === null || fromTableContainer === undefined ) {
            console.error("moveContactRow(): Cant find the " + from + " container.");
            return;
        }

        let toTableBody = toTableContainer.getElementsByTagName("tbody");

        if ( toTableBody === null || toTableBody === undefined || toTableBody.length === 0 ) {
            toTableBody = document.createElement("tbody");
            toTableContainer.append( toTableBody )
            toTableBody.append( rowToBeMoved );
        } else {
            toTableBody[0].append( rowToBeMoved );
        }

        let statusTableCell = document.getElementById("q_mailing_contact_status_" + id);

        if ( from === "archive" ) {
            statusTableCell.classList.add("aktiv");
            statusTableCell.classList.remove("archiv");
        } else {
            statusTableCell.classList.remove("aktiv");
            statusTableCell.classList.add("archiv");
        }

       

    }

    

    /**
     * Removes the contact row from the table and triggers an delete ajax request.
     * @param {id of the contact} id 
     */
    function deleteContact( id ) {

        // Triggers ajax request

        return;

        let nonce = "q_invoice_nonce";

        if ( nonce === null || nonce === undefined ) {
            console.error("sendEditContactData(): sendEditContactData(): no nonce element for edit contacts");
            return;
        }

        let destination = window.location.origin + "/wp-admin/admin-ajax.php";

        let data = "action=q_delete_contact&data=" + JSON.stringify({id : id});

        let callback = (response) => {

            response = JSON.parse( response );

            if ( response.success ) {
                // Removes row from table
                let rowToBeRemoved = document.getElementById( "q_mailing_contact_data_" + id );

                if ( rowToBeRemoved === null || rowToBeRemoved === undefined ) {
                    console.error("deleteContact(): there is no row with the given id");
                    return;
                }

                rowToBeRemoved = rowToBeRemoved.parentElement;

                let tbody = rowToBeRemoved.parentElement;

                tbody.removeChild( rowToBeRemoved );

            }

            
            showResponseMsg(response.msg, response.success);

        }
           
        sendOverAjax(destination, data, callback);
        

    }

}