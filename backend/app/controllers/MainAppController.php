<?php // APP Controller with basic functions

    class MainAppController {

        // get Http host (Url without protocol)
        public function getHTTPhost() {
            return $_SERVER['HTTP_HOST'];
        }

        // check if url is valid
        public function isUrlValid() {
            
            global $config;

            // check if url is valid
            if ($config->config["url"] == $this->getHTTPhost()) {
                return true;
            } else {
                return false;
            }
        }

        // check if HTTPS
        public function isSSL() {

            // check if https seted
            if (isset($_SERVER['HTTPS'])) {

                // check https valid value
                if ($_SERVER['HTTPS'] == 1) {
                    return true;

                // check https valid value
                } elseif ($_SERVER['HTTPS'] == 'on') {
                    return true;
                }
            }

            // return default bool -> if https unreachable
            return false;
        }

        // check if site protocol valid
        public function isProtocolValid() {

            global $config;

            if ($config->config["https"] == true && !$this->isSSL()) {
                return false;
            } else {
                return true;
            }
        }

        // check if dev_mode enabled
        public function isDevModeEnabled() {

            global $config;

            // check if dev_mode = true
            if ($config->config["dev_mode"] == true) {
                return true;
            } else {
                return false;
            }
        }

        // check if maintenance enabled
        public function isMaintenanceModeEnabled() {

            global $config;

            // check if maintenance enabled
            if ($config->config["maintenance"] == true) {
                return true;
            } else {
                return false;
            }
        }

        // get token from query string
        public function getAPIToken() {

            // create object usable
            global $mysql;

            // check if token seted
            if (!empty($_GET["token"])) {
                return $mysql->escape($_GET["token"], true, true);
            } else {
                return null;
            }
        }

        // check if api token is valid
        public function isAPITokenValid($token) {   

            global $config;

            if ($token == $config->config["token"]) {
                return true;
            } else {
                return false;
            }
        }

        // get API action
        public function getAPIAction() {

            global $mysql;

            // check if action is not empty
            if (!empty($_GET["action"])) {
                return $mysql->escape($_GET["action"], true, true);
            } else {
                return null;
            }
        }

        // get API limit
        public function getAPILimit() {

            // create object usable
            global $mysql;

            // check if action is not empty
            if (!empty($_GET["limit"])) {
                return $mysql->escape($_GET["limit"], true, true);
            } else {
                return null;
            }
        }

        // get API limit
        public function getAPIStartBy() {

            global $mysql;

            // check if action is not empty
            if (!empty($_GET["startBy"])) {
                return $mysql->escape($_GET["startBy"], true, true);
            } else {
                return null;
            }
        }

        // get Gallery name string
        public function getGalleryName() {

            global $mysql;

            // check if galleryName is not empty
            if (!empty($_GET["galleryName"])) {
                return $mysql->escape($_GET["galleryName"], false, false);
            } else {
                return null;
            }
        }

        // get name string
        public function getName() {

            global $mysql;

            // check if name is not empty
            if (!empty($_GET["name"])) {
                return $mysql->escape($_GET["name"], false, false);
            } else {
                return null;
            }
        }

        //Get ID from query string
        public function getID() {

            global $mysql;

            // check if id is not empty
            if (!empty($_GET["id"])) {
                return $mysql->escape($_GET["id"], true, true);
            } else {
                return null;
            }
        }

        // get visitor ip adress (compatible with cloudflare)
        public function getRemoteAdress() {
            
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $address = $_SERVER['HTTP_CLIENT_IP'];
                
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
  
            } else {
                $address = $_SERVER['REMOTE_ADDR'];
            }
            return $address;
        }

        public function getRandomString($length = 10) {
            return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
        }

        // send API haders for client
        public function sendAPIheaders() {

            // send API headers if action is not delete or upload
            if ($this->getAPIAction() != "delete" && $this->getAPIAction() != "upload" && $this->getAPIAction() != "edit") {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, POST');
                header("Access-Control-Allow-Headers: X-Requested-With"); 
                header('Content-Type: application/json; charset=utf-8');
            }
        }
    }
?>