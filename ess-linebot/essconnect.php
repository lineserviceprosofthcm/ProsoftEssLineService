<?php
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

//**เอาไว้ให้ HR ส่งข่าววว*//
function NEWS($news)
{
    $link = ConnectDatabase();
    $sql = "SELECT * FROM news WHERE newsid = '".$news."'  AND IsDelete = 0";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $str = "";
      while($row = mysqli_fetch_assoc($result)) 
      {
          $str.="".$row['newsHD']."\n".$row['newsDT']."";
      }
      return $str;
  }
  return "ยังไม่มีข่าวสารใดๆอัพเดท";
  $link->close();
}

function SendUserID()
{
  $link = ConnectDatabase();
  $sql = "SELECT UserID FROM emLineuserconnect WHERE UserID IS NOT NULL AND EmpID IS NOT NULL AND IsStatus = 1";
  $result = mysqli_query($link, $sql);
  $arr = [];
  if (mysqli_num_rows($result) > 0)
  {
    while($row = mysqli_fetch_assoc($result))
    {
        array_push($arr,$row['UserID']);
    }
}
return $arr;
$link->close();
}

////////////////////////service ขึ้นแรกสุดหลังทำการ Add Essbotline////////////////////////////////////////////////////////////////////////////////////////////

//ตรวจเช็คว่า LineID นี้เคยมีการลงทะเบียนเชื่อมกับ EmpID ไว้หรือไม่//
function CheckID($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT COUNT(UserID) AS COUNT1 FROM emLineuserconnect WHERE UserID = '".$userID."' ";
    $result = mysqli_query($link, $sql);
    while($row = mysqli_fetch_assoc($result))
    {
        if($row['COUNT1'] > 0 )
        {
          return 1;
        }
        else
        {
        $sql = "INSERT INTO `emLineuserconnect`(`ConnectID`, `UserID`, `ThisMenu`, `LatestDate`,  `IsStatus`) VALUES (uuid(),'".$userID."','0',now(),0)";
        $link->query($sql);
        return 0;
    }
}

$link->close();
}

//UpdateThisMenu ในขึ้นตอนต่าง//
function UpdateThisMenu($userID, $menuID)
{
    $link = ConnectDatabase();
    $sql = "UPDATE emLineuserconnect SET ThisMenu = ".$menuID." WHERE UserID = '".$userID."' ";
    $result = mysqli_query($link, $sql);
    return true;
    $link->close();
}

//Select ดู Status ว่า UserID นี้ลงทะเบียนรึยัง//
function GetStatus($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT IsStatus FROM emLineuserconnect WHERE UserID = '".$userID."' ";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            return $row['IsStatus'];
        }
    }
    $link->close();
}

//Select ดู menu ว่า UserID นี้กำลังอยู่ในขั้นตอนไหน//
function GetThisMenu($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT * FROM emLineuserconnect WHERE UserID = '".$userID."' ";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            return $row['ThisMenu'];
        }
    }
    else
    {
        return 1;
    }
    $link->close();
}

//update ภาษาตอนที่ผู้ใช้งานเลือก//
function AddLanguageline($Language, $userID)
{
    $link = ConnectDatabase();
    $sql = "UPDATE emLineuserconnect SET Language = '".$Language."' WHERE UserID = '".$userID."' ";
    $result = mysqli_query($link, $sql);
    if ($Language == "ENG")
    {
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '4' WHERE UserID = '".$userID."' ";
        $link->query($sql1);
        return "Please Enter Your Employee ID";
    }
    else if ($Language == "TH")
    {
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '2' WHERE UserID = '".$userID."' ";
        $link->query($sql1);
        return "กรุณาพิมพ์รหัสพนักงาน";
    }
    else
    {
        return "กรุณาเลือกภาษา\nPlease select language.";
    }
    return "มีบางอย่างผิดพลาด";
    $link->close();
}

//check รหัสพนักงานว่าเคยมีการลงทะเบียนแล้วหรือไม่ (ภาษาไทย)//
function AddIDTH($emcode,$userID)
{
    $link = ConnectDatabase();
    $qu = "SELECT * FROM emEmployee WHERE EmpCode = '".$emcode."'";
    $results = mysqli_query($link, $qu);
    if (mysqli_num_rows($results) > 0)
    {
        $qusql = "SELECT emLineuserconnect.EmpID FROM emEmployee,emLineuserconnect
        WHERE emEmployee.EmpID =  emLineuserconnect.EmpID AND emEmployee.EmpCode = '".$emcode."'";
        $resultt = mysqli_query($link, $qusql);
        if ($rows = mysqli_num_rows($resultt) > 0)
        {
            return "รหัสพนักงานนี้ ได้มีการลงทะเบียนไว้แล้ว";
        }
        else
        {
            $sql = "SELECT EmpID FROM emEmployee WHERE EmpCode = '".$emcode."'";
            $result = mysqli_query($link, $sql);
            while ($row = mysqli_fetch_assoc($result))
            {
                $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '3' , EmpID = '".$row['EmpID']."' WHERE UserID = '".$userID."' ";
                $link->query($sql1);
                return "กรุณาพิมพ์เลข 4 ตัวท้ายของบัตรประชาชน";
            }
        }
    }
return "ไม่มีรหัสพนักงานนี้อยู่ในสารระบบ";
$link->close();
}


//ยันยันตัวตนด้วยเลข 4 ตัวท้ายของบัตรประชาชน (ภาษาไทย)//
function confirmsTH($idcard, $userID)
{
    $link = ConnectDatabase();
    $A = substr($idcard, 0, 1);
    $B = substr($idcard, 1, 2);
    $C = substr($idcard, 3, 3);
    $mixidcard = $A."-".$B."-".$C;

    $sql = "SELECT emPerson.IdentityCard , emPerson.FirstName
    FROM emLineuserconnect,emPerson ,emEmployee
    WHERE SUBSTRING( emPerson.IdentityCard ,12 , 6  ) = '".$mixidcard."'
    AND emLineuserconnect.EmpID = emEmployee.EmpID
    AND emPerson.PersonID = emEmployee.PersonID
    AND emLineuserconnect.UserID = '".$userID."'"; // เพิ่มใหม่ AND emLineuserconnect.UserID = '".$userID."'
    $result = mysqli_query($link, $sql);
    if ($rows = mysqli_num_rows($result) > 0)
    {
        while ($rows = mysqli_fetch_assoc($result)) {
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0', IsStatus = 1 WHERE UserID = '".$userID."' ";
        $link->query($sql1);
        return "สวัสดีคุณ ".$rows['FirstName']." ระบบได้ทำการ ลงทะเบียนเรียบร้อยแล้ว\nสามารถใช้บริการได้ด้วยการเลือก Menu";
        }
    }
    else
    {
        $sql = "UPDATE emLineuserconnect SET EmpID = '0' , ThisMenu = '2' WHERE UserID = '".$userID."' ";
        $link->query($sql);
        return "เลข 4 ตัวท้ายไม่ถูกต้องกรุณาทำรายการใหม่\nกรุณาพิมพ์เลข 4 ตัวท้ายของบัตรประชาชนใหม่";
    }
    return "มีบางอย่างผิดพลาด";
    $link->close();
}

//check รหัสพนักงานว่าเคยมีการลงทะเบียนแล้วหรือไม่ (ENG)//
function AddIDENG($emcode, $userID)
{
    $link = ConnectDatabase();
    $qu = "SELECT * FROM emEmployee WHERE EmpCode = '".$emcode."'";
    $results = mysqli_query($link, $qu);
    if (mysqli_num_rows($results) > 0)
    {
        $qusql = "SELECT emLineuserconnect.EmpID FROM emEmployee,emLineuserconnect
        WHERE emEmployee.EmpID =  emLineuserconnect.EmpID AND emEmployee.EmpCode = '".$emcode."'";
        $resultt = mysqli_query($link, $qusql);
        if ($rows = mysqli_num_rows($resultt) > 0)
        {
            return "This employee ID has already been registered.";
        }
        else
        {
            $sql = "SELECT EmpID FROM emEmployee WHERE EmpCode = '".$emcode."'";
            $result = mysqli_query($link, $sql);
            while ($row = mysqli_fetch_assoc($result))
            {
                $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '4' , EmpID = '".$row['EmpID']."' WHERE UserID = '".$userID."' ";
                $link->query($sql1);
                return "Please enter the last 4 digits of your ID card.";
            }
        }
    }
    return "No employee ID exists in the system.";
    $link->close();
}

//ยันยันตัวตนด้วยเลข 4 ตัวท้ายของบัตรประชาชน (ENG)//
function confirmsENG($idcard, $userID)
{
    $link = ConnectDatabase();
    $A = substr($idcard, 0, 1);
    $B = substr($idcard, 1, 2);
    $C = substr($idcard, 3, 3);
    $mixidcard = $A."-".$B."-".$C;

    $sql = "SELECT emPerson.IdentityCard 
    FROM emLineuserconnect,emPerson ,emEmployee 
    WHERE SUBSTRING( emPerson.IdentityCard ,12 , 6  ) = '".$mixidcard."'
    AND emLineuserconnect.EmpID = emEmployee.EmpID 
    AND emPerson.PersonID = emEmployee.PersonID
    AND emLineuserconnect.UserID = '".$userID."'"; // เพิ่มใหม่ AND emLineuserconnect.UserID = '".$userID."'
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0)
    {
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0', IsStatus = 1 WHERE UserID = '".$userID."' ";
        $link->query($sql1);
        return "The system has already been registered.\nThe service is available by selecting the item.";
    }
    else
    {
        $sql = "UPDATE emLineuserconnect SET EmpID = '0' , ThisMenu = '4' WHERE UserID = '".$userID."' ";
        $link->query($sql);
        return "The last 4 digits are not valid.\nPlease make a new entry.\nPlease enter employee code.";
    }

    $link->close();
}

//Select เพื่อนตรวจดูภาษาในการตอบกลับ//
function GetLanguage($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT Language FROM emLineuserconnect WHERE UserID = '".$userID."' ";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['Language'];
        }
    }
    $link->close();
}

//เปลี่ยนภาษา//
function reLanguage($Language, $userID)
{
    $link = ConnectDatabase();
    $sqls = "UPDATE emLineuserconnect SET ThisMenu = '0', Language = '".$Language."' WHERE UserID = '".$userID."' ";
    $link->query($sqls);
    if ($Language == "ENG") 
    {
        return "You have made changes to your language.";
    } 
    else if($Language == "TH")
    {
        return "ระบบได้ทำการเปลี่ยนแปลงภาษาเรียบร้อยแล้ว.";
    }
    else
    {
        return "กรุณาเลือกภาษา\nPlease select language.";
    }
    $link->close();
}

//ยกเลิกการลงทะเบียน//
function Unregister($idcard, $userID)
{
    $link = ConnectDatabase();
    $A = substr($idcard, 0, 1);
    $B = substr($idcard, 1, 2);
    $C = substr($idcard, 3, 3); 
    $mixidcard = $A."-".$B."-".$C;
    
    $sql = "SELECT emPerson.IdentityCard,emLineuserconnect.Language FROM emLineuserconnect,emPerson ,emEmployee WHERE SUBSTRING( emPerson.IdentityCard ,12 , 6  ) = '".$mixidcard."'
    AND emLineuserconnect.EmpID = emEmployee.EmpID AND emPerson.PersonID = emEmployee.PersonID";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['Language'] == "ENG") {
                $sql1 = "DELETE FROM `emLineuserconnect` WHERE UserID = '".$userID."' ";
                $link->query($sql1);
                return "The system has been canceled successfully.";
            } else {
                $sql1 = "DELETE FROM `emLineuserconnect` WHERE UserID = '".$userID."' ";
                $link->query($sql1);
                return "ระบบได้ทำการ ยกเลิกการลงทะเบียนเรียบร้อยแล้ว";
            }
        }
    } else {
        $sql = "SELECT * FROM emLineuserconnect WHERE UserID = '".$userID."'";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['Language'] == "ENG") {
                    return "Last 4 digits are invalid. Please reprint.่\nTo cancel a print  Cancel.";
                } else {
                    return "เลข 4 ตัวท้ายไม่ถูกต้องกรุณาพิมพ์ใหม่\nต้องการยกเลิกการทำรายการพิมพ์ ยกเลิก";
                }
            }
        }
    }
    return "มีบางอย่างผิดพลาด";
    $link->close();
}

//////////////////////////////////////////////////////////////////สิ้นสุด service//////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////การโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////

//สำหรับ check ตัวแทน//
function CheckDeputi($userID)
{
    $link = ConnectDatabase();
    $sqls = "SELECT emGrantApprove.GrantApproveID AS GrantID FROM emGrantApprove,emLineuserconnect WHERE emGrantApprove.ApproverID = emLineuserconnect.EmpID
    AND emLineuserconnect.UserID = '".$userID."' ";
    $results = mysqli_query($link, $sqls);
    if (mysqli_num_rows($results) > 0) 
    {
        while($row = mysqli_fetch_assoc($results)) 
        {
            $sql1 = "SELECT DeputizeGrantApproveID
            FROM emDeputizeApprove
            WHERE  DeputizeGrantApproveID = '".$row['GrantID']."'
            AND CONVERT(emDeputizeApprove.StartDate,DATE) <= CONVERT(curdate(),DATE)            
            AND CONVERT(emDeputizeApprove.EndDate,DATE) >= CONVERT(curdate(),DATE)";
            $result1 = mysqli_query($link, $sql1);
            if (mysqli_num_rows($result1) > 0) 
            {
                return 1;
            }
            else
            {
                return 0;
            }
        }
    }

    $link->close();
}



//ใช้ยกเลิกการทำรายการขออนุมัติ//
function Cancels($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT * FROM hrTimeLeaveRecordConfirm WHERE  UserID = '".$userID."' ORDER BY LeaveID DESC LIMIT 1";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sql1 = "DELETE FROM `hrTimeLeaveRecordConfirm` WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."'";
            $link->query($sql1);
            return "ยกเลิกการทำรายการอนุมัติแล้ว";
        }
    }
    $link->close();
}

//เพิ่มเอกสารขอลา//
function AddApp($typeapp,$userID)
{
    $link = ConnectDatabase();
    $year = date("Y");
    $sql = "SELECT * FROM hrLeaveType WHERE LeaveTypeCode = '".$typeapp."'";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0)
    {
        while ($rowq = mysqli_fetch_assoc($result))
        {
            //ดึง EmpID ออกมา//
            $quss = "SELECT * FROM emLineuserconnect WHERE emLineuserconnect.UserID = '".$userID."'";
            $resulttss = mysqli_query($link, $quss);
            while ($row_quss = mysqli_fetch_assoc($resulttss)) 
            {
                //Check ว่าวันนี้มีการขอลาไปหรือยัง//
                $qu = "SELECT * FROM hrTimeLeaveRecord,emLineuserconnect WHERE hrTimeLeaveRecord.DocuDate = CURDATE()
                AND hrTimeLeaveRecord.EmpID = '".$row_quss['EmpID']."' AND emLineuserconnect.UserID = '".$userID."'";
                $resultt = mysqli_query($link, $qu);
                if (mysqli_num_rows($resultt) > 0)
                {
                    while ($row = mysqli_fetch_assoc($resultt))
                    {
                        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                        $link->query($sql1);
                        return "ไม่สามารถขอลาได้ เนื่องจากมีการขอลา เอกสาร ".$row['Docuno']." ไปแล้ว";
                    }
                }
                else
                {

                    $sql = "select hrEmpWorkProfile.OrgID,emLineuserconnect.EmpID
                    from emEmployee,hrEmpWorkProfile,emLineuserconnect
                    where emEmployee.EmpID = hrEmpWorkProfile.EmpID
                    AND emLineuserconnect.UserID = '".$userID."'
                    AND emLineuserconnect.EmpID = emEmployee.EmpID";
                    $result = mysqli_query($link, $sql);
                    while ($row = mysqli_fetch_assoc($result))
                    {
                                //Check ว่าจำนวนวันลาของประเภทการลายังลาได้อยู่ไหม//
                        $sqli = "select Days from hrTimeLeaveRema where RecordYear = '".$year."'
                        AND EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$rowq['LeaveTypeID']."'";
                        $results = mysqli_query($link, $sqli);
                                //Check ว่ามีข้อมูลอยู่ใน hrTimeLeaveRema หรือยัง//
                        if (mysqli_num_rows($results) > 0)
                        {
                            while ($rows = mysqli_fetch_assoc($results))
                            {
                                if($rows['Days'] > 0)
                                {
                                    $sql2 = "INSERT INTO hrTimeLeaveRecordConfirm (EmpID, OrgID ,UserID,LeaveTypeID) VALUES ('".$row['EmpID']."','".$row['OrgID']."','".$userID."','".$rowq['LeaveTypeID']."')";
                                    $link->query($sql2);
                                    $sql3 = "UPDATE emLineuserconnect SET ThisMenu = '2' WHERE UserID = '".$userID."' ";
                                    $link->query($sql3);
                                    return "กรุณากรอกสาเหตุการลา";
                                }
                                else
                                {
                                    $sql = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                                    $link->query($sql);
                                    return "จำนวนวันลา".$row['LeaveTypeName']."ของคุณหมดแล้ว";
                                }
                            }    
                        }
                        else
                        {
                                    //กรณียังไม่มีข้อมูลจึงไปดึงข้อมูลมาจาก hrAssignLeaveNum ซึ่งเป็นข้อมูลตั้งต้น//
                            $sqli = "SELECT * FROM  hrAssignLeaveNum WHERE EmpID = '".$row['EmpID']."'";
                            $resultss = mysqli_query($link, $sqli);
                                    //Check ว่ามีข้อมูลในตารางตั้งต้นหรือไม่//
                            if (mysqli_num_rows($resultss) > 0)
                            {
                                while ($rows = mysqli_fetch_assoc($resultss))
                                {
                                    $sql2 = "INSERT INTO `hrTimeLeaveRema`(`RecordID`, `RecordYear`, `EmpID`, `LeaveTypeID`, `Days`, `Hours`, `OrgID`, `DaysNow`, `HoursNow`)
                                    VALUES (uuid(),'".$year."','".$row['EmpID']."','".$rowq['LeaveTypeID']."','".$rows['AssDayNum']."','0','".$row['OrgID']."','0','0')";
                                    $results = mysqli_query($link, $sql2);
                                    if ($results === true) 
                                    {
                                        $sql2 = "INSERT INTO hrTimeLeaveRecordConfirm (EmpID, OrgID ,UserID,LeaveTypeID) VALUES ('".$row['EmpID']."','".$row['OrgID']."','".$userID."','".$rowq['LeaveTypeID']."')";
                                        $link->query($sql2);
                                        $sql3 = "UPDATE emLineuserconnect SET ThisMenu = '2' WHERE UserID = '".$userID."' ";
                                        $link->query($sql3);
                                        return "กรุณากรอกสาเหตุการลา";
                                    }

                                }
                            }
                            else
                            {
                                $sqli = "SELECT * FROM hrLeaveType WHERE IsDeleted = 0 AND OrgID = '".$row['OrgID']."' AND LeaveTypeCode = '".$typeapp."'";
                                $resultss = mysqli_query($link, $sqli);
                                        //Check ว่ามีข้อมูลในตารางตั้งต้นหรือไม่//
                                if (mysqli_num_rows($resultss) > 0)
                                {
                                    while ($rows = mysqli_fetch_assoc($resultss))
                                    {
                                        $sql2 = "INSERT INTO `hrTimeLeaveRema`(`RecordID`, `RecordYear`, `EmpID`, `LeaveTypeID`, `Days`, `Hours`, `OrgID`, `DaysNow`, `HoursNow`)
                                        VALUES (uuid(),'".$year."','".$row['EmpID']."','".$rowq['LeaveTypeID']."','".$rows['LeaveTypeDayNum']."','0','".$row['OrgID']."','0','0')";
                                        $results = mysqli_query($link, $sql2);
                                        if ($results === true) 
                                        {
                                            $sql2 = "INSERT INTO hrTimeLeaveRecordConfirm (EmpID, OrgID ,UserID,LeaveTypeID) VALUES ('".$row['EmpID']."','".$row['OrgID']."','".$userID."','".$rowq['LeaveTypeID']."')";
                                            $link->query($sql2);
                                            $sql3 = "UPDATE emLineuserconnect SET ThisMenu = '2' WHERE UserID = '".$userID."' ";
                                            $link->query($sql3);
                                            return "กรุณากรอกสาเหตุการลา";
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    else
    {
        return "คุณทำการลาไม่ถูกต้อง กรุณาเลือกประเภทการลา";
    }

    $link->close();
}

//สาเหตุการขอลา//
function AddDetail($detail, $userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT  LeaveID FROM hrTimeLeaveRecordConfirm  WHERE UserID = '".$userID."' ORDER BY LeaveID DESC LIMIT 1";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        while ($row = mysqli_fetch_assoc($result)) 
        {
            $sql = "UPDATE hrTimeLeaveRecordConfirm SET LeaveRemark = '".$detail."'  WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."' ";
            $link->query($sql);
            $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '3' WHERE UserID = '".$userID."' ";
            $link->query($sql1);
            return "กรุณาพิมพ์เลข 4 ตัวท้ายของบัตรประชาชน เพื่อทำการยืนยัน\nสามารถยกเลิกการทำรายการด้วยการพิมพ์ ยกเลิก ++esscon551";
        }
    }
    $link->close();
}

//ยืนยันตนตนสำหรับขออนุมัติลา//
function confirmsapp($idcard,$userID)
{
    $link = ConnectDatabase();
    $A = substr($idcard, 0, 1);
    $B = substr($idcard, 1, 2);
    $C = substr($idcard, 3, 3); 
    $mixidcard = $A."-".$B."-".$C;
    //Selct หารหัสเลข 4 ตัวท้ายของบัตรประชาชนเพื่อนยืนยันตัวตน (ขออนุมัติลา)//
    $sql = "SELECT emPerson.IdentityCard
    FROM emLineuserconnect,emPerson ,emEmployee
    WHERE SUBSTRING( emPerson.IdentityCard ,12 , 6  ) = '".$mixidcard."'
    AND emLineuserconnect.EmpID = emEmployee.EmpID
    AND emPerson.PersonID = emEmployee.PersonID";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        //Select เอกสารล่าสุดที่รอการ Confirm//
        $sql = "SELECT * FROM hrTimeLeaveRecordConfirm WHERE  UserID = '".$userID."' ORDER BY LeaveID DESC LIMIT 1";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                //ตารางการทำงาน ของพนักงานใน UserID นั้นๆ//
                $sql3 = "SELECT ET.WorkDate AS LeaveDate, ET.ShiftID As ShiftID  ,S.TimeIn1 AS StartTime,(CASE S.TimeCount  WHEN 2 THEN S.TimeOUT1          
                WHEN 4 THEN S.TimeOUT2 WHEN 6 THEN S.TimeOUT3 END) AS EndTime, S.WorkTime AS HourLeave                  
                FROM hrTimeEmpWorkScheDT AS ET LEFT JOIN hrTimeShift AS S ON S.ShiftID = ET.ShiftID  LEFT JOIN hrTimeEmpWorkSche AS EW ON EW.EmpWorkScheID = ET.EmpWorkScheID                   
                WHERE EW.EmpID = '".$row['EmpID']."' AND S.IsShiftOT = 0    
                AND CONVERT(ET.WorkDate,DATE) = CONVERT(curdate(),DATE) 
                And ET.OrgID = '".$row['OrgID']."'";
                $results2 = mysqli_query($link, $sql3);
                if (mysqli_num_rows($results2) > 0) 
                {

                    $LE = "LEV";
                    $year = date("Y");
                    $Month = date("m");
                    $maxId = $row['LeaveID'] ;
                    $nextId = $LE.$year.$Month.$maxId;
                            //สร้างเอกสารขออนุมัติลาใน hrTimeLeaveRecord โดยอ้างอิงข้อมูลจาก hrTimeLeaveRecordConfirm//
                    $sql1 = "INSERT INTO `hrTimeLeaveRecord`(`LeaveID`, `Docuno`, `DocuDate`, `EmpID`, `LeaveTypeID`, `LeaveRemark`,`IsUrgent`, `ApproveStatus`, 
                    `ApproveLevel`, `IsCancel`, `TestimonialType`, `SaveToHealth`, `TestimonialStartDate`, `TestimonialEndDate`, `Date1`, `CreatedBy`, 
                    `CreatedDate`, `ModifiedBy`, `ModifiedDate`, `IsDeleted`, `OrgID`) VALUES (uuid(),'".$nextId."',curdate(),'".$row['EmpID']."','".$row['LeaveTypeID']."',
                    '".$row['LeaveRemark']."',0,'W','N',0,'0',0,curdate(),curdate(),curdate(),'3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',curdate(),'3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',
                    curdate(),0,'".$row['OrgID']."')";
                    
                    $results = mysqli_query($link, $sql1);
                    if ($results === true) 
                    {
                                //Select เอกสารที่สร้างจากด้านบนเพื่อน สร้างเอกสาร Detail//
                        $sql2 = "SELECT * FROM  hrTimeLeaveRecord WHERE EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."' AND DocuDate = curdate() ORDER BY LeaveID DESC LIMIT 1";
                        $results1 = mysqli_query($link, $sql2);
                        if (mysqli_num_rows($result) > 0) 
                        {
                            while ($rows = mysqli_fetch_assoc($results1)) 
                            {
                                while ($rows1 = mysqli_fetch_assoc($results2)) 
                                {

                                                //เพิ่มข้อมูลลงในตาราง Deteil//
                                    $sql4 = "INSERT INTO `hrTimeLeaveRecordDetail`(`LeaveDetailID`, `ListNo`, `LeaveDate`, `ShiftID`, `StartTime`, `EndTime`, `HourLeave`, `LeaveID`, 
                                    `CreatedBy`, `CreatedDate`, `ModifiedBy`, `ModifiedDate`, `IsDeleted`, `ShiftDate`, `StartDate`, `EndDate`) 
                                    VALUES (uuid(),'1',curdate(),'".$rows1['ShiftID']."','".$rows1['StartTime']."','".$rows1['EndTime']."','".$rows1['HourLeave']."','".$rows['LeaveID']."' ,'3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',curdate(),
                                    '3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',curdate(),0,curdate(),curdate(),curdate())";
                                    $link->query($sql4);
                                    $year = date("Y");
                                    $sql5 = "Select Days from hrTimeLeaveRema where RecordYear = '".$year."' AND EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."'";
                                    $results3 = mysqli_query($link, $sql5);
                                    if (mysqli_num_rows($results3) > 0) 
                                    {
                                        while ($rows2 = mysqli_fetch_assoc($results3)) 
                                        {
                                                        //ลดจำนวนวันลาลงตามข้อมูลใน DB//
                                            $maxdays = $rows2['Days'] - 1;
                                            $sql1 = "UPDATE hrTimeLeaveRema SET Days = '".$maxdays."' WHERE EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."' AND RecordYear = '".$year."'";
                                            $link->query($sql1);
                                                        //ลบเอกสารที่รอ confirm ในตาราง hrTimeLeaveRecordConfirm//
                                            $sql6 = "DELETE FROM `hrTimeLeaveRecordConfirm` WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."'";
                                            $link->query($sql6);
                                                        //อัพเดทสถานะการทำงานของระบบ//
                                            $sql7 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                                            $link->query($sql7);
                                            return "ระบบได้ทำการ ขออนุมัติลาเรียบร้อยแล้ว";
                                        }
                                    }
                                }

                            }
                        }  
                    }          
                }
                else
                {
                    $sql8 = "DELETE FROM `hrTimeLeaveRecordConfirm` WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."'";
                    $link->query($sql8);
                    $sql9 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                    $link->query($sql9);
                    return "ไม่สามารถทำการลาได้\nเนื่องจากคุณไม่ได้กำหนดกะงาน"; 
                }
            }
        } 
    }
else 
{
    return "เลข 4 ตัวท้ายไม่ถูกต้องกรุณาพิมพ์ใหม่\nสามารถยกเลิกการทำรายการด้วยการพิมพ์ ยกเลิก";
}
return "มีบางอย่างผิดพลาด";
$link->close();
}


//*เช็คลำดับขั้น หาผู้อนุมัติหลักและผู้อนุมัติแทน*//
function CheckLV($userID)
{
    $link = ConnectDatabase();
    $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
    $sql = "SELECT emEmployee.EmpID,hrTimeEmpWorkSche.OrgID 
    FROM emLineuserconnect , emEmployee , hrTimeEmpWorkSche 
    WHERE emLineuserconnect.UserID = '".$userID."'
    AND emEmployee.EmpID = emLineuserconnect.EmpID
    AND emLineuserconnect.EmpID = hrTimeEmpWorkSche.EmpID";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        while($row = mysqli_fetch_assoc($result)) 
        {
            $sql1 = "SELECT  count(GrantApp.GrantApproveID) AS GrantApproveID  FROM emGrantApprove AS GrantApp  
            INNER JOIN emEmpApproved AS EmpApp ON GrantApp.GrantApproveID = EmpApp.GrantApproveID  
            WHERE GrantApp.MenuID = '".$MenuID."' AND EmpApp.EmpID = '".$row['EmpID']."'
            AND GrantApp.IsDeleted = 0 AND GrantApp.IsInactive = 0 AND EmpApp.IsDeleted = 0";
            $result1 = mysqli_query($link, $sql1);
            while($row1 = mysqli_fetch_assoc($result1)) 
            {
                if($row1['GrantApproveID'] > 0)/*แท้จริงแล้วมันต้องมากกว่า 1 แต่มันยังไม่มีข้อมูล Flow จึงใส่ 0 */
                {
                        //ไม่เข้าลำดับขั้น//
                        // $sql2 = "SELECT OCENA.OrgEmailNotApproveID AS OrgEmailNotApproveID, OCENA.OrgID AS OrgID,  
                        //          OCENA.IsUserSendMail AS IsUserSendMail,  OCENA.IsAlertSendMailWait AS IsAlertSendMailWait,  
                        //          OCENA.IsAlertSendMailApprove  AS IsAlertSendMailApprove, OCENA.IsAlertSendMailNotApprove AS IsAlertSendMailNotApprove,  
                        //          OCENA.IsAlertSendMailAgentApprove AS IsAlertSendMailAgentApprove, OCENA.AddessCC   AS AddessCC,  
                        //          OCENA.IsDeleted AS IsDeleted  FROM emOrgConfigEmailNotApprove  AS OCENA                   
                        //          WHERE OCENA.OrgID = '".$row['OrgID']."' AND OCENA.IsDeleted=0";
                        // $result2 = mysqli_query($link, $sql2);
                        // while($row2 = mysqli_fetch_assoc($result2)) 
                        // {
                            // if($row2['IsAlertSendMailAgentApprove'] == 1)
                            // {
                    $sql3 = "SELECT GrantApp_2.ApproverID AS ApproverID,                              
                    GrantApp.ApproverID AS DeputizeEmpID
                    FROM emGrantApprove AS GrantApp_2    
                    LEFT JOIN emEmpApproved AS EmpApp_2 ON GrantApp_2.GrantApproveID = EmpApp_2.GrantApproveID    
                    LEFT JOIN emDeputizeApprove AS Deputi ON GrantApp_2.GrantApproveID = Deputi.GrantApproveID AND Deputi.IsDeleted =0 AND Deputi.MenuID = '".$MenuID."'
                    AND CONVERT(Deputi.StartDate,DATE) <= CONVERT(curdate(),DATE)            
                    AND CONVERT(Deputi.EndDate,DATE) >= CONVERT(curdate(),DATE)        
                    LEFT JOIN emGrantApprove AS GrantApp ON Deputi.DeputizeGrantApproveID = GrantApp.GrantApproveID AND GrantApp.IsDeleted =0 AND GrantApp.IsInactive =0                          
                    WHERE EmpApp_2.EmpID = '".$row['EmpID']."'
                    AND GrantApp_2.MenuID = '".$MenuID."'                     
                    AND EmpApp_2.IsDeleted = 0    
                    AND GrantApp_2.IsDeleted =0      
                    AND GrantApp_2.IsInactive =0 ORDER BY ApproverID ASC LIMIT 1";
                    $result3 = mysqli_query($link, $sql3);
                    while($row3 = mysqli_fetch_assoc($result3)) 
                    {
                                    //ถ้าผู้อนุมัติแทนมีค่าเท่ากับ NULL ให่ส่้งข้อความหาผู้อนุมติหลักได้เลย//
                        if($row3['DeputizeEmpID'] == Null)
                        {
                            $sql4 = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                            AND  emGrantApprove.ApproverID ='".$row3['ApproverID']."'";
                            $result4 = mysqli_query($link, $sql4);
                            while($row4 = mysqli_fetch_assoc($result4)) 
                            {
                                return "A".$row4['UserID'];
                            }
                        }
                        else
                        {
                                        //กรณีมีการตั้งผู้อนุมติแทน//
                            $sql4 = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                            AND  emGrantApprove.ApproverID ='".$row3['DeputizeEmpID']."'";
                            $result4 = mysqli_query($link, $sql4);
                            while($row4 = mysqli_fetch_assoc($result4)) 
                            {
                                return "B".$row4['UserID'];
                            }
                        }
                    }
                            // }
                            // else
                            // {
                            //     return "N";
                            // }
                        // }
                }
                else
                {
                        //เข้าลำดับขั้นค้างไว้ก่อน
                }   
            }
        } 
    }
    $link->close();
}

//*หาผู้อนุมัติหลัก*//
function Grant($userID)
{
    $link = ConnectDatabase();
    $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
    $sql = "SELECT emEmployee.EmpID,hrTimeEmpWorkSche.OrgID 
    FROM emLineuserconnect , emEmployee , hrTimeEmpWorkSche 
    WHERE emLineuserconnect.UserID = '".$userID."'
    AND emEmployee.EmpID = emLineuserconnect.EmpID
    AND emLineuserconnect.EmpID = hrTimeEmpWorkSche.EmpID";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        while($row = mysqli_fetch_assoc($result)) 
        {
            $sql1 = "SELECT  count(GrantApp.GrantApproveID) AS GrantApproveID  FROM emGrantApprove AS GrantApp  
            INNER JOIN emEmpApproved AS EmpApp ON GrantApp.GrantApproveID = EmpApp.GrantApproveID  
            WHERE GrantApp.MenuID = '".$MenuID."' AND EmpApp.EmpID = '".$row['EmpID']."'
            AND GrantApp.IsDeleted = 0 AND GrantApp.IsInactive = 0 AND EmpApp.IsDeleted = 0";
            $result1 = mysqli_query($link, $sql1);
            while($row1 = mysqli_fetch_assoc($result1)) 
            {
                if($row1['GrantApproveID'] > 0)/*แท้จริงแล้วมันต้องมากกว่า 1 แต่มันยังไม่มีข้อมูล Flow จึงใส่ 0 */
                {

                    $sql3 = "SELECT GrantApp_2.ApproverID AS ApproverID,                              
                    GrantApp.ApproverID AS DeputizeEmpID
                    FROM emGrantApprove AS GrantApp_2    
                    LEFT JOIN emEmpApproved AS EmpApp_2 ON GrantApp_2.GrantApproveID = EmpApp_2.GrantApproveID    
                    LEFT JOIN emDeputizeApprove AS Deputi ON GrantApp_2.GrantApproveID = Deputi.GrantApproveID AND Deputi.IsDeleted =0 AND Deputi.MenuID = '".$MenuID."'
                    AND CONVERT(Deputi.StartDate,DATE) <= CONVERT(curdate(),DATE)            
                    AND CONVERT(Deputi.EndDate,DATE) >= CONVERT(curdate(),DATE)        
                    LEFT JOIN emGrantApprove AS GrantApp ON Deputi.DeputizeGrantApproveID = GrantApp.GrantApproveID AND GrantApp.IsDeleted =0 AND GrantApp.IsInactive =0                          
                    WHERE EmpApp_2.EmpID = '".$row['EmpID']."'
                    AND GrantApp_2.MenuID = '".$MenuID."'                     
                    AND EmpApp_2.IsDeleted = 0    
                    AND GrantApp_2.IsDeleted =0      
                    AND GrantApp_2.IsInactive =0 ORDER BY ApproverID ASC LIMIT 1";
                    $result3 = mysqli_query($link, $sql3);
                    while($row3 = mysqli_fetch_assoc($result3)) 
                    {
                        $sql4 = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                        AND  emGrantApprove.ApproverID ='".$row3['ApproverID']."'";
                        $result4 = mysqli_query($link, $sql4);
                        while($row4 = mysqli_fetch_assoc($result4)) 
                        {
                            return $row4['UserID'];
                        }
                    }
                }
            }
        } 
    }
    $link->close();
}

//*หาชื่อผู้อนุมัติแทน*//
function Deputize($userID)
{
    $link = ConnectDatabase();
    $sql4 = "SELECT CONCAT(Title,' ',FirstName, ' ', LastName) AS FullName 
    FROM emPerson,emLineuserconnect ,emEmployee
    WHERE emLineuserconnect.UserID = '".$userID."'
    AND emLineuserconnect.EmpID = emEmployee.EmpID
    AND emEmployee.PersonID = emPerson.PersonID";
    $result4 = mysqli_query($link, $sql4);
    while($row4 = mysqli_fetch_assoc($result4)) 
    {
        return $row4['FullName'];
    }

    $link->close();
}



function DocumentApp($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT hrTimeLeaveRecord.Docuno FROM hrTimeLeaveRecord,emLineuserconnect WHERE hrTimeLeaveRecord.EmpID = emLineuserconnect.EmpID AND
    hrTimeLeaveRecord.DocuDate = curdate() AND emLineuserconnect.UserID = '".$userID."'  ORDER BY hrTimeLeaveRecord.LeaveID DESC LIMIT 1";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        return $row['Docuno'];
    }
}
$link->close();
}

/*ตรวจสอบการเข้าถึง Approved ในริชคอนเทน*/
function CheckMNG($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
    AND  emLineuserconnect.UserID = '".$userID."'";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        return 1;
    }
    else
    {
        return 0;
    }
    $link->close();
}

/*หัวหน้าอนุมัติเอกสารผ่านไลน์*/
function MNGApprovedDocument($appstatus,$userID)
{
    $link = ConnectDatabase();
    $A = substr($appstatus, 0, 1);
    $B = substr($appstatus, 1, 11);
    if($A == 'Y')
    {
        $sql = "UPDATE hrTimeLeaveRecord SET ApproveStatus = 'Y' WHERE Docuno = '".$B."'";
        $result1 = mysqli_query($link, $sql);
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
        $result2 = mysqli_query($link, $sql1);
        return "อนุมัติเอกสารเรียบร้อยแล้ว";
    }
    elseif($A == 'N')
    {
        $sql = "UPDATE hrTimeLeaveRecord SET ApproveStatus = 'N' WHERE Docuno = '".$B."'";
        $result1 = mysqli_query($link, $sql);
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
        $result2 = mysqli_query($link, $sql1);
        return "ไม่อนุมัติเอกสารเรียบร้อยแล้ว";
    }
    else
    {
        return "คุณทำรายการไม่ถูกต้อง";
    }   

    $link->close();
}

function CheckEmp($Docuno)
{
  $link = ConnectDatabase();
  $B = substr($Docuno, 1, 11);
  $sql = "SELECT emLineuserconnect.UserID
  FROM emLineuserconnect,hrTimeLeaveRecord
  WHERE hrTimeLeaveRecord.EmpID = emLineuserconnect.EmpID
  AND hrTimeLeaveRecord.Docuno = '".$B."'";
  $result = mysqli_query($link, $sql);
  if (mysqli_num_rows($result) > 0)
  {
      while ($row = mysqli_fetch_assoc($result))
      {
          return $row['UserID'];
      }
  }
  $link->close();
}

function CheckMNGDeputi($Docuno,$UserID)
{
  $link = ConnectDatabase();
  $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
  $B = substr($Docuno, 1, 11);
  $sqlq = "SELECT emLineuserconnect.UserID
  FROM emLineuserconnect,hrTimeLeaveRecord
  WHERE hrTimeLeaveRecord.EmpID = emLineuserconnect.EmpID
  AND hrTimeLeaveRecord.Docuno = '".$B."'";
  $resultq = mysqli_query($link, $sqlq);
  if (mysqli_num_rows($resultq) > 0)
  {
      while ($rowq = mysqli_fetch_assoc($resultq))
      {        
        $sql = "SELECT emEmployee.EmpID,hrTimeEmpWorkSche.OrgID 
        FROM emLineuserconnect , emEmployee , hrTimeEmpWorkSche 
        WHERE emLineuserconnect.UserID = '".$rowq['UserID']."'
        AND emEmployee.EmpID = emLineuserconnect.EmpID
        AND emLineuserconnect.EmpID = hrTimeEmpWorkSche.EmpID";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0) 
        {
            while($row = mysqli_fetch_assoc($result)) 
            {
                $sql1 = "SELECT  count(GrantApp.GrantApproveID) AS GrantApproveID  FROM emGrantApprove AS GrantApp  
                INNER JOIN emEmpApproved AS EmpApp ON GrantApp.GrantApproveID = EmpApp.GrantApproveID  
                WHERE GrantApp.MenuID = '".$MenuID."' AND EmpApp.EmpID = '".$row['EmpID']."'
                AND GrantApp.IsDeleted = 0 AND GrantApp.IsInactive = 0 AND EmpApp.IsDeleted = 0";
                $result1 = mysqli_query($link, $sql1);
                while($row1 = mysqli_fetch_assoc($result1)) 
                {
                    if($row1['GrantApproveID'] > 0)/*แท้จริงแล้วมันต้องมากกว่า 1 แต่มันยังไม่มีข้อมูล Flow จึงใส่ 0 */
                    {

                        $sql3 = "SELECT GrantApp_2.ApproverID AS ApproverID,                              
                        GrantApp.ApproverID AS DeputizeEmpID
                        FROM emGrantApprove AS GrantApp_2    
                        LEFT JOIN emEmpApproved AS EmpApp_2 ON GrantApp_2.GrantApproveID = EmpApp_2.GrantApproveID    
                        LEFT JOIN emDeputizeApprove AS Deputi ON GrantApp_2.GrantApproveID = Deputi.GrantApproveID AND Deputi.IsDeleted =0 AND Deputi.MenuID = '".$MenuID."'
                        AND CONVERT(Deputi.StartDate,DATE) <= CONVERT(curdate(),DATE)            
                        AND CONVERT(Deputi.EndDate,DATE) >= CONVERT(curdate(),DATE)        
                        LEFT JOIN emGrantApprove AS GrantApp ON Deputi.DeputizeGrantApproveID = GrantApp.GrantApproveID AND GrantApp.IsDeleted =0 AND GrantApp.IsInactive =0                          
                        WHERE EmpApp_2.EmpID = '".$row['EmpID']."'
                        AND GrantApp_2.MenuID = '".$MenuID."'                     
                        AND EmpApp_2.IsDeleted = 0    
                        AND GrantApp_2.IsDeleted =0      
                        AND GrantApp_2.IsInactive =0 ORDER BY ApproverID ASC LIMIT 1";
                        $result3 = mysqli_query($link, $sql3);
                        while($row3 = mysqli_fetch_assoc($result3)) 
                        {
                            $sql4 = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                            AND  emGrantApprove.ApproverID ='".$row3['ApproverID']."'";
                            $result4 = mysqli_query($link, $sql4);
                            while($row4 = mysqli_fetch_assoc($result4)) 
                            {
                                if($row4['UserID'] == $UserID)
                                {
                                    return "0";
                                }
                                else
                                {
                                    return $row4['UserID'];
                                }
                            }
                        }
                    }
                }
            } 
        }
    }
}
$link->close();
}




function DocumentEmp($Docuno)
{
    $link = ConnectDatabase();
    $B = substr($Docuno, 1, 11);
    $sql = "SELECT Docuno FROM hrTimeLeaveRecord WHERE Docuno = '".$B."'";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        return $row['Docuno'];
    }
}
$link->close();
}
//////////////////////////////////////////////////////////////สิ้นสุดการโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////สิ้นสุดการโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////สิ้นสุดการโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////สิ้นสุดการโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////สิ้นสุดการโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////สิ้นสุดการโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////สิ้นสุดการโต้ตอบด้วยภาษาไทย//////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////โต้ตอบด้วยภาษาอังกฤษ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////โต้ตอบด้วยภาษาอังกฤษ//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////โต้ตอบด้วยภาษาอังกฤษ//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////โต้ตอบด้วยภาษาอังกฤษ//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////โต้ตอบด้วยภาษาอังกฤษ//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////โต้ตอบด้วยภาษาอังกฤษ//////////////////////////////////////////////////////////////////////
//ใช้ยกเลิกการทำรายการขออนุมัติ//
function CancelsENG($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT * FROM hrTimeLeaveRecordConfirm WHERE  UserID = '".$userID."' ORDER BY LeaveID DESC LIMIT 1";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sql1 = "DELETE FROM `hrTimeLeaveRecordConfirm` WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."'";
            $link->query($sql1);
            return "Cancel transaction";
        }
    }
    $link->close();
}

function AddAppENG($typeapp,$userID)
{
    $link = ConnectDatabase();
    $year = date("Y");
    $sql = "SELECT * FROM hrLeaveType WHERE LeaveTypeCode = '".$typeapp."'";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0)
    {
        while ($rowq = mysqli_fetch_assoc($result))
        {
            //ดึง EmpID ออกมา//
            $quss = "SELECT * FROM emLineuserconnect WHERE emLineuserconnect.UserID = '".$userID."'";
            $resulttss = mysqli_query($link, $quss);
            while ($row_quss = mysqli_fetch_assoc($resulttss)) 
            {
                //Check ว่าวันนี้มีการขอลาไปหรือยัง//
                $qu = "SELECT * FROM hrTimeLeaveRecord,emLineuserconnect WHERE hrTimeLeaveRecord.DocuDate = CURDATE()
                AND hrTimeLeaveRecord.EmpID = '".$row_quss['EmpID']."' AND emLineuserconnect.UserID = '".$userID."'";
                $resultt = mysqli_query($link, $qu);
                if (mysqli_num_rows($resultt) > 0)
                {
                    while ($row = mysqli_fetch_assoc($resultt))
                    {
                        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                        $link->query($sql1);
                        return "Can't leave Because of the request to leave the document ".$row['Docuno'];
                    }
                }
                else
                {

                    $sql = "select hrEmpWorkProfile.OrgID,emLineuserconnect.EmpID
                    from emEmployee,hrEmpWorkProfile,emLineuserconnect
                    where emEmployee.EmpID = hrEmpWorkProfile.EmpID
                    AND emLineuserconnect.UserID = '".$userID."'
                    AND emLineuserconnect.EmpID = emEmployee.EmpID";
                    $result = mysqli_query($link, $sql);
                    while ($row = mysqli_fetch_assoc($result))
                    {
                                //Check ว่าจำนวนวันลาของประเภทการลายังลาได้อยู่ไหม//
                        $sqli = "select Days from hrTimeLeaveRema where RecordYear = '".$year."'
                        AND EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$rowq['LeaveTypeID']."'";
                        $results = mysqli_query($link, $sqli);
                                //Check ว่ามีข้อมูลอยู่ใน hrTimeLeaveRema หรือยัง//
                        if (mysqli_num_rows($results) > 0)
                        {
                            while ($rows = mysqli_fetch_assoc($results))
                            {
                                if($rows['Days'] > 0)
                                {
                                    $sql2 = "INSERT INTO hrTimeLeaveRecordConfirm (EmpID, OrgID ,UserID,LeaveTypeID) VALUES ('".$row['EmpID']."','".$row['OrgID']."','".$userID."','".$rowq['LeaveTypeID']."')";
                                    $link->query($sql2);
                                    $sql3 = "UPDATE emLineuserconnect SET ThisMenu = '2' WHERE UserID = '".$userID."' ";
                                    $link->query($sql3);
                                    return "Please fill in the donation cause";
                                }
                                else
                                {
                                    $sql = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                                    $link->query($sql);
                                    return "The number of leave days Your out";
                                }
                            }    
                        }
                        else
                        {
                                    //กรณียังไม่มีข้อมูลจึงไปดึงข้อมูลมาจาก hrAssignLeaveNum ซึ่งเป็นข้อมูลตั้งต้น//
                            $sqli = "SELECT * FROM  hrAssignLeaveNum WHERE EmpID = '".$row['EmpID']."'";
                            $resultss = mysqli_query($link, $sqli);
                                    //Check ว่ามีข้อมูลในตารางตั้งต้นหรือไม่//
                            if (mysqli_num_rows($resultss) > 0)
                            {
                                while ($rows = mysqli_fetch_assoc($resultss))
                                {
                                    $sql2 = "INSERT INTO `hrTimeLeaveRema`(`RecordID`, `RecordYear`, `EmpID`, `LeaveTypeID`, `Days`, `Hours`, `OrgID`, `DaysNow`, `HoursNow`)
                                    VALUES (uuid(),'".$year."','".$row['EmpID']."','".$rowq['LeaveTypeID']."','".$rows['AssDayNum']."','0','".$row['OrgID']."','0','0')";
                                    $results = mysqli_query($link, $sql2);
                                    if ($results === true) 
                                    {
                                        $sql2 = "INSERT INTO hrTimeLeaveRecordConfirm (EmpID, OrgID ,UserID,LeaveTypeID) VALUES ('".$row['EmpID']."','".$row['OrgID']."','".$userID."','".$rowq['LeaveTypeID']."')";
                                        $link->query($sql2);
                                        $sql3 = "UPDATE emLineuserconnect SET ThisMenu = '2' WHERE UserID = '".$userID."' ";
                                        $link->query($sql3);
                                        return "Please fill in the donation cause";
                                    }

                                }
                            }
                            else
                            {
                                $sqli = "SELECT * FROM hrLeaveType WHERE IsDeleted = 0 AND OrgID = '".$row['OrgID']."' AND LeaveTypeCode = '".$typeapp."'";
                                $resultss = mysqli_query($link, $sqli);
                                        //Check ว่ามีข้อมูลในตารางตั้งต้นหรือไม่//
                                if (mysqli_num_rows($resultss) > 0)
                                {
                                    while ($rows = mysqli_fetch_assoc($resultss))
                                    {
                                        $sql2 = "INSERT INTO `hrTimeLeaveRema`(`RecordID`, `RecordYear`, `EmpID`, `LeaveTypeID`, `Days`, `Hours`, `OrgID`, `DaysNow`, `HoursNow`)
                                        VALUES (uuid(),'".$year."','".$row['EmpID']."','".$rowq['LeaveTypeID']."','".$rows['LeaveTypeDayNum']."','0','".$row['OrgID']."','0','0')";
                                        $results = mysqli_query($link, $sql2);
                                        if ($results === true) 
                                        {
                                            $sql2 = "INSERT INTO hrTimeLeaveRecordConfirm (EmpID, OrgID ,UserID,LeaveTypeID) VALUES ('".$row['EmpID']."','".$row['OrgID']."','".$userID."','".$rowq['LeaveTypeID']."')";
                                            $link->query($sql2);
                                            $sql3 = "UPDATE emLineuserconnect SET ThisMenu = '2' WHERE UserID = '".$userID."' ";
                                            $link->query($sql3);
                                            return "Please fill in the donation cause";
                                        }

                                    }
                                }
                            }
                        }
                    }
                    

                }
            }
        }
    }
    else
    {
        return "You made a mistake.\nPlease select Type of leave.";
    }
    $link->close();
}


//สาเหตุการขอลา ENG//
function AddDetailENG($detail, $userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT  LeaveID FROM hrTimeLeaveRecordConfirm  WHERE UserID = '".$userID."' ORDER BY LeaveID DESC LIMIT 1";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        while ($row = mysqli_fetch_assoc($result)) 
        {
            $sql = "UPDATE hrTimeLeaveRecordConfirm SET LeaveRemark = '".$detail."'  WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."' ";
            $link->query($sql);
            $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '3' WHERE UserID = '".$userID."' ";
            $link->query($sql1);
            return "Please enter the last 4 digits of the card. Confirmation can be canceled. \n Please cancel the print job";
        }
    }

    $link->close();
}

//ยืนยันตนตนสำหรับขออนุมัติลา//
function confirmsappENG($idcard,$userID)
{
    $link = ConnectDatabase();
    $A = substr($idcard, 0, 1);
    $B = substr($idcard, 1, 2);
    $C = substr($idcard, 3, 3); 
    $mixidcard = $A."-".$B."-".$C;
    //Selct หารหัสเลข 4 ตัวท้ายของบัตรประชาชนเพื่อนยืนยันตัวตน (ขออนุมัติลา)//
    $sql = "SELECT emPerson.IdentityCard
    FROM emLineuserconnect,emPerson ,emEmployee
    WHERE SUBSTRING( emPerson.IdentityCard ,12 , 6  ) = '".$mixidcard."'
    AND emLineuserconnect.EmpID = emEmployee.EmpID
    AND emPerson.PersonID = emEmployee.PersonID";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        //Select เอกสารล่าสุดที่รอการ Confirm//
        $sql = "SELECT * FROM hrTimeLeaveRecordConfirm WHERE  UserID = '".$userID."' ORDER BY LeaveID DESC LIMIT 1";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                     //ตารางการทำงาน ของพนักงานใน UserID นั้นๆ//
               $sql3 = "SELECT ET.WorkDate  AS LeaveDate, ET.ShiftID As ShiftID  ,S.TimeIn1 AS StartTime,(CASE S.TimeCount  WHEN 2 THEN S.TimeOUT1          
               WHEN 4 THEN S.TimeOUT2 WHEN 6 THEN S.TimeOUT3 END) AS EndTime, S.WorkTime AS HourLeave                   
               FROM hrTimeEmpWorkScheDT AS ET LEFT JOIN hrTimeShift AS S ON S.ShiftID = ET.ShiftID  LEFT JOIN hrTimeEmpWorkSche AS EW ON EW.EmpWorkScheID = ET.EmpWorkScheID                
               WHERE EW.EmpID = '".$row['EmpID']."' AND S.IsShiftOT = 0 
               AND CONVERT(ET.WorkDate,DATE) = CONVERT(curdate(),DATE)  
               And ET.OrgID = '".$row['OrgID']."'";
               $results2 = mysqli_query($link, $sql3);
               if (mysqli_num_rows($results2) > 0) 
               {

                $LE = "LEV";
                $year = date("Y");
                $Month = date("m");
                $maxId = $row['LeaveID'] ;
                $nextId = $LE.$year.$Month.$maxId;
                        //สร้างเอกสารขออนุมัติลาใน hrTimeLeaveRecord โดยอ้างอิงข้อมูลจาก hrTimeLeaveRecordConfirm//
                $sql1 = "INSERT INTO `hrTimeLeaveRecord`(`LeaveID`, `Docuno`, `DocuDate`, `EmpID`, `LeaveTypeID`, `LeaveRemark`,`IsUrgent`, `ApproveStatus`, 
                `ApproveLevel`, `IsCancel`, `TestimonialType`, `SaveToHealth`, `TestimonialStartDate`, `TestimonialEndDate`, `Date1`, `CreatedBy`, 
                `CreatedDate`, `ModifiedBy`, `ModifiedDate`, `IsDeleted`, `OrgID`) VALUES (uuid(),'".$nextId."',curdate(),'".$row['EmpID']."','".$row['LeaveTypeID']."',
                '".$row['LeaveRemark']."',0,'W','N',0,'0',0,curdate(),curdate(),curdate(),'3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',curdate(),'3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',
                curdate(),0,'".$row['OrgID']."')";

                $results = mysqli_query($link, $sql1);
                if ($results === true) 
                {
                            //Select เอกสารที่สร้างจากด้านบนเพื่อน สร้างเอกสาร Detail//
                    $sql2 = "SELECT * FROM  hrTimeLeaveRecord WHERE EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."' AND DocuDate = curdate() ORDER BY LeaveID DESC LIMIT 1";
                    $results1 = mysqli_query($link, $sql2);
                    if (mysqli_num_rows($result) > 0) 
                    {
                        while ($rows = mysqli_fetch_assoc($results1)) 
                        {
                            while ($rows1 = mysqli_fetch_assoc($results2)) 
                            {

                                            //เพิ่มข้อมูลลงในตาราง Deteil//
                                $sql4 = "INSERT INTO `hrTimeLeaveRecordDetail`(`LeaveDetailID`, `ListNo`, `LeaveDate`, `ShiftID`, `StartTime`, `EndTime`, `HourLeave`, `LeaveID`, 
                                `CreatedBy`, `CreatedDate`, `ModifiedBy`, `ModifiedDate`, `IsDeleted`, `ShiftDate`, `StartDate`, `EndDate`) 
                                VALUES (uuid(),'1',curdate(),'".$rows1['ShiftID']."','".$rows1['StartTime']."','".$rows1['EndTime']."','".$rows1['HourLeave']."','".$rows['LeaveID']."' ,'3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',curdate(),
                                '3f3bf3ad-b4c9-4d44-a56f-ab55c4e4fb05',curdate(),0,curdate(),curdate(),curdate())";
                                $link->query($sql4);
                                $year = date("Y");
                                $sql5 = "Select Days from hrTimeLeaveRema where RecordYear = '".$year."' AND EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."'";
                                $results3 = mysqli_query($link, $sql5);
                                if (mysqli_num_rows($results3) > 0) 
                                {
                                    while ($rows2 = mysqli_fetch_assoc($results3)) 
                                    {
                                                    //ลดจำนวนวันลาลงตามข้อมูลใน DB//
                                        $maxdays = $rows2['Days'] - 1;
                                        $sql1 = "UPDATE hrTimeLeaveRema SET Days = '".$maxdays."' WHERE EmpID = '".$row['EmpID']."' AND LeaveTypeID = '".$row['LeaveTypeID']."' AND RecordYear = '".$year."'";
                                        $link->query($sql1);
                                                    //ลบเอกสารที่รอ confirm ในตาราง hrTimeLeaveRecordConfirm//
                                        $sql6 = "DELETE FROM `hrTimeLeaveRecordConfirm` WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."'";
                                        $link->query($sql6);
                                                    //อัพเดทสถานะการทำงานของระบบ//
                                        $sql7 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                                        $link->query($sql7);
                                        return "The system has already been approved";
                                    }
                                }
                            }

                        }
                    }  
                }

            }
            else
            {
                $sql8 = "DELETE FROM `hrTimeLeaveRecordConfirm` WHERE UserID = '".$userID."' AND LeaveID = '".$row['LeaveID']."'";
                $link->query($sql8);
                $sql9 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
                $link->query($sql9);
                return "Can't make a leave. \nBecause you have not scheduled a job"; 
            }
        }
    } 

}
else 
{
    return "The last 4 digits are not valid. Please reprint. \nPlease cancel the transaction by canceling";
}
return "Something is wrong";
$link->close();
}


//*เช็คลำดับขั้น หาผู้อนุมัติหลักและผู้อนุมัติแทน*//
function CheckLVENG($userID)
{
    $link = ConnectDatabase();
    $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
    $sql = "SELECT emEmployee.EmpID,hrTimeEmpWorkSche.OrgID 
    FROM emLineuserconnect , emEmployee , hrTimeEmpWorkSche 
    WHERE emLineuserconnect.UserID = '".$userID."'
    AND emEmployee.EmpID = emLineuserconnect.EmpID
    AND emLineuserconnect.EmpID = hrTimeEmpWorkSche.EmpID";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        while($row = mysqli_fetch_assoc($result)) 
        {
            $sql1 = "SELECT  count(GrantApp.GrantApproveID) AS GrantApproveID  FROM emGrantApprove AS GrantApp  
            INNER JOIN emEmpApproved AS EmpApp ON GrantApp.GrantApproveID = EmpApp.GrantApproveID  
            WHERE GrantApp.MenuID = '".$MenuID."' AND EmpApp.EmpID = '".$row['EmpID']."'
            AND GrantApp.IsDeleted = 0 AND GrantApp.IsInactive = 0 AND EmpApp.IsDeleted = 0";
            $result1 = mysqli_query($link, $sql1);
            while($row1 = mysqli_fetch_assoc($result1)) 
            {
                if($row1['GrantApproveID'] > 0)/*แท้จริงแล้วมันต้องมากกว่า 1 แต่มันยังไม่มีข้อมูล Flow จึงใส่ 0 */
                {
                        //ไม่เข้าลำดับขั้น//
                        // $sql2 = "SELECT OCENA.OrgEmailNotApproveID AS OrgEmailNotApproveID, OCENA.OrgID AS OrgID,  
                        //          OCENA.IsUserSendMail AS IsUserSendMail,  OCENA.IsAlertSendMailWait AS IsAlertSendMailWait,  
                        //          OCENA.IsAlertSendMailApprove  AS IsAlertSendMailApprove, OCENA.IsAlertSendMailNotApprove AS IsAlertSendMailNotApprove,  
                        //          OCENA.IsAlertSendMailAgentApprove AS IsAlertSendMailAgentApprove, OCENA.AddessCC   AS AddessCC,  
                        //          OCENA.IsDeleted AS IsDeleted  FROM emOrgConfigEmailNotApprove  AS OCENA                   
                        //          WHERE OCENA.OrgID = '".$row['OrgID']."' AND OCENA.IsDeleted=0";
                        // $result2 = mysqli_query($link, $sql2);
                        // while($row2 = mysqli_fetch_assoc($result2)) 
                        // {
                            // if($row2['IsAlertSendMailAgentApprove'] == 1)
                            // {
                    $sql3 = "SELECT GrantApp_2.ApproverID AS ApproverID,                              
                    GrantApp.ApproverID AS DeputizeEmpID
                    FROM emGrantApprove AS GrantApp_2    
                    LEFT JOIN emEmpApproved AS EmpApp_2 ON GrantApp_2.GrantApproveID = EmpApp_2.GrantApproveID    
                    LEFT JOIN emDeputizeApprove AS Deputi ON GrantApp_2.GrantApproveID = Deputi.GrantApproveID AND Deputi.IsDeleted =0 AND Deputi.MenuID = '".$MenuID."'
                    AND CONVERT(Deputi.StartDate,DATE) <= CONVERT(curdate(),DATE)            
                    AND CONVERT(Deputi.EndDate,DATE) >= CONVERT(curdate(),DATE)        
                    LEFT JOIN emGrantApprove AS GrantApp ON Deputi.DeputizeGrantApproveID = GrantApp.GrantApproveID AND GrantApp.IsDeleted =0 AND GrantApp.IsInactive =0                          
                    WHERE EmpApp_2.EmpID = '".$row['EmpID']."'
                    AND GrantApp_2.MenuID = '".$MenuID."'                     
                    AND EmpApp_2.IsDeleted = 0    
                    AND GrantApp_2.IsDeleted =0      
                    AND GrantApp_2.IsInactive =0 ORDER BY ApproverID ASC LIMIT 1";
                    $result3 = mysqli_query($link, $sql3);
                    while($row3 = mysqli_fetch_assoc($result3)) 
                    {
                                    //ถ้าผู้อนุมัติแทนมีค่าเท่ากับ NULL ให่ส่้งข้อความหาผู้อนุมติหลักได้เลย//
                        if($row3['DeputizeEmpID'] == Null)
                        {
                            $sql4 = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                            AND  emGrantApprove.ApproverID ='".$row3['ApproverID']."'";
                            $result4 = mysqli_query($link, $sql4);
                            while($row4 = mysqli_fetch_assoc($result4)) 
                            {
                                return "A".$row4['UserID'];
                            }
                        }
                        else
                        {
                                        //กรณีมีการตั้งผู้อนุมติแทน//
                            $sql4 = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                            AND  emGrantApprove.ApproverID ='".$row3['DeputizeEmpID']."'";
                            $result4 = mysqli_query($link, $sql4);
                            while($row4 = mysqli_fetch_assoc($result4)) 
                            {
                                return "B".$row4['UserID'];
                            }
                        }
                    }
                            // }
                            // else
                            // {
                            //     return "N";
                            // }
                        // }
                }
                else
                {
                        //เข้าลำดับขั้นค้างไว้ก่อน
                }   
            }
        } 
    }
    $link->close();
}

//*หาผู้อนุมัติหลัก*//
function GrantENG($userID)
{
    $link = ConnectDatabase();
    $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
    $sql = "SELECT emEmployee.EmpID,hrTimeEmpWorkSche.OrgID 
    FROM emLineuserconnect , emEmployee , hrTimeEmpWorkSche 
    WHERE emLineuserconnect.UserID = '".$userID."'
    AND emEmployee.EmpID = emLineuserconnect.EmpID
    AND emLineuserconnect.EmpID = hrTimeEmpWorkSche.EmpID";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        while($row = mysqli_fetch_assoc($result)) 
        {
            $sql1 = "SELECT  count(GrantApp.GrantApproveID) AS GrantApproveID  FROM emGrantApprove AS GrantApp  
            INNER JOIN emEmpApproved AS EmpApp ON GrantApp.GrantApproveID = EmpApp.GrantApproveID  
            WHERE GrantApp.MenuID = '".$MenuID."' AND EmpApp.EmpID = '".$row['EmpID']."'
            AND GrantApp.IsDeleted = 0 AND GrantApp.IsInactive = 0 AND EmpApp.IsDeleted = 0";
            $result1 = mysqli_query($link, $sql1);
            while($row1 = mysqli_fetch_assoc($result1)) 
            {
                if($row1['GrantApproveID'] > 0)/*แท้จริงแล้วมันต้องมากกว่า 1 แต่มันยังไม่มีข้อมูล Flow จึงใส่ 0 */
                {

                    $sql3 = "SELECT GrantApp_2.ApproverID AS ApproverID,                              
                    GrantApp.ApproverID AS DeputizeEmpID
                    FROM emGrantApprove AS GrantApp_2    
                    LEFT JOIN emEmpApproved AS EmpApp_2 ON GrantApp_2.GrantApproveID = EmpApp_2.GrantApproveID    
                    LEFT JOIN emDeputizeApprove AS Deputi ON GrantApp_2.GrantApproveID = Deputi.GrantApproveID AND Deputi.IsDeleted =0 AND Deputi.MenuID = '".$MenuID."'
                    AND CONVERT(Deputi.StartDate,DATE) <= CONVERT(curdate(),DATE)            
                    AND CONVERT(Deputi.EndDate,DATE) >= CONVERT(curdate(),DATE)        
                    LEFT JOIN emGrantApprove AS GrantApp ON Deputi.DeputizeGrantApproveID = GrantApp.GrantApproveID AND GrantApp.IsDeleted =0 AND GrantApp.IsInactive =0                          
                    WHERE EmpApp_2.EmpID = '".$row['EmpID']."'
                    AND GrantApp_2.MenuID = '".$MenuID."'                     
                    AND EmpApp_2.IsDeleted = 0    
                    AND GrantApp_2.IsDeleted =0      
                    AND GrantApp_2.IsInactive =0 ORDER BY ApproverID ASC LIMIT 1";
                    $result3 = mysqli_query($link, $sql3);
                    while($row3 = mysqli_fetch_assoc($result3)) 
                    {
                        $sql4 = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                        AND  emGrantApprove.ApproverID ='".$row3['ApproverID']."'";
                        $result4 = mysqli_query($link, $sql4);
                        while($row4 = mysqli_fetch_assoc($result4)) 
                        {
                            return $row4['UserID'];
                        }
                    }
                }
            }
        } 
    }
    $link->close();
}

//*หาชื่อผู้อนุมัติแทน*//
function DeputizeENG($userID)
{
    $link = ConnectDatabase();
    $sql4 = "SELECT CONCAT(Title,' ',FirstName, ' ', LastName) AS FullName 
    FROM emPerson,emLineuserconnect ,emEmployee
    WHERE emLineuserconnect.UserID = '".$userID."'
    AND emLineuserconnect.EmpID = emEmployee.EmpID
    AND emEmployee.PersonID = emPerson.PersonID";
    $result4 = mysqli_query($link, $sql4);
    while($row4 = mysqli_fetch_assoc($result4)) 
    {
        return $row4['FullName'];
    }

    $link->close();
}



function DocumentAppENG($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT hrTimeLeaveRecord.Docuno FROM hrTimeLeaveRecord,emLineuserconnect WHERE hrTimeLeaveRecord.EmpID = emLineuserconnect.EmpID AND
    hrTimeLeaveRecord.DocuDate = curdate() AND emLineuserconnect.UserID = '".$userID."'  ORDER BY hrTimeLeaveRecord.LeaveID DESC LIMIT 1";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        return $row['Docuno'];
    }
}
$link->close();
}

/*ตรวจสอบการเข้าถึง Approved ในริชคอนเทน*/
function CheckMNGENG($userID)
{
    $link = ConnectDatabase();
    $sql = "SELECT emLineuserconnect.UserID FROM emLineuserconnect,emGrantApprove WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
    AND  emLineuserconnect.UserID = '".$userID."'";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
        return 1;
    }
    else
    {
        return 0;
    }
    $link->close();
}

/*เช็คว่ามีเอกสารขออนุมัติหรือไม่*/
function CheckDocApp($userID)
{

  $link = ConnectDatabase();
  $sqls = "SELECT emGrantApprove.GrantApproveID AS GrantID FROM emGrantApprove,emLineuserconnect WHERE emGrantApprove.ApproverID = emLineuserconnect.EmpID
  AND emLineuserconnect.UserID = '".$userID."' ";
  $results = mysqli_query($link, $sqls);
  if (mysqli_num_rows($results) > 0) 
  {
      while($row = mysqli_fetch_assoc($results)) 
      {
          $sql1 = "SELECT COUNT(Record.Docuno) AS Doc 
          FROM hrTimeLeaveRecord AS Record 
          LEFT JOIN emEmpApproved AS empApp ON empApp.EmpID = Record.EmpID
          LEFT JOIN emGrantApprove AS empGrant ON empGrant.GrantApproveID = empApp.GrantApproveID
          WHERE empApp.GrantApproveID = '".$row['GrantID']."'
          AND Record.ApproveStatus = 'W'";
          $result1 = mysqli_query($link, $sql1);
          while($rows1 = mysqli_fetch_assoc($result1)) 
            {
                if ($rows1['Doc'] > 0) 
                {
                    return 1;
                }
                else
                {
                    return 0;
                }
            }
        }
    }
$link->close();
}


/*หัวหน้าอนุมัติเอกสารผ่านไลน์*/
function MNGApprovedDocumentENG($appstatus,$userID)
{
    $link = ConnectDatabase();
    $A = substr($appstatus, 0, 1);
    $B = substr($appstatus, 1, 11);
    if($A == 'Y')
    {
        $sql = "UPDATE hrTimeLeaveRecord SET ApproveStatus = 'Y' WHERE Docuno = '".$B."'";
        $result1 = mysqli_query($link, $sql);
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
        $result2 = mysqli_query($link, $sql1);
        return "Document approved successfully";
    }
    elseif($A == 'N')
    {
        $sql = "UPDATE hrTimeLeaveRecord SET ApproveStatus = 'N' WHERE Docuno = '".$B."'";
        $result1 = mysqli_query($link, $sql);
        $sql1 = "UPDATE emLineuserconnect SET ThisMenu = '0' WHERE UserID = '".$userID."' ";
        $result2 = mysqli_query($link, $sql1);
        return "Disapproved document";
    }
    else
    {
        return "You made an invalid entry";
    }   

    $link->close();
}

function CheckEmpENG($Docuno)
{
  $link = ConnectDatabase();
  $B = substr($Docuno, 1, 11);
  $sql = "SELECT emLineuserconnect.UserID
  FROM emLineuserconnect,hrTimeLeaveRecord
  WHERE hrTimeLeaveRecord.EmpID = emLineuserconnect.EmpID
  AND hrTimeLeaveRecord.Docuno = '".$B."'";
  $result = mysqli_query($link, $sql);
  if (mysqli_num_rows($result) > 0)
  {
      while ($row = mysqli_fetch_assoc($result))
      {
          return $row['UserID'];
      }
  }
  $link->close();
}

function DocumentEmpENG($Docuno)
{
    $link = ConnectDatabase();
    $B = substr($Docuno, 1, 11);
    $sql = "SELECT Docuno FROM hrTimeLeaveRecord WHERE Docuno = '".$B."'";
    $result = mysqli_query($link, $sql);
    if (mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        return $row['Docuno'];
    }
}
$link->close();
}