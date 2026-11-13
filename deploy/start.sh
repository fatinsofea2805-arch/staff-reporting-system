#!/bin/sh
set -eu
mkdir -p /var/data/uploads/profile_pictures
# Seed only on first deployment. Never overwrite the live SQLite database.
if [ ! -f /var/data/ptjpm.sqlite ]; then
    cp /var/www/html/data/ptjpm.sqlite /var/data/ptjpm.sqlite
fi
if [ ! -f /var/data/.uploads-seeded ]; then
    cp -an /var/www/html/uploads/. /var/data/uploads/
    touch /var/data/.uploads-seeded
fi
if [ ! -L /var/www/html/uploads ]; then
    rm -rf /var/www/html/uploads
    ln -s /var/data/uploads /var/www/html/uploads
fi
chown -R www-data:www-data /var/data
php <<'PHP'
<?php
$password = getenv('ADMIN_RESET_PASSWORD');

if ($password !== false && $password !== '') {
    if (strlen($password) < 12) {
        fwrite(STDERR, "Admin password must have at least 12 characters.\n");
        exit(1);
    }

    require '/var/www/html/connection.php';

    $stmt = $dbCon->prepare(
        "UPDATE admin
         SET ADMIN_PASSWORD = ?, LOGGED_IN = 0
         WHERE ADMIN_EMAIL = ?"
    );

    if (!$stmt || !$stmt->execute([
        password_hash($password, PASSWORD_DEFAULT),
        'admin2025@gmail.com'
    ])) {
        fwrite(STDERR, "Admin password reset failed.\n");
        exit(1);
    }
}
PHP

unset ADMIN_RESET_PASSWORD
exec apache2-foreground
