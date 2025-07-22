<?php
// process_event.php - Procesar el registro de eventos de vehículos
require_once 'db_config.php';

$message = "";
$message_type = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_date = $_POST['event_date'] ?? '';
    $license_plate = strtoupper(trim($_POST['license_plate'] ?? ''));
    $description = trim($_POST['description'] ?? '');

    // Validaciones básicas
    if (!$event_date || !$license_plate || !isset($_FILES['vehicle_image'])) {
        $message = "Todos los campos obligatorios son requeridos.";
        $message_type = "error";
    } else {
        // Validar fecha (no debe ser futura)
        $selected_date = new DateTime($event_date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        if ($selected_date > $today) {
            $message = "La fecha del evento no puede ser futura.";
            $message_type = "error";
        } else {
            // Procesar carga de archivo
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file = $_FILES['vehicle_image'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                switch ($file['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = "La imagen es demasiado grande. Tamaño máximo permitido: 5MB.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = "La carga de la imagen se interrumpió. Intente nuevamente.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $message = "No se seleccionó ninguna imagen.";
                        break;
                    default:
                        $message = "Error en la carga de la imagen.";
                }
                $message_type = "error";
            } else {
                // Validar tipo de archivo
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mime_type, $allowed_types)) {
                    $message = "Tipo de archivo no permitido. Solo se aceptan imágenes JPG, PNG y GIF.";
                    $message_type = "error";
                } else {
                    // Validar tamaño de archivo (5MB máximo)
                    $max_size = 5 * 1024 * 1024; // 5MB
                    if ($file['size'] > $max_size) {
                        $message = "La imagen es demasiado grande. Tamaño máximo: 5MB.";
                        $message_type = "error";
                    } else {
                        // Crear directorio de uploads si no existe
                        $upload_dir = __DIR__ . '/uploads/';
                        if (!is_dir($upload_dir)) {
                            if (!mkdir($upload_dir, 0755, true)) {
                                $message = "Error al crear el directorio de imágenes.";
                                $message_type = "error";
                            }
                        }
                        
                        if (empty($message)) {
                            // Generar nombre único para el archivo
                            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                            if ($file_ext === 'jpeg') $file_ext = 'jpg';
                            
                            $unique_name = 'veh_' . date('Ymd_His') . '_' . uniqid() . '.' . $file_ext;
                            $target_path = $upload_dir . $unique_name;
                            
                            if (!move_uploaded_file($file['tmp_name'], $target_path)) {
                                $message = "Error al guardar la imagen en el servidor.";
                                $message_type = "error";
                            } else {
                                // Guardar registro del evento en la base de datos
                                try {
                                    $stmt = $pdo->prepare("INSERT INTO events (event_date, license_plate, image_path, description) VALUES (?, ?, ?, ?)");
                                    $stmt->execute([
                                        $event_date, 
                                        $license_plate, 
                                        'uploads/' . $unique_name,
                                        $description
                                    ]);
                                    
                                    $message = "¡Evento registrado exitosamente! Se ha guardado la información de la patente " . htmlspecialchars($license_plate) . ".";
                                    $message_type = "success";
                                    $success = true;
                                    
                                } catch (PDOException $e) {
                                    // Si hay error en la base de datos, eliminar la imagen subida
                                    if (file_exists($target_path)) {
                                        unlink($target_path);
                                    }
                                    
                                    $message = "Error al registrar el evento en la base de datos. Por favor, intente nuevamente.";
                                    $message_type = "error";
                                    
                                    // En producción, registrar el error en un log
                                    error_log("Database error in process_event.php: " . $e->getMessage());
                                }
                            }
                        }
                    }
                }
            }
        }
    }
} else {
    // Si se accede directamente sin POST, redirigir
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo $success ? '✅ Registro Exitoso' : '❌ Error en el Registro'; ?></h1>
        
        <div class="message <?php echo $message_type; ?>">
            <p><?php echo $message; ?></p>
            
            <?php if ($success): ?>
                <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.2); border-radius: 8px;">
                    <h3 style="margin: 0 0 10px 0; color: white;">Detalles del evento registrado:</h3>
                    <p style="margin: 5px 0;"><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($event_date)); ?></p>
                    <p style="margin: 5px 0;"><strong>Patente:</strong> <?php echo htmlspecialchars($license_plate); ?></p>
                    <?php if (!empty($description)): ?>
                        <p style="margin: 5px 0;"><strong>Descripción:</strong> <?php echo htmlspecialchars($description); ?></p>
                    <?php endif; ?>
                    <p style="margin: 5px 0;"><strong>Imagen:</strong> Guardada correctamente</p>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="button">🔍 Nueva Verificación</a>
            <a href="view_events.php" class="button" style="margin-left: 10px; background: rgba(255,255,255,0.2);">📋 Ver Eventos</a>
            <?php if (!$success): ?>
                <a href="javascript:history.back()" class="button" style="margin-left: 10px; background: rgba(255,255,255,0.2);">
                    ← Volver al Formulario
                </a>
            <?php endif; ?>
        </div>

        <?php if ($success): ?>
            <div class="back-link">
                <p style="margin-top: 30px; text-align: center; color: #666; font-size: 0.9em;">
                    El evento ha sido registrado con ID #<?php echo $pdo->lastInsertId(); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
    <script>
        // Mostrar animación de éxito
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.container');
            container.style.animation = 'fadeIn 0.8s ease-in';
            
            // Auto-redirect después de 10 segundos
            let countdown = 10;
            const redirectMsg = document.createElement('p');
            redirectMsg.style.textAlign = 'center';
            redirectMsg.style.color = '#666';
            redirectMsg.style.fontSize = '0.9em';
            redirectMsg.style.marginTop = '20px';
            
            const updateCountdown = () => {
                redirectMsg.innerHTML = `Redirigiendo automáticamente en ${countdown} segundos...`;
                countdown--;
                
                if (countdown < 0) {
                    window.location.href = 'index.php';
                }
            };
            
            document.querySelector('.container').appendChild(redirectMsg);
            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
    <?php endif; ?>
</body>
</html>
