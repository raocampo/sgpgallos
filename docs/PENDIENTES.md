# Pendientes

## Base de datos

- Aplicar `BD/sgpgallos_migracion_v2.sql`.
- Normalizar columnas que hoy guardan IDs como texto en algunas tablas historicas.
- Revisar y completar llaves foraneas.
- Reemplazar la restriccion global de `anillo` por una restriccion compuesta por torneo.

## Seguridad

- Cambiar credenciales administrativas heredadas.
- Sacar configuraciones sensibles del repo si se desea desplegar fuera de local.
- Definir politica de roles y permisos por modulo.

## Negocio

- Mejorar reglas del cotejamiento segun criterios finales del usuario.
- Incorporar dashboard con estadisticas deportivas reales.
- Agregar cierre de torneo y reportes consolidados.
- Agregar historico por gallo y ranking.

## Calidad tecnica

- Agregar pruebas funcionales del flujo principal.
- Reducir deuda tecnica en modulos heredados fuera del flujo principal.
- Revisar consistencia de nombres de columnas y tablas.
- Documentar mejor el proceso de despliegue y backup.

## UX

- Afinar formularios de altas y ediciones restantes.
- Mejorar mensajes de validacion.
- Revisar reportes imprimibles y formato PDF.
