-- Add status column to the rentals table
ALTER TABLE rentals
ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending' AFTER total_price;
