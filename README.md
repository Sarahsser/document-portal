# 🎓 University Document Portal

A full-stack web application that allows university students to request and track academic documents online, while giving administrators a dashboard to manage and process those requests.

---

## ✨ Features

- 🔐 Role-based authentication — separate login flows for students and admins
- 📄 Document request form with personal details pre-filled from the database
- 📊 Student dashboard with tabbed document status tracking
- 🛠️ Admin dashboard to view, sort, and update document statuses
- 🌙 Dark mode toggle across all pages
- 📧 Email notifications via PHPMailer when document status changes
- 🔍 Live search/filter on all document tables
- ✅ Server-side validation to prevent data tampering

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML, CSS, Bootstrap 5, JavaScript |
| Backend | PHP (PDO) |
| Database | MySQL |
| Email | PHPMailer + Gmail SMTP |
| Server | Apache (XAMPP) |

---

## 📁 Project Structure

```
document-portal/
├── assets/
│   ├── profile.jpg          # Sidebar profile picture
│   ├── bg-login.jpg         # Login & landing page background
│   └── bg-form.jpg          # Document request form background
├── index.html               # Landing page
├── student-login.php        # Student authentication
├── admin-login.php          # Admin authentication
├── student-dashboard.php    # Student home with document tabs
├── admin-dashboard.php      # Admin home with recent documents
├── form.php                 # Document request form
├── request_document.php     # Form submission handler
├── check_document.php       # Student document status page
├── sort_document.php        # Admin document management
├── notification.php         # Email notification service
├── db_connect.php           # PDO database connection
├── logout.php               # Session destruction
├── style.css                # Dashboard shared styles
├── form.css                 # Form page styles
├── check_document.css       # Document status styles
└── script.js                # Dark mode + tab navigation
```

---

## 🗄️ Database Schema

**Table: `users`**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL,
    email VARCHAR(255),
    full_name VARCHAR(255),
    student_id VARCHAR(50)
);
```

**Table: `documents`**
```sql
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(255),
    dob DATE,
    email VARCHAR(255),
    student_id VARCHAR(50),
    year VARCHAR(10),
    department VARCHAR(100),
    document_type VARCHAR(100),
    copies INT DEFAULT 1,
    preferred_language VARCHAR(50),
    faculty VARCHAR(100),
    request_date DATE,
    due_date DATE,
    status ENUM('requested','in_progress','ready','rejected') DEFAULT 'requested',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 🚀 Running the Project

1. **Clone the repository**
   ```bash
   git clone https://github.com/Sarahsser/document-portal.git
   ```

2. **Set up the server** — install [XAMPP](https://www.apachefriends.org) and start Apache + MySQL

3. **Place files** in `C:/xampp/htdocs/document-portal/`

4. **Create the database** — open `http://localhost/phpmyadmin`, create a database named `DocumentPortal`, then run the SQL above

5. **Configure the connection** — edit `db_connect.php` with your credentials:
   ```php
   $host = 'localhost';
   $dbname = 'DocumentPortal';
   $username = 'root';
   $password = '';
   ```

6. **Seed a user** — visit `http://localhost/document-portal/add_user.php` once, then delete the file

7. **Open the app** at `http://localhost/document-portal/index.html`

---

## 📸 Preview

<!-- Add your demo video here after uploading it to the repo -->
> 🎬 Demo video coming soon

---

## 🔒 Security Notes

- Passwords are hashed with `password_hash()` / `password_verify()`
- All queries use PDO prepared statements (no SQL injection)
- Session-based access control on every protected page
- `db_connect.php` is excluded from version control via `.gitignore`

---

## 👩‍💻 Author

Made with ☕ as part of a university web development project.

**Sarah** — [@Sarahsser](https://github.com/Sarahsser)
