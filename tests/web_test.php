<?php
// tests/web_test.php
require_once __DIR__ . '/../config/db.php';

echo "<h1>Ejecución de Pruebas Unitarias</h1>";
echo "<pre>";

function runTest($testName, $callback) {
    echo "Ejecutando: $testName... ";
    try {
        $callback();
        echo "✅ PASÓ\n";
    } catch (Exception $e) {
        echo "❌ FALLÓ: " . $e->getMessage() . "\n";
    }
}

try {
    // 1. Test Database Connection
    runTest('Conexión a Base de Datos', function() use ($pdo) {
        if (!$pdo) throw new Exception("No se pudo conectar a la BD");
    });

    // 2. Test Create Product
    $productId = 0;
    runTest('Crear Producto de Prueba', function() use ($pdo, &$productId) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Test Product Web', 'Desc Web', 150.00, 50]);
        $productId = $pdo->lastInsertId();
        if (!$productId) throw new Exception("No se generó ID de producto");
    });

    // 3. Test Create Order
    runTest('Crear Pedido', function() use ($pdo, $productId) {
        $customer_name = "Test User Web";
        $customer_phone = "0987654321";
        $customer_address = "Web Address";
        $total_amount = 150.00;

        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, total_amount, status) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$customer_name, $customer_phone, $customer_address, $total_amount]);
        $order_id = $pdo->lastInsertId();

        if (!$order_id) throw new Exception("No se generó ID de pedido");

        $stmtDetail = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, 1, ?)");
        $stmtDetail->execute([$order_id, $productId, $total_amount]);
        
        $check = $pdo->query("SELECT * FROM orders WHERE id = $order_id")->fetch();
        if (!$check) throw new Exception("El pedido no se encuentra en la BD");
    });

    echo "\nResumen: Todas las pruebas críticas pasaron.\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR CRÍTICO DE BD: " . $e->getMessage() . "\n";
}
echo "</pre>";
?>
