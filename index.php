<?php

require_once 'GenPassword.php';
require_once 'PasswordGeneratorService.php';
require_once 'PasswordValidator.php';

header('Content-Type: application/json');

$service   = new PasswordGeneratorService();
$validator = new PasswordValidator();

try {
    $fullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method   = $_SERVER['REQUEST_METHOD'];

    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path      = '/' . ltrim(substr($fullPath, strlen($scriptDir)), '/');

    $path = preg_replace('#^/index\.php#', '', $path);
    if ($path === '' || $path === false) {
        $path = '/';
    }

    if ($path === '/') {
        echo json_encode([
            'api'       => 'Password Generator API',
            'endpoints' => [
                'GET  /api/password'          => 'Genera una contrasena. Params: length, includeUppercase, includeLowercase, includeNumbers, includeSymbols, excludeAmbiguous, exclude',
                'POST /api/passwords'         => 'Genera varias contrasenas. Body JSON: { count, length, ... }',
                'POST /api/password/validate' => 'Valida una contrasena. Body JSON: { password, requirements: { minLength, requireUppercase, requireNumbers, requireSymbols } }',
            ],
        ], JSON_PRETTY_PRINT);
        exit;
    }

    // GET /api/password
    if ($path === '/api/password' && $method === 'GET') {
        $password = $service->generate($_GET);
        echo json_encode([
            'success'  => true,
            'password' => $password,
            'length'   => strlen($password),
        ]);
        exit;
    }

    // POST /api/passwords
    if ($path === '/api/passwords' && $method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            throw new InvalidArgumentException('El cuerpo de la peticion debe ser un JSON valido');
        }
        $passwords = $service->generateMultiple($input);
        echo json_encode([
            'success'   => true,
            'count'     => count($passwords),
            'passwords' => $passwords,
        ]);
        exit;
    }

    // POST /api/password/validate
    if ($path === '/api/password/validate' && $method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input) || !isset($input['password'])) {
            throw new InvalidArgumentException('Se requiere el campo "password" en el cuerpo JSON');
        }
        $result = $validator->validate($input['password'], $input['requirements'] ?? []);
        echo json_encode($result);
        exit;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Endpoint no encontrado', 'path_recibido' => $path]);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
