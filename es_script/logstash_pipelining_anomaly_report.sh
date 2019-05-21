#!/bin/bash

/usr/bin/curl -XDELETE "http://192.168.12.219:9200/tbl_contract_validation*"

sleep 100

/usr/bin/curl -XDELETE "http://192.168.12.219:9200/temp_company_match_data_consolidate*" 

sleep 200

/usr/share/logstash/bin/./logstash --path.settings /etc/logstash/ 

