ALTER TABLE leasing_companies
  ADD requires_inspection_id TINYINT(1) NULL after logo_path,
  ADD web_url TEXT NULL AFTER logo_path

  ;