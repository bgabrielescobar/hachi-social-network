# 1. Instalación

En esta guía vas a instalar PHP, descargar el proyecto y verlo funcionando en tu navegador.
No hace falta instalar MySQL: para practicar se usa **SQLite**, una base de datos que vive en un solo archivo.

## Qué necesitas

| Programa | Para qué sirve | ¿Obligatorio? |
|---|---|---|
| PHP 7.4 o más nuevo (mejor PHP 8) | Ejecuta el proyecto | Sí |
| Las extensiones `pdo_sqlite` y `mbstring` de PHP | La base de datos local y los textos con acentos y emojis | Sí |
| Git | Descargar el proyecto | Recomendado (también puedes bajar el ZIP desde GitHub) |
| Un editor de código, por ejemplo [VS Code](https://code.visualstudio.com/) | Leer y cambiar el código | Recomendado |
| MySQL o MariaDB | Solo para publicar el proyecto en un hosting | No |

## Paso 1: instalar PHP

### Windows

La forma más fácil es instalar [XAMPP](https://www.apachefriends.org/), que trae PHP (y también MySQL).
PHP queda en `C:\xampp\php\php.exe`.

Para poder escribir solo `php` en la terminal, agrega la carpeta `C:\xampp\php` a la variable de entorno `PATH`
(busca "variables de entorno" en el menú de inicio). Si no quieres hacerlo, usa la ruta completa en los comandos,
por ejemplo `C:\xampp\php\php.exe -v`.

### macOS

Con [Homebrew](https://brew.sh/):

```bash
brew install php
```

### Linux (Ubuntu / Debian)

```bash
sudo apt install php-cli php-sqlite3 php-mbstring
```

Agrega `php-mysql` si también vas a usar MySQL.

### Comprobar que funciona

Abre una terminal y escribe:

```bash
php -v
```

Tiene que mostrar algo como `PHP 8.3.6 (cli)`. Después revisa las extensiones:

```bash
# macOS / Linux
php -m | grep -i -E "sqlite|mbstring"

# Windows
php -m | findstr /i "sqlite mbstring"
```

Tienen que aparecer `pdo_sqlite` y `mbstring`. Si falta alguna, mira [Problemas frecuentes](#problemas-frecuentes).

## Paso 2: descargar el proyecto

```bash
git clone https://github.com/bgabrielescobar/hachi-social-network.git
cd hachi-social-network
```

Sin Git: en la página del repositorio en GitHub, botón **Code → Download ZIP**, descomprímelo y abre una terminal dentro de la carpeta.

## Paso 3: crear el archivo `.env`

El `.env` guarda la configuración: qué base de datos usar, contraseñas, etc. No se sube a Git
porque puede tener contraseñas, así que cada persona crea el suyo a partir de la plantilla `.env.example`:

```bash
# macOS / Linux
cp .env.example .env

# Windows
copy .env.example .env
```

Abre `.env` con tu editor y cambia estas dos líneas:

```ini
DB_DRIVER=sqlite
APP_DEBUG=1
```

- `DB_DRIVER=sqlite` usa SQLite: la base de datos se crea sola en `database/hachi.sqlite`.
- `APP_DEBUG=1` muestra los errores de PHP en la página, muy útil mientras aprendes.

## Paso 4: arrancar el servidor

Dentro de la carpeta del proyecto:

```bash
php -S localhost:8000
```

Esto arranca el **servidor web que trae PHP**. Es perfecto para programar, pero no para publicar un sitio real.

- Deja la terminal abierta: mientras esté abierta, el servidor funciona.
- En la terminal vas a ver una línea por cada petición y los errores de PHP.
- Para apagarlo: `Ctrl + C`.

## Paso 5: usar la app

1. Abre http://localhost:8000 en el navegador.
2. Haz clic en **Create an account** y crea una cuenta. Entras directo al timeline.
3. Escribe un post con un hashtag, por ejemplo `Mi primer post en #Hachi`.
4. Haz clic en el hashtag y en tu nombre para ver las otras páginas.

> **Consejo:** abre una ventana privada o de incógnito para crear un segundo usuario.
> Así puedes probar los likes y las tendencias con dos cuentas a la vez.

## Empezar de cero

¿Quieres borrar todos los usuarios y posts? Apaga el servidor y borra el archivo `database/hachi.sqlite`.
La próxima vez que abras la página se crea una base de datos vacía.

## Usar MySQL en tu computadora (opcional)

Solo si quieres practicar con la misma base de datos que usaría un hosting. Con XAMPP:

1. En el panel de XAMPP, arranca **MySQL**.
2. Entra a http://localhost/phpmyadmin y crea una base de datos llamada `hachi`.
3. Con la base `hachi` seleccionada, pestaña **Importar**, elige el archivo `database/schema.sql` y pulsa **Continuar**.
4. En tu `.env`:

   ```ini
   DB_DRIVER=mysql
   HOST=127.0.0.1
   DB_NAME=hachi
   USER_NAME=root
   PASSWORD=
   ```

   En XAMPP el usuario `root` no tiene contraseña, por eso `PASSWORD` queda vacío.

## Problemas frecuentes

| Qué ves | Por qué pasa | Cómo se arregla |
|---|---|---|
| `php` no se reconoce como un comando | PHP no está en el `PATH` | Agrega la carpeta de PHP al `PATH` o usa la ruta completa (paso 1) |
| La página dice `Missing .env file, create it from .env.example` | No existe el `.env` | Haz el paso 3 |
| La página dice `Database connection error.` | PHP no pudo conectarse a la base de datos | El motivo real está en la terminal donde corre `php -S` (ver las filas de abajo) |
| En la terminal: `could not find driver` | Falta la extensión `pdo_sqlite` (o `pdo_mysql` para MySQL) | Ejecuta `php --ini` para ver dónde está tu `php.ini`, ábrelo y quita el `;` del principio de `;extension=pdo_sqlite` (o `;extension=pdo_mysql`). Reinicia el servidor |
| En la terminal: `Access denied for user` | Usuario o contraseña de MySQL incorrectos | Revisa `USER_NAME` y `PASSWORD` en el `.env` |
| En la terminal: `Unknown database` | La base de datos de MySQL no existe | Créala y revisa `DB_NAME` |
| `Call to undefined function mb_strlen()` | Falta la extensión `mbstring` | Igual que `pdo_sqlite`: quita el `;` de `;extension=mbstring` en `php.ini` |
| La página está en blanco o dice error 500 | Hubo un error de PHP y está oculto | Pon `APP_DEBUG=1` en el `.env`, recarga y mira también la terminal |
| Se ve el código PHP en el navegador | Abriste el archivo con doble clic | Entra siempre por http://localhost:8000 |
| `Failed to listen on localhost:8000` | Otro programa usa el puerto 8000 | Usa otro puerto: `php -S localhost:8080` |
| El login no tiene iconos o la letra es distinta | Los iconos y la fuente se descargan de internet | Revisa tu conexión. La app funciona igual |
| `Wrong email or password` con una cuenta vieja | Las cuentas de la versión anterior guardaban la contraseña en otro formato | Crea la cuenta de nuevo |

---

Siguiente: [2. Arquitectura →](2-arquitectura.md)
