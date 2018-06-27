<?php

require('line-bot.php');
include('essconnect.php');

$channelSecret = '8ef9f00965d77bdfb32a4db2b31dc916';
$access_token  = 'Tzanu2luA1gLcJDW9CQVIU6uQZUJRyG0E2t8OOgO/Zbi67BVCGw0XqHnkt1JKaL5yJRt1725tpEFjXjzAYgGa8akM0GNAaILEfxdQU32ZxtH/dodtLyM77gAntZ3RFMBfJQgkOAGm5xS7OKvSj/AGwdB04t89/1O/w1cDnyilFU=';


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
//ตรวจเช็คว่า LineID นี้เคยมีการลงทะเบียนเชื่อมกับ EmpID ไว้หรือไม่//
$curMenu = CheckID($bot->userId); // ลงทะเบียนแล้วค่า $curMenu = 1 / ยังค่า $curMenu = 0 
if ($curMenu == 0) 
{    
    if($bot->text == "ลงทะเบียน" || $bot->text == "Sing up" || $bot->text == "sing up"|| $bot->text == "Singup" || $bot->text == "singup" || $bot->text == "SING UP"){
    //UpdateThisMenu ให้อยู่ในขึ้นตอนการลงทะเบียน//
    $sing = Singup($bot->userId); // สมัคไอดี เพิ่ม ###########
    $bot->SendLanguage($bot->replyToken);
    $get = UpdateThisMenu($bot->userId, 1); // เช็ต ThisMenu = 6 ใน IDline คนนั้น
    //$bot->replyMessageNew($bot->replyToken,"พิมพ์ เลือกภาษา"); 
    }
    else
    {
    $bot->bottom($bot->replyToken); // bottom แสดงตัวเลือก ลงทะเบียน/ยกเลิก
    }
}
else
{   // ขั้นตอนการสมัค 
    //Select ดู Status ว่า UserID นี้ลงทะเบียนรึยัง//
    $Status = GetStatus($bot->userId); // ส่งค่า IsStatus 0/1
    if($Status == 0)  // ยังไม่ได้ลงทะเบียน
    {
        //Select ดู menu ว่า UserID นี้กำลังอยู่ในขั้นตอนไหน// 
        $menu = GetThisMenu($bot->userId); // แสดงค่า ThisMenu //สมัค
        if ($menu > 0) 
        {
            switch ($menu) 
            {
                case 1: //เลือกภาษา
                      $result = AddLanguageline($bot->text, $bot->userId); // เช็คภาษาที่ใช้ TH/ENG
                      $bot->replyMessageNew($bot->replyToken, $result);
                      break;
                case 2: //เช็ครหัสพนักงาน
                      $result = AddIDTH($bot->text, $bot->userId); // เช็ครหัสพนักงาน ว่าเคยลงทะเบียนหรือยัง
                      $bot->replyMessageNew($bot->replyToken, $result);
                      break;
                case 3: //เช็ครหัส4ตัวท้าย
                      $result = confirmsTH($bot->text, $bot->userId); // ยืนยันด้วยเลข4ตัวท้ายบัตร ปชช Set ThisMenu = 0,IsStatus = 1
                      if($result == "1"){
                        $sp = "เลข 4 ตัวท้ายของบัตรประชาชนไม่ถูกต้อง\nกรุณาพิมพ์เลข 4 ตัวท้ายของบัตรประชาชนใหม่";
                        $bot->replyMessageNew($bot->replyToken, $sp);
                    }else{
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot
                    }
                    break;
                    case 4:
                      $result = AddIDENG($bot->text, $bot->userId); // เช็ครหัสพนักงาน ว่าเคยลงทะเบียนหรือยัง
                      $bot->replyMessageNew($bot->replyToken, $result);
                      break;
                      case 5:
                      $result = confirmsENG($bot->text, $bot->userId);// ยืนยันด้วยเลข4ตัวท้ายบัตร ปชช Set ThisMenu = 0,IsStatus = 1
                      $bot->replyMessageNew($bot->replyToken, $result);
                      break;
                case 6: //เลือกภาษา
                $bot->SendLanguage($bot->replyToken);
                      UpdateThisMenu($bot->userId, 1); // ThisMenu = 1 เสร็จสิ้นการลงทะเบียน
                      break;
                      break;
                      default:
                      break;
            }
        }
    }
    //กรณีที่ลงทะเบียนแล้ว Status = 1 จะเข้าเงื่อนไขนี้//
    else
    {
        //Check ภาษาในการตอบกลับ TH / ENG//
        $Language = GetLanguage($bot->userId); 
        if ($Language == "TH") 
        {
            //ภาษาไทย//
            //Select ดู menu ว่า UserID นี้กำลังอยู่ในขั้นตอนไหน//
            $emplo = GetThisMenu($bot->userId); // แสดง ThisMenu ว่าค่าอะไร
            if ($emplo > 0) 
            {
                switch ($emplo) 
                {
                case 1: // การลา
                if ($bot->text == "ยกเลิก" || $bot->text == "Cancel" || $bot->text == "1" || $bot->text == "Menu" 
                    || $bot->text =="เมนู") 
                {
                        UpdateThisMenu($bot->userId, 0);  // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        $result ="*ยกเลิกการทำรายการลาแล้ว";
                        //$bot->replyMessageNew($bot->replyToken, $result); 
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot

                    }elseif($bot->text == "Language" || $bot->text == "เปลี่ยนภาษา"){
                        $result ="*ยกเลิกการทำรายการลาแล้ว";
                        UpdateThisMenu($bot->userId, 5); // เชตค่า ThisMenu = 1
                        $bot->SendRELanguage($bot->replyToken, $result); // ปุ่มกดขอ Language หน้า Linebot

                    }elseif($bot->text == "Setting" || $bot->text == "ตั้งค่า"){
                        UpdateThisMenu($bot->userId, 0);
                        $result ="*ยกเลิกการทำรายการลาแล้ว";
                        $name = name($bot->userId);
                        $bot->SendSettingTH($bot->replyToken, $name, $result); // ปุ่มกดขอ Setting หน้า Linebot

                    }elseif ($bot->text == "Leave" || $bot->text == "ขอลา"){
                        $text = Checkdayleave($bot->userId);
                        $bot->SendApproved($bot->replyToken, $text);

                    }else{
                        $result = AddApp($bot->text, $bot->userId); // เช็คการลา
                        if($result == "1"){
                            $text = "*คุณทำการลาไม่ถูกต้อง\nกรุณาเลือกประเภทการลาให้ถูกต้อง หรือ กดยกเลิก";
                            $bot->SendApprovedfail($bot->replyToken, $text); 
                        }else{
                            $bot->bottomcancel($bot->replyToken, $result); 
                        }
                    }
                    
                    break;
                case 2: // เหตุผลการลา
                if ($bot->text == "ยกเลิก" || $bot->text == "Cancel" || $bot->text == "1" || $bot->text =="เมนู" 
                    || $bot->text == "Menu") 
                    {
                        UpdateThisMenu($bot->userId, 0); // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot

                    }elseif($bot->text == "Language" || $bot->text == "เปลี่ยนภาษา"){
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        UpdateThisMenu($bot->userId, 5); // เชตค่า ThisMenu = 1
                        $bot->SendRELanguage($bot->replyToken, $result); // ปุ่มกดขอ Language หน้า Linebot

                    }elseif($bot->text == "Setting" || $bot->text == "ตั้งค่า"){
                        UpdateThisMenu($bot->userId, 0);
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        $name = name($bot->userId);
                        $bot->SendSettingTH($bot->replyToken, $name, $result); // ปุ่มกดขอ Setting หน้า Linebot

                    }elseif ($bot->text == "Leave" || $bot->text == "ขอลา"){
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        $bot->SendApproved($bot->replyToken, $result);

                    }elseif($bot->text == "ย้อนกลับ"){
                        UpdateThisMenu($bot->userId, 1); // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        $result = "*ย้อนกลับ เลือกประเภทการลาใหม่";
                        $bot->SendApproved($bot->replyToken, $result);
                    }    
                    else 
                    {
                        $result = AddDetail($bot->text, $bot->userId); // กรอกเหตุผลการลา และ เช็ต ThisMenu = 3
                        $bot->bottomcancel($bot->replyToken, $result);
                    }
                         
                    break;
                case 3: // ตรวจเช๊ครหัส 4 ตัวท้าย
                if ($bot->text == "ยกเลิก" || $bot->text == "Cancel" || $bot->text == "1" || $bot->text =="เมนู" 
                    || $bot->text == "Menu")   
                    {
                        UpdateThisMenu($bot->userId, 0); // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot

                    }elseif($bot->text == "Language" || $bot->text == "เปลี่ยนภาษา"){
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        UpdateThisMenu($bot->userId, 5); // เชตค่า ThisMenu = 1
                        $bot->SendRELanguage($bot->replyToken, $result); // ปุ่มกดขอ Language หน้า Linebot



                    }elseif($bot->text == "Setting" || $bot->text == "ตั้งค่า"){
                        UpdateThisMenu($bot->userId, 0);
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        $name = name($bot->userId);
                        $bot->SendSettingTH($bot->replyToken, $name, $result); // ปุ่มกดขอ Setting หน้า Linebot

                    }elseif ($bot->text == "Leave" || $bot->text == "ขอลา"){
                        UpdateThisMenu($bot->userId, 1);
                        $result = Cancels($bot->userId); // ลบข้อมูลของ userId ใน hrTimeLeaveRecordConfirm
                        $bot->SendApproved($bot->replyToken, $result);

                    }elseif($bot->text == "ย้อนกลับ"){
                        UpdateThisMenu($bot->userId, 2); // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        $result = "ย้อนกลับ กรอกสาเหตุการลาใหม่";
                        $bot->bottomcancel($bot->replyToken, $result);
                    }  
                    else 
                    {
                        $result = confirmsapp($bot->text, $bot->userId); //กรอกรหัส4ตัว แล้ว ตวรจเช็ตรหัสกับการอนุมัติ
                        if($result == "ระบบได้ทำการ ขออนุมัติลาเรียบร้อยแล้ว")
                        {
                            //ตรวจสอบว่าเอกสารเข้าลำดับขั้นหรือไม่ ดึง IDline ของหัวหน้าเพื่อแจ้งเตือนเอกสารขออนุมัติ//
                            $results = DocumentApp($bot->userId); // ตรวจเช็ค แสดงค่า Docuno
                            $idleader = CheckLV($bot->userId); // เช็คลำดับขั้น หาผู้อนุมัติหลักและผู้อนุมัติแทน
                            $A = substr($idleader, 0, 1);
                            if($A == "A")
                            {
                                $B = substr($idleader, 1, 100);
                                $idreturn = $B;
                                $C = "";
                            }
                            else
                            {   
                                $B = substr($idleader, 1, 100);
                                /*หาชื่อผู้อนุมัติแทน*/
                                $Dep = Deputize($B);
                                $C = "โดยมี ".$Dep." เป็นผู้อนุมัติแทน";
                                $idreturn = Grant($bot->userId);
                                $idreturns = $B;
                            }
                            $bot->home($bot->replyToken, $result);
                        }
                        else
                        {
                            if($result == "1"){
                                $text = "เลข 4 ตัวท้ายไม่ถูกต้อง\nกรุณาพิมพ์เลข 4 ตัวท้ายใหม่\nหรือ ยกเลิก";
                                $bot->bottomcancel($bot->replyToken, $text); 
                            }else{
                                $bot->home($bot->replyToken, $result);
                            }
                        }                 
                    }
                    //$bot->replyMessageNew($bot->replyToken, $result);
                    // //แจ้งเตือนถึงหัวหน้าว่ามีเอกสารขออนุมัติ//
                    $bot->sendMessageNew($idreturn,"มีเอกสารขออนุมัติ " .$results.$C);
                    $bot->sendMessageNew($idreturns,"มีเอกสารขออนุมัติ " .$results);
                    break;
                case 5: // เปลี่ยนภาษา
                if ($bot->text == "ยกเลิก" || $bot->text == "Cancel" || $bot->text == "1" || $bot->text =="เมนู" 
                    || $bot->text == "Menu") 
                    {
                    UpdateThisMenu($bot->userId, 0);
                    $result ="*ยกเลิกการทำรายการเปลี่ยนภาษาแล้ว";
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot

                    }elseif($bot->text == "Setting" || $bot->text == "ตั้งค่า"){
                        UpdateThisMenu($bot->userId, 0);
                        $result ="*ยกเลิกการทำรายการเปลี่ยนภาษาแล้ว";
                        $name = name($bot->userId);
                        $bot->SendSettingTH($bot->replyToken, $name, $result); // ปุ่มกดขอ Setting หน้า Linebot

                    }elseif ($bot->text == "Leave" || $bot->text == "ขอลา"){
                        UpdateThisMenu($bot->userId, 1);
                        $result ="*ยกเลิกการทำรายการเปลี่ยนภาษาแล้ว";
                        $bot->SendApproved($bot->replyToken, $result);

                    } 
                    else 
                    {
                        if($bot->text == "ไทย" || $bot->text == "TH"){  // เพิ่ม #################
                            $botn = "TH";
                        }else if ($bot->text == "อังกฤษ" || $bot->text == "ENG") {
                            $botn = "ENG";
                        }

                        if(/*$bot->text == "TH" || $bot->text == "ENG" || */ $botn == "TH" || $botn == "ENG")
                        {
                            $result = reLanguage($botn, $bot->userId); // เปลี่ยนภาษา แล้ว set ThisMenu = '0'
                            $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot
                        }
                        else if($bot->text == "Language" || $bot->text == "เปลี่ยนภาษา")
                        {
                            $bot->SendRELanguage($bot->replyToken); // แสดงเมนูเลือกภาษาอีกครั้ง
                        }
                        else
                        {
                            $result = "*เลือกภาษาไม่ถูกต้อง";
                            $bot->SendRELanguage($bot->replyToken, $result); 
                        }
                    }
                    break;
                case 6: // ลบ ID
                if ($bot->text == "ยกเลิก" || $bot->text == "Cancel" || $bot->text == "1" || $bot->text =="เมนู" 
                    || $bot->text == "Menu")  
                {
                        UpdateThisMenu($bot->userId, 0); // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        $result = "*ยกเลิกการทำรายการลบทะเบียนแล้ว";
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot

                    }elseif($bot->text == "Language" || $bot->text == "เปลี่ยนภาษา"){
                        $result = "*ยกเลิกการทำรายการลบทะเบียนแล้ว";
                        UpdateThisMenu($bot->userId, 5); // เชตค่า ThisMenu = 1
                        $bot->SendRELanguage($bot->replyToken, $result); // ปุ่มกดขอ Language หน้า Linebot

                    }elseif($bot->text == "Setting" || $bot->text == "ตั้งค่า"){
                        UpdateThisMenu($bot->userId, 0);
                        $result = "*ยกเลิกการทำรายการลบทะเบียนแล้ว";
                        $name = name($bot->userId);
                        $bot->SendSettingTH($bot->replyToken, $name, $result); // ปุ่มกดขอ Setting หน้า Linebot

                    }elseif ($bot->text == "Leave" || $bot->text == "ขอลา"){
                        UpdateThisMenu($bot->userId, 1);
                        $result = "*ยกเลิกการทำรายการลบทะเบียนแล้ว";
                        $bot->SendApproved($bot->replyToken, $result);

                    }
                    else 
                    {
                        $result = Unregister($bot->text, $bot->userId); // กรอกรหัส4ตัวยืนยันก่อนลบ ID
                        $bot->home($bot->replyToken, $result); // เพิ่ม
                    }
                    $bot->replyMessageNew($bot->replyToken, $result);
                    break;
                case 7: // การอนุมัด
                if ($bot->text == "ยกเลิก" || $bot->text == "Cancel" || $bot->text == "1" || $bot->text =="เมนู" 
                    || $bot->text == "Menu") 
                    {
                        UpdateThisMenu($bot->userId, 0); // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot

                    }elseif($bot->text == "Language" || $bot->text == "เปลี่ยนภาษา"){
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        UpdateThisMenu($bot->userId, 5); // เชตค่า ThisMenu = 1
                        $bot->SendRELanguage($bot->replyToken, $result); // ปุ่มกดขอ Language หน้า Linebot

                    }elseif($bot->text == "Setting" || $bot->text == "ตั้งค่า"){
                        UpdateThisMenu($bot->userId, 0);
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        $name = name($bot->userId);
                        $bot->SendSettingTH($bot->replyToken, $name, $result); // ปุ่มกดขอ Setting หน้า Linebot

                    }elseif ($bot->text == "Leave" || $bot->text == "ขอลา"){
                        UpdateThisMenu($bot->userId, 1);
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        $bot->SendApproved($bot->replyToken, $result);

                    }
                    else 
                    {
                        $A = substr($bot->text,0, 3);
                        if($A == "LEV")
                        {
                            UpdateThisMenu($bot->userId, 8); // เชตค่า ThisMenu = 8 
                            $bot->TESTApproveds($bot->replyToken,$bot->text); // การตอบรับการลา Y/N
                        }
                        else
                        {
                            $result = "คุณทำรายการไม่ถูกต้อง กรุณาเลือกเอกสารที่ต้องการอนุมัติ";
                        }

                    } 
                    $bot->home($bot->replyToken, $result);

                    break; 
                case 8: // การอนุมัติ / แทน
                if ($bot->text == "ยกเลิก" || $bot->text == "Cancel" || $bot->text == "1" || $bot->text =="เมนู" 
                    || $bot->text == "Menu")   
                    {   
                        UpdateThisMenu($bot->userId, 0); // ยกเลิกแล้ว เชตค่า ThisMenu = 0
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot

                    }elseif($bot->text == "Language" || $bot->text == "เปลี่ยนภาษา"){
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        UpdateThisMenu($bot->userId, 5); // เชตค่า ThisMenu = 1
                        $bot->SendRELanguage($bot->replyToken, $result); // ปุ่มกดขอ Language หน้า Linebot

                    }elseif($bot->text == "Setting" || $bot->text == "ตั้งค่า"){
                        UpdateThisMenu($bot->userId, 0);
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        $name = name($bot->userId);
                        $bot->SendSettingTH($bot->replyToken, $name, $result); // ปุ่มกดขอ Setting หน้า Linebot

                    }elseif ($bot->text == "Leave" || $bot->text == "ขอลา"){
                        UpdateThisMenu($bot->userId, 1);
                        $result = "*ยกเลิกการทำรายการขออนุมัติแล้ว";
                        $bot->SendApproved($bot->replyToken, $result);

                    }
                    else 
                    {
                        $A = substr($bot->text, 0, 1); 
                        if($A == "Y" || $A == "N")
                        {
                            $emp = CheckEmp($bot->text); // แสดง UserID
                            $mng = CheckMNGDeputi($bot->text, $bot->userId); //แสดง UserID คนอนุมัติแทน
                            $Deps = Deputize($bot->userId); // แสดงผู้ที่อนุมัติแทน

                            $result = MNGApprovedDocument($bot->text, $bot->userId); // เช็คการอนุมัติ
                            if($result == "อนุมัติเอกสารเรียบร้อยแล้ว")
                            {
                                $results1 = DocumentEmp($bot->text); // เหตุผลการลา
                                $results2 = " ได้รับการอนุมัติแล้ว";
                                $results3 = "ได้รับการอนุมัติโดยมี".$Deps."เป็นผู้อนุมัติแทน";
                            }
                            else
                            {
                                $results1 = DocumentEmp($bot->text); // เหตุผลการลา
                                $results2 = " ไม่ได้รับการอนุมัติ";
                                $results3 = "ถูกยกเลิกโดยมี ".$Deps."เป็นผู้อนุมัติแทน";

                            }
                            
                        }
                        else
                        {
                            $result = "คุณทำรายการไม่ถูกต้อง";  
                        }

                    }
                    $bot->replyMessageNew($bot->replyToken, $result);
                    $bot->sendMessageNew($emp,"เอกสารหมายเลข ".$results1.$results2);
                    $bot->sendMessageNew($mng,"เอกสารหมายเลข ".$results1.$results3);

                    break;           
                    break;
                    default:
                    //code......
                    break;
                }
            }
            else // ทุกการยกเลิกจะ set ThisMenu = 0
            {
                //โค้ดส่วนเมนูพวกขอลา//
                if ($bot->text == "เมนู" || $bot->text == "Menu") 
                {
                    $bot->SendTemplate($bot->replyToken); // ปุ่มกดขอ เมนู หน้า Linebot
                } 
                elseif ($bot->text == "ขอลา" || $bot->text == "Leave") 
                {
                    $text = Checkdayleave($bot->userId);
                    //$bot->replyMessageNew($bot->replyToken, $text);
                    $bot->SendApproved($bot->replyToken, $text); // ปุ่มกดขอ ลา หน้า Linebot
                    UpdateThisMenu($bot->userId, 1); // เชตค่า ThisMenu = 1
                } 
                elseif ($bot->text == "ตั้งค่า" || $bot->text == "Setting") 
                {
                    $name = name($bot->userId);
                    $bot->SendSettingTH($bot->replyToken, $name); // ปุ่มกดขอ Setting หน้า Linebot
                } 
                elseif ($bot->text == "เปลี่ยนภาษา" || $bot->text == "Language") 
                {
                    UpdateThisMenu($bot->userId, 5); // เชตค่า ThisMenu = 1
                    $bot->SendRELanguage($bot->replyToken); // ปุ่มกดขอ Language หน้า Linebot
                }
                elseif($bot->text == "ลงชื่อเข้างาน")
                {
                    $bot->Location($bot->replyToken);
                } 
                elseif ($bot->text == "Approved") 
                {
                    /*เช็คสิทธิการเข้าถึงว่าสามารถใช้ฟังชั่นนี้ได้หรือไม่*/
                    $MSG = CheckMNG($bot->userId); // ตรวจสอบ UserID ว่ามีสิทธิ์ใช้งานหรือไม่
                    if($MSG > 0)
                    {
                        /*เช็คว่ามีเอกสารขออนุมัติหรือไม่*/
                        $CDApp = CheckDocApp($bot->userId);
                        if($CDApp > 0)
                        {
                            //ตรวจสอบการถูกตั้งเป็นผู้อนุมัติแทน
                            $Deputi = CheckDeputi($bot->userId); // ตรวจว่ามีเอกสารขอลาหรือไม่
                            if($Deputi > 0)
                            {
                                $bot->MNGApprovedDeputi($bot->replyToken,$bot->userId); // อนุมัติลาจากหัวหน้า
                                UpdateThisMenu($bot->userId, 7); // เชตค่า ThisMenu = 7
                            }
                            else
                            {                                   
                                $bot->MNGApproved($bot->replyToken,$bot->userId); // ตัวแทนอนุมัติลา
                                UpdateThisMenu($bot->userId, 7); // เชตค่า ThisMenu = 7
                            }
                        }
                        else
                        {
                            $bot->home($bot->replyToken, "ไม่มีเอกสารขออนุมัติ");
                        }
                    }
                    else
                    {
                        $bot->home($bot->replyToken, "คุณไม่มีสิทธิ์เข้าถึง");
                    } 
                } 
                elseif ($bot->text == "ยกเลิกการลงทะเบียน" || $bot->text == "Unregister") 
                {
                    UpdateThisMenu($bot->userId, 6); // เชตค่า ThisMenu = 6
                    $bot->cancelUnregister($bot->replyToken, "กรุณาพิมพ์เลข 4 ตัวเพื่อยืนยันตัวตน\nกดยกเลิกเพื่อยกเลิกรายการ");
                }
                elseif ($bot->text == "ขอลา/อนุมัติเอกสาร") 
                {
                    $bot->leave_appro($bot->replyToken); // ปุ่มกดขอ Setting หน้า Linebot
                }
                elseif ($bot->text == "กลับเมนูหลัก") 
                {
                    $bot->SendTemplate($bot->replyToken); // ปุ่มกดขอ Setting หน้า Linebot
                }  
                else 
                {
                    $result = "*ไม่มีรายการคำสั่งนี้\nกรุณาเลือกทำรายการใหม่";
                    $bot->SendTemplate($bot->replyToken, $result); // ปุ่มกดขอ เมนู หน้า Linebot
                    //$bot->replyMessageNew($bot->replyToken, "ไม่มีรายการคำสั่งนี้");
                }
            }
        }

        //------------------------------------------ จบ ของ ไทย -----------------------------------------------------
        
        else//โค้ดอิ้งเก็บไว้ก่อนนะ
        {
            //ENG//
            //Select ดู menu ว่า UserID นี้กำลังอยู่ในขั้นตอนไหน//
            $emplo = GetThisMenu($bot->userId);
            if ($emplo > 0) 
            {
                switch ($emplo) 
                {
                    case 1:
                    if ($bot->text == "Cancel") 
                    {
                        UpdateThisMenu($bot->userId, 0);
                        $result ="Canceled transaction";
                    }
                    elseif ($bot->text == "Leave") 
                    {
                        $bot->SendApprovedENG($bot->replyToken);
                    }  
                    else 
                    {
                        $result = AddAppENG($bot->text, $bot->userId);
                    }
                    $bot->replyMessageNew($bot->replyToken, $result);
                    break;
                    case 2:
                    if ($bot->text == "Cancel") 
                    {
                        UpdateThisMenu($bot->userId, 0);
                        $result = CancelsENG($bot->userId);
                    } 
                    else 
                    {
                        $result = AddDetailENG($bot->text, $bot->userId);
                    }
                    $bot->replyMessageNew($bot->replyToken, $result);
                    break;
                    case 3:
                    if ($bot->text == "Cancel") 
                    {
                        UpdateThisMenu($bot->userId, 0);
                        $result = CancelsENG($bot->userId);
                    } 
                    else 
                    {
                        $result = confirmsappENG($bot->text, $bot->userId);
                        if($result == "The system has already been approved")
                        {
                            //ตรวจสอบว่าเอกสารเข้าลำดับขั้นหรือไม่ ดึง IDline ของหัวหน้าเพื่อแจ้งเตือนเอกสารขออนุมัติ//
                            $results = DocumentAppENG($bot->userId);
                            $idleader = CheckLVENG($bot->userId);
                            $A = substr($idleader, 0, 1);
                            if($A == "A")
                            {
                                $B = substr($idleader, 1, 100);
                                $idreturn = $B;
                                $C = "";
                            }
                            else
                            {   
                                $B = substr($idleader, 1, 100);
                                /*หาชื่อผู้อนุมัติแทน*/
                                $Dep = DeputizeENG($B);
                                $C = $Dep." Be approved";
                                $idreturn = GrantENG($bot->userId);
                                $idreturns = $B;
                            }

                            
                        }
                        
                    }
                    $bot->replyMessageNew($bot->replyToken, $result);
                        // //แจ้งเตือนถึงหัวหน้าว่ามีเอกสารขออนุมัติ//
                    $bot->sendMessageNew($idreturn,"Request for approval " .$results.$C);
                    $bot->sendMessageNew($idreturns,"Request for approval " .$results);
                    break;
                    case 5:
                    if ($bot->text == "Cancel") 
                    {
                        UpdateThisMenu($bot->userId, 0);
                        $result = CancelsENG($bot->userId);
                    } 
                    else 
                    {
                        if($bot->text == "TH" || $bot->text == "ENG")
                        {
                            $result = reLanguage($bot->text, $bot->userId);
                        }
                        else if($bot->text == "Language")
                        {
                            $bot->SendRELanguage($bot->replyToken);
                        }
                        else
                        {
                            $result = "Please select language.";
                        }
                    }
                    $bot->replyMessageNew($bot->replyToken, $result);  
                    break;
                    case 6:
                    if ($bot->text == "Cancel") 
                    {
                        UpdateThisMenu($bot->userId, 0);
                        $result = "Canceled transaction";
                    } 
                    else 
                    {
                        $result = UnregisterENG($bot->text, $bot->userId);
                    }
                    $bot->replyMessageNew($bot->replyToken, $result);
                    break;
                    case 7:
                    if ($bot->text == "Cancel") 
                    {
                        UpdateThisMenu($bot->userId, 0);
                        $result = "Canceled transaction";
                    } 
                    else 
                    {
                        $A = substr($bot->text,0, 3);
                        if($A = "LEV")
                        {
                            UpdateThisMenu($bot->userId, 8);
                            $bot->TESTApproveds($bot->replyToken,$bot->text); 
                        }
                        else
                        {
                            $result = "You made a mistake.\nPlease select the document that you want to approve.";
                        }
                    } 

                    $bot->replyMessageNew($bot->replyToken, $result);

                    break; 
                    case 8:
                    if ($bot->text == "Cancel") 
                    {
                        UpdateThisMenu($bot->userId, 0);
                        $result = "Canceled transaction";
                    } 
                    else 
                    {
                        $A = substr($bot->text, 0, 1);
                        if($A == "Y" || $A == "N")
                        {
                            $emp = CheckEmpENG($bot->text);
                            $mng = CheckMNGDeputi($bot->text, $bot->userId);
                            $Deps = Deputize($bot->userId);

                            $result = MNGApprovedDocumentENG($bot->text, $bot->userId);
                            if($result == "Document approved successfully")
                            {
                                $results1 = DocumentEmpENG($bot->text);
                                $results2 = " Approved";
                                $results3 = "Approved by".$Deps;
                            }
                            else
                            {
                                $results1 = DocumentEmpENG($bot->text);
                                $results2 = " Not approved";
                                $results3 = "Canceled by".$Deps;
                            }
                        }
                        else
                        {
                            $result = "You made a mistake.";  
                        }
                    }
                    $bot->replyMessageNew($bot->replyToken, $result);
                    $bot->sendMessageNew($emp,"Document Number ".$results1.$results2);
                    $bot->sendMessageNew($mng,"Document Number ".$results1.$results3);

                    break;           
                    break;
                    default:
                    //code......
                    break;

                }
            }
            else
            {
                //โค้ดส่วนเมนูพวกขอลา//
                if ($bot->text == "Menu") 
                {
                    $bot->SendTemplateENG($bot->replyToken);
                } 
                elseif ($bot->text == "Leave") 
                {
                    $bot->SendApprovedENG($bot->replyToken);
                    UpdateThisMenu($bot->userId, 1);
                } 
                elseif ($bot->text == "Setting") 
                {
                    $bot->SendSettingENG($bot->replyToken);
                } 
                elseif ($bot->text == "Language") 
                {
                    $bot->SendRELanguage($bot->replyToken);
                    UpdateThisMenu($bot->userId, 5);
                } 
                elseif ($bot->text == "Approved") 
                {
                    /*เช็คสิทธิการเข้าถึงว่าสามารถใช้ฟังชั่นนี้ได้หรือไม่*/
                    $MSG = CheckMNGENG($bot->userId);
                    if($MSG > 0)
                    {
                        /*เช็คว่ามีเอกสารขออนุมัติหรือไม่*/
                        $CDApp = CheckDocApp($bot->userId);
                        if($CDApp > 0)
                        {
                                //ตรวจสอบการถูกตั้งเป็นผู้อนุมัติแทน
                            $Deputi = CheckDeputi($bot->userId);
                            if($Deputi > 0)
                            {
                                $bot->MNGApprovedDeputiENG($bot->replyToken,$bot->userId);
                                UpdateThisMenu($bot->userId, 7);
                            }
                            else
                            {                                   
                                $bot->MNGApprovedENG($bot->replyToken,$bot->userId);
                                UpdateThisMenu($bot->userId, 7);
                            }
                        }
                        else
                        {
                            $bot->replyMessageNew($bot->replyToken, "No approval document");
                        }
                        
                    }
                    else
                    {
                        $bot->replyMessageNew($bot->replyToken, "You do not have access");
                    }
                    
                } 
                elseif ($bot->text == "Unregister") 
                {
                    UpdateThisMenu($bot->userId, 6);
                    $bot->replyMessageNew($bot->replyToken, "Please enter the last 4 digits of your ID to verify your identity. \nPlease cancel the printout");
                } 
                else 
                {
                    $bot->replyMessageNew($bot->replyToken, "You made a mistake. Please select a new one");
                }
            }
        }
    }
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