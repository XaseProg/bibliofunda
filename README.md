# Biblioteca ISFT N° 240

Sistema de gestión de biblioteca para el Instituto Superior de Formación Técnica N° 240.

## Características

- **Catálogo público**: los visitantes pueden buscar libros por título, autor, carrera, curso o materia.
- **Reservas**: cualquier persona puede reservar un libro dejando su nombre, teléfono y carrera/curso.
- **Panel de administración**: el bibliotecario puede:
  - Cargar, editar y eliminar libros (con cantidad de ejemplares).
  - Ver todas las reservas y préstamos.
  - Convertir una reserva en préstamo (cuando la persona retira el libro).
  - Registrar préstamos directos sin reserva previa.
  - Registrar devoluciones.
  - Cancelar reservas.
- **Estados**: disponible, reservado, prestado, devuelto, cancelado.
- **Diseño**: azul y blanco, responsive (adaptable a celular y computadora).

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx, etc.)

## Instalación

1. **Subir los archivos** a tu servidor (por FTP o el panel que uses).

2. **Crear la base de datos**: importá el archivo `database.sql` desde phpMyAdmin o ejecutándolo en MySQL:

   ```
   mysql -u tu_usuario -p < database.sql
   ```

   Esto crea la base de datos `biblioteca_isft240` con las tablas necesarias y el usuario administrador.

3. **Configurar la conexión**: editá el archivo `config.php` y cambiá los datos de conexión a tu servidor MySQL:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'biblioteca_isft240');
   define('DB_USER', 'root');       // tu usuario de MySQL
   define('DB_PASS', '');           // tu contraseña de MySQL
   ```

4. **Listo**: abrí la página en el navegador. El catálogo se ve sin login. Para entrar al panel, hacé clic en "Admin".

## Acceso al panel

- **Usuario**: `admin`
- **Contraseña**: `isft240@`

> **Importante**: cambiá la contraseña del administrador después de instalar. Para hacerlo, ejecutá este SQL en phpMyAdmin:

```sql
UPDATE administradores SET password = 'TU_NUEVA_PASSWORD' WHERE usuario = 'admin';
```

## Estructura de archivos

```
├── index.php              → Catálogo público con buscador
├── libro.php              → Detalle de libro + formulario de reserva
├── login.php              → Login del administrador
├── logout.php             → Cerrar sesión
├── config.php             → Configuración de conexión a MySQL
├── functions.php          → Funciones y lógica del sistema
├── header.php             → Header común (páginas públicas)
├── footer.php             → Footer común (páginas públicas)
├── database.sql           → Script para crear la base de datos
├── css/
│   └── estilos.css        → Estilos (azul y blanco)
└── admin/
    ├── header_admin.php   → Header del panel
    ├── footer_admin.php   → Footer del panel
    ├── dashboard.php      → Resumen general
    ├── libros.php         → Listado y gestión de libros
    ├── libro_form.php     → Alta/edición de un libro
    ├── libro_eliminar.php → Eliminar un libro
    ├── reservas.php       → Listado de reservas y préstamos
    ├── prestar.php        → Registrar préstamo (de reserva o directo)
    ├── cancelar.php       → Cancelar una reserva
    └── devolver.php       → Registrar devolución
```

## Cómo funciona el flujo

1. Un visitante busca un libro en el catálogo.
2. Entra al detalle del libro y completa el formulario de reserva (nombre, teléfono, carrera/curso).
3. El libro queda con estado **reservado** y se descuenta un ejemplar disponible.
4. La persona se acerca al instituto a retirarlo.
5. El administrador entra al panel, va a "Reservas y préstamos", y hace clic en **Prestar**.
6. Se carga la fecha de devolución. El estado pasa a **prestado**.
7. Cuando la persona devuelve el libro, el administrador hace clic en **Devolver**.
8. El estado pasa a **devuelto** y se recupera un ejemplar disponible.

El administrador también puede hacer un **préstamo directo** (sin reserva previa) desde el botón "Prestar" en la lista de libros.
