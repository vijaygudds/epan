ALTER TABLE `employee_attandance` ADD COLUMN `late_coming` int(11) DEFAULT 0;
ALTER TABLE `employee_attandance` ADD COLUMN `early_leave` int(11) DEFAULT 0;
ALTER TABLE `employee_attandance` ADD COLUMN `total_work_in_mintues` int(11) DEFAULT 0;
ALTER TABLE `employee_attandance` ADD COLUMN `total_movement_in` int(11) DEFAULT 0;
ALTER TABLE `employee_attandance` ADD COLUMN `total_movement_out` int(11) DEFAULT 0;