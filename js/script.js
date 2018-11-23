$('table').dataTable();
viewData();
$('#update').prop("disabled",true);

function viewData(){
    $.get('server.php', function(data){
        $('tbody').html(data)
    })
}

function saveData(){
    var hd = $('#hd').val()
    var dt = $('#dt').val()
    $.post('server.php?p=add', {hd:hd, dt:dt}, function(data){
        viewData()
        $('#hd').val(' ')
        $('#dt').val(' ')
    })
}

function editData(newsid,hd, dt) {
    $('#newsid').val(newsid)
    $('#hd').val(hd)
    $('#dt').val(dt)
    $('#newsid').prop("readonly",true);
    $('#save').prop("disabled",true);
    $('#update').prop("disabled",false);
}

function updateData(){
    var newsid = $('#newsid').val()
    var hd = $('#hd').val()
    var dt = $('#dt').val()
    $.post('server.php?p=update', {newsid:newsid, hd:hd, dt:dt}, function(data){
        viewData()
        $('#newsid').val(' ')
        $('#hd').val(' ')
        $('#dt').val(' ')
        $('#id').prop("readonly",false);
        $('#save').prop("disabled",false);
        $('#update').prop("disabled",true);
    })
}

function deleteData(newsid){
    $.post('server.php?p=delete', {newsid:newsid}, function(data){
        viewData()
    })
}

function removeConfirm(newsid){
    var con = confirm('Are you sure, want to delete this data!');
    if(con=='1'){
        deleteData(newsid);
    }
}

$(function() {

    var $sidebar   = $("#sidebar"),
        $window    = $(window),
        offset     = $sidebar.offset(),
        topPadding = 15;

    $window.scroll(function() {
        if ($window.scrollTop() > offset.top) {
            $sidebar.stop().animate({
                marginTop: $window.scrollTop() - offset.top + topPadding
            });
        } else {
            $sidebar.stop().animate({
                marginTop: 0
            });
        }
    });

});

function SendMS(id){
    $.ajax({
        url: "Upnews.php",
        data: {
            txtNews: id
        },
        type: 'post',
        success: function (data) {
            window.location.replace("HR.php");
            MSSuccess(id);
        },
        error: function (e) {
            console.log("error");
        }
    });

}


function MSSuccess(id){
    $.ajax({
        url: "https://prosoftesslineservice.herokuapp.com/esslinebot.php",
        data: {
            txtNews: id
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
