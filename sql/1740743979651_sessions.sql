CREATE TABLE sessions (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  token TEXT NOT NULL,
  remote_address TEXT NULL,
  browser VARCHAR(32) NULL,
  browser_version VARCHAR(32) NULL,
  browser_title VARCHAR(64) NULL,
  os VARCHAR(32) NULL,
  os_type VARCHAR(32) NULL,
  os_title VARCHAR(64) NULL,
  device_type VARCHAR(32) NULL,
  city VARCHAR(32) NULL,
  postal_code VARCHAR(8) NULL,
  country VARCHAR(32) NULL,
  region VARCHAR(32) NULL,
  continent VARCHAR(32) NULL,
  timezone VARCHAR(32) NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;