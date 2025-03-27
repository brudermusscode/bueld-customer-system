-- CREATE TABLE repair_order_parts (
--   id INT NOT NULL AUTO_INCREMENT,

--   deleted_at TIMESTAMP NULL,
--   updated_at TIMESTAMP NULL,
--   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE repair_order_parts
  ADD price DECIMAL(10,2) DEFAULT 0.00 AFTER custom_field;