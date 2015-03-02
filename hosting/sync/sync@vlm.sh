#!/bin/sh

cd
VLMRACINE=/home/vlm
echo "Passage en maintenance de N2"
~/scripts/maintenance.sh "Bienvenue sur Neptune2, nous rouvrons le service VLM bientôt" > sync1.log
echo "Passage en maintenance de N1"
ssh vlm@v-l-m.org "export VLMRACINE=/home/vlm && /home/vlm/scripts/maintenance.sh \"Ici Neptune1 : VLM vous retrouve bientot sur Neptune2.. Adieu !\"" >>sync1.log
echo "Export datas sur N1"
ssh vlm@v-l-m.org "export VLMRACINE=/home/vlm && /home/vlm/vlmcode/base/scripts/dump-alive.sh /home/vlm/tmp/vlmdump-alive.sql" >>sync1.log
ssh vlm@v-l-m.org "export VLMRACINE=/home/vlm && /home/vlm/vlmcode/base/scripts/dump-history.sh /home/vlm/tmp/vlmdump-history.sql" >>sync1.log
echo "Transport datas de N1 vers N2"
scp vlm@v-l-m.org:/home/vlm/tmp/vlmdump-alive.sql.gz ~/tmp/ >>sync1.log
scp vlm@v-l-m.org:/home/vlm/tmp/vlmdump-history.sql.gz ~/tmp/ >>sync1.log
echo "Import datas sur N2"
~/vlmcode/base/scripts/importdump.sh ~/tmp/vlmdump-alive.sql.gz >>sync1.log
~/vlmcode/base/scripts/importdump.sh ~/tmp/vlmdump-history.sql.gz >>sync1.log
echo "Import OK sur N2, sortie maintenance"
~/scripts/maj_module.sh site >>sync1.log
~/scripts/maj_module.sh lib/phpcommon >>sync1.log
~/scripts/maj_module.sh moteur >>sync1.log
echo "Done, Welcome Neptune2"
