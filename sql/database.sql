CREATE DATABASE IF NOT EXISTS marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE marketplace;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image_url VARCHAR(500) DEFAULT NULL,
  category VARCHAR(80) NOT NULL,
  `condition` VARCHAR(40) NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  seller_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_seller FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cart (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  UNIQUE KEY uq_cart_user_product (user_id, product_id),
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(32) NOT NULL DEFAULT 'pending',
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

INSERT INTO users (name, email, password_hash, is_admin) VALUES
('Admin User', 'admin@example.com', '$2y$12$pjenrOjJleYrtLhkISlqUeHtf7j7ZQe.omWgXJs3wap.7.5D04z2u', 1);

INSERT INTO products (name, description, price, image_url, category, `condition`, stock, seller_id) VALUES
('Vintage turntable', 'Belt-drive, tested working.', 129.99, 'https://placeimg.abh.ai/things/640/480?sfsdfsf=sdf', 'Electronics', 'Good', 2, 1),
('Jazz vinyl LP', 'Original pressing.', 24.50, 'https://placeimg.abh.ai/things/640/480?random=234aa', 'Vinyl', 'Very Good', 5, 1),
('Denim jacket', 'Size M, light wear.', 45.00, 'https://placeimg.abh.ai/things/640/480?raasd=asd', 'Clothing', 'Good', 1, 1),
('Sony headphones', 'Noise-cancelling, includes case.', 89.99, 'https://placeimg.abh.ai/things/640/480?pfokg=sdf', 'Electronics', 'Like New', 4, 1),
('Programming textbook', 'Clean pages, no highlights.', 32.00, 'https://placeimg.abh.ai/things/640/480?rqweqw=-we', 'Books', 'Very Good', 3, 1),
('Rock vinyl bundle', 'Three LPs, sleeves intact.', 55.00, 'https://placeimg.abh.ai/things/640/480?raxcas=asd', 'Vinyl', 'Good', 2, 1),
('Leather messenger bag', 'Brown, fits 15-inch laptop.', 78.50, 'https://placeimg.abh.ai/things/640/480?nghf=asd', 'Clothing', 'Good', 1, 1),
('Retro game console', 'Tested with two controllers.', 199.00, 'https://placeimg.abh.ai/things/640/480?iquweh=qweqwe', 'Electronics', 'Fair', 1, 1),
('Sci-fi paperback lot', 'Five books, bundle price.', 18.00, 'https://placeimg.abh.ai/things/640/480?awiouuihdqw=qsas', 'Books', 'Acceptable', 6, 1),
('Concert poster', 'Limited print, stored flat.', 40.00, 'https://placeimg.abh.ai/things/640/480?asd1d=qwqbbc', 'Collectibles', 'Very Good', 2, 1),
('Bluetooth speaker', 'Portable, USB-C charge.', 49.99, 'https://placeimg.abh.ai/things/640/480?12we3=11', 'Electronics', 'Like New', 5, 1),
('Wool scarf', 'Handmade, grey.', 22.00, 'https://placeimg.abh.ai/things/640/480?znmxbc=12', 'Clothing', 'New', 4, 1),
('CD box set', 'Classical, 10 discs.', 35.00, 'https://placeimg.abh.ai/things/640/480?lkmjs=13', 'CDs', 'Good', 2, 1),
('Film camera body', '35mm, light meter works.', 110.00, 'https://placeimg.abh.ai/things/640/480?qasdwqe=14', 'Electronics', 'Good', 1, 1),
('Vintage pins set', 'Enamel collector pins.', 15.00, 'https://placeimg.abh.ai/things/640/480?qwertt=15', 'Collectibles', 'Very Good', 8, 1);
