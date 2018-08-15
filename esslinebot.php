<?php

include('line-bot.php');

$channelSecret = '24b8330e076be1a325adf77ff6d0f555';
$access_token  = 'mDXYsTvt05NiOjLkB4i0sSBL+u67LR/F0+xsdo4gzX3ApRhwnzZm+OHuMRpU8r/XGtALW0RmQoE7jXdwy3tp1CWw4Hg33idfG3RK1DU0SPL48NKxwfEZC57QdfOPFYxyTvw7qJ9le6IZ3BQWL6to8AdB04t89/1O/w1cDnyilFU=';


$bot = new BOT_API($channelSecret, $access_token);
//$idnews = $_POST['txtNews'];

/*
if(!empty($idnews)){

    $str = NEWS($idnews);
    
    $arr = SendUserID();
    $iCount = count($arr);
    for ($i = 0; $i<$iCount; $i++) {
        $bot->sendMessageNew($arr[$i],$str);
    }
// return echo "success";
}
*/
//$bot->replyMessageNew($replyToken,'Hello!');
if (!empty($bot->isEvents)) {
    if($bot->text == "im")
    {
        $imageMapUrl = 'http://prosoft.gotdns.com/ESS/Content/Default/Images/icon-calendar';
        $replyData = new ImagemapMessageBuilder(
            $imageMapUrl,
            'This is Title',
            new BaseSizeBuilder(699,1040),
            array(
                new ImagemapMessageActionBuilder(
                    'test image map',
                    new AreaBuilder(0,0,520,699)
                    ),
                new ImagemapUriActionBuilder(
                    'http://www.ninenik.com',
                    new AreaBuilder(520,0,520,699)
                    )
            ));  
       $bot->replyMessage($replyToken,$replyData);
    }else{
        $bot->replyMessageNew($replyToken,'Hello!');
    }
}else{
    $bot->replyMessageNew($replyToken,'Hello!');
}
?>