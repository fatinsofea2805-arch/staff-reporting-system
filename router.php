<?php
// Use this router with PHP's development server to protect private data files.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$root = realpath(__DIR__);
$file = realpath(__DIR__ . $path);
if (preg_match('~(?:^|/)(?:data|\.vscode|Mail)(?:/|$)|(?:^|/)\.|\.(?:sqlite(?:-[a-z]+)?|db|sql|bat|md|ini|log)$~i', $path)
    || in_array(basename($path), ['connection.php', 'database.php', 'router.php'], true)
    || ($file !== false && $file !== $root && strpos($file, $root . DIRECTORY_SEPARATOR) !== 0)) {
    http_response_code(404);
    exit('Not found');
}
if ($path === '/') {
    header('Location: homepage.html');
    exit;
}
if (preg_match('~^/uploads/.*\.(?:php\d*|phtml|phar)$~i', $path)) {
    http_response_code(404);
    exit('Not found');
}
return false;
