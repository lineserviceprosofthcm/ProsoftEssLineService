<HTML>
<HEAD><TITLE> My Homepage </TITLE></HEAD>
<BODY BGCOLOR=#FFFFFF>
<?php 
  echo "Hello World"; 
  $files = glob('URL/*');
   foreach($files as $file) {
       echo $file;
   }

  
?>
  
</BODY>
</HTML>
