#!/bin/bash

# Script by : rahul dhakad
# Created from scratch on : <30-03-2018>  Track Ticket No: 
# Technical stake holder : <nitesh nilmbalkar from rajeev nair team>
# Contact no of technical stake holder : <Nitesh Nimbalkar => 4180>
# Business stake holder : <Rajeev nair>
# Purpose / Description : sp_mis_report process
#----------------------------------------------------
# Last modified on : 30-03-2018
# Last modified by : rahul dhakad 

SSH_ARGS="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"
EMAIL_ID="rahul.dhakad@justdial.com,nitesh.nimbalkar@justdial.com,pravinkumar.yadav@justdial.com,mahendra.seth1@justdial.com"
#EMAIL_ID="rahul.dhakad@justdial.com"
HNAME="$(hostname | cut -d. -f1)"
SUBJECT="mis_report process for $(date -d "-1 month" +%B-%Y)"
CITYDGT=$(/sbin/ip route | grep src |awk '{print $9}'| cut -d. -f3|head -1)
LOCALUSER="process_auto"
HOST="localhost"
LOCALPASSWD="gr8exp9master"
DATE=`date +%d-%m-%Y`
data_last_month=`date -d "-1 month" +%B-%Y`
LOGFILE="/var/log/daily_log/mis_report.log"
CTID_3_OCT=$(sh /scripts/active_ip.sh  | awk 'FNR == 3 {print $1}')
CTID_12_OCT=$(sh /scripts/active_ip.sh  | awk 'FNR == 2 {print $1}')
CTID_123_OCT=$(sh /scripts/active_ip.sh  | awk 'FNR == 1 {print $1}')
CITY_NAME_INI=$(/bin/grep ${CTID_123_OCT} /scripts/pan_genio_ip.ini | awk -F '|' '{print $1}')

# ------ Function Define ------ #

function CHECK () {
if [ $? -eq 0 ]; then
        echo "Success"
#        /scripts/standard_sms_sending_script.sh -m " ${SUBJECT} completed sucessfully on 192.168.14.49 " -f /scripts/sms-number_campaign_report.txt
else
        echo "Failed"
        #echo "${SUBJECT} failed on `date`"  | mail -s "${SUBJECT} failed on `date`" ${EMAIL_ID}
#        echo -e  "Mis_report process failed `date` \n $(cat $LOGFILE) " |  mail -s "Mis_report process failed" ${EMAIL_ID}
        echo "Mis_report process failed..attaching error logs "| mail -a /var/log/daily_log/mis_report.log -s "attached file" ${EMAIL_ID}
        /scripts/standard_sms_sending_script.sh -m " ${SUBJECT} failed on 192.168.14.49 " -f /scripts/sms-number_campaign_report.txt
        exit 1
fi
}

CITYLST="mumbai delhi kolkata bangalore chennai pune hyderabad ahmedabad remote"
#CITYLST="kolkata"

for CITY in $CITYLST;
do

	echo "drop table tbl_es_search_data_src_$CITY on $HOST"
	sudo mysql -Bse "DROP TABLE IF EXISTS db_lead.tbl_es_search_data_src_$CITY;"
	#CHECK
	echo "create table tbl_es_search_data_src_$CITY on $HOST"
	sudo mysql -Bse "CREATE TABLE db_lead.tbl_es_search_data_src_$CITY (source_parentid VARCHAR(100) DEFAULT NULL,source_field VARCHAR(100) DEFAULT NULL, match_percent VARCHAR(50) DEFAULT NULL,dest_parentid VARCHAR(100) DEFAULT NULL,dest_field VARCHAR(255) DEFAULT NULL,field_type VARCHAR(50) DEFAULT NULL,source VARCHAR(50) DEFAULT NULL,KEY(source_parentid),KEY(dest_parentid), KEY(field_type));"
	#CHECK
	echo "Inserting data es_anomaly_report_src_comp_$CITY.txt in db_lead on $HOST"
	mysql -Bse "LOAD DATA INFILE \"/tmp/es_anomaly_report_src_comp_${CITY}.txt\" INTO TABLE db_lead.tbl_es_search_data_src_${CITY} fields terminated by '\#' enclosed by '\n' IGNORE 1 LINES;"
	#CHECK
	echo "Inserting data es_anomaly_report_src_add_$CITY.txt in db_lead on $HOST"
	mysql -Bse "LOAD DATA INFILE \"/tmp/es_anomaly_report_src_add_${CITY}.txt\" INTO TABLE db_lead.tbl_es_search_data_src_${CITY} fields terminated by '\#' enclosed by '\n' IGNORE 1 LINES;"
	#CHECK
	echo "Inserting data es_anomaly_report_src_phone_$CITY.txt in db_lead on $HOST"
	mysql -Bse "LOAD DATA INFILE \"/tmp/es_anomaly_report_src_phone_${CITY}.txt\" INTO TABLE db_lead.tbl_es_search_data_src_${CITY} fields terminated by '\#' enclosed by '\n' IGNORE 1 LINES;"
	#CHECK
	
	
	echo "drop table tbl_es_search_data_dest_$CITY on $HOST"
	sudo mysql -Bse "DROP TABLE IF EXISTS db_lead.tbl_es_search_data_dest_$CITY;"
	#CHECK
	echo "create table tbl_es_search_data_dest_$CITY on $HOST"
	sudo mysql -Bse "CREATE TABLE db_lead.tbl_es_search_data_dest_$CITY (source_parentid VARCHAR(100) DEFAULT NULL,source_field VARCHAR(100) DEFAULT NULL, match_percent VARCHAR(50) DEFAULT NULL,dest_parentid VARCHAR(100) DEFAULT NULL,dest_field VARCHAR(255) DEFAULT NULL,field_type VARCHAR(50) DEFAULT NULL,source VARCHAR(50) DEFAULT NULL,KEY(source_parentid),KEY(dest_parentid), KEY(field_type));"
	#CHECK
	echo "Inserting data es_anomaly_report_dest_comp_$CITY.txt in db_lead on $HOST"
	mysql -Bse "LOAD DATA INFILE \"/tmp/es_anomaly_report_dest_comp_${CITY}.txt\" INTO TABLE db_lead.tbl_es_search_data_dest_${CITY} fields terminated by '\#' enclosed by '\n' IGNORE 1 LINES;"
	#CHECK
	echo "Inserting data es_anomaly_report_dest_add_$CITY.txt in db_lead on $HOST"
	mysql -Bse "LOAD DATA INFILE \"/tmp/es_anomaly_report_dest_add_${CITY}.txt\" INTO TABLE db_lead.tbl_es_search_data_dest_${CITY} fields terminated by '\#' enclosed by '\n' IGNORE 1 LINES;"
	#CHECK
	echo "Inserting data es_anomaly_report_dest_phone_$CITY.txt in db_lead on $HOST"
	mysql -Bse "LOAD DATA INFILE \"/tmp/es_anomaly_report_dest_phone_${CITY}.txt\" INTO TABLE db_lead.tbl_es_search_data_dest_${CITY} fields terminated by '\#' enclosed by '\n' IGNORE 1 LINES;"
	#CHECK

done
echo "DONE"
exit 0
