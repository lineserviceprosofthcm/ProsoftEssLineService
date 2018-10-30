<html>
<head>
<title>Test Line Bot</title>
</head>
<body>
<?php
	$strFileName = "/URL/lineservice.prosofthcm.com.txt";
	$objFopen = fopen($strFileName, 'r');
	$file = fgets($objFopen, 4096);
	echo $file;
?>
</body>
</html>