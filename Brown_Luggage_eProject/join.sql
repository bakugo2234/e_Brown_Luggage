use shop_management;



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
    hex_code varchar(100)
);
ALTER TABLE Colors
    ADD hex_code varchar(100);
    
    
CREATE TABLE Sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);
CREATE TABLE Product_Sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    size_id int,
    product_id int,
    foreign key (size_id) references Sizes(id),
    foreign key (product_id) references Products(id)
);


CREATE TABLE Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    category_id INT,
    brand_id INT,
    size_id INT,
    color_id INT, 
    price DECIMAL(10,2),
    gender ENUM('male', 'female', 'unisex'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(id),
    FOREIGN KEY (brand_id) REFERENCES Brands(id),
    FOREIGN KEY (size_id) REFERENCES Sizes(id),
    FOREIGN KEY (color_id) REFERENCES Colors(id)
);

ALTER TABLE Products
MODIFY COLUMN price INT NOT NULL;

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (product_id) REFERENCES Products(id)
);

ALTER TABLE Feedbacks
ADD status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending';

CREATE TABLE Contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(100),
    message TEXT
); 
CREATE TABLE Product_colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    color_id INT,
    FOREIGN KEY (product_id) REFERENCES Products(id),
    FOREIGN KEY (color_id) REFERENCES Colors(id)
);

INSERT INTO Product_colors (product_id, color_id)
VALUES 
(1, 1),
(1, 2),
(1, 2);


INSERT INTO Product_Sizes (product_id, size_id)
VALUES 
(1, 1),
(1, 2),
(1, 2);
ALTER TABLE Products
DROP FOREIGN KEY products_ibfk_3; -- Xóa khóa ngoại liên quan đến color_id (tên khóa có thể khác, kiểm tra trước)

ALTER TABLE Products
DROP COLUMN size_id;

-- Insert into Categories

-- 6
insert into Products (name, description, category_id, brand_id, size_id, color_id, price, gender)
values 
("Balo Herschel Orion", "Very Good", 2, 3, 3, 2, 799.000, "female");

-- 1
insert into Users (name, email, password, phone, address, city, role)
values 
("Hoang Van Hai", "example2@gmail.com", "123456", "0123456", "Ha Noi", "Ha Noi", "customer"),
("Nguyen Van Duy", "example3@gmail.com", "1234567", "01234567", "Ha Noi", "Ha Noi", "customer");

-- 4
insert into Colors (name, hex_code)
values
("black", "#000000");


-- 2
insert into Categories (name)
values 
("Balo"),
("Túi sách"),
("Phụ kiện");


-- 7
insert into Product_images (product_id, image_url, u_primary)
values  
(4, "image/index/vali4.jpg", 1);



-- 8
insert into Feedbacks (user_id, product_id, message, rating)
values  
( 2, 2, "Very Bad", 1),
( 3, 3, "Very Good", 5);


-- 3
insert into Brands (name)
values 
("Larita"),
("Herschel"),
("Pisani");

-- 5
insert into Sizes (name)
values 
("S"),
("M"),
("L"),
("XL"),
("XXL"),
("2XL");

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

