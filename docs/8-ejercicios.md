# 8. Ejercicios

Ideas para practicar, de las más fáciles a las más difíciles. Cada ejercicio tiene **pistas** que dicen
qué archivos tocar, pero no la solución: la gracia es que la encuentres tú.

| Dificultad | Qué necesitas saber |
|---|---|
| ⭐ | Cambiar valores, textos o CSS |
| ⭐⭐ | Seguir el camino completo: base de datos → controlador → módulo → vista |
| ⭐⭐⭐ | Crear tablas, páginas o acciones nuevas, y pensar en la seguridad |

## Cómo trabajar

1. **Una rama de Git por ejercicio**, así puedes volver atrás cuando quieras:

   ```bash
   git checkout -b ejercicio-colores
   ```

2. Ten `APP_DEBUG=1` en el `.env` y la terminal del servidor a la vista.
3. Cuando funcione, revisa la [lista de control de seguridad](5-seguridad.md#lista-de-control-para-código-nuevo).
4. Guarda tu trabajo:

   ```bash
   git add .
   git commit -m "Cambio los colores del timeline"
   ```

5. Para volver al proyecto original: `git checkout master`.

---

## Nivel 1: primeros pasos

### 1. Tus propios colores ⭐

**Objetivo:** cambia el azul de Hachi por el color que quieras, y el rojo del like por otro.

**Pistas:**
- Los colores están en variables al principio de `public/css-min/Home.min.css` (`:root`).
- Busca dónde se usa `var(--brand)` para ver qué partes van a cambiar.

**Aprendes:** variables CSS.

### 2. Modo oscuro ⭐

**Objetivo:** si la computadora o el celular están en modo oscuro, el timeline se ve con fondo oscuro.

**Pistas:**
- La media query `@media (prefers-color-scheme: dark) { ... }` se aplica solo en modo oscuro.
- Dentro, vuelve a definir las variables de `:root` (`--bg`, `--card`, `--text`, `--border`...).
- Para probarlo sin cambiar tu sistema: herramientas del navegador (`F12`) → menú de tres puntos →
  **Más herramientas → Renderizado** → "Emular prefers-color-scheme".

**Aprendes:** media queries y por qué conviene usar variables para los colores.

### 3. Más tendencias ⭐

**Objetivo:** muestra 10 tendencias en lugar de 5, calculadas con los últimos 3 días en lugar de 7.

**Pistas:**
- Las constantes `TRENDS_LIMIT` y `TRENDS_DAYS` en `App/Helpers/Database/Tables/Post.php`.
- El título "Trends this week" ya no sería correcto: está en `App/View/Home.view.php`.

**Aprendes:** constantes, y que al cambiar una regla hay que revisar los textos que la describen.

### 4. Posts más largos ⭐

**Objetivo:** permite posts de hasta 500 caracteres.

**Pistas:** el número 280 aparece en **cuatro** lugares, y tienen que coincidir:
- `PostController::MAX_LENGTH` (el servidor).
- `MAX_POST_LENGTH` en `public/js-min/Home.min.js` (el contador).
- El `280` inicial del contador en `App/View/Home.view.php`.
- La columna `content varchar(280)` en `database/schema.sql`. En una base MySQL que ya existe hay que cambiarla con
  `ALTER TABLE posts MODIFY content varchar(500) NOT NULL;`. En SQLite, `TEXT` no tiene límite.

**Aprendes:** una misma regla puede vivir en varios lugares. Si cambias uno y te olvidas de otro, aparecen errores raros.

### 5. Hachi en español ⭐

**Objetivo:** traduce toda la interfaz al español.

**Pistas:**
- Los textos de las páginas están en `App/View/` (`Index.view.php`, `Home.view.php`).
- Los mensajes de error los envía el servidor: busca `jsonError(` en `App/Controller/`.
- También hay textos en JavaScript: `'FAILED!'`, `confirm('Delete this post?')`...
- `HomeModule::timeAgo()` devuelve `now`, `5m` y los meses en inglés (`gmdate('M')` da `Sep`).
  Para los meses puedes usar un array: `['Jan' => 'ene', 'Feb' => 'feb', ...]`.
- `HomeModule` arma el texto `3 posts` de las tendencias.
- En `App/View/Head.view.php`, cambia `<html lang="en">` por `lang="es"`.

**Aprendes:** dónde vive cada texto: vistas, servidor y JavaScript.

### 6. La página About ⭐

**Objetivo:** haz el [tutorial](7-tutorial-nueva-pagina.md) completo.

**Aprendes:** todas las capas del proyecto.

---

## Nivel 2: intermedio

### 7. Contador de posts en el perfil ⭐⭐

**Objetivo:** en la página de un usuario (`home.php?user=ID`), debajo del nombre, muestra cuántos posts escribió:
"Ana López · 12 posts".

**Pistas:**
- Nuevo método en `Post.php`, por ejemplo `countPostsByUser(int $userId)`, con `COUNT(*)` y `WHERE user_id = :user_id`.
- En `HomeController`, llámalo solo si hay `$author` y guárdalo en `$this->data`.
- Muéstralo en la cabecera del perfil en `Home.view.php`.
- No uses `count($data['posts'])`: el timeline trae como máximo 50 posts.

**Aprendes:** el camino completo de un dato, desde el SQL hasta la pantalla.

### 8. Posts que me gustaron ⭐⭐

**Objetivo:** una página `home.php?liked=1` con los posts a los que les diste like, y un enlace para llegar.

**Pistas:**
- `Post::selectTimeline()` ya arma los filtros en el array `$where`. Agrega uno más:
  `p.post_id IN (SELECT post_id FROM likes WHERE user_id = :viewer_id)`.
- Lee el parámetro en `HomeController` igual que `user` y `tag`.
- La vista necesita una cabecera para este caso (mira cómo se hace la del hashtag).

**Aprendes:** subconsultas y cómo reutilizar una consulta con filtros opcionales.

### 9. Ver posts más viejos ⭐⭐

**Objetivo:** al final del timeline, un enlace "Older posts" que muestra los 50 posts anteriores.

**Pistas:**
- La técnica más simple: `home.php?before=120` muestra los posts con `post_id < 120`.
- El número del enlace es el `post_id` del último post de la página.
- Muestra el enlace solo si la página tiene 50 posts (si tiene menos, no hay más).
- Que el enlace conserve los filtros `user` y `tag` si los había.

**Aprendes:** paginación, la forma de repartir muchos resultados en varias páginas.

### 10. Tendencias de hoy ⭐⭐

**Objetivo:** en la caja de tendencias, dos pestañas: **Today** (últimas 24 horas) y **This week**.

**Pistas:**
- Convierte los 7 días de `selectWeeklyTrends()` en un parámetro, por ejemplo `selectTrends(int $days)`.
- Elige el período con la dirección, por ejemplo `home.php?trends=today`.
- Recuerda el cálculo de la fecha: `time() - $days * 86400`.

**Aprendes:** convertir código fijo en código reutilizable con parámetros.

### 11. Tus primeras pruebas automáticas ⭐⭐

**Objetivo:** un archivo que revise solo que `Hashtag::extract()` funciona bien.

**Pistas:**
- Crea `tests/hashtag-test.php` con `require __DIR__ . '/../App/Helpers/Hashtag/Hashtag.php';`.
- Escribe casos: un texto y lo que debería devolver. Por ejemplo, `'Hola #PHP y #php'` → `['php']`,
  `'a#b'` → `[]`, `'#1'` → `[]`, `'#Fútbol'` → `['fútbol']`.
- Compara con `===` y muestra "OK" o "FALLA" en cada caso.
- Ejecútalo con `php tests/hashtag-test.php`.
- Rompe la expresión regular a propósito y mira cómo lo detectan tus pruebas.

**Aprendes:** qué es una prueba automática y por qué ahorra tiempo.

---

## Nivel 3: avanzado

### 12. Editar mi perfil ⭐⭐⭐

**Objetivo:** una página para escribir una frase y una ciudad, que se muestren en el perfil.

**Pistas:**
- La tabla `user_profile` ya tiene las columnas `quote` y `city`, sin usar.
- Necesitas una página (el [tutorial](7-tutorial-nueva-pagina.md) te sirve de guía) con un formulario,
  y un endpoint JSON para guardar, con un `UPDATE ... WHERE user_id = :user_id`.
- El `user_id` sale de la sesión, **nunca** del formulario.
- Valida el largo en el servidor (`quote varchar(255)`, `city varchar(100)`) y escapa al mostrar.

**Aprendes:** `UPDATE`, formularios y autorización.

### 13. Editar un post ⭐⭐⭐

**Objetivo:** un botón "Edit" en tus posts que permita cambiar el texto.

**Pistas:**
- Una acción nueva `edit` en `PostController` (sigue la [receta](7-tutorial-nueva-pagina.md#receta-agregar-una-acción-json)).
- `UPDATE posts SET content = :content WHERE post_id = :post_id AND user_id = :user_id`.
- Los hashtags cambian: borra los del post en `post_hashtags` y vuelve a guardar los nuevos,
  dentro de una transacción.
- En JavaScript, cambia el texto del post por un `<textarea>` y, al guardar, por el texto nuevo.

**Aprendes:** transacciones y cambiar la página con JavaScript.

### 14. Seguir usuarios ⭐⭐⭐

**Objetivo:** botón "Follow" en el perfil y una opción del timeline que muestre solo los posts de las personas que sigues.

**Pistas:**
- Nueva tabla `follows (follower_id, followed_id)`, con clave primaria en la pareja, en **los dos** esquemas.
- Seguir y dejar de seguir funciona igual que el like (`toggleLike()`).
- El filtro del timeline: `p.user_id IN (SELECT followed_id FROM follows WHERE follower_id = :viewer_id)`.
- No se puede seguir a uno mismo.

**Aprendes:** diseñar una tabla nueva y una relación entre usuarios.

### 15. Comentarios ⭐⭐⭐

**Objetivo:** comentar los posts y ver cuántos comentarios tiene cada uno.

**Pistas:**
- Nueva tabla `comments (comment_id, post_id, user_id, content, created_at)`.
- Una página por post (por ejemplo `post-view.php?id=5`) con el post, sus comentarios y un formulario.
- El número de comentarios en el timeline se puede contar como los likes (subconsulta en `selectTimeline()`).
- Al borrar un post también hay que borrar sus comentarios.

**Aprendes:** relaciones uno a muchos y páginas de detalle.

### 16. Límite de intentos de login ⭐⭐⭐

**Objetivo:** después de 5 intentos fallidos con el mismo email en 15 minutos, rechazar los siguientes por un rato.

**Pistas:**
- Guarda los intentos fallidos en una tabla nueva (`email`, `created_at`). Guardarlos en la sesión no sirve:
  el atacante puede borrar sus cookies.
- En `LoginController`, antes de comprobar la contraseña, cuenta los intentos recientes.
- Piensa qué mensaje mostrar y si conviene borrar los intentos después de un login correcto.

**Aprendes:** ataques de fuerza bruta y cómo frenarlos ([5. Seguridad](5-seguridad.md)).

### 17. Foto de perfil ⭐⭐⭐

**Objetivo:** subir una imagen y usarla como avatar en lugar de las iniciales.

**Pistas:**
- Formulario con `<input type="file" accept="image/*">`. En PHP llega en `$_FILES`.
- **Seguridad:** comprueba que de verdad es una imagen (`getimagesize()`), limita el tamaño, guárdala con un
  nombre aleatorio que elijas tú (nunca el nombre original) y solo con extensión `.jpg`, `.png` o `.webp`.
- Guárdala en `public/uploads/` y la ruta en `user_profile`.
- Agrega `public/uploads/` al `.gitignore`.

**Aprendes:** subir archivos de forma segura, uno de los puntos más delicados de la web.

---

¿Terminaste alguno? Cuéntalo, compártelo y sigue con el siguiente. **La práctica es lo que hace al programador.**

[← 7. Tutorial](7-tutorial-nueva-pagina.md) · [Glosario →](glosario.md)
