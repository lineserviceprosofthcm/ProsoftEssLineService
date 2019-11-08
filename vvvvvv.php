<?php 
require('line-bot.php');
include('essconnect.php');
$channelSecret = 'b777323834edf3fd96558faf97a3a69a';
$access_token  = '5ZP5bMi9tgEXR9Zwq5+TBJ6C5pv5SMbaIWBes4l1MGHQph3JbHhQyqRWD+e7pxEKsmwy0i5qQWc7gEAhudIS2GuUPEQmNY21zhsL0nrRbkQVCVUl8HoKS7s11KpkPiOXIapgGt7EALkwBLicU19DqgdB04t89/1O/w1cDnyilFU=';
$bot = new BOT_API($channelSecret, $access_token);
$NewsHDID = $_POST['NewsHDID'];
$News = $_POST['News'];
$LineIDLeaveRecord = $_POST['LineIDLeaveRecord'];
$Detail = $_POST['Detail'];
$LineID_NextApprove = $_POST['LineID_NextApprove'];
$WaitApprove = $_POST['WaitApprove'];
$LineID_EmpID = $_POST['LineID_EmpID'];
$ApproveStatus = $_POST['ApproveStatus'];
$va = $_GET['va'];

echo "Start Code";
echo "<br>OK BOT_API";
echo "<br>OK Require";
echo "<br>".$bot->userId;


if(!empty($va)){
    $bot->SendMessageTo("U3b8800b03f1c9d49899b6fd2da70bbb6","5555");
	echo "<br>".$va;
}

if (!empty($bot->isEvents)) {
	$bot->SendMessageTo("U3b8800b03f1c9d49899b6fd2da70bbb6","aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
	echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
}
?>
