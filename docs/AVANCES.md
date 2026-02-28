# Avances

## Base del sistema

- Conexion PDO centralizada en `admin/bd.php`.
- Helper comun en `admin/includes/app.php` con:
  - sesiones seguras
  - flashes
  - CSRF
  - auth
  - contexto de torneo
- Login y logout rehechos para trabajar con contrasenas en hash.

## Seguridad

- Eliminado el uso operativo de contrasenas en texto plano.
- Altas y ediciones de usuarios guardan claves con hash.
- Varias acciones destructivas pasaron de `GET` a `POST`.
- Se incorporo confirmacion para borrados en UI.

## UI y experiencia

- Nuevo estilo administrativo en `css/admin-panel.css`.
- Dashboard principal mejorado.
- Pantallas de torneos, gallos, cotejamiento, peleas y resultados con tarjetas, metricas y tablas mas limpias.
- Navegacion por torneo activo.

## Flujo deportivo

- Seleccion de torneo corregida desde URL + sesion.
- Modulo de exclusiones corregido.
- Cotejamiento rehecho con:
  - filtro por peso
  - filtro por altura
  - filtro por nacimiento
  - aplicacion de exclusiones
  - gallos libres
  - coteja manual
- Pactado de peleas con validacion para evitar duplicados y gallos ya comprometidos.
- Registro de resultados operativo.
- Reporte PDF de peleas disponible.

## Codigo y mantenimiento

- Archivos duplicados del modulo de peleas archivados en `admin/secciones/peleas/legacy/`.
- README del repo actualizado.
- Dump SQL ajustado para no publicar usuarios en texto plano.
- Migracion sugerida agregada en `BD/sgpgallos_migracion_v2.sql`.
