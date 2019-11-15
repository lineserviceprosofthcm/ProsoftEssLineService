<?php
header('Access-Control-Allow-Origin: *');
require('line-bot.php');
include('essconnect.php');

$channelSecret = 'b777323834edf3fd96558faf97a3a69a';
$access_token  = '5ZP5bMi9tgEXR9Zwq5+TBJ6C5pv5SMbaIWBes4l1MGHQph3JbHhQyqRWD+e7pxEKsmwy0i5qQWc7gEAhudIS2GuUPEQmNY21zhsL0nrRbkQVCVUl8HoKS7s11KpkPiOXIapgGt7EALkwBLicU19DqgdB04t89/1O/w1cDnyilFU=';

$NewsHDID = $_POST['NewsHDID'];
$News = $_POST['News'];
$LineIDLeaveRecord = $_POST['LineIDLeaveRecord'];
$LineIDAbstain = $_POST['LineIDAbstain'];
$Detail = $_POST['Detail'];
$LineID_NextApprove = $_POST['LineID_NextApprove'];
$LineID_NextApproveAbstain = $_POST['LineID_NextApproveAbstain'];
$WaitApprove = $_POST['WaitApprove'];
$LineID_EmpID = $_POST['LineID_EmpID'];
$LineID_EmpIDAbstain = $_POST['LineID_EmpIDAbstain'];
$ApproveStatus = $_POST['ApproveStatus'];
$bot = new BOT_API($channelSecret, $access_token);

// แจ้งข่าวสาร
if(!empty($NewsHDID)){
    $arr = SendNewsTo($NewsHDID);
    $iCount = count($arr);
    for ($i = 0; $i<$iCount; $i++) {
        $bot->SendMessageTo($arr[$i],$News);
    }
    echo json_encode(array('success' => '1'));
}

// แจ้งเอกสารลาหาผู้อนุมัติ
if(!empty($LineIDLeaveRecord)){
    $bot->SendMessageApproveTo($LineIDLeaveRecord,$Detail);
    echo json_encode(array('success' => '1'));
}

// แจ้งเอกสารลาคนอนุมัติถัดไป
if(!empty($LineID_NextApprove)){
    $bot->SendMessageApproveTo($LineID_NextApprove ,$WaitApprove);
    echo json_encode(array('success' => '1'));
}

// แจ้งเอกสารยกเว้นรูดบัตรหาผู้อนุมัติ
if(!empty($LineIDAbstain)){
    $bot->SendMessageApproveAbstainTo($LineIDAbstain,$Detail);
    echo json_encode(array('success' => '1'));
}

// แจ้งเอกสารยกเว้นรูดบัตรคนอนุมัติถัดไป
if(!empty($LineID_NextApproveAbstain)){
    $bot->SendMessageApproveAbstainTo($LineID_NextApproveAbstain ,$WaitApprove);
    echo json_encode(array('success' => '1'));
}

// แจ้งเอกสารหาผู้ขอลา
if(!empty($LineID_EmpID)){
    $bot->SendMessageToEmpRequest($LineID_EmpID ,$ApproveStatus);
    echo json_encode(array('success' => '1'));
}

// แจ้งเอกสารหาผู้ขอยกเว้นรูดบัตร
if(!empty($LineID_EmpIDAbstain)){
    $bot->SendMessageToEmpRequestAbstain($LineID_EmpIDAbstain ,$ApproveStatus);
    echo json_encode(array('success' => '1'));
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
            case "QR":
                $bot->photoQR($bot->replyToken);
            break;
            case "Test":
                Test();
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
