-- CREATE TABLE repair_parts (
--   id INT NOT NULL AUTO_INCREMENT,

--   deleted_at TIMESTAMP NULL,
--   updated_at TIMESTAMP NULL,
--   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE repair_parts
  ADD article_id VARCHAR(224) NULL AFTER initial_id;