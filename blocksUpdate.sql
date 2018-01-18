ALTER TABLE `blocks` DROP `categorie_target`;
ALTER TABLE `blocks` CHANGE `id_target` `id_block` BIGINT(20) UNSIGNED NOT NULL;