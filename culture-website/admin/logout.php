<?php
require_once __DIR__ . '/../config/app.php';

session_unset();
session_destroy();

redirect(APP_URL . '/admin/login.php');
