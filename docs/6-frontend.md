# 6. Frontend: CSS y JavaScript

El **frontend** es lo que pasa en el navegador: cómo se ve la página (CSS) y cómo reacciona a lo que haces (JavaScript).
El HTML lo genera PHP con las vistas ([2. Arquitectura](2-arquitectura.md)); aquí vemos todo lo demás.

## Los archivos

```
public/
├── css-min/
│   ├── master.min.css   estilos de todas las páginas (la alerta)
│   ├── Index.min.css    página de login y registro
│   └── Home.min.css     timeline
├── js-min/
│   ├── master.min.js    funciones de todas las páginas: postJson, formToObject, alertDialog
│   ├── Index.min.js     login y registro
│   └── Home.min.js      escribir posts, likes y borrar
└── img/                 imágenes del login
```

> Los nombres terminan en `.min` por costumbre del proyecto, pero **no están minificados**
> (minificar es quitar espacios y comentarios para que el archivo pese menos). Puedes leerlos y editarlos normalmente.
> La excepción es la primera parte de `Index.min.css`, que viene de una plantilla y sí está minificada.

`Module::addResources()` decide qué archivos carga cada página, en este orden:

1. Los externos: la fuente Poppins y los iconos (de internet).
2. Los compartidos: `master.min.css` y `master.min.js`.
3. Los de la página, si existen: `Home.min.css` y `Home.min.js` para `home.php`.

El orden importa: en CSS, si dos reglas chocan, gana la que se carga después; en JavaScript,
`Home.min.js` puede usar `postJson()` porque `master.min.js` se cargó antes.

---

## CSS

### Variables

Al principio de `Home.min.css` están los colores en **variables CSS**:

```css
:root {
  --brand: #4292dc;      /* azul de Hachi */
  --bg: #f5f7fa;         /* fondo */
  --like: #e0245e;       /* rojo del corazón */
}

.hashtag {
  color: var(--brand);   /* se usan con var() */
}
```

Cambia `--brand` y cambia el azul en toda la página. Pruébalo.

### El diseño con flexbox

El timeline y las tendencias se ponen lado a lado con **flexbox**:

```css
.layout {
  display: flex;   /* los hijos se ponen en fila */
  gap: 24px;       /* espacio entre ellos */
}

.timeline { flex: 1; }         /* ocupa todo el espacio que sobra */
.sidebar  { width: 300px; }    /* ancho fijo */
```

```
┌──────────────────── .layout (display: flex) ────────────────────┐
│ ┌──────────── .timeline (flex: 1) ────────────┐ ┌─ .sidebar ──┐ │
│ │ caja para escribir                           │ │ Trends this │ │
│ │ post                                         │ │ week        │ │
│ │ post                                         │ │ (300px)     │ │
│ └──────────────────────────────────────────────┘ └─────────────┘ │
└──────────────────────────────────────────────────────────────────┘
```

### Diseño responsive (celulares)

Las **media queries** aplican reglas solo en pantallas pequeñas:

```css
@media screen and (max-width: 900px) {
  .layout  { flex-direction: column; }   /* una columna en vez de dos */
  .sidebar { order: -1; }                /* las tendencias pasan arriba */
}
```

Para probarlo, abre las herramientas del navegador (`F12`) y activa el **modo dispositivo**
(`Ctrl + Shift + M`, o `Cmd + Shift + M` en Mac).

### Elementos fijos al hacer scroll

`position: sticky` deja la barra superior (`.topbar`) y las tendencias (`.sidebar`) fijas mientras bajas por el timeline.

### Animaciones

La alerta (`master.min.css`) empieza escondida arriba de la pantalla y transparente. Cuando JavaScript le agrega
la clase `display-alert`, baja y aparece. La transición hace que el cambio sea suave:

```css
.alert {
  opacity: 0;
  transform: translate(-50%, -150%);         /* escondida arriba */
  transition: opacity 0.4s, transform 0.4s;  /* animar estos cambios durante 0.4 segundos */
}

.alert.display-alert {
  opacity: 1;
  transform: translate(-50%, 0);             /* en su lugar */
}
```

El cambio entre los formularios de login y registro (`Index.min.css`) usa la misma idea con las clases
`display-on` y `display-off`.

---

## JavaScript

### Cuándo se ejecuta: `defer`

Los scripts están en el `<head>` con el atributo `defer`:

```html
<script src="public/js-min/Home.min.js" defer></script>
```

`defer` hace que se ejecuten **cuando el HTML ya terminó de cargarse**. Sin `defer`, `document.querySelector('.timeline')`
no encontraría nada, porque el timeline todavía no existiría.

### `master.min.js`: las herramientas compartidas

**`postJson(url, datos)`** envía datos al servidor y devuelve la respuesta. Usa `fetch`, que hace
peticiones sin recargar la página:

```js
async function postJson(url, payload) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)      // objeto -> texto JSON
    });
    return await response.json();          // texto JSON -> objeto
}
```

- `async` / `await`: `fetch` tarda (va por la red). `await` espera la respuesta sin congelar la página.
- `JSON.stringify` convierte el objeto en texto para enviarlo; `response.json()` hace lo contrario.
- La versión real también maneja los errores de red y la sesión vencida (respuesta 401).

**`formToObject(form)`** convierte un formulario en un objeto usando el atributo `name` de cada campo:

```html
<input name="email" value="ana@example.com">
<input name="pass" value="secreto">
```

```js
formToObject(form)   // {email: 'ana@example.com', pass: 'secreto'}
```

**`alertDialog(titulo, mensaje)`** muestra la alerta roja durante 3 segundos.

### `Index.min.js`: login y registro

```js
document.querySelector('#login-form').addEventListener('submit', (event) => {
    event.preventDefault();                   // 1. no dejar que el navegador recargue la página
    submitForm(event.target, 'login.php');    // 2. enviar los datos con fetch
});
```

`submitForm()` desactiva el botón mientras espera (así no se envía dos veces), llama a `postJson()`
y, si la respuesta tiene `code: 0`, va a `home.php`. Si no, muestra el error.

### `Home.min.js`: el timeline

**El contador de caracteres** se actualiza con el evento `input`, que ocurre con cada tecla:

```js
textarea.addEventListener('input', updateCounter);
```

Para contar usa `Array.from(texto).length` y no `texto.length`, porque algunos emojis ocupan dos
"unidades" en JavaScript: `"🐶".length` es 2, pero `Array.from("🐶").length` es 1.

**Delegación de eventos.** En lugar de poner un `click` en cada botón de cada post, hay uno solo
en todo el timeline:

```js
document.querySelector('.timeline').addEventListener('click', async (event) => {
    const post = event.target.closest('.post');          // ¿en qué post fue el clic?
    const postId = post.dataset.postId;                  // lee data-post-id="5" del HTML
    const likeButton = event.target.closest('.like-btn');   // ¿fue en el botón de like?
    // ...
});
```

- `event.target` es el elemento exacto donde se hizo clic (por ejemplo, el corazón).
- `closest('.post')` sube por el HTML hasta encontrar el post que lo contiene.
- `dataset.postId` lee el atributo `data-post-id` que puso la vista.

Ventaja: funciona con cualquier cantidad de posts, incluso con los que se agreguen después.

**Actualizar la página sin recargarla.** Después de un like, solo se cambia ese botón:

```js
likeButton.classList.toggle('liked', data.liked);                      // corazón rojo o gris
likeButton.querySelector('.like-count').textContent = data.likes;     // el número
```

Y después de borrar, el post se quita con `post.remove()`.

## Lo que se envían el navegador y el servidor

| Acción | Archivo JS | Envía a | Datos | Respuesta si sale bien |
|---|---|---|---|---|
| Iniciar sesión | `Index.min.js` | `login.php` | `{email, pass, remember-me}` | `{code: 0}` |
| Crear cuenta | `Index.min.js` | `register.php` | `{first_name, last_name, email, pass, re_pass}` | `{code: 0}` |
| Publicar | `Home.min.js` | `post.php` | `{action: 'create', content}` | `{code: 0}` |
| Like | `Home.min.js` | `post.php` | `{action: 'like', post_id}` | `{code: 0, liked, likes}` |
| Borrar | `Home.min.js` | `post.php` | `{action: 'delete', post_id}` | `{code: 0}` |

Si algo sale mal, la respuesta es siempre `{code: 1, title, message}`.

## Herramientas del navegador (`F12`)

| Pestaña | Para qué |
|---|---|
| **Elementos** (Elements) | Ver el HTML y probar cambios de CSS en vivo |
| **Consola** (Console) | Ver los errores de JavaScript y probar código, por ejemplo `alertDialog('Hola', 'Probando')` |
| **Red** (Network) | Ver cada petición a `post.php`, lo que se envió y lo que respondió el servidor |

---

[← 5. Seguridad](5-seguridad.md) · Siguiente: [7. Tutorial: agregar una página →](7-tutorial-nueva-pagina.md)
