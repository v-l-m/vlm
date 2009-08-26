#!/bin/bash

# Ce script doit etre executable.
if [ -x /opt/php/bin/php ];
then
   PHP=/opt/php/bin/php
else 
   PHP=php
fi

# Appel aux scripts de recuperation des positions des reels
cd $VLMRACINE/vlmcode/lib/phpcommon

# Pym et Benoit
$PHP $VLMRACINE/vlmcode/moteur/posscripts/oeposNyLo.php


