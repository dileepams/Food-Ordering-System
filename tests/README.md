# Testing

This directory contains the automated tests for the application.

## Prerequisites

- PHP 8.0 or higher
- MySQL
- Composer

## Setup

1.  **Install Dependencies:**
    ```bash
    composer install
    ```

2.  **Create a Test Database:**
    - Create a MySQL database named `fos_db`.
    - Import the `database/fos_db.sql` file into the `fos_db` database.

3.  **Create a Test User:**
    - Create a MySQL user with the following credentials:
        - **Username:** `testuser`
        - **Password:** `password`
    - Grant the user `ALL PRIVILEGES` on the `fos_db` database.

4.  **Configure `phpunit.xml`:**
    - Copy `phpunit.xml.dist` to `phpunit.xml`.
    - If your database credentials are a secret, you can remove `phpunit.xml` from the `.gitignore` file.

## Running Tests

To run the tests, execute the following command from the root of the project:

```bash
vendor/bin/phpunit
```
