# ⚙️ Lanka Vehicle: Vendor & Supply Chain Management System

### 🤝 Connecting Suppliers to Success

A secure and comprehensive web application built in PHP for managing vendors, tracking product inventory, and streamlining the Purchase Order (PO) process. Designed to facilitate efficient supply chain operations for Lanka Vehicle.

## ✨ Core Features & Functionality

This system supports two distinct user roles: **Admin** (Lanka Vehicle Management) and **Vendor** (External Suppliers).

| Feature Group | Admin Dashboard (`dashboard.php`) | Vendor Dashboard (`dashboard_user.php`) |
| :--- | :--- | :--- |
| **Vendor Management** | ✅ Add, Edit, and Delete vendors. | ❌ (Vendor views their own profile only). |
| **Product Inventory** | ✅ Full CRUD (Create, Read, Update, Delete) for all products. | ✅ Read-only list of products associated with their ID. |
| **Purchase Orders (PO)** | ✅ Create new orders using available products/vendors. | ✅ View all assigned new orders. |
| **Order Tracking** | ✅ View non-delivered orders. | ✅ Track and update the status of assigned orders (Pending, Processing, Shipped). |
| **Delivery Confirmation** | ✅ Mark any non-delivered order as 'Delivered'. | ✅ View history of successfully delivered orders. |

## 💻 Tech Stack

* **Backend:** PHP 7.x/8.x
* **Database:** MySQL/MariaDB
* **Database Access:** PHP Data Objects (PDO) for secure connection and querying
* **Interface:** HTML5 & CSS3 (Requires `style.css` and Font Awesome)

## 🚀 Getting Started

Follow these steps to set up and run the project locally.

### 1. Database Setup

The system connects to a database named `lanka_vehicle`.

1.  **Create Database:** Create a new MySQL database named `lanka_vehicle`.
2.  **Configuration:** Edit `config.php` to match your database credentials:

    ```php
    // config.php
    $host = '127.0.0.1';
    $dbname = 'lanka_vehicle';
    $username = 'root'; // <-- CHANGE THIS
    $password = '';    // <-- CHANGE THIS
    // ...
    ```

3.  **Schema:** While the SQL schema is not provided, the application requires the following tables for full functionality:
    * `vendor` (includes `vendor_ID`, `admin`, `password`, etc.)
    * `product` (includes `product_ID`, `vendor_ID`, `price`, `product_count`, etc.)
    * `purchase_order` (includes `purchase_order_ID`, `vendor_ID`, `product_ID`, `order_state`, etc.)

### 2. Application Deployment

1.  Place all files (`*.php`) and the required `style.css` file into your web server's document root (e.g., XAMPP's `htdocs`).
2.  Ensure PHP sessions are enabled and working correctly.
3.  Access the application via your browser: `http://localhost/index.php` (or similar).

### 3. Login and Usage

* Users are authenticated using PHP sessions.
* The system directs users to either `dashboard.php` (Admin) or `dashboard_user.php` (Vendor) based on their login credentials and the `admin` flag in the `vendor` table.

## 🔒 Security Note

**⚠️ Important:** The current version of `add_vendor.php` stores passwords directly without hashing. For any production or publicly accessible environment, you **MUST** update the code to use secure hashing (e.g., `password_hash()` and `password_verify()`) to protect user data.
👨‍💻 Author

R. P. T. Sandeepa Dilhara (electronic, communication, and IT undergraduate student )
