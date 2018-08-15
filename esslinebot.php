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
    $imageMapUrl = 'https://prosoft.gotdns.com/ESS/Content/Default/Images/icon-calendar.png?_ignored=';
    //$bot->replyMessageNew($bot->$replyToken,$imageMapUrl);
    /*$replyData = new ImagemapMessageBuilder(
        $imageMapUrl,
        'This is Title',
        new BaseSizeBuilder(200,300),
        array(
            new \LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder(
                'test image map',
                new \LINE\LINEBot\ImagemapActionBuilder\AreaBuilder(0,0,120,200)
                ),
            new \LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder(
                'https://www.ninenik.com',
                new \LINE\LINEBot\ImagemapActionBuilder\AreaBuilder(120,0,120,200)
                )
        )); 
        */
    $bot->replyMessageNew($bot->replyToken, 'https://prosoft.gotdns.com/ESS/Content/Default/Images/icon-calendar.png?_ignored=');
    //$bot->replyMessageNew($bot->$replyToken,$replyData);
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