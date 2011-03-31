#!/bin/bash
source $VLMRACINE/conf/conf_script || exit 1

echo "Sending test message to $1..."

$VLMPHPPATH -r "mail(\"$1\", \"Test de messagerie VLM\", \"SUCCESS !\");"

echo "...Done. Check your inbox."
