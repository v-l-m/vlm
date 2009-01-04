#!/bin/bash

source $VLMRACINE/conf/conf_base

mysql -h $DBSERVER -u $DBUSER --password=$DBPASSWORD $DBNAME > vlmdump.sql
