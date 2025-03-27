CREATE TABLE papers (
  id INT NOT NULL AUTO_INCREMENT,
  paper_number VARCHAR(48) NOT NULL,
  paper_type VARCHAR(24) NOT NULL default "repair",
  object_type VARCHAR(24) NOT NULL default "bike",
  employee_id INT NOT NULL,
  customer_id INT NOT NULL,
  additions TEXT NULL,

  workers_costs DECIMAL(10,2) NULL,
  expected_costs DECIMAL(10,2) NULL,
  final_costs DECIMAL(10,2) NULL,

  pickup_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;