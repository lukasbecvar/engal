<?php // delete componnt

    class MediaDelete {

        // delete image by ID
        public function deleteImage() {

            global $mainController;
            global $mysql;
            global $config;

            if ($mainController->getID() == null) {
                
                // log action to mysql
                $mysql->log("Delete", "delete failed image id not specified");

                // print error msg
                die("Error(11): ids not found.");
            } else {

                // get if form query string
                $id = $mainController->getID();
                    
                // get image identifier
                $identifier = $mysql->read("SELECT identifier FROM images WHERE id = '".$id."'", "identifier");

                // file path builder
                $filePath = "../storage/images/".$identifier;

                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // delete image from database
                $mysql->insert("DELETE FROM images WHERE id=$id");

                // log action to mysql
                $mysql->log("Delete", "Image: $id deleted from database");
    
                // close window with js
                echo "<script>window.close();</script>";
            }
        }
    }
?>