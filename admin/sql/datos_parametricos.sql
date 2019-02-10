-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 12-12-2018 a las 11:49:00
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
-- Estructura de tabla para la tabla `datos_parametricos`
--

CREATE TABLE `datos_parametricos` (
  `id_dato_parametrico` int(11) NOT NULL,
  `id_categoria_parametrica` int(11) NOT NULL,
  `id_dato_parametrico_padre` int(11) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `valor` varchar(64) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `datos_parametricos`
--

INSERT INTO `datos_parametricos` (`id_dato_parametrico`, `id_categoria_parametrica`, `id_dato_parametrico_padre`, `nombre`, `valor`, `estado`) VALUES
(76, 1, NULL, 'Area 1 a definir por FD', NULL, 1),
(77, 1, NULL, 'Area 2 a definir por FD', NULL, 1),
(78, 2, NULL, 'Unidad 1', '', 1),
(79, 3, NULL, 'ANTE-PROYECTO', '', 1),
(80, 3, NULL, 'EN CURSO', '', 1),
(81, 3, NULL, 'FINALIZADO', '', 1),
(82, 4, NULL, 'Tipo 1', '', 1),
(83, 5, NULL, 'P.O. N° UNO', '', 1),
(84, 3, NULL, 'ELIMINADO', '', 1),
(85, 6, NULL, 'ESTANDARD', '', 1),
(86, 7, NULL, 'Vacaciones', NULL, 1),
(87, 7, NULL, 'Enfermedad', NULL, 1),
(88, 7, NULL, 'Trámites - Otros', '', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `datos_parametricos`
--
ALTER TABLE `datos_parametricos`
  ADD PRIMARY KEY (`id_dato_parametrico`),
  ADD KEY `fk_dato_parametrico_categoria` (`id_categoria_parametrica`),
  ADD KEY `fk_dato_parametrico_padre` (`id_dato_parametrico_padre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `datos_parametricos`
--
ALTER TABLE `datos_parametricos`
  MODIFY `id_dato_parametrico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `datos_parametricos`
--
ALTER TABLE `datos_parametricos`
  ADD CONSTRAINT `fk_dato_parametrico_categoria` FOREIGN KEY (`id_categoria_parametrica`) REFERENCES `categorias_parametricas` (`id_categoria_parametrica`),
  ADD CONSTRAINT `fk_dato_parametrico_padre` FOREIGN KEY (`id_dato_parametrico_padre`) REFERENCES `datos_parametricos` (`id_dato_parametrico`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
