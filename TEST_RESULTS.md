# Verification

Tested with PHP 8.4.27 and PDO SQLite using a separate copy of the database.
The tests did not add test accounts or test records to the delivered database.

- All 40 PHP files passed PHP syntax checks.
- All 40 imported records were compared against the supplied SQL values before
  resetting the transient admin login flag. Password hashes were preserved.
- SQLite integrity check passed.
- 50 HTTP checks passed: home/login pages; staff registration; rejected incorrect
  password; successful staff and admin login; staff/admin dashboards and lists;
  profile update with an apostrophe; lateness, emergency and holiday create/edit/
  delete; Seen statuses; search filters including apostrophes; admin logout;
  blocked requests to the database, schema, original dump and connection helpers.
- Newly generated alphanumeric IDs worked for profile update and holiday deletion.
- Separate multipart checks verified lateness and emergency uploads and preservation
  of their attachment filenames when editing without a replacement upload.
- Malaysia timestamp defaults and SQLite uniqueness/ENUM constraints were checked.

The checks exercise PHP handlers over HTTP, not a manual browser layout review.
Uploaded attachment display, external CDN availability, and legacy password
recovery behaviour are not covered by the main HTTP checks.
