<?php
// index.php - Página principal para verificación de patentes vehiculares
require_once 'db_config.php';

$message = "";
$owner_name = "";
$not_found = false;
$license_plate_input = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $license_plate_input = strtoupper(trim($_POST['license_plate'] ?? ''));
    
    if (empty($license_plate_input)) {
        $message = "Por favor, ingrese una patente vehicular.";
        $message_type = "error";
    } else {
        try {
            // Preparar y ejecutar consulta
            $stmt = $pdo->prepare("SELECT owner_name FROM vehicles WHERE UPPER(license_plate) = ?");
            $stmt->execute([$license_plate_input]);
            $result = $stmt->fetch();
            
            if ($result) {
                $owner_name = $result['owner_name'];
                $message = "✓ La patente <strong>" . htmlspecialchars($license_plate_input) . "</strong> está registrada a nombre de: <strong>" . htmlspecialchars($owner_name) . "</strong>";
                $message_type = "success";
            } else {
                $message = "⚠ La patente <strong>" . htmlspecialchars($license_plate_input) . "</strong> no se encuentra registrada en el sistema.";
                $message_type = "warning";
                $not_found = true;
            }
        } catch (PDOException $e) {
            $message = "Error al consultar la base de datos. Por favor, intente nuevamente.";
            $message_type = "error";
            // En producción, registrar el error en un log
            error_log("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Patente Vehicular</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🚗 Verificar Patente Vehicular</h1>
        
        <form method="POST" action="index.php" id="verificationForm">
            <div class="form-group">
                <label for="license_plate">Número de Patente:</label>
                <input 
                    type="text" 
                    id="license_plate" 
                    name="license_plate" 
                    placeholder="Ej: ABC123, XYZ789" 
                    value="<?php echo htmlspecialchars($license_plate_input); ?>"
                    required
                    maxlength="20"
                    style="text-transform: uppercase;"
                >
            </div>
            <button type="submit" id="submitBtn">
                <span id="btnText">Verificar Patente</span>
            </button>
        </form>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <p><?php echo $message; ?></p>
                <?php if ($not_found): ?>
                    <a href="event_registration.php?plate=<?php echo urlencode($license_plate_input); ?>" class="button">
                        📝 Registrar Nuevo Evento
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="view_events.php" style="display: inline-block; margin-top: 20px;">📋 Ver Eventos Registrados</a>
            <p style="margin-top: 20px; text-align: center; color: #666; font-size: 0.9em;">
                Sistema de Verificación de Patentes Vehiculares v1.0
            </p>
        </div>
    </div>

    <script>
        // Mejorar la experiencia del usuario con JavaScript
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const licenseInput = document.getElementById('license_plate');
            
            // Validar formato básico de patente
            const plateValue = licenseInput.value.trim().toUpperCase();
            if (plateValue.length < 3) {
                e.preventDefault();
                alert('Por favor, ingrese una patente válida (mínimo 3 caracteres).');
                licenseInput.focus();
                return;
            }
            
            // Mostrar estado de carga
            submitBtn.disabled = true;
            btnText.innerHTML = 'Verificando... <span class="loading"></span>';
        });

        // Convertir automáticamente a mayúsculas mientras se escribe
        document.getElementById('license_plate').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        // Limpiar el formulario si hay un mensaje de éxito
        <?php if ($message_type === 'success'): ?>
        setTimeout(function() {
            document.getElementById('license_plate').value = '';
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>
