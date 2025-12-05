<?php
session_start();
require_once '../config/db.php';

// --- Lógica de Backend (CRUD) ---

// Eliminar
if (isset($_GET['delete_product'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_product']]);
    header("Location: index.php?msg=deleted");
    exit;
}
if (isset($_GET['delete_category'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete_category']]);
    header("Location: index.php?tab=categories&msg=deleted");
    exit;
}

// Procesar Formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        // --- PRODUCTOS ---
        if ($action === 'save_product') {
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'];
            $cat_id = $_POST['category_id'];
            $desc = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];

            // Manejo de Imagen
            $imagePath = $_POST['current_image'] ?? '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadDir = '../assets/img/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                    $imagePath = 'assets/img/' . $fileName;
                }
            }

            if ($id) {
                // Update
                $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=? WHERE id=?");
                $stmt->execute([$cat_id, $name, $desc, $price, $stock, $imagePath, $id]);
            } else {
                // Create
                $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$cat_id, $name, $desc, $price, $stock, $imagePath]);
            }
            header("Location: index.php?msg=saved");
            exit;
        }

        // --- CATEGORÍAS ---
        if ($action === 'save_category') {
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'];
            $desc = $_POST['description'];

            if ($id) {
                $stmt = $pdo->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
                $stmt->execute([$name, $desc, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                $stmt->execute([$name, $desc]);
            }
            header("Location: index.php?tab=categories&msg=saved");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// --- Consultas de Datos ---
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$totalProducts = count($products);
$totalOrders = count($orders);
$totalSales = 0;
foreach ($orders as $o) $totalSales += $o['total_amount'];

// Alertas de Stock
$lowStockItems = [];
foreach ($products as $p) {
    if ($p['stock'] <= 12) {
        $lowStockItems[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Premium | Matia's Store</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Reusing Main Styles for Premium Look -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Admin Specific Overrides */
        
        /* IMPROVED Light Theme for Admin */
        [data-theme="light"] body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            color: #2d3748;
        }

        [data-theme="light"] .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.08);
            color: #2d3748;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        [data-theme="light"] .glass-card:hover {
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
        }

        [data-theme="light"] .table-glass {
            color: #2d3748;
        }

        [data-theme="light"] .table-glass th,
        [data-theme="light"] .table-glass td {
            border-color: rgba(0, 0, 0, 0.08);
            color: #2d3748;
        }

        [data-theme="light"] h1,
        [data-theme="light"] h2,
        [data-theme="light"] h3,
        [data-theme="light"] h4,
        [data-theme="light"] h5 {
            color: #1a202c !important;
        }

        [data-theme="light"] .nav-pills .nav-link {
            color: #4a5568;
        }

        [data-theme="light"] .nav-pills .nav-link.active {
            background: linear-gradient(45deg, #11998e, #38ef7d);
            color: white;
        }

        [data-theme="light"] .nav-pills .nav-link:hover:not(.active) {
            background: rgba(0, 0, 0, 0.05);
            color: #2d3748;
        }

        [data-theme="light"] .text-white {
            color: #2d3748 !important;
        }

        [data-theme="light"] .text-warning {
            color: #d69e2e !important;
        }

        [data-theme="light"] .text-muted {
            color: #718096 !important;
        }

        [data-theme="light"] .badge {
            color: #fff !important;
        }

        /* Ensure buttons stay colorful */
        [data-theme="light"] .btn-success {
            background: #48bb78;
            border-color: #48bb78;
            color: white;
        }

        [data-theme="light"] .btn-danger {
            background: #f56565;
            border-color: #f56565;
            color: white;
        }

        [data-theme="light"] .btn-primary {
            background: #4299e1;
            border-color: #4299e1;
            color: white;
        }

        [data-theme="light"] .btn-info {
            background: #0bc5ea;
            border-color: #0bc5ea;
            color: white;
        }

        /* Theme Toggle in Admin */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .admin-container {
            padding-top: 100px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            transition: transform 0.3s;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .table-glass {
            color: white;
        }

        .table-glass th,
        .table-glass td {
            border-color: rgba(255, 255, 255, 0.1);
            background: transparent !important;
            color: white;
        }

        .nav-pills .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border-radius: 50px;
            padding: 10px 25px;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(45deg, #11998e, #38ef7d);
            color: white;
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .stock-alert {
            border-left: 4px solid #ffc107;
        }

        .stock-critical {
            border-left: 4px solid #dc3545;
        }
    </style>
</head>

<body>

    <!-- Navbar Glassmorphism -->
    <nav class="navbar navbar-expand-lg fixed-top glass-nav">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="#">
                <i class="fas fa-user-shield me-2 text-warning"></i>ADMIN PANEL
            </a>
            <div class="d-flex">
                <a href="../index.php" target="_blank" class="btn btn-outline-light rounded-pill btn-sm">
                    <i class="fas fa-external-link-alt me-2"></i>Ver Tienda
                </a>
            </div>
        </div>
    </nav>

    <div class="container admin-container">

        <!-- Stock Alerts Section -->
        <?php if (count($lowStockItems) > 0): ?>
            <div class="glass-card stock-alert animate__animated animate__fadeInDown">
                <h4 class="text-warning mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Alertas de Inventario</h4>
                <div class="row g-3">
                    <?php foreach ($lowStockItems as $item): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="p-2 rounded <?php echo $item['stock'] == 0 ? 'bg-danger bg-opacity-25 border border-danger' : 'bg-warning bg-opacity-10 border border-warning'; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span class="badge <?php echo $item['stock'] == 0 ? 'bg-danger' : 'bg-warning text-dark'; ?>">
                                        <?php echo $item['stock'] == 0 ? 'AGOTADO' : $item['stock'] . ' unid.'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="glass-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total Productos</h6>
                        <h2 class="fw-bold mb-0"><?php echo $totalProducts; ?></h2>
                    </div>
                    <i class="fas fa-box stat-icon text-primary"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50">Ventas Totales</h6>
                        <h2 class="fw-bold mb-0">$<?php echo number_format($totalSales, 2); ?></h2>
                    </div>
                    <i class="fas fa-dollar-sign stat-icon text-success"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50">Pedidos</h6>
                        <h2 class="fw-bold mb-0"><?php echo $totalOrders; ?></h2>
                    </div>
                    <i class="fas fa-shopping-bag stat-icon text-warning"></i>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4 justify-content-center" id="adminTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#products"><i class="fas fa-box me-2"></i>Productos</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#categories"><i class="fas fa-tags me-2"></i>Categorías</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#orders"><i class="fas fa-clipboard-list me-2"></i>Pedidos</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">

            <!-- Products Tab -->
            <div class="tab-pane fade show active" id="products">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold">Gestión de Productos</h3>
                        <button class="btn btn-success rounded-pill shadow" onclick="openProductModal()">
                            <i class="fas fa-plus me-2"></i>Nuevo Producto
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-glass align-middle">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="../<?php echo $p['image'] ?: 'assets/img/no-image.png'; ?>" width="50" height="50" class="rounded-circle object-fit-cover border border-secondary">
                                        </td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                                        <td><span class="badge bg-primary bg-opacity-50 border border-primary"><?php echo htmlspecialchars($p['cat_name'] ?? 'Sin Cat.'); ?></span></td>
                                        <td class="text-success fw-bold">$<?php echo number_format($p['price'], 2); ?></td>
                                        <td>
                                            <?php if ($p['stock'] == 0): ?>
                                                <span class="badge bg-danger">Agotado</span>
                                            <?php elseif ($p['stock'] <= 12): ?>
                                                <span class="badge bg-warning text-dark"><?php echo $p['stock']; ?> (Bajo)</span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><?php echo $p['stock']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info rounded-circle me-1" onclick='openProductModal(<?php echo json_encode($p); ?>)'><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('index.php?delete_product=<?php echo $p['id']; ?>')"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Categories Tab -->
            <div class="tab-pane fade" id="categories">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold">Categorías</h3>
                        <button class="btn btn-success rounded-pill shadow" onclick="openCategoryModal()">
                            <i class="fas fa-plus me-2"></i>Nueva Categoría
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-glass align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $c): ?>
                                    <tr>
                                        <td>#<?php echo $c['id']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($c['name']); ?></td>
                                        <td class="text-white-50"><?php echo htmlspecialchars($c['description']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info rounded-circle me-1" onclick='openCategoryModal(<?php echo json_encode($c); ?>)'><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('index.php?delete_category=<?php echo $c['id']; ?>')"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Orders Tab -->
            <div class="tab-pane fade" id="orders">
                <div class="glass-card">
                    <h3 class="fw-bold mb-4">Últimos Pedidos</h3>
                    <div class="table-responsive">
                        <table class="table table-glass align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Contacto</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td>#<?php echo $o['id']; ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                            <div class="small text-white-50"><?php echo htmlspecialchars($o['customer_address']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($o['customer_phone']); ?></td>
                                        <td class="text-warning fw-bold">$<?php echo number_format($o['total_amount'], 2); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                                        <td><span class="badge bg-info text-dark"><?php echo strtoupper($o['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Producto (Glass) -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal text-white">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header border-bottom border-secondary">
                        <h5 class="modal-title fw-bold" id="productModalTitle">Nuevo Producto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save_product">
                        <input type="hidden" name="id" id="prodId">
                        <input type="hidden" name="current_image" id="prodCurrentImage">

                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="name" id="prodName" class="form-control bg-transparent text-white border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label>Categoría</label>
                            <select name="category_id" id="prodCat" class="form-select bg-transparent text-white border-secondary" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" class="text-dark"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Descripción</label>
                            <textarea name="description" id="prodDesc" class="form-control bg-transparent text-white border-secondary" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Precio</label>
                                <input type="number" step="0.01" name="price" id="prodPrice" class="form-control bg-transparent text-white border-secondary" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Stock</label>
                                <input type="number" name="stock" id="prodStock" class="form-control bg-transparent text-white border-secondary" value="10">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Imagen</label>
                            <input type="file" name="image" class="form-control bg-transparent text-white border-secondary" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Categoría (Glass) -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal text-white">
                <form method="POST">
                    <div class="modal-header border-bottom border-secondary">
                        <h5 class="modal-title fw-bold" id="catModalTitle">Nueva Categoría</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save_category">
                        <input type="hidden" name="id" id="catId">

                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="name" id="catName" class="form-control bg-transparent text-white border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label>Descripción</label>
                            <textarea name="description" id="catDesc" class="form-control bg-transparent text-white border-secondary"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        <th>Categoría</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <img src="../<?php echo $p['image'] ?: 'assets/img/no-image.png'; ?>" width="50" height="50" class="rounded-circle object-fit-cover border border-secondary">
                    </td>
                    <td class="fw-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><span class="badge bg-primary bg-opacity-50 border border-primary"><?php echo htmlspecialchars($p['cat_name'] ?? 'Sin Cat.'); ?></span></td>
                    <td class="text-success fw-bold">$<?php echo number_format($p['price'], 2); ?></td>
                    <td>
                        <?php if ($p['stock'] == 0): ?>
                            <span class="badge bg-danger">Agotado</span>
                        <?php elseif ($p['stock'] <= 12): ?>
                            <span class="badge bg-warning text-dark"><?php echo $p['stock']; ?> (Bajo)</span>
                        <?php else: ?>
                            <span class="badge bg-success"><?php echo $p['stock']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-info rounded-circle me-1" onclick='openProductModal(<?php echo json_encode($p); ?>)'><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('index.php?delete_product=<?php echo $p['id']; ?>')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
    </div>
    </div>

    <!-- Categories Tab -->
    <div class="tab-pane fade" id="categories">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Categorías</h3>
                <button class="btn btn-success rounded-pill shadow" onclick="openCategoryModal()">
                    <i class="fas fa-plus me-2"></i>Nueva Categoría
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-glass align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                            <tr>
                                <td>#<?php echo $c['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($c['name']); ?></td>
                                <td class="text-white-50"><?php echo htmlspecialchars($c['description']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info rounded-circle me-1" onclick='openCategoryModal(<?php echo json_encode($c); ?>)'><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete('index.php?delete_category=<?php echo $c['id']; ?>')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Orders Tab -->
    <div class="tab-pane fade" id="orders">
        <div class="glass-card">
            <h3 class="fw-bold mb-4">Últimos Pedidos</h3>
            <div class="table-responsive">
                <table class="table table-glass align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td>#<?php echo $o['id']; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                    <div class="small text-white-50"><?php echo htmlspecialchars($o['customer_address']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($o['customer_phone']); ?></td>
                                <td class="text-warning fw-bold">$<?php echo number_format($o['total_amount'], 2); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo strtoupper($o['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>
    </div>

    <!-- Modal Producto (Glass) -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal text-white">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header border-bottom border-secondary">
                        <h5 class="modal-title fw-bold" id="productModalTitle">Nuevo Producto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save_product">
                        <input type="hidden" name="id" id="prodId">
                        <input type="hidden" name="current_image" id="prodCurrentImage">

                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="name" id="prodName" class="form-control bg-transparent text-white border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label>Categoría</label>
                            <select name="category_id" id="prodCat" class="form-select bg-transparent text-white border-secondary" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" class="text-dark"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Descripción</label>
                            <textarea name="description" id="prodDesc" class="form-control bg-transparent text-white border-secondary" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Precio</label>
                                <input type="number" step="0.01" name="price" id="prodPrice" class="form-control bg-transparent text-white border-secondary" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Stock</label>
                                <input type="number" name="stock" id="prodStock" class="form-control bg-transparent text-white border-secondary" value="10">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Imagen</label>
                            <input type="file" name="image" class="form-control bg-transparent text-white border-secondary" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Categoría (Glass) -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal text-white">
                <form method="POST">
                    <div class="modal-header border-bottom border-secondary">
                        <h5 class="modal-title fw-bold" id="catModalTitle">Nueva Categoría</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save_category">
                        <input type="hidden" name="id" id="catId">

                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="name" id="catName" class="form-control bg-transparent text-white border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label>Descripción</label>
                            <textarea name="description" id="catDesc" class="form-control bg-transparent text-white border-secondary"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Modales
        let prodModal;
        let catModal;

        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar Modales
            prodModal = new bootstrap.Modal(document.getElementById('productModal'));
            catModal = new bootstrap.Modal(document.getElementById('categoryModal'));

            // Tab Persistence
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                const trigger = document.querySelector(`button[data-bs-target="#${tab}"]`);
                if (trigger) {
                    const tabInstance = new bootstrap.Tab(trigger);
                    tabInstance.show();
                }
            }

            // Messages
            const msg = params.get('msg');
            if (msg) {
                if (msg === 'saved') Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    background: '#1e1e1e',
                    color: '#fff'
                });
                if (msg === 'deleted') Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    background: '#1e1e1e',
                    color: '#fff'
                });

                // Clean URL to prevent alert on reload
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (tab ? '?tab=' + tab : '');
                window.history.replaceState({
                    path: newUrl
                }, '', newUrl);
            }
        });

        function openProductModal(data = null) {
            document.getElementById('productModalTitle').textContent = data ? 'Editar Producto' : 'Nuevo Producto';
            document.getElementById('prodId').value = data ? data.id : '';
            document.getElementById('prodName').value = data ? data.name : '';
            document.getElementById('prodCat').value = data ? data.category_id : '';
            document.getElementById('prodDesc').value = data ? data.description : '';
            document.getElementById('prodPrice').value = data ? data.price : '';
            document.getElementById('prodStock').value = data ? data.stock : '10';
            document.getElementById('prodCurrentImage').value = data ? data.image : '';
            prodModal.show();
        }

        function openCategoryModal(data = null) {
            document.getElementById('catModalTitle').textContent = data ? 'Editar Categoría' : 'Nueva Categoría';
            document.getElementById('catId').value = data ? data.id : '';
            document.getElementById('catName').value = data ? data.name : '';
            document.getElementById('catDesc').value = data ? data.description : '';
            catModal.show();
        }

        function confirmDelete(url) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1e1e1e',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        }
    </script>

    <script>
        // Theme Management
        function initializeTheme() {
            const savedTheme = localStorage.getItem('matias_theme') || 'dark';
            document.body.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
        }

        function toggleTheme() {
            const currentTheme = document.body.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.body.setAttribute('data-theme', newTheme);
            localStorage.setItem('matias_theme', newTheme);
            updateThemeIcon(newTheme);
        }

        function updateThemeIcon(theme) {
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                const icon = themeToggle.querySelector('i');
                if (theme === 'dark') {
                    icon.className = 'fas fa-moon';
                } else {
                    icon.className = 'fas fa-sun';
                }
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', initializeTheme);
    </script>
</body>

</html>