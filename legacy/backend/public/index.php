<?php 

    // include all functions ///////////////////////////////////////////////////
    require_once("../config.php");
    require_once("../app/controllers/MainAppController.php");
    require_once("../app/controllers/MysqlController.php");
    require_once("../app/controllers/MediaController.php");
    require_once("../app/Responses.php");
    require_once("../app/Uploader.php");
    require_once("../app/Editor.php");
    require_once("../app/MediaDelete.php");

    // create objects of app classes
    $config = new Config();
    $mainController = new MainAppController();
    $mysql = new MysqlController();
    $response = new Responses();
    $mediaController = new MediaController();
    $uploader = new Uploader();
    $editor = new Editor();
    $mediaDelete = new MediaDelete();
    ////////////////////////////////////////////////////////////////////////////

    // init libs
	if(file_exists('../vendor/autoload.php')) {
		require_once('../vendor/autoload.php');	
	} else {
		
		// redirect to error page if composer components is not installed
		if ($mainController->isDevModeEnabled()) {
            http_response_code(404);
            die("../vendor dir not found, please reinstall composer");
		} else {
            die("Error(1): Please contact service admin to fix");
		}
	} 

	// set default encoding
	header('Content-type: text/html; charset='.$config->config["encoding"]);


	// init whoops for error headling //////////////////////////////////////////
	if ($mainController->isDevModeEnabled()) {
		$whoops = new \Whoops\Run;
		$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		$whoops->register();
	}
    ////////////////////////////////////////////////////////////////////////////
    


    /////////////////////////// Check if all valid /////////////////////////////
    // check if URL is valid
    if ($mainController->isUrlValid()) {

        // check if protocol is valid
        if ($mainController->isProtocolValid()) {
            
            // check if maintenance is enabled
            if ($mainController->isMaintenanceModeEnabled()) {
                http_response_code(503);

                // log action to mysql
                $mysql->log("API Load", "Request blocked by maintenance mode");

                // print error msg
                die("Error(4): Service is currently under maintenance mode, please try again leater...");
            
            } 
            
            // valid protocol
            else { 

                // check if API token is valid
                $token = $mainController->getAPIToken();

                // check if token is not null
                if ($mainController->isAPITokenValid($token)) {

                    // include main app component
                    include("../app/API.php");

                } else {

                    // log action to mysql
                    $mysql->log("API Access", "Access blocked for invalid API token");

                    // print error msg
                    die("Error(7): Please use valid API token");
                }
            }

        // protocol invalid
        } else {

            // log action to mysql
            $mysql->log("API Load", "Blocked by invalid protocol: https");

            // check if dev_mode enabled
            if ($mainController->isDevModeEnabled()) {
                http_response_code(403);

                // print error msg
                die("Protocol error: https required");
            } else {

                // print error msg
                die("Error(3): Unauthorized request.");
            }
        }

    // invalid url
    } else {

        // log action to mysql
        $mysql->log("API Load", "Blocked  domain is not valid");

        // check if dev_mode enabled
        if ($mainController->isDevModeEnabled()) {
            http_response_code(403);

            // print error msg
            die("Current domain is not valid, please use: ".$config->config["url"]);
        } else {

            // print error msg
            die("Error(2): Unauthorized request.");
        }
    }
    ///////////////////////////////////////////////////////////////////////////
?>