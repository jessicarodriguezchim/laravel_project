# Task scheduler — reporte diario de citas (8:00 AM)

El comando `appointments:send-daily-report` está programado en `routes/console.php` a las **08:00** en la zona horaria de `config/app.php` (actualmente `America/Merida`).

## Por que NO llega solo a las 8:00 (muy importante)

Laravel **no** tiene un proceso en segundo plano que espere las 8:00.  
Solo revisa la hora **cuando alguien ejecuta** `schedule:run`.

Si **no** tienes cron (Linux) ni **Programador de tareas** (Windows) llamando `schedule:run` **cada minuto**, el recordatorio **nunca** se enviara automaticamente.

Algo externo debe invocar cada minuto:

```bash
php artisan schedule:run
```

En Windows puedes usar el archivo **`schedule-run.bat`** de esta carpeta (ver seccion Windows abajo).

---

## Probar en local (sin esperar a las 8:00)

```bash
cd doctor-appointment-app-4b
php artisan appointments:send-daily-report
```

O ejecutar el scheduler una vez (solo corre lo que “toca” según la hora):

```bash
php artisan schedule:run
```

---

## Linux / macOS (cron)

Abre el crontab del usuario que corre PHP:

```bash
crontab -e
```

Añade (ajusta la ruta al proyecto):

```cron
* * * * * cd /ruta/completa/a/doctor-appointment-app-4b && php artisan schedule:run >> /dev/null 2>&1
```

---

## Windows (Programador de tareas)

### Opcion A — Usar `schedule-run.bat` (recomendado)

1. Ubica el archivo **`schedule-run.bat`** dentro de `doctor-appointment-app-4b`.
2. Abre **Programador de tareas** → **Crear tarea basica** (o **Crear tarea**).
3. Nombre: `Laravel schedule:run`.
4. Desencadenador: **Diariamente** (cualquier hora; lo importante es la repeticion).
5. Accion: **Iniciar un programa**.
6. **Programa o script:** ruta completa al `.bat`, por ejemplo:  
   `C:\Users\kekev\Desktop\Gaxiola\laravel_project\doctor-appointment-app-4b\schedule-run.bat`
7. **Iniciar en (opcional):** la misma carpeta `doctor-appointment-app-4b`.
8. En tareas avanzadas / propiedades de la tarea:
   - Marca **Ejecutar con los privilegios mas altos** solo si hace falta.
   - En **Desencadenadores** → editar → **Repetir cada:** `1 minuto`, **durante:** `Indefinidamente`.

Los intentos quedan registrados en `storage/logs/scheduler.log`.

### Opcion B — PHP directo

1. **Programa:** ruta a `php.exe` (Laragon suele ser `C:\laragon\bin\php\php-8.x.x-Win32-vs16-x64\php.exe`).
2. **Argumentos:** `artisan schedule:run`
3. **Iniciar en:** `...\doctor-appointment-app-4b`

### Opcion C — Bucle automatico con doble clic (Windows)

En la carpeta del proyecto hay **`schedule-loop.bat`**: ejecuta `schedule:run` **cada 60 segundos** hasta que cierres la ventana.

1. Abre el Explorador en `doctor-appointment-app-4b`.
2. Doble clic en **`schedule-loop.bat`**.
3. Deja la ventana abierta (puedes minimizarla). Para detener: cierra la ventana.

El log se va escribiendo en `storage/logs/scheduler.log`.

**Si dice que no encuentra `php`:** al abrir con doble clic Windows a veces no usa el mismo PATH que PowerShell. Edita **`schedule-resolve-php.bat`**, descomenta la linea 3 y pon la ruta que te da `where php` en PowerShell.

Alternativa en PowerShell:

```powershell
cd C:\Users\kekev\Desktop\Gaxiola\laravel_project\doctor-appointment-app-4b
while ($true) { php artisan schedule:run; Start-Sleep -Seconds 60 }
```

---

## Cambiar la hora (produccion)

Edita `routes/console.php` y usa `dailyAt`:

```php
Schedule::command('appointments:send-daily-report')
    ->dailyAt('08:00')
    ->timezone(config('app.timezone'));
```

## Pruebas cada 2 o 3 minutos

En `routes/console.php` puedes usar:

- `->everyTwoMinutes()`
- `->everyThreeMinutes()`

Sigue siendo necesario que `schedule:run` se ejecute **cada minuto**; Laravel solo correra el comando cuando toque (cada 2 o 3 minutos segun elijas).

Y revisa `config/app.php` → `timezone` si necesitas otra ciudad.

---

## No me llego el recordatorio (revision rapida)

1. **`schedule:run` no envia a cualquier hora**  
   Solo ejecuta tareas cuya hora ya llego. Si no son las 08:00 en `config/app.php` → `timezone`, veras:  
   `No scheduled commands are ready to run.`  
   **Solucion:** prueba el envio directo:  
   `php artisan appointments:send-daily-report`

2. **Ejecutar desde la carpeta correcta**  
   Debe existir `artisan` en la carpeta actual:  
   `cd ...\doctor-appointment-app-4b`

3. **Correo del admin**  
   En `.env`: `ADMIN_REPORT_EMAIL` o `ADMIN_REPORT_EMAILS` (lista separada por comas).

4. **Gmail**  
   - `MAIL_USERNAME` y `MAIL_FROM_ADDRESS` conviene que sean el **mismo** correo Gmail que autentica SMTP.  
   - La **App Password** va **sin espacios** en `.env`.  
   - Tras cambiar `.env`: `php artisan optimize:clear`

5. **Citas “de hoy”**  
   El reporte lista citas con fecha **hoy** en la zona horaria de la app. Si no hay ninguna, el correo igual puede enviarse (tabla vacia).
