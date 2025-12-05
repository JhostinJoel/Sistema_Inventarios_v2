<?php
// api/save_order.php
header('Content-Type: application/json');
require_once '../config/db.php';

// Recibir JSON raw
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$data) {
            throw new Exception("No se recibieron datos JSON válidos");
        }

        $customer_name = $data['name'] ?? '';
        $customer_phone = $data['phone'] ?? '';
        $customer_address = $data['address'] ?? '';
        $total_amount = $data['total'] ?? 0;
        $items = $data['items'] ?? [];

        if (empty($items) || !$customer_name) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        $pdo->beginTransaction();

        // 1. Crear la Orden
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, total_amount, status) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$customer_name, $customer_phone, $customer_address, $total_amount]);
        $order_id = $pdo->lastInsertId();

        // 2. Crear Detalles
        $stmtDetail = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $checkProduct = $pdo->prepare("SELECT id FROM products WHERE id = ?");

        $missingItems = [];

        // Primera pasada: Verificar existencia
        foreach ($items as $item) {
            $checkProduct->execute([$item['id']]);
            if (!$checkProduct->fetch()) {
                $missingItems[] = $item['name'];
            }
        }

        if (!empty($missingItems)) {
            $pdo->rollBack();
            $names = implode(", ", $missingItems);
            echo json_encode([
                'success' => false,
                'message' => "Los siguientes productos ya no están disponibles: $names. Por favor elimínalos de tu carrito."
            ]);
            exit;
        }

        // Segunda pasada: Insertar
        foreach ($items as $item) {
            $stmtDetail->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        $pdo->commit();

        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
