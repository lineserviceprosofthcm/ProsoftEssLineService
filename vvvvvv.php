<?php 

$files = glob('URL/*');
for($i = 0; $i < count($files) ; $i++){
	echo = $files[$i];
}
echo "END CODE";

$va = $_GET['va'];

if(!empty($va)){
    echo $va;
}
?>
