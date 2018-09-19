<HTML>
<HEAD><TITLE> My Homepage </TITLE></HEAD>
<BODY BGCOLOR=#FFFFFF>
<?php 
  echo "Hello World"; 
  $files = glob('URL/*');
   foreach($files as $file) {
       echo substr($file,-4);
   }

  
?>
  
</BODY>
</HTML>
