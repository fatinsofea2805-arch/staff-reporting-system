# Staff Reporting System — PHP + SQLite

This version uses a local SQLite database through PHP PDO. No MySQL server,
phpMyAdmin, WAMP or XAMPP is needed. The existing interface is retained.

## Start on Windows

1. Extract the entire archive into a writable folder.
2. Install standalone PHP 8.1 or newer and add its folder to your Windows PATH.
   PHP 8.4 was used for verification. PHP itself is still required.
3. In the PHP installation folder, copy `php.ini-development` to `php.ini` if
   you do not have a php.ini. Enable these settings (remove the leading `;`):

   ```ini
   extension_dir = "ext"
   extension=pdo_sqlite
   ```

   Use `php --ini` to check which php.ini is loaded.
   Use `php -m` to check that `PDO` and `pdo_sqlite` are listed.
4. Double-click `Start_App.bat` inside the project folder.
5. Open http://127.0.0.1:8000 in your browser. Keep the command window open.
   Press Ctrl+C to stop the server.

Alternatively, open a terminal inside the project folder and run:

```sh
php -d upload_max_filesize=5M -d post_max_size=8M -S 127.0.0.1:8000 router.php
```

Use the router shown above; it prevents downloading the database and SQL dumps.
The built-in PHP server is intended for local development/demo use.
The interface still loads some fonts, styles and icons from online CDNs.

## Existing accounts and data

The supplied SQL data is already imported into `data/ptjpm.sqlite`:

| Table | Imported records |
| --- | ---: |
| admin | 1 |
| staff | 9 |
| emergency | 15 |
| lateness | 12 |
| holiday | 3 |

Use your existing account email and password. Password hashes are preserved;
no new default password was introduced. The imported admin `LOGGED_IN` flag
was reset to 0, because the previous MySQL session is no longer active.

Use the admin logout button when finished. The original single-admin login rule
is retained, including its LOGGED_IN flag.

## Database and backups

- `connection.php`: PDO SQLite connection; no database username or password.
- `database.php`: parameter binding and buffered SELECT result helpers.
- `data/ptjpm.sqlite`: live database. Keep this file; do not import the MySQL
  dump into SQLite.
- `data/schema.sql`: converted SQLite schema for reference.
- `data/original_mysql.sql`: original MySQL dump for reference.
- `uploads/`: existing uploaded attachments and profile pictures.

Stop the server before backing up. Copy both `data/ptjpm.sqlite` and `uploads/`
to preserve records and attachments. The data folder and database must be writable.
Missing database files produce an error rather than silently creating an empty one.
New default timestamps use Malaysia time (UTC+8); imported timestamps are preserved.
An optional `STAFF_DB_PATH` environment variable can select another existing SQLite
file for testing or deployment.

## Changes

- Replaced MySQLi access with PDO SQLite across the application.
- Converted MySQL table definitions, ENUM rules, indexes and date/hour queries.
- Kept staff/holiday IDs as text when viewing, updating and deleting records.
- Bound holiday inserts to the logged-in admin, satisfying required ADMIN_ID.
- Removed SQL escaping from bound parameters to avoid doubled apostrophes.
- Standardized the updated lateness status to `Not Seen`.
- Preserved existing attachments when editing without a new upload.
- Saved the selected leave date when editing an emergency request.

## Verification

See `TEST_RESULTS.md` for the checks performed. This is a database migration;
existing authentication/authorization and password-recovery design were not
redesigned.
