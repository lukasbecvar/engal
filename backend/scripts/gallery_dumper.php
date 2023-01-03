#!/usr/bin/php 
<?php // dumper (Dump all images to GalleryDump archive)

    // include config
    require_once("config.php");

    // init Config
	$configOBJ = new Config();

    // clear console
    echo chr(27).chr(91).'H'.chr(27).chr(91).'J';

    // function for random number generate
    function genNumbrGenerator($lenght) {
        $permitted_chars = "0123456789";
        $generated = substr(str_shuffle($permitted_chars), 0, $lenght);
        return $generated;
    }

    // input handlerer
    $inputHandlerer = fopen ("php://stdin","r");

    // init basic values
    $mysqlIP = $configOBJ->config["ip"];
    $mysqlUser = $configOBJ->config["username"];
    $mysqlPassword = $configOBJ->config["password"];

    // delete temp dump directory
    system("rm -rf ".escapeshellarg("dump/"));

    // check if values is not empty
    if (!empty($mysqlIP) && !empty($mysqlUser) && !empty($mysqlPassword)) {

        // get images from mysql
        $images = mysqli_query(mysqli_connect(trim($mysqlIP), trim($mysqlUser), trim($mysqlPassword), $configOBJ->config["basedb"]), "SELECT * FROM images");

        // counter default value
        $counter = 1;

        // save separate files to dump
        while ($row = mysqli_fetch_assoc($images)) {
        
            // get image data
            $id = $row["id"];
            $name =$row["name"];
            $galleryName = trim($row["gallery"]);
            $base64 = $row["content"]
        
            // make dump/images/ if not exist
            if (!file_exists('dump/images/')) {
                mkdir('dump/images/', 0777, true);
            }

            // make dump/galery name if not exist
            if (!file_exists('dump/images/'.$galleryName."/")) {
                mkdir('dump/images/'.$galleryName."/", 0777, true);
            }
            
            // create image file with random name (for duplicit files)
            if (file_exists("dump/images/".$galleryName."/".$name.".jpg")) {
                $name = $name.genNumbrGenerator(10)."_".$counter;
            }

            // put image data to file
            file_put_contents("dump/images/".$galleryName."/".$name."_".$counter.".jpg", base64_decode($base64));
            
            // print msg to console
            echo "\033[32m".$name." : saved to dump/images/".$galleryName."/".$name.".jpg\033[0m\n";
        
            // increase counter +1
            $counter++;
        } 
        
        // create GalleryDump archive
        $phar = new PharData('GalleryDump.tar.gz');

        // put dump to archie
        $phar->buildFromDirectory('dump/');

        // delete temp dump directory
        system("rm -rf ".escapeshellarg("dump/"));

    } else {

        // print mysql error
        echo "\033[31mError MysqlIP, user or password is empty, please check config.php\033[0m\n";
    }
?>