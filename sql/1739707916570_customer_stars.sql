CREATE TABLE customer_stars (
  id INT NOT NULL AUTO_INCREMENT,
  customer_id INT NOT NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ALTER TABLE customer_stars
--   ADD is_default TINYINT DEFAULT 0 AFTER product_id;