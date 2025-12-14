# Code Quality Analysis Report

## 1. Executive Summary
The analyzed codebase represents a legacy or entry-level PHP application. It exhibits significant structural, stylistic, and maintainability issues that make it difficult to scale, test, and secure. The application follows a procedural style mixed with some object-oriented patterns, but lacks a coherent architecture (like MVC).

**Overall Rating**: **Poor**
**Recommendation**: A complete rewrite using a modern framework (Laravel, Symfony) is recommended over refactoring, given the depth of the structural issues.

## 2. Structural Analysis

### Architecture
*   **Pattern**: The application does not follow a recognized architectural pattern (e.g., MVC, ADR). It relies on "Page Controller" logic where each PHP file (`index.php`, `view_prod.php`) handles its own logic and presentation.
*   **Routing**: Routing is primitive, relying on query parameters (e.g., `index.php?page=home`) to include files dynamically. This is fragile and prone to Local File Inclusion (LFI) if not strictly validated (though the current implementation `include $page.'.php'` is dangerous).
    *   *Risk*: `index.php` blindly includes `$page . '.php'`. If a user passes `../../etc/passwd` (and the server is configured to allow it, though the suffix `.php` mitigates non-PHP files), it could lead to issues. More realistically, it allows executing any PHP file on the system.

### Database Interaction
*   **Coupling**: Database logic is tightly coupled with presentation. SQL queries are scattered throughout view files (e.g., `view_prod.php`).
*   **Config**: Database credentials are hardcoded in `admin/db_connect.php`.
*   **Pattern**: There is an attempt at a DAO/Service pattern in `admin/admin_class.php` (the `Action` class), but it acts as a "God Class", handling everything from user authentication to product management and cart operations.

### Frontend
*   **Organization**: CSS and JS are mixed. Some are in external files (`css/styles.css`), but substantial logic is inline within PHP files (e.g., `index.php` contains a large `<script>` block).
*   **Dependencies**: Dependencies like jQuery and Bootstrap are manually included (some via CDN, some local), making version management difficult.

## 3. Code Style & Readability

### Naming Conventions
*   **Inconsistency**: Variables use snake_case (`$first_name`, `$login_user_id`), which is standard for PHP, but class methods also use snake_case (`save_user`, `add_to_cart`) instead of the PSR-1/PSR-12 standard camelCase (`saveUser`, `addToCart`).
*   **Clarity**: Some variable names are generic (`$qry`, `$save`, `$data`).

### Logic & Flow
*   **Spaghetti Code**: PHP logic (DB queries, session handling) is interleaved with HTML. This makes the code hard to read and impossible to unit test.
    *   *Example*: `index.php` starts a session, queries the DB for settings, defines CSS styles dynamically, and then outputs HTML.
*   **DRY Violations**: The modal logic (HTML structure for modals) is repeated in both `index.php` and `admin/index.php`.

### Modern Standards (PSR)
*   **Violations**:
    *   No Namespaces: Classes are in the global namespace.
    *   No Autoloading: Files are manually included.
    *   Side Effects: Files like `admin/db_connect.php` execute code (connect to DB) immediately upon inclusion, rather than just declaring symbols.

## 4. Maintainability & Error Handling

### Error Handling
*   **Lack of Try-Catch**: The code uses `or die(...)` style error handling in `db_connect.php`, which halts execution and exposes system details to the user.
*   **Silent Failures**: In `admin/ajax.php`, specific actions just echo the result of the `Action` class methods. If those methods fail silently or return unexpected values, the frontend AJAX handles it with generic error logging.

### Hardcoding
*   **Credentials**: DB credentials are hardcoded.
*   **Paths**: URLs are often hardcoded or relative, which breaks if the project structure changes.

## 5. Specific "Code Smells"

1.  **God Object (`admin/admin_class.php`)**: The `Action` class handles *all* AJAX requests. It has too many responsibilities (SRP violation).
2.  **Global State**: widespread use of `$_SESSION` global array directly in views.
3.  **Extract usage**: The use of `extract($_POST)` is a major maintenance headache (unknown variables appearing in scope) and a security flaw.
4.  **Inline Javascript**: Large chunks of logic in `<script>` tags inside `.php` files (e.g., `login.php`, `index.php`) make frontend debugging hard.

## 6. Recommendations

1.  **Adopt a Framework**: Move to Laravel or Symfony to get MVC structure, routing, and ORM out of the box.
2.  **Separate Concerns**:
    *   Move DB logic to Repository classes.
    *   Move business logic to Service classes.
    *   Keep Views (HTML) dumb.
3.  **Implement Coding Standards**: Enforce PSR-12 using tools like PHP_CodeSniffer.
4.  **Configuration Management**: Move credentials to `.env` files.
