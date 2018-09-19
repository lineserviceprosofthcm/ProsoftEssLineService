<?php

require('line-bot.php');
include('essconnect.php');

$channelSecret = '24b8330e076be1a325adf77ff6d0f555';
$access_token  = 'mDXYsTvt05NiOjLkB4i0sSBL+u67LR/F0+xsdo4gzX3ApRhwnzZm+OHuMRpU8r/XGtALW0RmQoE7jXdwy3tp1CWw4Hg33idfG3RK1DU0SPL48NKxwfEZC57QdfOPFYxyTvw7qJ9le6IZ3BQWL6to8AdB04t89/1O/w1cDnyilFU=';
$NewsHDID = $_POST['NewsHDID'];
$News = $_POST['News'];
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
    for ($i = 0; $i<$iCount; $i++) {
        $bot->SendMessageTo($arr[$i],$News);
    }
    //$ArrID = array("U7fb3dc484426fb164c424df09b7a42ba","U05a39ae3a619678ef4b1b58111980a79");
    //$iCount = count($ArrID);
    //for ($i = 0; $i<$iCount; $i++) {
    //    $bot->SendMessageTo($ArrID[$i],$News);
    //}
    //$bot->SendMessageTo("U7fb3dc484426fb164c424df09b7a42ba",$News);
}

// แจ้งเอกสารลาหาผู้อนุมัติ
if(!empty($LineIDLeaveRecord)){
    $bot->SendMessageApproveTo($LineIDLeaveRecord,$Detail);
    //$bot->SendMessageApproveTo("U7fb3dc484426fb164c424df09b7a42ba",$Detail);
}

// แจ้งเอกสารคนอนุมัติถัดไป
if(!empty($LineID_NextApprove)){
    $bot->SendMessageApproveTo($LineID_NextApprove ,$WaitApprove);
    //$bot->SendMessageApproveTo("U7fb3dc484426fb164c424df09b7a42ba",$WaitApprove);
}

// แจ้งเอกสารหาผู้ขอลา
if(!empty($LineID_EmpID)){
    $bot->SendMessageToEmpRequest($LineID_EmpID ,$ApproveStatus);
    //$bot->SendMessageToEmpRequest("U7fb3dc484426fb164c424df09b7a42ba",$ApproveStatus);
}

if (!empty($bot->isEvents)) {
    $Language = GetLanguage($bot->userId);
    if($Language == "th-TH")
    {
        /*if($bot->text == "Approve Center")
        {
            $bot->ApproveCenter($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "Time Attendance")
        {
            $bot->TimeAttendance($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "จำนวนวันอนุญาตลา")
        {
            $Text = LeaveRemainNum($bot->userId);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }
        elseif($bot->text == "Payroll")
        {
            $bot->Payroll($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "ขอสลิปเงินเดือน")
        {
            $Text = EPaySlip($bot->userId);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }
        elseif($bot->text == "Organization")
        {
            $bot->Organization($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "วันหยุดองค์กร")
        {
            $Text = calendar($bot->userId);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }
        elseif($bot->text == "ที่ตั้งองค์กร")
        {
            $Text = LocationOrganization($bot->userId);
            $bot->LocationOrg($bot->replyToken,$Text);
        }
        elseif($bot->text == "Setting")
        {
            $bot->Setting($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "เปลี่ยนภาษา")
        {
            $bot->SendLanguage($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "ภาษาไทย (Thai)" || $bot->text == "ภาษาอังกฤษ (English)")
        {   
            $Text = ChangeLanguage($bot->userId,$bot->text);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }
        elseif($bot->text == "AboutUs")
        {
            $bot->AboutUs($bot->replyToken);
        }
        else
        {
            $bot->BOT_New($bot->replyToken,$bot->text);
        }*/
        switch($bot->text){
            case "Approve Center":
                $bot->ApproveCenter($bot->replyToken,$bot->userId);
            break;
            case "Time Attendance":
                $bot->TimeAttendance($bot->replyToken,$bot->userId);
            break;
            case "วันอนุญาตลา":
                $Text = LeaveRemainNum($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Payroll":
                $bot->Payroll($bot->replyToken,$bot->userId);
            break;
            case "ขอสลิปเงินเดือน":
                $Text = EPaySlip($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Organization":
                $bot->Organization($bot->replyToken,$bot->userId);
            break;
            case "วันหยุดองค์กร":
                $Text = calendar($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "ที่ตั้งองค์กร":
                $Text = LocationOrganization($bot->userId);
                $bot->LocationOrg($bot->replyToken,$Text);
            break;
            case "Setting":
                $bot->Setting($bot->replyToken,$bot->userId);
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
            case "AboutUs":
                $bot->AboutUs($bot->replyToken);
            break;
            default:
                $bot->BOT_New($bot->replyToken,$bot->text);
            break;
        }
    }
    else if($Language == "en-US") //#####################################################################################
    {
        /*if($bot->text == "Approve Center")
        {
            $bot->ApproveCenterEng($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "Time Attendance")
        {
            $bot->TimeAttendanceEng($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "Leave Remain")
        {
            $Text = LeaveRemainNumEng($bot->userId);
            $bot->replyMessageNew($bot->replyToken,$Text);
            //$bot->LeaveRemain($bot->replyToken);
        }
        elseif($bot->text == "Payroll")
        {
            $bot->PayrollEng($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "E-Pay Slip")
        {
            $Text = EPaySlip($bot->userId);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }
        elseif($bot->text == "Organization")
        {
            $bot->OrganizationEng($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "Calendar")
        {
            $Text = CalendarEng($bot->userId);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }
        elseif($bot->text == "Location")
        {
            $Text = LocationOrganization($bot->userId);
            $bot->LocationOrg($bot->replyToken,$Text);
        }
        elseif($bot->text == "Setting")
        {
            $bot->SettingEng($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "Language")
        {
            $bot->SendLanguage($bot->replyToken,$bot->userId);
        }
        elseif($bot->text == "ภาษาไทย (Thai)" || $bot->text == "ภาษาอังกฤษ (English)")
        {   
            $Text = ChangeLanguage($bot->userId,$bot->text);
            $bot->replyMessageNew($bot->replyToken,$Text);
        }
        elseif($bot->text == "AboutUs")
        {
            $bot->AboutUs($bot->replyToken);
        }
        else
        {
            $bot->BOT_New($bot->replyToken,$bot->text);
        }*/
        switch($bot->text){
            case "Approve Center":
                $bot->ApproveCenterEng($bot->replyToken,$bot->userId);
            break;
            case "Time Attendance":
                $bot->TimeAttendanceEng($bot->replyToken,$bot->userId);
            break;
            case "Leave Remain":
                $Text = LeaveRemainNumEng($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Payroll":
                $bot->PayrollEng($bot->replyToken,$bot->userId);
            break;
            case "E-Pay Slip":
                $Text = EPaySlip($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Organization":
                $bot->OrganizationEng($bot->replyToken,$bot->userId);
            break;
            case "Organization Calendar":
                $Text = CalendarEng($bot->userId);
                $bot->replyMessageNew($bot->replyToken,$Text);
            break;
            case "Location of Organization":
                $Text = LocationOrganization($bot->userId);
                $bot->LocationOrg($bot->replyToken,$Text);
            break;
            case "Setting":
                $bot->Setting($bot->replyToken,$bot->userId);
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
            case "AboutUs":
                $bot->AboutUs($bot->replyToken);
            break;
            default:
                $bot->BOT_New($bot->replyToken,$bot->text);
            break;
        }
    }
    else
    {
        $bot->SendLanguage($bot->replyToken,$bot->userId);
    }
}

if ($bot->isSuccess()) 
{
  echo 'Succeeded!';
  exit();
}

// Failed
echo $bot->response->getHTTPStatus . ' ' . $bot->response->getRawBody();
exit();
?>

