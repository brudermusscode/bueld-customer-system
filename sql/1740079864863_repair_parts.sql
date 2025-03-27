CREATE TABLE repair_parts (
  id INT NOT NULL AUTO_INCREMENT,
  type VARCHAR(12) NOT NULL,
  initial_id INT NULL,
  name TEXT NOT NULL,
  price decimal(10,2) NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ALTER TABLE repair_parts
--   ADD is_default TINYINT DEFAULT 0 AFTER product_id;