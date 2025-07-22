<?php
// event_registration.php - Formulario para registrar eventos de vehículos no registrados
$license_plate = $_GET['plate'] ?? '';
$license_plate = strtoupper(trim($license_plate));

// Obtener la fecha actual para el campo de fecha
$current_date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Evento de Vehículo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📋 Registrar Evento del Vehículo</h1>
        
        <p style="text-align: center; color: #666; margin-bottom: 25px;">
            Complete la información del evento para la patente no registrada
        </p>

        <form action="process_event.php" method="POST" enctype="multipart/form-data" id="eventForm">
            <div class="form-group">
                <label for="event_date">📅 Fecha del Evento:</label>
                <input 
                    type="date" 
                    id="event_date" 
                    name="event_date" 
                    value="<?php echo $current_date; ?>"
                    max="<?php echo $current_date; ?>"
                    required
                >
                <small style="color: #666; font-size: 0.85em;">La fecha no puede ser futura</small>
            </div>

            <div class="form-group">
                <label for="license_plate">🚗 Patente del Vehículo:</label>
                <input 
                    type="text" 
                    id="license_plate" 
                    name="license_plate" 
                    value="<?php echo htmlspecialchars($license_plate); ?>" 
                    placeholder="Ej: ABC123"
                    required
                    maxlength="20"
                    style="text-transform: uppercase;"
                >
            </div>

            <div class="form-group">
                <label for="vehicle_image">📸 Imagen del Vehículo:</label>
                <input 
                    type="file" 
                    id="vehicle_image" 
                    name="vehicle_image" 
                    accept="image/jpeg,image/jpg,image/png,image/gif" 
                    required
                >
                <small style="color: #666; font-size: 0.85em;">
                    Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB
                </small>
            </div>

            <div class="form-group">
                <label for="description">📝 Descripción del Evento (Opcional):</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3" 
                    placeholder="Describa brevemente el evento o situación..."
                    style="padding: 15px 18px; border: 2px solid #e2e8f0; border-radius: 12px; font-family: inherit; resize: vertical; transition: all 0.3s ease;"
                ></textarea>
            </div>

            <button type="submit" id="submitBtn">
                <span id="btnText">💾 Registrar Evento</span>
            </button>
        </form>

        <div class="back-link">
            <a href="index.php">← Volver a la verificación de patentes</a>
            <span style="margin: 0 10px;">|</span>
            <a href="view_events.php">📋 Ver eventos registrados</a>
        </div>

        <!-- Preview de imagen -->
        <div id="imagePreview" style="display: none; margin-top: 20px; text-align: center;">
            <p style="color: #666; margin-bottom: 10px;">Vista previa de la imagen:</p>
            <img id="previewImg" style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </div>
    </div>

    <script>
        // Mejorar la experiencia del usuario
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const fileInput = document.getElementById('vehicle_image');
            const licenseInput = document.getElementById('license_plate');
            
            // Validaciones básicas
            const plateValue = licenseInput.value.trim().toUpperCase();
            if (plateValue.length < 3) {
                e.preventDefault();
                alert('Por favor, ingrese una patente válida (mínimo 3 caracteres).');
                licenseInput.focus();
                return;
            }

            // Validar archivo
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('Por favor, seleccione una imagen del vehículo.');
                fileInput.focus();
                return;
            }

            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (file.size > maxSize) {
                e.preventDefault();
                alert('La imagen es demasiado grande. El tamaño máximo permitido es 5MB.');
                return;
            }

            // Mostrar estado de carga
            submitBtn.disabled = true;
            btnText.innerHTML = '⏳ Registrando evento...';
        });

        // Convertir patente a mayúsculas
        document.getElementById('license_plate').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        // Preview de imagen
        document.getElementById('vehicle_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Validar fecha (no permitir fechas futuras)
        document.getElementById('event_date').addEventListener('change', function(e) {
            const selectedDate = new Date(e.target.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate > today) {
                alert('La fecha del evento no puede ser futura.');
                e.target.value = '<?php echo $current_date; ?>';
            }
        });

        // Estilo para textarea focus
        document.getElementById('description').addEventListener('focus', function(e) {
            e.target.style.borderColor = '#667eea';
            e.target.style.boxShadow = '0 0 0 3px rgba(102, 126, 234, 0.1)';
        });

        document.getElementById('description').addEventListener('blur', function(e) {
            e.target.style.borderColor = '#e2e8f0';
            e.target.style.boxShadow = 'none';
        });
    </script>
</body>
</html>
