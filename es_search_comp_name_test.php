<?php

include('es_con.php');
include('utils.php');

$esObj = new EsConn();
$esClient = $esObj->get_client_con();

$source_array= array("mumbai","delhi","kolkata","bangalore","chennai","pune","hyderabad","ahmedabad");
foreach($source_array as $source){

$source_params = [
    "scroll" => "30s",          // how long between scroll requests. should be small!
    "size" => 10000,               // how many results *per shard* you want back
    "index" => "tbl_contract_validation_$source",
    "type"=>"tbl_dest",
    "body" => [
        "query" => [
            "match_all" => new \stdClass()
        ]
    ]
];

$dest_params_comp = [
	"index"=>"temp_company_match_data_consolidate_$source",
	"type"=>"tbl_sourec",
	"body" => [
        "query" => [
            "match" => [
                "companyname" => ""
            ]
        ]
    ]

];
/*
$dest_params_add = [
        "index"=>"temp_company_match_data_consolidate_remote",
        "type"=>"data",
        "body" => [
        "query" => [
            "match" => [
                "full_address" => ""
            ]
        ]
    ]

];
*/
// Execute the search
// The response will contain the first batch of documents
// and a scroll_id
$source_response = $esClient->search($source_params);
#print_r($source_response);exit;
#print_r($source_response['hits']['hits'][0]['_source']['companyname']);
#echo count($source_response);exit;
$result_array=array();
$output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";

// Now we loop until the scroll "cursors" are exhausted
while (isset($source_response['hits']['hits']) && count($source_response['hits']['hits']) > 0) {

    // **
    // Do your work here, on the $response['hits']['hits'] array
    // **

foreach($source_response['hits']['hits'] as $data){
	$search_flag = false;
	$companyname = trim($data['_source']['companyname']);
	$new_address = trim($data['_source']['new_address']);
	if(!empty($companyname)){
	$search_flag=true;
	$dest_params_comp['body']['query']['match']['companyname']=$companyname;
	$dest_response_comp = $esClient->search($dest_params_comp);
	
#################################company_match
	if(!empty($dest_response_comp['hits']['hits']) ){

			 similar_text($companyname,$dest_response_comp['hits']['hits'][0]['_source']['companyname'],$comp_per);
			# similar_text($new_address,$dest_response_add['hits']['hits'][0]['_source']['full_address'],$add_per);
		if($comp_per>85){
		$field_type="companyname";
		$output_data .= "".$data['_source']['parentid']."#". $data['_source']['companyname']."#". $comp_per."#". $dest_response_comp['hits']['hits'][0]['_source']['parentid'] ."#". $dest_response_comp['hits']['hits'][0]['_source']['companyname']."#". $field_type ."#".$source."\n";	
		#array_push($result_array,$data);	
			}	
	}
}
/*
#################address_match##############3333
if(!empty($data['_source']['new_address']))
#print_r($data);#exit;
echo 	$new_address = trim($data['_source']['new_address']);
	if(!empty($new_address)){
	$search_flag=true;
	$dest_params_add['body']['query']['match']['full_address']=$new_address;
	print_r($dest_params_add);
	$dest_response_add = $esClient->search($dest_params_add);

	if( !empty($dest_response_add['hits']['hits']) ){
			$field_type="address";
                         #similar_text($companyname,$dest_response_comp['hits']['hits'][0]['_source']['companyname'],$comp_per);
                         similar_text($new_address,$dest_response_add['hits']['hits'][0]['_source']['full_address'],$add_per);
                if($add_per > 80){
                echo "".$data['_source']['parentid']."#". $data['_source']['new_address']."#". $add_per."#". $dest_response_add['hits']['hits'][0]['_source']['parentid'] ."#". $dest_response_add['hits']['hits'][0]['_source']['full_address']."#". $field_type."\n";
                #array_push($result_array,$data);       
                        }
        }
}
#######################################3Address End############################
*/
if(!$search_flag)
continue;

}

    // When done, get the new scroll_id
    // You must always refresh your _scroll_id!  It can change sometimes
    $scroll_id = $source_response['_scroll_id'];

    // Execute a Scroll request and repeat
    $source_response = $esClient->scroll([
            "scroll_id" => $scroll_id,  //...using our previously obtained _scroll_id
            "scroll" => "30s"           // and the same timeout window
        ]
    );
}
#############################Serach END###############################
#$result_json = json_encode($result_array);
#file_put_contents('/home/laljiy/es_serach_data.json', $result_json);
file_put_contents("/tmp/es_serach_data_$source.txt", $outout_data);
exit;
}
?>
