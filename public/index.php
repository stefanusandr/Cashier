<?php
/**
 * SmartKasir - Front Controller (public/index.php)
 * Mengatur PSR-4 Autoloading dan Router HTTP Request.
 */

declare(strict_types=1);

// 1. Standarisasi dan Autoloader Sederhana
spl_autoload_register(function ($class) {
    $prefix = 'SmartKasir\\';
    $baseDir = __DIR__ . '/../Src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $parts = explode('\\', $relativeClass);

    // Ubah nama folder namespace pertama ke huruf kecil agar cocok dengan sistem folder Src/
    if (count($parts) > 0) {
        $parts[0] = strtolower($parts[0]);
    }

    $file = $baseDir . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Dukungan Serving Static Assets pada PHP Built-in Server
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . $uri;
    if (is_file($filePath)) {
        return false; // Serahkan file langsung ke server
    }
}

use SmartKasir\Controllers\AuthController;
use SmartKasir\Controllers\DashboardController;
use SmartKasir\Controllers\POSController;
use SmartKasir\Controllers\ItemController;

// 3. Simple Front Controller Router
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
    case '':
    case '/':
    case '/login':
        $controller = new AuthController();
        if ($requestMethod === 'POST') {
            $controller->processLogin();
        } else {
            $controller->showLoginForm();
        }
        break;

    case '/logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case '/dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    case '/pos':
        $controller = new POSController();
        $controller->index();
        break;

    case '/pos/search':
        $controller = new POSController();
        $controller->searchItem();
        break;

    case '/items':
        $controller = new ItemController();
        $controller->index();
        break;

    default:
        http_response_code(404);
        echo "<div style='font-family: sans-serif; text-align: center; padding: 4rem;'>
                <h1 style='color: #ef4444;'>404 - Halaman Tidak Ditemukan</h1>
                <p>Halaman yang Anda tuju tidak tersedia.</p>
                <a href='/dashboard' style='color: #4f46e5; text-decoration: none; font-weight: bold;'>&larr; Kembali ke Beranda</a>
              </div>";
        break;
}
