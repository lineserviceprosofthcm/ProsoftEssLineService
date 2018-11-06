<?php 
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

function GetLanguage($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    
    $url = "https://".$TextURL."/api/LanguageAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function ChangeLanguage($LineID,$Lang){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    
    $SetLang = "";
    if($Lang == "ภาษาไทย (Thai)"){
        $SetLang = "th-TH";
    }else{
        $SetLang = "en-US";
    }
    $url = "https://".$TextURL."/api/LanguageSettingAPI/".$LineID."/".$SetLang;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function LeaveRemainNum($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/api/LeaveRemainAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "";
    if($open != null){
        $sum = "ข้อมูลจำนวนวันลาคงเหลือ\n------------------------------";
        foreach($open as $text){
            if($text['LeaveTypeName'] == "ชื่อผู้ใช้ของคุณ ยังไม่ได้ลงทะเบียน" || $text['LeaveTypeName'] == "User not register"){
                $sum = $text['LeaveTypeName'];
            }else{
                $sum = $sum."\nประเภทการลา : ".$text['LeaveTypeName'];
                $sum = $sum."\nวันอนุญาตลา : ".$text['LeaveTypeDayNum'];
                $sum = $sum."\nจำนวนวันลา : ".$text['Days'];
                $sum = $sum."\nวันลาคงเหลือ : ".$text['Hours'];
                $sum = $sum."\n-----------------------------";
            }
        }
    }else{
        return "ไม่พบข้อมูล";
    }
    return $sum;
}

function LeaveRemainNumEng($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/api/LeaveRemainAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "";
    if($open != null){
        $sum = "Leave Information Days Num\n-----------------------------";
        foreach($open as $text){
            if($text['LeaveTypeName'] == "ชื่อผู้ใช้ของคุณ ยังไม่ได้ลงทะเบียน" || $text['LeaveTypeName'] == "User not register"){
                $sum = $text['LeaveTypeName'];
            }else{
                $sum = $sum."\nLeave Type : ".$text['LeaveTypeName'];
                $sum = $sum."\nLeave Approve : ".$text['LeaveTypeDayNum'];
                $sum = $sum."\nLeave Record : ".$text['Days'];
                $sum = $sum."\nLeave Days Num : ".$text['Hours'];
                $sum = $sum."\n-----------------------------";
            }
        }
    }else{
        return "No Data.";
    }
    return $sum;
}

function EPaySlip($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/api/EPaySlipAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function Withholdingtaxcertificate($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/api/TaxCert_RequestAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $textsp = explode(",",$open);

    return $textsp;
}

function SalaryCert($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/api/SalaryCert_RequestAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $textsp = explode(",",$open);

    return $textsp;
}

function WorkCert($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/api/WorkCert_RequestAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $textsp = explode(",",$open);

    return $textsp;
}

function SendNewsTo($NewsHDID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/Api/SendNewsToLineAPI/".$NewsHDID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function LocationOrganization($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/Api/LocationOrgAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function Calendar($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/APi/CalendarAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "";
    $ischeck = true;
    $i = 0;
    if($open != null){
        $sum = "วันหยุดองค์กร\n-----------------------------";
        foreach($open as $text){
            if($text['headcalendar'] == "ชื่อผู้ใช้ของคุณ ยังไม่ได้ลงทะเบียน" || $text['headcalendar'] == "Please register to use system."){
                $sum = $text['headcalendar'];
                $ischeck = false;
            }else if($text['headcalendar'] == "ปฎิทินของคุณไม่ได้กำหนดวัน" || $text['headcalendar'] == "Your calendar is not set the holiday."){
                $sum = $text['headcalendar'];
                $ischeck = false;
            }else{
                $sum = $sum."\n".$text['countholiday'].".".$text['nameday']." ".$text['numday'];
                $sum = $sum."\n".": ".$text['Subject'];
                $i = $i + 1;
                $sum = $sum."\n-----------------------------";
            }
        }
        if($ischeck){
            $sum = $sum."\nรวมวันหยุดประจำปี ".$i." วัน";
            $sum = $sum."\n-----------------------------";
        }
    }else{
        return "ไม่พบข้อมูล";
    }
    return $sum;
}

function CalendarEng($LineID){
    $files = glob('URL/*');
    foreach($files as $file) { 
    $this->TextURL    = str_replace("_","/",(str_replace("URL/","",(str_replace(".txt","",$file)))));}
    $url = "https://".$TextURL."/APi/CalendarAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "";
    $ischeck = true;
    $i = 0;
    if($open != null){
        $sum = "Organization Calendar\n-----------------------------";
        foreach($open as $text){
            if($text['headcalendar'] == "ชื่อผู้ใช้ของคุณ ยังไม่ได้ลงทะเบียน" || $text['headcalendar'] == "Please register to use system."){
                $sum = $text['headcalendar'];
                $ischeck = false;
            }else if($text['headcalendar'] == "ปฎิทินของคุณไม่ได้กำหนดวัน" || $text['headcalendar'] == "Your calendar is not set the holiday."){
                $sum = $text['headcalendar'];
                $ischeck = false;
            }else{
                $sum = $sum."\n".$text['countholiday'].".".$text['nameday']." ".$text['numday'];
                $sum = $sum."\n".": ".$text['Subject'];
                $i = $i + 1;
                $sum = $sum."\n-----------------------------";
            }
        }
        if($ischeck){
            $sum = $sum."\nTotal annual holiday ".$i;
            $sum = $sum."\n-----------------------------";
        }
    }else{
        return "No data to display";
    }
    return $sum;
}
?>
