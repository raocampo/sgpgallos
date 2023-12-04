-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-07-2023 a las 01:30:12
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `exclusiones`
--

CREATE TABLE `exclusiones` (
  `IdExclusion` int(11) NOT NULL,
  `nombreFamiliaUno` varchar(255) NOT NULL,
  `nombreFamiliaDos` varchar(255) NOT NULL,
  `torneoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familias`
--

CREATE TABLE `familias` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `localidad` varchar(255) NOT NULL,
  `representanteId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(14, 'LA ZARZA', 'LOJA', 1),
(15, 'JP', 'CARIAMANGA', 16),
(16, 'CHUQUIMARCA', 'CARIAMANGA', 17),
(17, 'EL CASCAJO', 'LOJA', 18),
(18, 'HNOS. PARDO', 'CARIAMANGA', 23),
(19, 'SAN CARLOS', 'CARIAMANGA', 24),
(20, 'SUQUINDA GALLOS', 'LOJA', 25),
(21, 'CUEVAS III', 'CARIAMANGA', 26),
(22, 'PINDAL', 'PINDAL', 27),
(23, 'LNS NAMICELA', 'LA ELVIRA', 28),
(24, 'TOLEDO - MORALES', 'VILCABAMBA', 29),
(25, 'MA. DE LOS ANGELES', 'LOJA', 30),
(26, 'EL AGRADO', 'LOJA', 31),
(27, 'EL PRADO', 'LOJA', 32),
(28, 'LOS PARCES', 'LOJA', 33),
(29, 'JM', 'LOJA', 34),
(30, 'LA TERRAZA', 'LOJA', 35),
(31, 'CASTA DE ORO XXX', 'USA', 2);

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
  `frente` varchar(255) NOT NULL,
  `familiasId` int(2) NOT NULL,
  `representanteId` int(11) NOT NULL,
  `torneoId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gallos`
--

INSERT INTO `gallos` (`ID`, `anillo`, `pesoReal`, `tamañoReal`, `placa`, `nacimiento`, `frente`, `familiasId`, `representanteId`, `torneoId`) VALUES
(2, '6811', 3.09, 85.10, '', '', '1', 2, 19, 1),
(3, '6812', 3.10, 81.10, '', '', '1', 2, 19, 1),
(5, '6809', 3.15, 86.40, '', '', '2', 3, 4, 1),
(6, '6810', 3.14, 84.10, '', '', '2', 3, 4, 1),
(7, '6807', 4.01, 85.90, '', '', '1', 3, 4, 1),
(8, '6808', 3.06, 84.80, '', '', '1', 3, 4, 1),
(9, '6805', 3.13, 83.40, '', '', '2', 4, 5, 1),
(10, '6806', 3.07, 78.80, '', '', '2', 4, 5, 1),
(11, '6803', 4.02, 89.40, '', '', '1', 4, 5, 1),
(12, '6804', 4.07, 91.50, '', '', '1', 4, 5, 1),
(13, '6801', 3.08, 83.70, '', '', '1', 5, 6, 1),
(14, '6802', 3.11, 89.70, '', '', '1', 5, 6, 1),
(15, '6799', 3.08, 85.60, '', '', '2', 5, 7, 1),
(16, '6800', 3.08, 84.70, '', '', '2', 5, 7, 1),
(17, '6797', 4.06, 96.00, '', '', '1', 5, 7, 1),
(18, '6798', 4.00, 86.50, '', '', '1', 5, 7, 1),
(19, '6795', 4.02, 92.80, '', '', '1', 6, 8, 1),
(20, '6796', 3.09, 86.60, '', '', '1', 6, 8, 1),
(22, '6793', 3.15, 94.30, '', '', '2', 7, 9, 1),
(23, '6794', 3.11, 88.70, '', '', '2', 7, 9, 1),
(24, '6791', 3.14, 93.30, '', '', '1', 7, 9, 1),
(25, '6792', 3.07, 83.80, '', '', '1', 7, 9, 1),
(26, '6789', 3.12, 82.60, '', '', '1', 8, 10, 1),
(27, '6790', 3.12, 92.10, '', '', '1', 8, 10, 1),
(28, '6787', 4.02, 90.00, '', '', '1', 5, 11, 1),
(29, '6788', 3.03, 83.70, '', '', '1', 5, 11, 1),
(30, '6785', 3.14, 83.90, '', '', '2', 10, 12, 1),
(31, '6786', 3.10, 81.40, '', '', '2', 10, 12, 1),
(32, '6783', 3.09, 88.60, '', '', '1', 10, 12, 1),
(33, '6784', 3.09, 85.70, '', '', '1', 10, 12, 1),
(34, '6781', 3.15, 89.50, '', '', '3', 11, 13, 1),
(35, '6782', 3.07, 81.10, '', '', '3', 11, 13, 1),
(36, '6779', 3.02, 81.50, '', '', '2', 11, 13, 1),
(37, '6780', 3.07, 80.00, '', '', '2', 11, 13, 1),
(38, '6777', 4.01, 89.00, '', '', '1', 11, 13, 1),
(39, '6778', 3.14, 84.80, '', '', '1', 11, 13, 1),
(40, '6775', 4.02, 88.40, '', '', '1', 12, 14, 1),
(41, '6776', 4.04, 91.60, '', '', '1', 12, 14, 1),
(42, '6773', 3.10, 89.40, '', '', '1', 12, 15, 1),
(43, '6774', 4.00, 87.70, '', '', '1', 12, 15, 1),
(44, '6771', 3.14, 76.80, '', '', '3', 14, 1, 1),
(45, '6772', 3.13, 82.50, '', '', '3A', 1, 1, 1),
(46, '6769', 3.11, 81.30, '', '', '2', 1, 1, 1),
(47, '6770', 3.10, 84.50, '', '', '2', 1, 1, 1),
(48, '6767', 3.09, 83.10, '', '', '1', 1, 1, 1),
(49, '6768', 3.11, 84.60, '', '', '1', 1, 1, 1),
(50, '6766', 4.02, 85.40, '', '', '1', 15, 16, 1),
(51, '6765', 3.12, 91.10, '', '', '1', 15, 16, 1),
(52, '6764', 4.02, 83.40, '', '', '1', 16, 17, 1),
(53, '6763', 3.10, 85.90, '', '', '1', 16, 17, 1),
(54, '6762', 3.04, 91.70, '', '', '1', 17, 18, 1),
(55, '6761', 4.02, 91.40, '', '', '1', 17, 18, 1),
(87, '6448', 4.01, 86.00, '', '', '1', 12, 15, 2),
(88, '6449', 3.08, 81.10, '', '', '1', 12, 15, 2),
(89, '6473', 3.15, 87.80, '', '', '1', 12, 14, 2),
(90, '6474', 3.11, 86.10, '', '', '1', 12, 14, 2),
(91, '6475', 4.03, 87.60, '', '', '1', 12, 22, 2),
(92, '6476', 4.04, 86.80, '', '', '1', 12, 22, 2),
(93, '6477', 3.08, 82.00, '', '', '1', 18, 23, 2),
(94, '6478', 3.13, 82.40, '', '', '1', 18, 23, 2),
(95, '6479', 3.08, 83.40, '', '', '1', 18, 23, 2),
(96, '6480', 3.03, 79.30, '', '', '1', 18, 23, 2),
(97, '6481', 3.08, 86.80, '', '', '1', 19, 24, 2),
(98, '6482', 3.11, 82.90, '', '', '1', 19, 24, 2),
(99, '6483', 4.07, 92.10, '', '', '3', 20, 25, 2),
(100, '6484', 3.11, 82.10, '', '', '3', 20, 25, 2),
(101, '6485', 4.06, 89.30, '', '', '2', 20, 25, 2),
(102, '6486', 4.00, 88.50, '', '', '2', 20, 25, 2),
(103, '6487', 4.03, 92.50, '', '', '2', 31, 2, 2),
(104, '6488', 4.00, 94.60, '', '', '2', 31, 2, 2),
(105, '6489', 4.00, 89.40, '', '', '3', 31, 2, 2),
(106, '6490', 3.09, 83.50, '', '', '3', 31, 2, 2),
(107, '6491', 3.09, 83.70, '', '', '2', 21, 26, 2),
(108, '6492', 3.13, 86.80, '', '', '2', 21, 26, 2),
(109, '6493', 3.09, 82.10, '', '', '1', 21, 26, 2),
(110, '6494', 3.12, 87.90, '', '', '1', 21, 26, 2),
(111, '6495', 3.06, 86.40, '', '', '1', 5, 6, 2),
(112, '6496', 3.11, 89.00, '', '', '1', 9, 6, 2),
(113, '6497', 3.04, 83.80, '', '', '1', 22, 27, 2),
(114, '6498', 3.07, 86.70, '', '', '1', 22, 27, 2),
(115, '6601', 4.02, 92.40, '', '', '2', 23, 28, 2),
(116, '6602', 3.14, 87.80, '', '', '2', 23, 28, 2),
(117, '6603', 4.00, 86.30, '', '', '1', 23, 28, 2),
(118, '6604', 3.13, 92.70, '', '', '1', 23, 28, 2),
(119, '6605', 4.02, 91.50, '', '', '1', 24, 29, 2),
(120, '6606', 3.07, 90.20, '', '', '1', 24, 29, 2),
(121, '6607', 4.02, 83.70, '', '', '3', 11, 13, 2),
(122, '6608', 3.14, 91.80, '', '', '3', 11, 13, 2),
(123, '6610', 4.02, 87.20, '', '', '2', 11, 13, 2),
(124, '6611', 4.07, 87.10, '', '', '2', 11, 13, 2),
(125, '6612', 4.04, 92.50, '', '', '1', 11, 13, 2),
(126, '6613', 4.00, 81.70, '', '', '1', 11, 13, 2),
(127, '6614', 3.13, 87.70, '', '', '1', 17, 18, 2),
(128, '6615', 3.13, 84.90, '', '', '1', 17, 18, 2),
(129, '6616', 3.14, 90.80, '', '', '2', 17, 18, 2),
(130, '6617', 3.10, 85.30, '', '', '2', 17, 18, 2),
(131, '6618', 4.01, 91.50, '', '', '3', 25, 30, 2),
(132, '6619', 4.02, 89.10, '', '', '3', 25, 30, 2),
(133, '6621', 4.06, 94.50, '', '', '2', 25, 30, 2),
(134, '6622', 3.10, 90.80, '', '', '2', 25, 30, 2),
(135, '6623', 4.06, 89.20, '', '', '1', 26, 31, 2),
(136, '6624', 4.10, 90.50, '', '', '1', 26, 31, 2),
(137, '6625', 3.13, 89.70, '', '', '1', 1, 1, 2),
(138, '6626', 4.03, 82.60, '', '', '1', 1, 1, 2),
(139, '6627', 4.09, 92.70, '', '', '3', 1, 1, 2),
(140, '6628', 4.04, 88.60, '', '', '3', 1, 1, 2),
(141, '6629', 4.01, 86.60, '', '', '2', 8, 10, 2),
(142, '6630', 3.15, 83.30, '', '', '2', 8, 10, 2),
(143, '6631', 3.09, 85.20, '', '', '1', 8, 10, 2),
(144, '6632', 3.07, 86.10, '', '', '1', 8, 10, 2),
(145, '6633', 3.14, 89.20, '', '', '1', 7, 9, 2),
(146, '6634', 3.12, 92.40, '', '', '1', 7, 9, 2),
(147, '6635', 3.13, 89.50, '', '', '2', 7, 9, 2),
(148, '6636', 3.10, 91.20, '', '', '2', 7, 9, 2),
(149, '6637', 3.12, 97.60, '', '', '6', 7, 9, 2),
(150, '6638', 4.08, 99.99, '', '', '6', 7, 9, 2),
(151, '6639', 3.09, 87.60, '', '', '1', 27, 32, 2),
(152, '6640', 3.09, 88.60, '', '', '1', 27, 32, 2),
(153, '6641', 3.13, 86.90, '', '', '1', 28, 33, 2),
(154, '6642', 3.14, 81.70, '', '', '1', 28, 33, 2),
(155, '6643', 4.09, 90.80, '', '', '1', 3, 4, 2),
(156, '6644', 4.03, 83.60, '', '', '1', 3, 4, 2),
(157, '6645', 4.03, 86.30, '', '', '1', 3, 4, 2),
(158, '6646', 4.02, 86.10, '', '', '1', 3, 4, 2),
(159, '6647', 3.09, 82.90, '', '', '2', 3, 4, 2),
(160, '6648', 4.01, 80.40, '', '', '2', 3, 4, 2),
(161, '6649', 3.15, 93.80, '', '', '1', 5, 7, 2),
(162, '6650', 4.08, 97.00, '', '', '1', 5, 7, 2),
(163, '6651', 4.06, 98.80, '', '', '1', 29, 34, 2),
(164, '6652', 4.05, 92.40, '', '', '1', 29, 34, 2),
(165, '6653', 4.01, 89.70, '', '', '11', 30, 35, 2),
(166, '6654', 4.00, 85.00, '', '', '11', 30, 35, 2),
(167, '6655', 4.01, 83.60, '', '', '12', 30, 35, 2),
(168, '6656', 3.11, 86.40, '', '', '12', 30, 35, 2),
(169, '6657', 3.15, 85.20, '', '', '13', 30, 35, 2),
(170, '6658', 4.02, 83.60, '', '', '13', 30, 35, 2),
(171, '6659', 4.07, 89.70, '', '', '1', 10, 12, 2),
(172, '6660', 4.00, 90.40, '', '', '1', 10, 12, 2),
(173, '6661', 4.02, 89.20, '', '', '2', 10, 12, 2),
(174, '6662', 3.05, 82.90, '', '', '2', 10, 12, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peleas`
--

CREATE TABLE `peleas` (
  `ID_Pelea` int(11) NOT NULL,
  `galloL` varchar(255) NOT NULL,
  `galloV` varchar(255) NOT NULL,
  `torneoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representante`
--

CREATE TABLE `representante` (
  `ID` int(11) NOT NULL,
  `nombreCompleto` varchar(255) NOT NULL,
  `localidad` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(21, 'juan piguave', 'CARIAMANGA'),
(22, 'La Sureñita', ''),
(23, 'Luis Pardo', ''),
(24, 'Soto - Pardo', ''),
(25, 'Milton y Pablo Bermeo', ''),
(26, 'Familia Cueva', ''),
(27, 'Fernando Valdivieso', ''),
(28, 'Leo Namicela', ''),
(29, 'Toledo - Morales', ''),
(30, 'Galo Escudero', ''),
(31, 'Roberto Ochoa', ''),
(32, 'Carlos Yaguana', ''),
(33, 'Jairo Danilo', ''),
(34, 'Freddy Yamunaque', ''),
(35, 'Julio Narvaez', '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `torneos`
--

INSERT INTO `torneos` (`ID`, `nombre`, `fecha_inicio`, `fecha_fin`, `tipoTorneo`) VALUES
(1, 'PRIMERA FECHA CAMPEONATO PROVINCIAL 2023', '2023-01-28', '0000-00-00', 'Provincial'),
(2, 'TERCERA FECHA CAMPEONATO PROVINCIAL 2022', '2022-07-16', '0000-00-00', 'Provincial');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `ID_Coteja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cotejaaut`
--
ALTER TABLE `cotejaaut`
  MODIFY `ID_Coteja1` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `exclusiones`
--
ALTER TABLE `exclusiones`
  MODIFY `IdExclusion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `familias`
--
ALTER TABLE `familias`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `gallos`
--
ALTER TABLE `gallos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT de la tabla `peleas`
--
ALTER TABLE `peleas`
  MODIFY `ID_Pelea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `representante`
--
ALTER TABLE `representante`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
