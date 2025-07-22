<?php
// view_events.php - Página para ver los eventos registrados
require_once 'db_config.php';

$search_plate = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Construir consulta con filtro opcional
$where_clause = '';
$params = [];

if (!empty($search_plate)) {
    $where_clause = 'WHERE UPPER(license_plate) LIKE ?';
    $params[] = '%' . strtoupper(trim($search_plate)) . '%';
}

try {
    // Contar total de registros
    $count_sql = "SELECT COUNT(*) as total FROM events $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_records / $per_page);

    // Obtener eventos con paginación
    $sql = "SELECT * FROM events $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_message = "Error al cargar los eventos.";
    error_log("Database error in view_events.php: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Registrados</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .events-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .search-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: end;
        }
        
        .event-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        
        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .event-plate {
            font-size: 1.2em;
            font-weight: 700;
            color: #667eea;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .event-date {
            color: #666;
            font-size: 0.9em;
        }
        
        .event-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .event-image:hover {
            transform: scale(1.05);
        }
        
        .event-description {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-style: italic;
            color: #555;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #667eea;
            border: 1px solid #e2e8f0;
        }
        
        .pagination a:hover {
            background: #667eea;
            color: white;
        }
        
        .pagination .current {
            background: #667eea;
            color: white;
        }
        
        .no-events {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .stats {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            color: #666;
        }
        
        /* Modal para imagen */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
        }
        
        .modal img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .event-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .event-image {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="events-container">
        <h1 style="text-align: center; margin-bottom: 30px;">📋 Eventos Registrados</h1>
        
        <!-- Formulario de búsqueda -->
        <form class="search-form" method="GET">
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <label for="search">Buscar por patente:</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="<?php echo htmlspecialchars($search_plate); ?>"
                    placeholder="Ej: ABC123"
                    style="text-transform: uppercase;"
                >
            </div>
            <button type="submit" style="margin-top: 0;">🔍 Buscar</button>
            <?php if (!empty($search_plate)): ?>
                <a href="view_events.php" class="button" style="background: #6c757d; margin-top: 0;">Limpiar</a>
            <?php endif; ?>
        </form>
        
        <!-- Estadísticas -->
        <?php if (isset($total_records)): ?>
            <div class="stats">
                <?php if (!empty($search_plate)): ?>
                    Mostrando <?php echo count($events); ?> evento(s) para la búsqueda "<?php echo htmlspecialchars($search_plate); ?>"
                <?php else: ?>
                    Total de eventos registrados: <?php echo $total_records; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Lista de eventos -->
        <?php if (isset($error_message)): ?>
            <div class="message error">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php elseif (empty($events)): ?>
            <div class="no-events">
                <?php if (!empty($search_plate)): ?>
                    <h3>No se encontraron eventos para la patente "<?php echo htmlspecialchars($search_plate); ?>"</h3>
                    <p>Intenta con una búsqueda diferente.</p>
                <?php else: ?>
                    <h3>No hay eventos registrados</h3>
                    <p>Los eventos aparecerán aquí cuando se registren patentes no encontradas.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <div class="event-header">
                        <div>
                            <div class="event-plate">🚗 <?php echo htmlspecialchars($event['license_plate']); ?></div>
                            <div class="event-date">
                                📅 <?php echo date('d/m/Y', strtotime($event['event_date'])); ?> 
                                | ⏰ Registrado: <?php echo date('d/m/Y H:i', strtotime($event['created_at'])); ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <small style="color: #999;">ID: #<?php echo $event['id']; ?></small>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                        <?php if (!empty($event['image_path']) && file_exists($event['image_path'])): ?>
                            <div>
                                <img 
                                    src="<?php echo htmlspecialchars($event['image_path']); ?>" 
                                    alt="Imagen del vehículo <?php echo htmlspecialchars($event['license_plate']); ?>"
                                    class="event-image"
                                    onclick="openModal('<?php echo htmlspecialchars($event['image_path']); ?>')"
                                >
                            </div>
                        <?php endif; ?>
                        
                        <div style="flex: 1;">
                            <?php if (!empty($event['description'])): ?>
                                <div class="event-description">
                                    <strong>Descripción:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                                </div>
                            <?php else: ?>
                                <div style="color: #999; font-style: italic;">
                                    Sin descripción adicional
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search_plate) ? '&search=' . urlencode($search_plate) : ''; ?>">« Anterior</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search_plate) ? '&search=' . urlencode($search_plate) : ''; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search_plate) ? '&search=' . urlencode($search_plate) : ''; ?>">Siguiente »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Enlaces de navegación -->
        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php" class="button">🔍 Verificar Patente</a>
        </div>
    </div>
    
    <!-- Modal para mostrar imagen ampliada -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="modalImage" src="" alt="Imagen ampliada">
        </div>
    </div>
    
    <script>
        // Convertir búsqueda a mayúsculas
        document.getElementById('search').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
        
        // Funciones del modal
        function openModal(imageSrc) {
            document.getElementById('imageModal').style.display = 'block';
            document.getElementById('modalImage').src = imageSrc;
        }
        
        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera de la imagen
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
