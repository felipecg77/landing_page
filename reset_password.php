<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$phone = isset($input['phone']) ? trim($input['phone']) : (isset($input['telefono']) ? trim($input['telefono']) : '');
$password = isset($input['password']) ? trim($input['password']) : '';

if (empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Teléfono y contraseña son requeridos']);
    exit;
}

// Limpiar teléfono
$phone = preg_replace('/\D/', '', $phone);
if (strlen($phone) === 12 && substr($phone, 0, 2) === '52') {
    $phone = substr($phone, 2);
}

try {
    $host = 'db.yprxfgoimoguymilkluh.supabase.co';
    $port = '5432';
    $dbname = 'postgres';
    $user = 'postgres';
    $pass = 'dSN52XV1bIgyUNn1';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Verificar si el usuario existe
    $stmt = $pdo->prepare("SELECT id FROM public.usuarios WHERE telefono = :phone LIMIT 1");
    $stmt->execute([':phone' => $phone]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userRow) {
        http_response_code(404);
        echo json_encode(['error' => 'El número de teléfono no está registrado']);
        exit;
    }

    $email = $phone . '@comidaparatodos.com';

    // Actualizar contraseña en auth.users usando pgcrypto
    $updateStmt = $pdo->prepare("UPDATE auth.users SET encrypted_password = crypt(:password, gen_salt('bf')) WHERE email = :email");
    $updateStmt->execute([
        ':password' => $password,
        ':email' => $email
    ]);

    echo json_encode(['success' => true, 'message' => 'Contraseña restablecida exitosamente']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
