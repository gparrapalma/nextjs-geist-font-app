<?php
// install.php - Script de instalación automática
error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = $_GET['step'] ?? 1;
$message = '';
$error = '';

// Función para verificar requisitos
function checkRequirements() {
    $requirements = [
        'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'FileInfo Extension' => extension_loaded('fileinfo'),
        'Uploads Directory Writable' => is_writable(__DIR__ . '/uploads'),
    ];
    
    return $requirements;
}

// Función para crear la base de datos
function createDatabase($host, $user, $pass, $dbname) {
    try {
        // Conectar sin especificar base de datos
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Crear base de datos
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Usar la base de datos
        $pdo->exec("USE `$dbname`");
        
        // Leer y ejecutar el script SQL
        $sql = file_get_contents(__DIR__ . '/database.sql');
        $sql = str_replace('CREATE DATABASE IF NOT EXISTS vehicle_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;', '', $sql);
        $sql = str_replace('USE vehicle_db;', '', $sql);
        
        // Ejecutar cada declaración SQL
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        throw new Exception("Error de base de datos: " . $e->getMessage());
    }
}

// Función para actualizar configuración
function updateConfig($host, $user, $pass, $dbname) {
    $config = file_get_contents(__DIR__ . '/db_config.php');
    $config = str_replace("'localhost'", "'$host'", $config);
    $config = str_replace("'root'", "'$user'", $config);
    $config = str_replace("''", "'$pass'", $config);
    $config = str_replace("'vehicle_db'", "'$dbname'", $config);
    
    return file_put_contents(__DIR__ . '/db_config.php', $config);
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        $host = $_POST['host'] ?? 'localhost';
        $user = $_POST['user'] ?? '';
        $pass = $_POST['pass'] ?? '';
        $dbname = $_POST['dbname'] ?? 'vehicle_db';
        
        try {
            createDatabase($host, $user, $pass, $dbname);
            updateConfig($host, $user, $pass, $dbname);
            $message = "¡Base de datos creada exitosamente!";
            $step = 3;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - Sistema de Patentes Vehiculares</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .install-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: white;
        }
        
        .step.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .step.completed {
            background: #48bb78;
        }
        
        .step.pending {
            background: #e2e8f0;
            color: #666;
        }
        
        .requirement {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .requirement.pass {
            background: #d4edda;
            color: #155724;
        }
        
        .requirement.fail {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status {
            font-weight: bold;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <h1 style="text-align: center; margin-bottom: 30px;">🚗 Instalación del Sistema</h1>
        
        <!-- Indicador de pasos -->
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? ($step == 1 ? 'active' : 'completed') : 'pending'; ?>">1</div>
            <div class="step <?php echo $step >= 2 ? ($step == 2 ? 'active' : 'completed') : 'pending'; ?>">2</div>
            <div class="step <?php echo $step >= 3 ? ($step == 3 ? 'active' : 'completed') : 'pending'; ?>">3</div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <!-- Paso 1: Verificar requisitos -->
            <h2>Paso 1: Verificación de Requisitos</h2>
            <p>Verificando que tu servidor cumple con los requisitos mínimos:</p>
            
            <?php
            $requirements = checkRequirements();
            $allPassed = true;
            foreach ($requirements as $requirement => $passed):
                if (!$passed) $allPassed = false;
            ?>
                <div class="requirement <?php echo $passed ? 'pass' : 'fail'; ?>">
                    <span><?php echo $requirement; ?></span>
                    <span class="status"><?php echo $passed ? '✓ OK' : '✗ FALLO'; ?></span>
                </div>
            <?php endforeach; ?>
            
            <?php if ($allPassed): ?>
                <div class="alert success">
                    ¡Todos los requisitos se cumplen! Puedes continuar con la instalación.
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="?step=2" class="button">Continuar →</a>
                </div>
            <?php else: ?>
                <div class="alert error">
                    Algunos requisitos no se cumplen. Por favor, configura tu servidor antes de continuar.
                </div>
            <?php endif; ?>
            
        <?php elseif ($step == 2): ?>
            <!-- Paso 2: Configuración de base de datos -->
            <h2>Paso 2: Configuración de Base de Datos</h2>
            <p>Ingresa los datos de conexión a tu base de datos MySQL:</p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="host">Servidor MySQL:</label>
                    <input type="text" id="host" name="host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="user">Usuario:</label>
                    <input type="text" id="user" name="user" value="root" required>
                </div>
                
                <div class="form-group">
                    <label for="pass">Contraseña:</label>
                    <input type="password" id="pass" name="pass" placeholder="Deja vacío si no tienes contraseña">
                </div>
                
                <div class="form-group">
                    <label for="dbname">Nombre de la Base de Datos:</label>
                    <input type="text" id="dbname" name="dbname" value="vehicle_db" required>
                    <small style="color: #666;">Se creará automáticamente si no existe</small>
                </div>
                
                <button type="submit">Crear Base de Datos</button>
            </form>
            
        <?php elseif ($step == 3): ?>
            <!-- Paso 3: Instalación completada -->
            <h2>¡Instalación Completada!</h2>
            
            <div class="alert success">
                <h3>✅ El sistema se ha instalado correctamente</h3>
                <p>La base de datos ha sido creada y configurada con datos de ejemplo.</p>
            </div>
            
            <h3>Datos de prueba incluidos:</h3>
            <ul style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <li><strong>ABC123</strong> - Juan Pérez</li>
                <li><strong>XYZ789</strong> - María González</li>
                <li><strong>DEF456</strong> - Carlos Rodríguez</li>
                <li><strong>GHI789</strong> - Ana Martínez</li>
            </ul>
            
            <h3>Próximos pasos:</h3>
            <ol style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <li>Elimina o renombra este archivo <code>install.php</code> por seguridad</li>
                <li>Configura los permisos del directorio <code>uploads/</code></li>
                <li>Personaliza la configuración en <code>config.php</code> si es necesario</li>
                <li>¡Comienza a usar el sistema!</li>
            </ol>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="index.php" class="button" style="font-size: 1.1em; padding: 15px 30px;">
                    🚀 Ir al Sistema
                </a>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <small style="color: #666;">
                    Recuerda eliminar install.php después de completar la instalación
                </small>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
