<?php
$NewsHDID = $_GET['NewsHDID'];
$News = $_GET['News'];
if(!empty($NewsHDID)){
	echo "ok";
	echo $_GET['NewsHDID']."|".$NewsHDID;
	echo $_GET['News']."|".$News;
}
echo "asdf";
$homepage = file_get_contents('https://responsive.prosoft.co.th/api/LanguageSettingAPI/U3b8800b03f1c9d49899b6fd2da70bbb6/th-TH');
?>