<HTML>
<HEAD><TITLE> My Homepage </TITLE></HEAD>
<BODY BGCOLOR=#FFFFFF>
<?php 
  echo "Hello World"; 
  $files = glob('URL/*');
   foreach($files as $file) {
       echo substr($file,-4);
       echo str_replace("URL/,"",("str_replace(".txt","",$file)));
   }

  
?>
  
</BODY>
</HTML>
