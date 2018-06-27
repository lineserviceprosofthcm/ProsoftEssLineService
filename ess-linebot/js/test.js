//  $(document).ready(function(){
//   var A = $("#statusy");
//   var B = $("#statusn");
//   if (A = "อนุมัติ") {
//     $('.dis-status').prop("disabled", true);
//   }else if (B = "ไม่อนุมัติ") {
//   	$('.dis-status').prop("disabled", true);
//   }else{
//   	$('.dis-status').prop("disabled", false);
//   }
// });
function DeleteApprove(id){
    $.ajax({
        url: "DeleteApprove.php",
        data: {
           txtDelete: id
        },
        type: 'post',
        success: function (data) {
            if(data == true){
                $("tr[data-id='"+id+"']").remove();
            }
            $("tr[data-id='"+id+"']").remove();
        },
        error: function (e) {
            console.log("error");
        }
    });

}
