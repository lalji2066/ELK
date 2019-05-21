<?php

include('es_con.php');

$esObj = new EsConn();
$esClient = $esObj->get_client_con();

$params = [
    "scroll" => "30s",          // how long between scroll requests. should be small!
    "size" => 50,               // how many results *per shard* you want back
    "index" => "tbl_contract_validation_mumbai",
    "body" => [
        "query" => [
            "match_all" => new \stdClass()
        ]
    ]
];

// Execute the search
// The response will contain the first batch of documents
// and a scroll_id
$response = $esClient->search($params);

print_r($response);
echo count($response);exit;

// Now we loop until the scroll "cursors" are exhausted
while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {

    // **
    // Do your work here, on the $response['hits']['hits'] array
    // **
print_r($response['hits']['hits']);
    // When done, get the new scroll_id
    // You must always refresh your _scroll_id!  It can change sometimes
    $scroll_id = $response['_scroll_id'];

    // Execute a Scroll request and repeat
    $response = $client->scroll([
            "scroll_id" => $scroll_id,  //...using our previously obtained _scroll_id
            "scroll" => "30s"           // and the same timeout window
        ]
    );
}

?>
