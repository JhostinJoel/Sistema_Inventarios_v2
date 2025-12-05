<?php
require_once 'config/db.php';

// Obtener categorías
$stmtCat = $pdo->query("SELECT * FROM categories");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos (con nombre de categoría)
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matia's Store | Estilo y Calidad</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- Navbar Glassmorphism -->
    <nav class="navbar navbar-expand-lg fixed-top glass-nav">
        <div class="container">
            <a class="navbar-brand text-white fw-bold animate__animated animate__fadeInLeft" href="#">
                <i class="fas fa-store me-2 text-warning"></i>MATIA'S STORE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Categorías</a>
                        <ul class="dropdown-menu glass-dropdown">
                            <li><a class="dropdown-item text-white" href="#" onclick="filterProducts('all')">Todas</a></li>
                            <?php foreach ($categories as $cat): ?>
                                <li><a class="dropdown-item text-white" href="#" onclick="filterProducts('<?php echo $cat['id']; ?>')"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#productos">Productos</a></li>
                    <li class="nav-item ms-3">
                        <button class="theme-toggle" onclick="toggleTheme()" id="themeToggle" title="Cambiar tema">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                    <li class="nav-item ms-2">
                        <button class="btn btn-warning rounded-pill position-relative" onclick="openCart()">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount">0</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="inicio" class="hero-section d-flex align-items-center text-center text-white">
        <div class="container position-relative z-2">
            <h1 class="display-1 fw-bold animate__animated animate__zoomIn text-gradient-hero">MATIA'S STORE</h1>
            <p class="lead animate__animated animate__fadeInUp animate__delay-1s fs-3 mb-4">El futuro del estilo está aquí.</p>
            <a href="#productos" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-bold animate__animated animate__bounceIn animate__delay-2s shadow-lg hover-scale">
                Explorar Colección <i class="fas fa-rocket ms-2"></i>
            </a>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-particles" id="particles-js"></div>
    </header>

    <!-- Products Section -->
    <section id="productos" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title fw-bold text-white animate__animated animate__fadeIn">Nuestra Colección</h2>
                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap" id="categoryFilters">
                    <button class="btn btn-outline-light rounded-pill px-4 active" onclick="filterProducts('all')">Todo</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="btn btn-outline-light rounded-pill px-4" onclick="filterProducts('<?php echo $cat['id']; ?>')"><?php echo htmlspecialchars($cat['name']); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row g-4" id="productsGrid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-lg-3 product-item" data-category="<?php echo $product['category_id']; ?>">
                            <!-- Added data-tilt for 3D effect -->
                            <div class="card product-card h-100 border-0 shadow-lg" data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.5">
                                <div class="product-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://via.placeholder.com/300x300?text=No+Image'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="overlay d-flex justify-content-center align-items-center gap-2">
                                        <button class="btn btn-light rounded-circle shadow p-3" onclick="addToCart(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                            <i class="fas fa-cart-plus text-primary fa-lg"></i>
                                        </button>
                                    </div>
                                    <span class="badge bg-primary position-absolute top-0 end-0 m-3 py-2 px-3 rounded-pill shadow"><?php echo htmlspecialchars($product['category_name'] ?? 'General'); ?></span>
                                </div>
                                <div class="card-body text-center text-white">
                                    <h5 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-white-50 small mb-3"><?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?></p>
                                    <h4 class="price-tag mb-3">$<?php echo number_format($product['price'], 2); ?></h4>
                                    <button class="btn btn-primary w-100 rounded-pill btn-buy py-2 fw-bold" onclick="addToCart(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                        Agregar al Carrito
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info glass-alert">
                            <i class="fas fa-info-circle me-2"></i> No hay productos disponibles.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Cart Offcanvas -->
    <div class="offcanvas offcanvas-end glass-offcanvas text-white" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold"><i class="fas fa-shopping-bag me-2"></i>Tu Carrito</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <div id="cartItems" class="flex-grow-1 overflow-auto">
                <!-- Items will be injected here -->
                <div class="text-center mt-5 text-white-50">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <p>Tu carrito está vacío</p>
                </div>
            </div>

            <div class="border-top border-secondary pt-3 mt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="h5 mb-0">Total:</span>
                    <span class="h4 fw-bold text-warning mb-0" id="cartTotal">$0.00</span>
                </div>
                <button class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-lg hover-scale" onclick="openCheckoutModal()">
                    <i class="fab fa-whatsapp me-2"></i>Procesar Pedido
                </button>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-modal text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Finalizar Compra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="checkoutForm">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control bg-transparent text-white border-secondary" id="customerName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono / WhatsApp</label>
                            <input type="tel" class="form-control bg-transparent text-white border-secondary" id="customerPhone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección de Entrega</label>
                            <textarea class="form-control bg-transparent text-white border-secondary" id="customerAddress" rows="2" required></textarea>
                        </div>
                        <div class="alert alert-warning bg-opacity-10 border-warning text-warning small">
                            <i class="fas fa-info-circle me-1"></i> Se enviará tu pedido a WhatsApp para coordinar el pago y envío.
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Confirmar y Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black text-white py-5 mt-5 border-top border-secondary">
        <div class="container text-center">
            <h3 class="fw-bold mb-3">MATIA'S STORE</h3>
            <p class="text-white-50 mb-4">Innovación y estilo en cada detalle.</p>
            <div class="d-flex justify-content-center gap-3 mb-4">
                <a href="#" class="text-white fs-4 hover-scale"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white fs-4 hover-scale"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white fs-4 hover-scale"><i class="fab fa-twitter"></i></a>
            </div>
            <p class="mb-0 fw-light small">&copy; <?php echo date('Y'); ?> Matia's Store. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Vanilla Tilt for 3D Effects -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>