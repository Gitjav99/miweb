-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 13-09-2025 a las 13:36:21
-- Versión del servidor: 8.0.43
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `NeoVibra`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat`
--

CREATE TABLE `chat` (
  `id` int UNSIGNED NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chat`
--

INSERT INTO `chat` (`id`, `titulo`, `creado_en`, `actualizado_en`) VALUES
(1, 'General', '2025-09-13 13:35:03', '2025-09-13 13:35:03'),
(2, 'Project X', '2025-09-13 13:35:03', '2025-09-13 13:35:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_participante`
--

CREATE TABLE `chat_participante` (
  `chat_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `rol` enum('admin','miembro','invitado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'miembro',
  `invitado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chat_participante`
--

INSERT INTO `chat_participante` (`chat_id`, `usuario_id`, `rol`, `invitado_en`) VALUES
(1, 1, 'admin', '2025-09-12 09:17:00'),
(1, 2, 'miembro', '2025-09-12 09:17:05'),
(1, 3, 'invitado', '2025-09-12 09:17:10'),
(1, 4, 'invitado', '2025-09-12 09:17:15'),
(2, 1, 'admin', '2025-09-12 09:18:00'),
(2, 2, 'miembro', '2025-09-12 09:18:05'),
(2, 5, 'invitado', '2025-09-12 09:18:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

CREATE TABLE `mensaje` (
  `id` int UNSIGNED NOT NULL,
  `chat_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `enviado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensaje`
--

INSERT INTO `mensaje` (`id`, `chat_id`, `usuario_id`, `contenido`, `enviado_en`) VALUES
(1, 1, 1, '¡Hola a todos! Bienvenidos al chat general.', '2025-09-12 09:20:00'),
(2, 1, 2, '¡Hola Alice! Yo también estoy listo.', '2025-09-12 09:20:30'),
(3, 1, 3, '¿Qué se habla en este chat?', '2025-09-12 09:21:00'),
(4, 1, 4, 'Yo me uno cuando tenga tiempo.', '2025-09-12 09:21:30'),
(5, 2, 1, 'Equipo, la reunión de mañana está programada a las 10:00 AM.', '2025-09-12 09:25:00'),
(6, 2, 2, 'Recibido. Traeré el informe de ventas.', '2025-09-12 09:25:30'),
(7, 2, 5, '¿Puedo unirme a la llamada?', '2025-09-12 09:26:00'),
(8, 2, 1, 'Sí, claro. Te añadiremos a la lista.', '2025-09-12 09:26:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_perfil` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `configuracion` json DEFAULT NULL,
  `ultima_conexion` datetime DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `foto_perfil`, `configuracion`, `ultima_conexion`, `password_hash`, `is_active`) VALUES
(1, 'Alice', 'https://example.com/profiles/alice.jpg', '{\"theme\": \"dark\", \"notifications\": true}', '2025-09-12 09:15:42', '$2y$10$abcdefghijklmnopqrstuv', 1),
(2, 'Bob', 'https://example.com/profiles/bob.png', '{\"theme\": \"light\", \"notifications\": false}', '2025-09-12 10:02:18', '$2y$10$abcdefghijklmnopqrstuv', 1),
(3, 'Carol', NULL, '{\"theme\": \"dark\", \"notifications\": true}', '2025-09-10 14:20:00', '$2y$10$abcdefghijklmnopqrstuv', 1),
(4, 'Dave', 'https://example.com/profiles/dave.gif', '{\"theme\": \"dark\", \"notifications\": false}', '2025-09-09 08:45:30', '$2y$10$abcdefghijklmnopqrstuv', 1),
(5, 'Eve', NULL, '{\"theme\": \"light\", \"notifications\": true}', NULL, '$2y$10$abcdefghijklmnopqrstuv', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creado_en` (`creado_en`);

--
-- Indices de la tabla `chat_participante`
--
ALTER TABLE `chat_participante`
  ADD PRIMARY KEY (`chat_id`,`usuario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `enviado_en` (`enviado_en`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `chat_participante`
--
ALTER TABLE `chat_participante`
  ADD CONSTRAINT `fk_chat_participante_chat` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_participante_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD CONSTRAINT `fk_mensaje_chat` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mensaje_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
