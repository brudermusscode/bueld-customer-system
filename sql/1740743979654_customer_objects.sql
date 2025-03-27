ALTER TABLE customer_objects
  ADD UNIQUE object_unique_identifier_idx (brand_id, object_unique_identifier);