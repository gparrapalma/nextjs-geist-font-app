# Sistema de Verificación de Patentes Vehiculares

Una aplicación web completa desarrollada en PHP y MySQL para verificar si una patente vehicular está registrada en la base de datos y permitir el registro de eventos para patentes no registradas.

## 🚀 Características

- **Verificación de Patentes**: Consulta rápida para verificar si una patente está registrada
- **Información del Propietario**: Muestra el nombre del propietario si la patente está registrada
- **Registro de Eventos**: Permite registrar eventos para patentes no registradas
- **Carga de Imágenes**: Subida segura de imágenes de vehículos
- **Diseño Moderno**: Interfaz responsive con gradientes y animaciones
- **Validaciones**: Validación tanto del lado cliente como del servidor

## 📋 Requisitos del Sistema

- **Servidor Web**: Apache o Nginx con soporte PHP
- **PHP**: Versión 7.4 o superior
- **MySQL**: Versión 5.7 o superior
- **Extensiones PHP requeridas**:
  - PDO
  - PDO_MySQL
  - fileinfo
  - GD (opcional, para procesamiento de imágenes)

## 🛠️ Instalación

### 1. Configurar la Base de Datos

1. Accede a tu servidor MySQL:
```bash
mysql -u root -p
```

2. Ejecuta el script de base de datos:
```bash
mysql -u root -p < database.sql
```

O copia y pega el contenido de `database.sql` en tu cliente MySQL preferido.

### 2. Configurar la Conexión

1. Edita el archivo `db_config.php`:
```php
$host = 'localhost';        // Tu servidor MySQL
$db   = 'vehicle_db';       // Nombre de la base de datos
$user = 'tu_usuario';       // Tu usuario MySQL
$pass = 'tu_contraseña';    // Tu contraseña MySQL
```

### 3. Configurar Permisos

1. Asegúrate de que el directorio `uploads/` tenga permisos de escritura:
```bash
chmod 755 uploads/
```

2. Si usas Apache, asegúrate de que el módulo `mod_rewrite` esté habilitado.

### 4. Configurar el Servidor Web

#### Para Apache:
Coloca los archivos en tu directorio web (ej: `/var/www/html/vehicle-system/`)

#### Para desarrollo local con PHP:
```bash
cd php-app
php -S localhost:8000
```

## 📖 Uso de la Aplicación

### Verificar una Patente

1. Accede a `index.php`
2. Ingresa el número de patente en el campo correspondiente
3. Haz clic en "Verificar Patente"
4. El sistema mostrará:
   - ✅ **Si está registrada**: Nombre del propietario
   - ⚠️ **Si no está registrada**: Opción para registrar un evento

### Registrar un Evento

1. Desde la página de verificación, haz clic en "Registrar Nuevo Evento"
2. Completa el formulario:
   - **Fecha del Evento**: Fecha cuando ocurrió el evento
   - **Patente**: Número de patente del vehículo
   - **Imagen**: Foto del vehículo (JPG, PNG, GIF - máx. 5MB)
   - **Descripción**: Descripción opcional del evento
3. Haz clic en "Registrar Evento"

## 🗂️ Estructura de Archivos

```
php-app/
├── index.php              # Página principal de verificación
├── event_registration.php # Formulario de registro de eventos
├── process_event.php      # Procesamiento del registro
├── db_config.php          # Configuración de base de datos
├── style.css              # Estilos CSS modernos
├── database.sql           # Script de base de datos
├── uploads/               # Directorio para imágenes subidas
└── README.md              # Este archivo
```

## 🗄️ Estructura de la Base de Datos

### Tabla `vehicles`
- `id`: ID único (AUTO_INCREMENT)
- `license_plate`: Número de patente (UNIQUE)
- `owner_name`: Nombre del propietario
- `created_at`: Fecha de creación

### Tabla `events`
- `id`: ID único (AUTO_INCREMENT)
- `event_date`: Fecha del evento
- `license_plate`: Número de patente
- `image_path`: Ruta de la imagen
- `description`: Descripción del evento
- `created_at`: Fecha de creación

## 🔒 Seguridad

- **Prepared Statements**: Protección contra inyección SQL
- **Validación de Archivos**: Solo imágenes permitidas
- **Sanitización**: Escape de datos de salida
- **Límites de Tamaño**: Máximo 5MB por imagen
- **Validación de Fechas**: No se permiten fechas futuras

## 🎨 Personalización

### Modificar Estilos
Edita `style.css` para cambiar:
- Colores del gradiente
- Tipografía
- Espaciado y diseño
- Animaciones

### Agregar Campos
Para agregar nuevos campos al registro de eventos:
1. Modifica la tabla `events` en `database.sql`
2. Actualiza `event_registration.php`
3. Modifica `process_event.php`

## 🐛 Solución de Problemas

### Error de Conexión a la Base de Datos
- Verifica las credenciales en `db_config.php`
- Asegúrate de que MySQL esté ejecutándose
- Confirma que la base de datos `vehicle_db` existe

### Error de Permisos de Archivo
```bash
chmod 755 uploads/
chown www-data:www-data uploads/  # En sistemas Ubuntu/Debian
```

### Imágenes No Se Cargan
- Verifica que el directorio `uploads/` exista y tenga permisos
- Confirma que la extensión `fileinfo` esté habilitada en PHP
- Revisa los logs de error de PHP

## 📝 Datos de Prueba

La base de datos incluye estos vehículos de ejemplo:
- **ABC123** - Juan Pérez
- **XYZ789** - María González
- **DEF456** - Carlos Rodríguez
- **GHI789** - Ana Martínez

## 🔄 Actualizaciones Futuras

Posibles mejoras:
- Panel de administración
- Historial de eventos por patente
- Exportación de reportes
- API REST
- Notificaciones por email
- Búsqueda avanzada

## 📞 Soporte

Para reportar problemas o sugerir mejoras, por favor documenta:
1. Versión de PHP y MySQL
2. Mensaje de error completo
3. Pasos para reproducir el problema
4. Configuración del servidor

---

**Versión**: 1.0  
**Desarrollado con**: PHP, MySQL, HTML5, CSS3, JavaScript  
**Licencia**: MIT
