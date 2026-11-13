@echo off
cd /d "%~dp0"
where php >nul 2>nul
if errorlevel 1 (
  echo PHP was not found. Install PHP and add its folder to PATH. See README.md.
  pause
  exit /b 1
)
php -r "exit(extension_loaded('pdo_sqlite') ? 0 : 1);"
if errorlevel 1 (
  echo Enable extension=pdo_sqlite in php.ini first. See README.md.
  pause
  exit /b 1
)
echo Open http://127.0.0.1:8000 in your browser.
echo Keep this window open. Press Ctrl+C to stop.
php -d upload_max_filesize=5M -d post_max_size=8M -S 127.0.0.1:8000 router.php
pause
