<?php

require('line-bot.php');
include('essconnect.php');

$channelSecret = '53580e5121a5cf757d4ea3342b91b2da';
$access_token  = 'HnYiosVXrVsSbH35XHeQCkTgPn6Pa3shrsh+K7RJ8RIPF8hpXr4IBm40k/4B2lmr1mIRRl+JFwcohXq7JIXSmOOoBl2UhUoaMEGuRaD5uQ1kXURzsh2vwjY58D1/RPO523ZweZArgtN8XaHy5eZJvQdB04t89/1O/w1cDnyilFU=';


$bot = new BOT_API($channelSecret, $access_token);
$idnews = $_POST['txtNews'];
$DocID = $_POST['txtDoc'];

//ถูกเรียกใช้กรรณีมีการอนุมัติหรือไม่อนุมัติ เอกสารขอนุมัติ//
if(!empty($DocID)){

  $str = Doc($DocID);
  $id = SendUserDocID($DocID);
  $bot->sendMessageNew($id,$str);
}

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
    $curMenu = CheckID($bot->userId);
    if ($curMenu == 0) 
    {
        //UpdateThisMenu ให้อยู่ในขึ้นตอนการลงทะเบียน//
        $get = UpdateThisMenu($bot->userId, 6);
        $bot->replyMessageNew($bot->replyToken,"คุณยังไม่ได้ทำการลงทะเบียน กรุณาพิมพ์ ลงทะเบียน\n\nYou have not yet registered,please enter sing up");
    }
    else
    {
        //Select ดู Status ว่า UserID นี้ลงทะเบียนรึยัง//
        $Status = GetStatus($bot->userId);
        if($Status == 0)
        {
            
            //Select ดู menu ว่า UserID นี้กำลังอยู่ในขั้นตอนไหน//
            $menu = GetThisMenu($bot->userId);
            if ($menu > 0) 
            {
                switch ($menu) 
                {
                    case 1:
                          $result = AddLanguageline($bot->text, $bot->userId);
                          $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 2:
                          $result = AddIDTH($bot->text, $bot->userId);
                          $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 3:
                          $result = confirmsTH($bot->text, $bot->userId);
                          $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 4:
                          $result = AddIDENG($bot->text, $bot->userId);
                          $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 5:
                          $result = confirmsENG($bot->text, $bot->userId);
                          $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 6:
                          $bot->SendLanguage($bot->replyToken);
                          UpdateThisMenu($bot->userId, 1);
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
            //Check ภาษาในการตอบกลับ//
            $Language = GetLanguage($bot->userId);
            if ($Language == 0) 
            {
                //ภาษาไทย//
                //Select ดู menu ว่า UserID นี้กำลังอยู่ในขั้นตอนไหน//
                $emplo = GetThisMenu($bot->userId);
                if ($emplo > 0) 
                {
                    switch ($emplo) 
                    {
                    case 1:
                        if ($bot->text == "ยกเลิก") 
                        {
                            UpdateThisMenu($bot->userId, 0);
                            $result ="ยกเลิกการทำรายการแล้ว";
                        } 
                        else 
                        {
                            $result = AddApp($bot->text, $bot->userId);
                        }
                            $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 2:
                        if ($bot->text == "ยกเลิก") 
                        {
                            UpdateThisMenu($bot->userId, 0);
                            $result = Cancels($bot->userId);
                        } 
                        else 
                        {
                            $result = AddDetail($bot->text, $bot->userId);
                        }
                            $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 3:
                        if ($bot->text == "ยกเลิก") 
                        {
                            UpdateThisMenu($bot->userId, 0);
                            $result = Cancels($bot->userId);
                        } 
                        else 
                        {
                            $result = confirmsapp($bot->text, $bot->userId);
                            if($result == "ระบบได้ทำการ ขออนุมัติลาเรียบร้อยแล้ว")
                            {
                                //ตรวจสอบว่าเอกสารเข้าลำดับขั้นหรือไม่ ดึง IDline ของหัวหน้าเพื่อแจ้งเตือนเอกสารขออนุมัติ//
                                $results = DocumentApp($bot->userId);
                                $idleader = CheckLV($bot->userId);
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

                                
                            }
                            
                        }
                            $bot->replyMessageNew($bot->replyToken, $result);
                            // //แจ้งเตือนถึงหัวหน้าว่ามีเอกสารขออนุมัติ//
                            $bot->sendMessageNew($idreturn,"มีเอกสารขออนุมัติ " .$results.$C);
                            $bot->sendMessageNew($idreturns,"มีเอกสารขออนุมัติ " .$results);
                          break;
                    case 5:
                        if ($bot->text == "ยกเลิก") 
                        {
                            UpdateThisMenu($bot->userId, 0);
                            $result = Cancels($bot->userId);
                        } 
                        else 
                        {
                            $result = reLanguage($bot->text, $bot->userId);
                        }
                            $bot->replyMessageNew($bot->replyToken, $result);  
                          break;
                    case 6:
                        if ($bot->text == "ยกเลิก") 
                        {
                            UpdateThisMenu($bot->userId, 0);
                            $result = "ยกเลิกการทำรายการแล้ว";
                        } 
                        else 
                        {
                            $result = Unregister($bot->text, $bot->userId);
                        }
                            $bot->replyMessageNew($bot->replyToken, $result);
                          break;
                    case 7:
                        if ($bot->text == "ยกเลิก") 
                        {
                            UpdateThisMenu($bot->userId, 0);
                            $result = Cancels($bot->userId);
                        } 
                        else 
                        {
                            UpdateThisMenu($bot->userId, 8);
                            $result = MNGShowDocument($bot->text, $bot->userId);
                        } 
                            $bot->Approved($bot->replyToken,$bot->text); 
                            $bot->replyMessageNew($bot->replyToken, $result);
                            
                          break; 
                    case 8:
                        if ($bot->text == "ยกเลิก") 
                        {
                            UpdateThisMenu($bot->userId, 0);
                            $result = "ยกเลิกการทำรายการแล้ว";
                        } 
                        else 
                        {
                            $emp = CheckEmp($bot->text);
                            $result = MNGApprovedDocument($bot->text, $bot->userId);
                            if($result == "อนุมัติเอกสารเรียบร้อยแล้ว")
                            {
                                $results2 = " ได้รับการอนุมัติแล้ว";
                                $results1 = DocumentEmp($bot->text);
                            }
                            else
                            {
                                $results2 = " ไม่ได้รับการอนุมัติ";
                                $results1 = DocumentEmp($bot->text);
                            }
                            
                        }
                            $bot->replyMessageNew($bot->replyToken, $result);
                            $bot->sendMessageNew($emp,"เอกสารหมายเลข ".$results1.$results2);
                            
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
                    if ($bot->text == "เมนู" || $bot->text == "Menu") 
                    {
                        $bot->SendTemplate($bot->replyToken);
                    } 
                    elseif ($bot->text == "ขอลา" || $bot->text == "Leave") 
                    {
                        $bot->SendApproved($bot->replyToken);
                        UpdateThisMenu($bot->userId, 1);
                    } 
                    elseif ($bot->text == "Setting") 
                    {
                        $bot->SendSettingTH($bot->replyToken);
                    } 
                    elseif ($bot->text == "Language") 
                    {
                        $bot->SendRELanguage($bot->replyToken);
                        UpdateThisMenu($bot->userId, 5);
                    } 
                    // elseif ($bot->text == "Date") 
                    // {
                    //     $bot->Approved($bot->replyToken,$bot->text); 
                    // } 
                    elseif ($bot->text == "Approved") 
                    {
                        /*เช็คสิทธิการเข้าถึงว่าสามารถใช้ฟังชั่นนี้ได้หรือไม่*/
                        $MSG = CheckMNG($bot->userId);
                        if($MSG > 0)
                        {
                            /*เช็คว่ามีเอกสารขออนุมัติหรือไม่*/
                            $CDApp = CheckDocApp($bot->userId);
                            if($CDApp > 0)
                            {
                                    $bot->MNGApproved($bot->replyToken,$bot->userId);
                                    UpdateThisMenu($bot->userId, 7);
                            }
                            else
                            {
                                    $bot->replyMessageNew($bot->replyToken, "ไม่มีเอกสารขออนุมัติ");
                            }
                            
                        }
                        else
                        {
                            $bot->replyMessageNew($bot->replyToken, "คุณไม่มีสิทธิ์เข้าถึง");
                        }
                        
                    } 
                    elseif ($bot->text == "ยกเลิกการลงทะเบียน" || $bot->text == "Unregister") 
                    {
                        UpdateThisMenu($bot->userId, 6);
                        $bot->replyMessageNew($bot->replyToken, "กรุณาพิมพ์เลข 4 ตัวท้ายของรหัสบัตรประชาชนเพื่อยืนยันตัวตน\nต้องการยกเลิกการทำรายการพิมพ์ ยกเลิก");
                    } 
                    else 
                    {
                        $bot->replyMessageNew($bot->replyToken, "คุณทำรายการผิดพลาด กรุณาเลือกทำรายการใหม่");
                    }
                }
            }
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
                            $result = reLanguageENG($bot->text, $bot->userId);
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
                            $result = CancelsENG($bot->userId);
                        } 
                        else 
                        {
                            UpdateThisMenu($bot->userId, 8);
                            $result = MNGShowDocumentENG($bot->text, $bot->userId);
                        } 
                            $bot->ApprovedENG($bot->replyToken,$bot->text); 
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
                            $emp = CheckEmpENG($bot->text);
                            $result = MNGApprovedDocumentENG($bot->text, $bot->userId);
                            if($result == "Document approved successfully")
                            {
                                $results2 = " Approved";
                                $results1 = DocumentEmpENG($bot->text);
                            }
                            else
                            {
                                $results2 = " Not approved";
                                $results1 = DocumentEmpENG($bot->text);
                            }
                            
                        }
                            $bot->replyMessageNew($bot->replyToken, $result);
                            $bot->sendMessageNew($emp,"Document Number ".$results1.$results2);
                            
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
                    if ($bot->text == "เมนู" || $bot->text == "Menu") 
                    {
                        $bot->SendTemplateENG($bot->replyToken);
                    } 
                    elseif ($bot->text == "ขอลา" || $bot->text == "Leave") 
                    {
                        $bot->SendApprovedENG($bot->replyToken);
                        UpdateThisMenu($bot->userId, 1);
                    } 
                    elseif ($bot->text == "Setting") 
                    {
                        $bot->SendSettingTH($bot->replyToken);
                    } 
                    elseif ($bot->text == "Language") 
                    {
                        $bot->SendRELanguage($bot->replyToken);
                        UpdateThisMenu($bot->userId, 5);
                    } 
                    // elseif ($bot->text == "Date") 
                    // {
                    //     $bot->Approved($bot->replyToken,$bot->text); 
                    // } 
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
                                    $bot->MNGApprovedENG($bot->replyToken,$bot->userId);
                                    UpdateThisMenu($bot->userId, 7);
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
                    elseif ($bot->text == "ยกเลิกการลงทะเบียน" || $bot->text == "Unregister") 
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
