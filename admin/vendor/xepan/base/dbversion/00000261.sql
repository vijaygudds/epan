UPDATE `item` SET `purchase_nominal_id`= nominal_id WHERE (`is_purchasable`= '1' );
UPDATE `item` SET `nominal_id`= 0 WHERE (`is_saleable`='0');