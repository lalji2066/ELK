<?php

require 'vendor/autoload.php';
include('config/es_config.php');

use Elasticsearch\ClientBuilder;

$data = file_get_contents('php://input');
#echo"\n". $data."\n";
#print_r($_REQUEST);exit;
#echo$params = $_REQUEST['params'];
#print_r($params);
$params_array = json_decode($data,true);
#print_r($params_array);
$action = $_REQUEST['action'];
$action_array = array('create_index','get_index','update_index','delete_index','search_index');
if(!in_array($action,$action_array)){
 echo 'Invalid action!! Please provide valid action.';
return false;
}
if(empty($params_array) || is_null($params_array)){
echo 'params cannot be empty!! please provide valid JSON data in params';
return false;
}

$es_obj = new EsConn();
$es_obj->get_client_con();
$result = $es_obj->$action($params_array);
if(!empty($result))
return json_encode($result);

class EsConn{

private $hosts;
private $clientBuilder;
private $client;
private $response;
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
		public function create_index($params,$bulk=false){
			if($bulk)
				#return $this->response = $this->client->bulk($params);
				print_r(json_encode($this->client->bulk($params)));
			else
				#return $this->response = $this->client->index($params);
				print_r(json_encode($this->client->index($params)));
		}
		public function get_index($params){ #print_r($params);
			#return $this->response = $this->client->get($params);
			print_r(json_encode($this->client->get($params)));
		}
		public function update_index($params){
			 #return $this->response = $this->client->update($params);
			print_r(json_encode($this->client->update($params)));
		}
		public function delete_index($params){
                        # return $this->response = $this->client->delete($params);
			print_r(json_encode($this->client->delete($params)));
                }
		public function search_index($params){
                         #return $this->response = $this->client->search($params);
			print_r(json_encode($this->client->search($params)));
                }

		
}
?>
