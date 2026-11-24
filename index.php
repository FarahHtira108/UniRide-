<?php
// Front controller : tout passe par index.php?page=xxx
session_start();
spl_autoload_register(function ($c) {
    $f = __DIR__ . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $c) . '.php';
    if (is_file($f)) require $f;
});
require __DIR__ . '/app/helpers.php';
$routes = require __DIR__ . '/app/routes.php';
$page = $_GET['page'] ?? 'home';
if (!isset($routes[$page])) { http_response_code(404); $page = '404'; }
[$ctl, $method] = $routes[$page];
$class = "App\\Controllers\\$ctl";
(new $class)->$method();
