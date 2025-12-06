-- database.sql

CREATE DATABASE IF NOT EXISTS inventario_whatsapp;
USE inventario_whatsapp;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255), -- Ruta de la imagen
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    customer_address TEXT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Datos de prueba (Opcional)
INSERT INTO categories (name, description) VALUES 
('Tecnología', 'Gadgets y dispositivos'),
('Ropa', 'Moda y estilo'),
('Hogar', 'Artículos para el hogar');

INSERT INTO products (category_id, name, description, price, image, stock) VALUES 
(1, 'Smartphone X', 'Último modelo con cámara 8K.', 999.99, 'assets/img/prod1.jpg', 10),
(2, 'Camiseta Premium', 'Algodón 100% orgánico.', 29.99, 'assets/img/prod2.jpg', 50),
(1, 'Auriculares Pro', 'Cancelación de ruido activa.', 199.50, 'assets/img/prod3.jpg', 20);
