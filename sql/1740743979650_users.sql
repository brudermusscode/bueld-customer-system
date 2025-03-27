CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(32) NOT NULL UNIQUE,
  employee_id INT NULL,
  password_encrypted TEXT NOT NULL,
  permissions int NOT NULL default 3,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

  INDEX employee_id_idx (employee_id),
  PRIMARY KEY (id)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;