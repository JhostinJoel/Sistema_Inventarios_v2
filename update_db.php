<?php
// update_db.php
require_once 'config/db.php';

try {
    echo "Actualizando base de datos...\n";

    // Crear tabla categorías
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT
    )");
    echo "✅ Tabla 'categories' verificada.\n";

    // Agregar columna category_id a products si no existe
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category_id INT");
        $pdo->exec("ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
        echo "✅ Columna 'category_id' agregada a 'products'.\n";
    } catch (PDOException $e) {
        // Ignorar error si la columna ya existe (Duplicate column name)
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "ℹ️ Columna 'category_id' ya existe.\n";
        } else {
            echo "⚠️ Nota sobre products: " . $e->getMessage() . "\n";
        }
    }

    // Insertar categorías por defecto si está vacía
    $count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO categories (name, description) VALUES 
            ('Tecnología', 'Gadgets y dispositivos'),
            ('Ropa', 'Moda y estilo'),
            ('Hogar', 'Artículos para el hogar')");
        echo "✅ Categorías por defecto insertadas.\n";
    }

    echo "¡Actualización completada!\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
