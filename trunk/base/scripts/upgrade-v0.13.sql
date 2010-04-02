# On allonge le champ, pour capter les adresses 
### => FM / fait le 30/03/2010 "en manuel" pour mise en place en V_0_12
ALTER TABLE `user_action` MODIFY `ipaddr` VARCHAR(255);
#Création d'un champ plus long
ALTER TABLE `user_action` ADD `fullipaddr` VARCHAR(255);



