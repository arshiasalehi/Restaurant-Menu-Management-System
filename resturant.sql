DROP DATABASE IF EXISTS resturant;
CREATE DATABASE resturant CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE resturant;

CREATE TABLE IF NOT EXISTS dishes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category ENUM('Appetizer', 'Main Course', 'Dessert', 'Beverage') NOT NULL,
    price DECIMAL(6,2) NOT NULL,
    description TEXT,
    available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dish_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    order_date DATE NOT NULL,
    status ENUM('Pending', 'Completed', 'Cancelled') NOT NULL,
    CONSTRAINT fk_orders_dish FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO dishes (name, category, price, description, available)
VALUES
    ('Caesar Salad', 'Appetizer', 8.50, 'Crisp romaine lettuce, parmesan, croutons, and house Caesar dressing.', 1),
    ('Grilled Chicken Burger', 'Main Course', 12.99, 'Grilled chicken breast burger with lettuce, tomato, and aioli.', 1),
    ('Chocolate Brownie', 'Dessert', 5.75, 'Warm chocolate brownie served with vanilla ice cream.', 1),
    ('Fresh Orange Juice', 'Beverage', 3.50, 'Freshly squeezed orange juice.', 1);

INSERT INTO orders (dish_id, customer_name, quantity, order_date, status)
VALUES
    (1, 'John Smith', 2, CURRENT_DATE, 'Pending'),
    (2, 'Jane Doe', 1, CURRENT_DATE, 'Pending'),
    (3, 'Mike Johnson', 3, CURRENT_DATE, 'Pending');
