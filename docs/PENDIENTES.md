# Pendientes del proyecto

## Alta prioridad

### 1. Base de datos

- Aplicar en entorno real [BD/sgpgallos_migracion_v2.sql](../BD/sgpgallos_migracion_v2.sql).
- Normalizar columnas historicas donde todavia se guardan IDs como texto.
- Completar llaves foraneas y restricciones de integridad.
- Revisar la regla de unicidad de `anillo` para dejarla por torneo.

### 2. Seguridad

- Cambiar credenciales administrativas heredadas.
- Definir roles y permisos por modulo.
- Evaluar sacar configuraciones sensibles del repo si se despliega fuera de local.

### 3. Pruebas reales

- Ejecutar prueba funcional completa del flujo del torneo en navegador.
- Validar escenarios con torneo cerrado.
- Validar flujo de resultados en movil y escritorio con datos reales.

## Prioridad media

### 4. Negocio y operacion

- Afinar mas reglas del cotejamiento segun criterios finales del usuario.
- Agregar ranking por criadero, representante y gallo.
- Incorporar historico por gallo.
- Generar reportes consolidados de cierre del torneo.
- Definir mejor reglas de anulacion, reapertura y cierre deportivo.

### 5. UX y UI

- Seguir compactando tablas del panel.
- Mejorar formularios de altas y ediciones restantes.
- Afinar estados vacios, mensajes de error y confirmaciones.
- Revisar consistencia visual del sidebar y encabezados en todas las pantallas.

## Prioridad baja

### 6. Calidad tecnica

- Reducir deuda tecnica en modulos heredados fuera del flujo principal.
- Revisar nombres historicos de columnas y tablas para estandarizacion.
- Documentar despliegue, respaldo y restauracion.
- Incorporar pruebas automatizadas minimas del flujo principal.
