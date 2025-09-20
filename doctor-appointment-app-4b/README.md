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
    
-----------------------------------------------------------------------------------------------------------------------------------------------------
2. Configuración técnica básica

Idioma del sistema: Español
Modificado en config/app.php:

'locale' => 'es',
-----------------------------------------------------------------------------------------------------------------------------------------------------

3. Zona horaria (config/app.php/linea 68)
'timezone' => 'UTC' cambia a 'timezone' => 'America/Merida',

-----------------------------------------------------------------------------------------------------------------------------------------------------
4. Cambio de foto de perfil:
Se reemplazó la foto de perfil por la nueva imagen en public/images/profile.jpg y se actualizó en la vista correspondiente.

    *Cómo verificar
- Ejecutar el servidor de Laravel -> php artisan serve
- Abrir la aplicación en el navegador y revisar:
        - La interfaz y mensajes del sistema aparecen en español.
        - La fecha y hora reflejan la zona horaria configurada.
        - La foto de perfil se muestra correctamente en el área del usuario.

# Panel administrativo con Flowbite

## 5. Página principal de Flowbite
Fuimos a la página principal de **Flowbite**, que es una biblioteca de componentes UI basada en **TailwindCSS**.  
Buscamos **Sidebar → Sidebar with navbar #**, encontramos una plantilla y revisamos el código.
-----------------------------------------------------------------------------------------------------------------------------------------------------
## 6. Estructura inicial
Vamos a la carpeta `resources/views/component/admin-layout.blade.php` y la movemos a una nueva carpeta llamada **Layouts**, en la cual se tienen tres archivos:

- `admin-layout.blade.php` → se cambia el nombre a **admin.blade.php**.
- `app.blade.php` → copiamos todo el contenido y lo pegamos en **admin.blade.php**, pero eliminamos de la línea 21 a la 40 (`<body>` hasta `@stack('modals')`).
- `guest.blade.php`.
-----------------------------------------------------------------------------------------------------------------------------------------------------
## 7. Crear componente
```bash
php artisan make:component AdminLayout

8. Rutas
En la carpeta routes:

admin.php → modificamos para que quede:

php
Copiar código
return view('admin.dashboard')->name('dashboard');
api.php

console.php

web.php
-----------------------------------------------------------------------------------------------------------------------------------------------------
9. Views
En views se crea una nueva carpeta admin con el archivo:

dashboard.blade.php → colocamos el componente o plantilla:

blade
Copiar código
<x-admin-layout></x-admin-layout>
-----------------------------------------------------------------------------------------------------------------------------------------------------
10. Sidebar
Entramos a la página oficial:
flowbite.com/docs/components/sidebar/#sidebar-with-navbar

Copiamos el contenido de Sidebar with navbar # y lo pegamos dentro de admin.blade.php a partir de la línea 21 (aprox. 200 líneas de código).

En la terminal ejecutamos:

bash
Copiar código
php artisan serve
El paquete que se encarga de la autenticación es Laravel Fortify (en Spring sería Spring Security).
-----------------------------------------------------------------------------------------------------------------------------------------------------
11. Modificar AdminLayout.php
En app/View/Components/AdminLayout.php modificamos la línea 24 con:

php
Copiar código
return view('layouts.admin');
Esto hace que la página se vea incompleta.
En la terminal ejecutamos:

bash
Copiar código
npm install
npm run build
-----------------------------------------------------------------------------------------------------------------------------------------------------
12. Organización de carpetas
Creamos nuevas carpetas para organizar los elementos a renderizar:

markdown
Copiar código
layouts
 └── includes
     ├── admin
     │   ├── navigation.blade.php
     │   └── sidebar.blade.php
     └── app
-----------------------------------------------------------------------------------------------------------------------------------------------------
13. Navigation
En resources/layouts/admin.blade.php, de la línea 21 a la 72 cortamos y pegamos en el archivo navigation.blade.php.
-----------------------------------------------------------------------------------------------------------------------------------------------------
14. Incluir navigation
En admin.blade.php, en la línea 22, agregamos:

blade
Copiar código
@include('layouts.includes.admin.navigation')
-----------------------------------------------------------------------------------------------------------------------------------------------------
15. Incluir sidebar
Repetimos el mismo proceso con el aside (línea 24 a 90).
Cortamos y pegamos en sidebar.blade.php y lo llamamos en admin.blade.php:

blade
Copiar código
@include('layouts.includes.admin.sidebar')
-----------------------------------------------------------------------------------------------------------------------------------------------------
16. Contenedor vacío
Eliminamos los estilos desde la línea 27 hasta la 125, dejando:

html
Copiar código
<div class="p-4 sm:ml-64"></div>
Agregamos:

html
Copiar código
<div class="mt-14">Hola mundo</div>
{{$slot}}
-----------------------------------------------------------------------------------------------------------------------------------------------------
17. Dashboard
En dashboard.blade.php colocamos:

blade
Copiar código
Hola desde admin
-----------------------------------------------------------------------------------------------------------------------------------------------------
18. Fondo y Flowbite
En admin.blade.php, en la línea 20, modificamos el <body>:

html
Copiar código
<body class="font-sans antialiased bg-gray-50">
Instalamos Flowbite:

bash
Copiar código
npm install flowbite --save
-----------------------------------------------------------------------------------------------------------------------------------------------------
19. CSS y Script
En resources/css/app.css agregamos:

css
Copiar código
/* Flowbite */
@import "flowbite/src/themes/default";
@plugin "flowbite/plugin";
@source "../../node_modules/flowbite";

[x-cloak] {
    display: none;
}
En admin.blade.php, en la línea 35, añadimos:

html
Copiar código
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
-----------------------------------------------------------------------------------------------------------------------------------------------------
20. Navigation-menu
En navigation-menu.blade.php:
En la opción Settings Dropdown (línea 74 a 124), cortamos y pegamos en navigation.blade.php, eliminando y sustituyendo el bloque de la línea 17 a la 48.
-----------------------------------------------------------------------------------------------------------------------------------------------------
21. Plantilla en profile
En el archivo show.blade.php de profile, reemplazamos app por admin:

blade
Copiar código
<x-admin-layout></x-admin-layout>
-----------------------------------------------------------------------------------------------------------------------------------------------------
22. Redirección y logo
Modificamos:

html
Copiar código
<a href="/" class="flex ms-2 md:me-24">
para redirigir a la página principal ("Hola desde admin").

Finalmente, cambiamos el logo por una imagen diferente en la carpeta public se creo una nueva acrpeta llamada images y se agrega una nueva imagen y se llama en  <img src="{{ asset('images/logo.png') }}" class="h-8 me-3" alt="Mi Logo" />






    


