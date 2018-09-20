<HTML>
<HEAD><TITLE> My Homepage </TITLE></HEAD>
<BODY BGCOLOR=#FFFFFF>
<?php 
  
include('line-bot.php');
  


function show()
{
   $Link = AppLink();
   echo $Link; 
}
  
 show();
 
function AppLink()
{
   $Link = null;
   $files = glob('URL/*');
   foreach($files as $file) { $Link = str_replace("URL/","",(str_replace(".txt","",$file))); }
   return $Link;
}

?>
  
</BODY>
</HTML>
