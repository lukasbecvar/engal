#!/usr/bin/php
<?php // images uploader script by media path

    // include config
    require_once("config.php");

    // init Config
	$configOBJ = new Config();
    
    // clear console
    echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
        
    // call main 
    init();
    
    function init() {
        
        global $configOBJ;
            
        // input handlerer
        $inputHandlerer = fopen ("php://stdin","r");

        // basic vars init
        $mysqlIP = $configOBJ->config["ip"];
        $mysqlUser = $configOBJ->config["username"];
        $mysqlPassword = $configOBJ->config["password"];
        $imageDir;
        $galleryName;

        // get gallery name
        echo "\033[34mGallery name:\033[0m";
        $galleryName = fgets($inputHandlerer);
              
        // replase spaces
        $galleryName = str_replace(' ', '_', $galleryName);

        // check if gallery names is empty
        if (strlen($galleryName) < 2) {
            die ("\033[31mError gallery name is empty\033[0m\n");
        } else {
            echo "\033[32mGallery name set to: ".$galleryName."\033[0m";
        } 

        //Get image dir
        echo "\033[34mImage directory:\033[0m";
        $imageDir = fgets($inputHandlerer);
              
        // check if image path is empty
        if (strlen($imageDir) < 2) {
            die ("\033[31mError image directory is empty\033[0m\n");
        } else {

            // check if image dir exist
            if (!file_exists(trim($imageDir))) {
                die ("\033[31mError image directory is not exist\033[0m\n");
            } else {
                echo "\033[32mImage directory set to: ".$imageDir."\033[0m";
            }
        }  

        // strip values
        $imageDir = trim($imageDir);

        // get files from input directory
        $files = glob($imageDir."/*.*");

        // sort files
        natsort($files); 
                    
        // upload all files to database
        foreach($files as $image) {

            // get image name
            $imageName = str_replace($imageDir, '', $image);
            $imageName = str_replace(' ', '_', $imageName);
            $imageName = str_replace('/', '', $imageName);
            $imageName = pathinfo($imageName, PATHINFO_FILENAME);

            // set default image name
            if (empty($imageName)) {
                $imageName = "image";
            }

            // remove image ext if exist
            $name = preg_replace('/_.*/', '', $imageName);
            
            // get image file
            $imageFile = base64_encode(file_get_contents($image));
            
            // get upload date
            $date = date('d.m.Y H:i:s');

            // strip spaces from gallery name
            $galleryName = trim($galleryName);

            // add new row to mysql
            mysqli_query(mysqli_connect($mysqlIP, $mysqlUser, $mysqlPassword, $configOBJ->config["basedb"]), "INSERT INTO `images`(`name`, `gallery`, `upload_date`, `content`) VALUES ('$name', '$galleryName', '$date', '$imageFile')");
        
            // print msg to console
            echo "\033[32m".$imageName." : uploaded to databases\033[0m\n";
        }
    }
?>