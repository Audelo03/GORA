# Tema Dorado/Negro para GORA

Este documento describe los pasos realizados para cambiar la paleta de colores de toda la aplicación a una temática de dorado y negro, y cómo está estructurado el SCSS para mantener el diseño organizado y escalable.

## Pasos realizados

1. Creación de variables de tema
   - Se creó `public/scss/_variables.scss` con la paleta de colores (dorado, negro y grises), radios, sombras, tipografías y breakpoints.

2. Creación de mixins reutilizables
   - Se creó `public/scss/_mixins.scss` con utilidades para aros de enfoque (accesibilidad), truncado de texto, efectos de hover, superficies tipo “card” y gradientes dorados.

3. Hoja de tema principal
   - Se creó `public/scss/theme.scss` que importa variables y mixins y aplica el tema a elementos globales, `navbar`, `sidebar`, contenido, formularios, botones, tablas/DataTables, badges, modales, alertas y Select2.

4. Compilación de SCSS a CSS
   - Se agregó `sass` como dependencia de desarrollo y scripts a `package.json`.
   - Comando de build: `npm run build:css` (compila a `public/css/theme.css`).
   - Comando de watch: `npm run watch:css` (recompila automáticamente en cambios).

5. Enlace del CSS del tema
   - Se añadió en `views/objects/header.php` la línea de enlace a `/GORA/public/css/theme.css` para que se aplique en toda la app.

## Estructura SCSS

```
public/
  scss/
    _variables.scss   // Paleta, tamaños, sombras, tipografía, breakpoints
    _mixins.scss      // Mixins: focus-ring, truncate, hover-brighten, card-surface, gradientes
    theme.scss        // Entrada principal: aplica el tema a componentes y utilidades
```

### `public/scss/_variables.scss`
- Colores base: negro, grafito y grises.
- Dorados: principal, claro y oscuro.
- Estados: éxito, peligro, aviso, info.
- Tipografía base y radios de borde.
- Sombras y z-index de overlays.
- Breakpoints.

### `public/scss/_mixins.scss`
- `focus-ring($color)`: aro de enfoque accesible con sombra.
- `truncate($lines)`: truncado multi-línea.
- `hover-brighten($factor)`: efecto de brillo al pasar el cursor.
- `card-surface`: superficie con degradado oscuro, borde sutil y sombra.
- `gold-gradient` y `text-gold`: gradientes dorados (de fondo y de texto).

### `public/scss/theme.scss`
- Global: fondo oscuro, texto claro, tipografía base, enlaces dorados.
- Utilidades: `.text-gold`, `.bg-gold`, `.border-gold`, `.btn-gold`.
- Navbar: fondo oscuro, bordes sutiles, enlaces con hover dorado, menús acorde al tema.
- Sidebar: degradado oscuro, estado activo en dorado semitransparente, hover en dorado.
- Contenido: degradado con “halo” dorado sutil.
- Tarjetas: superficies tipo card reutilizando `card-surface`.
- Formularios: campos oscuros con foco dorado, placeholders grises.
- Botones: primarios y variantes con dorado; outline con borde dorado.
- Tablas y DataTables: encabezados dorados, filas con hover sutil.
- Badges: dorados con texto oscuro.
- Modales/Alertas: superficies y bordes acordes al tema.
- Select2: estilos oscuros para integrarse al tema.

## Cómo compilar

1. Instalar dependencias (ya ejecutado):
   - `npm i`
2. Compilar una vez:
   - `npm run build:css`
3. Vigilar cambios mientras desarrollas:
   - `npm run watch:css`

El archivo resultante es `public/css/theme.css` y se incluye globalmente desde `views/objects/header.php`.

## Notas
- No se modificó ninguna lógica de PHP o JS; solo estilos.
- El tema está pensado para convivir con Bootstrap, priorizando nuestros estilos donde es necesario.


