<?php

// Obtenemos la ruta y el método
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Ajusta esto si tu API vive en una subcarpeta (ej: /api)
$basePath = '';
$uri = substr($uri, strlen($basePath));
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

// Body del request (JSON) disponible para cualquier método que lo necesite
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// Buscar coincidencia
if (isset($routes[$method][$uri])) {
    [$class, $action] = $routes[$method][$uri];
    $controller = new $class();
    $controller->$action($body);
} else {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Ruta no encontrada'
    ]);
}