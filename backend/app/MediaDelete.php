<?php // delete componnt

    class MediaDelete {

        // delete image by ID
        public function deleteImage() {

            // make objects usable in function
            global $mainController;
            global $mysql;

            if ($mainController->getID() == null) {
                
                // log action to mysql
                $mysql->log("Delete", "delete failed image id not specified");

                // print error msg
                die("Error(11): ids not found.");
            } else {

                // get if form query string
                $id = $mainController->getID();
                
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