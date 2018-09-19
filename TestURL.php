    <?php
        public $objOpen         = null;
        public $file            = null;
        public $LineAPIURL      = null;
    
        $objOpen = null;opendir("URL");
        $file = readdir($objOpen);
        while (($file = readdir($objOpen)) !== false){ $LineAPIURL = $file; }

        echo $LineAPIURL;

    ?>
