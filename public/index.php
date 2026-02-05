<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

if (Auth::check()) {
    redirect('profile.php');
}
redirect('login.php');
