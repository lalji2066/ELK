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
$action_array = array('get_sales_data','search_index');
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
		public function get_sales_data($params){ #print_r($params);#exit;
			$emp_code_array = $params['emp_code'];
			$mgr_code = $params['mgr_code'];
			#print_r($emp_code_array);
			$to_date = $params['to_date'];
			$from_date = $params['from_date'];
			$city = implode('","',$params['city']);
			$icity = $params['icity'];
			$emptype = $params['emptype'];
			$team_type_name = $params['team_type_name'];
			$collection = 0;
			foreach($emp_code_array as $emp_code){ #echo "TATA";
			$collection = 0;
			$managers_result =0;
			#################MANUAL#########
			$query = '{
   					 "bool":{
      						"must":[
        						{"term":{"mgr_code":'.$emp_code.'}},
        						{"terms":{"data_city.keyword":["'.$city.'"]}}
        					],
        
       						 "filter": [
								{"range": { "end_date": { "gte": "'.$from_date.'" }}},
								{"range": { "date": {"lte": "'.$to_date.'" }}}
							]
						 }
    					}';
			$query_array = json_decode($query,true);
		#	print_r($query_array);#exit;
			$managers_params['index'] = "sales_tbl_manager_all_lineage_finance_dc_$icity";
			$managers_params['type'] = "tbl_manager_all_lineage_finance_dc_$icity";
			$managers_params['client'] =  [ "ignore" => [400, 404] ];
			$managers_params['body']['size'] =5000 ;
			$managers_params['body']['query'] = $query_array;
			$managers_params['body']['sort']['date']['order'] = 'asc';
			#echo json_encode($managers_params);
			$managers_result=$this->client->search($managers_params);
			#print_r($managers_result);
				$i=0;
				$emp_res_array = null;
				foreach($managers_result['hits']['hits'] as $data){ #print_r($data);exit;
					#	echo"\n $emp_code \n";	
						$employees = $data['_source']['employees'];
						$date = substr(trim($data['_source']['date']),0,10);
						
						$employees_array = explode(',',$employees);	
						$employees_data="";
						foreach($employees_array as $emp){
							$employees_data .= intval($emp).',';
						}
						$employees = rtrim($employees_data,',');
						$emp_res_array[$i++] = ["date"=>$date,"employees"=>$employees];
				
				}
			#}	
		#	print_r($emp_res_array);#exit;

			#foreach($emp_res_array as $emp_res){
			$collection = 0;
			for( $j=0; $j < count($emp_res_array);$j++){
			#echo $key.	print_r($val);exit;
				$e_from_date=null;
				$e_to_date=null;
				$e_employees = $emp_res_array[$j]['employees'];
				$e_from_date = $emp_res_array[$j]['date'];
				if(strtotime($e_from_date) < strtotime($from_date)) $e_from_date = $from_date;
				#$e_from_date = $from_date;
				if(isset($emp_res_array[$j+1]['date']))
					$e_to_date =  date('Y-m-d', strtotime('-1 day', strtotime($emp_res_array[$j+1]['date'])));
				else
					$e_to_date = $to_date;
			#	echo "\n e_from_date:".$e_from_date."\n  e_to_date:".$e_to_date;
				$json_query_array =['employees'=> '{
    							"bool" : {
      								"must" :[
									{"term":{"emptype.keyword":"'.$emptype.'"}},
      									{"term":{"team_type_name.keyword":"'.$team_type_name.'"}},
									{"terms":{"jda_flag":[0,2]}},
                                                                        {"terms":{"main_city_flag":[0,1]}},	
									{"terms" : {"empcode":['.$e_employees.']}}
									],

								 "filter": [
									{"range": { "to_date": { "gte": "'.$e_from_date.'","lte": "'.$e_to_date.'" }}}
									]
    								}
  							}',
							'manager'=>'{
                                                        "bool" : {
                                                                "must" :[
									{"term":{"emptype.keyword":"TME"}},
                                                                        {"term":{"team_type_name.keyword":"Super Cat"}},
                                                                        {"terms":{"manager_code":['.$mgr_code.']}},
                                                                        {"terms":{"jda_flag":[0,2]}},
                                                                        {"terms":{"main_city_flag":[0,1]}},     
                                                                        {"terms" : {"empcode":['.$emp_code.']}}
                                                                        ],

                                                                 "filter": [
                                                                        {"range": { "to_date": { "gte": "'.$e_from_date.'","lte": "'.$e_to_date.'" }}}
                                                                        ]
                                                                }
                                                        }'];
			$json_query_aggs = '{
     						"total_collection" : { "sum" : { "field" : "collection" } }
    					}';
			foreach($json_query_array as $key => $json_query){
				$ $finance_result = null;
				$finance_params = [
                                                "index"=>"sales_tbl_actual_daily_sales_emplevel_2_finance_$icity",
						"type"=>"tbl_actual_daily_sales_emplevel_2_finance_$icity",
                                                "client" => [ "ignore" => [400, 404] ],
                                                        "body" => [
                                                                "size"=>5000,
                                                                "query" => json_decode($json_query,true),
								"aggs"=>json_decode($json_query_aggs,true)
								]
                                                ];
				#echo"\n  \n". json_encode($finance_params);
				$finance_result = $this->client->search($finance_params);
				#print_r($finance_result);
                               	$collection +=$finance_result['aggregations']['total_collection']['value'];
				echo"\n total collection from $e_from_date to $e_to_date for  $emp_code $key is :Rs ".$finance_result['aggregations']['total_collection']['value'];
		 
				} #end foreach
			} #end for loop
				$emp_collection_array[$emp_code] = ["emp_code"=>$emp_code,
							"collection"=>$collection]; 
		}#end emp_code foreach
			print_r($emp_collection_array);
	}#end function
		
}#end class
?>
