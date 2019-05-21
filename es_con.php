<?php

require 'vendor/autoload.php';
include('config/es_config.php');

use Elasticsearch\ClientBuilder;

class EsConn{

private $hosts;
private $clientBuilder;
private $client;
function __construct() {
#$client = ClientBuilder::create()->build();
		$this->hosts = [
   				 SERVER_IP,         // IP + Port
   				/* '192.168.1.2',              // Just IP
    				'mydomain.server.com:9201', // Domain + Port
    				'mydomain2.server.com',     // Just Domain
    				'https://localhost',        // SSL to localhost
    				'https://192.168.1.3:9200'  // SSL to IP + Port
				*/
			];
		}
		public function get_client_con(){
			$this->clientBuilder = ClientBuilder::create();   // Instantiate a new ClientBuilder
			$this->clientBuilder->setHosts($this->hosts);           // Set the hosts
			$this->client = $this->clientBuilder->build();  
			return $this->client;
		}
}
?>
