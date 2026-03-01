-- Migracion sugerida para estabilizar el sistema mejorado.
-- Ejecutar sobre la base de datos sgpgallos.

ALTER TABLE `peleas`
  ADD COLUMN `estado` VARCHAR(20) NOT NULL DEFAULT 'pendiente' AFTER `torneoId`,
  ADD COLUMN `ganador` VARCHAR(255) DEFAULT NULL AFTER `estado`,
  ADD COLUMN `observaciones` TEXT DEFAULT NULL AFTER `ganador`,
  ADD COLUMN `fecha_resultado` DATETIME DEFAULT NULL AFTER `observaciones`;

ALTER TABLE `torneos`
  ADD COLUMN `estado` VARCHAR(20) NOT NULL DEFAULT 'abierto' AFTER `tipoTorneo`,
  ADD COLUMN `fecha_cierre_real` DATETIME DEFAULT NULL AFTER `estado`;

ALTER TABLE `gallos`
  DROP INDEX `anillo`,
  ADD UNIQUE KEY `ux_gallos_anillo_torneo` (`anillo`, `torneoId`);

ALTER TABLE `usuarios`
  ADD UNIQUE KEY `ux_usuarios_apodo` (`apodo`);

ALTER TABLE `peleas`
  ADD KEY `idx_peleas_estado` (`estado`);

ALTER TABLE `exclusiones`
  ADD KEY `idx_exclusiones_torneo_familias` (`torneoId`, `nombreFamiliaUno`, `nombreFamiliaDos`);

ALTER TABLE `torneos`
  ADD KEY `idx_torneos_estado` (`estado`);

