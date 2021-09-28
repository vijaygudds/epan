ALTER TABLE `contact` ADD COLUMN `score`  int(11) NULL DEFAULT NULL;
UPDATE contact SET score = (SELECT SUM(score) FROM point_system Where contact_id = contact.id);