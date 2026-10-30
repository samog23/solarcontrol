<?php
session_start();
require_once 'conexion.php';

// Verificar inicio de sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$tipo_mensaje = "";

// LÓGICA PARA AGREGAR CITA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    $id_cita         = trim($_POST['id_cita']);
    $cliente         = trim($_POST['cliente']);
    $telefono        = trim($_POST['telefono']);
    $direccion       = trim($_POST['direccion']);
    $fecha           = trim($_POST['fecha']);
    $hora            = trim($_POST['hora']);
    $tipo_cita       = trim($_POST['tipo_cita']);
    $asesor_asignado = trim($_POST['asesor_asignado']);
    $estudio_area    = trim($_POST['estudio_area']);
    $estudio_cita    = trim($_POST['estudio_cita']);

    if (!empty($id_cita) && !empty($cliente) && !empty($fecha)) {
        try {
            $sql = "INSERT INTO citas (id_cita, cliente, telefono, direccion, fecha, hora, tipo_cita, asesor_asignado, estudio_area, estudio_cita) 
                    VALUES (:id_cita, :cliente, :telefono, :direccion, :fecha, :hora, :tipo_cita, :asesor_asignado, :estudio_area, :estudio_cita)";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':id_cita'         => $id_cita,
                ':cliente'         => $cliente,
                ':telefono'        => $telefono,
                ':direccion'       => $direccion,
                ':fecha'           => $fecha,
                ':hora'            => $hora,
                ':tipo_cita'       => $tipo_cita,
                ':asesor_asignado' => $asesor_asignado,
                ':estudio_area'    => $estudio_area,
                ':estudio_cita'    => $estudio_cita
            ]);

            $mensaje = "¡Cita registrada con éxito!";
            $tipo_mensaje = "exito";
        } catch (PDOException $e) {
            $mensaje = "Error al guardar (el ID de cita podría estar duplicado).";
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "Por favor completa al menos el ID, Cliente y Fecha.";
        $tipo_mensaje = "error";
    }
}

// OBTENER TODAS LAS CITAS REGISTRADAS
$stmt_citas = $conexion->query("SELECT * FROM citas ORDER BY fecha DESC, hora ASC");
$lista_citas = $stmt_citas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Citas - SolarControl</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .header { background-color: #1a365d; color: white; padding: 20px; text-align: center; position: relative; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .user-info { position: absolute; right: 20px; top: 20px; font-size: 14px; }
        .user-info a { color: #fbd38d; text-decoration: none; margin-left: 10px; font-weight: bold; }
        
        .alert { padding: 12px; margin: 15px; border-radius: 4px; text-align: center; font-weight: bold; }
        .alert-exito { background-color: #d4edda; color: #155724; }
        .alert-error { background-color: #f8d7da; color: #721c24; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr 180px; gap: 20px; padding: 20px; }
        .section-title { grid-column: span 2; background-color: #edf2f7; padding: 8px 12px; font-weight: bold; color: #2d3748; border-left: 4px solid #1a365d; }
        
        .field-group { margin-bottom: 12px; }
        .field-group label { display: block; font-size: 12px; font-weight: bold; color: #4a5568; margin-bottom: 4px; }
        .field-group input, .field-group select, .field-group textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #cbd5e0; border-radius: 4px; }
        
        .buttons-column { display: flex; flex-direction: column; gap: 10px; justify-content: center; }
        .btn-action { background-color: #1a365d; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: bold; cursor: pointer; text-align: center; }
        .btn-action:hover { background-color: #2c5282; }
        .btn-clear { background-color: #718096; }

        .table-container { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; font-size: 13px; }
        th { background-color: #1a365d; color: white; }
        tr:nth-child(even) { background-color: #f7fafc; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>SOLARCONTROL - REGISTRO DE CITA</h1>
        <div class="user-info">
            👤 <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?> | 
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert <?php echo ($tipo_mensaje == 'exito') ? 'alert-exito' : 'alert-error'; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form action="dashboard.php" method="POST">
        <input type="hidden" name="accion" value="agregar">
        
        <div class="form-grid">
            <!-- DATOS DEL CLIENTE -->
            <div>
                <div class="section-title">DATOS DEL CLIENTE</div>
                <br>
                <div class="field-group">
                    <label>ID DE CITA</label>
                    <input type="text" name="id_cita" required placeholder="Ej. CITA-001">
                </div>
                <div class="field-group">
                    <label>CLIENTE</label>
                    <input type="text" name="cliente" required placeholder="Nombre del cliente">
                </div>
                <div class="field-group">
                    <label>TELÉFONO</label>
                    <input type="text" name="telefono" placeholder="Número de contacto">
                </div>
                <div class="field-group">
                    <label>DIRECCIÓN</label>
                    <input type="text" name="direccion" placeholder="Dirección del predio">
                </div>
            </div>

            <!-- DATOS DE LA CITA -->
            <div>
                <div class="section-title">DATOS DE LA CITA</div>
                <br>
                <div class="field-group">
                    <label>FECHA</label>
                    <input type="date" name="fecha" required>
                </div>
                <div class="field-group">
                    <label>HORA</label>
                    <select name="hora">
                        <option value="08:00:00">08:00 AM</option>
                        <option value="09:00:00">09:00 AM</option>
                        <option value="10:00:00">10:00 AM</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="14:00:00">02:00 PM</option>
                        <option value="15:00:00">03:00 PM</option>
                        <option value="16:00:00">04:00 PM</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>TIPO DE CITA</label>
                    <select name="tipo_cita">
                        <option value="Técnica">Técnica</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Inspección">Inspección</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>ASESOR ASIGNADO</label>
                    <select name="asesor_asignado">
                        <option value="Asesor 1">Asesor 1</option>
                        <option value="Asesor 2">Asesor 2</option>
                        <option value="Asesor 3">Asesor 3</option>
                    </select>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="buttons-column">
                <button type="submit" class="btn-action">Agregar</button>
                <button type="reset" class="btn-action btn-clear">Limpiar</button>
            </div>

            <!-- ESTUDIOS -->
            <div style="grid-column: span 2;">
                <div class="section-title">ESTUDIOS</div>
                <br>
                <div style="display: flex; gap: 15px;">
                    <div class="field-group" style="flex: 1;">
                        <label>ESTUDIO DEL ÁREA</label>
                        <textarea name="estudio_area" rows="2" placeholder="Detalles de viabilidad solar..."></textarea>
                    </div>
                    <div class="field-group" style="flex: 1;">
                        <label>ESTUDIO DE LA CITA</label>
                        <textarea name="estudio_cita" rows="2" placeholder="Notas adicionales de la cita..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- TABLA DE CITAS REGISTRADAS -->
    <div class="table-container">
        <h2>Citas Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Cita</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Tipo</th>
                    <th>Asesor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($lista_citas) > 0): ?>
                    <?php foreach ($lista_citas as $cita): ?>
                        <tr>
                            <td><b><?php echo htmlspecialchars($cita['id_cita']); ?></b></td>
                            <td><?php echo htmlspecialchars($cita['cliente']); ?></td>
                            <td><?php echo htmlspecialchars($cita['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                            <td><?php echo htmlspecialchars($cita['tipo_cita']); ?></td>
                            <td><?php echo htmlspecialchars($cita['asesor_asignado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No hay citas registradas todavía.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>