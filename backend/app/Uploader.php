<?php // upload component

    class Uploader {

        // upload new image to images table with data form post request
        public function imageUploadFromClient() {
             
            global $mysql;
            global $mainController;

            // headers
            header('Access-Control-Allow-Origin: *');

            // check if request is post
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                // get data from post & escape
                $name = $_POST["name"];
                $gallery = $_POST["gallery"];
                $content = $_POST["content"];

                // init default values
                $upload_date = date('d.m.Y H:i:s');

                // check if values empty
                if (empty($name)) {

                    // log action to mysql
                    $mysql->log("Image upload", "error: POST value name is not defined");

                    // print error
                    die("Error(14): POST value name is not defined");
                } elseif (empty($gallery)) {

                    // log action to mysql
                    $mysql->log("Image upload", "error: POST value gallery is not defined");

                    // print error
                    die("Error(14): POST value gallery is not defined");
                } elseif (empty($content)) {

                    // log action to mysql
                    $mysql->log("Image upload", "error: POST value content is not defined");

                    // print error msg
                    die("Error(14): POST value content is not defined");
                } else {
                    
                    // create storage dir 
                    if (!file_exists("../storage/")) { 
                        @mkdir("../storage/"); 
                    } 
                    
                    // create image dir
                    if (!file_exists("../storage/images/")) { 
                        @mkdir("../storage/images/"); 
                    } 
                    
                    // generate image identifier
                    $identifier = $mainController->getRandomString(10)."_".time();
                    
                    // put file with content to file system
                    file_put_contents("../storage/images/".$identifier, $content);

                    // add new row to mysql
                    $mysql->insert("INSERT INTO `images`(`name`, `gallery`, `upload_date`, `identifier`) VALUES ('$name', '$gallery', '$upload_date', '$identifier')");
                
                    // log action to mysql
                    $mysql->log("Image upload", "Image ".$identifier." uploaded");
                }
            } else {

                // log action to mysql
                $mysql->log("Image upload", "error: POST - Request required!");
            
                // print error
                die("Error(13): POST - Request required!");
            }
        }
    }
?>