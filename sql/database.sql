-- Fashion & Clothing Store Database
CREATE DATABASE IF NOT EXISTS fashion_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fashion_store;

CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(255),
    size VARCHAR(100),
    color VARCHAR(100),
    category ENUM('mens','womens','kids','accessories') NOT NULL,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    order_status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
    shipping_name VARCHAR(100),
    shipping_address TEXT,
    shipping_phone VARCHAR(20),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    size VARCHAR(20),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    size VARCHAR(20),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (customer_id, product_id)
);

CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    comment TEXT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Seed Admin (password: admin123)
INSERT INTO customers (fullname, email, password, role) VALUES
('Admin', 'admin@fashionstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Seed Men's Products
INSERT INTO products (title, description, price, sale_price, image, size, color, category, stock) VALUES
('Classic Oxford Shirt', 'Premium cotton Oxford shirt with button-down collar, perfect for business casual', 59.99, 44.99, 'mens_shirt.jpg', 'S,M,L,XL,XXL', 'White,Blue,Grey', 'mens', 30),
('Slim Fit Chinos', 'Modern slim-fit chino trousers made from stretch cotton blend', 79.99, NULL, 'mens_chinos.jpg', '28,30,32,34,36', 'Beige,Navy,Olive', 'mens', 25),
('Leather Biker Jacket', 'Genuine leather biker jacket with asymmetric zip and quilted shoulders', 299.99, 249.99, 'mens_jacket.jpg', 'S,M,L,XL', 'Black,Brown', 'mens', 12),
('Essential White Tee', 'Classic heavyweight white t-shirt in 100% organic cotton', 24.99, NULL, 'mens_tee.jpg', 'S,M,L,XL,XXL', 'White,Black,Grey', 'mens', 50),
('Wool Blend Blazer', 'Sophisticated wool-blend blazer, fully lined with two-button fastening', 189.99, 149.99, 'mens_blazer.jpg', 'S,M,L,XL', 'Charcoal,Navy', 'mens', 18),

-- Seed Women's Products
('Floral Wrap Dress', 'Elegant wrap dress in floral print, perfect for any occasion', 89.99, 69.99, 'womens_dress.jpg', 'XS,S,M,L,XL', 'Floral Pink,Floral Blue', 'womens', 22),
('High-Waist Jeans', 'Flattering high-waist skinny jeans with stretch comfort fabric', 74.99, NULL, 'womens_jeans.jpg', '24,26,28,30,32', 'Dark Blue,Light Blue,Black', 'womens', 35),
('Silk Blouse', 'Luxurious 100% silk blouse with relaxed fit and button front', 129.99, 99.99, 'womens_blouse.jpg', 'XS,S,M,L', 'Ivory,Dusty Rose,Sage', 'womens', 16),
('Cashmere Sweater', 'Ultra-soft cashmere v-neck sweater, ethically sourced', 199.99, NULL, 'womens_sweater.jpg', 'XS,S,M,L,XL', 'Camel,Cream,Black', 'womens', 20),
('Trench Coat', 'Classic double-breasted trench coat with belt, a timeless wardrobe staple', 249.99, 199.99, 'womens_trench.jpg', 'XS,S,M,L,XL', 'Camel,Black', 'womens', 14);
