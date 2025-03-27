CREATE TABLE customer_objects (
  id INT NOT NULL AUTO_INCREMENT,
  type VARCHAR(12) NOT NULL,
  customer_id INT NOT NULL,
  brand_id INT NOT NULL,
  object_unique_identifier VARCHAR(128) NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  INDEX customer_id_idx (customer_id),
  UNIQUE object_unique_identifier_idx (type, object_unique_identifier),
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;