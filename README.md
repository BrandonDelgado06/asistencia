# Sistema de Control de Asistencia

Aplicación web en PHP + MySQL + Bootstrap 5 para administrar empleados y registrar entradas y salidas. Sin frameworks PHP.

## Instalación (XAMPP)
1. Copie el proyecto en `C:\xampp\htdocs\asistencia`.
2. Inicie Apache y MySQL en XAMPP.
3. En phpMyAdmin, pestaña Importar, cargue `database.sql`.
4. Revise las credenciales en `config/database.php`.
5. Marcación: `http://localhost/asistencia/`
6. Administración: `http://localhost/asistencia/admin/login.php`

## Acceso de prueba
- Usuario: `admin`
- Contraseña: `password`

## Seguridad
- Contraseñas con `password_hash` / `password_verify`.
- Sesiones PHP y páginas admin protegidas.
- Consultas preparadas y escape de salida.
- Validación de formularios en el servidor.