<?php // media edit component

    class Editor {

        // function for edit media name and gallery
        public function editMedia() {
            
            global $mainController;
            global $mediaController;
            global $mysql;

            // get media id with new name and gallery name query
            $id = $mainController->getID();
            $galleryName = $mainController->getGalleryName();
            $name = $mainController->getName();

            // check if id is seted
            if ($id == null) {

                // log action to mysql
                $mysql->log("Response", "edit filed Undefined ID GET");

                // print error msg
                die("Error(12): Undefined ID GET");
            } else {

                // check if galleryName is set
                if ($galleryName == null) {

                    // log action to mysql
                    $mysql->log("Response", "edit filed undefined galleryName GET.");

                    // print error msg
                    die("Error(10): undefined galleryName GET.");
                } else {

                    // check if name is set
                    if ($name == null) {

                        // log action to mysql
                        $mysql->log("Response", "edit filed undefined name GET.");

                        // print error msg
                        die("Error(16): undefined name GET.");                        
                    } else {

                        // send update media query
                        $mediaController->editMedia($id, $name, $galleryName);
                    }
                }
            }
        }
    }
?>