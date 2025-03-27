-- CREATE TABLE bookmarks (
--   id INT NOT NULL AUTO_INCREMENT,

--   deleted_at TIMESTAMP NULL,
--   updated_at TIMESTAMP NULL,
--   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE bookmarks
  ADD type VARCHAR(24) NOT NULL AFTER id,
  CHANGE customer_id reference_id INT NOT NULL,
  ADD INDEX reference_id_idx (reference_id);