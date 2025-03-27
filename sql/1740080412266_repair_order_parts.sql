CREATE TABLE repair_order_parts (
  id INT NOT NULL AUTO_INCREMENT,
  repair_order_id INT NOT NULL,
  repair_part_id INT NOT NULL,
  custom_field TEXT NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ALTER TABLE repair_order_parts
--   ADD is_default TINYINT DEFAULT 0 AFTER product_id;