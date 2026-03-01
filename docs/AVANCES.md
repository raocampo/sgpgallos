# Avances del proyecto

## 1. Base comun del sistema

- Se centralizo la infraestructura comun en `admin/includes/app.php`.
- Se incorporaron helpers para:
  - sesion segura
  - autenticacion
  - CSRF
  - flashes
  - redirecciones
  - contexto de torneo
  - validaciones de estado del torneo
- `admin/bd.php` ahora soporta configuracion por entorno y compatibilidad gradual con cambios de esquema.

## 2. Seguridad y acceso

- El login fue rehecho para trabajar con hash de contrasenas.
- Se dejo migracion automatica de contrasenas heredadas al iniciar sesion.
- Altas y ediciones de usuarios ya no dependen de texto plano.
- Se agrego pantalla de cambio de contrasena para el usuario activo.
- Varias acciones criticas fueron movidas de `GET` a `POST`.

## 3. Torneos

- Se corrigio la seleccion y apertura del torneo activo.
- Se agrego dashboard operativo del torneo.
- Se incorporo estado `abierto/cerrado`.
- Se puede cerrar o reabrir el torneo desde el dashboard.
- Los modulos deportivos se bloquean cuando el torneo esta cerrado.

## 4. Modulo deportivo

- Se corrigio el flujo de exclusiones por torneo.
- Se rehizo el cotejamiento para trabajar con:
  - tolerancia de peso
  - tolerancia de altura
  - compatibilidad por nacimiento
  - respeto de exclusiones
  - gallos libres
  - registro manual de cotejas
- El algoritmo ya no depende solo del orden de lista; prioriza mejores parejas validas segun tolerancias activas.
- Se reforzo el pactado de peleas para evitar:
  - peleas duplicadas
  - gallos comprometidos mas de una vez
- Se implemento registro de resultados por pelea.
- Se mantuvo el reporte PDF operativo.

## 5. Interfaz administrativa

- Se creo una capa visual nueva en `css/admin-panel.css`.
- El panel maestro y el flujo del torneo usan sidebar responsivo.
- Se mejoraron:
  - tarjetas de metricas
  - tablas
  - layout de formularios
  - navegacion del torneo
  - resultados desde modal
- Se compactaron tablas del flujo deportivo para mejorar lectura.

## 6. Mantenimiento del codigo

- Se archivaron variantes legacy del modulo de peleas.
- Se limpio el flujo principal para dejar una ruta activa mas clara:
  - cotejamiento
  - peleas pactadas
  - resultados
  - reporte
- Se actualizaron dump y migracion para reflejar el estado actual del sistema.
- Se mejoro la documentacion del repositorio.

## 7. Estado actual del flujo principal

El flujo principal hoy permite operar de forma continua:

1. Iniciar sesion.
2. Crear o abrir torneo.
3. Cargar entidades base.
4. Registrar exclusiones.
5. Generar cotejas.
6. Pactar peleas.
7. Registrar resultados.
8. Consultar dashboard del torneo.

## 8. Verificacion realizada

- Se hicieron validaciones de sintaxis con `php -l` en archivos criticos modificados.
- No se dejo aun una bateria automatizada de pruebas funcionales end-to-end.
