<?php
include('vendor/autoload.php');
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;

use \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use \LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
// use \LINE\LINEBot\MessageBuilder\ButtonTemplateBuilder;
//------------------------con-------------------//
use \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;

//------------test template------------------------------//
use \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;

class BOT_API extends LINEBot
{

    /* ====================================================================================
     * Variable
     * ==================================================================================== */

    private $httpClient     = null;
    private $endpointBase   = null;
    private $channelSecret  = null;

    public $content         = null;
    public $events          = null;

    public $isEvents        = false;
    public $isText          = false;
    public $isImage         = false;
    public $isSticker       = false;

    public $text            = null;
    public $replyToken      = null;
    public $source          = null;
    public $message         = null;
    public $timestamp       = null;

    public $response        = null;

    public $userId          = null;

    /* ====================================================================================
     * Custom
     * ==================================================================================== */

    public function __construct($channelSecret, $access_token)
    {
        $this->httpClient     = new CurlHTTPClient($access_token);
        $this->channelSecret  = $channelSecret;
        $this->endpointBase   = LINEBot::DEFAULT_ENDPOINT_BASE;

        $this->content        = file_get_contents('php://input');
        $events               = json_decode($this->content, true);

        if (!empty($events['events'])) {
            $this->isEvents = true;
            $this->events   = $events['events'];

            foreach ($events['events'] as $event) {
                $this->replyToken = $event['replyToken'];
                $this->source     = (object) $event['source'];
                $this->message    = (object) $event['message'];
                $this->timestamp  = $event['timestamp'];
                $this->userId     = $event['source']['userId'];
                if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
                    $this->isText = true;
                    $this->text   = $event['message']['text'];
                }

                if ($event['type'] == 'message' && $event['message']['type'] == 'image') {
                    $this->isImage = true;
                }

                if ($event['type'] == 'message' && $event['message']['type'] == 'sticker') {
                    $this->isSticker = true;
                }
            }
        }

        parent::__construct($this->httpClient, [ 'channelSecret' => $channelSecret ]);
    }

    public function sendMessageNew($to = null, $message = null)
    {
        $messageBuilder = new TextMessageBuilder($message);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
            'to' => $to,
            // 'toChannel' => 'Channel ID,
            'messages'  => $messageBuilder->buildMessage()
        ]);
    }

    public function replyMessageNew($replyToken = null, $message = null)
    {
        $messageBuilder = new TextMessageBuilder($message);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $messageBuilder->buildMessage(),
        ]);
    }

    public function isSuccess()
    {
        return !empty($this->response->isSucceeded()) ? true : false;
    }


    function ConnectDatabase()
    {
        $connectstr_dbhost = 'db4free.net:3307';
        $connectstr_dbname = 'esslinebot';
        $connectstr_dbusername = 'weatherzilla';
        $connectstr_dbpassword = '12345678';
    
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

    public function SendLanguage($replyToken = null)
    {
        $columns = array();
        $img_url = "https://photos.app.goo.gl/hWIiEm18sc9JTakS2";
        for ($i=0;$i<1;$i++) {
            $actions = array(
        new MessageTemplateActionBuilder("ภาษาไทย", "0"),
        new MessageTemplateActionBuilder("English", "1"),
      );
            $column = new CarouselColumnTemplateBuilder("Language", "กรุณาเลือกภาษาที่ใช้แสดง\nPlease select a display language.", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Carousel Demo", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
              'replyToken' => $replyToken,
              'messages'   => $outputText->buildMessage(),
          ]);
    }

    public function SendRELanguage($replyToken = null)
    {
        $columns = array();
        $img_url = "https://photos.app.goo.gl/hWIiEm18sc9JTakS2";
        for ($i=0;$i<1;$i++) {
            $actions = array(
          new MessageTemplateActionBuilder("ภาษาไทย", "0"),
          new MessageTemplateActionBuilder("English", "1"),
          new MessageTemplateActionBuilder("ยกเลิก(Cancel)", "ยกเลิก"),
        );
            $column = new CarouselColumnTemplateBuilder("Language", "กรุณาเลือกภาษาทต้องการเปลี่ยน\nPlease select a display language.", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Carousel Demo", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
                'replyToken' => $replyToken,
                'messages'   => $outputText->buildMessage(),
            ]);
    }

    public function SendSettingTH($replyToken = null)
    {
        $columns = array();
        $img_url = "https://photos.app.goo.gl/s0ekbGqkpB81vkLd2";
        for ($i=0;$i<1;$i++) {
            $actions = array(
            new MessageTemplateActionBuilder("เปลี่ยนภาษา", "Language"),
            new MessageTemplateActionBuilder("ยกเลิกการลงทะเบียน", "Unregister"),
          );
            $column = new CarouselColumnTemplateBuilder("Language", "กรุณาเลือกภาษาทต้องการเปลี่ยน\nPlease select a display language.", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Carousel Demo", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
                  'replyToken' => $replyToken,
                  'messages'   => $outputText->buildMessage(),
              ]);
    }

///////////////////////////////////////////////*โต้ตอบด้วยเทมเพตภาษาไทย*/////////////////////////////////////////////////
///////////////////////////////////////////////*โต้ตอบด้วยเทมเพตภาษาไทย*/////////////////////////////////////////////////

    public function SendTemplate($replyToken = null)
    {
        $actions = array(
        new MessageTemplateActionBuilder("ขออนุมัติลา", "ขอลา"),
        new MessageTemplateActionBuilder("เปลี่ยนภาษา", "Language")

    );
        $img_url = "https://photos.app.goo.gl/0tXTSQGWqqcTTJ1M2";
        $button = new ButtonTemplateBuilder("เมนู", "กรุณาเลือกรายการที่ต้องการ", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Button template builder", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
    }

    public function Approved($replyToken = null,$text)
    {
        $actions = array(
        new MessageTemplateActionBuilder("อนุมัติ", "Y".$text.""),
        new MessageTemplateActionBuilder("ไม่อนุมัติ", "N".$text."")
      );
        $button = new ConfirmTemplateBuilder("กรุณาเลือกทำรายการ", $actions);
        $outputText = new TemplateMessageBuilder("lineconfirm", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
    }


    public function MNGApproved($replyToken = null,$userId)
    {
        $link = ConnectDatabase();
        $sql = "SELECT Docuno FROM hrTimeLeaveRecord,emLineuserconnect,emGrantApprove
                WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                AND  emLineuserconnect.UserID = '".$userId."' 
                AND hrTimeLeaveRecord.ApproveStatus = 'W'";
        $result = mysqli_query($link, $sql);
        $arr = [];

        if (mysqli_num_rows($result) > 0)
          {
              while($row = mysqli_fetch_assoc($result))
              {
                array_push($arr,new MessageTemplateActionBuilder("".$row['Docuno']."", "".$row['Docuno'].""));        
              }
         }
         $actions = $arr;

    $img_url = "https://photos.app.goo.gl/1TsYBYnQoinuVMBv2";
        $button = new ButtonTemplateBuilder("เอกสารขออนุมัติลา", "กรุณาเลือกเอกสารขออนุมัติลา", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Button template builder", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
      $link->close();
    }

    public function SendApproved($replyToken = null)
    {
        $actions = array(
            new MessageTemplateActionBuilder("ลาป่วย", "L-001"),
            new MessageTemplateActionBuilder("ลากิจ", "L-002"),
            new MessageTemplateActionBuilder("ลาพักร้อน", "L-003"),
            new MessageTemplateActionBuilder("ยกเลิก", "ยกเลิก"),

    );
    $img_url = "https://photos.app.goo.gl/rtW9Vowe6P2iQmIS2";
        $button = new ButtonTemplateBuilder("ประเภทการลา", "กรุณาเลือกรายการที่ต้องการ", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Button template builder", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
    }

///////////////////////////////////////////////*สิ้นสุดการโต้ตอบด้วยภาษาไทย*/////////////////////////////////////////////////
///////////////////////////////////////////////*สิ้นสุดการโต้ตอบด้วยภาษาไทย*/////////////////////////////////////////////////

////////////////////////////////////////////////*โต้ตอบด้วยเทมเพต ENG*////////////////////////////////////////////////////
////////////////////////////////////////////////*โต้ตอบด้วยเทมเพต ENG*////////////////////////////////////////////////////

    public function SendTemplateENG($replyToken = null)
    {
        $actions = array(
       new MessageTemplateActionBuilder("Approv Leave", "Leave"),
       new MessageTemplateActionBuilder("Setting Language", "Language")
   );
        $img_url = "https://photos.app.goo.gl/0tXTSQGWqqcTTJ1M2";
        $button = new ButtonTemplateBuilder("Menu", "Please select the items you want.", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Button template builder", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
         'replyToken' => $replyToken,
         'messages'   => $outputText->buildMessage(),
     ]);
    }

    public function ApprovedENG($replyToken = null,$text)
    {
        $actions = array(
        new MessageTemplateActionBuilder("Approved", "Y".$text.""),
        new MessageTemplateActionBuilder("Not Approved", "N".$text."")
      );
        $button = new ConfirmTemplateBuilder("Please select an item.", $actions);
        $outputText = new TemplateMessageBuilder("lineconfirm", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
    }

    public function MNGApprovedENG($replyToken = null,$userId)
    {
        $link = ConnectDatabase();
        $sql = "SELECT Docuno FROM hrTimeLeaveRecord,emLineuserconnect,emGrantApprove
                WHERE emLineuserconnect.EmpID = emGrantApprove.ApproverID 
                AND  emLineuserconnect.UserID = '".$userId."' 
                AND hrTimeLeaveRecord.ApproveStatus = 'W'";
        $result = mysqli_query($link, $sql);
        $arr = [];

        if (mysqli_num_rows($result) > 0)
          {
              while($row = mysqli_fetch_assoc($result))
              {
                array_push($arr,new MessageTemplateActionBuilder("".$row['Docuno']."", "".$row['Docuno'].""));        
              }
         }
         $actions = $arr;

    $img_url = "https://photos.app.goo.gl/1TsYBYnQoinuVMBv2";
        $button = new ButtonTemplateBuilder("Document on leave", "Please select an item.", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Button template builder", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
      $link->close();
    }

    public function SendApprovedENG($replyToken = null)
    {
        $actions = array(
            new MessageTemplateActionBuilder("Sick leave", "L-001"),
            new MessageTemplateActionBuilder("Errand leave", "L-002"),
            new MessageTemplateActionBuilder("Holiday leave", "L-003"),
            new MessageTemplateActionBuilder("Cancel", "Cancel"),

    );
    $img_url = "https://photos.app.goo.gl/rtW9Vowe6P2iQmIS2";
        $button = new ButtonTemplateBuilder("Type of leave", "Please select an item.", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Button template builder", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
    }

    public function SendSettingENG($replyToken = null)
    {
        $columns = array();
        $img_url = "https://photos.app.goo.gl/s0ekbGqkpB81vkLd2";
        for ($i=0;$i<1;$i++) {
            $actions = array(
              new MessageTemplateActionBuilder("Change language", "Language"),
              new MessageTemplateActionBuilder("Cancel Registration", "Unregister"),
            );
            $column = new CarouselColumnTemplateBuilder("Language", "กรุณาเลือกภาษาทต้องการเปลี่ยน\nPlease select a display language.", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Carousel Demo", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
                    'replyToken' => $replyToken,
                    'messages'   => $outputText->buildMessage(),
                ]);
    }

    public static function verify($access_token)
    {
        $ch = curl_init('https://api.line.me/v1/oauth/verify');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $access_token ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }
}
