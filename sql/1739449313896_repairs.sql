CREATE TABLE repairs (
  id INT NOT NULL AUTO_INCREMENT,
  repair_order_id INT NOT NULL,
  repair_type_id INT NOT NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ALTER TABLE repairs
--   ADD is_default TINYINT DEFAULT 0 AFTER product_id;
