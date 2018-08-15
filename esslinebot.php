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
    switch($bot->text){
        case:'ApproveCenter'
            $bot->ApproveCenter($bot->replyToken,$bot->userId);
        break;
        case:'TimeAttendance'
            $bot->TimeAttendance($bot->replyToken,$bot->userId);
        break;
        case:'Payroll'
            $bot->Payroll($bot->replyToken;
        break;
        case:'Organization'
            $bot->Organization($bot->replyToken);
        break;
        case:'Setting'
            $bot->Setting($bot->replyToken,$bot->userId);
        break;
        case:'Language'
            $bot->Language($bot->replyToken,$bot->userId);
        break;
        case:'AboutUs'
            $bot->AboutUs($bot->replyToken);
        break;
        case:'Leave'
            $bot->SendLeaveType($bot->replyToken);
        break;
        default:
            $bot->replyMessageNew($bot->replyToken,"ไม่มีรายการที่เลือก");
        break;
    }
    $bot->replyMessageNew($bot->replyToken, $bot->response);
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