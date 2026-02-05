<?php

declare(strict_types=1);

session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'auth_profile_system');
define('DB_USER', 'root');
define('DB_PASS', ''); 


function redirect(string $path): void {
    header("Location: {$path}");
    exit;
}


function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
