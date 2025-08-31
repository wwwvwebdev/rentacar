-- Add columns to support the "Rent with Driver" feature

-- Add columns to the 'cars' table
ALTER TABLE cars
ADD COLUMN with_driver_available BOOLEAN NOT NULL DEFAULT FALSE AFTER city,
ADD COLUMN driver_rate_per_day DECIMAL(10, 2) DEFAULT 0.00 AFTER price_per_day;

-- Add column to the 'rentals' table
ALTER TABLE rentals
ADD COLUMN with_driver BOOLEAN NOT NULL DEFAULT FALSE AFTER status;
