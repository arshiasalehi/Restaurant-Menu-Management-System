# 🍽️ Restaurant Dish & Order Management System

**Restaurant Dish & Order Management System** is a PHP + MySQL web application for managing dishes and customer orders in a clean, responsive interface.  
It supports transactional inserts, dynamic order rows, dish availability badges, and a sortable overview of dish popularity.

Built using **PHP**, **MySQL**, **Tailwind CSS**, and **vanilla JavaScript**, the app is lightweight, fast, and easy to extend for real-world use.

---

## 🚀 Features

### 🥘 Dish & Order Management
- Create **dishes** and **orders** in the same form  
- Add multiple orders dynamically using JavaScript  
- Transaction-safe inserts using PDO  
- Overview page showing:
  - Total orders per dish  
  - Availability badges (In Stock / Out of Stock)  
  - Automatic sorting by popularity  

### 🎨 Frontend & UI
- Responsive UI built with **Tailwind CSS via CDN**  
- Hover effects, gradient backgrounds, clean styling  
- Dynamic “Add Order” rows with vanilla JS  

### 🗄️ Database & Structure
- MySQL database with:
  - Foreign keys  
  - ENUM fields  
  - Pre-seeded sample data  
- Clean relational schema for dishes & orders  

---

# 💻 Tech Stack

## 🖥️ Backend
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

- **PHP 8.x** runtime  
- **PDO**: prepared statements, transactions, exceptions  
- CRUD operations for dishes & orders  

## 🎨 Frontend
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwindcss&logoColor=white)
![HTML](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)

- Tailwind CSS via CDN  
- Semantic, responsive HTML  
- Vanilla JS for dynamic form behaviour  

## 🧰 Dev Tools
![VSCode](https://img.shields.io/badge/VS_Code-007ACC?style=for-the-badge&logo=visualstudiocode&logoColor=white)
![SQLTools](https://img.shields.io/badge/SQLTools-5C2D91?style=for-the-badge&logo=datagrip&logoColor=white)

- SQLTools preset for quick DB access  
- Local MySQL development server  

---

# 🧠 Architecture Overview

## 🎨 Presentation Layer
- Tailwind-styled HTML pages  
- Dynamic order rows using JavaScript  
- Availability badges & hover styling  

## ⚙️ Business Logic
- Insert dishes & orders in a **single transaction**  
- Fetch and aggregate dish order totals  
- Automatic sorting by total orders  
- ENUM-based dish availability  

## 🗄️ Data Access Layer
- PDO connection (MySQL)  
- Prepared statements (secure)  
- Foreign key constraints  
- Seeded initial dataset  

---

# 🛠️ Setup Instructions

1. **Import the Database**
   ```bash
   mysql < restaurant.sql
   ```

2. **Configure Database Credentials**  
   In your PHP config file:
   ```php
   $dsn = 'mysql:host=localhost;dbname=restaurant;charset=utf8';
   $user = 'root';
   $pass = '';
   ```

3. **Start Local PHP Server**
   ```bash
   php -S localhost:8000
   ```

4. Visit the app:  
   👉 http://localhost:8000

5. (Optional)  
   Open **SQLTools** in VS Code to browse/edit the DB.

---

# 📊 Project Stats

| Metric               | Value                         |
|----------------------|-------------------------------|
| 🧑‍💻 Main Language     | PHP                           |
| 🗃️ Database           | MySQL with FK & ENUMs         |
| 🎨 UI Framework       | Tailwind CSS (CDN)            |
| 📁 Structure          | Minimal + modular PHP         |
| ⏳ Development Time   | ~1–2 days                     |

---

# 📚 Top Languages Used

![PHP](https://img.shields.io/badge/PHP-70%25-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQL](https://img.shields.io/badge/SQL-20%25-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![HTML](https://img.shields.io/badge/HTML-10%25-E34F26?style=for-the-badge&logo=html5&logoColor=white)

---

# 👥 Team Members

- [**Arshia Salehi**](https://github.com/arshiasalehi)
