<HTML>
<HEAD><TITLE> My Homepage </TITLE></HEAD>
<BODY BGCOLOR=#FFFFFF>
<?php 
  

function square()
{
   $Link = null;
   $files = glob('URL/*');
   foreach($files as $file) { $Link = str_replace("URL/","",(str_replace(".txt","",$file))); }
   return $Link;
}
echo square();   

?>
  
</BODY>
</HTML>
