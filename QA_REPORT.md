# QA Report: Food Ordering System

## Executive Summary
This report outlines the findings from a comprehensive Quality Assurance (QA) review of the Food Ordering System project. The review focused on code quality, security, and functionality.

**Overall Status**: **CRITICAL**
The application contains multiple critical security vulnerabilities that make it unsafe for production use. It requires immediate and significant refactoring to address SQL injection, insecure authentication, and arbitrary file upload vulnerabilities.

## Critical Vulnerabilities

### 1. SQL Injection (CWE-89)
**Severity**: **Critical**
**Description**: The application systematically uses user input directly in SQL queries without parameterization or escaping. This allows an attacker to manipulate queries to bypass authentication, access unauthorized data, or modify the database.
**Locations**:
- `admin/admin_class.php`: Methods `login`, `login2`, `save_user`, `save_settings`, `save_category`, `save_menu`, `add_to_cart`, `save_order`.
- `view_prod.php`: `$_GET['id']` is used directly in SQL.
- `admin/view_order.php`: `$_GET['id']` is used directly in SQL.
**Example (`admin/admin_class.php`)**:
```php
extract($_POST);
$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".$password."' ");
```
**Recommendation**: Use **Prepared Statements** (e.g., using `mysqli::prepare` and `bind_param`) for all database queries involving user input. Remove `extract($_POST)`.

### 2. Insecure Password Hashing (CWE-327)
**Severity**: **Critical**
**Description**: Passwords are hashed using the obsolete and insecure MD5 algorithm. MD5 is vulnerable to collision attacks and rainbow table lookups.
**Locations**:
- `admin/admin_class.php`: `login2`, `signup`, `save_user`.
**Example**:
```php
$data .= ", password = '".md5($password)."' ";
```
**Recommendation**: Use `password_hash()` (uses Bcrypt/Argon2) for storing passwords and `password_verify()` for checking them.

### 3. Arbitrary File Upload (CWE-434)
**Severity**: **Critical**
**Description**: The application allows file uploads without validating the file type or extension. The uploaded file is renamed based on a timestamp but retains its extension if not handled carefully (though the code appends `$_FILES['img']['name']`, effectively keeping the extension). A malicious user could upload a PHP shell (e.g., `image.php`) and execute arbitrary code on the server.
**Locations**:
- `admin/admin_class.php`: `save_settings`, `save_menu`.
**Example**:
```php
$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
```
**Recommendation**: Validate the MIME type and file extension against a whitelist (e.g., `.jpg`, `.png`). Store uploaded files outside the web root if possible, or prevent execution of scripts in the upload directory.

### 4. Cross-Site Scripting (XSS) (CWE-79)
**Severity**: **High**
**Description**: User input is reflected back to the browser without adequate sanitization. While some parts use `htmlentities`, it is not consistently applied, and `extract($_POST)` makes it hard to track variable origins.
**Locations**:
- `admin/admin_class.php`: `save_settings` (partial mitigation).
- `view_prod.php`, `index.php`: `$_GET` parameters reflected.
**Recommendation**: Sanitize all output using `htmlspecialchars()` or a context-aware escaping library.

### 5. Variable Pollution / Scope Injection
**Severity**: **High**
**Description**: The use of `extract($_POST)` allows an attacker to overwrite any variable in the current scope, potentially changing logic flow or privilege levels.
**Locations**: Widespread in `admin/admin_class.php`.
**Recommendation**: Explicitly access `$_POST['variable_name']`.

## Code Quality & Maintainability

1.  **Dependency Management**: The project lacks a `composer.json` or any dependency management.
2.  **Testing**: There are no automated tests.
3.  **Architecture**: The code mixes logic, database access, and presentation (HTML/PHP spaghetti code). This makes it hard to test and maintain.
4.  **Database**: The schema lacks foreign key constraints, which could lead to data inconsistency.

## Recommendations for Next Steps

1.  **Immediate Security Fixes**:
    *   Replace all dynamic SQL with prepared statements.
    *   Switch from `md5` to `password_hash`.
    *   Implement strict file upload validation.
2.  **Refactoring**:
    *   Remove `extract($_POST)`.
    *   Separate logic from views.
3.  **Testing**:
    *   Implement a test suite (Unit and Integration) to prevent regressions.

## Conclusion
The application in its current state is vulnerable to trivial exploitation. It is recommended to freeze feature development and focus entirely on remediation of the security issues identified above.
