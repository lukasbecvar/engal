<?php // mysql utils class

    class MysqlController {

        // connection to mysql by db name
        public function connect($dbName) {

            global $config;
            global $mainController;

            // try connect to database
            try {

                // connection
                $con = mysqli_connect($config->config["ip"], $config->config["username"], $config->config["password"], $dbName);
            
            } catch(Exception $e) { 
                
                // print error
                if ($mainController->isDevModeEnabled()) {
                    http_response_code(503);
                    die("Database error: ".$e->getMessage());
                } else {
                    die("Error(5): The service is unavailable.");
                }
            }

            // set mysql utf/8 charset
            mysqli_set_charset($con, $config->config["encoding"]);

            // return connection
            return $con;
        }

        // insert query to mysql
        public function insert($query) {

            global $config;
            global $mainController;

            // insert to mysql
            $useInsertQuery = mysqli_query($this->connect($config->config["basedb"]), $query);
            
            // print error if function filed
            if (!$useInsertQuery) {
                if ($mainController->isDevModeEnabled()) {
                    http_response_code(503);
                    die('Database error: the database server query could not be completed');	
                } else {
                   die("Error(6): The service is unavailable.");
                }
            }
        }

        // get mysql version
        public function getVersion() {
            $output = shell_exec('mysql -V');
            preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
            return $version[0];
        }

        // log to mysql logs table
        public function log($name, $value) {

            global $mainController;

            // escape values
            $name = $this->escape($name, true, true);
            $value = $this->escape($value, true, true);

            // init default values
            $date = date('d.m.Y H:i:s');
            $remote_addr = $mainController->getRemoteAdress();
            $status = "unreader";
            $browser = $_SERVER['HTTP_USER_AGENT'];

            // save log to table
            $this->insert("INSERT INTO `logs`(`name`, `value`, `date`, `remote_addr`, `browser`, `status`) VALUES ('$name', '$value', '$date', '$remote_addr', '$browser', '$status')");
        }

        // string escape
        public function escape($string, $stripTags = false, $specialChasr = false) {
            
            global $config;

            // escapee string
            $out = mysqli_real_escape_string($this->connect($config->config["basedb"]), $string);
            
            // strip html tags
            if ($stripTags) {
                $out = strip_tags($out);
            }

            // remove epecial chars
            if ($specialChasr) {
                $out = htmlspecialchars($out, ENT_QUOTES);
            }

            // return escaped string
            return $out;
        }

        // set encoding charset
        public function setCharset($charset) {

            global $config;

            // set charset
            mysqli_set_charset($this->mysqlConnect($config->config["basedb"]), $charset);
        }

        // read data from mysql
        public function read($query, $specifis) {
            
            global $config;
            
            // send SQL query
            $sql = mysqli_fetch_assoc(mysqli_query($this->mysqlConnect($config->config["basedb"]), $query));
            
            //return selected data
            return $sql[$specifis];
        }
    }
?>