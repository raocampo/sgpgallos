# Sistema de Competencia SG

Aplicacion web en PHP + PDO para administrar torneos de competencia: torneos, criaderos, representantes, gallos, exclusiones, cotejamiento, peleas y resultados.

## Resumen

El proyecto fue mejorado sobre la base existente, sin reescribirlo en framework. El objetivo fue estabilizar el flujo principal del torneo, reforzar seguridad basica, limpiar deuda tecnica del modulo deportivo y modernizar la interfaz administrativa.

Estado actual del flujo principal:

1. Iniciar sesion.
2. Crear o abrir torneo.
3. Cargar criaderos, representantes y gallos.
4. Registrar exclusiones.
5. Generar cotejas con tolerancias.
6. Pactar peleas.
7. Registrar resultados.
8. Consultar resumen del torneo y cierre operativo.

Documentacion complementaria:

- [Avances](docs/AVANCES.md)
- [Pendientes](docs/PENDIENTES.md)
- [Migracion de base sugerida](BD/sgpgallos_migracion_v2.sql)

## Stack

- PHP 8+
- PDO MySQL
- MySQL o MariaDB
- Bootstrap local
- jQuery + DataTables
- DOMPDF para reportes PDF

## Modulos activos

- Autenticacion de usuarios
- Torneos
- Representantes
- Criaderos
- Gallos
- Exclusiones
- Cotejamiento
- Peleas pactadas
- Resultados
- Dashboard operativo del torneo
- Cambio de contrasena del usuario activo

## Mejoras implementadas

- Login endurecido con hash de contrasenas y migracion de claves legacy.
- Helpers comunes para sesion, auth, flashes, escapes, CSRF y contexto del torneo.
- Acciones destructivas principales movidas de `GET` a `POST`.
- Sidebar administrativo y sidebar del torneo responsivos.
- Dashboard del torneo con estado, avance y accesos rapidos.
- Cotejamiento afinado con tolerancias de peso y altura, exclusiones y filtro de nacimiento.
- Control para evitar peleas duplicadas o gallos ya comprometidos.
- Registro de resultados desde modal por pelea.
- Estado de torneo `abierto/cerrado` con bloqueo de modificaciones deportivas.

## Estructura del proyecto

```text
admin/
  bd.php                         Conexion PDO y ajustes de esquema compatibles
  includes/app.php              Helpers comunes del sistema
  templates/                    Shell administrativo y layout del torneo
  secciones/
    torneos/                    Torneos y dashboard operativo
    representantes/             CRUD representantes
    familias/                   CRUD criaderos
    gallos/                     CRUD gallos
    exclusiones/                Reglas de exclusiones por torneo
    peleas/                     Cotejamiento, peleas, resultados y reporte
    usuarios/                   Usuarios y cambio de contrasena
BD/
  sgpgallos.sql                 Dump actualizado
  sgpgallos_migracion_v2.sql    Ajustes recomendados de esquema
css/
  admin-panel.css               Estilos del panel renovado
js/
  scriptcheck.js                Interacciones de tolerancia de peso
  scriptcheckAltura.js          Interacciones de tolerancia de altura
docs/
  AVANCES.md                    Resumen de trabajo realizado
  PENDIENTES.md                 Backlog tecnico y funcional
```

## Requisitos

- PHP 8.0 o superior
- MySQL o MariaDB
- Extension `pdo_mysql`
- Servidor local tipo Laragon, XAMPP o similar

## Configuracion local

La conexion usa por defecto:

- Host: `localhost`
- Base: `sgpgallos`
- Usuario: `root`
- Clave: vacia

Tambien se puede configurar por variables de entorno:

- `SG_DB_HOST`
- `SG_DB_NAME`
- `SG_DB_USER`
- `SG_DB_PASS`

Ejemplo en PowerShell:

```powershell
$env:SG_DB_HOST = "localhost"
$env:SG_DB_NAME = "sgpgallos"
$env:SG_DB_USER = "root"
$env:SG_DB_PASS = ""
```

## Instalacion

1. Clonar el repositorio en el directorio publico del servidor local.
2. Crear la base de datos `sgpgallos`.
3. Importar [BD/sgpgallos.sql](BD/sgpgallos.sql).
4. Aplicar [BD/sgpgallos_migracion_v2.sql](BD/sgpgallos_migracion_v2.sql) si se desea completar el esquema recomendado.
5. Abrir `http://localhost/SG/`.

## Flujo recomendado de uso

1. Crear usuarios administrativos y cambiar credenciales iniciales.
2. Crear un torneo.
3. Registrar criaderos y representantes.
4. Cargar gallos en el torneo.
5. Definir exclusiones si aplica.
6. Configurar tolerancias de cotejamiento.
7. Pactar peleas.
8. Registrar resultados.
9. Revisar dashboard y cierre del torneo.

## Seguridad aplicada

- `password_hash()` y `password_verify()`
- Migracion automatica de claves legacy al iniciar sesion
- Token CSRF en formularios de escritura
- Uso de `POST` para altas, bajas y cambios sensibles principales
- Escape centralizado de salida HTML
- Contexto de torneo centralizado para evitar errores de navegacion
- Cierre de torneo para bloquear cambios deportivos cuando corresponde

## Base de datos

El dump principal ya incluye mejoras del sistema actual, pero todavia existe deuda historica en algunas tablas. La migracion [BD/sgpgallos_migracion_v2.sql](BD/sgpgallos_migracion_v2.sql) deja preparado:

- estado del torneo
- fecha real de cierre
- columnas de resultados en `peleas`
- indices y ajustes operativos base

## Reportes

- Reporte PDF de peleas desde `admin/secciones/peleas/reportePelea.php`

## Estado del repositorio

- Rama principal: `main`
- Remoto configurado: `origin`

## Recomendaciones inmediatas

1. Probar el flujo completo en navegador con datos reales.
2. Aplicar la migracion SQL recomendada.
3. Cambiar credenciales administrativas heredadas.
4. Continuar con ranking, historico por gallo y reportes de cierre.

## Licencia

Uso interno del proyecto, salvo dependencias de terceros incluidas en el repositorio.
