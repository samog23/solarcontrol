<?php
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = ""; // 'exito' o 'error'

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    if (!empty($nombre) && !empty($usuario) && !empty($password)) {
        
        // 1. Verificar si el usuario ya existe
        $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
        $stmt_check->execute([':usuario' => $usuario]);

        if ($stmt_check->rowCount() > 0) {
            $mensaje = "El nombre de usuario ya existe. Intenta con otro.";
            $tipo_mensaje = "error";
        } else {
            // 2. Encriptar la contraseña
            $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

            // 3. Guardar el nuevo usuario en la base de datos
            $sql = "INSERT INTO usuarios (nombre, usuario, password) VALUES (:nombre, :usuario, :password)";
            $stmt = $conexion->prepare($sql);

            if ($stmt->execute([':nombre' => $nombre, ':usuario' => $usuario, ':password' => $password_encriptada])) {
                $mensaje = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
                $tipo_mensaje = "exito";
            } else {
                $mensaje = "Ocurrió un error al crear el usuario.";
                $tipo_mensaje = "error";
            }
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario - SolarControl</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; }
        .card h2 { margin-top: 0; color: #1a365d; text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { width: 100%; background: #1a365d; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #2c5282; }
        .mensaje-error { color: red; font-size: 14px; text-align: center; margin-bottom: 10px; }
        .mensaje-exito { color: green; font-size: 14px; text-align: center; margin-bottom: 10px; }
        .link { text-align: center; margin-top: 15px; display: block; color: #1a365d; text-decoration: none; font-size: 14px; }
        .link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <h2>Crear Cuenta</h2>
    
    <?php if (!empty($mensaje)): ?>
        <p class="<?php echo ($tipo_mensaje == 'exito') ? 'mensaje-exito' : 'mensaje-error'; ?>">
            <?php echo $mensaje; ?>
        </p>
    <?php endif; ?>

    <form action="crear_usuario.php" method="POST">
        <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" required placeholder="Ej. Juan Pérez">
        </div>
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="usuario" required placeholder="Ej. juanperez">
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn">Crear Cuenta</button>
    </form>

    <a href="login.php" class="link">¿Ya tienes cuenta? Inicia sesión aquí</a>
</div>

</body>
</html>