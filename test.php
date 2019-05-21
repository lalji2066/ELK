<?php

#require 'vendor/autoload.php';
include('es_con.php');
#use Elasticsearch\ClientBuilder;
/*
#$client = ClientBuilder::create()->build();
$hosts = [
    '192.168.12.219:9200',         // IP + Port
   // '192.168.1.2',              // Just IP
    //'mydomain.server.com:9201', // Domain + Port
   // 'mydomain2.server.com',     // Just Domain
   // 'https://localhost',        // SSL to localhost
   // 'https://192.168.1.3:9200'  // SSL to IP + Port

];
$clientBuilder = ClientBuilder::create();   // Instantiate a new ClientBuilder
$clientBuilder->setHosts($hosts);           // Set the hosts
$client = $clientBuilder->build();  
*/
$esObj = new EsConn();

$esClient = $esObj->get_client_con();
//print_r($client);
$params = [
    'index' => 'my_index_php1',
    'type' => 'my_type',
    'id' => 'my_id',
    'body' => ['testField' => 'abc']
];

$response = $esClient->index($params);
print_r($response);

?>
