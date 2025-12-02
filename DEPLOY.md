# Guía de Despliegue en Render.com

Esta guía te permitirá desplegar el Sistema de Inventarios Django en Render.com paso a paso.

## Requisitos Previos

- Cuenta en [Render.com](https://render.com) (gratis)
- Cuenta en [GitHub](https://github.com)
- Proyecto Django subido a GitHub
- Python 3.11+ instalado localmente

---

## PARTE 1: Preparar el Proyecto para Producción

### Paso 1: Agregar Dependencias de Producción

Abre `requirements.txt` y agrega al final:

```txt
gunicorn>=21.2.0
psycopg2-binary>=2.9.9
dj-database-url>=2.1.0
whitenoise>=6.6.0
```

### Paso 2: Modificar `config/settings.py`

#### 2.1 Agregar import al inicio del archivo:
```python
import dj_database_url
```

#### 2.2 Agregar WhiteNoise al MIDDLEWARE (después de SecurityMiddleware):
```python
MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'whitenoise.middleware.WhiteNoiseMiddleware',  # ← AGREGAR ESTA LÍNEA
    'django.contrib.sessions.middleware.SessionMiddleware',
    # ... resto del middleware
]
```

#### 2.3 Reemplazar la configuración de DATABASES:
```python
# Reemplaza toda la sección DATABASES con esto:
DATABASES = {
    'default': dj_database_url.config(
        default=os.getenv('DATABASE_URL', f"sqlite:///{BASE_DIR / 'db.sqlite3'}"),
        conn_max_age=600
    )
}
```

#### 2.4 Agregar configuración de archivos estáticos (después de STATIC_ROOT):
```python
STATIC_URL = 'static/'
STATICFILES_DIRS = [BASE_DIR / 'static']
STATIC_ROOT = BASE_DIR / 'staticfiles'
STATICFILES_STORAGE = 'whitenoise.storage.CompressedManifestStaticFilesStorage'  # ← AGREGAR
```

### Paso 3: Crear el Script de Build

Crea un archivo llamado `build.sh` en la raíz del proyecto:

```bash
#!/usr/bin/env bash
# exit on error
set -o errexit

pip install -r requirements.txt

python manage.py collectstatic --no-input

python manage.py migrate
```

### Paso 4: Subir Cambios a GitHub

```bash
git add .
git commit -m "Preparar proyecto para Render"
git push origin main
```

---

## PARTE 2: Crear Base de Datos PostgreSQL en Render

### Paso 1: Crear Base de Datos

1. Ve a [Render Dashboard](https://dashboard.render.com/)
2. Haz click en **"New +"** → **"PostgreSQL"**
3. Completa el formulario:
   - **Name**: `inventario_db` (o el nombre que prefieras)
   - **Database**: `inventario_db` (opcional, puedes dejarlo vacío)
   - **User**: Dejar vacío (se genera automáticamente)
   - **Region**: Selecciona la región más cercana (ej: Oregon US West)
   - **PostgreSQL Version**: Dejar por defecto
   - **Datadog API Key**: Dejar vacío
4. Haz click en **"Create Database"**
5. **Espera** a que la base de datos esté lista (1-2 minutos)

### Paso 2: Copiar las URLs de Conexión

Una vez creada la base de datos:

1. Ve a la pestaña **"Connections"**
2. **Copia y guarda** estas dos URLs (las necesitarás después):
   - **Internal Database URL**: Para conectar desde el servicio web en Render
   - **External Database URL**: Para conectar desde tu computadora local

---

## PARTE 3: Crear Servicio Web en Render

### Paso 1: Crear Web Service

1. En el Dashboard de Render, haz click en **"New +"** → **"Web Service"**
2. Conecta tu repositorio de GitHub:
   - Si es la primera vez, autoriza a Render a acceder a GitHub
   - Selecciona tu repositorio del Sistema de Inventarios
3. Haz click en **"Connect"**

### Paso 2: Configurar el Servicio

Completa el formulario con estos valores:

| Campo | Valor |
|-------|-------|
| **Name** | `sistema-de-inventarios` (o el nombre que prefieras) |
| **Region** | **La misma región que tu base de datos** (ej: Oregon US West) |
| **Branch** | `main` |
| **Runtime** | **Python 3** |
| **Build Command** | `./build.sh` |
| **Start Command** | `gunicorn config.wsgi:application` |
| **Instance Type** | **Free** |

### Paso 3: Configurar Variables de Entorno

Antes de crear el servicio, haz click en **"Advanced"** y agrega estas variables de entorno:

#### 3.1 Generar SECRET_KEY

En tu terminal local, ejecuta:
```bash
python -c "from django.core.management.utils import get_random_secret_key; print(get_random_secret_key())"
```

Copia la clave generada.

#### 3.2 Agregar Variables

Haz click en **"Add Environment Variable"** para cada una:

| Key | Value |
|-----|-------|
| `DATABASE_URL` | Pega la **Internal Database URL** que copiaste antes |
| `SECRET_KEY` | Pega la clave que generaste |
| `DEBUG` | `False` |
| `PYTHON_VERSION` | `3.11.9` |

### Paso 4: Crear el Servicio

1. Haz click en **"Create Web Service"**
2. Render comenzará a construir y desplegar tu aplicación
3. **Espera** a que termine (5-10 minutos la primera vez)

---

## PARTE 4: Migrar Datos (Opcional)

Si tienes datos en tu base de datos local que quieres migrar a Render:

### Paso 1: Exportar Datos Locales

En tu terminal local:

```bash
# Activa tu entorno virtual
# En Windows:
venv\Scripts\activate
# En Mac/Linux:
source venv/bin/activate

# Exporta los datos
python manage.py dumpdata --exclude auth.permission --exclude contenttypes --indent 2 > db_dump.json
```

### Paso 2: Subir el Dump a GitHub

```bash
git add db_dump.json
git commit -m "Agregar dump de base de datos"
git push origin main
```

### Paso 3: Importar Datos a Render

En tu terminal local, configura la conexión a la base de datos remota:

```bash
# Windows PowerShell:
$env:DATABASE_URL="[PEGA_AQUI_LA_EXTERNAL_DATABASE_URL]"

# Mac/Linux:
export DATABASE_URL="[PEGA_AQUI_LA_EXTERNAL_DATABASE_URL]"

# Importa los datos:
python manage.py loaddata db_dump.json
```

---

## PARTE 5: Verificar el Despliegue

### Paso 1: Revisar los Logs

1. En el Dashboard de Render, ve a tu servicio web
2. Haz click en **"Logs"**
3. Busca el mensaje: **"Your service is live"**

### Paso 2: Probar la Aplicación

1. Haz click en la URL de tu aplicación (arriba a la izquierda)
2. Debería abrir tu Sistema de Inventarios
3. Intenta iniciar sesión con tus credenciales

### Paso 3: Crear Superusuario (si es necesario)

Si necesitas crear un nuevo usuario administrador:

```bash
# Conéctate a la base de datos remota:
$env:DATABASE_URL="[EXTERNAL_DATABASE_URL]"

# Crea el superusuario:
python manage.py createsuperuser
```

---

## Solución de Problemas Comunes

### Error: "Application exited early"

**Causa**: Falta configurar variables de entorno o hay un error en el código.

**Solución**:
1. Verifica que todas las variables de entorno estén configuradas
2. Revisa los logs para ver el error específico
3. Asegúrate de que el Start Command sea: `gunicorn config.wsgi:application`

### Error: "No module named 'psycopg2'"

**Causa**: No se instaló psycopg2-binary.

**Solución**:
1. Verifica que `psycopg2-binary>=2.9.9` esté en `requirements.txt`
2. Haz un nuevo deploy: **Manual Deploy** → **"Deploy latest commit"**

### Error: "DisallowedHost"

**Causa**: El dominio de Render no está en ALLOWED_HOSTS.

**Solución**:
En `settings.py`, asegúrate de tener:
```python
ALLOWED_HOSTS = ['*']  # O agrega el dominio específico de Render
```

### La aplicación carga pero no hay datos

**Causa**: No se importaron los datos.

**Solución**:
Sigue la **PARTE 4** de esta guía para migrar los datos.

---

## Actualizaciones Futuras

Cada vez que hagas cambios en tu código:

```bash
git add .
git commit -m "Descripción de los cambios"
git push origin main
```

Render detectará automáticamente los cambios y hará un nuevo deploy.

---

## Comandos Útiles

### Ver tablas en la base de datos remota:
```python
# Crea un archivo check_db.py:
import psycopg2
conn = psycopg2.connect("EXTERNAL_DATABASE_URL")
cur = conn.cursor()
cur.execute("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';")
print(cur.fetchall())
```

### Hacer backup de la base de datos:
```bash
python manage.py dumpdata > backup_$(date +%Y%m%d).json
```

### Limpiar la base de datos (¡CUIDADO!):
```bash
$env:DATABASE_URL="[EXTERNAL_DATABASE_URL]"
python manage.py flush
```

---

## Recursos Adicionales

- [Documentación de Render](https://render.com/docs)
- [Guía de Django Deployment](https://docs.djangoproject.com/en/5.0/howto/deployment/)
- [Render Community](https://community.render.com/)

---

## Checklist de Despliegue

- [ ] Agregar dependencias a `requirements.txt`
- [ ] Modificar `settings.py` (imports, middleware, database, static files)
- [ ] Crear `build.sh`
- [ ] Subir cambios a GitHub
- [ ] Crear base de datos PostgreSQL en Render
- [ ] Copiar Internal y External Database URLs
- [ ] Crear Web Service en Render
- [ ] Configurar variables de entorno (DATABASE_URL, SECRET_KEY, DEBUG, PYTHON_VERSION)
- [ ] Esperar a que el deploy termine
- [ ] (Opcional) Migrar datos locales
- [ ] Verificar que la aplicación esté live
- [ ] Probar login y funcionalidades

---

**¡Felicidades!** Tu aplicación Django ahora está desplegada en Render.com 🎉
