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

/*echo file_get_contents("URL/LineAPI.txt");*/

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
    public $TextURL         = null;

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

        if (!empty($events['events'])) {
            $this->isEvents = true;
            $this->events   = $events['events'];

            foreach ($events['events'] as $event) {
                $this->replyToken = $event['replyToken'];
                $this->source     = (object) $event['source'];
                $this->message    = (object) $event['message'];
                $this->timestamp  = $event['timestamp'];
                $this->userId     = $event['source']['userId'];
                
                $files = glob('URL/*');
                foreach($files as $file) { 
                $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
                
                /*$this->TextURL    = file_get_contents("URL/LineAPI.txt");*/
                /*$this->TextURL    = "prosoft.gotdns.com/ESS/";*/
                
                    
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
    
    $files = glob('URL/*');
    foreach($files as $file) { 
    $TextURL_App    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    
    $actions = array(
    New UriTemplateActionBuilder("Go To Approve", "https://".$TextURL_App."/Lineservice/approveleave/approveleaveinfo/".$ToLineID));
    $img_url = "https://www.prosofthcm.com/upload/5934/zwLbACxL0c.jpg";
    $button  = new ButtonTemplateBuilder("Notice Approval", $message, $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Notice Approval", $button);
    
    $multiMessage = new MultiMessageBuilder;
    $multiMessage->add($outputText);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
        'to' => $ToLineID,
        'messages'  => $multiMessage->buildMessage()
    ]);
}
    
public function SendMessageApproveAbstainTo($ToLineID = null, $message = null){
    
    $files = glob('URL/*');
    foreach($files as $file) { 
    $TextURL_App    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    
    $actions = array(
    New UriTemplateActionBuilder("Go To Approve", "https://".$TextURL_App."/Lineservice/ApproveAbstain/ApproveAbstaininfo/".$ToLineID));
    $img_url = "https://www.prosofthcm.com/upload/5934/zwLbACxL0c.jpg";
    $button  = new ButtonTemplateBuilder("Notice Approval", $message, $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Notice Approval", $button);
    
    $multiMessage = new MultiMessageBuilder;
    $multiMessage->add($outputText);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
        'to' => $ToLineID,
        'messages'  => $multiMessage->buildMessage()
    ]);
}

public function SendMessageToEmpRequest($ToLineID = null, $message = null){
    
    $files = glob('URL/*');
    foreach($files as $file) { 
    $TextURL_App    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    
    $actions = array(
    New UriTemplateActionBuilder("View Description", "https://".$TextURL_App."/LineService/LeaveRequest/LeaveRequestList/".$ToLineID));
    $img_url = "https://www.prosofthcm.com/upload/5934/zwLbACxL0c.jpg";
    $button  = new ButtonTemplateBuilder("Notice Approval", $message, $img_url, $actions);
    $outputText = new TemplateMessageBuilder("View Description", $button);
    
    $multiMessage = new MultiMessageBuilder;
    $multiMessage->add($outputText);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
        'to' => $ToLineID,
        'messages'  => $multiMessage->buildMessage()
    ]);
}
    
public function SendMessageToEmpRequestAbstain($ToLineID = null, $message = null){
    
    $files = glob('URL/*');
    foreach($files as $file) { 
    $TextURL_App    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    
    $actions = array(
    New UriTemplateActionBuilder(" ", ""));
    $img_url = "https://www.prosofthcm.com/upload/5934/zwLbACxL0c.jpg";
    $button  = new ButtonTemplateBuilder("Notice Approval", $message, $img_url, $actions);
    $outputText = new TemplateMessageBuilder("View Description", $button);
    
    $multiMessage = new MultiMessageBuilder;
    $multiMessage->add($outputText);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
        'to' => $ToLineID,
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
        New UriTemplateActionBuilder("ลงทะเบียน", "https://".$this->TextURL."/LineService/Register/RegisterInfo/".$LineID),
        New MessageTemplateActionBuilder("ย้อนกลับ", "ย้อนกลับ")
    );
    $button  = new ConfirmTemplateBuilder("ลงทะเบียนใช้งาน\nYou have not yet registered" , $actions);
    $outputText = new TemplateMessageBuilder("ลงทะเบียนใช้งาน", $button);
    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
}

public function ApproveCenter()
{
    $actions = array(
        New UriTemplateActionBuilder("ขออนุมัติลา", "https://".$this->TextURL."/LineService/LeaveRequest/LeaveRequestInfo/".$this->userId),
        New UriTemplateActionBuilder("ขอยกเว้นรูดบัตร", "https://".$this->TextURL."/LineService/AbstainTime/AbstainTimeInfo/".$this->userId),
        New UriTemplateActionBuilder("อนุมัติเอกสารลา", "https://".$this->TextURL."/LineService/ApproveLeave/ApproveLeaveInfo/".$this->userId),
        New UriTemplateActionBuilder("อนุมัติยกเว้นรูดบัตร", "https://".$this->TextURL."/LineService/ApproveAbstain/ApproveAbstainInfo/".$this->userId)
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/BEQPPo7iiF.jpg";
    $button  = new ButtonTemplateBuilder("Approve Center", "สำหรับขอ/อนุมัติเอกสารต่าง ๆ...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Approve Center", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function ApproveCenterEng()
{
    $actions = array(
        New UriTemplateActionBuilder("Leave Request", "https://".$this->TextURL."/LineService/LeaveRequest/LeaveRequestInfo/".$this->userId),
        New UriTemplateActionBuilder("Abstain Time", "https://".$this->TextURL."/LineService/AbstainTime/AbstainTimeInfo/".$this->userId),
        New UriTemplateActionBuilder("Approve Leave", "https://".$this->TextURL."/LineService/ApproveLeave/ApproveLeaveInfo/".$this->userId),
        New UriTemplateActionBuilder("Approve Abstain", "https://".$this->TextURL."/LineService/ApproveAbstain/ApproveAbstainInfo/".$this->userId)
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/BEQPPo7iiF.jpg";
    $button  = new ButtonTemplateBuilder("Approve Center", "For request or approve documents...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Approve Center", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function TimeAttendance()
{
    $actions = array(
        New UriTemplateActionBuilder("ลงเวลาเข้างาน", "https://".$this->TextURL."/LineService/TimeStamp/TimeStampInfo/".$this->userId),
        New UriTemplateActionBuilder("ข้อมูลเวลาทำงาน", "https://".$this->TextURL."/LineService/WorkTime/WorkTimeInfo/".$this->userId),
        New MessageTemplateActionBuilder("สิทธิ์การลา/วันลาคงเหลือ", "สิทธิ์การลา/วันลาคงเหลือ"),
        New UriTemplateActionBuilder("ข้อมูลการขอลา", "https://".$this->TextURL."/LineService/LeaveRequest/LeaveRequestList/".$this->userId)
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/4XNG8W47Yn.jpg";
    $button  = new ButtonTemplateBuilder("Time Attendence", "สำหรับจัดการข้อมูลเวลาการทำงาน...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Time Attendence", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function TimeAttendanceEng()
{
    $actions = array(
        New UriTemplateActionBuilder("Time Stamp", "https://".$this->TextURL."/LineService/TimeStamp/TimeStampInfo/".$this->userId),
        New UriTemplateActionBuilder("Work Time Detail", "https://".$this->TextURL."/LineService/WorkTime/WorkTimeInfo/".$this->userId),
        New MessageTemplateActionBuilder("Leave Remain", "Leave Remain"),
        New UriTemplateActionBuilder("Leave Information", "https://".$this->TextURL."/LineService/LeaveRequest/LeaveRequestList/".$this->userId)
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/4XNG8W47Yn.jpg";
    $button  = new ButtonTemplateBuilder("Time Attendence", "For manage work time data...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Time Attendence", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function Payroll()
{
    $actions = array(
        New MessageTemplateActionBuilder("ขอสลิปเงินเดือน", "ขอสลิปเงินเดือน"),
        New MessageTemplateActionBuilder("ขอเอกสาร 50 ทวิ", "ขอเอกสาร 50 ทวิ"),
        New MessageTemplateActionBuilder("ขอใบรับรองการทำงาน", "ขอใบรับรองการทำงาน"),
        New MessageTemplateActionBuilder("ขอเอกสารรับรองเงินเดือน", "ขอเอกสารรับรองเงินเดือน")
        
         );

    $img_url = "https://www.prosofthcm.com/upload/5934/CGD9pX8Q9X.jpg";
    $button  = new ButtonTemplateBuilder("Payroll", "สำหรับจัดการข้อมูลเงินเดือน...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Payroll", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}
    
public function PayrollEng()
{
    $actions = array(
        New MessageTemplateActionBuilder("E-Pay Slip", "E-Pay Slip"),
        New MessageTemplateActionBuilder("50 Bis Request", "50 Bis Request"),
        New MessageTemplateActionBuilder("Works Cer. Request", "Works Cer. Request"),
        New MessageTemplateActionBuilder("Salary Cer. Request", "Salary Cer. Request")
        
         );

    $img_url = "https://www.prosofthcm.com/upload/5934/CGD9pX8Q9X.jpg";
    $button  = new ButtonTemplateBuilder("Payroll", "For manage your salary data...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Payroll", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function Organization()
{
    $actions = array(
        New MessageTemplateActionBuilder("วันหยุดองค์กร", "วันหยุดองค์กร"),
        New UriTemplateActionBuilder("สร้างข่าวสารองค์กร", "https://".$this->TextURL."/LineService/News/NewsInfo/".$this->userId),
        New UriTemplateActionBuilder("ข้อมูลข่าวสาร", "https://".$this->TextURL."/LineService/News/NewsList/".$this->userId),
        New MessageTemplateActionBuilder("ที่ตั้งองค์กร", "ที่ตั้งองค์กร")
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/VFrLXsJrey.jpg";
    $button  = new ButtonTemplateBuilder("Organization", "สำหรับดูข้อมูลเกี่ยวกับองค์กร...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Organization", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function OrganizationEng()
{
    $actions = array(
        New MessageTemplateActionBuilder("Calendar", "Organization Calendar"),
        New UriTemplateActionBuilder("Create News", "https://".$this->TextURL."/LineService/News/NewsInfo/".$this->userId),
        New UriTemplateActionBuilder("News List", "https://".$this->TextURL."/LineService/News/NewsList/".$this->userId),
        New MessageTemplateActionBuilder("Location", "Location of Organization")
        );    

    $img_url = "https://www.prosofthcm.com/upload/5934/VFrLXsJrey.jpg";
    $button  = new ButtonTemplateBuilder("Organization", "For view about organization data...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Organization", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
    
}

public function Setting()
{
    $actions = array(        
        New UriTemplateActionBuilder("ลงทะเบียน", "https://".$this->TextURL."/LineService/Register/RegisterInfo/".$this->userId),
        New MessageTemplateActionBuilder("เปลี่ยนภาษา", "เปลี่ยนภาษา")
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/3dHoTCaSmu.jpg";
    $button  = new ButtonTemplateBuilder("Setting", "สำหรับตั้งค่าการใช้งานระบบ...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Setting", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}

public function SettingEng()
{
    $actions = array(        
        New UriTemplateActionBuilder("Register", "https://".$this->TextURL."/LineService/Register/RegisterInfo/".$this->userId),
        New MessageTemplateActionBuilder("Language", "Language")
        );

    $img_url = "https://www.prosofthcm.com/upload/5934/3dHoTCaSmu.jpg";
    $button  = new ButtonTemplateBuilder("Setting", "For setting the system...", $img_url, $actions);
    $outputText = new TemplateMessageBuilder("Setting", $button);

    $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
        'replyToken' => $this->replyToken,
        'messages'   => $outputText->buildMessage(),
    ]);
}
public function photoQR($replyToken = null)
{
//$outputText = new ImageMessageBuilder("https://".$this->TextURL."/upload/Resource/Linebot.png", "https://".$this->TextURL."/upload/Resource/Linebot.png");
$outputText = new ImageMessageBuilder("https://prosoftesslineservice.herokuapp.com/img/QR-ProsoftLine.png", "https://prosoftesslineservice.herokuapp.com/img/QR-ProsoftLine.png");
$this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
    'replyToken' => $replyToken,
    'messages'   => $outputText->buildMessage(),
]);
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
            $messageBuilder = new TextMessageBuilder("ไม่มีคำสั่ง ".$text." นี้");
            //$StickerBuilder = new StickerMessageBuilder("1","7");
            //$StickerBuilder = new StickerMessageBuilder("2","527");
            $StickerBuilder = new StickerMessageBuilder("2","109");
            //$StickerBuilder = new StickerMessageBuilder("1","109");
            $multiMessage = new MultiMessageBuilder;
            $multiMessage->add($messageBuilder);
            $multiMessage->add($StickerBuilder);
            $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $multiMessage->buildMessage(),
            ]);

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
