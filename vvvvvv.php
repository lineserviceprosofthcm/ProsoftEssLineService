<?php 
require('fortest.php');
$channelSecret = 'b777323834edf3fd96558faf97a3a69a';
$access_token  = '5ZP5bMi9tgEXR9Zwq5+TBJ6C5pv5SMbaIWBes4l1MGHQph3JbHhQyqRWD+e7pxEKsmwy0i5qQWc7gEAhudIS2GuUPEQmNY21zhsL0nrRbkQVCVUl8HoKS7s11KpkPiOXIapgGt7EALkwBLicU19DqgdB04t89/1O/w1cDnyilFU=';
$bot = new BOT_API($channelSecret, $access_token);
echo "Start Code";
echo "<br>OK BOT_API";
echo "<br>OK Require";
$va = $_GET['va'];

if(!empty($va)){
    bot->SendMessageTo("U3b8800b03f1c9d49899b6fd2da70bbb6","5555");
	echo "<br>".$va;
}
?>
