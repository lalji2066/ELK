<?php

error_reporting(E_ALL);

include('es_con.php');
include('utils.php');

$esObj = new EsConn();
$esClient = $esObj->get_client_con();

#$source_array= array("mumbai","delhi","kolkata","bangalore","chennai","pune","hyderabad","ahmedabad");
#foreach($source_array as $source){
if(isset($_GET['city']))
$source = $_GET['city'];
else
$source = $argv[1];
#echo $source;exit;
if(!empty($source)){
#$source = $_GET['city'];

$source_params = [
    "scroll" => "30s",          // how long between scroll requests. should be small!
    "size" => 1000,               // how many results *per shard* you want back
    "index" => "tbl_contract_validation_$source",
    "type"=>"tbl_source",
    "client" => [ "ignore" => [400, 404] ],
    "body" => [
        "query" => [
            "match_all" => new \stdClass()
        ]
    ]
];


$dest_params_comp = [
	"index"=>"temp_company_match_data_consolidate_$source",
	"type"=>"tbl_dest",
	"client" => [ "ignore" => [400, 404] ],
	"body" => [
        "query" => [
        ]
    ]

];
// Execute the search
// The response will contain the first batch of documents
// and a scroll_id
$source_response = $esClient->search($source_params);
$result_array=array();
$output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
$id=0;
// Now we loop until the scroll "cursors" are exhausted
while (isset($source_response['hits']['hits']) && count($source_response['hits']['hits']) > 0) {

    // **
    // Do your work here, on the $response['hits']['hits'] array
    // **

	foreach($source_response['hits']['hits'] as $data){
		$id++;
		$search_flag = false;
		$companyname = trim($data['_source']['companyname']);
		$new_address = trim($data['_source']['new_address']);
		$mobile      = trim($data['_source']['mobile']);
		$query_json ='{
    					"bool":{
      						"should":[
                					{"match":{"companyname":"'.$companyname.'"}},
                					{"match":{"full_address":"'.$new_address.'"}},
                					{"match":{"phone":"'.$mobile.'"}}
        					],
        					"minimum_should_match" : 1
      					}
    				}';
		if(!empty($companyname)){
			$search_flag=true;
			$dest_params_comp['body']['query'] = json_decode($query_json,true);
		#	print_r($dest_params_comp);exit;
			$dest_response_comp = $esClient->search($dest_params_comp);
			#print_r($dest_response_comp);exit;
			if(!empty($dest_response_comp['hits']['hits']) ){
				$res_loop = 0;
				foreach($dest_response_comp['hits']['hits'] as $dest_response){ #print_r($dest_response);exit;
					similar_text($companyname,$dest_response['_source']['companyname'],$comp_per);
					similar_text($new_address,$dest_response['_source']['full_address'],$add_per);
					similar_text($mobile,$dest_response['_source']['phone'],$phone_per);
					 $field_type="companyname~new_address~mobile";
	                        echo    $output_data .= "".$data['_source']['parentid']."#". $data['_source']['companyname']."~".$data['_source']['new_address']."~".$data['_source']['mobile']."#". $comp_per."~".$add_per."~".$phone_per."#". $dest_response['_source']['parentid'] ."#". $dest_response['_source']['companyname']."~".$dest_response['_source']['full_address']."~".$dest_response['_source']['phone']."#". $field_type ."#".$source."\n"; 
				
					if(++$res_loop > 2 )
						break;
				}			
			}
		}

	}

    // When done, get the new scroll_id
    // You must always refresh your _scroll_id!  It can change sometimes
    $scroll_id = $source_response['_scroll_id'];

    // Execute a Scroll request and repeat
    $source_response = $esClient->scroll([
            "scroll_id" => $scroll_id,  //...using our previously obtained _scroll_id
            "scroll" => "60s"           // and the same timeout window
        ]
    );
}
#############################Serach END###############################
exit;
error_reporting(E_ALL);
if(empty(file_put_contents('/tmp/es_search/curl_es_search_data_'.$source.'.txt', $output_data))){
	echo $output_data;
}
#exit;
}
?>
