Configuración del proyecto

1. Integración con MySQL

Archivo de configuración: .env

DB_CONNECTION=mysql ---> cambio
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doctor_appointments ---> cambio
DB_USERNAME=usuario
DB_PASSWORD=contraseña


Migraciones: Se ejecutaron las migraciones para crear las tablas necesarias:

    - php artisan migrate

Cómo verificar
    - Abrir MySQL Workbench o cliente de MySQL.
    - Conectarse a la base de datos configurada (doctor_appointments-app-4b).
    - Verificar que existan las tablas creadas por Laravel (users, appointments, etc.).
    

2. Configuración técnica básica

Idioma del sistema: Español
Modificado en config/app.php:

'locale' => 'es',


3. Zona horaria (config/app.php/linea 68)
'timezone' => 'UTC' cambia a 'timezone' => 'America/Merida',


4. Cambio de foto de perfil:
Se reemplazó la foto de perfil por la nueva imagen en public/images/profile.jpg y se actualizó en la vista correspondiente.

    *Cómo verificar
- Ejecutar el servidor de Laravel -> php artisan serve
- Abrir la aplicación en el navegador y revisar:
        - La interfaz y mensajes del sistema aparecen en español.
        - La fecha y hora reflejan la zona horaria configurada.
        - La foto de perfil se muestra correctamente en el área del usuario.





