# HardwareHub ⚡ - E-Commerce Web Application

HardwareHub is a full-featured, academic-grade E-Commerce Web Application built following the **Model-View-Controller (MVC)** architectural pattern in PHP and MySQL (PDO). The application is designed to simulate a high-performance computer hardware and components store featuring real-time stock verification, session-based shopping cart management, transactional order processing, and administrative inventory controls.

Adheres strictly to the **CIS324 Academic Web Engineering Guidelines**.

---

## 🚀 Key Highlights & Architectural Features

1. **Model-View-Controller (MVC) Architecture:**
   - **Models (`/models`)**: Encapsulates database logic and business rules (`User.php`, `Product.php`, `Order.php`).
   - **Views (`/views`)**: Semantic HTML5 presentation templates sanitized against XSS attacks (`catalog.php`, `product_details.php`, `cart.php`, `checkout.php`, `admin_dashboard.php`, `login.php`, `orders.php`, `error.php`).
   - **Controllers (`/controllers`)**: Coordinates application workflows, validation, and session state (`AuthController.php`, `ProductController.php`, `CartController.php`, `AdminController.php`).
   - **Router (`index.php`)**: Dispatches incoming requests via clean page and action parameter mapping.

2. **Security-First Engineering:**
   - **SQL Injection Prevention:** 100% of all database queries are executed using **PDO Prepared Statements** with strict parameter binding (`PDO::ATTR_EMULATE_PREPARES => false`).
   - **Password Security:** Passwords are cryptographically hashed using standard `password_hash($password, PASSWORD_BCRYPT)` and authenticated with `password_verify()`.
   - **Cross-Site Scripting (XSS) Defense:** All dynamic view outputs are sanitized through `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')` via the global `e()` helper function.
   - **Session Security & Fixation Protection:** Session state is managed via PHP `$_SESSION` with `session_regenerate_id(true)` executed upon login and privilege transitions.
   - **Role-Based Access Control (RBAC):** Strict authentication barriers protect administrative routes. Any non-admin attempting to reach the admin dashboard or inventory actions is redirected to login.
   - **ACID Transactional Checkout:** Order submission executes inside a database transaction (`beginTransaction`, `commit`, `rollBack`) to prevent race conditions and decrement stock atomically.

3. **RESTful JSON API Integration:**
   - Asynchronous JSON endpoint at `/api/check_stock.php` queries the database and delivers live stock data to front-end JavaScript using the modern **Fetch API** (`fetch()`).
   - Dynamically updates product availability and disables the "Add to Cart" button in real-time without reloading the page.

4. **Multi-Page Functional Application:**
   - **Page 1: Catalog Grid (`catalog.php`)** - Product cards, search by name/description, category pills, price & stock sorting.
   - **Page 2: Product Details (`product_details.php`)** - Hardware specifications, interactive quantity selector, live stock badge powered by the Fetch API.
   - **Page 3: Shopping Cart (`cart.php`)** - Session-persisted cart (`$_SESSION['cart']`), item quantity increment/decrement, line-item subtotal, tax calculation, and cart item removal.
   - **Page 4: Checkout Form (`checkout.php`)** - Shipping address submission, mock payment method, order summary review, and transactional inventory commitment.
   - **Page 5: Admin Inventory Dashboard (`admin_dashboard.php`)** - Full CRUD operations (Add, Edit, Update, Delete hardware), KPI metrics (total SKUs, low stock count, sales revenue), and recent customer orders.
   - **Page 6: Authentication Portal (`login.php`)** - Login and registration with one-click academic demo credentials fill buttons.
   - **Page 7: Order History (`orders.php`)** - Customer order tracking and summary.

---

## 📁 Project Directory Structure

```text
Hardwarehub_PhP/
├── index.php                 # Central entry point & request router
├── database.sql              # Normalized database schema & seed dataset
├── README.md                 # Complete documentation & setup instructions
├── .gitignore                # Ignored files
├── /config
│   └── database.php          # Singleton PDO connection with error handling
├── /includes
│   └── helpers.php           # Sanitization (XSS) & currency formatters
├── /models
│   ├── User.php              # User authentication, registration & CRUD
│   ├── Product.php           # Hardware inventory management & queries
│   └── Order.php             # Transactional checkout & order history
├── /views
│   ├── /layout
│   │   ├── header.php        # Semantic navigation, search, cart counter, alerts
│   │   └── footer.php        # Responsive footer & technical credits
│   ├── catalog.php           # Page 1: Product Grid & filtering
│   ├── product_details.php   # Page 2: Single product info & live stock check
│   ├── cart.php              # Page 3: Shopping cart (Session-based)
│   ├── checkout.php          # Page 4: Order checkout form
│   ├── admin_dashboard.php   # Page 5: Inventory management & CRUD
│   ├── login.php             # Login & Registration forms
│   ├── orders.php            # Customer order history
│   └── error.php             # Standardized error presentation (404/403/500)
├── /controllers
│   ├── AuthController.php    # Authentication & session regeneration
│   ├── ProductController.php # Catalog presentation & product routing
│   ├── CartController.php    # Cart state & order processing
│   └── AdminController.php   # Inventory CRUD operations & RBAC enforcement
├── /api
│   └── check_stock.php       # JSON endpoint for JavaScript fetch()
└── /public
    ├── /css
    │   └── style.css         # Responsive dark-slate tech styling
    └── /js
        └── script.js         # Fetch API live stock verification & DOM logic
```

---

## 🛠️ Step-by-Step Setup Guide with XAMPP

### 1. Requirements
- **XAMPP** (with Apache and MySQL / MariaDB installed)
- **PHP 8.0+** (verified compatible with PHP 8.2 and 8.3)
- Modern web browser (Chrome, Edge, Firefox, Brave)

### 2. Place Project in XAMPP
Copy or clone this repository into your XAMPP `htdocs` directory:
```bash
# Path example on Windows:
C:\xampp\htdocs\Hardwarehub_PhP
```
*(Or keep it in your workspace directory and run with PHP Built-in Server `php -S localhost:8000`).*

### 3. Start Apache & MySQL in XAMPP
1. Open the **XAMPP Control Panel**.
2. Click **Start** for **Apache**.
3. Click **Start** for **MySQL**.

### 4. Import the Database in phpMyAdmin
1. Open your browser and navigate to: **[http://localhost/phpmyadmin](http://localhost/phpmyadmin)**.
2. In the top navigation bar, click on **Import**.
3. Click **Choose File** and select the `database.sql` file located in the root of this project.
4. Click **Import** (or **Go**) at the bottom.
5. The `hardwarehub_db` database and normalized tables (`Users`, `Hardware_Products`, `Orders`) will be created automatically with seed data.

> **Default Connection Settings (Pre-configured in `config/database.php`):**
> - **Host:** `127.0.0.1` (or `localhost`)
> - **Port:** `3306`
> - **Database:** `hardwarehub_db`
> - **User:** `root`
> - **Password:** *(empty by default in XAMPP)*

### 5. Access the Web Application
Open your browser and navigate to:
- If hosted in `xampp/htdocs/Hardwarehub_PhP`: **[http://localhost/Hardwarehub_PhP/](http://localhost/Hardwarehub_PhP/)**
- If using PHP CLI server:
  ```bash
  cd "path\to\Hardwarehub_PhP"
  "C:\xampp\php\php.exe" -S localhost:8000
  ```
  Then visit **[http://localhost:8000](http://localhost:8000)**.

---

## 🔐 Academic Demo Credentials

The database script initializes pre-configured accounts for testing:

| Role | Email Address | Password | Privileges |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@hardwarehub.com` | `Admin@123` | Full Access: Inventory CRUD, KPI metrics, All Orders |
| **Customer** | `customer@hardwarehub.com` | `Customer@123` | Store browsing, Cart, Checkout, Order tracking |

*(On the Login screen, click the **Academic Demo Accounts** quick-fill buttons to automatically populate these credentials!)*

---

## 📡 API Endpoint Documentation

### Endpoint: Check Real-Time Stock
- **Route:** `GET /api/check_stock.php?product_id={id}`
- **Headers:** `Content-Type: application/json`
- **Parameters:**
  - `product_id` *(int, required)*: The unique primary key of the hardware item.

#### Example Response (In Stock):
```json
{
    "product_id": 102,
    "stock": 12,
    "status": "in_stock",
    "product_name": "NVIDIA GeForce RTX 4080 Super 16GB",
    "price": 999.99
}
```

#### Example Response (Out of Stock):
```json
{
    "product_id": 108,
    "stock": 0,
    "status": "out_of_stock",
    "product_name": "Lian Li O11 Dynamic EVO Mid-Tower Case",
    "price": 159.9
}
```

#### Front-End JavaScript Implementation (`public/js/script.js`):
```javascript
const response = await fetch(`api/check_stock.php?product_id=${encodeURIComponent(productId)}`);
const data = await response.json();

if (data.stock <= 0) {
    liveBadge.textContent = 'Currently Out of Stock';
    addToCartBtn.disabled = true; // Button disabled dynamically
} else {
    liveBadge.textContent = `In Stock: ${data.stock} units available`;
    addToCartBtn.disabled = false;
}
```

---

## 🧪 Security & Quality Verification Checklist

- [x] **Anti-SQL Injection:** Every SQL statement in `models/` uses `prepare()` and `execute()`. Emulated prepares disabled.
- [x] **Anti-XSS:** All dynamic PHP echo statements pass through `htmlspecialchars()`.
- [x] **Session Fixation:** `session_regenerate_id(true)` executed upon login.
- [x] **Session-Based Cart:** `$_SESSION['cart']` stores item counts and validates stock before checkout.
- [x] **Database Transactions:** Checkout process wraps multiple table operations in ACID transactions.
- [x] **Role-Based Redirection:** Admin portal enforces `$_SESSION['role'] === 'admin'`.
- [x] **Responsive Design:** Mobile, tablet, and desktop adaptive CSS grid and flexbox.
- [x] **PHP Syntax Check:** All files verified with `php -l` without errors.
