
function BindApprove(id){
    // var id = obj.data('id');
    console.log(id);
    $.ajax({
        url: "Approve.php",
        data: {
           txtDoc: id
        },
        type: 'post',
        success: function (data) {
            if(data == true){
                $("tr[data-id='"+id+"']").remove();
            }
            $("tr[data-id='"+id+"']").remove();
            BindSuccess(id);
        },
        error: function (e) {
            console.log("error");
        }
    });

}


function BindNotApprove(id){
    $.ajax({
        url: "NotApprove.php",
        data: {
           txtDoc: id
        },
        type: 'post',
        success: function (data) {
            if(data == true){
                $("tr[data-id='"+id+"']").remove();
            }
            $("tr[data-id='"+id+"']").remove();
            BindSuccess(id);
        },
        error: function (e) {
            console.log("error");
        }
    });

}


function BindSuccess(id){
    $.ajax({
        url: "../testbot.php",
        data: {
            txtDoc: id
        },
        type: 'post',
        success: function (data) {
            console.log(data);
        },
        error: function (e) {
            console.log("error");
        }
    });

}

