#!/bin/bash
# By Lalji Yadav
#set -x
declare -a arr=("mumbai" "delhi" "kolkata" "bangalore" "chennai" "pune" "hyderabad" "ahmedabad" "remote")
declare -a arrsrc=("COMPANYNAME" "ADDRESS" "PHONE")

for i in "${arr[@]}"
do
	echo $i
	for j in "${arrsrc[@]}"
	do
	echo $j

	cnt=`ps aux | grep [p]hp."/var/www/html/es_script/es_search_anomaly_report_panIndia.php $i $j" | wc -l`
	if [ $cnt -eq 0 ]; then
    	rm -rf /tmp/es_search_anomaly_report_panIndia_$i_$j.lock
    	/bin/php /var/www/html/es_script/es_search_anomaly_report_panIndia.php $i $j > /tmp/es_script_match_log_$i_$j.txt 2>&1&
	elif [ "$cnt" -gt 1 ]; then
    	ps aux | grep [p]hp."/var/www/html/es_script/es_search_anomaly_report_panIndia.php $i $j" | awk '{print $2}' | xargs kill
    	rm -rf /tmp/es_search_anomaly_report_panIndia_$i_$j.lock
    	/bin/php /var/www/html/es_script/es_search_anomaly_report_panIndia.php $i $j > /tmp/es_script_match_log_$i_$j.txt 2>&1&
	elif [ "$cnt" -eq 1 ]; then
    	echo "Es search Php Script is running!!"
	fi

	done

done

exit 0
