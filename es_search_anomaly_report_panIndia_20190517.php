<?php
ini_set('memory_limit','2048M');
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
if($source=='remote')
 $s_count=2;
else
 $s_count=6;

##############NOTE###########
# $source_params => small_table i.e. current month data table
# $source_search_params => small_table i.e. current month data table using for self search
# $dest_search_params => Target Table having overall company Data


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
$source_search_params = [
	"size" => $s_count,
        "index"=>"tbl_contract_validation_$source",
        "type"=>"tbl_source",
        "client" => [ "ignore" => [400, 404] ],
        "body" => [
                "query" => [
                        "match" => [

                         ]
                 ]
         ]
];
$dest_search_params = [
	"size" => $s_count,
	"index"=>"temp_company_match_data_consolidate_$source",
	"type"=>"tbl_dest",
	"client" => [ "ignore" => [400, 404] ],
	"body" => [
        	"query" => [
            		"match" => [
				
           		 ]
       		 ]
   	 ]
];

// Execute the search
// The response will contain the first batch of documents
// and a scroll_id
$source_response = $esClient->search($source_params);
$result_array=array();
$src_comp_output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
$src_add_output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
$dest_comp_output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
$dest_add_output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
$src_phone_output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
$dest_phone_output_data ="source_parentid#source_field#match_percent#dest_parentid#dest_field#field_type#source\n";
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
		$phone = trim($data['_source']['phone']);
			########unset serach array########
		unset($source_search_params['body']['query']['match']);
                unset($dest_search_params['body']['query']['match']);

		##########Source companyname match Start#############
                if(!empty($companyname)){
                        $search_flag=true;
                        $source_search_params['body']['query']['match']['companyname'] = $companyname; #print_r($source_search_params);
                        $source_search_comp_response = $esClient->search($source_search_params);#print_r($source_search_comp_response);exit;
			if(!empty($source_search_comp_response['hits']['hits']) && count($source_search_comp_response['hits']['hits'])>0){
                 	       foreach($source_search_comp_response['hits']['hits'] as $src_comp_result ){ #print_r($src_comp_result);
                        	        similar_text($companyname,$src_comp_result['_source']['companyname'],$src_comp_per);
                                	$field_type="source_companyname";
					if(trim($data['_source']['parentid']) != trim($src_comp_result['_source']['parentid'])){
                                		$src_comp_output_data .= "".$data['_source']['parentid']."#". $data['_source']['companyname']."#". $src_comp_per."#". $src_comp_result['_source']['parentid'] ."#". $src_comp_result['_source']['companyname']."#". $field_type ."#".$source."\n";
                        		}
				}
                	}
		}
                #########Source companyname match END#############
		
                ##########Dest companyname match Start#############
                if(!empty($companyname)){
                        $search_flag=true;
                        $dest_search_params['body']['query']['match']['companyname'] = $companyname;
                        $dest_search_comp_response = $esClient->search($dest_search_params);
			if(!empty($dest_search_comp_response['hits']['hits']) && count($dest_search_comp_response['hits']['hits'])>0){
                        	foreach($dest_search_comp_response['hits']['hits'] as $dest_comp_result ){
                                	similar_text($companyname,$dest_comp_result['_source']['companyname'],$dest_comp_per);
                                	$field_type="dest_companyname";
                               		$dest_comp_output_data .= "".$data['_source']['parentid']."#". $data['_source']['companyname']."#". $dest_comp_per."#". $dest_comp_result['_source']['parentid'] ."#". $dest_comp_result['_source']['companyname']."#". $field_type ."#".$source."\n";            
                       		 }
                	}
		}
                #########Dest companyname match END#############
/*
############################################################################################################################################
							###ADDRESS####
					unset($source_search_params['body']['query']['match']);
					unset($dest_search_params['body']['query']['match']);
############################################################################################################################################
	
		##########Source address match Start#############
                if(!empty($new_address)){
                        $search_flag=true;
                        $source_search_params['body']['query']['match']['new_address'] = $new_address;
                        $source_search_addr_response = $esClient->search($source_search_params);
                        foreach($source_search_addr_response['hits']['hits'] as $src_add_result ){
                                similar_text($new_address,$src_add_result['_source']['full_address'],$src_add_per);
                                $field_type="source_address";
				if(trim($data['_source']['parentid']) != trim($src_add_result['_source']['parentid'])){
                                	$src_add_output_data .= "".$data['_source']['parentid']."#". $data['_source']['new_address']."#". $src_add_per."#". $src_add_result['_source']['parentid'] ."#". $src_add_result['_source']['full_address']."#". $field_type ."#".$source."\n";            
                       		}
			 }
                }
                #########Source Address match END#############

		##########Dest address match Start#############
		if(!empty($new_address)){
			$search_flag=true;
			$dest_search_params['body']['query']['match']['full_address'] = $new_address;
			$dest_search_addr_response = $esClient->search($dest_search_params);
			foreach($dest_search_addr_response['hits']['hits'] as $dest_add_result ){
				similar_text($new_address,$dest_add_result['_source']['full_address'],$dest_add_per);
				$field_type="dest_address";
				$dest_add_output_data .= "".$data['_source']['parentid']."#". $data['_source']['new_address']."#". $dest_add_per."#". $dest_add_result['_source']['parentid'] ."#". $dest_add_result['_source']['full_address']."#". $field_type ."#".$source."\n";	
			}
		}
		#########Dest Address match END#############


############################################################################################################################################
                                                        ###PHONE####
                                        unset($source_search_params['body']['query']['match']);
                                        unset($dest_search_params['body']['query']['match']);
############################################################################################################################################

                ##########Source phone match Start#############
                if(!empty($phone)){
                        $search_flag=true;
                        $source_search_params['body']['query']['match']['phone'] = $phone;
                        $source_search_phone_response = $esClient->search($source_search_params);
                        foreach($source_search_phone_response['hits']['hits'] as $src_phone_result ){
                                similar_text($phone,$src_add_result['_source']['phone'],$src_phone_per);
                                $field_type="source_phone";
                                if(trim($data['_source']['parentid']) != trim($src_add_result['_source']['parentid'])){
                                        $src_phone_output_data .= "".$data['_source']['parentid']."#". $data['_source']['phone']."#". $src_phone_per."#". $src_add_result['_source']['parentid'] ."#". $src_add_result['_source']['phone']."#". $field_type ."#".$source."\n";
                                }
                         }
                }
                #########Source phone match END#############

                ##########Dest phone match Start#############
                if(!empty($phone)){
                        $search_flag=true;
                        $dest_search_params['body']['query']['match']['phone'] = $phone;
                        $dest_search_phone_response = $esClient->search($dest_search_params);
                        foreach($dest_search_phone_response['hits']['hits'] as $dest_phone_result ){
                                similar_text($phone,$dest_phone_result['_source']['phone'],$dest_phone_per);
                                $field_type="dest_phone";
                                $dest_phone_output_data .= "".$data['_source']['parentid']."#". $data['_source']['phone']."#". $dest_phone_per."#". $dest_add_result['_source']['parentid'] ."#". $dest_add_result['_source']['phone']."#". $field_type ."#".$source."\n";
                        }
                }
                #########Dest phone match END#############

*/






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
#$result_json = json_encode($result_array);
#file_put_contents('/home/laljiy/es_serach_data.json', $result_json);
#echo $source ."\n";
error_reporting(E_ALL);
if(empty(file_put_contents('/tmp/es_anomaly_report_src_comp_'.$source.'.txt', $src_comp_output_data))){
	echo "es_anomaly_report_src_comp_$source file not created";
}
if(empty(file_put_contents('/tmp/es_anomaly_report_src_add_'.$source.'.txt', $src_add_output_data))){
         echo "es_anomaly_report_src_add_$source file not created";
}
if(empty(file_put_contents('/tmp/es_anomaly_report_dest_comp_'.$source.'.txt', $dest_comp_output_data))){
         echo "es_anomaly_report_dest_comp_$source file not created";
}
if(empty(file_put_contents('/tmp/es_anomaly_report_dest_add_'.$source.'.txt', $dest_add_output_data))){
       	 echo "es_anomaly_report_dest_add_$source file not created";
}
if(empty(file_put_contents('/tmp/es_anomaly_report_src_phone_'.$source.'.txt', $src_phone_output_data))){
         echo "es_anomaly_report_src_phone_$source file not created";
}
if(empty(file_put_contents('/tmp/es_anomaly_report_dest_phone_'.$source.'.txt', $dest_phone_output_data))){
         echo "es_anomaly_report_dest_phone_$source file not created";
}

#exit;
}
?>
