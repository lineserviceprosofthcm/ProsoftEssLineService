<?php

include('line-bot.php');

$channelSecret = '24b8330e076be1a325adf77ff6d0f555';
$access_token  = 'mDXYsTvt05NiOjLkB4i0sSBL+u67LR/F0+xsdo4gzX3ApRhwnzZm+OHuMRpU8r/XGtALW0RmQoE7jXdwy3tp1CWw4Hg33idfG3RK1DU0SPL48NKxwfEZC57QdfOPFYxyTvw7qJ9le6IZ3BQWL6to8AdB04t89/1O/w1cDnyilFU=';


$bot = new BOT_API($channelSecret, $access_token);
$idnews = $_POST['txtNews'];


if(!empty($idnews)){

    $str = NEWS($idnews);
    
    $arr = SendUserID();
    $iCount = count($arr);
    for ($i = 0; $i<$iCount; $i++) {
        $bot->sendMessageNew($arr[$i],$str);
    }
// return echo "success";
}

if (!empty($bot->isEvents)) {
    if($bot->text == "im")
    {
        
        $bot->ApproveCenter($bot->replyToken,$bot->userId);
    }
/*
    if($bot->text == "ApproveCenter")
    {
        $bot->ApproveCenter($bot->replyToken,$bot->userId);
    }
    elseif($bot->text == "TimeAttendance")
    {
        $bot->TimeAttendance($bot->replyToken,$bot->userId);
    }
    elseif($bot->text == "Payroll")
    {
        $bot->Payroll($bot->replyToken);
    }
    elseif($bot->text == "Organization")
    {
        $bot->Organization($bot->replyToken);
    }
    elseif($bot->text == "Setting")
    {
        $bot->Setting($bot->replyToken,$bot->userId);
    }
    elseif($bot->text == "Language")
    {
        $bot->SendLanguage($bot->replyToken,$bot->userId);
    }
    elseif($bot->text == "AboutUs")
    {
        $bot->AboutUs($bot->replyToken);
    }
    elseif($bot->text == "Leave")
    {
        $bot->SendLeaveType($bot->replyToken);
    }
    else
    {
    $bot->replyMessageNew($bot->replyToken,"ไม่มีรายการที่เลือก");
    }
    */
    $bot->replyMessageNew($bot->replyToken, print "<html><body><div class='alert alert-danger' role='alert'>Failed to add data</div></body></html>");
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