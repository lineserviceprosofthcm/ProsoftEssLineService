<?php
$NewsHDID = $_GET['NewsHDID'];
$News = $_GET['News'];
if(!empty($NewsHDID)){
	echo "ok";
	echo $_GET['NewsHDID']."|".$NewsHDID;
	echo $_GET['News']."|".$News;
}
echo "asdf";
?>