CREATE TABLE repair_categories (
  id INT NOT NULL AUTO_INCREMENT,
  initial_id INT NULL,
  description TEXT NOT NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ALTER TABLE repair_types
--   ADD is_default TINYINT DEFAULT 0 AFTER product_id;