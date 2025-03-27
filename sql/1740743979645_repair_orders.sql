ALTER TABLE repair_orders
  ADD is_leasing TINYINT(1) NULL AFTER type,
  ADD leasing_id INT NULL AFTER is_leasing;