<?php

$config = require __DIR__ . '/config.php';

try {
    $dsn = "sqlsrv:Server={$config['server']},{$config['port']};"
         . "Database={$config['database']};"
         . "Encrypt=yes;TrustServerCertificate=yes";

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos',
        'error'   => $e->getMessage(),
    ]);
    exit();
}

return $pdo;