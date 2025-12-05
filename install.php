<?php
// install.php
header('Content-Type: text/html; charset=utf-8');
$host = 'localhost'; // Intentaremos localhost primero
$username = 'root';
$password = 'Cobra'; // Contraseña provista por usuario

echo "<h1>Instalación de Base de Datos</h1>";

try {
    // Intentar conexión con localhost
    try {
        $pdo = new PDO("mysql:host=localhost", $username, $password);
    } catch (PDOException $e) {
        // Si falla, intentar con 127.0.0.1
        $pdo = new PDO("mysql:host=127.0.0.1", $username, $password);
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Conexión exitosa a MySQL.</p>";
    
    // Crear DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `inventario_whatsapp`");
    echo "<p>✅ Base de datos 'inventario_whatsapp' verificada/creada.</p>";
    
    $pdo->exec("USE `inventario_whatsapp`");
    
    // Leer SQL
    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);
    echo "<p>✅ Tablas creadas correctamente.</p>";
    
    echo "<h3>¡Instalación Completada!</h3>";
    echo "<p>Ahora puedes <a href='index.php'>ir a la tienda</a> o <a href='tests/web_test.php'>ejecutar pruebas</a>.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: Usuario '$username', Password (vacío). Verifica que tu MySQL esté corriendo.</p>";
}
?>
