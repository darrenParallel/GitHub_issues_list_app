$(function() {
    $('#datatable').dataTable({
        "order": [[ 3, "desc" ]]
    });
    
    $('#error_list').click(function() {
        $('#list-div').hide();
        $('#add-div').hide();
        $('#err-div').show();
    });
    
    $('#add_new_issue').click(function() {
        $('#err-div').hide();
        $('#list-div').show();
        $('#add-div').show();
        
    });
    
    $('#list_json_issues').click(function() {
        window.location.href = "index.php";
        return false;
    });
    
    $('#add_issue').click(function() {
        
        if(!$.trim(document.getElementById("clientName").value)) {
            alert('Please add the Clients Name');
            return false;
        }
        
        if(!$.trim(document.getElementById("actionRequest").value).length) {
            alert('Please add a Title (Action)');
            return false;
        }
        
        if(!$.trim(document.getElementById("issueDescription").value).length) {
            alert('Please add a Description');
            return false;
        }
        
        $(".loader").fadeIn("slow");
        $.ajax({
            type: "GET",
            data: {
                set : "1",
                clientName : document.getElementById("clientName").value,
                issuePriority : document.getElementById("issuePriority").value,
                issueCategory : document.getElementById("issueCategory").value,
                actionRequest : document.getElementById("actionRequest").value,
                issueDescription : document.getElementById("issueDescription").value
            },
            dataType: "text",
            url: "router.php",
            success: function(data) {
                $(".loader").fadeIn("slow");
                location.reload(true);
            },
            error: function(jqXHR, textStatus, errorThrown) { 
                if (textStatus+errorThrown != '') {
                    $(".loader").fadeIn("slow");
                    console.log(jqXHR, textStatus, errorThrown);
                    location.reload(true);
                }
            }
         });
    });
    
    $('#refresh_json').click(function() {
        $(".loader").fadeIn("slow");
        $.ajax({
            type: "GET",
            data: {
                set : "3"
            },
            dataType: "text",
            url: "router.php",
            success: function(data) {
                $(".loader").fadeIn("slow");
                location.reload(true);
            },
            error: function(jqXHR, textStatus, errorThrown) { 
                if (textStatus+errorThrown != '') {
                    $(".loader").fadeIn("slow");
                    console.log(jqXHR, textStatus, errorThrown);
                    location.reload(true);
                }
            }
       });
   });
});


$(window).load(function() {
    $(".loader").fadeOut("slow");
});