# 🏪 Sistema de Inventario Inteligente

Sistema completo de gestión de inventario con punto de venta, reportes y chatbot con IA desarrollado en Django.

![Python](https://img.shields.io/badge/Python-3.12-blue)
![Django](https://img.shields.io/badge/Django-5.2-green)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-MIT-yellow)

## 📋 Tabla de Contenidos

- [Características](#características)
- [Tecnologías](#tecnologías)
- [Requisitos Previos](#requisitos-previos)
- [Instalación Paso a Paso](#instalación-paso-a-paso)
- [Configuración de la Base de Datos](#configuración-de-la-base-de-datos)
- [Configuración del Chatbot IA](#configuración-del-chatbot-ia)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Módulos del Sistema](#módulos-del-sistema)
- [Usuarios y Roles](#usuarios-y-roles)
- [API y Endpoints](#api-y-endpoints)
- [Troubleshooting](#troubleshooting)

## ✨ Características

### Gestión de Inventario
- ✅ CRUD completo de productos
- ✅ Categorías y almacenes
- ✅ Control de stock en tiempo real
- ✅ Alertas de stock bajo
- ✅ Movimientos de inventario (entradas/salidas)

### Punto de Venta (POS)
- ✅ Interfaz moderna e intuitiva
- ✅ Carrito de compras interactivo
- ✅ Registro de ventas
- ✅ Asociación con clientes
- ✅ Actualización automática de stock

### Reportes
- ✅ Reportes de ventas por rango de fechas
- ✅ Cálculo automático de ingresos totales
- ✅ Visualización detallada de transacciones
- ✅ Exportación de datos

### Chatbot con IA
- 🤖 Asistente virtual integrado (Google Gemini)
- 🤖 Consultas sobre inventario y stock
- 🤖 Información de ventas
- 🤖 Respuestas contextuales

### Gestión de Usuarios
- 👤 Sistema de autenticación robusto
- 👤 Roles: Administrador, Vendedor, Almacén
- 👤 Permisos granulares
- 👤 Recuperación de contraseña

## 🛠 Tecnologías

### Backend
- **Django 5.2** - Framework web principal
- **Python 3.12** - Lenguaje de programación
- **SQLite** - Base de datos (configurable a MySQL/PostgreSQL)
- **Google Generative AI** - Chatbot inteligente

### Frontend
- **Bootstrap 5.3** - Framework CSS
- **Font Awesome 6.4** - Iconos
- **SweetAlert2** - Notificaciones elegantes
- **JavaScript Vanilla** - Interactividad

### Utilidades
- **python-dotenv** - Gestión de variables de entorno
- **crispy-forms** - Formularios mejorados

## 📦 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **Python 3.10 o superior** - [Descargar](https://www.python.org/downloads/)
- **Git** - [Descargar](https://git-scm.com/downloads)
- **pip** - Gestor de paquetes de Python (incluido con Python)
- **Navegador moderno** - Chrome, Firefox, Edge

## 🚀 Instalación Paso a Paso

### 1️⃣ Clonar el Repositorio

```bash
# Usando HTTPS
git clone https://github.com/JhostinJoel/Sistema-de-Inventarios.git

# O usando SSH
git clone git@github.com:JhostinJoel/Sistema-de-Inventarios.git

# Entrar al directorio del proyecto
cd Sistema-de-Inventarios
```

### 2️⃣ Crear Entorno Virtual

```bash
# En Windows
python -m venv venv
venv\Scripts\activate

# En macOS/Linux
python3 -m venv venv
source venv/bin/activate
```

> 💡 **Nota**: Deberías ver `(venv)` al inicio de tu línea de comandos

### 3️⃣ Instalar Dependencias

```bash
pip install -r requirements.txt
```

**Paquetes principales que se instalarán:**
- Django==5.2.8
- google-generativeai
- python-dotenv
- django-crispy-forms
- crispy-bootstrap5

### 4️⃣ Configurar Variables de Entorno

```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# En Windows
copy .env.example .env
```

**Editar el archivo `.env` con tus valores:**

```bash
DEBUG=True
SECRET_KEY=django-insecure-tu-clave-secreta-aqui

# Obtén tu API key en: https://makersuite.google.com/app/apikey
GEMINI_API_KEY=tu-api-key-de-gemini-aqui

# Configuración de Base de Datos (opcional)
# DATABASE_ENGINE=django.db.backends.sqlite3
# DATABASE_NAME=db.sqlite3
```

> ⚠️ **IMPORTANTE**: Nunca compartas tu `SECRET_KEY` ni `GEMINI_API_KEY` públicamente

## 🗄 Configuración de la Base de Datos

### Usando SQLite (Por Defecto)

SQLite viene incluido con Python, no requiere instalación adicional.

### Opción 1: Importar Base de Datos Existente

Si tienes el archivo `db.sqlite3` incluido en el repositorio:

```bash
# La base de datos ya está lista, solo ejecuta las migraciones pendientes
python manage.py migrate
```

### Opción 2: Crear Base de Datos desde Cero

```bash
# 1. Aplicar migraciones
python manage.py migrate

# 2. Crear superusuario (administrador)
python manage.py createsuperuser
# Usuario: admin
# Email: admin@example.com
# Password: (tu contraseña segura)

# 3. (Opcional) Cargar datos de prueba
python manage.py loaddata initial_data.json
```

### Estructura de la Base de Datos

El sistema crea las siguientes tablas principales:

**Módulo Users:**
- `users_user` - Usuarios del sistema
- `users_user_groups` - Relación usuarios-grupos
- `users_user_user_permissions` - Permisos de usuarios

**Módulo Inventory:**
- `inventory_category` - Categorías de productos
- `inventory_warehouse` - Almacenes
- `inventory_product` - Productos
- `inventory_stockmovement` - Movimientos de inventario

**Módulo Sales:**
- `sales_client` - Clientes
- `sales_supplier` - Proveedores
- `sales_sale` - Ventas
- `sales_saledetail` - Detalle de ventas

### Migrar a MySQL/PostgreSQL

Si deseas usar MySQL o PostgreSQL en producción:

**Para MySQL:**

```bash
# Instalar driver
pip install mysqlclient

# Configurar en .env
DATABASE_ENGINE=django.db.backends.mysql
DATABASE_NAME=inventario_db
DATABASE_USER=tu_usuario
DATABASE_PASSWORD=tu_password
DATABASE_HOST=localhost
DATABASE_PORT=3306
```

**Para PostgreSQL:**

```bash
# Instalar driver
pip install psycopg2

# Configurar en .env
DATABASE_ENGINE=django.db.backends.postgresql
DATABASE_NAME=inventario_db
DATABASE_USER=tu_usuario
DATABASE_PASSWORD=tu_password
DATABASE_HOST=localhost
DATABASE_PORT=5432
```

## 🤖 Configuración del Chatbot IA

### 1. Obtener API Key de Google Gemini

1. Ve a [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Inicia sesión con tu cuenta de Google
3. Click en "Create API Key"
4. Copia la clave generada

### 2. Configurar la API Key

```bash
# Edita tu archivo .env
GEMINI_API_KEY=AIzaSy... (tu clave aquí)
```

### 3. Verificar Funcionamiento

1. Inicia el servidor: `python manage.py runserver`
2. Accede a cualquier página del sistema
3. Click en el botón azul flotante (esquina inferior derecha)
4. Escribe: "¿Cuántos productos hay en stock?"

## 🏃‍♂️ Ejecutar el Servidor

```bash
# Desarrollo (localhost:8000)
python manage.py runserver

# Accesible desde red local
python manage.py runserver 0.0.0.0:8000
```

Abre tu navegador en: **http://127.0.0.1:8000**

## 📁 Estructura del Proyecto

```
Sistema-de-Inventarios/
├── apps/
│   ├── chatbot/          # Módulo chatbot IA
│   │   ├── views.py      # API del chatbot
│   │   └── utils.py      # Lógica de Gemini
│   ├── dashboard/        # Panel principal
│   ├── inventory/        # Gestión de inventario
│   │   ├── models.py     # Productos, categorías, almacenes
│   │   ├── views.py      # Vistas CRUD
│   │   └── forms.py      # Formularios
│   ├── sales/            # Ventas y POS
│   │   ├── models.py     # Ventas, clientes, proveedores
│   │   ├── views.py      # POS, reportes
│   │   └── urls.py       # Rutas
│   ├── users/            # Autenticación
│   │   ├── models.py     # Modelo de usuario personalizado
│   │   ├── permissions.py # Control de acceso
│   │   └── views.py      # Login, registro, perfil
│   └── notifications/    # Sistema de notificaciones
├── config/
│   ├── settings.py       # Configuración Django
│   ├── urls.py           # Rutas principales
│   └── wsgi.py           # WSGI para producción
├── templates/
│   ├── base.html         # Template base
│   ├── dashboard/        # Templates dashboard
│   ├── inventory/        # Templates inventario
│   ├── sales/            # Templates ventas
│   │   ├── pos.html      # Punto de venta
│   │   ├── report.html   # Reportes
│   │   └── sale_detail.html
│   └── users/            # Templates usuarios
├── static/               # Archivos estáticos
├── media/                # Archivos subidos
├── db.sqlite3            # Base de datos SQLite
├── .env                  # Variables de entorno (NO subir a Git)
├── .env.example          # Ejemplo de .env
├── requirements.txt      # Dependencias Python
├── manage.py             # CLI de Django
└── README.md             # Este archivo
```

## 📚 Módulos del Sistema

### 🏠 Dashboard
- Vista general del sistema
- Estadísticas en tiempo real
- Productos con stock bajo
- Ventas del día
- Movimientos recientes

### 📦 Inventario
- **Productos**: Gestión completa de productos
- **Categorías**: Organización por categorías
- **Almacenes**: Múltiples ubicaciones de almacenamiento
- **Movimientos**: Registro de entradas y salidas

### 💰 Ventas
- **Punto de Venta (POS)**: Interfaz de venta rápida
- **Registro de Ventas**: Historial completo
- **Clientes**: Base de datos de clientes
- **Proveedores**: Gestión de proveedores
- **Reportes**: Análisis de ventas

### 👥 Usuarios
- **Gestión de Usuarios**: CRUD de usuarios
- **Roles y Permisos**: Control de acceso granular
- **Perfil**: Edición de perfil personal
- **Autenticación**: Login/Logout seguro

## 🔐 Usuarios y Roles

### Roles Disponibles

| Rol | Permisos | Acceso |
|-----|----------|--------|
| **Administrador** | Todos los permisos | Dashboard, Inventario, Ventas, Usuarios, Reportes |
| **Vendedor** | Ventas y consultas | Dashboard, POS, Ver inventario, Ventas |
| **Almacén** | Gestión de stock | Dashboard, Inventario, Movimientos |

### Usuario por Defecto

Después de la instalación, puedes usar:

```
Usuario: admin
Contraseña: password123
Rol: Administrador
```

> ⚠️ **IMPORTANTE**: Cambia la contraseña inmediatamente en producción

## 🌐 API y Endpoints

### Endpoints Principales

```
# Autenticación
/users/login/          - Inicio de sesión
/users/logout/         - Cerrar sesión
/users/register/       - Registro de usuario

# Dashboard
/                      - Panel principal

# Inventario
/inventory/products/              - Lista de productos
/inventory/products/add/          - Crear producto
/inventory/products/<id>/edit/    - Editar producto
/inventory/products/<id>/delete/  - Eliminar producto

# Ventas
/sales/pos/                       - Punto de venta
/sales/sales/                     - Lista de ventas
/sales/sales/<id>/                - Detalle de venta
/sales/reports/                   - Reportes de ventas
/sales/clients/                   - Gestión de clientes

# Chatbot
/chatbot/api/                     - API del chatbot (POST)
```

### Ejemplo de Uso del API del Chatbot

```javascript
fetch('/chatbot/api/', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRFToken': csrfToken
    },
    body: JSON.stringify({
        message: '¿Cuántos productos hay?'
    })
})
.then(response => response.json())
.then(data => console.log(data.response));
```

## 🐛 Troubleshooting

### Error: "No module named 'django'"

```bash
# Asegúrate de estar en el entorno virtual
venv\Scripts\activate  # Windows
source venv/bin/activate  # macOS/Linux

# Reinstala las dependencias
pip install -r requirements.txt
```

### Error: "API Key not configured"

```bash
# Verifica que tu .env tenga la clave correcta
GEMINI_API_KEY=AIzaSy...

# Reinicia el servidor
python manage.py runserver
```

### Error: "Table doesn't exist"

```bash
# Ejecuta las migraciones
python manage.py migrate
```

### El menú lateral no se muestra

```bash
# Limpia la caché del navegador
# Presiona Ctrl+Shift+R (Windows/Linux) o Cmd+Shift+R (Mac)
```

### Error 403 en gestión de usuarios

Verifica que tu usuario tenga rol de administrador:

```python
# En el shell de Django
python manage.py shell

from apps.users.models import User
user = User.objects.get(username='tuusuario')
user.role = 'ADMIN'
user.is_staff = True
user.save()
```

## 📝 Comandos Útiles

```bash
# Crear superusuario
python manage.py createsuperuser

# Aplicar migraciones
python manage.py migrate

# Crear nuevas migraciones
python manage.py makemigrations

# Ejecutar shell de Django
python manage.py shell

# Recolectar archivos estáticos
python manage.py collectstatic

# Ejecutar tests
python manage.py test

# Crear backup de la base de datos
python manage.py dumpdata > backup.json

# Restaurar backup
python manage.py loaddata backup.json
```

## 🚢 Despliegue en Producción

### Preparación

```bash
# 1. Cambiar DEBUG a False en .env
DEBUG=False

# 2. Generar nueva SECRET_KEY
python -c 'from django.core.management.utils import get_random_secret_key; print(get_random_secret_key())'

# 3. Configurar ALLOWED_HOSTS en settings.py
ALLOWED_HOSTS = ['tudominio.com', 'www.tudominio.com']

# 4. Recolectar archivos estáticos
python manage.py collectstatic
```

### Opciones de Hosting

- **Heroku** - Fácil despliegue, free tier disponible
- **PythonAnywhere** - Especializado en Django
- **DigitalOcean** - Más control, requiere configuración
- **AWS/Azure** - Escalable, profesional
- **Render** - ⭐ Recomendado, fácil y gratis

### Despliegue en Render (Recomendado)

Este proyecto está optimizado para desplegarse en [Render.com](https://render.com) con PostgreSQL.

**📖 Guía completa de despliegue**: Ver [DEPLOY.md](DEPLOY.md)

**Resumen rápido:**

1. El proyecto ya incluye los archivos necesarios:
   - `build.sh` - Script de construcción
   - `requirements.txt` - Con dependencias de producción (gunicorn, psycopg2, whitenoise)
   - `settings.py` - Configurado para PostgreSQL con `dj-database-url`

2. Crea una base de datos PostgreSQL en Render

3. Crea un Web Service conectado a tu repositorio GitHub

4. Configura las variables de entorno:
   ```
   DATABASE_URL=postgresql://...
   SECRET_KEY=tu-clave-secreta
   DEBUG=False
   PYTHON_VERSION=3.11.9
   ```

5. ¡Listo! Render desplegará automáticamente tu aplicación

**🔗 Demo en vivo**: [https://sistema-de-inventarios.onrender.com](https://sistema-de-inventarios-h50e.onrender.com) *(actualiza con tu URL)*


## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**Jhostin Joel**
- GitHub: [@JhostinJoel](https://github.com/JhostinJoel)
- Proyecto: [Sistema-de-Inventarios](https://github.com/JhostinJoel/Sistema-de-Inventarios)

## 🤝 Contribuciones

Las contribuciones son bienvenidas! Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📞 Soporte

Si encuentras algún problema o tienes preguntas:

1. Revisa la sección [Troubleshooting](#troubleshooting)
2. Busca en los [Issues](https://github.com/JhostinJoel/Sistema-de-Inventarios/issues)
3. Crea un nuevo Issue si es necesario

---

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub!
