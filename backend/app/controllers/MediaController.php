<?php // media controller for get/upload/edit/count media

    class MediaController {

        // get all images count or by gallery name 
        public function getImagesCount($gallery = "all") {

            global $mysql;
            global $config;

            // get count of all images
            if ($gallery == "all") {
            
                // send query to mysql to get count
                $count = mysqli_fetch_assoc(mysqli_query($mysql->connect($config->config["basedb"]), "SELECT COUNT(*) AS count FROM images"))["count"];
            } 
            
            // get count by gallery name
            else {

                // send query to mysql to get count
                $count = mysqli_fetch_assoc(mysqli_query($mysql->connect($config->config["basedb"]), "SELECT COUNT(*) AS count FROM images WHERE gallery LIKE '%$gallery%'"))["count"];
            }

            // return final images count
            return $count;
        }

        // get count of galleries
        public function getGalleryCount() {

            global $mysql;
            global $config;  

            // send query to mysql to get count
            $count = mysqli_fetch_assoc(mysqli_query($mysql->connect($config->config["basedb"]), "SELECT COUNT(DISTINCT gallery) AS count FROM images"))["count"];

            // return final galleries count
            return $count;
        }

        // get all images IDS array
        public function getIDsArray($galleryName = "all") {

            global $mysql;
            global $config;  

            // create empty array for ids
            $idsArr = [];
            
            // all ids
            if ($galleryName == "all") {
                $ids = mysqli_query($mysql->connect($config->config["basedb"]), "SELECT id from images ORDER BY id DESC");
            } 

            // all images with randomize
            elseif ($galleryName == "randomImagess2WH92Aww") {
                $ids = mysqli_query($mysql->connect($config->config["basedb"]), "SELECT id from images ORDER BY rand()");
            }
            
            // get ids by gallery name
            else {

                $ids = mysqli_query($mysql->connect($config->config["basedb"]), "SELECT id from images WHERE gallery='$galleryName'");
            }
            
            // push ids to array
            foreach ($ids as $value) {
                array_push($idsArr, $value["id"]);
            }

            // return final array
            return $idsArr;
        }

        // get all gallery names in array
        public function getAllGaleryNamesArray() {
            
            global $mysql;
            global $config;  

            // create empty array for names
            $names = [];

            // get gallery names from database
            $galleryNames = mysqli_query($mysql->connect($config->config["basedb"]), "SELECT DISTINCT gallery from images");
        
            // push to array
            foreach ($galleryNames as $value) {
                array_push($names, $value["gallery"]);
            }

            // return final array
            return $names;
        }

        // get image content in array
        public function getImageContentByID($id) {

            global $mysql;
            global $config;  

            // get image content from database
            $imageContent = mysqli_fetch_assoc(mysqli_query($mysql->connect($config->config["basedb"]), "SELECT * from images WHERE id='$id'"));

            // check if content is not null
            if ($imageContent != null) {

                // build array with image content
                $content = [
                    "id" => $id,
                    "name" => $imageContent["name"],
                    "gallery" => $imageContent["gallery"],
                    "upload_date" => $imageContent["upload_date"],
                    "content" => $imageContent["content"]
                ];

                // return final content
                return $content;
            } else {

                // print not found error
                die("Error(13): ID:".$id." not found in database");
            }
        }

        // get image content in array
        public function getImagesContentByGallery($gallery, $start, $limit) {

            global $mysql;
            global $config;  

            // set default start select value
            if ($start == NULL) {
                $start = 0;
            }

            // get image content from database
            if ($gallery == "randomImagess2WH92Aww") {
                $imageContent = mysqli_query($mysql->connect($config->config["basedb"]), "SELECT * from images ORDER BY rand() LIMIT $start, $limit");
            } else if ($gallery == "allhPC12fR0u") {
                $imageContent = mysqli_query($mysql->connect($config->config["basedb"]), "SELECT * from images ORDER BY id DESC LIMIT $start, $limit");
            }else {
                $imageContent = mysqli_query($mysql->connect($config->config["basedb"]), "SELECT * from images WHERE gallery='$gallery' LIMIT $start, $limit");
            }

            // check if content is not null
            if ($imageContent != null) {

                // create empty array for images data
                $arr = [];

                foreach ($imageContent as $value) {
                    
                    // build image array
                    $item = [
                        "id" => $value["id"],
                        "name" => $value["name"],
                        "gallery" => $value["gallery"],
                        "upload_date" => $value["upload_date"],
                        "content" => $value["content"]
                    ];

                    // push image data to final array
                    array_push($arr, $item);
                }

                // return array
                return $arr;
            } else {

                // print not found error
                die("Error(13): gallery:".$gallery." not found in database");
            }
        }

        // media update function
        public function editMedia($id, $name, $gallery) {
        
            global $mysql;
            global $config;

            // update image name
            mysqli_query($mysql->connect($config->config["basedb"]), "UPDATE images SET name='$name' WHERE id=$id");
            
            // update image gallery name
            mysqli_query($mysql->connect($config->config["basedb"]), "UPDATE images SET gallery='$gallery' WHERE id=$id");
        }
    }
?>