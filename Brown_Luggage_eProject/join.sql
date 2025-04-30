create database shop_management;
USE shop_management;

-- Create Users table (assumed structure since not provided)
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    role ENUM('customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create independent tables
CREATE TABLE Categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE Brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE Colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    hex_code VARCHAR(100)
);

CREATE TABLE Sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

-- Create Products table (without size_id and color_id to avoid later ALTER)
CREATE TABLE Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    category_id INT,
    brand_id INT,
    price INT NOT NULL, -- Modified to INT as per ALTER
    gender ENUM('male', 'female', 'unisex'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(id),
    FOREIGN KEY (brand_id) REFERENCES Brands(id)
);

-- Create many-to-many relationship tables
CREATE TABLE Product_Sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    size_id INT,
    product_id INT,
    FOREIGN KEY (size_id) REFERENCES Sizes(id),
    FOREIGN KEY (product_id) REFERENCES Products(id)
);

CREATE TABLE Product_colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    color_id INT,
    FOREIGN KEY (product_id) REFERENCES Products(id),
    FOREIGN KEY (color_id) REFERENCES Colors(id)
);

-- Create other dependent tables
CREATE TABLE Product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_url VARCHAR(255),
    u_primary TINYINT(1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Products(id)
);

CREATE TABLE Orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    status ENUM('pending', 'processing', 'shipped', 'completed', 'cancelled'),
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id)
);

CREATE TABLE Order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES Orders(id),
    FOREIGN KEY (product_id) REFERENCES Products(id)
);

CREATE TABLE Payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    amount DECIMAL(10,2),
    status ENUM('pending', 'completed', 'failed'),
    payment_method ENUM('credit_card', 'paypal', 'bank_transfer', 'cod'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES Orders(id)
);

CREATE TABLE Carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (product_id) REFERENCES Products(id)
);

CREATE TABLE Feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    message TEXT,
    rating INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (product_id) REFERENCES Products(id)
);

CREATE TABLE Contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(100),
    message TEXT
);

-- Insert data into independent tables
INSERT INTO Users (name, email, password, phone, address, city, role)
VALUES 
    ('Hoang Van Hai', 'example2@gmail.com', '123456', '0123456', 'Ha Noi', 'Ha Noi', 'customer'),
    ('Nguyen Van Duy', 'example3@gmail.com', '1234567', '01234567', 'Ha Noi', 'Ha Noi', 'customer');

INSERT INTO Categories (name)
VALUES 
    ('Vali'),
    ('Balo'),
    ('Túi sách'),
    ('Phụ kiện');

INSERT INTO Brands (name)
VALUES 
    ('Larita'),
    ('Herschel'),
    ('Pisani');

INSERT INTO Colors (name, hex_code)
VALUES 
    ('white', '#FFFFFF'),
    ('black', '#000000'),
    ('gray', '#808080');

INSERT INTO Sizes (name)
VALUES 
    ('S'),
    ('M'),
    ('L'),
    ('XL'),
    ('XXL'),
    ('2XL');

-- Insert into Products
INSERT INTO Products (name, description, category_id, brand_id, price, gender)
VALUES 
    -- Vali (category_id = 1, brand_id = 2)
    ('Vali Herschel 1', 'Vali cao cấp', 1, 2, 1200000, 'unisex'),
    ('Vali Herschel 2', 'Vali cao cấp', 1, 2, 1250000, 'unisex'),
    ('Vali Herschel 3', 'Vali cao cấp', 1, 2, 1300000, 'unisex'),
    ('Vali Herschel 4', 'Vali cao cấp', 1, 2, 1350000, 'unisex'),
    ('Vali Herschel 5', 'Vali cao cấp', 1, 2, 1400000, 'unisex'),
    ('Vali Herschel 6', 'Vali cao cấp', 1, 2, 1450000, 'unisex'),
    ('Vali Herschel 7', 'Vali cao cấp', 1, 2, 1500000, 'unisex'),
    ('Vali Herschel 8', 'Vali cao cấp', 1, 2, 1550000, 'unisex'),
    -- Balo (category_id = 2, brand_id = 1)
    ('Balo Larita 1', 'Balo thời trang', 2, 1, 799000, 'female'),
    ('Balo Larita 2', 'Balo thời trang', 2, 1, 829000, 'female'),
    ('Balo Larita 3', 'Balo thời trang', 2, 1, 849000, 'female'),
    ('Balo Larita 4', 'Balo thời trang', 2, 1, 869000, 'female'),
    ('Balo Larita 5', 'Balo thời trang', 2, 1, 889000, 'female'),
    ('Balo Larita 6', 'Balo thời trang', 2, 1, 899000, 'female'),
    ('Balo Larita 7', 'Balo thời trang', 2, 1, 919000, 'female'),
    ('Balo Larita 8', 'Balo thời trang', 2, 1, 939000, 'female'),
    -- Túi sách (category_id = 3, brand_id = 3)
    ('Túi Pisani 1', 'Túi sách da', 3, 3, 599000, 'male'),
    ('Túi Pisani 2', 'Túi sách da', 3, 3, 619000, 'male'),
    ('Túi Pisani 3', 'Túi sách da', 3, 3, 639000, 'male'),
    ('Túi Pisani 4', 'Túi sách da', 3, 3, 659000, 'male'),
    ('Túi Pisani 5', 'Túi sách da', 3, 3, 679000, 'male'),
    ('Túi Pisani 6', 'Túi sách da', 3, 3, 699000, 'male'),
    ('Túi Pisani 7', 'Túi sách da', 3, 3, 719000, 'male'),
    ('Túi Pisani 8', 'Túi sách da', 3, 3, 739000, 'male'),
    -- Additional product

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM Products 
WHERE name = 'Balo Herschel Orion' 
AND description = 'Very Good' 
AND category_id = 2
AND brand_id = 3 
AND price = 799000 
AND gender = 'female';
SET FOREIGN_KEY_CHECKS = 1;
-- Insert into Product_Sizes
INSERT INTO Product_Sizes (product_id, size_id)
VALUES 
    (1, 2), (1, 3),
    (2, 2), (2, 3),
    (3, 2), (3, 3),
    (4, 2), (4, 3),
    (5, 2), (5, 3),
    (6, 2), (6, 3),
    (7, 2), (7, 3),
    (8, 2), (8, 3),
    (9, 1), (9, 2),
    (10, 1), (10, 2),
    (11, 1), (11, 2),
    (12, 1), (12, 2),
    (13, 1), (13, 2),
    (14, 1), (14, 2),
    (15, 1), (15, 2),
    (16, 1), (16, 2),
    (17, 1), (17, 2),
    (18, 1), (18, 2),
    (19, 1), (19, 2),
    (20, 1), (20, 2),
    (21, 1), (21, 2),
    (22, 1), (22, 2),
    (23, 1), (23, 2),
    (24, 1), (24, 2),
    (25, 3); -- Balo Herschel Orion

-- Insert into Product_colors
INSERT INTO Product_colors (product_id, color_id)
VALUES 
    (1, 1), (1, 2), (1, 3),
    (2, 2), (2, 3),
    (3, 1), (3, 3),
    (4, 2),
    (5, 1), (5, 2),
    (6, 3),
    (7, 2), (7, 3),
    (8, 1), (8, 2), (8, 3),
    (9, 1), (9, 2),
    (10, 2), (10, 3),
    (11, 1), (11, 3),
    (12, 2),
    (13, 1), (13, 2),
    (14, 3),
    (15, 2), (15, 3),
    (16, 1), (16, 2), (16, 3),
    (17, 1), (17, 2),
    (18, 2), (18, 3),
    (19, 1), (19, 3),
    (20, 2),
    (21, 1), (21, 2),
    (22, 3),
    (23, 2), (23, 3),
    (24, 1), (24, 2), (24, 3),
    (25, 2); -- Balo Herschel Orion

-- Insert into Product_images
INSERT INTO Product_images (product_id, image_url, u_primary)
VALUES 
    (1, 'image/index/vali1.jpg', 1),
    (2, 'image/index/vali2.jpg', 1),
    (3, 'image/index/vali3.jpg', 1),
    (4, 'image/index/vali4.jpg', 1),
    (5, 'image/index/vali5.jpg', 1),
    (6, 'image/index/vali6.jpg', 1),
    (7, 'image/index/vali7.jpg', 1),
    (8, 'image/index/vali8.jpg', 1),
    (9, 'image/index/balo1.jpg', 1),
    (10, 'image/index/balo2.jpg', 1),
    (11, 'image/index/balo3.jpg', 1),
    (12, 'image/index/balo4.jpg', 1),
    (13, 'image/index/balo5.jpg', 1),
    (14, 'image/index/balo6.jpg', 1),
    (15, 'image/index/balo7.jpg', 1),
    (16, 'image/index/balo8.jpg', 1),
    (17, 'image/index/tuisach1.jpg', 1),
    (18, 'image/index/tuisach2.jpg', 1),
    (19, 'image/index/tuisach3.jpg', 1),
    (20, 'image/index/tuisach4.jpg', 1),
    (21, 'image/index/tuisach5.jpg', 1),
    (22, 'image/index/tuisach6.jpg', 1),
    (23, 'image/index/tuisach7.jpg', 1),
    (24, 'image/index/tuisach8.jpg', 1),
    (25, 'image/index/balo_orion.jpg', 1);

-- Insert into Feedbacks
INSERT INTO Feedbacks (user_id, product_id, message, rating, status)
VALUES 
    (3, 1, 'Very Bad', 1, 'approved'),
    (3, 2, 'Very Good', 5, 'approved');







SELECT 
    p.id AS product_id,
    p.name AS product_name,
    p.price AS product_price,
  
    GROUP_CONCAT(c.hex_code) AS colour_names,
    
    pi.image_url AS product_image,
    AVG(f.rating) AS average_rating
FROM 
    Products p
    INNER JOIN Colors c ON  c.id
    LEFT JOIN Product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
    LEFT JOIN Feedbacks f ON p.id = f.product_id
GROUP BY 
    p.id, p.name, p.price, pi.image_url
ORDER BY 
    p.id;

SELECT p.id, p.name, c.hex_code
FROM Products p
LEFT JOIN Colors c ON c.id = p.color_id;

DESCRIBE Users;

SELECT 
    c.id AS cart_id,
    c.product_id,
    c.quantity,
    p.name AS product_name,
    p.price AS product_price,
    pi.image_url AS product_image,
    GROUP_CONCAT(DISTINCT sizes.name ORDER BY sizes.name) AS size_names,
    
    GROUP_CONCAT(cl.hex_code) AS colour_hex_code
FROM 
    Carts c
    JOIN Products p ON c.product_id = p.id
    LEFT JOIN Product_Images pi ON p.id = pi.product_id AND pi.u_primary = 1
    LEFT JOIN Product_Sizes ps ON p.id = ps.product_id
    LEFT JOIN Sizes sizes ON ps.size_id = sizes.id
    LEFT JOIN Product_colors pc ON p.id = pc.product_id
    LEFT JOIN Colors cl ON pc.color_id = cl.id
WHERE 
    c.user_id = 2
GROUP BY 
    c.id, c.product_id, c.quantity, p.name, p.price, pi.image_url;
