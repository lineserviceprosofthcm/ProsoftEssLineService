<?php

$userID = $_POST['AddIDTH'];
if(!empty($userID)){
    CheckID($userID);
}

//ตรวจเช็คว่า LineID นี้เคยมีการลงทะเบียนเชื่อมกับ EmpID ไว้หรือไม่//
function CheckID($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT * FROM emLineuserconnect WHERE UserID = '".$userID."' ";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result))
        {
            if(($row['EmpID'] > 0 ) || ($row['EmpID'] == NULL))
            {
              return 1;
            }
        }
    }
    else
    {
        $sql = "INSERT INTO `emLineuserconnect`(`ConnectID`, `UserID`, `ThisMenu`, `LatestDate`,  `IsStatus`) VALUES (uuid(),'".$userID."','0',now(),0)";
        $link->query($sql);
        return 0;
    }
    $link->close();
}

