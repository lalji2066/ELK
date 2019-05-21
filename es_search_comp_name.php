<?php

include('es_con.php');
include('utils.php');

$esObj = new EsConn();
$esClient = $esObj->get_client_con();

$source_params = [
    "scroll" => "30s",          // how long between scroll requests. should be small!
    "size" => 1000,               // how many results *per shard* you want back
    "index" => "tbl_contract_validation_remote",
   "type"=>"tbl_dest",
    "client" => [ "ignore" => [400, 404] ],
    "body" => [
        "query" => [
            "match_all" => new \stdClass()
        ]
    ]
];

$dest_params_comp = [
	"index"=>"temp_company_match_data_consolidate_remote",
	"type"=>"tbl_sourec",
	"client" => [ "ignore" => [400, 404] ],
	"body" => [
        "query" => [
            "match" => [
                "companyname" => ""
            ]
        ]
    ]

];
$dest_params_add = [
        "index"=>"temp_company_match_data_consolidate_remote",
        "type"=>"data",
	"client" => [ "ignore" => [400, 404] ],
        "body" => [
        "query" => [
            "match" => [
                "full_address" => ""
            ]
        ]
    ]

];

// Execute the search
// The response will contain the first batch of documents
// and a scroll_id
$source_response = $esClient->search($source_params);
#print_r($source_response);exit;
#print_r($source_response['hits']['hits'][0]['_source']['companyname']);
#echo count($source_response);exit;
/*
foreach($source_response['hits']['hits'] as $data){
#echo $companyname = ($data['_source']['companyname']);
//	$companyname="Baba Bakery";
        $dest_params['body']['query']['match']['companyname']=$companyname;
	print_r($dest_params);#exit;
        $dest_response = $esClient->search($dest_params);
#	if(!empty($dest_response['hits']['hits']))
        print_r($dest_response);
#       exit;
}
*/
$result_array=array();
echo "id#source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
// Now we loop until the scroll "cursors" are exhausted
$i=0;
while (isset($source_response['hits']['hits']) && count($source_response['hits']['hits']) > 0) {
#$i++;
    // **
    // Do your work here, on the $response['hits']['hits'] array
    // **

	foreach($source_response['hits']['hits'] as $data){
	$i++;
	$search_flag = false;
	$companyname = trim($data['_source']['companyname']);
	$new_address = trim($data['_source']['new_address']);
	if(!empty($companyname)){
	$search_flag=true;
	$dest_params_comp['body']['query']['match']['companyname']=$companyname;
#	try{
#	if($esClient->searchExists($dest_params_comp))
		$dest_response_comp = $esClient->search($dest_params_comp);

#	else
#	continue;
#	}catch (Exception $e) {
                // Not found. You might want to return FALSE if wrapped in a function.
                // return FALSE;
 #       }

#################################company_match
	if(!empty($dest_response_comp['hits']['hits']) ){

		 similar_text($companyname,$dest_response_comp['hits']['hits'][0]['_source']['companyname'],$comp_per);
		if($comp_per>85){
		$field_type="companyname";
		echo "$i#".$data['_source']['parentid']."#". $data['_source']['companyname']."#". $comp_per."#". $dest_response_comp['hits']['hits'][0]['_source']['parentid'] ."#". $dest_response_comp['hits']['hits'][0]['_source']['companyname']."#". $field_type ."#remote \n";	
		#array_push($result_array,$data);	
			}	
	}
}
/*
#################address_match##############3333
	$new_address = trim($data['_source']['new_address']);
	if(!empty($new_address)){
	$search_flag=true;
	$dest_params_add['body']['query']['match']['full_address']=$new_address;
	try {
		if($esClient->searchExists($dest_params_add))
		$dest_response_add = $esClient->search($dest_params_add);
		else
			continue;
	} catch (Exception $e) {
  		// Not found. You might want to return FALSE if wrapped in a function.
  		// return FALSE;
	}
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
#$result_json = json_encode($result_array);
#file_put_contents('/home/laljiy/es_serach_data.json', $result_json);
#echo"\n total_count:". $i;
?>
