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

function ConnectDatabase()
{

    $connectstr_dbhost = '31.170.166.134';
    $connectstr_dbname = 'u663869224_line';
    $connectstr_dbusername = 'u663869224_hrmi';
    $connectstr_dbpassword = 'v06dt22ssn';

    $link = mysqli_connect($connectstr_dbhost, $connectstr_dbusername, $connectstr_dbpassword, $connectstr_dbname);

    if (!$link)
    {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }
    mysqli_set_charset($link, "utf8");

    return $link;
}
