<?php
// setup_db.php
$host = '127.0.0.1';
$username = 'root';
$password = 'Cobra';

try {
    echo "Conectando a MySQL...\n";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Creando base de datos 'inventario_whatsapp' if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `inventario_whatsapp`");

    echo "Seleccionando base de datos...\n";
    $pdo->exec("USE `inventario_whatsapp`");

    echo "Creando tablas...\n";
    $sql = file_get_contents('database.sql');

    // Split SQL by semicolon to execute multiple statements if needed, 
    // but PDO::exec might handle it depending on driver. 
    // Safer to just run the big block or split it.
    // Let's try executing the whole file content first.
    $pdo->exec($sql);

    echo "¡Base de datos y tablas creadas correctamente!\n";
    echo "Por favor recarga phpMyAdmin para ver 'inventario_whatsapp'.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Si tienes contraseña en root, edita este archivo y config/db.php\n";
}
