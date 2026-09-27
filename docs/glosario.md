# Glosario

Las palabras técnicas que aparecen en el proyecto y en esta documentación, explicadas en pocas líneas.

**Autoloader.** Función que carga automáticamente el archivo de una clase la primera vez que se usa, para no escribir
`require` a mano. En Hachi está en `Bootstrap::classLoader()`.

**Backend / Frontend.** El backend es lo que corre en el servidor (PHP, la base de datos). El frontend es lo que corre
en el navegador (HTML, CSS, JavaScript).

**Bootstrap.** El código que arranca la aplicación en cada petición (`App/Bootstrap/Bootstrap.php`).
No confundir con la librería de CSS del mismo nombre, que este proyecto no usa.

**Clave foránea (foreign key).** Columna que apunta a una fila de otra tabla. `posts.user_id` apunta a `users.user_id`.

**Clave primaria (primary key).** Columna (o grupo de columnas) que identifica cada fila sin repetirse, por ejemplo `post_id`.

**Consulta preparada (prepared statement).** Consulta SQL con huecos (`:email`) donde los datos se pasan aparte con `execute()`.
Protege contra la inyección SQL.

**Controlador.** Clase que recibe una petición y decide qué responder (`App/Controller`).

**Cookie.** Dato pequeño que el servidor le pide al navegador que guarde y que el navegador envía en cada petición.
Hachi solo usa la cookie de sesión `PHPSESSID`.

**CSRF (Cross-Site Request Forgery).** Ataque en el que otra web hace que tu navegador envíe una petición a un sitio
donde tienes la sesión abierta. Ver [5. Seguridad](5-seguridad.md#5-csrf-cross-site-request-forgery).

**Delegación de eventos.** Poner un solo "escuchador" de eventos en un elemento que contiene a muchos otros
(el timeline) en lugar de uno en cada elemento (cada botón).

**DOM.** La representación de la página que JavaScript puede leer y cambiar: `document.querySelector()`, `classList`, etc.

**Endpoint.** Una dirección del servidor que el JavaScript llama para hacer algo y que responde datos (no una página).
En Hachi: `login.php`, `register.php` y `post.php`.

**Escapar.** Convertir los caracteres especiales de un texto para que se muestren como texto y no se interpreten como código.
`htmlspecialchars()` convierte `<` en `&lt;`.

**Facade (patrón).** Una clase que da un punto de acceso único a varias clases. `Facade` da acceso a las tablas `User` y `Post`.

**fetch.** Función de JavaScript para hacer peticiones al servidor sin recargar la página.

**Hash.** Resultado de pasar un dato por una función que no se puede revertir. Las contraseñas se guardan como hash.

**HTTP.** El protocolo con el que hablan el navegador y el servidor. Una petición tiene un **método** (`GET` para pedir,
`POST` para enviar datos) y la respuesta tiene un **código de estado**: `200` bien, `302` redirección,
`400` petición inválida, `401` falta la sesión, `404` no existe, `500` error del servidor.

**Inyección SQL.** Ataque que mete código SQL en un dato para cambiar la consulta. Se evita con consultas preparadas.

**JSON.** Formato de texto para intercambiar datos: `{"code": 0, "likes": 3}`. PHP lo genera con `json_encode()`
y lo lee con `json_decode()`; JavaScript usa `JSON.stringify()` y `JSON.parse()`.

**Media query.** Regla de CSS que se aplica solo en ciertas condiciones, por ejemplo en pantallas de menos de 900 píxeles.

**Módulo.** En este proyecto, la clase que prepara los datos para mostrarlos y arma la página con las vistas (`App/Module`).

**MVC.** Modelo-Vista-Controlador, una forma de organizar el código en tres partes: los datos, lo que se muestra
y la lógica que los une.

**Namespace.** El "apellido" de una clase, que evita choques de nombres: `App\Controller\HomeController`.
En Hachi coincide con la carpeta del archivo.

**PDO.** La forma de PHP de conectarse a distintas bases de datos (MySQL, SQLite...) con los mismos métodos.

**Petición y respuesta.** El navegador envía una petición (quiero `home.php`) y el servidor devuelve una respuesta (el HTML).

**Redirección.** Respuesta que le dice al navegador que vaya a otra dirección (`header('Location: home.php')`).

**Regex (expresión regular).** Patrón para buscar texto. `Hashtag::PATTERN` es la regex que encuentra los hashtags.

**Responsive.** Diseño que se adapta al tamaño de la pantalla (computadora, tableta, celular).

**Sesión.** Datos que el servidor recuerda entre peticiones de la misma persona, identificada por una cookie.
Ver [5. Seguridad](5-seguridad.md#2-sesiones-cómo-sabe-el-servidor-quién-eres).

**Singleton (patrón).** Clase de la que solo existe un objeto en toda la petición. `Singleton::getFacade()` y
`SessionManager::getInstance()` siempre devuelven el mismo.

**SQL.** El lenguaje para trabajar con bases de datos: `SELECT` (leer), `INSERT` (agregar), `UPDATE` (cambiar), `DELETE` (borrar).

**SQLite.** Base de datos que vive en un solo archivo y no necesita instalar un servidor. Ideal para aprender.

**Subconsulta.** Un `SELECT` dentro de otra consulta. El timeline las usa para contar los likes de cada post.

**Transacción.** Grupo de consultas que se guardan todas juntas o ninguna (`beginTransaction()` … `commit()`).

**UTC.** La hora universal, igual en todo el mundo. Hachi guarda las fechas en UTC para no depender del lugar del servidor.

**Vista.** Archivo con el HTML de una página y huecos de PHP para los datos (`App/View`).

**XSS (Cross-Site Scripting).** Ataque que mete código JavaScript en una página a través de un texto que escribió
un usuario. Se evita escapando el texto. Ver [5. Seguridad](5-seguridad.md#4-xss-cross-site-scripting).

---

[← Volver al README](../README.md)
