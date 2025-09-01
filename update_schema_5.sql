-- Create settings table and add initial WhatsApp number

CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_name VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT
);

INSERT INTO settings (setting_name, setting_value) VALUES ('whatsapp_number', '923000000000');
