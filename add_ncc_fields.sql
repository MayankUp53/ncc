BEGIN;

ALTER TABLE customer ADD COLUMN caller_age text DEFAULT '不明';
ALTER TABLE customer ADD COLUMN patient_gender text DEFAULT '不明';
ALTER TABLE customer ADD COLUMN family_same_household text DEFAULT '不明';
ALTER TABLE customer ADD COLUMN hospital_visited text DEFAULT '';

ALTER TABLE customer_history ADD COLUMN caller_age text DEFAULT '不明';
ALTER TABLE customer_history ADD COLUMN patient_gender text DEFAULT '不明';
ALTER TABLE customer_history ADD COLUMN family_same_household text DEFAULT '不明';
ALTER TABLE customer_history ADD COLUMN hospital_visited text DEFAULT '';

COMMIT;
