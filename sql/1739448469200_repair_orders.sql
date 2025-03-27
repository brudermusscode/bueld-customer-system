CREATE TABLE repair_orders (
  id INT NOT NULL AUTO_INCREMENT,
  reference_id VARCHAR(32) NOT NULL,
  customer_id INT NOT NULL,
  employee_id INT NOT NULL,
  type VARCHAR(12) NOT NULL default "bike",

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ALTER TABLE repair_orders
--   ADD is_default TINYINT DEFAULT 0 AFTER product_id;