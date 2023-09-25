<?php // API Config

    class Config {

        public $config = [

            // url with subdomain to access API
            "url" => "engal.localhost", 

            // API Version
            "version" => 1.0,              
            
            // default charset
            "encoding" => "utf8",           

            // if this = true (Site can run only on https://)
            "https" => false,                
            
            // devmode value
            "dev_mode" => true,                

			// maintenance config (Disable acces to API)
			"maintenance" => false,         

            // verify token access
            "token" => "1234",   

			/* mysql config */
			"ip" 		=> 	"localhost",	// mysql server ip
			"basedb" 	=> 	"engal",	    // mysql default db name
			"username"	=> 	"root",			// mysql user 
			"password" 	=> 	"root"			// Mysql password
        ];
    }
?>
