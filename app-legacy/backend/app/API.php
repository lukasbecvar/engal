<?php // main API structure list

    // send API headers
    $mainController->sendAPIHeaders();

    /////////////////////////////// API-ACTIONS ///////////////////////////////
    // print service status
    if ($mainController->getAPIAction() == "status") {
        $response->status();

    // print media counter
    } elseif ($mainController->getAPIAction() == "counter") {
        $response->counter();

    // print all images ids
    } elseif ($mainController->getAPIAction() == "ids") {
        $response->getAllImagesIDs();

    // print images ids by gallery name
    } elseif ($mainController->getAPIAction() == "idsByName") {
        $response->getIDSByGalleryName();

    // print all gallery names
    } elseif ($mainController->getAPIAction() == "allGaleryNames") {
        $response->getAllGaleryNames();

    // print image content by ID
    } elseif ($mainController->getAPIAction() == "imageContent") {
        $response->getImageContentByID();

    // print images content by gallery name
    } elseif ($mainController->getAPIAction() == "getAllImagesDataByGallery") {
        $response->getAllImagesByGallery();

    // edit image name and gallery
    } elseif ($mainController->getAPIAction() == "edit") {
        $editor->editMedia();

    // image upload function
    } elseif ($mainController->getAPIAction() == "upload") {
        $uploader->imageUploadFromClient();

    // delete image by ID
    } elseif ($mainController->getAPIAction() == "delete") {
        $mediaDelete->deleteImage();
    ///////////////////////////////////////////////////////////////////////////

    // action not found
    } else {

        // print not found error
        if ($mainController->getAPIAction()) {

            // log action to mysql
            $mysql->log("API Load", "Load filed action parameter not found");

            // print error msg
            die("Error(8): action parameter not found!");
        } else {

            // log action to mysql
            $mysql->log("API Load", "Load filed unknow action paramater");

            // print error msg
            die("Error(9): unknow action paramater");
        }
    }
?>