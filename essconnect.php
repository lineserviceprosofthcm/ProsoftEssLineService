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
    
    $url = "https://lineservice.prosofthcm.com/api/LanguageAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function ChangeLanguage($LineID,$Lang){
    $SetLang = "";
    if($Lang == "ภาษาไทย (Thai)"){
        $SetLang = "th-TH";
    }else{
        $SetLang = "en-US";
    }
    $url = "https://lineservice.prosofthcm.com/api/LanguageSettingAPI/".$LineID."/".$SetLang;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function LeaveRemainNum($LineID){
    
    $url = "https://lineservice.prosofthcm.com/api/LeaveRemainAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "ข้อมูลจำนวนวันลาคงเหลือ\n------------------------------";
    if($open != null){
        foreach($open as $text){
            $sum = $sum."\nประเภทการลา : ".$text['LeaveTypeName'];
            $sum = $sum."\nวันอนุญาตลา : ".$text['LeaveTypeDayNum'];
            $sum = $sum."\nจำนวนวันลา : ".$text['Days'];
            $sum = $sum."\nวันลาคงเหลือ : ".$text['Hours'];
            $sum = $sum."\n-----------------------------";
        }
    }else{
        return "ไม่พบข้อมูล";
    }
    return $sum;
}

function LeaveRemainNumEng($LineID){
    
    $url = "https://lineservice.prosofthcm.com/api/LeaveRemainAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "Leave Information Days Num\n-----------------------------";
    if($open != null){
        foreach($open as $text){
            $sum = $sum."\nLeave Type : ".$text['LeaveTypeName'];
            $sum = $sum."\nLeave Approve : ".$text['LeaveTypeDayNum'];
            $sum = $sum."\nLeave Record : ".$text['Days'];
            $sum = $sum."\nLeave Days Num : ".$text['Hours'];
            $sum = $sum."\n-----------------------------";
        }
    }else{
        return "No Data.";
    }
    return $sum;
}

function EPaySlip($LineID){
    
    $url = "https://lineservice.prosofthcm.com/api/EPaySlipAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function SendNewsTo($NewsHDID){
    
    $url = "https://lineservice.prosofthcm.com/Api/SendNewsToLineAPI/".$NewsHDID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function LocationOrganization($LineID){
    
    $url = "https://lineservice.prosofthcm.com/Api/LocationOrgAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    
    return $open;
}

function Calendar($LineID){
    $url = "https://lineservice.prosofthcm.com/APi/CalendarAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "วันหยุดองค์กร\n-----------------------------";
    $i = 0;
    if($open != null){
        foreach($open as $text){
            $sum = $sum."\n".$text['countholiday'].".".$text['Subject'];
            $sum = $sum."\n   ".$text['nameday']." ที่ ".$text['numday'];
            $sum = $sum."\n   เดือน ".$text['namemounth']." ปี ".$text['year'];
            $i = $i + 1;
        }
    }else{
        return "ไม่พบข้อมูล";
    }
    $sum = $sum."\n-----------------------------";
    $sum = $sum."\nรวมวันหยุดประจำปี ".$i." วัน";
    $sum = $sum."\n-----------------------------";
    return $sum;
}

function CalendarEng($LineID){
    $url = "https://lineservice.prosofthcm.com/APi/CalendarAPI/".$LineID;
    $open = json_decode(file_get_contents($url), true);
    $sum = "Organization Calender\n-----------------------------";
    $i = 0;
    if($open != null){
        foreach($open as $text){
            $sum = $sum."\n".$text['countholiday'].".".$text['Subject'];
            $sum = $sum."\n   Day ".$text['nameday']." At ".$text['numday'];
            $sum = $sum."\n   Mounth ".$text['namemounth']." Year ".$text['year'];
            $i = $i + 1;
        }
    }else{
        return "No data to display";
    }
    $sum = $sum."\n-----------------------------";
    $sum = $sum."\nTotal annual holiday ".$i;
    $sum = $sum."\n-----------------------------";
    return $sum;
}
?>
