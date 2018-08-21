<?php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// include composer autoload
require_once '../../vendor/autoload.php';
// กรณีมีการเชื่อมต่อกับฐานข้อมูล
//require_once("dbconnect.php");
 
// import the Intervention Image Manager Class
use Intervention\Image\ImageManager;  
 
if(isset($_GET['file']) && $_GET['file']!=""){
    $picFile = trim($_GET['file']);
    $originalFilePath = '../../img/'; // แก้ไขเป็นโฟลเดอร์รูปต้นฉบับ
    $fullFilePath = $originalFilePath.$picFile;
    $fullFilePathJPG = $fullFilePath.'.jpg';
    $fullFilePathPNG = $fullFilePath.'.png';
    $fullFile = '';
    $picType = '';
    if(file_exists($fullFilePathJPG)){
        $picType = 'jpg';
        $fullFile = $fullFilePath.'.'.$picType;
    }
    if(file_exists($fullFilePathPNG)){
        $picType = 'png';
        $fullFile = $fullFilePath.'.'.$picType;
    }   
    if($picType==''){
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    // สร้างตัวแปรอ้างอิง object ตัวจัดการรูปภาพ
    $manager = new ImageManager();    
    $img = $manager->make($fullFile);     
    if(isset($_GET['mode']) && $_GET['mode']=='f'){
        if(isset($_GET['width']) && $_GET['width']!="" && isset($_GET['height']) && $_GET['height']!=""){       
            $img->fit($_GET['width'], $_GET['height'], function ($constraint) {
                $constraint->upsize(); // ถ้าค่าที่กำหนดมากกว่าค่าเดิม ไม่ต้องปรับขนาด
            });
        }else{
            // only width
            if(isset($_GET['width']) && $_GET['width']!=""){
                $img->fit($_GET['width']);               
            }else{ // no width parameter
                 
            }
        }       
    }
    if(isset($_GET['mode']) && $_GET['mode']=='c'){
        if(isset($_GET['width']) && $_GET['width']!="" && isset($_GET['height']) && $_GET['height']!=""){   
            $img->crop($_GET['width'], $_GET['height']);         
        }else{
            // only width
            if(isset($_GET['width']) && $_GET['width']!=""){
                $img->crop($_GET['width'], $_GET['width']);              
            }else{ // no width parameter
                 
            }
        }       
    }   
    if(isset($_GET['mode']) && $_GET['mode']=='r'){
        if(isset($_GET['width']) && $_GET['width']!="" && isset($_GET['height']) && $_GET['height']!=""){   
            $img->resize($_GET['width'], $_GET['height']);           
        }else{
            // only width
            if(isset($_GET['width']) && $_GET['width']!=""){
                $img->resize($_GET['width'], null, function ($constraint) {
                    $constraint->aspectRatio();// ให้คงสัดส่วนของรูปภาพ
                }); 
            }else{ // no width parameter
                 
            }
        }       
    }       
    if(isset($_GET['mode']) && $_GET['mode']=='w'){
        // only width
        if(isset($_GET['width']) && $_GET['width']!=""){
            $img->widen($_GET['width'], function ($constraint) {
                $constraint->upsize(); // ถ้าค่าความกว้างที่กำหนดมากกว่าค่าเดิม ไม่ต้องปรับขนาด
            });
        }else{ // no width parameter
             
        }
    }           
    if(isset($_GET['mode']) && $_GET['mode']=='h'){
        if(isset($_GET['width']) && $_GET['width']!="" && isset($_GET['height']) && $_GET['height']!=""){       
            $img->heighten($_GET['height'], function ($constraint) {
                $constraint->upsize(); // ถ้าค่าความสูงที่กำหนดมากกว่าค่าเดิม ไม่ต้องปรับขนาด
            });
        }else{
            // only width
            if(isset($_GET['width']) && $_GET['width']!=""){
                $img->heighten($_GET['width'], function ($constraint) {
                    $constraint->upsize(); // ถ้าค่าความสูงที่กำหนดมากกว่าค่าเดิม ไม่ต้องปรับขนาด
                });             
            }else{ // no width parameter
                 
            }
        }
    }   
     
    // ส่ง HTTP header และข้อมูลของรูปเพื่อนำไปแสดง
    echo $img->response();           
}else{
    header("HTTP/1.0 404 Not Found");
    exit;   
}
?>