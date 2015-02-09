UPGRADE d'une version à l'autre

Les instructions sont dans UPDATE, mais depuis la 0.11, la majorité des modifications sont prise en charge par les scripts mysql et php versionnés dans SCRIPTS.
Les instructions spécifiques d'UPDATE peuvent donc être aussi listées dans /hosting/UPGRADE

SCRIPTS

* init.sh crée une base vlm et un user associé à partir des éléments de configuration. Il faut le passwd root de la base mysql pour pouvoir l'utiliser.
* dump.sh et importdump.sh utilisent un dump SQL (i.e. : non binaire) gzippé pour travailler
* runupgrade.sh prends la version en argument et lance les scripts php et mysql ad-hoc

SCHEMAS

Les tables de VLM sont décomposées en plusieurs catégories.
NB: Ces exports en grande partie datent de décembre 2008. Le modèle a évolué depuis.

# Les tables des polygones GSHHS, dans les 5 résolutions
gshhs_tables.sql

# Tout ce qui concerne la création d'une course
# Beaucoup plus de détails là : http://wiki.virtual-loup-de-mer.org/index.php/Proposer_des_courses
races_tables.sql

# Les tables applicatives, courses, joueurs, positions, classements...
schema.sql

# Les tables des polaires sont désormais inutiles
# Leurs définitions sont toujours disponibles
boat_tables.sql

PARAMETRAGE MYSQL

Le fichier *.debian.diff contient les différences avec une Debian Squeeze.


