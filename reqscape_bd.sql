create database reqscape_db;

use reqscape_db;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-08-2024 a las 22:07:10
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `reqscape_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `palabras`
--

CREATE TABLE `palabras` (
  `id` int(11) NOT NULL,
  `requirements_id` int(11) DEFAULT NULL,
  `palabra` varchar(50) NOT NULL,
  `orden` int(11) DEFAULT NULL,
  `requirements_correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `passwords`
--

CREATE TABLE `passwords` (
  `id` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `token` varchar(200) NOT NULL,
  `codigo` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `passwords`
--

INSERT INTO `passwords` (`id`, `correo`, `token`, `codigo`, `fecha`) VALUES
(0, 'bethsaidach@gmail.com', 'eef6d47daf', 900932, '2024-07-24 19:13:00'),
(0, 'bethsaidach@gmail.com', '5141ebf203', 745191, '2024-07-24 19:16:59'),
(0, 'bethsaidach@gmail.com', '81cacb553f', 295843, '2024-07-24 19:25:56'),
(0, 'bethsaidach@gmail.com', 'ad8374322b', 156440, '2024-07-24 19:27:49'),
(0, 'bethsaidach@gmail.com', '426fc5ab86', 3043, '2024-07-24 19:40:59'),
(0, 'bethsaidach@gmail.com', 'ef1dc8fdcc', 8290, '2024-07-24 19:47:52'),
(0, 'bethsaidach@gmail.com', '94ca1b0d71', 4987, '2024-07-24 20:06:03'),
(0, 'bethsaidach@gmail.com', '916d648aa6', 1662, '2024-07-24 20:12:34'),
(0, 'bethsaidach@gmail.com', '68c19cf6d7', 7685, '2024-07-24 20:22:37'),
(0, 'bethsaidach@gmail.com', '2eb147c327', 3602, '2024-07-24 20:26:22'),
(0, 'bethsaidach@gmail.com', '8a3d6631ad', 4436, '2024-07-24 20:36:48'),
(0, 'bethsaidach@gmail.com', '6005c495f6', 4763, '2024-07-24 20:50:59'),
(0, 'bethsaidach@gmail.com', '74baa6efe4', 6881, '2024-07-24 20:52:32'),
(0, 'bethsaidach@gmail.com', 'a280b1c758', 2921, '2024-07-24 20:56:27'),
(0, 'bethsaidach@gmail.com', '51958985a7', 8642, '2024-07-24 20:58:20'),
(0, 'bethsaidach@gmail.com', '7b194461af', 2695, '2024-07-24 21:06:36'),
(0, 'bethsaidach@gmail.com', '73f35a06ea', 7395, '2024-07-24 21:44:26'),
(0, 'bethsaidach@gmail.com', '7bf75418ce', 2134, '2024-07-24 23:35:23'),
(0, 'bethsaidach@gmail.com', '056b2759e1', 2217, '2024-08-05 19:43:58'),
(0, 'bethsaidach@gmail.com', 'bbe5ee4843', 4946, '2024-08-05 19:44:01'),
(0, 'bethsaidach@gmail.com', '016a075d3b', 1544, '2024-08-05 19:47:05'),
(0, 'bethsaidach@gmail.com', 'f06435de59', 7410, '2024-08-05 19:47:08'),
(0, 'bethsaidach@gmail.com', 'dc8add6719', 9169, '2024-08-05 19:48:36'),
(0, 'bethsaidach@gmail.com', '3c52c89476', 1636, '2024-08-05 19:48:45'),
(0, 'bethsaidach@gmail.com', 'eaaf207220', 3706, '2024-08-05 19:48:58'),
(0, 'bethsaidach@gmail.com', 'eb85b3c18c', 6056, '2024-08-05 19:53:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntajes`
--

CREATE TABLE `puntajes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `puntaje` int(11) DEFAULT NULL,
  `nivel` int(11) NOT NULL DEFAULT 1,
  `room_code` varchar(10) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requirements`
--

CREATE TABLE `requirements` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_ambiguous` tinyint(1) NOT NULL,
  `retro` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requirements_2`
--

CREATE TABLE `requirements_2` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `room_requirements`
--

CREATE TABLE `room_requirements` (
  `id` int(11) NOT NULL,
  `room_code` varchar(10) NOT NULL,
  `requirement_id` int(11) DEFAULT NULL,
  `nivel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `room_requirements1`
--

CREATE TABLE `room_requirements1` (
  `id` int(11) NOT NULL,
  `room_code` varchar(10) NOT NULL,
  `requirement_id` int(11) DEFAULT NULL,
  `nivel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `imagen_perfil` varchar(255) DEFAULT NULL,
  `perfil` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `correo`, `contrasena`, `imagen_perfil`, `perfil`) VALUES
(1, 'Bethsaida', 'bethsaidach@gmail.com', '$2y$10$504jZneB/G.SjptyyI46pO0OjGqxLW6w.3lQgXmVOVUkAT8aS.0h.', 'uploads/Diseño sin título (28).png', 'estudiante'),
(2, 'Beth', 'bethsaida.cheam@ug.edu.ec', '$2y$10$OBwJFmuARrWqm1CZjp7yLuwbXmyj0aFXukvcNc7ytX1Ya7QrANoTO', 'uploads/2620507.png', 'profesor'),
(3, 'Eli', 'bethsaidachea@gmail.com', '$2y$10$q4Vg97A8ebSO.oWsKu8Hmecp0S.u9FASANswv2UGFwSsnW071IWQK', NULL, 'estudiante'),
(4, 'Elizabeth', 'bethsaidach@outlook.com', '$2y$10$d1/80nSnd7VyvUZA6Aqx/udfOi872oCup5tzg81vj0T5H7dKIvb6m', NULL, 'estudiante');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `palabras`
--
ALTER TABLE `palabras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requirements_id` (`requirements_id`);

--
-- Indices de la tabla `puntajes`
--
ALTER TABLE `puntajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `requirements_2`
--
ALTER TABLE `requirements_2`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `room_requirements`
--
ALTER TABLE `room_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requirement_id` (`requirement_id`);

--
-- Indices de la tabla `room_requirements1`
--
ALTER TABLE `room_requirements1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_room_req` (`room_code`,`requirement_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `palabras`
--
ALTER TABLE `palabras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `puntajes`
--
ALTER TABLE `puntajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `requirements`
--
ALTER TABLE `requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `requirements_2`
--
ALTER TABLE `requirements_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `room_requirements`
--
ALTER TABLE `room_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `room_requirements1`
--
ALTER TABLE `room_requirements1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `palabras`
--
ALTER TABLE `palabras`
  ADD CONSTRAINT `palabras_ibfk_1` FOREIGN KEY (`requirements_id`) REFERENCES `requirements_2` (`id`);

--
-- Filtros para la tabla `puntajes`
--
ALTER TABLE `puntajes`
  ADD CONSTRAINT `puntajes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `room_requirements`
--
ALTER TABLE `room_requirements`
  ADD CONSTRAINT `room_requirements_ibfk_1` FOREIGN KEY (`requirement_id`) REFERENCES `requirements_2` (`id`);
COMMIT;

ALTER TABLE palabras MODIFY palabra VARCHAR(255);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
