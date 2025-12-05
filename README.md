# 🏪 Matia's Store - Sistema de Inventario y Tienda Online

Sistema completo de gestión de inventario con tienda online, panel de administración y procesamiento de pedidos vía WhatsApp, desarrollado en PHP puro con MySQL.

![PHP](https://img.shields.io/badge/PHP-8.0+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-MIT-yellow)

## 📋 Tabla de Contenidos

- [Características](#características)
- [Tecnologías](#tecnologías)
- [Requisitos Previos](#requisitos-previos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Módulos del Sistema](#módulos-del-sistema)
- [Pruebas Unitarias](#pruebas-unitarias)
- [Uso del Sistema](#uso-del-sistema)
- [API Endpoints](#api-endpoints)
- [Troubleshooting](#troubleshooting)

## ✨ Características

### 🛒 Tienda Online (Frontend Público)
- ✅ Diseño moderno con **Glassmorphism** y tema oscuro
- ✅ Efectos 3D en tarjetas de productos (Vanilla Tilt.js)
- ✅ Animaciones fluidas (Animate.css)
- ✅ Carrito de compras persistente (LocalStorage)
- ✅ Filtrado de productos por categorías
- ✅ Checkout con formulario de datos del cliente
- ✅ Integración con WhatsApp para confirmación de pedidos
- ✅ **Totalmente responsive** (móvil, tablet, desktop)

### 🎛️ Panel de Administración
- ✅ CRUD completo de **Productos**
- ✅ CRUD completo de **Categorías**
- ✅ Gestión de **Pedidos** con estados (pendiente, completado, cancelado)
- ✅ Dashboard con estadísticas en tiempo real
- ✅ **Alertas de stock bajo** (≤ 12 unidades)
- ✅ **Alertas de productos agotados** (0 unidades)
- ✅ Interfaz premium con glassmorphism
- ✅ Modales interactivos (SweetAlert2)

### 📦 Gestión de Pedidos
- ✅ Validación de productos antes de crear pedidos
- ✅ Detección de productos "fantasma" (eliminados del inventario)
- ✅ Notificación al cliente sobre confirmación telefónica
- ✅ Mensaje detallado de WhatsApp con:
  - Fecha y hora del pedido
  - Datos del cliente
  - Lista de productos con precios individuales
  - Total a pagar

### 🔒 Seguridad y Validación
- ✅ Validación de integridad referencial (Foreign Keys)
- ✅ Transacciones de base de datos (ACID)
- ✅ Manejo de errores con rollback automático
- ✅ Sanitización de datos (htmlspecialchars, prepared statements)

## 🛠️ Tecnologías

### Backend
- **PHP 8.0+** - Lenguaje del servidor
- **MySQL 8.0+** - Base de datos relacional
- **PDO** - Capa de abstracción de base de datos

### Frontend
- **HTML5** - Estructura
- **CSS3** - Estilos (Glassmorphism, gradientes, animaciones)
- **JavaScript (Vanilla)** - Lógica del cliente
- **Bootstrap 5.3** - Framework CSS
- **Font Awesome 6.4** - Iconos
- **Animate.css 4.1** - Animaciones CSS
- **Vanilla Tilt.js 1.8** - Efectos 3D
- **SweetAlert2** - Alertas modernas

### Herramientas
- **Git** - Control de versiones
- **PHP Built-in Server** - Servidor de desarrollo

## 📋 Requisitos Previos

- PHP 8.0 o superior
- MySQL 8.0 o superior
- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- Git (opcional)

## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/matias-store.git
cd matias-store
```

### 2. Configurar la Base de Datos

#### Opción A: Usando el instalador automático

1. Navega a `http://localhost:8080/install.php`
2. Sigue las instrucciones en pantalla

#### Opción B: Configuración manual

1. Crea la base de datos:

```sql
CREATE DATABASE matias_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importa el esquema:

```bash
mysql -u root -p matias_store < database.sql
```

3. Configura las credenciales en `config/db.php`:

```php
<?php
$host = 'localhost';
$dbname = 'matias_store';
$username = 'root';
$password = 'tu_contraseña';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
```

### 3. Iniciar el Servidor

#### Para acceso local (solo desde tu PC):

```bash
php -S localhost:8080
```

#### Para acceso desde dispositivos móviles en la misma red:

```bash
php -S 0.0.0.0:8080
```

Luego accede desde tu móvil usando la IP local:
```
http://192.168.x.x:8080
```

Para encontrar tu IP local:
```bash
# Windows
ipconfig | findstr "IPv4"

# Linux/Mac
ifconfig | grep "inet "
```

### 4. Acceder al Sistema

- **Tienda:** `http://localhost:8080/`
- **Admin:** `http://localhost:8080/admin/`

## 📁 Estructura del Proyecto

```
Proyecto1_v2/
│
├── admin/
│   └── index.php              # Panel de administración completo
│
├── api/
│   └── save_order.php         # Endpoint para guardar pedidos
│
├── assets/
│   ├── css/
│   │   └── style.css          # Estilos personalizados
│   └── js/
│       └── app.js             # Lógica del carrito y checkout
│
├── config/
│   └── db.php                 # Configuración de base de datos
│
├── tests/
│   ├── unit_tests.php         # Pruebas unitarias CRUD
│   └── web_test.php           # Pruebas de integración
│
├── index.php                  # Página principal de la tienda
├── database.sql               # Esquema de base de datos
├── install.php                # Instalador automático
└── README.md                  # Este archivo
```

## 🎯 Módulos del Sistema

### 1. Tienda Online (`index.php`)

**Funcionalidades:**
- Visualización de productos con imágenes
- Filtrado por categorías
- Carrito de compras interactivo
- Checkout con formulario
- Integración con WhatsApp

**Características técnicas:**
- Persistencia del carrito en `localStorage`
- Validación de formularios
- Manejo de estados (vacío, con productos)
- Animaciones y efectos 3D

### 2. Panel de Administración (`admin/index.php`)

**Secciones:**

#### Dashboard
- Total de productos
- Total de categorías
- Total de pedidos
- Alertas de stock (bajo y agotado)

#### Gestión de Productos
- **Crear:** Formulario con nombre, descripción, precio, stock, categoría e imagen
- **Leer:** Tabla con todos los productos y sus detalles
- **Actualizar:** Modal de edición con datos precargados
- **Eliminar:** Confirmación con SweetAlert2

#### Gestión de Categorías
- **Crear:** Formulario con nombre y descripción
- **Leer:** Tabla con todas las categorías
- **Actualizar:** Modal de edición
- **Eliminar:** Confirmación con SweetAlert2

#### Gestión de Pedidos
- Visualización de todos los pedidos
- Cambio de estado (pendiente → completado/cancelado)
- Detalles de cada pedido (productos, cantidades, total)

### 3. API de Pedidos (`api/save_order.php`)

**Endpoint:** `POST /api/save_order.php`

**Request Body:**
```json
{
  "name": "Juan Pérez",
  "phone": "3001234567",
  "address": "Calle 123 #45-67",
  "total": 150000,
  "items": [
    {
      "id": 1,
      "name": "Producto 1",
      "price": 50000,
      "quantity": 2
    },
    {
      "id": 2,
      "name": "Producto 2",
      "price": 50000,
      "quantity": 1
    }
  ]
}
```

**Response (Éxito):**
```json
{
  "success": true,
  "order_id": 123
}
```

**Response (Error - Producto no existe):**
```json
{
  "success": false,
  "message": "Los siguientes productos ya no están disponibles: Mouse Gamer. Por favor elimínalos de tu carrito."
}
```

**Validaciones:**
1. Verifica que todos los productos existan en la base de datos
2. Si algún producto fue eliminado, devuelve error específico
3. Usa transacciones para garantizar integridad
4. Rollback automático en caso de error

## 🧪 Pruebas Unitarias

### Ejecutar las Pruebas

Navega a:
```
http://localhost:8080/tests/unit_tests.php
```

### Tests Implementados

#### Categorías (CRUD Completo)
1. ✅ **Create:** Insertar nueva categoría
2. ✅ **Read:** Leer categoría por ID
3. ✅ **Update:** Actualizar nombre y descripción
4. ✅ **Delete:** Eliminar categoría

#### Productos (CRUD Completo)
1. ✅ **Create:** Insertar nuevo producto con categoría
2. ✅ **Read:** Leer producto por ID
3. ✅ **Update:** Actualizar precio y stock
4. ✅ **Delete:** Eliminar producto

### Resultados de las Pruebas

![Unit Test Results](file:///C:/Users/jhost/.gemini/antigravity/brain/030097c8-5f54-4da2-853f-2b1c37cde93b/unit_test_results_1764974620526.png)

**Resumen:**
- Total de pruebas: 8
- Exitosas: 8
- Fallidas: 0
- Tasa de éxito: 100%

### Evidencia en Video

La ejecución completa de las pruebas fue grabada y está disponible en:
```
file:///C:/Users/jhost/.gemini/antigravity/brain/030097c8-5f54-4da2-853f-2b1c37cde93b/unit_tests_execution_1764974590015.webp
```

## 📖 Uso del Sistema

### Para Clientes (Tienda Online)

1. **Navegar productos:**
   - Abre `http://localhost:8080/`
   - Explora los productos disponibles
   - Usa los filtros de categorías

2. **Agregar al carrito:**
   - Haz clic en "Agregar al Carrito"
   - El carrito se abre automáticamente
   - Ajusta cantidades con los botones +/-

3. **Realizar pedido:**
   - Haz clic en "Procesar Pedido"
   - Completa el formulario (nombre, teléfono, dirección)
   - Haz clic en "Confirmar y Enviar"
   - Serás redirigido a WhatsApp con el detalle completo

### Para Administradores (Panel Admin)

1. **Acceder al panel:**
   - Abre `http://localhost:8080/admin/`

2. **Gestionar productos:**
   - Haz clic en "Nuevo Producto"
   - Completa el formulario
   - Guarda los cambios
   - Para editar: clic en el ícono de lápiz
   - Para eliminar: clic en el ícono de basura

3. **Gestionar categorías:**
   - Haz clic en "Nueva Categoría"
   - Ingresa nombre y descripción
   - Guarda los cambios

4. **Ver pedidos:**
   - Revisa la tabla de pedidos
   - Cambia el estado según corresponda
   - Verifica los detalles de cada pedido

## 🔧 Configuración Avanzada

### Cambiar el Número de WhatsApp

Edita `assets/js/app.js`:

```javascript
const ADMIN_PHONE = '573143632877'; // Cambia este número
```

### Personalizar Alertas de Stock

Edita `admin/index.php`:

```php
// Línea ~40
$lowStockThreshold = 12; // Cambia el umbral de stock bajo
```

### Modificar Estilos

Edita `assets/css/style.css`:

```css
:root {
    --accent-color: #89CFF0; /* Color de acento (azul claro) */
    --dark-bg: #0a0a0f;      /* Fondo oscuro */
    --card-bg: rgba(20, 20, 25, 0.7); /* Fondo de tarjetas */
}
```

## 🐛 Troubleshooting

### Error: "No se puede conectar a la base de datos"

**Solución:**
1. Verifica que MySQL esté corriendo
2. Revisa las credenciales en `config/db.php`
3. Asegúrate de que la base de datos `matias_store` exista

### Error: "SQLSTATE[23000]: Integrity constraint violation"

**Solución:**
Este error ocurre cuando intentas crear un pedido con productos que ya no existen.

1. Abre el carrito
2. Elimina todos los productos
3. Agrega productos nuevos desde la tienda
4. Intenta de nuevo

### El carrito no guarda los productos

**Solución:**
1. Verifica que tu navegador permita `localStorage`
2. Limpia la caché del navegador
3. Recarga la página con Ctrl+F5

### No puedo acceder desde el móvil

**Solución:**
1. Asegúrate de usar `php -S 0.0.0.0:8080`
2. Verifica que ambos dispositivos estén en la misma red WiFi
3. Desactiva temporalmente el firewall de Windows
4. Usa la IP correcta (verifica con `ipconfig`)

### Las animaciones no funcionan

**Solución:**
1. Verifica que tengas conexión a internet (CDNs)
2. Limpia la caché del navegador
3. Revisa la consola del navegador (F12) para errores

## 📱 Acceso Móvil

### Configuración

1. Inicia el servidor con:
   ```bash
   php -S 0.0.0.0:8080
   ```

2. Encuentra tu IP local:
   ```bash
   ipconfig | findstr "IPv4"
   ```

3. En tu móvil, abre el navegador y accede a:
   ```
   http://TU_IP_LOCAL:8080
   ```

### URLs Móviles

- **Tienda:** `http://192.168.1.2:8080/`
- **Admin:** `http://192.168.1.2:8080/admin/`

*(Reemplaza `192.168.1.2` con tu IP local)*

## 🎨 Diseño y UX

### Paleta de Colores

- **Fondo oscuro:** `#0a0a0f`
- **Acento (azul claro):** `#89CFF0`
- **Tarjetas:** `rgba(20, 20, 25, 0.7)`
- **Texto:** `#e0e0e0`

### Efectos Visuales

- **Glassmorphism:** Fondo translúcido con `backdrop-filter: blur()`
- **Gradientes:** Texto con degradado de blanco a azul claro
- **Sombras:** Glow effect con `box-shadow` y color de acento
- **3D Tilt:** Efecto de inclinación en tarjetas de productos
- **Animaciones:** Fade in, zoom, bounce (Animate.css)

### Responsividad

El sistema es completamente responsive con breakpoints en:
- **992px:** Tablets
- **768px:** Móviles grandes
- **576px:** Móviles pequeños

## 📊 Base de Datos

### Esquema

#### Tabla: `categories`
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tabla: `products`
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT,
    image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
```

#### Tabla: `orders`
```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(200) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tabla: `order_details`
```sql
CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);
```

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Haz fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**Jhostin Joel**
- GitHub: [@JhostinJoel](https://github.com/JhostinJoel)
- LinkedIn: [Jhostin Joel](https://linkedin.com/in/jhostinjoel)

## 🙏 Agradecimientos

- Bootstrap por el framework CSS
- Font Awesome por los iconos
- SweetAlert2 por las alertas modernas
- Vanilla Tilt.js por los efectos 3D
- Animate.css por las animaciones

---

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub!
