#Nettoyages
DROP TABLE IF EXISTS races_loch;

#Tables loch pour les courses
CREATE TABLE `races_loch` (
  `time` bigint(20) default NULL,
  `loch` double default NULL,	
  `idusers` bigint(20) NOT NULL,	
  `idraces` bigint(20) NOT NULL,	
KEY `idraces` (`idraces`),
KEY `idusers` (`idusers`),
KEY `entry` (`time`,`idraces`,`idusers`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
  
# On enleve les colonnes 'wind' de 'positions' et 'histpos'
# (Non utilisees, et ne comportant pas toutes les informations qui
# pourraient etre desirables)
ALTER TABLE `histpos` DROP COLUMN `wind`;
ALTER TABLE `positions` DROP COLUMN `wind`;

#On ajoute une clef indexee dans admin_changelog pour pouvoir naviguer plus facilement dedans
ALTER TABLE admin_changelog ADD id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id);

#On ajoute une clef dans races_results pour optimiser les requetes
ALTER TABLE `races_results` ADD KEY(`position`);
ALTER TABLE `races_results` ADD INDEX(`idusers`,`deptime`,`duration`);

#On mets à standard tous les _boats_ admins
UPDATE users SET class = 'standard' WHERE class = 'admin';

#Meilleure indexation de races
ALTER TABLE `races` ADD INDEX filter (started, deptime, closetime);
