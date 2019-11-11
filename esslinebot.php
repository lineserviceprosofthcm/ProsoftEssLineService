<?php

require('line-bot.php');
include('essconnect.php');

$channelSecret = 'b777323834edf3fd96558faf97a3a69a';
$access_token  = '5ZP5bMi9tgEXR9Zwq5+TBJ6C5pv5SMbaIWBes4l1MGHQph3JbHhQyqRWD+e7pxEKsmwy0i5qQWc7gEAhudIS2GuUPEQmNY21zhsL0nrRbkQVCVUl8HoKS7s11KpkPiOXIapgGt7EALkwBLicU19DqgdB04t89/1O/w1cDnyilFU=';

$NewsHDID = $_GET['NewsHDID'];
$News = $_GET['News'];
$LineIDLeaveRecord = $_POST['LineIDLeaveRecord'];
$Detail = $_POST['Detail'];
$LineID_NextApprove = $_POST['LineID_NextApprove'];
$WaitApprove = $_POST['WaitApprove'];
$LineID_EmpID = $_POST['LineID_EmpID'];
$ApproveStatus = $_POST['ApproveStatus'];
$bot = new BOT_API($channelSecret, $access_token);


// แจ้งข่าวสาร
if(!empty($NewsHDID)){
    $arr = SendNewsTo($NewsHDID);
    $iCount = count($arr);
    //for ($i = 0; $i<$iCount; $i++) {
        //$bot->SendMessageTo($arr[$i],$News);
    //}
    //$response = array(
        //status => true,
        //message => $NewsHDID,
        //message2 => $News,
        //message3 => $arr
    //);
    //echo json_encode($response);
    
    $myXMLData = "<?xml version='1.0' encoding='UTF-8'?><note><to>Tove</to><from>Jani</from><heading>Reminder</heading><body>Don't forget me this weekend!</body></note>";
    $xml=simplexml_load_string($myXMLData) or die("Error: Cannot create object");
    print_r($xml);
}

// แจ้งเอกสารลาหาผู้อนุมัติ
if(!empty($LineIDLeaveRecord)){
    $bot->SendMessageApproveTo($LineIDLeaveRecord,$Detail);
}

// แจ้งเอกสารคนอนุมัติถัดไป
if(!empty($LineID_NextApprove)){
    $bot->SendMessageApproveTo($LineID_NextApprove ,$WaitApprove);
}

// แจ้งเอกสารหาผู้ขอลา
if(!empty($LineID_EmpID)){
    $bot->SendMessageToEmpRequest($LineID_EmpID ,$ApproveStatus);
}

if (!empty($bot->isEvents)) {
    $Language = GetLanguage($bot->userId);
    if($Language == "th-TH")
    {
        switch($bot->text){
            case "Approve Center":
                $bot->ApproveCenter();
            break;
            case "Time Attendance":
                $bot->TimeAttendance();
            break;
            case "สิทธิ์การลา/วันลาคงเหลือ":
                $Text = LeaveRemainNum($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Payroll":
                $bot->Payroll();
            break;
            case "ขอสลิปเงินเดือน":
                $Text = EPaySlip($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "ขอเอกสาร 50 ทวิ":
                $Text = Withholdingtaxcertificate($bot->userId);
                if(count($Text) > 1){
                    $bot->SendMessageTo($Text[1],$Text[0]); // ส่งข้อความหาHR
                    $bot->replyMessageNew($bot->replyToken,$Text[2]); // ส่งข้อความหาผู้ขอ
                }else{
                    $bot->replyMessageNew($bot->replyToken,$Text[0]); // ส่งข้อความหาผู้ขอ
                }
            break;
            case "ขอใบรับรองการทำงาน":
                $Text = WorkCert($bot->userId);
                if(count($Text) > 1){
                    $bot->SendMessageTo($Text[1],$Text[0]); // ส่งข้อความหาHR
                    $bot->replyMessageNew($bot->replyToken,$Text[2]); // ส่งข้อความหาผู้ขอ
                }else{
                    $bot->replyMessageNew($bot->replyToken,$Text[0]); // ส่งข้อความหาผู้ขอ
                }
            break;
            case "ขอเอกสารรับรองเงินเดือน":
                $Text = SalaryCert($bot->userId);
                if(count($Text) > 1){
                    $bot->SendMessageTo($Text[1],$Text[0]); // ส่งข้อความหาHR
                    $bot->replyMessageNew($bot->replyToken,$Text[2]); // ส่งข้อความหาผู้ขอ
                }else{
                    $bot->replyMessageNew($bot->replyToken,$Text[0]); // ส่งข้อความหาผู้ขอ
                }
            break;
            case "Organization":
                $bot->Organization();
            break;
            case "วันหยุดองค์กร":
                $Text = calendar($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "ที่ตั้งองค์กร":
                $Text = LocationOrganization($bot->userId);
                if($Text == "ชื่อผู้ใช้ของคุณ ยังไม่ได้ลงทะเบียน" || $Text == "Please register to use system." || $Text == "ไม่พบที่อยู่องค์กร" || $Text == "not find Locationtion of Organization."){
                    $bot->replyMessageNew($bot->replyToken,$Text);
                }else{
                    $bot->LocationOrg($bot->replyToken,$Text);
                }
            break;
            case "Setting":
                $bot->Setting();
            break;
            case "เปลี่ยนภาษา":
                $bot->SendLanguage($bot->replyToken,$bot->userId);
            break;
            case "ภาษาไทย (Thai)":
                $Text = ChangeLanguage($bot->userId,$bot->text);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "ภาษาอังกฤษ (English)":
                $Text = ChangeLanguage($bot->userId,$bot->text);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            default:
                $bot->BOT_New($bot->replyToken,$bot->text);
            break;
        }
    }
    else if($Language == "en-US") //#####################################################################################
    {
        switch($bot->text){
            case "Approve Center":
                $bot->ApproveCenterEng();
            break;
            case "Time Attendance":
                $bot->TimeAttendanceEng();
            break;
            case "Leave Remain":
                $Text = LeaveRemainNumEng($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Payroll":
                $bot->PayrollEng();
            break;
            case "E-Pay Slip":
                $Text = EPaySlip($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "50 Bis Request":
                $Text = Withholdingtaxcertificate($bot->userId);
                if(count($Text) > 1){
                    $bot->SendMessageTo($Text[1],$Text[0]); // ส่งข้อความหาHR
                    $bot->replyMessageNew($bot->replyToken,$Text[2]); // ส่งข้อความหาผู้ขอ
                }else{
                    $bot->replyMessageNew($bot->replyToken,$Text[0]); // ส่งข้อความหาผู้ขอ
                }
            break;
            case "Works Cer. Request":
                $Text = WorkCert($bot->userId);
                if(count($Text) > 1){
                    $bot->SendMessageTo($Text[1],$Text[0]); // ส่งข้อความหาHR
                    $bot->replyMessageNew($bot->replyToken,$Text[2]); // ส่งข้อความหาผู้ขอ
                }else{
                    $bot->replyMessageNew($bot->replyToken,$Text[0]); // ส่งข้อความหาผู้ขอ
                }
            break;
            case "Salary Cer. Request":
                $Text = SalaryCert($bot->userId);
                if(count($Text) > 1){
                    $bot->SendMessageTo($Text[1],$Text[0]); // ส่งข้อความหาHR
                    $bot->replyMessageNew($bot->replyToken,$Text[2]); // ส่งข้อความหาผู้ขอ
                }else{
                    $bot->replyMessageNew($bot->replyToken,$Text[0]); // ส่งข้อความหาผู้ขอ
                }
            break;
            case "Organization":
                $bot->OrganizationEng();
            break;
            case "Organization Calendar":
                $Text = CalendarEng($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Location of Organization":
                $Text = LocationOrganization($bot->userId);
                if($Text == "ชื่อผู้ใช้ของคุณ ยังไม่ได้ลงทะเบียน" || $Text == "Please register to use system." || $Text == "ไม่พบที่อยู่องค์กร" || $Text == "not find Locationtion of Organization."){
                    $bot->replyMessageNew($bot->replyToken,$Text);
                }else{
                    $bot->LocationOrg($bot->replyToken,$Text);
                }
            break;
            case "Setting":
                $bot->SettingEng();
            break;
            case "Language":
                $bot->SendLanguage($bot->replyToken,$bot->userId);
            break;
            case "ภาษาไทย (Thai)":
                $Text = ChangeLanguage($bot->userId,$bot->text);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "ภาษาอังกฤษ (English)":
                $Text = ChangeLanguage($bot->userId,$bot->text);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            default:
                $bot->BOT_New($bot->replyToken,$bot->text);
            break;
        }
    }
    else if($Language == "NoLang")
    {
        if($bot->text == "ภาษาไทย (Thai)" || $bot->text == "ภาษาอังกฤษ (English)"){
            $Text = ChangeLanguage($bot->userId,$bot->text);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }else{
            $bot->SendLanguage($bot->replyToken,$bot->userId);
        }
    }
    else
    { // Check Connect DB
        $bot->replyMessageNew($bot->replyToken,"ยังไม่ได้เชื่อมต่อกับฐานข้อมูล\nNot connection DB.");
    }
}
exit();
?>
