#!/bin/sh

cp /etc/mysql/my.cnf.binlog /etc/mysql/my.cnf
/etc/init.d/mysql restart
/etc/init.d/apache2 restart
