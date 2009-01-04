#!/bin/bash

source $VLMRACINE/conf/conf_base

mysqldump -h $DBSERVER -u $DBUSER --password=$DBPASSWORD $DBNAME > vlmdump.sql
