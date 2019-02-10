-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 12-12-2018 a las 11:24:17
-- Versión del servidor: 10.1.36-MariaDB-cll-lve
-- Versión de PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `intwayc1_gestionproyectos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `codigo_interno` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_update` datetime DEFAULT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_sucursal`, `id_rol`, `codigo_interno`, `email`, `nombre`, `password`, `fecha_creacion`, `fecha_update`, `estado`) VALUES
(1, 1, 1, 167485563, 'test@test.com', 'test', 'ÿØ=ŸÕVi', '2017-12-24 10:47:16', '2018-10-15 00:00:00', 1),
(7, 1, 1, 175623466, 'roropeza@funciondigital.com', 'RAFAEL OROPEZA', 'ÿ{m—g6†\ZQÿÎLt$ç', '2018-08-31 00:00:00', '2018-10-12 00:00:00', 0),
(8, 1, 1, 161073851, 'ventas@funciondigital.com', 'OTTO GONZALEZ', 'ÿ…öÁ1¤’n', '2018-08-31 00:00:00', '2018-10-12 00:00:00', 0),
(9, 1, 3, 153172089, 'operq@test.com', 'Operario 1', 'ÿVoo;ùo', '2018-09-06 00:00:00', '2018-10-25 00:00:00', 0),
(10, 1, 3, 120111813, 'operario2@test.com', 'Operario 2', 'ÿG—S†«±y¥', '2018-09-18 00:00:00', '2018-10-12 00:00:00', 0),
(11, 1, 1, 162179931, 'vdold@funciondigital.com', 'Dold, Valentina', 'ÿ¸aÉÌã[K£[÷éfó®Ì', '2018-10-12 00:00:00', '2018-10-19 00:00:00', 1),
(12, 1, 1, 179423204, 'shuck@funciondigital.com', 'Huck, María Sol', 'ÿ3:ÂiëïR$´ÖŸhá', '2018-10-12 00:00:00', '2018-10-16 00:00:00', 1),
(13, 1, 1, 161607331, 'amancera@funciondigital.com', 'Mancera, Alberto Enrique', 'ÿvÅçd¡u]‡m4ÄúMa', '2018-10-12 00:00:00', '2018-11-08 00:00:00', 1),
(14, 1, 2, 165947349, 'erpaez@funciondigital.com', 'Paez, Ramon Eduardo', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(15, 1, 2, 154914754, 'slopez@funciondigital.com', 'Lopez, Sergio Daniel', 'ÿ¸aÉÌã[K£[÷éfó®Ì', '2018-10-12 00:00:00', '2018-10-26 00:00:00', 1),
(16, 1, 3, 186731730, 'mrivera@funciondigial.com', 'Roberto Rivera, Miguel Angel', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(17, 1, 3, 178914392, 'rfranco@funciondigital.com', 'Franco, Rodrigo', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(18, 1, 3, 144376774, 'pfranco@funciondigital.com', 'Franco, Pablo', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(19, 1, 3, 95140699, 'bpereyra@funciondigital.com', 'Pereyra, Bruno Heriberto', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', '2018-10-25 00:00:00', 0),
(20, 1, 3, 166819904, 'rlencinas@funciondigital.com', 'Lencinas, Jaime Raúl', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(21, 1, 3, 120989053, 'jvallejos@funciondigital.com', 'Vallejos, Jeremías', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(22, 1, 3, 114485553, 'gmereles@funciondigital.com', 'Mereles, Jeremías Gabriel', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(23, 1, 3, 119460613, 'lquisbert@funciondigital.com', 'Quisbert, Luis', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(24, 1, 3, 163846406, 'gimenezfunciondigital@gmail.com', 'Gimenez, Gabriel Maximiliano', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(25, 1, 3, 170850200, 'dsanchez@funciondigital.com', 'Sanchez, Daniel', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(26, 1, 3, 172711783, 'moyanofunciondigital@gmail.com', 'Moyano, Claudio', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(27, 1, 3, 167164788, 'jimenezfunciondigital@gmail.com', 'Jimenez, Daniel', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(28, 1, 3, 149430111, 'erodriguez@funciondigital.com', 'Rodriguez Villanueva, Enzo', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', '2018-11-26 00:00:00', 0),
(29, 1, 1, 155656194, 'servicios@funciondigital.com', 'Garcia Blanca, Afrany ', 'ÿêÆ!²üQ¤È4È\"å§â', '2018-10-12 00:00:00', '2018-11-15 00:00:00', 1),
(30, 1, 1, 139990642, 'agoncalves@funciondigital.com', 'Goncalves, Edgardo Andres', 'ÿ¸aÉÌã[K£[÷éfó®Ì', '2018-10-12 00:00:00', '2018-10-16 00:00:00', 1),
(31, 1, 1, 142984629, 'mpulgar@funciondigital.com', 'Pulgar, Miguel ', 'ÿ¸aÉÌã[K£[÷éfó®Ì', '2018-10-12 00:00:00', '2018-10-16 00:00:00', 1),
(32, 1, 3, 104951226, 'dvert@funciondigital.com', 'Vert, Daniel', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-12 00:00:00', NULL, 1),
(35, 1, 2, 103153369, 'ignaciosc@gmail.com', 'Ignacio Sclar', 'ÿVŒöšðÍ’“µÿ¨ïŽpÐ', '2018-10-15 00:00:00', '2018-10-25 00:00:00', 0),
(36, 1, 3, 42704734, 'ggarcia@funciondigital.com', 'García Gimenez, Gustavo Damián', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-19 00:00:00', '2018-10-19 00:00:00', 1),
(37, 1, 3, 103065871, 'ptrachitte@funciondigital.com', 'Trachitte, Pablo', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-19 00:00:00', NULL, 1),
(38, 1, 3, 14512019, 'IVERT@FUNCIONDIGITAL.COM', 'Vert, Iago', 'ÿ’`ÙmÆ*’„Mæ2ºü8É', '2018-10-22 00:00:00', '2018-10-22 00:00:00', 1),
(41, 1, 3, 56493896, 'bpereya2@funciondigital.com', 'Pereyra, Bruno', 'ÿ{m—g6†\ZQÿÎLt$ç', '2018-10-26 00:00:00', '2018-10-26 00:00:00', 1),
(43, 1, 3, 19292234, 'Jara@gmail.com', 'Mauricio Jara', 'ÿ…öÁ1¤’n', '2018-11-16 00:00:00', NULL, 1),
(44, 1, 3, 50064454, 'Hceballos@gmail.com', 'Humberto Ceballos', 'ÿ…öÁ1¤’n', '2018-11-16 00:00:00', NULL, 1),
(45, 1, 3, 29915763, 'ALEJANDRO@GMAIL.COM', 'Oliveira Alejandro ', 'ÿ…öÁ1¤’n', '2018-11-16 00:00:00', '2018-11-16 00:00:00', 1),
(46, 1, 3, 64108400, 'avillareal@funciondigital.com', 'Villareal Ariel', 'ÿ{m—g6†\ZQÿÎLt$ç', '2018-11-26 00:00:00', NULL, 1),
(47, 1, 3, 73254088, 'oluna@funciondigital.com', 'Luna, Oscar', 'ÿ{m—g6†\ZQÿÎLt$ç', '2018-12-06 00:00:00', NULL, 1),
(48, 1, 3, 41864638, 'wavila@funciondigital.com', 'Ávila, Walter', 'ÿ{m—g6†\ZQÿÎLt$ç', '2018-12-06 00:00:00', NULL, 1),
(49, 1, 3, 109074663, 'bavila@funciondigital.com', 'Ávila, Brian', 'ÿ{m—g6†\ZQÿÎLt$ç', '2018-12-06 00:00:00', NULL, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `codigo_interno` (`codigo_interno`),
  ADD KEY `FK_usuario_sucural` (`id_sucursal`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `FK_usuario_sucural` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
