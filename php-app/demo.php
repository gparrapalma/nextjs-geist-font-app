<?php
// demo.php - Demostración de la aplicación sin base de datos
$action = $_GET['action'] ?? 'home';
$demo_vehicles = [
    'ABC123' => 'Juan Pérez',
    'XYZ789' => 'María González', 
    'DEF456' => 'Carlos Rodríguez',
    'GHI789' => 'Ana Martínez'
];

$message = '';
$message_type = '';
$license_plate_input = '';
$not_found = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'verify') {
    $license_plate_input = strtoupper(trim($_POST['license_plate'] ?? ''));
    
    if (empty($license_plate_input)) {
        $message = "Por favor, ingrese una patente vehicular.";
        $message_type = "error";
    } else {
        if (isset($demo_vehicles[$license_plate_input])) {
            $owner_name = $demo_vehicles[$license_plate_input];
            $message = "✓ La patente <strong>" . htmlspecialchars($license_plate_input) . "</strong> está registrada a nombre de: <strong>" . htmlspecialchars($owner_name) . "</strong>";
            $message_type = "success";
        } else {
            $message = "⚠ La patente <strong>" . htmlspecialchars($license_plate_input) . "</strong> no se encuentra registrada en el sistema.";
            $message_type = "warning";
            $not_found = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEMO - Sistema de Verificación de Patentes Vehiculares</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .demo-banner {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 12px;
            font-weight: bold;
        }
        
        .demo-data {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .vehicle-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        
        .vehicle-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .vehicle-item:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .nav-menu {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .nav-menu a {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            border: 2px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .nav-menu a:hover, .nav-menu a.active {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="demo-banner">
            🚨 MODO DEMOSTRACIÓN - Esta es una versión de prueba sin base de datos
        </div>
        
        <h1>🚗 Sistema de Verificación de Patentes</h1>
        
        <div class="nav-menu">
            <a href="?action=home" class="<?php echo $action === 'home' ? 'active' : ''; ?>">🏠 Inicio</a>
            <a href="?action=verify" class="<?php echo $action === 'verify' ? 'active' : ''; ?>">🔍 Verificar</a>
            <a href="?action=register" class="<?php echo $action === 'register' ? 'active' : ''; ?>">📝 Registrar</a>
            <a href="?action=events" class="<?php echo $action === 'events' ? 'active' : ''; ?>">📋 Eventos</a>
        </div>
        
        <?php if ($action === 'home'): ?>
            <!-- Página de inicio -->
            <div class="demo-data">
                <h2>Bienvenido al Sistema de Patentes Vehiculares</h2>
                <p>Este sistema permite:</p>
                <ul style="text-align: left; margin: 20px 0;">
                    <li>✅ Verificar si una patente está registrada</li>
                    <li>✅ Mostrar información del propietario</li>
                    <li>✅ Registrar eventos para patentes no registradas</li>
                    <li>✅ Subir imágenes de vehículos</li>
                    <li>✅ Ver historial de eventos</li>
                </ul>
                
                <h3>Datos de Prueba Disponibles:</h3>
                <div class="vehicle-list">
                    <?php foreach ($demo_vehicles as $plate => $owner): ?>
                        <div class="vehicle-item">
                            <strong><?php echo $plate; ?></strong><br>
                            <small><?php echo $owner; ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        <?php elseif ($action === 'verify'): ?>
            <!-- Verificación de patentes -->
            <form method="POST" action="?action=verify" id="verificationForm">
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
                <button type="submit">Verificar Patente</button>
            </form>

            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <p><?php echo $message; ?></p>
                    <?php if ($not_found): ?>
                        <a href="?action=register&plate=<?php echo urlencode($license_plate_input); ?>" class="button">
                            📝 Registrar Nuevo Evento
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="demo-data">
                <h3>💡 Prueba con estas patentes:</h3>
                <div class="vehicle-list">
                    <?php foreach ($demo_vehicles as $plate => $owner): ?>
                        <div class="vehicle-item" onclick="document.getElementById('license_plate').value='<?php echo $plate; ?>'">
                            <strong><?php echo $plate; ?></strong><br>
                            <small>Click para probar</small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        <?php elseif ($action === 'register'): ?>
            <!-- Registro de eventos -->
            <h2>📋 Registrar Evento del Vehículo</h2>
            
            <form action="#" method="POST" enctype="multipart/form-data" onsubmit="return showDemo(event)">
                <div class="form-group">
                    <label for="event_date">📅 Fecha del Evento:</label>
                    <input type="date" id="event_date" name="event_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="license_plate_reg">🚗 Patente del Vehículo:</label>
                    <input 
                        type="text" 
                        id="license_plate_reg" 
                        name="license_plate" 
                        value="<?php echo htmlspecialchars($_GET['plate'] ?? ''); ?>" 
                        placeholder="Ej: ABC123"
                        required
                        style="text-transform: uppercase;"
                    >
                </div>

                <div class="form-group">
                    <label for="vehicle_image">📸 Imagen del Vehículo:</label>
                    <input type="file" id="vehicle_image" name="vehicle_image" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="description">📝 Descripción del Evento:</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="3" 
                        placeholder="Describa brevemente el evento..."
                        style="padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-family: inherit; width: 100%; box-sizing: border-box;"
                    ></textarea>
                </div>

                <button type="submit">💾 Registrar Evento</button>
            </form>
            
        <?php elseif ($action === 'events'): ?>
            <!-- Lista de eventos -->
            <h2>📋 Eventos Registrados</h2>
            
            <div class="demo-data">
                <h3>Eventos de Demostración</h3>
                
                <div class="event-card" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 12px; margin: 15px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 1.2em; font-weight: 700; color: #667eea;">🚗 NON123</div>
                            <div style="color: #666; font-size: 0.9em;">📅 20/01/2025 | ⏰ Registrado: 20/01/2025 14:30</div>
                        </div>
                        <div style="text-align: right;">
                            <small style="color: #999;">ID: #001</small>
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <strong>Descripción:</strong><br>
                        Vehículo estacionado en zona prohibida durante evento público
                    </div>
                </div>
                
                <div class="event-card" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 12px; margin: 15px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 1.2em; font-weight: 700; color: #667eea;">🚗 TEST456</div>
                            <div style="color: #666; font-size: 0.9em;">📅 19/01/2025 | ⏰ Registrado: 19/01/2025 09:15</div>
                        </div>
                        <div style="text-align: right;">
                            <small style="color: #999;">ID: #002</small>
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <strong>Descripción:</strong><br>
                        Vehículo abandonado en vía pública por más de 48 horas
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
        
        <div class="back-link">
            <p style="margin-top: 30px; text-align: center; color: #666; font-size: 0.9em;">
                Sistema de Verificación de Patentes Vehiculares v1.0 - DEMO
            </p>
        </div>
    </div>

    <script>
        // Convertir a mayúsculas
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="text"]');
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase();
                });
            });
        });
        
        function showDemo(event) {
            event.preventDefault();
            alert('🎉 ¡DEMO EXITOSA!\n\nEn la versión completa con base de datos:\n• Se guardaría el evento en MySQL\n• Se subiría la imagen al servidor\n• Se generaría un ID único\n• Se enviaría confirmación por email');
            return false;
        }
    </script>
</body>
</html>
