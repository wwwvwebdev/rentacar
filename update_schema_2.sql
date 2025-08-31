-- Add city column to the cars table
ALTER TABLE cars ADD COLUMN city VARCHAR(100) NOT NULL AFTER model;
