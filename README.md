# Sistema de Competencia SG

Aplicacion web en PHP + PDO para gestionar torneos, gallos, exclusiones, cotejamiento, peleas pactadas y resultados.

## Estado actual

El sistema fue refactorizado sobre la base existente. Hoy cuenta con:

- autenticacion mas segura
- panel administrativo con UI renovada
- seleccion de torneo por contexto
- CRUD principales endurecidos con `POST` y CSRF
- modulo de resultados funcional
- flujo de cotejamiento y peleas reorganizado
- archivos legacy del modulo de peleas archivados

Documentacion complementaria:

- [Avances](docs/AVANCES.md)
- [Pendientes](docs/PENDIENTES.md)
- [Migracion sugerida](BD/sgpgallos_migracion_v2.sql)

## Requisitos

- PHP 8.0 o superior
- MySQL o MariaDB
- Servidor local tipo Laragon, XAMPP o Apache/Nginx con PHP
- Extension PDO MySQL habilitada

## Estructura principal

```text
admin/
  bd.php                         Conexion PDO
  includes/app.php              Sesion, auth, CSRF, helpers
  templates/                    Plantillas del panel
  secciones/
    torneos/                    Torneos
    gallos/                     Registro de gallos
    exclusiones/                Exclusiones por torneo
    peleas/                     Cotejamiento, peleas y resultados
BD/
  sgpgallos.sql                 Dump base del proyecto
  sgpgallos_migracion_v2.sql    Ajustes de esquema recomendados
css/
  admin-panel.css               UI nueva del panel
```

## Configuracion local

Por defecto la conexion usa estos valores:

- host: `localhost`
- base de datos: `sgpgallos`
- usuario: `root`
- clave: vacia

Se pueden sobrescribir por variables de entorno:

- `SG_DB_HOST`
- `SG_DB_NAME`
- `SG_DB_USER`
- `SG_DB_PASS`

Ejemplo en Laragon o Apache:

```powershell
$env:SG_DB_HOST="localhost"
$env:SG_DB_NAME="sgpgallos"
$env:SG_DB_USER="root"
$env:SG_DB_PASS=""
```

## Instalacion

1. Clonar el repositorio en el directorio web.
2. Crear la base `sgpgallos`.
3. Importar [BD/sgpgallos.sql](BD/sgpgallos.sql).
4. Aplicar [BD/sgpgallos_migracion_v2.sql](BD/sgpgallos_migracion_v2.sql) si se desea normalizar y mejorar indices.
5. Levantar el servidor local y abrir `http://localhost/SG/`.

## Flujo de uso

1. Ingresar al panel administrativo.
2. Crear o abrir un torneo.
3. Cargar representantes, criaderos y gallos.
4. Configurar exclusiones del torneo si aplica.
5. Ir a `Cotejas` y generar propuestas por peso, altura, nacimiento y exclusiones.
6. Pactar peleas desde las cotejas seleccionadas.
7. Registrar resultados y emitir reporte.

## Seguridad aplicada

- login con `password_hash()` y `password_verify()`
- migracion automatica de claves legacy a hash en login
- proteccion CSRF en formularios de escritura
- operaciones destructivas migradas a `POST`
- cierre de sesion limpio
- contexto de torneo centralizado

## Modulos activos

- Torneos
- Representantes
- Criaderos
- Gallos
- Exclusiones
- Cotejamiento
- Peleas pactadas
- Resultados
- Usuarios

## Notas tecnicas

- El modulo activo de peleas vive en:
  - `admin/secciones/peleas/cotejamiento.php`
  - `admin/secciones/peleas/peleaGenerada.php`
  - `admin/secciones/peleas/resultados.php`
  - `admin/secciones/peleas/reportePelea.php`
- Las versiones historicas fueron movidas a `admin/secciones/peleas/legacy/`.
- `admin/bd.php` agrega columnas minimas de resultados en `peleas` si aun no existen.

## Recomendaciones inmediatas

1. Cambiar credenciales administrativas antiguas desde el modulo de usuarios.
2. Probar el flujo completo en navegador:
   torneo -> gallos -> exclusiones -> cotejas -> peleas -> resultados.
3. Aplicar la migracion de base de datos para mejorar integridad.

## Git

Repositorio remoto configurado:

- `origin`: `https://github.com/raocampo/sgpgallos.git`

## Licencia

Uso interno del proyecto, salvo dependencias de terceros incluidas en el repositorio.
