CREATE TABLE repairs (
  id INT NOT NULL AUTO_INCREMENT,
  employee_id INT NOT NULL,
  repair_type_id INT NOT NULL,
  paper_id INT NOT NULL,

  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE unq_employee (employee_id),
  UNIQUE unq_repair_type (repair_type_id),
  UNIQUE unq_paper (paper_id)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
