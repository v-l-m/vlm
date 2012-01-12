#Suppression des tracks cachées
UPDATE `users` SET color = SUBSTR(color, 2) WHERE LEFT(color, 1) = '-';
