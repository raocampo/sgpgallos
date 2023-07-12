-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-07-2023 a las 15:05:33
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sgpgallos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coteja`
--

CREATE TABLE `coteja` (
  `ID_Coteja` int(11) NOT NULL,
  `galloL` varchar(255) NOT NULL,
  `galloV` varchar(255) NOT NULL,
  `estado` int(2) NOT NULL,
  `torneoId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotejaaut`
--

CREATE TABLE `cotejaaut` (
  `ID_Coteja1` int(11) NOT NULL,
  `galloL` varchar(255) NOT NULL,
  `galloV` varchar(255) NOT NULL,
  `estado` int(11) NOT NULL,
  `torneoId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `exclusiones`
--

CREATE TABLE `exclusiones` (
  `IdExclusion` int(11) NOT NULL,
  `nombreFamiliaUno` varchar(255) NOT NULL,
  `nombreFamiliaDos` varchar(255) NOT NULL,
  `torneoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `exclusiones`
--

INSERT INTO `exclusiones` (`IdExclusion`, `nombreFamiliaUno`, `nombreFamiliaDos`, `torneoId`) VALUES
(1, '1', '4', 1),
(2, '1', '6', 1),
(3, '2', '6', 1),
(4, '10', '6', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familias`
--

CREATE TABLE `familias` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `localidad` varchar(255) NOT NULL,
  `representanteId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `familias`
--

INSERT INTO `familias` (`codigo`, `nombre`, `localidad`, `representanteId`) VALUES
(1, 'LA ZARZA', 'LOJA', 1),
(2, 'LA SOCIEDAD', 'LOJA', 19),
(3, 'CRIADERO RIVERA', 'SANTO DOMINGO', 4),
(4, 'OROBIO DMC', 'CUENCA', 20),
(5, 'GALPÓN QUINARA', 'USA', 6),
(6, 'LA DUPLA', 'MALACATOS', 8),
(7, 'HNOS. VALDIVIESO', 'LOJA', 9),
(8, 'SANTANA', 'LOJA', 10),
(9, 'QUINARA', 'QUINARA', 11),
(10, 'EL PRESI', 'USA', 12),
(11, 'SALVAJE', 'PERÚ', 13),
(12, 'SAMANIEGO', 'LOJA', 14),
(13, 'SAMANIEGO', 'LOJA', 15),
(14, 'LA ZARZA', 'LOJA', 1),
(15, 'JP', 'CARIAMANGA', 16),
(16, 'CHUQUIMARCA', 'CARIAMANGA', 17),
(17, 'EL CASCAJO', 'LOJA', 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gallos`
--

CREATE TABLE `gallos` (
  `ID` int(11) NOT NULL,
  `anillo` varchar(255) NOT NULL,
  `pesoReal` decimal(4,2) DEFAULT NULL,
  `tamañoReal` decimal(4,2) DEFAULT NULL,
  `placa` varchar(255) NOT NULL,
  `nacimiento` text NOT NULL,
  `frente` text NOT NULL,
  `familiasId` int(2) NOT NULL,
  `representanteId` int(11) NOT NULL,
  `torneoId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `gallos`
--

INSERT INTO `gallos` (`ID`, `anillo`, `pesoReal`, `tamañoReal`, `placa`, `nacimiento`, `frente`, `familiasId`, `representanteId`, `torneoId`) VALUES
(2, '6811', '3.09', '85.10', '', '', '1', 2, 19, 1),
(3, '6812', '3.10', '81.10', '', '', '1', 2, 19, 1),
(5, '6809', '3.15', '86.40', '', '', '2', 3, 4, 1),
(6, '6810', '3.14', '84.10', '', '', '2', 3, 4, 1),
(7, '6807', '4.01', '85.90', '', '', '1', 3, 4, 1),
(8, '6808', '3.06', '84.80', '', '', '1', 3, 4, 1),
(9, '6805', '3.13', '83.40', '', '', '2', 4, 5, 1),
(10, '6806', '3.07', '78.80', '', '', '2', 4, 5, 1),
(11, '6803', '4.02', '89.40', '', '', '1', 4, 5, 1),
(12, '6804', '4.07', '91.50', '', '', '1', 4, 5, 1),
(13, '6801', '3.08', '83.70', '', '', '1', 5, 6, 1),
(14, '6802', '3.11', '89.70', '', '', '1', 5, 6, 1),
(15, '6799', '3.08', '85.60', '', '', '2', 5, 7, 1),
(16, '6800', '3.08', '84.70', '', '', '2', 5, 7, 1),
(17, '6797', '4.06', '96.00', '', '', '1', 5, 7, 1),
(18, '6798', '4.00', '86.50', '', '', '1', 5, 7, 1),
(19, '6795', '4.02', '92.80', '', '', '1', 6, 8, 1),
(20, '6796', '3.09', '86.60', '', '', '1', 6, 8, 1),
(22, '6793', '3.15', '94.30', '', '', '2', 7, 9, 1),
(23, '6794', '3.11', '88.70', '', '', '2', 7, 9, 1),
(24, '6791', '3.14', '93.30', '', '', '1', 7, 9, 1),
(25, '6792', '3.07', '83.80', '', '', '1', 7, 9, 1),
(26, '6789', '3.12', '82.60', '', '', '1', 8, 10, 1),
(27, '6790', '3.12', '92.10', '', '', '1', 8, 10, 1),
(28, '6787', '4.02', '90.00', '', '', '1', 5, 11, 1),
(29, '6788', '3.03', '83.70', '', '', '1', 5, 11, 1),
(30, '6785', '3.14', '83.90', '', '', '2', 10, 12, 1),
(31, '6786', '3.10', '81.40', '', '', '2', 10, 12, 1),
(32, '6783', '3.09', '88.60', '', '', '1', 10, 12, 1),
(33, '6784', '3.09', '85.70', '', '', '1', 10, 12, 1),
(34, '6781', '3.15', '89.50', '', '', '3', 11, 13, 1),
(35, '6782', '3.07', '81.10', '', '', '3', 11, 13, 1),
(36, '6779', '3.02', '81.50', '', '', '2', 11, 13, 1),
(37, '6780', '3.07', '80.00', '', '', '2', 11, 13, 1),
(38, '6777', '4.01', '89.00', '', '', '1', 11, 13, 1),
(39, '6778', '3.14', '84.80', '', '', '1', 11, 13, 1),
(40, '6775', '4.02', '88.40', '', '', '1', 12, 14, 1),
(41, '6776', '4.04', '91.60', '', '', '1', 12, 14, 1),
(42, '6773', '3.10', '89.40', '', '', '1', 12, 15, 1),
(43, '6774', '4.00', '87.70', '', '', '1', 12, 15, 1),
(44, '6771', '3.14', '76.80', '', '', '3', 14, 1, 1),
(45, '6772', '3.13', '82.50', '', '05', '3', 1, 1, 1),
(46, '6769', '3.11', '81.30', '', '', '2', 1, 1, 1),
(47, '6770', '3.10', '84.50', '', '', '2', 1, 1, 1),
(48, '6767', '3.09', '83.10', '', '', '1', 1, 1, 1),
(49, '6768', '3.11', '84.60', '', '', '1', 1, 1, 1),
(50, '6766', '4.02', '85.40', '', '', '1', 15, 16, 1),
(51, '6765', '3.12', '91.10', '', '', '1', 15, 16, 1),
(52, '6764', '4.02', '83.40', '', '', '1', 16, 17, 1),
(53, '6763', '3.10', '85.90', '', '', '1', 16, 17, 1),
(54, '6762', '3.04', '91.70', '', '', '1', 17, 18, 1),
(55, '6761', '4.02', '91.40', '', '', '1', 17, 18, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peleas`
--

CREATE TABLE `peleas` (
  `ID_Pelea` int(11) NOT NULL,
  `galloL` varchar(255) NOT NULL,
  `galloV` varchar(255) NOT NULL,
  `torneoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `peleas`
--

INSERT INTO `peleas` (`ID_Pelea`, `galloL`, `galloV`, `torneoId`) VALUES
(1, '36', '29', 1),
(2, '54', '8', 1),
(3, '10', '37', 1),
(4, '35', '25', 1),
(5, '13', '48', 1),
(6, '16', '2', 1),
(7, '15', '33', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representante`
--

CREATE TABLE `representante` (
  `ID` int(11) NOT NULL,
  `nombreCompleto` varchar(255) NOT NULL,
  `localidad` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `representante`
--

INSERT INTO `representante` (`ID`, `nombreCompleto`, `localidad`) VALUES
(1, 'Familia Espinosa', 'LOJA'),
(2, 'Ocampo - Cobos', 'CARIAMANGA'),
(3, 'La Sociedad', 'LOJA'),
(4, 'Edgar Rivera', 'SANTO DOMINGO'),
(5, 'Mauricio Orobio', 'CUENCA'),
(6, 'Alex Eras', 'USA'),
(7, 'Fabrizio Iñiguez', 'QUINARA'),
(8, 'Luis Consa', 'Malacatos'),
(9, 'Hnos. Valdivieso', 'LOJA'),
(10, 'Nestor Celi', 'LOJA'),
(11, 'Stalin Armijos', 'QUINARA'),
(12, 'William Rosales', 'USA'),
(13, 'Pichu Huamán', 'PERÚ'),
(14, 'Efraín Samaniego', 'LOJA'),
(15, 'Alí Samaniego', 'LOJA'),
(16, 'John Castillo', 'CARIAMANGA'),
(17, 'Francisco Chuquimarca', 'CARIAMANGA'),
(18, 'Anibal Carrión', 'LOJA'),
(19, 'La suciedad', 'LOJA'),
(20, 'Mauricio Orobio', 'CUENCA'),
(21, 'juan piguave', 'CARIAMANGA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipotorneo`
--

CREATE TABLE `tipotorneo` (
  `ID` int(11) NOT NULL,
  `nacional` varchar(255) NOT NULL,
  `provincial` varchar(255) NOT NULL,
  `local` varchar(255) NOT NULL,
  `abierto` varchar(255) NOT NULL,
  `prueba` varchar(255) NOT NULL,
  `torneoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

CREATE TABLE `tipousuario` (
  `ID` int(11) NOT NULL,
  `administrador` varchar(255) NOT NULL,
  `operador` varchar(255) NOT NULL,
  `visitante` varchar(255) NOT NULL,
  `usuarioId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneos`
--

CREATE TABLE `torneos` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `tipoTorneo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `torneos`
--

INSERT INTO `torneos` (`ID`, `nombre`, `fecha_inicio`, `fecha_fin`, `tipoTorneo`) VALUES
(1, 'PRIMERA FECHA CAMPEONATO PROVINCIAL 2023', '2023-01-28', '0000-00-00', 'Provincial'),
(2, 'TORNEO NACIONAL 2023', '2023-07-05', '0000-00-00', 'Nacional');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `apodo` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `empresa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `nombre`, `correo`, `apodo`, `clave`, `empresa`) VALUES
(3, 'Robert Ocampo Ocampo', 'raocampo@gamil.com', 'raocampo', '9654', 'CorpSimtelec '),
(5, 'Administrador', 'administrador@gamil.com', 'admin', 'admin', 'ASOGAL');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `coteja`
--
ALTER TABLE `coteja`
  ADD PRIMARY KEY (`ID_Coteja`),
  ADD KEY `torneoId` (`torneoId`);

--
-- Indices de la tabla `cotejaaut`
--
ALTER TABLE `cotejaaut`
  ADD PRIMARY KEY (`ID_Coteja1`),
  ADD KEY `torneoId` (`torneoId`);

--
-- Indices de la tabla `exclusiones`
--
ALTER TABLE `exclusiones`
  ADD PRIMARY KEY (`IdExclusion`),
  ADD KEY `torneoId` (`torneoId`);

--
-- Indices de la tabla `familias`
--
ALTER TABLE `familias`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `representanteId` (`representanteId`);

--
-- Indices de la tabla `gallos`
--
ALTER TABLE `gallos`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `anillo` (`anillo`),
  ADD KEY `familiasId` (`familiasId`,`representanteId`),
  ADD KEY `representanteId` (`representanteId`),
  ADD KEY `torneoId` (`torneoId`);

--
-- Indices de la tabla `peleas`
--
ALTER TABLE `peleas`
  ADD PRIMARY KEY (`ID_Pelea`),
  ADD KEY `torneoId` (`torneoId`);

--
-- Indices de la tabla `representante`
--
ALTER TABLE `representante`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `tipotorneo`
--
ALTER TABLE `tipotorneo`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `usuarioId` (`usuarioId`);

--
-- Indices de la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `coteja`
--
ALTER TABLE `coteja`
  MODIFY `ID_Coteja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cotejaaut`
--
ALTER TABLE `cotejaaut`
  MODIFY `ID_Coteja1` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `exclusiones`
--
ALTER TABLE `exclusiones`
  MODIFY `IdExclusion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `familias`
--
ALTER TABLE `familias`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `gallos`
--
ALTER TABLE `gallos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `peleas`
--
ALTER TABLE `peleas`
  MODIFY `ID_Pelea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `representante`
--
ALTER TABLE `representante`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tipotorneo`
--
ALTER TABLE `tipotorneo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `torneos`
--
ALTER TABLE `torneos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `coteja`
--
ALTER TABLE `coteja`
  ADD CONSTRAINT `coteja_ibfk_1` FOREIGN KEY (`torneoId`) REFERENCES `torneos` (`ID`);

--
-- Filtros para la tabla `cotejaaut`
--
ALTER TABLE `cotejaaut`
  ADD CONSTRAINT `cotejaaut_ibfk_1` FOREIGN KEY (`torneoId`) REFERENCES `torneos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `exclusiones`
--
ALTER TABLE `exclusiones`
  ADD CONSTRAINT `exclusiones_ibfk_1` FOREIGN KEY (`torneoId`) REFERENCES `torneos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `familias`
--
ALTER TABLE `familias`
  ADD CONSTRAINT `familias_ibfk_1` FOREIGN KEY (`representanteId`) REFERENCES `representante` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `gallos`
--
ALTER TABLE `gallos`
  ADD CONSTRAINT `gallos_ibfk_1` FOREIGN KEY (`familiasId`) REFERENCES `familias` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gallos_ibfk_2` FOREIGN KEY (`representanteId`) REFERENCES `representante` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gallos_ibfk_3` FOREIGN KEY (`torneoId`) REFERENCES `torneos` (`ID`);

--
-- Filtros para la tabla `peleas`
--
ALTER TABLE `peleas`
  ADD CONSTRAINT `peleas_ibfk_1` FOREIGN KEY (`torneoId`) REFERENCES `torneos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
