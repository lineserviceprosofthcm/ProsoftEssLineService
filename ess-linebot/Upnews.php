<?php
require("Connect.php");
$newsID = $_POST['txtNews'];

 	$link = ConnectDatabase();
    $sql = "UPDATE news SET status = 'ส่งแล้ว' WHERE newsid = '".$newsID."'";
    $result = mysqli_query($link, $sql);
    if ($result === true) {
     	
        return json_encode("value");
    }
    $link->close();
?>