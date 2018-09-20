<?php 
include('vendor/autoload.php');
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use \LINE\LINEBot\MessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use \LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use \LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use \LINE\LINEBot\ImagemapActionBuilder;
use \LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use \LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use \LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use \LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use \LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\TemplateActionBuilder;
use \LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;

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
    public $isImagemap      = false;
    public $isMessage       = false;

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
    const DEFAULT_ENDPOINT_BASE = 'https://api.line.me';
    public function __construct($channelSecret, $access_token)
    {
        $this->httpClient     = new CurlHTTPClient($access_token);
        $this->channelSecret  = $channelSecret;
        $this->endpointBase   = LINEBot::DEFAULT_ENDPOINT_BASE;
        $this->content        = file_get_contents('php://input');
        $events               = json_decode($this->content, true);
        //$this->AppLink        = AppLink();
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
            }
        }
        parent::__construct($this->httpClient, [ 'channelSecret' => $channelSecret ]);
    }
public function SendMessageTo($ToLineID = null, $message = null){
    $messageBuilder = new TextMessageBuilder($message);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
        'to' => $ToLineID,
        // 'toChannel' => 'Channel ID,
        'messages'  => $messageBuilder->buildMessage()
    ]);
}
public function SendMessageApproveTo($ToLineID = null, $message = null){
    //$messageBuilder = new TextMessageBuilder($message);
    $Temp = new TemplateMessageBuilder('Approve Center',
        new ConfirmTemplateBuilder(
            $message, // ข้อความแนะนำหรือบอกวิธีการ หรือคำอธิบาย
                array(
                    new UriTemplateActionBuilder(
                        'Go to', // ข้อความสำหรับปุ่มแรก
                        "https://lineservice.prosofthcm.com/LineService/ApproveLeave/ApproveLeaveInfo/".$ToLineID // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                    ),
                    new MessageTemplateActionBuilder(
                        'Approve', // ข้อความสำหรับปุ่มแรก
                        "https://lineservice.prosofthcm.com/LineService/ApproveLeave/ApproveLeaveInfo/".$ToLineID // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                    )
                )
            )
        );
    $multiMessage = new MultiMessageBuilder;
    //$multiMessage->add($messageBuilder);
    $multiMessage->add($Temp);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
        'to' => $ToLineID,
        // 'toChannel' => 'Channel ID,
        'messages'  => $multiMessage->buildMessage()
    ]);
}
public function SendMessageToEmpRequest($ToLineID = null, $message = null){
    //$messageBuilder = new TextMessageBuilder($message);
    $Temp = new TemplateMessageBuilder('Approve Center',
        new ConfirmTemplateBuilder(
            $message, // ข้อความแนะนำหรือบอกวิธีการ หรือคำอธิบาย
                array(
                    new UriTemplateActionBuilder(
                        'Go to information', // ข้อความสำหรับปุ่มแรก
                        "https://lineservice.prosofthcm.com/LineService/LeaveRequest/LeaveRequestList/".$ToLineID // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                    ),
                    new UriTemplateActionBuilder(
                        'Go to request', // ข้อความสำหรับปุ่มแรก
                        "https://lineservice.prosofthcm.com/LineService/LeaveRequest/LeaveRequestList/".$ToLineID // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                    )
                )
            )
        );
    $multiMessage = new MultiMessageBuilder;
    //$multiMessage->add($messageBuilder);
    $multiMessage->add($Temp);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
        'to' => $ToLineID,
        // 'toChannel' => 'Channel ID,
        'messages'  => $multiMessage->buildMessage()
    ]);
}
public function replyMessageNew($replyToken = null, $message = null){
    $messageBuilder = new TextMessageBuilder($message);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $messageBuilder->buildMessage(),
    ]);
}
public function SendLanguage($replyToken = null, $LineID){
    $img_url = "https://www.prosofthcm.com/upload/5934/LK2wVaS34N.jpg";
        /*
        $actions = array(
            New UriTemplateActionBuilder("ภาษาไทย (Thai)", "https://lineservice.prosofthcm.com/LineService/Language/Language/".$LineID."/th-TH"),
            New UriTemplateActionBuilder("ภาษาอังกฤษ (English)", "https://lineservice.prosofthcm.com/LineService/Language/Language/".$LineID."/en-US")
        );
        */
        $actions = array(
            New MessageTemplateActionBuilder("ภาษาไทย (Thai)", "ภาษาไทย (Thai)"),
            New MessageTemplateActionBuilder("ภาษาอังกฤษ (English)", "ภาษาอังกฤษ (English)")
        );
        $button = new ButtonTemplateBuilder("Language Setting","กรุณาเลือกภาษาที่ต้องการใช้งาน...\nPlease select language...", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Language Setting", $button);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
      'replyToken' => $replyToken,
      'messages'   => $outputText->buildMessage(),
  ]);
}
public function Register($replyToken = null, $LineID){
    $actions = array(
        New UriTemplateActionBuilder("ลงทะเบียน", "https://lineservice.prosofthcm.com/LineService/Register/RegisterInfo/".$LineID),
        New MessageTemplateActionBuilder("ย้อนกลับ", "ย้อนกลับ")
    );
    $button  = new ConfirmTemplateBuilder("ลงทะเบียนใช้งาน\nYou have not yet registered" , $actions);
    $outputText = new TemplateMessageBuilder("ลงทะเบียนใช้งาน", $button);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
}
public function ApproveCenter($replyToken = null,$LineID)
{
    //$Link = AppLink();
    $actions = array(
        New UriTemplateActionBuilder("ขออนุมัติลา", "https://lineservice.prosofthcm.com/LineService/LeaveRequest/LeaveRequestInfo/".$LineID),
        New UriTemplateActionBuilder("ขอยกเว้นรูดบัตร", "https://lineservice.prosofthcm.com/LineService/AbstainTime/AbstainTimeInfo/".$LineID),
        New UriTemplateActionBuilder("อนุมัติเอกสารลา", "https://lineservice.prosofthcm.com/LineService/ApproveLeave/ApproveLeaveInfo/".$LineID),
        New UriTemplateActionBuilder("อนุมัติยกเว้นรูดบัตร", "https://lineservice.prosofthcm.com/LineService/ApproveRequestAbstain/ApproveAbstainlnfo/".$LineID)
        );
    $img_url = "https://www.prosofthcm.com/upload/5934/BEQPPo7iiF.jpg";
    $button  = new ButtonTemplateBuilder("Approve Center", "สำหรับขอ/อนุมัติเอกสารต่าง ๆ...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Approve Center", $button);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}
public function ApproveCenterEng($replyToken = null,$LineID)
{
    $actions = array(
        New UriTemplateActionBuilder("Leave Request", "https://lineservice.prosofthcm.com/LineService/LeaveRequest/LeaveRequestInfo/".$LineID),
        New UriTemplateActionBuilder("Abstain Time", "https://lineservice.prosofthcm.com/LineService/AbstainTime/AbstainTimeInfo/".$LineID),
        New UriTemplateActionBuilder("Approve Leave", "https://lineservice.prosofthcm.com/LineService/ApproveLeave/ApproveLeaveInfo/".$LineID),
        New UriTemplateActionBuilder("Approve Abstain", "https://lineservice.prosofthcm.com/LineService/ApproveRequestAbstain/ApproveAbstainlnfo/".$LineID)
        );
    $img_url = "https://www.prosofthcm.com/upload/5934/BEQPPo7iiF.jpg";
    $button  = new ButtonTemplateBuilder("Approve Center", "For request or approve documents...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Approve Center", $button);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}
public function TimeAttendance($replyToken = null, $LineID)
{
    $actions = array(
        New UriTemplateActionBuilder("ลงเวลาเข้างาน", "https://lineservice.prosofthcm.com/LineService/TimeStamp/TimeStampInfo/".$LineID),
        New UriTemplateActionBuilder("ข้อมูลเวลาทำงาน", "https://lineservice.prosofthcm.com/LineService/WorkTime/WorkTimeInfo/".$LineID),
        New MessageTemplateActionBuilder("สิทธิ์การลาและวันลาคงเหลือ", "สิทธิ์การลาและวันลาคงเหลือ"),
        New UriTemplateActionBuilder("ข้อมูลการขอลา", "https://lineservice.prosofthcm.com/LineService/LeaveRequest/LeaveRequestList/".$LineID)
        );
    $img_url = "https://www.prosofthcm.com/upload/5934/4XNG8W47Yn.jpg";
    $button  = new ButtonTemplateBuilder("Time Attendence", "สำหรับจัดการข้อมูลเวลาการทำงาน...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Time Attendence", $button);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function TimeAttendanceEng($replyToken = null, $LineID)
{
    $actions = array(
        New UriTemplateActionBuilder("Time Stamp", "https://lineservice.prosofthcm.com/LineService/TimeStamp/TimeStampInfo/".$LineID),
        New UriTemplateActionBuilder("Work Time Detail", "https://lineservice.prosofthcm.com/LineService/WorkTime/WorkTimeInfo/".$LineID),
        New MessageTemplateActionBuilder("Leave Remain", "Leave Remain"),
        New UriTemplateActionBuilder("Leave Information", "https://lineservice.prosofthcm.com/LineService/LeaveRequest/LeaveRequestList/".$LineID)
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/4XNG8W47Yn.jpg";
    $button  = new ButtonTemplateBuilder("Time Attendence", "For manage work time data...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Time Attendence", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function Payroll($replyToken = null,$LineID)
{
    $actions = array(
        New MessageTemplateActionBuilder("ขอสลิปเงินเดือน", "ขอสลิปเงินเดือน")
        //New MessageTemplateActionBuilder("ขอเอกสาร 50 ทวิ", "ขอเอกสาร 50 ทวิ"),
        //New MessageTemplateActionBuilder("Works Cer.Request", "Works Cer.Request"),
        //New MessageTemplateActionBuilder("Salary Cer.Request", "Salary Cer.Request")
        
        /*
        New UriTemplateActionBuilder("Tax Calculator", "https://www.prosofthcm.com/Article/Detail/65472"),
        New UriTemplateActionBuilder("Google", "http://www.Google.co.th"),
        New MessageTemplateActionBuilder("Test", "Test")
        */
         );

    $img_url = "https://www.prosofthcm.com/upload/5934/CGD9pX8Q9X.jpg";
    $button  = new ButtonTemplateBuilder("Payroll", "สำหรับจัดการข้อมูลเงินเดือน...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Payroll", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}
    
public function PayrollEng($replyToken = null,$LineID)
{
    $actions = array(
        New MessageTemplateActionBuilder("E-Pay Slip", "E-Pay Slip")
        //New MessageTemplateActionBuilder("ขอเอกสาร 50 ทวิ", "ขอเอกสาร 50 ทวิ"),
        //New MessageTemplateActionBuilder("Works Cer.Request", "Works Cer.Request"),
        //New MessageTemplateActionBuilder("Salary Cer.Request", "Salary Cer.Request")
        
        /*
        New UriTemplateActionBuilder("Tax Calculator", "https://www.prosofthcm.com/Article/Detail/65472"),
        New UriTemplateActionBuilder("Google", "http://www.Google.co.th"),
        New MessageTemplateActionBuilder("Test", "Test")
        */
         );

    $img_url = "https://www.prosofthcm.com/upload/5934/CGD9pX8Q9X.jpg";
    $button  = new ButtonTemplateBuilder("Payroll", "For manage your salary data...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Payroll", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function Organization($replyToken = null,$LineID)
{
    $actions = array(
        New MessageTemplateActionBuilder("วันหยุดองค์กร", "วันหยุดองค์กร"),
        New UriTemplateActionBuilder("สร้างข่าวสารองค์กร", "https://lineservice.prosofthcm.com/LineService/News/NewsInfo/".$LineID),
        New UriTemplateActionBuilder("ข้อมูลข่าวสาร", "https://lineservice.prosofthcm.com/LineService/News/NewsList/".$LineID),
        New MessageTemplateActionBuilder("ที่ตั้งองค์กร", "ที่ตั้งองค์กร")
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/VFrLXsJrey.jpg";
    $button  = new ButtonTemplateBuilder("Organization", "สำหรับดูข้อมูลเกี่ยวกับองค์กร...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Organization", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
    
}

public function OrganizationEng($replyToken = null,$LineID)
{
    $actions = array(
        New MessageTemplateActionBuilder("Calendar", "Organization Calendar"),
        New UriTemplateActionBuilder("Create News", "https://lineservice.prosofthcm.com/LineService/News/NewsInfo/".$LineID),
        New UriTemplateActionBuilder("News List", "https://lineservice.prosofthcm.com/LineService/News/NewsList/".$LineID),
        New MessageTemplateActionBuilder("Location", "Location of Organization")
        );    

    $img_url = "https://www.prosofthcm.com/upload/5934/VFrLXsJrey.jpg";
    $button  = new ButtonTemplateBuilder("Organization", "For view about organization data...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Organization", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
    
}

public function Setting($replyToken = null, $LineID)
{
    $actions = array(        
        New UriTemplateActionBuilder("ลงทะเบียน", "https://lineservice.prosofthcm.com/LineService/Register/RegisterInfo/".$LineID),
        New MessageTemplateActionBuilder("เปลี่ยนภาษา", "เปลี่ยนภาษา")
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/3dHoTCaSmu.jpg";
    $button  = new ButtonTemplateBuilder("Setting", "สำหรับตั้งค่าการใช้งานระบบ...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Setting", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function SettingEng($replyToken = null, $LineID)
{
    $actions = array(        
        New UriTemplateActionBuilder("Register", "https://lineservice.prosofthcm.com/LineService/Register/RegisterInfo/".$LineID),
        New MessageTemplateActionBuilder("Language", "Language")
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/3dHoTCaSmu.jpg";
    $button  = new ButtonTemplateBuilder("Setting", "For setting the system...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Setting", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function AboutUs($replyToken = null)
{
    $actions = array(
        New UriTemplateActionBuilder("Redirect", "https://www.prosofthcm.com/")
        //New UriTemplateActionBuilder("Getlocation", "https://lineservice.prosofthcm.com/LineService/GetLocaltion/GetLocaltion"),
        //New MessageTemplateActionBuilder("Test", "Test"),
        //New MessageTemplateActionBuilder("Test", "Test")
         );

    $img_url = "https://www.prosofthcm.com/upload/5934/kXfjuHYzSj.jpg";
    $button  = new ButtonTemplateBuilder("About Us", "Menu", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("About Us", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function photoQR($replyToken = null)
{
$outputText = new ImageMessageBuilder("https://lineservice.prosofthcm.com/upload/Resource/Linebot.png", "https://lineservice.prosofthcm.com/upload/Resource/Linebot.png");
$this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
    'replyToken' => $replyToken,
    'messages'   => $outputText->buildMessage(),
]);
//$response = $bot->replyMessage($event->getReplyToken(), $outputText);
}

public function LocationOrg($replyToken = null,$Text)
{
    $split = explode(",", $Text);
    $DetailOrg = $split[0];
    $Latitude = $split[1];
    $Longtitude = $split[2];
    $Phone = $split[3];

    $outputText = new LocationMessageBuilder($Phone,$DetailOrg,$Latitude,$Longtitude);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
    'replyToken' => $replyToken,
    'messages'   => $outputText->buildMessage(),
    ]);
}

public function BOT_New($replyToken = null, $text)
{
    $TEXT = substr($text, 0, 2);
    $textsub = substr($text, 2, 100);
    $split = explode(",", $textsub);
    switch($TEXT){
        case "Lo":
            $outputText = new LocationMessageBuilder("GetLocation",$split[0].",".$split[1],$split[0],$split[1]);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $outputText->buildMessage(),
            ]);
        break;
        case "Qr":
            $outputText = new ImageMessageBuilder("https://lineservice.prosofthcm.com/upload/Resource/Linebot.png", "https://lineservice.prosofthcm.com/upload/Resource/Linebot.png");
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $outputText->buildMessage(),
            ]);
        break;
        case "St":
            $replyData = new StickerMessageBuilder("1","17");
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $replyData->buildMessage(),
            ]);
        break;
        case "To":
            $messageBuilder = new TextMessageBuilder($split[1]);
            $StickerBuilder = new StickerMessageBuilder($split[2],$split[3]);
            $multiMessage = new MultiMessageBuilder;
            $multiMessage->add($messageBuilder);
            $multiMessage->add($StickerBuilder);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
            'to' => $split[0],
            'messages'   => $multiMessage->buildMessage(),
            ]);
        break;
        case "P1":
            $outputText = new ImageMessageBuilder("https://avatars2.githubusercontent.com/u/1119714?s=300", "https://avatars2.githubusercontent.com/u/1119714?s=300");
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $outputText->buildMessage(),
            ]);
        break;
        case "Im":
            $base = new BaseSizeBuilder(699,1040);
            $arr = array(
                new ImagemapUriActionBuilder("https://www.google.co.th", new AreaBuilder(0,0,520,699)),
                new ImagemapMessageActionBuilder("Text", new AreaBuilder(520,0,520,699))
            );
            $replyData = new ImagemapMessageBuilder("https://avatars2.githubusercontent.com/u/1119714?s=1040","test",$base,$arr);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $replyData->buildMessage(),
        ]);
        break;
        case "T1":
            $base = new BaseSizeBuilder(1040,710);
            $arr = array(
                new ImagemapMessageActionBuilder("Text1", new AreaBuilder(0,790,1040,130)),
                new ImagemapMessageActionBuilder("Text2", new AreaBuilder(0,660,1040,130)),
                new ImagemapMessageActionBuilder("Text3", new AreaBuilder(0,530,1040,130)),
                new ImagemapMessageActionBuilder("Text4", new AreaBuilder(0,400,1040,130))

                //new ImagemapUriActionBuilder("https://www.google.co.th", new AreaBuilder(35,624,965,199)),
                //new ImagemapUriActionBuilder("https://www.google.co.th", new AreaBuilder(35,823,965,186)),
                //new ImagemapUriActionBuilder("https://www.google.co.th", new AreaBuilder(35,1009,965,188)),
                //new ImagemapMessageActionBuilder("Text", new AreaBuilder(35,1197,965,187))
            );
            $replyData = new ImagemapMessageBuilder("https://www.prosofthcm.com/upload/5934/ZIkjVrH1Mv.png?S=699","test",$base,$arr);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $replyData->buildMessage(),
        ]);
        break;
        case "T2":
            $base = new BaseSizeBuilder(1040,710);
            $arr = array(
                new ImagemapMessageActionBuilder("Text4", new AreaBuilder(0,790,1040,130)),
                new ImagemapMessageActionBuilder("Text3", new AreaBuilder(0,660,1040,130)),
                new ImagemapMessageActionBuilder("Text2", new AreaBuilder(0,530,1040,130)),
                new ImagemapMessageActionBuilder("Text1", new AreaBuilder(0,400,1040,130))
            );
            $replyData = new ImagemapMessageBuilder("https://www.prosofthcm.com/upload/5934/epGPOPH7LC.png?S=699","test",$base,$arr);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $replyData->buildMessage(),
        ]);
        break;
        case "T3":
            $base = new BaseSizeBuilder(699,900);
            $arr = array(
                new ImagemapUriActionBuilder("https://www.google.co.th", new AreaBuilder(0,0,520,699)),
                new ImagemapMessageActionBuilder("Text", new AreaBuilder(520,0,520,699))
            );
            $replyData = new ImagemapMessageBuilder("https://www.prosofthcm.com/upload/5934/zMqgwsQ36v.png?S=600","test",$base,$arr);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $replyData->buildMessage(),
        ]);
        break;
        case "T4":
            $base = new BaseSizeBuilder(699,1040);
            $arr = array(
                new ImagemapUriActionBuilder("https://www.google.co.th", new AreaBuilder(0,0,520,699)),
                new ImagemapMessageActionBuilder("Text", new AreaBuilder(520,0,520,699))
            );
            $replyData = new ImagemapMessageBuilder("https://www.prosofthcm.com/upload/5934/zMqgwsQ36v.png?S=251","test",$base,$arr);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $replyData->buildMessage(),
        ]);
        break;
        case "Ur":
        $imageMapUrl = "https://lineservice.prosofthcm.com/upload/Resource/imgtest.jpg";
        $base = new BaseSizeBuilder(698,1039);
        $imgmap = array();
        $imgmap1 = array(
            new ImagemapMessageActionBuilder("Test", new AreaBuilder(0,0,35,69)),
            new ImagemapMessageActionBuilder("Test", new AreaBuilder(68,0,35,69))
        );
        $replyData = new UriTemplateActionBuilder("Imgmap","https://lineservice.prosofthcm.com/upload/Resource/imgtest.jpg");
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $replyToken,
        'messages'   => $replyData->buildTemplateAction(),
        ]);
        break;
        default:
            $messageBuilder = new TextMessageBuilder("ไม่มีคำสั่ง ".$text." นี้");
            //$StickerBuilder = new StickerMessageBuilder("1","7");
            //$StickerBuilder = new StickerMessageBuilder("2","527");
            $StickerBuilder = new StickerMessageBuilder("2","159");
            //$StickerBuilder = new StickerMessageBuilder("1","109");
            $multiMessage = new MultiMessageBuilder;
            $multiMessage->add($messageBuilder);
            $multiMessage->add($StickerBuilder);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $multiMessage->buildMessage(),
            ]);
        break;
    }
}
public function Sticker($replyToken = null)
{
    $sti = new StickerMessageBuilder("1","17");

        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $replyData->buildMessage(),
        ]);
}

public function LocationMessage($replyToken = null, $text)
{
    $split = explode(",", $text); 
    if($split[1] != null){
        $outputText = new LocationMessageBuilder("GetLocation",$split[0].",".$split[1],$split[0],$split[1]);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $outputText->buildMessage(),
        ]);
    }
    else
    {
        $messageBuilder = new TextMessageBuilder($text);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $messageBuilder->buildMessage(),
        ]);
    }
}
}
?>
