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
    $imageMapUrl = 'https://hcmdemo.prosoft.co.th/Content/Setup/Images/OrgUnitType.png';
        try {
            /*
            $replyData = new ImagemapMessageBuilder(
                $imageMapUrl,
                'This is Title',
                new BaseSizeBuilder(200,300),
                array(
                    new ImagemapMessageActionBuilder(
                        'test image map',
                        new AreaBuilder(0,0,120,300)
                        ),
                    new ImagemapUriActionBuilder(
                        'https://www.ninenik.com',
                        new AreaBuilder(120,0,120,300)
                        )
                )); 
*/
                $picFullSize = 'https://hcmdemo.prosoft.co.th/Content/Setup/Images/OrgUnitType.png';
                    $picThumbnail = 'https://hcmdemo.prosoft.co.th/Content/Setup/Images/OrgUnitType.png/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
            $bot->replyMessageNew($bot->$replyToken,'image map try');
        }
        catch{
            $bot->replyMessageNew($bot->replyToken, $imageMapUrl);
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