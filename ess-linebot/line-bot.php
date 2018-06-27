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
        $connectstr_dbhost = '31.170.166.134';
        $connectstr_dbname = 'u663869224_line';
        $connectstr_dbusername = 'u663869224_hrmi';
        $connectstr_dbpassword = 'v06dt22ssn';
    
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
        $img_url = "https://www.prosofthcm.com/upload/5934/eo3hrcpDoM.png";
        for ($i=0;$i<1;$i++) {
            $actions = array(
        new MessageTemplateActionBuilder("ภาษาไทย", "TH"),
        new MessageTemplateActionBuilder("English", "ENG"),
      );
            $column = new CarouselColumnTemplateBuilder("Language", "กรุณาเลือกภาษาที่ใช้แสดง\nPlease select a display language.", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Setting Language", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
              'replyToken' => $replyToken,
              'messages'   => $outputText->buildMessage(),
          ]);
    }

    public function SendRELanguage($replyToken = null)
    {
        $columns = array();
        $img_url = "https://www.prosofthcm.com/upload/5934/eo3hrcpDoM.png";
        for ($i=0;$i<1;$i++) {
            $actions = array(
          new MessageTemplateActionBuilder("TH(ภาษาไทย)", "TH"),
          new MessageTemplateActionBuilder("ENG(อังกฤษ)", "ENG"),
          new MessageTemplateActionBuilder("Cancel(ยกเลิก)", "Cancel(ยกเลิก)"),
        );
            $column = new CarouselColumnTemplateBuilder("Language", "กรุณาเลือกภาษาทต้องการเปลี่ยน\nPlease select a display language.", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Setting Language", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
                'replyToken' => $replyToken,
                'messages'   => $outputText->buildMessage(),
            ]);
    }

   
///////////////////////////////////////////////*โต้ตอบด้วยเทมเพตภาษาไทย*/////////////////////////////////////////////////
///////////////////////////////////////////////*โต้ตอบด้วยเทมเพตภาษาไทย*/////////////////////////////////////////////////

    public function SendSettingTH($replyToken = null)
    {
        $columns = array();
        $img_url = "https://www.prosofthcm.com/upload/5934/67m2YbOk6S.jpg";
        for ($i=0;$i<1;$i++) {
            $actions = array(
            new MessageTemplateActionBuilder("เปลี่ยนภาษา", "Language"),
            new MessageTemplateActionBuilder("ยกเลิกการลงทะเบียน", "Unregister"),
        );
            $column = new CarouselColumnTemplateBuilder("ตั้งค่า", "กรุณาเลือกรายการที่ต้องการ", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Setting Language", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
                'replyToken' => $replyToken,
                'messages'   => $outputText->buildMessage(),
            ]);
    }
    
    public function SendTemplate($replyToken = null)
    {
        $actions = array(
        new MessageTemplateActionBuilder("ขออนุมัติลา", "ขอลา"),
        new MessageTemplateActionBuilder("อนุมัติเอกสาร", "Approved"),
        new MessageTemplateActionBuilder("เปลี่ยนภาษา", "Language")

    );
        $img_url = "https://www.prosofthcm.com/upload/5934/HDIVJszBfE.jpg";
        $button = new ButtonTemplateBuilder("เมนู", "กรุณาเลือกรายการที่ต้องการ", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Menu", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
    }

    public function TESTApproveds($replyToken = null,$text)
    {
        $link = ConnectDatabase();
        $sql = "SELECT hrTimeLeaveRecord.Docuno,hrTimeLeaveRecord.DocuDate,hrTimeLeaveRecord.LeaveRemark,emPerson.Title,
        emPerson.FirstName,emPerson.LastName 
        FROM hrTimeLeaveRecord ,emEmployee,emPerson 
        WHERE hrTimeLeaveRecord.EmpID = emEmployee.EmpID 
        AND hrTimeLeaveRecord.Docuno = '".$text."'
        AND emPerson.PersonID = emEmployee.PersonID";
        $result = mysqli_query($link, $sql);
        $str = "";
        if (mysqli_num_rows($result) > 0)
        {
            while($row = mysqli_fetch_assoc($result))
            {
                  $B = substr($row['DocuDate'],0, 10);
                  $str = "วันที่ ".$B." ".$row['Title']." ".$row['FirstName']." สาเหตุการลา ".$row['LeaveRemark'];
            } 
        }
        
        $actions = array(
            new MessageTemplateActionBuilder("อนุมัติ", "Y".$text.""),
            new MessageTemplateActionBuilder("ไม่อนุมัติ", "N".$text.""),
            new MessageTemplateActionBuilder("ยกเลิก", "ยกเลิก")

         );

        $img_url = "https://www.prosofthcm.com/upload/5934/5d1apZw0Oh.jpg";
        $button = new ButtonTemplateBuilder("รายละเอียดการลา", $str , $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Approved", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
      $link->close();
    }

    public function MNGApprovedDeputi($replyToken = null,$userId)
    {
        $link = ConnectDatabase();
        $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
        $sqls = "SELECT emGrantApprove.GrantApproveID AS GrantID 
                FROM emGrantApprove,emLineuserconnect 
                WHERE emGrantApprove.ApproverID = emLineuserconnect.EmpID
                AND emLineuserconnect.UserID = '".$userId."' ";
                $results = mysqli_query($link, $sqls);
                if (mysqli_num_rows($results) > 0) 
                {
                    while($row = mysqli_fetch_assoc($results)) 
                    {
                        $sql1 = "SELECT Record.Docuno 
                                FROM hrTimeLeaveRecord AS Record , emGrantApprove AS GrantApp
                                LEFT JOIN emDeputizeApprove AS Deputi ON Deputi.DeputizeGrantApproveID = GrantApp.GrantApproveID
                                AND Deputi.IsDeleted =0 
                                AND Deputi.MenuID = '".$MenuID."'
                                AND CONVERT(Deputi.StartDate,DATE) <= CONVERT(curdate(),DATE)            
                                AND CONVERT(Deputi.EndDate,DATE) >= CONVERT(curdate(),DATE)
                                WHERE  Record.ApproveStatus = 'W' 
                                AND Record.IsDeleted = 0
                                AND Deputi.DeputizeGrantApproveID = '".$row['GrantID']."'
                                AND GrantApp.MenuID =  '".$MenuID."'
                                GROUP BY Record.Docuno";
                                $result1 = mysqli_query($link, $sql1);
                                $arr = [];
                                if (mysqli_num_rows($result1) > 0)
                                {
                                    while($row1 = mysqli_fetch_assoc($result1))
                                    {
                                        array_push($arr,new MessageTemplateActionBuilder("".$row1['Docuno']."", "".$row1['Docuno']."")); 
                                            
                                    }
                                }
                    }
                }

         $actions = $arr;

    $img_url = "https://www.prosofthcm.com/upload/5934/tIn6U0zMf6.jpg";
        $button = new ButtonTemplateBuilder("เอกสารขออนุมัติลา", "กรุณาเลือกเอกสารขออนุมัติลา", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Document No", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
      $link->close();
    }

    public function MNGApproved($replyToken = null,$userId)
    {
        $link = ConnectDatabase();
        $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c'; //?????????????????????????????????
        $sqls = "SELECT emGrantApprove.GrantApproveID AS GrantID 
                FROM emGrantApprove,emLineuserconnect 
                WHERE emGrantApprove.ApproverID = emLineuserconnect.EmpID
                AND emLineuserconnect.UserID = '".$userId."' ";
                $results = mysqli_query($link, $sqls);
                if (mysqli_num_rows($results) > 0) 
                {
                    while($row = mysqli_fetch_assoc($results)) 
                    {
                        $sql1 = "SELECT Record.Docuno
                                FROM hrTimeLeaveRecord AS Record 
                                LEFT JOIN emEmpApproved AS empApp ON empApp.EmpID = Record.EmpID
                                LEFT JOIN emGrantApprove AS empGrant ON empGrant.GrantApproveID = empApp.GrantApproveID
                                WHERE empApp.GrantApproveID = '".$row['GrantID']."'
                                AND Record.ApproveStatus = 'W'";
                                $result1 = mysqli_query($link, $sql1);
                                $arr = [];
                                if (mysqli_num_rows($result1) > 0)
                                {
                                    while($row1 = mysqli_fetch_assoc($result1))
                                    {
                                        array_push($arr,new MessageTemplateActionBuilder("".$row1['Docuno']."", "".$row1['Docuno'].""));     
                                    }
                                }
                    }
                }
         $actions = $arr;

    $img_url = "https://www.prosofthcm.com/upload/5934/tIn6U0zMf6.jpg";
        $button = new ButtonTemplateBuilder("เอกสารขออนุมัติลา", "กรุณาเลือกเอกสารขออนุมัติลา", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Document No", $button);
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
    $img_url = "https://www.prosofthcm.com/upload/5934/tIn6U0zMf6.jpg";
        $button = new ButtonTemplateBuilder("ประเภทการลา", "กรุณาเลือกรายการที่ต้องการ", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Type Approved", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
    }

///////////////////////////////////////////////*สิ้นสุดการโต้ตอบด้วยภาษาไทย*/////////////////////////////////////////////////
///////////////////////////////////////////////*สิ้นสุดการโต้ตอบด้วยภาษาไทย*/////////////////////////////////////////////////

////////////////////////////////////////////////*โต้ตอบด้วยเทมเพต ENG*////////////////////////////////////////////////////
////////////////////////////////////////////////*โต้ตอบด้วยเทมเพต ENG*////////////////////////////////////////////////////

    public function SendSettingENG($replyToken = null)
    {
        $columns = array();
        $img_url = "https://www.prosofthcm.com/upload/5934/67m2YbOk6S.jpg";
        for ($i=0;$i<1;$i++) {
            $actions = array(
            new MessageTemplateActionBuilder("Change Language", "Language"),
            new MessageTemplateActionBuilder("UnCancel Registrationregister", "Unregister"),
        );
            $column = new CarouselColumnTemplateBuilder("Setting Language", "Please select the items you want.", $img_url, $actions);
            $columns[] = $column;
        }
        $carousel = new CarouselTemplateBuilder($columns);
        $outputText = new TemplateMessageBuilder("Setting", $carousel);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
                'replyToken' => $replyToken,
                'messages'   => $outputText->buildMessage(),
            ]);
    }

    public function SendTemplateENG($replyToken = null)
    {
        $actions = array(
       new MessageTemplateActionBuilder("Leave", "Leave"),
       new MessageTemplateActionBuilder("Approved", "Approved"),
       new MessageTemplateActionBuilder("Setting Language", "Language")
   );
        $img_url = "https://www.prosofthcm.com/upload/5934/HDIVJszBfE.jpg";
        $button = new ButtonTemplateBuilder("Menu", "Please select the items you want.", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Menu", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
         'replyToken' => $replyToken,
         'messages'   => $outputText->buildMessage(),
     ]);
    }

    public function ApprovedENG($replyToken = null,$text)
    {
        $link = ConnectDatabase();
        $sql = "SELECT hrTimeLeaveRecord.Docuno,hrTimeLeaveRecord.DocuDate,hrTimeLeaveRecord.LeaveRemark,emPerson.Title,
        emPerson.FirstName,emPerson.LastName 
        FROM hrTimeLeaveRecord ,emEmployee,emPerson 
        WHERE hrTimeLeaveRecord.EmpID = emEmployee.EmpID 
        AND hrTimeLeaveRecord.Docuno = '".$text."' 
        AND emPerson.PersonID = emEmployee.PersonID";
        $result = mysqli_query($link, $sql);
        $str = "";
        if (mysqli_num_rows($result) > 0)
        {
            while($row = mysqli_fetch_assoc($result))
            {
                  $B = substr($row['DocuDate'],0, 10);
                  $str = "Date ".$B." ".$row['Title']." ".$row['FirstName']." Remake ".$row['LeaveRemark'];
            } 
        }
        
        $actions = array(
            new MessageTemplateActionBuilder("Approved", "Y".$text.""),
            new MessageTemplateActionBuilder("Not Approved", "N".$text.""),
            new MessageTemplateActionBuilder("Cancel", "Cancel")

         );

        $img_url = "https://www.prosofthcm.com/upload/5934/5d1apZw0Oh.jpg";
        $button = new ButtonTemplateBuilder("lease select an item", $str , $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Approved", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
      $link->close();
    }


    public function MNGApprovedDeputiENG($replyToken = null,$userId)
    {
        $link = ConnectDatabase();
        $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
        $sqls = "SELECT emGrantApprove.GrantApproveID AS GrantID 
                FROM emGrantApprove,emLineuserconnect 
                WHERE emGrantApprove.ApproverID = emLineuserconnect.EmpID
                AND emLineuserconnect.UserID = '".$userId."' ";
                $results = mysqli_query($link, $sqls);
                if (mysqli_num_rows($results) > 0) 
                {
                    while($row = mysqli_fetch_assoc($results)) 
                    {
                        $sql1 = "SELECT Record.Docuno 
                                FROM hrTimeLeaveRecord AS Record , emGrantApprove AS GrantApp
                                LEFT JOIN emDeputizeApprove AS Deputi ON Deputi.DeputizeGrantApproveID = GrantApp.GrantApproveID
                                AND Deputi.IsDeleted =0 
                                AND Deputi.MenuID = '".$MenuID."'
                                AND CONVERT(Deputi.StartDate,DATE) <= CONVERT(curdate(),DATE)            
                                AND CONVERT(Deputi.EndDate,DATE) >= CONVERT(curdate(),DATE)
                                WHERE  Record.ApproveStatus = 'W' 
                                AND Record.IsDeleted = 0
                                AND Deputi.DeputizeGrantApproveID = '".$row['GrantID']."'
                                AND GrantApp.MenuID =  '".$MenuID."'
                                GROUP BY Record.Docuno";
                                $result1 = mysqli_query($link, $sql1);
                                $arr = [];
                                if (mysqli_num_rows($result1) > 0)
                                {
                                    while($row1 = mysqli_fetch_assoc($result1))
                                    {
                                        array_push($arr,new MessageTemplateActionBuilder("".$row1['Docuno']."", "".$row1['Docuno']."")); 
                                            
                                    }
                                }
                    }
                }

         $actions = $arr;

    $img_url = "https://www.prosofthcm.com/upload/5934/tIn6U0zMf6.jpg";
        $button = new ButtonTemplateBuilder("เอกสารขออนุมัติลา", "กรุณาเลือกเอกสารขออนุมัติลา", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Document No", $button);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
          'replyToken' => $replyToken,
          'messages'   => $outputText->buildMessage(),
      ]);
      $link->close();
    }


    public function MNGApprovedENG($replyToken = null,$userId)
    {
        $link = ConnectDatabase();
        $MenuID = 'c12ee3e6-56e5-4824-a5e3-147add72ce3c';
        $sqls = "SELECT emGrantApprove.GrantApproveID AS GrantID 
                FROM emGrantApprove,emLineuserconnect 
                WHERE emGrantApprove.ApproverID = emLineuserconnect.EmpID
                AND emLineuserconnect.UserID = '".$userId."' ";
                $results = mysqli_query($link, $sqls);
                if (mysqli_num_rows($results) > 0) 
                {
                    while($row = mysqli_fetch_assoc($results)) 
                    {
                        $sql1 = "SELECT Record.Docuno
                                FROM hrTimeLeaveRecord AS Record 
                                LEFT JOIN emEmpApproved AS empApp ON empApp.EmpID = Record.EmpID
                                LEFT JOIN emGrantApprove AS empGrant ON empGrant.GrantApproveID = empApp.GrantApproveID
                                WHERE empApp.GrantApproveID = '".$row['GrantID']."'
                                AND Record.ApproveStatus = 'W'";
                                $result1 = mysqli_query($link, $sql1);
                                $arr = [];
                                if (mysqli_num_rows($result1) > 0)
                                {
                                    while($row1 = mysqli_fetch_assoc($result1))
                                    {
                                        array_push($arr,new MessageTemplateActionBuilder("".$row1['Docuno']."", "".$row1['Docuno']."")); 
                                            
                                    }
                                }
                    }
                }

     $actions = $arr;

    $img_url = "https://www.prosofthcm.com/upload/5934/tIn6U0zMf6.jpg";
        $button = new ButtonTemplateBuilder("Document on leave", "Please select an item.", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Document No", $button);
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
    $img_url = "https://www.prosofthcm.com/upload/5934/tIn6U0zMf6.jpg";
        $button = new ButtonTemplateBuilder("Type of leave", "Please select an item.", $img_url, $actions);
        $outputText = new TemplateMessageBuilder("Type Approved", $button);
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
