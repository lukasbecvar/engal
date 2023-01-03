<?php // API response builder functions

    class Responses {

        // status response
        public function status() {

            global $mainController;
            global $config;
            global $mysql;

            // build array to print
            $status = [
                "status" => "running",
                "api_version" => $config->config["version"],
                "maintenance" => $config->config["maintenance"],
                "dev_mode" => $config->config["dev_mode"],
                "encoding" => $config->config["encoding"],
                "action" => $mainController->getAPIAction(),
            ];

            // log action to mysql
            $mysql->log("Response", "returned status");

            // print array in json
            echo json_encode($status);   
        }

        // get media counter
        public function counter() {

            global $mediaController;
            global $mysql;

            // build array to print
            $counters = [
                "images" => $mediaController->getImagesCount(),
                "galleries" => $mediaController->getGalleryCount()
            ];

            // log action to mysql
            $mysql->log("Response", "returned counter");

            // print array in json
            echo json_encode($counters);              
        }

        // get all images IDs
        public function getAllImagesIDs() {

            global $mediaController;
            global $mysql;

            // get IDs array
            $ids = $mediaController->getIDsArray();

            // log action to mysql
            $mysql->log("Response", "returned all images ids");
            
            // print array in json
            echo json_encode($ids);   
        }

        // get images IDs by gallery name
        public function getIDSByGalleryName() {

            global $mediaController;
            global $mainController;
            global $mysql;

            // get gallery name from query string (URL)
            $galleryName = $mainController->getGalleryName();

            // check if seted galleryName query string
            if ($galleryName == null) {

                // log action to mysql
                $mysql->log("Response", "returned none for undefined galleryName GET.");

                // print error msg
                die("Error(10): undefined galleryName GET.");
            } else {
                
                // get all images ids list
                if ($galleryName == "allhPC12fR0u") {
                    $ids = $mediaController->getIDsArray("allhPC12fR0u");
                
                // get all images ids with random sort
                } else if ($galleryName == "randomImagess2WH92Aww") {
                    $ids = $mediaController->getIDsArray("randomImagess2WH92Aww");
                
                // get images list by gallery name
                } else {
                    $ids = $mediaController->getIDsArray($galleryName);
                }

                // check if ids found
                if (!empty($ids)) {

                    // log action to mysql
                    $mysql->log("Response", "returned images ids by gallery:".$galleryName);

                    // print array in json
                    echo json_encode($ids);   
                } else {

                    // log action to mysql
                    $mysql->log("Response", "returned images ids filed: ids not found.");

                    // print error msg
                    die("Error(11): ids not found.");
                }
            }
        }

        // get All gallery names
        public function getAllGaleryNames() {
          
            global $mediaController;  
            global $mysql;

            // get gallery names array
            $galleryNames = $mediaController->getAllGaleryNamesArray();

            // log action to mysql
            $mysql->log("Response", "returned gallery names");

            // print array in json
            echo json_encode($galleryNames);   
        }

        // get content of image by specify id
        public function getImageContentByID() {

            global $mediaController;  
            global $mainController;
            global $mysql;

            // get id from query string (URL)
            $specifcID = $mainController->getID();

            // check if id defined
            if ($specifcID == null) {

                // log action to mysql
                $mysql->log("Response", "returned content filed Undefined ID GET");

                // print error msg
                die("Error(12): Undefined ID GET");
            } else {
        
                // get content
                $content = $mediaController->getImageContentByID($specifcID);

                // log action to mysql
                $mysql->log("Response", "returned image content by id:".$specifcID);

                // print data in json
                echo json_encode($content);   
            }
        }

        // get all images by gallery name
        public function getAllImagesByGallery() {
            
            global $mediaController;  
            global $mainController;
            global $mysql;

            // get id from query string (URL)
            $specifcGallery = $mainController->getGalleryName();

            // get api limit
            $APILimit = $mainController->getAPILimit();

            // get api start
            $APIStartBY = $mainController->getAPIStartBy();

            // check if limit get seted
            if ($APILimit == null) {

                // log action to mysql
                $mysql->log("Response", "returned content failed Undefined limit GET");

                // print error msg
                die("Error(14): Undefined limit GET");
            }

            // check if id defined
            if ($specifcGallery == null) {

                // log action to mysql
                $mysql->log("Response", "returned content failed Undefined galleryName GET");

                // print error msg
                die("Error(15): Undefined galleryName GET");
            } else {
            
                // get content
                $content = $mediaController->getImagesContentByGallery($specifcGallery, $APIStartBY, $APILimit);

                // log action to mysql
                $mysql->log("Response", "returned image content by gallery:".$specifcGallery);

                // print data in json
                echo json_encode($content);   
            }
        }
    }
?>