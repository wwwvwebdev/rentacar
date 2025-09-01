-- Add columns to the 'users' table for document verification

ALTER TABLE users
ADD COLUMN cnic_image_path VARCHAR(255) NULL,
ADD COLUMN license_image_path VARCHAR(255) NULL,
ADD COLUMN is_verified BOOLEAN NOT NULL DEFAULT FALSE;
