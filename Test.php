<HTML>
<HEAD><TITLE> My Homepage </TITLE></HEAD>
<BODY BGCOLOR=#FFFFFF>
<?php 
  echo "Hello World"; 
  $files = glob('URL/*txt');
   foreach($files as $file) {
       echo "filename:".$file."<br />";
   }

  
?>
  
</BODY>
</HTML>
