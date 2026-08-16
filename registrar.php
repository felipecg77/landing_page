<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$phone = isset($input['telefono']) ? trim($input['telefono']) : '';

if (!empty($phone)) {
    $line = date('Y-m-d H:i:s') . " | " . $phone . "\n";
    file_put_contents('clientes_registrados.txt', $line, FILE_APPEND | LOCK_EX);
    echo json_encode(['status' => 'success', 'telefono' => $phone]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Teléfono vacío']);
}
?>
