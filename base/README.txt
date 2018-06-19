UPGRADE d'une version � l'autre

Les instructions sont dans UPDATE, mais depuis la 0.11, la majorit� des modifications sont prise en charge par les scripts mysql et php versionn�s dans SCRIPTS.
Les instructions sp�cifiques d'UPDATE peuvent donc �tre aussi list�es dans /hosting/UPGRADE

SCRIPTS

* init.sh cr�e une base vlm et un user associ� � partir des �l�ments de configuration. Il faut le passwd root de la base mysql pour pouvoir l'utiliser.
* dump.sh et importdump.sh utilisent un dump SQL (i.e. : non binaire) gzipp� pour travailler
* runupgrade.sh prends la version en argument et lance les scripts php et mysql ad-hoc

SCHEMAS

Les tables de VLM sont d�compos�es en plusieurs cat�gories.
NB: Ces exports en grande partie datent de d�cembre 2008. Le mod�le a �volu� depuis.

# Les tables des polygones GSHHS, dans les 5 r�solutions
gshhs_tables.sql

# Tout ce qui concerne la cr�ation d'une course
# Beaucoup plus de d�tails l� : http://wiki.v-l-m.org/index.php/Proposer_des_courses
races_tables.sql

# Les tables applicatives, courses, joueurs, positions, classements...
schema.sql

# Les tables des polaires sont d�sormais inutiles
# Leurs d�finitions sont toujours disponibles
boat_tables.sql

PARAMETRAGE MYSQL

Le fichier *.debian.diff contient les diff�rences avec une Debian Squeeze.


