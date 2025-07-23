# PERP - Peru ERP's

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-orange.svg)](https://laravel.com/)
[![Filament Version](https://img.shields.io/badge/Filament-v3-blue.svg)](https://filamentphp.com/)

Sistema de gestión de inventario y administración de productos desarrollado con Laravel y Filament, diseñado para pequeñas y medianas empresas.

## 🚀 Características Principales

* **Gestión de Empresas:** Administración de múltiples entidades de empresa.
* **Gestión de Productos:**
    * Información detallada del producto (nombre, SKU, descripción).
    * Unidades de peso (`Tonelada`, `Kilogramo`, `Gramo`) seleccionables, con `Kilogramo` por defecto.
    * Unidades de medida (`Metro`, `Centímetro`, `Milímetro`) seleccionables, con `Centímetro` por defecto.
    * Precios de compra y venta diferenciados, con soporte para **monedas específicas** para cada tipo de precio.
    * Asociación de imágenes a los productos.
    * Control de stock y alertas de stock mínimo.
* **Gestión de Monedas:** Entidad dedicada para registrar y gestionar diferentes tipos de moneda (PEN, USD, EUR, etc.) con sus símbolos y formatos.
* **Autenticación y Autorización:** Gestión de usuarios y roles/permisos (a través de Filament Shield).
* **Panel de Administración Intuitivo:** Interfaz de usuario generada con Filament PHP.
* **Soft Deletes:** Soporte para eliminación lógica de registros.

## 🛠️ Tecnologías Utilizadas

* **Laravel Framework:** 11.x
* **Filament PHP:** v3.x (Admin Panel, Forms, Tables, Infolists)
* **PHP:** ^8.2
* **Base de Datos:** MySQL (o PostgreSQL, SQLite, etc.)
* **Composer:** Gestor de paquetes de PHP
* **NPM / Yarn:** Gestor de paquetes de JavaScript (para activos de frontend)
* **Tailwind CSS:** Framework CSS para el diseño
* **Alpine.js:** Pequeño framework JS para interactividad
* **Filament Shield:** Para gestión de roles y permisos.
* **Doctrine DBAL:** Requerido para ciertas operaciones de migración (ej. `change()` en columnas).

## 📋 Requisitos del Sistema

Antes de comenzar, asegúrate de tener instalado lo siguiente:

* PHP >= 8.2
* Composer
* Node.js & NPM (o Yarn)
* Una base de datos (MySQL 8+ recomendado)
* Git

## ⚙️ Instalación

Sigue estos pasos para configurar el proyecto en tu máquina local:

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/Ckodda/perp
    cd perp
    ```

2.  **Instalar dependencias de Composer:**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de NPM y compilar activos:**
    ```bash
    npm install
    npm run dev # o npm run build para producción
    ```

4.  **Configurar el archivo `.env`:**
    Copia el archivo de entorno de ejemplo y configúralo.
    ```bash
    cp .env.example .env
    ```
    Abre `.env` y ajusta las credenciales de tu base de datos y otras variables de entorno.
    ```env
    APP_NAME="NombreDeTuProyecto"
    APP_URL=http://localhost:8000 # O la URL de tu entorno
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password
    ```

5.  **Generar la clave de la aplicación:**
    ```bash
    php artisan key:generate
    ```

6.  **Ejecutar las migraciones de la base de datos:**
    Esto creará las tablas necesarias, incluyendo `companies`, `currencies`, `products`, y las tablas de autenticación/permisos.
    ```bash
    php artisan migrate
    ```
    Si quieres llenar la base de datos con datos de prueba (incluyendo monedas predefinidas si tu migración `create_currencies_table` las tiene, o si tienes seeders):
    ```bash
    php artisan migrate:fresh --seed
    ```

7.  **Crear un enlace de almacenamiento simbólico:**
    Esto es necesario para que las imágenes de productos (y otros archivos subidos) sean accesibles públicamente.
    ```bash
    php artisan storage:link
    ```

8.  **Crear un usuario administrador (si no usaste `--seed` con un seeder de usuario):**
    ```bash
    php artisan make:filament-user
    # Sigue las instrucciones para crear tu primer usuario
    ```
    *Asegúrate de asignar roles o permisos apropiados si estás usando Filament Shield.*

9.  **Iniciar el servidor de desarrollo:**
    ```bash
    php artisan serve
    ```

Ahora puedes acceder a la aplicación en tu navegador, generalmente en `http://localhost:8000` y al panel de administración en `http://localhost:8000/admin`.

## 📧 Contacto

* **Tu Nombre/Alias:** [Tu Perfil de GitHub](https://github.com/tu_usuario) | [Tu Correo Electrónico](mailto:tu.email@example.com)
