<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// =========================================================================
// CONTRASEÑA DE ACCESO AL PANEL DE CLIENTES (Puedes cambiarla aquí)
// =========================================================================
define('ADMIN_PASSWORD', 'Comida2026!'); 

// Manejo de Cierre de Sesión
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_auth']);
    header('Location: ver_clientes.php');
    exit;
}

// Manejo de Inicio de Sesión
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_auth'] = true;
    } else {
        $error = 'Contraseña incorrecta. Acceso denegado.';
    }
}

$is_authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;
$file = 'clientes_registrados.txt';
$registros = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración de Clientes - Comida Para Todos</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 90vh; margin: 0; }
        .container { max-width: 800px; width: 100%; background: #1e293b; padding: 32px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.6); border: 1px solid #334155; }
        h1 { color: #f97316; margin-top: 0; text-align: center; font-size: 1.8rem; }
        .badge { background: #10b981; color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #0f172a; color: #10b981; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; }
        tr:hover { background: rgba(255,255,255,0.05); }
        .empty { text-align: center; color: #94a3b8; padding: 40px; }
        .btn-download { display: inline-block; background: #10b981; color: white; text-decoration: none; padding: 10px 18px; border-radius: 10px; font-weight: bold; }
        .btn-logout { background: #ef4444; color: white; text-decoration: none; padding: 10px 18px; border-radius: 10px; font-weight: bold; }
        .login-box { max-width: 400px; margin: 0 auto; text-align: center; }
        .input-pwd { width: 100%; padding: 14px; border-radius: 10px; border: 1px solid #334155; background: #0f172a; color: white; font-size: 1rem; box-sizing: border-box; margin-bottom: 16px; outline: none; }
        .input-pwd:focus { border-color: #10b981; }
        .btn-login { width: 100%; background: #f97316; color: white; border: none; padding: 14px; border-radius: 10px; font-weight: bold; font-size: 1rem; cursor: pointer; }
        .btn-login:hover { background: #ea580c; }
        .error-msg { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$is_authenticated): ?>
            <!-- Formulario de Autenticación de Seguridad -->
            <div class="login-box">
                <div style="font-size: 3rem; margin-bottom: 10px;">🔒</div>
                <h1>Acceso Protegido</h1>
                <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 24px;">Ingresa la contraseña de administrador para ver la lista de clientes pre-registrados.</p>
                
                <?php if (!empty($error)): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="ver_clientes.php">
                    <input type="password" name="password" placeholder="Contraseña de administrador" required class="input-pwd" autofocus>
                    <button type="submit" class="btn-login">Ingresar al Panel</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Panel Autorizado -->
            <h1>📊 Panel de Clientes Pre-registrados</h1>
            <p style="text-align: center; color: #94a3b8;">Lista privada de usuarios interesados en el lanzamiento del <strong>01/11/2026</strong></p>
            <hr style="border-color: #334155; margin: 20px 0;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <span class="badge">Total Registrados: <?php echo count($registros); ?></span>
                <div>
                    <a href="clientes_registrados.txt" download class="btn-download">📥 Descargar TXT</a>
                    <a href="ver_clientes.php?action=logout" class="btn-logout">🔒 Salir</a>
                </div>
            </div>

            <?php if (empty($registros)): ?>
                <div class="empty">Aún no hay clientes registrados.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha y Hora</th>
                            <th>Teléfono WhatsApp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach (array_reverse($registros) as $linea): 
                            $parts = explode(' | ', $linea);
                            $fecha = isset($parts[0]) ? $parts[0] : '-';
                            $tel = isset($parts[1]) ? $parts[1] : $linea;
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($fecha); ?></td>
                            <td><strong><?php echo htmlspecialchars($tel); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
