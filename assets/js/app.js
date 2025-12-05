// assets/js/app.js

const ADMIN_PHONE = '573143632877';
let cart = [];

// Cargar carrito del localStorage al iniciar
document.addEventListener('DOMContentLoaded', () => {
    const savedCart = localStorage.getItem('matias_cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartUI();
    }

    // Initialize theme
    initializeTheme();
});

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

// Filtrar Productos
function filterProducts(categoryId) {
    const products = document.querySelectorAll('.product-item');
    const buttons = document.querySelectorAll('#categoryFilters button');

    // Actualizar botones activos
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    products.forEach(prod => {
        if (categoryId === 'all' || prod.dataset.category === categoryId) {
            prod.style.display = 'block';
            prod.classList.add('animate__animated', 'animate__fadeIn');
        } else {
            prod.style.display = 'none';
        }
    });
}

// Agregar al Carrito
function addToCart(product) {
    const existing = cart.find(item => item.id === product.id);

    if (existing) {
        existing.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            image: product.image,
            quantity: 1
        });
    }

    saveCart();
    updateCartUI();

    // Feedback visual
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        background: '#1e1e1e',
        color: '#fff'
    });
    toast.fire({
        icon: 'success',
        title: 'Agregado al carrito'
    });

    openCart(); // Abrir carrito automáticamente
}

// Eliminar del Carrito
function removeFromCart(id) {
    // Convertir a número para asegurar coincidencia
    const numId = parseInt(id);
    cart = cart.filter(item => parseInt(item.id) !== numId);
    saveCart();
    updateCartUI();
}

// Actualizar Cantidad
function updateQuantity(id, change) {
    const numId = parseInt(id);
    const item = cart.find(item => parseInt(item.id) === numId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(numId);
        } else {
            saveCart();
            updateCartUI();
        }
    }
}

// Guardar en LocalStorage
function saveCart() {
    localStorage.setItem('matias_cart', JSON.stringify(cart));
}

// Actualizar UI del Carrito
function updateCartUI() {
    const cartCount = document.getElementById('cartCount');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');

    // Actualizar contador
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;

    // Calcular total precio
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    cartTotal.textContent = '$' + total.toLocaleString('es-CO', { minimumFractionDigits: 2 });

    // Renderizar items
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="text-center mt-5 text-white-50">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Tu carrito está vacío</p>
            </div>`;
    } else {
        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="card mb-3 bg-dark border-secondary text-white">
                <div class="row g-0 align-items-center">
                    <div class="col-3 p-2">
                        <img src="${item.image || 'https://via.placeholder.com/100'}" class="img-fluid rounded" alt="${item.name}">
                    </div>
                    <div class="col-9">
                        <div class="card-body py-2 px-0 pe-2">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title mb-1 text-truncate">${item.name}</h6>
                                <button class="btn btn-link text-danger p-0" onclick="removeFromCart(${item.id})"><i class="fas fa-times"></i></button>
                            </div>
                            <p class="card-text small text-warning mb-1">$${item.price.toLocaleString('es-CO')}</p>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQuantity(${item.id}, -1)">-</button>
                                <span class="mx-2 small">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQuantity(${item.id}, 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

// Abrir Carrito
function openCart() {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
    offcanvas.show();
}

// Abrir Checkout
function openCheckoutModal() {
    if (cart.length === 0) {
        Swal.fire('Carrito Vacío', 'Agrega productos antes de procesar.', 'warning');
        return;
    }
    const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    modal.show();
}

// Procesar Pedido
document.getElementById('checkoutForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const name = document.getElementById('customerName').value;
    const phone = document.getElementById('customerPhone').value;
    const address = document.getElementById('customerAddress').value;
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    // Datos para guardar
    const orderData = {
        name,
        phone,
        address,
        total,
        items: cart
    };

    // Notificación
    Swal.fire({
        title: 'Procesando Pedido...',
        text: 'Por favor espera un momento.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Enviar al Backend
    fetch('api/save_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Pedido Recibido!',
                    html: `Te estaremos llamando al <strong>${phone}</strong> para confirmar la dirección de envío.<br><br>Te redirigiremos a WhatsApp para enviar el detalle.`,
                    timer: 4000,
                    showConfirmButton: false
                }).then(() => {
                    redirectToWhatsApp(orderData, result.order_id);
                    // Limpiar carrito
                    cart = [];
                    saveCart();
                    updateCartUI();
                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                    bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas')).hide();
                });
            } else {
                Swal.fire('Error', 'No se pudo guardar el pedido: ' + result.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
});

function redirectToWhatsApp(data, orderId) {
    const date = new Date().toLocaleString('es-CO');
    let itemsList = data.items.map(item => `- ${item.quantity}x ${item.name} ($${item.price.toLocaleString('es-CO')} c/u)`).join('\n');

    const message = `Hola Matia's Store, quiero confirmar mi pedido #${orderId}:\n\n` +
        `📅 *Fecha:* ${date}\n` +
        `👤 *Cliente:* ${data.name}\n` +
        `📞 *Teléfono:* ${data.phone}\n` +
        `📍 *Dirección:* ${data.address}\n\n` +
        `🛒 *Detalle del Pedido:*\n${itemsList}\n\n` +
        `💰 *Total a Pagar:* $${data.total.toLocaleString('es-CO')}`;

    const url = `https://wa.me/${ADMIN_PHONE}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}
