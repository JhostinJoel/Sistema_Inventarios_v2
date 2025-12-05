<?php
// config/db.php

$host = '127.0.0.1';
$db_name = 'inventario_whatsapp';
$username = 'root';
$password = 'Cobra'; // Cambiar si tienes contraseña en tu MySQL local

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si la base de datos no existe, intentamos crearla (solo para desarrollo local)
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
        $pdo->exec("USE `$db_name`");
    } catch (PDOException $e2) {
        die("Error de conexión: " . $e->getMessage());
    }
}
