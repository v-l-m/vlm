#!/bin/bash

date +%s > ~vlm/tmp/s1
date +%s > ~vlm/tmp/s2
date +%s > ~vlm/tmp/s3
date +%s > ~vlm/tmp/s4
date +%s > ~vlm/tmp/s5
mailq >> ~vlm/tmp/s1
df -h >> ~vlm/tmp/s2
mysql -e "show status" >> ~vlm/tmp/s3
mysql -e "show variables" >> ~vlm/tmp/s3
mysql vlm -e "select unix_timestamp(time),races,boats,duration from updates where races<>0 order by time desc limit 5;" >> ~vlm/tmp/s4
ntpq -p </dev/null >> ~vlm/tmp/s5 2>&1