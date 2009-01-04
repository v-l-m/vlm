#!/bin/bash
#ce script n'est utilisé en principe qu'une seule fois à l'install du serveur

source $VLMRACINE/conf/conf_base
echo "CREATE USER '$DBUSER' IDENTIFIED BY '$DBPASSWORD';" > init.sql.tmp
echo "CREATE DATABASE $DBNAME ;" >> init.sql.tmp
echo "GRANT ALL ON $DBNAME.* TO '$DBUSER' ;" >> init.sql.tmp
echo "CREATE DATABASE temporary ;" >> init.sql.tmp
echo "GRANT ALL ON temporary.* TO '$DBUSER' ;" >> init.sql.tmp

echo "Tapez votre mot de passe root"
mysql -u root -p < init.sql.tmp

echo "L'utilisateur $DBUSER a été créé avec le mot de passe $DBPASSWORD"
echo "Il a tous les droits sur la base $DBNAME"

rm init.sql.tmp