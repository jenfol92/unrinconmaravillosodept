-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-05-2026 a las 14:48:52
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
-- Base de datos: `tienda_recursos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas_seguridad`
--

CREATE TABLE `alertas_seguridad` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alertas_seguridad`
--

INSERT INTO `alertas_seguridad` (`id`, `usuario_id`, `tipo`, `mensaje`, `ip`, `user_agent`, `fecha`, `estado`) VALUES
(6, 4, 'acceso_no_reconocido', 'El usuario indica que no reconoce el acceso actual a su cuenta.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 11:03:19', 'pendiente'),
(7, 4, 'acceso_no_reconocido', 'El usuario indica que no reconoce el acceso actual a su cuenta.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 11:10:32', 'pendiente'),
(8, 4, 'acceso_no_reconocido', 'El usuario indica que no reconoce el acceso actual a su cuenta.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 13:10:35', 'pendiente'),
(9, 4, 'acceso_no_reconocido', 'El usuario indica que no reconoce el acceso actual a su cuenta.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 13:35:33', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha_agregado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(3, 'Conocimiento del Medio'),
(4, 'Dossiers Especiales'),
(1, 'Lengua'),
(2, 'Matematicas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_gratuitas`
--

CREATE TABLE `categorias_gratuitas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_gratuitas`
--

INSERT INTO `categorias_gratuitas` (`id`, `nombre`, `activa`) VALUES
(1, 'Horarios visuales', 1),
(2, 'Pictogramas de secuencia', 1),
(3, 'Paneles visuales de actividades', 1),
(4, 'Llavero conductas y anticipación', 1),
(5, 'Paso a paso de actividades', 1),
(6, 'Apoyo visual en mesa', 1),
(7, 'Lengua', 1),
(8, 'Juegos', 1),
(9, 'Conocimiento del medio', 1),
(10, 'Matemáticas', 1),
(11, 'Raquel', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_mensajes`
--

CREATE TABLE `contacto_mensajes` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(180) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contacto_mensajes`
--

INSERT INTO `contacto_mensajes` (`id`, `producto_id`, `nombre`, `email`, `asunto`, `mensaje`, `leido`, `fecha`) VALUES
(1, NULL, 'Alujenn', 'jenfolalc@alu.edu.gva.es', 'vffdnch', 'bgfcmyjtydffgd', 0, '2026-05-29 13:31:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descargas`
--

CREATE TABLE `descargas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `token_descarga` varchar(64) NOT NULL,
  `fecha_compra` datetime DEFAULT current_timestamp(),
  `fecha_expiracion` datetime DEFAULT NULL,
  `numero_descargas` int(11) DEFAULT 0,
  `max_descargas` int(11) DEFAULT 5,
  `archivo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `descargas`
--

INSERT INTO `descargas` (`id`, `usuario_id`, `producto_id`, `pedido_id`, `token_descarga`, `fecha_compra`, `fecha_expiracion`, `numero_descargas`, `max_descargas`, `archivo_path`) VALUES
(1, 4, 2, NULL, '62a6d401-4f98-11f1-a647-04d9f5cb71dc', '2026-05-14 15:25:35', NULL, 0, 5, 'recursos/prueba.pdf'),
(3, 4, 2, NULL, 'c29b5a1b4f2f4b374b6b20c7ba3b880af330c75b8fefb28853a11ebba78aa3e5', '2026-05-20 16:25:03', '2027-05-20 16:25:03', 0, 5, NULL),
(4, 4, 65, NULL, '87105eccb8cdb972d4109aa47ff2df21328b3cf98fa9279c74e89ab54d466f85', '2026-05-20 16:30:31', '2027-05-20 16:30:31', 5, 5, NULL),
(5, 4, 5, NULL, '9a680e2c14d6098af6fed967f854d20b074f47e03f10b54b59d7903f543aea11', '2026-05-18 11:02:45', '2027-05-18 11:02:45', 0, 5, NULL),
(6, 4, 4, NULL, '6c4961ced95a1260eb2110f164e4622951c0092333954c2b7f9382de016465e5', '2026-05-18 11:07:36', '2027-05-18 11:07:36', 0, 5, NULL),
(7, 4, 9, NULL, '5487af8611a652df5282b552a27640753914f4cb79b6f268878c37e638636b31', '2026-05-28 16:15:32', '2027-05-28 16:15:32', 0, 5, NULL),
(8, 4, 4, NULL, '21b8de94f9ef93ab71f84db21020473a042945c98a04499bba4431294fc3a883', '2026-05-28 16:18:09', '2027-05-28 16:18:09', 0, 5, NULL),
(9, 4, 6, NULL, '6180a8fe8d3c3dbf5724d7bb88d71a8fbcdfc526dc0a29fcd9d90fb97594164f', '2026-05-28 16:20:01', '2027-05-28 16:20:01', 0, 5, NULL),
(10, 4, 2, 1, '240717d788bdd7a10911fc4df9084e43c027a873daede3677d79ada626d18466', '2026-05-18 11:02:45', '2027-05-18 11:02:45', 0, 5, NULL),
(11, 4, 5, 1, 'd5e30518f4b8787dde3ed05725b849b0993815e482b44f42ab39e30a9b140f1c', '2026-05-18 11:02:45', '2027-05-18 11:02:45', 0, 5, NULL),
(12, 4, 2, 2, '894b509d66af51580312a66eb4a117a080dc9963ebc41585c9ac8132bd9a56a2', '2026-05-18 11:07:36', '2027-05-18 11:07:36', 0, 5, NULL),
(13, 4, 4, 2, 'c447ca81944cf790b5934bf3f20bd5efbe86b5443d7f7acf5a25cb10420e3362', '2026-05-18 11:07:36', '2027-05-18 11:07:36', 0, 5, NULL),
(14, 4, 9, 7, '1a69b8cea3e75e41a1993ed3037ce8d0c12cd25022de611abdc49b918ea459cb', '2026-05-28 16:15:32', '2027-05-28 16:15:32', 0, 5, NULL),
(15, 4, 4, 8, 'bdbfac0ae8c8ce02533d7ac30851809971ec4a6e1a4b822a0bbcd778e0673219', '2026-05-28 16:18:09', '2027-05-28 16:18:09', 0, 5, NULL),
(16, 4, 6, 9, '0cc6c5d2e7018daccd391450225887d81c8b0e6b400a2d4762981b465da25e6b', '2026-05-28 16:20:01', '2027-05-28 16:20:01', 0, 5, NULL),
(17, 4, 65, 13, '22254083592234c714abfe3e76ad4ce22c90dfeeafac3ba3eb578f2cbb788f42', '2026-05-28 19:12:47', '2027-05-28 19:12:47', 2, 5, 'recursos/productos/65/tarjetas-mayor-menor-e-igual-2o-1779111817.pdf'),
(18, 9, 2, 11, '3944fabef65067e43bbd75185221409baadadec4c6a95dabedf08410dae1004b', '2026-05-28 18:10:12', '2027-05-28 18:10:12', 0, 5, NULL),
(19, 4, 69, 14, '816118263e6c8cfe221554938410ffe8614ea1d33db039392ce0c0cdeff3d058', '2026-05-29 13:25:42', '2027-05-29 13:25:42', 0, 5, NULL),
(20, 4, 2, 15, 'fed2bb016dc4bd9763e61080699ce56288ddf58b1ab2c5cbf46e65589666fee7', '2026-05-29 13:27:38', '2027-05-29 13:27:38', 0, 5, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 2, 1, 2.50),
(2, 1, 5, 1, 3.25),
(3, 2, 2, 1, 2.50),
(4, 2, 4, 1, 1.50),
(5, 3, 2, 1, 2.50),
(6, 3, 4, 1, 1.50),
(7, 3, 5, 1, 3.25),
(8, 3, 6, 1, 2.50),
(9, 4, 2, 1, 2.50),
(10, 4, 4, 1, 1.50),
(11, 4, 5, 1, 3.25),
(12, 4, 6, 1, 2.50),
(13, 5, 12, 1, 1.75),
(14, 6, 4, 1, 1.50),
(15, 7, 9, 1, 2.75),
(16, 8, 4, 1, 1.50),
(17, 9, 6, 1, 2.50),
(18, 10, 2, 1, 2.50),
(19, 11, 2, 2, 2.50),
(20, 12, 9, 1, 2.75),
(21, 13, 65, 1, 1.75),
(22, 14, 69, 1, 2.50),
(23, 15, 2, 1, 2.50),
(24, 16, 2, 1, 2.50),
(25, 16, 3, 1, 2.50),
(26, 16, 69, 1, 2.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `favoritos`
--

INSERT INTO `favoritos` (`id`, `usuario_id`, `producto_id`, `fecha`) VALUES
(10, 5, 5, '2026-05-12 13:05:54'),
(13, 7, 7, '2026-05-22 11:37:54'),
(33, 9, 3, '2026-05-29 10:57:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ips_sospechosas`
--

CREATE TABLE `ips_sospechosas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `ip` varchar(45) NOT NULL,
  `motivo` text DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ips_sospechosas`
--

INSERT INTO `ips_sospechosas` (`id`, `usuario_id`, `ip`, `motivo`, `fecha`, `estado`) VALUES
(6, 4, '::1', 'IP registrada como sospechosa por acceso no reconocido.', '2026-05-26 11:03:19', 'pendiente'),
(7, 4, '::1', 'IP registrada como sospechosa por acceso no reconocido.', '2026-05-26 11:10:32', 'pendiente'),
(8, 4, '::1', 'IP registrada como sospechosa por acceso no reconocido.', '2026-05-28 13:10:35', 'pendiente'),
(9, 4, '::1', 'IP registrada como sospechosa por acceso no reconocido.', '2026-05-29 13:35:33', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`id`, `nombre`) VALUES
(1, '1º Primaria'),
(2, '2º Primaria'),
(3, '3º Primaria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `referencia_pago` varchar(255) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagado','completado','fallido','reembolsado') NOT NULL DEFAULT 'pendiente',
  `fecha_pago` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `pedido_id`, `referencia_pago`, `monto`, `estado`, `fecha_pago`) VALUES
(1, 1, 'cs_test_b1RGh1pNWyHlsuHEBmU413p4uefnCNeejegGPLksaG3jJTP8hWCDv8glGm', 5.75, 'pagado', '2026-05-18 11:07:06'),
(2, 2, 'cs_test_b1C4xriM0pXecTzp7DoA8ATvuAurxGi7poRjeWJ3sFtlAXxtwqUJUoe8BQ', 4.00, 'pagado', '2026-05-18 11:09:36'),
(3, 3, 'cs_test_b1gpKMgj9B1PHwY2pKIwUx2b13YJOqhqskmShNU4EQweemWtuMlpFxNY9D', 9.75, 'pendiente', NULL),
(4, 4, 'cs_test_b1jEqHXwWAjElbiZYMhdF9F2ahRzpBJLxrSTSZS7tKSW6VfZqZiKBJAULo', 9.75, 'pendiente', NULL),
(5, 5, 'cs_test_a1awZVwT64oNkTiOBPyRWbbk67Lgf6RqoCZrcphmCI2N2vz6KOhFduT81z', 1.75, 'pendiente', NULL),
(6, 6, 'cs_test_a1cLcaB0uDFZEGzyNybsZbFXV2gTOf93vLOsDfkQyeanJ9mz5swvvWwaA4', 1.50, 'pagado', '2026-05-28 16:11:09'),
(7, 7, 'cs_test_a167iDDgd8C5SGgdVEwdptcFXcGkY59vHSJDFRHvgZCzDeO2r4zI8fFnLy', 2.75, 'pagado', '2026-05-28 16:16:22'),
(8, 8, 'cs_test_a13vLe15k3EOSvEmVCdR72Gp6J9XAdVglBAEiYubw1jslS7YOkabKXJtTH', 1.50, 'pagado', '2026-05-28 16:18:47'),
(9, 9, 'cs_test_a1Mq9iUvPWskuJL6n8ytoMvUw7uiQogxPMnHQpvubH8PudZO93JsrCKLTk', 2.50, 'pagado', '2026-05-28 16:20:17'),
(10, 10, 'cs_test_a1MGVWF3tb8Bzn4Li1p1XimAe8lx5tgtCOwXJIkSpBpeo8JodeUN1WyqsA', 2.50, 'pagado', '2026-05-28 16:44:35'),
(11, 11, 'cs_test_a1kJkRNtbDGlW3YP5MghieGxh1bz2ydYeRE7SgV3toBNfbqLyoC9qUBaax', 5.00, 'pagado', '2026-05-28 18:10:26'),
(12, 12, 'cs_test_a1GN7VZ94OkaCHyOwrJI4VzxOaB0aJNdT3LtnsrSxpEhJmcHljvuIQDid7', 2.75, 'pendiente', NULL),
(13, 13, 'cs_test_a1tWPTOQhS1nz5rTslbWX4XxobuOwaRkIXJQtFBKjGiR635d01KIhOx3ZN', 1.75, 'pagado', '2026-05-28 19:13:03'),
(14, 14, 'cs_test_a1nJcDb3MsQfIzJjShwKEHzVwHDSC5cyCY5EKYYC0TeDhHVzmmngrcFG2U', 2.50, 'pagado', '2026-05-29 13:26:07'),
(15, 15, 'cs_test_a1NPxOWBrATMDYzkBZOQ0oDm4IcfjeMzmQkqvPNbP9X7FK1P8nH1GDCIzf', 2.50, 'pagado', '2026-05-29 13:27:52'),
(16, 16, 'cs_test_b16VijvHsLqMG7TEiAFQQ7dqhT7cRXZ7ieEr3zecV6lNKL1csO9uQpKFXk', 7.50, 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `usuario_id`, `token_hash`, `fecha_creacion`, `fecha_expiracion`, `usado`) VALUES
(1, 5, '6abb18fdaa764c1d6c1ac2cf5c0c3b40d20d54d987b0595a55ef57ddc855e6d8', '2026-05-22 10:15:28', '2026-05-22 11:15:28', 1),
(2, 5, '4c29f7e1d143aeabe7cf924ffee20feb8a8ef1bf9d863e4e195e731fc3057732', '2026-05-22 10:42:35', '2026-05-22 11:42:35', 1),
(3, 5, 'a53688f22a7bff92f64b56f9cded577f2eee1d873e0bfee5dc908045f33c88ef', '2026-05-22 10:43:41', '2026-05-22 11:43:41', 1),
(4, 5, 'a11b3303197b2c2c28179a41ab6688aac4b43426486aa7daac38ceb1a18d120d', '2026-05-22 10:43:51', '2026-05-22 11:43:51', 1),
(5, 5, '1108c9e16ae9181682e844be4e4cf2bd142fb877d943de3d67a2e8b645fd3609', '2026-05-22 10:51:08', '2026-05-22 11:51:08', 1),
(6, 5, 'f6f744d0d92e65eeabefe93b1ac4b23d4ffb071c66a0c0598724f9b428b78a02', '2026-05-22 10:56:37', '2026-05-22 11:56:37', 1),
(7, 5, '57a4f34936164cc022339cec867b7bacf9bad45734cf120cdd1e161ee7c3bc5e', '2026-05-22 11:23:40', '2026-05-22 12:23:40', 1),
(8, 5, 'acc0f0c0de42f2ff6da2591e9a065d346706e72ce51d5f9e161eecddda1a0ce6', '2026-05-22 11:25:09', '2026-05-22 12:25:09', 1),
(9, 4, 'e4b0d5db617f49b0ffb8e287da27711966b922eab3d2a7e5733717aed1bdbbcb', '2026-05-26 11:10:40', '2026-05-26 12:10:40', 1),
(10, 7, '6643bf4ebdc311dd736f5e40e1b7b7a07683f19c26deeb131d0f8d5fce113ae1', '2026-05-26 11:12:26', '2026-05-26 12:12:26', 1),
(11, 4, '315ddd276181115afe1cdf8fd681a6c281e0cce2f3274593462e8a5b7f175600', '2026-05-28 13:10:58', '2026-05-28 14:10:58', 1),
(12, 4, '91f463824708d4f5525a6be78f335eadcab8bfb6d3a829b48b148e1d4df2114e', '2026-05-28 13:11:31', '2026-05-28 14:11:31', 1),
(13, 4, 'c8e77e63ea35885bf6113a675e39a6862ee6782d85cef9db47be868b6deeb8c9', '2026-05-29 13:35:43', '2026-05-29 14:35:43', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagado','cancelado','reembolsado') DEFAULT 'pendiente',
  `metodo_pago` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `fecha_pedido`, `total`, `estado`, `metodo_pago`) VALUES
(1, 4, '2026-05-18 11:02:45', 5.75, 'pagado', 'stripe'),
(2, 4, '2026-05-18 11:07:36', 4.00, 'pagado', 'stripe'),
(3, 4, '2026-05-18 11:26:52', 9.75, 'pendiente', 'stripe'),
(4, 4, '2026-05-18 11:27:34', 9.75, 'pendiente', 'stripe'),
(5, 4, '2026-05-25 12:53:11', 1.75, 'pendiente', 'stripe'),
(6, 5, '2026-05-28 16:09:22', 1.50, 'pagado', 'stripe'),
(7, 4, '2026-05-28 16:15:32', 2.75, 'pagado', 'stripe'),
(8, 4, '2026-05-28 16:18:09', 1.50, 'pagado', 'stripe'),
(9, 4, '2026-05-28 16:20:01', 2.50, 'pagado', 'stripe'),
(10, 7, '2026-05-28 16:44:17', 2.50, 'pagado', 'stripe'),
(11, 9, '2026-05-28 18:10:12', 5.00, 'pagado', 'stripe'),
(12, 4, '2026-05-28 18:13:43', 2.75, 'pendiente', 'stripe'),
(13, 4, '2026-05-28 19:12:47', 1.75, 'pagado', 'stripe'),
(14, 4, '2026-05-29 13:25:42', 2.50, 'pagado', 'stripe'),
(15, 4, '2026-05-29 13:27:38', 2.50, 'pagado', 'stripe'),
(16, 4, '2026-05-29 13:30:54', 7.50, 'pendiente', 'stripe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `precio` decimal(8,2) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `nivel_id` int(11) DEFAULT NULL,
  `imagen` varchar(30) DEFAULT NULL,
  `estado` enum('activo','oculto','eliminado') DEFAULT 'activo',
  `max_descargas` int(11) DEFAULT 5,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `es_gratuito` tinyint(1) DEFAULT 0,
  `video_url` varchar(200) DEFAULT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `archivo_s3_key` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `titulo`, `descripcion`, `contenido`, `precio`, `categoria_id`, `nivel_id`, `imagen`, `estado`, `max_descargas`, `fecha_creacion`, `es_gratuito`, `video_url`, `clicks`, `archivo_s3_key`) VALUES
(2, 'Detective matemático 1º', 'En esta actividad los alumnos deben descubrir un número a partir de una serie de pistas relacionadas con el valor posicional de sus cifras (decenas y unidades). A través de diferentes indicaciones como “la cifra de las decenas es…”, “tiene … unidades”, el alumnado deberá analizar la información y deducir qué número cumple todas las condiciones. Se trata de una propuesta que fomenta el razonamiento lógico.', 'Incluye 30 retos matemáticos diferentes con números del 10 al 99. Diseñado para trabajar el razonamiento lógico y el sistema decimal. Plantilla lista para imprimir y plastificar.', 2.50, 2, 1, 'detectivem', 'activo', 5, '2026-03-31 18:34:44', 0, 'detectivemat1.mp4', 81, NULL),
(3, 'Detective matemático 2º', 'En esta actividad los alumnos deben descubrir un número a partir de una serie de pistas relacionadas con el valor posicional de sus cifras (centenas, decenas y unidades). A través de diferentes indicaciones como \"la cifra de las decenas es…\", \"tiene … unidades\", el alumnado deberá analizar la información y deducir qué número cumple todas las condiciones. Se trata de una propuesta que fomenta el razonamiento lógico y el pensamiento matemático.', 'Incluye 30 retos matemáticos diferentes con números del 100 al 999, diseñados para trabajar el razonamiento lógico, la comprensión del sistema de numeración decimal y la comparación de números. Plantilla lista para imprimir y plastificar.', 2.50, 2, 2, 'detecseg.w', 'activo', 5, '2026-03-31 18:37:59', 0, 'detectivemat2.mp4', 24, NULL),
(4, 'Decenas y unidades', 'Este material está diseñado para reforzar la comprensión del valor posicional en números de dos cifras. A través de tarjetas visuales, el alumnado debe clasificar cada número en la plantilla correspondiente, reconociendo y ubicando las decenas y unidades.', '32 tarjetas  con descomposición de números de 2 cifras.\r\nPlantilla para clasificar el número por posiciones (D-U).\r\nIlustraciones de barras y cuadraditos representando las decenas y unidades.', 1.50, 2, 1, 'decenasyun', 'activo', 5, '2026-03-31 18:46:37', 0, NULL, 50, NULL),
(5, 'Sumas, restas, decenas y unidades', 'Esta actividad consiste en un conjunto de tarjetas de cálculo diseñadas para trabajar el cálculo de forma progresiva y motivadora.\r\nCada tarjeta presenta una operación con un espacio en blanco para escribir el resultado. El alumnado puede trabajar de manera individual o en pequeños grupos. \r\nSe puede realizar como repaso, estaciones de aprendizaje, reto de velocidad o refuerzo.', '62 tarjetas de sumas y restas.\r\nOperaciones multiniveladas desde el nivel 1 (más facil)hasta el nivel 3 (difícil) en sumas. \r\nOperaciones multiniveladas desde el nivel 1 (fácil) hasta nivel 2 (difícil) en restas.\r\nSe acompaña una plantilla de autocorrección, colocando la solución al dorso de la tarjeta.', 3.25, 2, 1, 'sumasresta', 'activo', 5, '2026-04-10 10:57:01', 0, NULL, 12, NULL),
(6, 'Cálculo mental (Sumas)', 'Esta actividad consiste en una ruleta de sumas pensadas para primero de primaria, con el objetivo de reforzar el cálculo mental y escrito de forma visual y motivadora.\r\nEl alumnado debe observar la ruleta, resolver cada suma y completar el resultado en el hueco ,correspondiente.', '4 ruletas de sumas con 8 operaciones cada una.\r\nUn número en el centrom que será el número fijo de la suma.\r\nUn espacio en blanco en cada operación para que el alumnado escriba el resultado.\r\nPor detrás, la misma ruleta autocorregible.', 2.50, 2, 1, 'calculo1.w', 'activo', 5, '2026-04-10 10:57:01', 0, NULL, 32, NULL),
(7, 'Plantilla con números del 1 al 99', 'Esta actividad está diseñada para que el alumnado trabaje de forma manipulativa y visual la comprensión de los números de tres cifras (del 1 al 99). A través de una plantilla con tarjetas para cada número, el alumnado completará diferentes representaciones y descomposiciones del número propuesto. Es ideal para reforzar la numeración, el valor posicional y la representación gráfica de las decenas y unidades.', 'Inlcluye 24 números seleccionados, tarjetas individuales, plantilla general reutilizable para que se puedan colocar los números uno a uno.\r\nCada número se presenta en un conjunto de tarjetas con las siguientes secciones a completar por el alumnado: \r\nNúmero en cifras, \r\nNúmero en palabras, \r\nDescomposición de números (expresando en número como suma de decenas y unidades), \r\nRepresentación gráfica: símbolo visual que representan las decenas y unidades. \r\nDescomposición en letra. Representación en ábaco.', 2.75, 2, 1, 'num1a99de1', 'activo', 5, '2026-04-10 11:01:22', 0, NULL, 8, NULL),
(8, 'Valor posicional y números hasta el 99', 'Esta actividad está diseñada para que el alumnado comprenda y consolide el valor posicional de los números de dos cifras de forma visual, manipulativa y significativa. \r\nA través de una plantilla estructurada, el alumnado trabaja cada número desde diferentes representaciones: \r\nRepresentación de decenas y unidades. \r\nIdentificación de la descomposición en forma de suma. \r\nEscritura del número en cifras. \r\nEste enfoque permite reforzar la comprensión profunda del número favoreciendo la conexión entre cantidad, símbolo y palabra escrita.', 'Incluye 30 números seleccionados hasta el 99 con sus diferentes representaciones de decenas y unidades, su escritura, y su descomposición. \r\nPlantilla lista para imprimir y plastificar.', 2.50, 2, 1, 'valorposic', 'activo', 5, '2026-04-10 11:01:22', 0, NULL, 1, NULL),
(9, 'RESTAS DECENAS Y UNIDADES 1º', 'Este recurso está diseñado para que el alumnado practique las restas de números de dos cifras de forma razonada, visual y manipulativa, reforzando la comprensión del valor posicional y el uso correcto de las restas en primero de primaria. \r\nA través de tarjetas, el alumnado resolverá restas descomponiendo los números en decenas y unidades, favoreciendo un aprendizaje más significativo y comprensivo.', '28 tarjetas de restas de dos cifras. \r\nTarjetas pensadas para trabajar la descomposición D-U. \r\nDiseño claro y reutilizable, ideal para plastificar. \r\nTarjetas autocorregibles para fomentar autonomía en el alumnado.', 2.75, 2, 1, 'restasdecenasyunidades.png', 'activo', 5, '2026-04-10 11:04:24', 0, NULL, 23, NULL),
(10, 'Relación de números, escritura y descomposición', 'Este recurso permite trabajar la relación entre el número en su forma numérica, su forma escrita en palabras y su descomposición en decenas y unidades. A través de tarjetas, el alumnado debe emparejar correctamente las tres representaciones de cada número, desarrollando la comprensión global del valor posicional y la lectoescritura numérica.', '16 tarjetas  de números de dos cifras con la escritura en palabras de cada número y la descomposición correspondiente (D-U).', 2.50, 2, 1, 'relaciones', 'activo', 5, '2026-04-10 11:04:24', 0, NULL, 3, NULL),
(11, 'Tarjetas de descomposición del 1 al 99', 'Esta actividad está diseñada para aprender a construir y descomponer números de dos cifras de forma visual y práctica', '32 tarjetas de descomposición. \r\nActividad perfecta para trabajar en estaciones de aprendizaje, rincones matemáticos o refuerzo.', 2.75, 2, 1, 'tardescomp', 'activo', 5, '2026-04-10 11:06:57', 0, NULL, 2, NULL),
(12, 'Tarjetas Mayor, Menor e Igual', 'Este recurso ofrece una forma visual, divertida y muy práctica para que el alumnado aprenda a comparar números utilizando los símbolos de mayor, menor e igual. \r\nDeben observar dos cantidades y colocar el símbolo que corresponda para indicar si el número es mayor, menor o igual.', '30 tarjetas para identificar si el número es mayor, menor e igual.', 1.75, 2, 1, 'maymenigprim.webp', 'activo', 5, '2026-04-10 11:06:57', 0, 'mayormenorigual,mp4', 4, NULL),
(13, 'Numero anterior y posterior', 'Este recurso está diseñado para trabajar el concepto del número anterior y posterior de forma visual, estructurada y progresiva. A través de tarjetas con diferentes niveles de dificultad y códigos de color por curso, el alumnado podrá identificar qué número va antes y cuál va después en una secuencia.', '28 tarjetas  naranjas con diferentes combinados de números de 1 y 2 cifras. \r\nIdeal para plastificar y reutilizar. \r\nPerfecto para trabajar en estaciones de aprendizaje, rincones, trabajo individual o refuerzo.', 1.75, 2, 1, 'antypostprim.webp', 'activo', 5, '2026-04-10 11:09:05', 0, NULL, 2, NULL),
(14, 'Menor a mayor y Mayor a menor', 'Este recurso está diseñado para trabajar de forma manipulativa y visual el orden numérico. Mediante tarjetas con series numéricas, el alumnado debe organizarlas según el criterio indicado: de menor a mayor o de mayor a menor.', '18 tarjetas con diferentes combinados de números de 2 cifras', 1.75, 2, 1, 'menamaymay', 'activo', 5, '2026-04-10 11:09:05', 0, 'demayoramenor.mp4', 1, NULL),
(15, 'Series ascendentes y descendentes', 'Este recurso está diseñado para reforzar el reconocimiento de patrones numéricos mediante series ascendentes y descendentes. Se proponen diferentes niveles con saltos de 2, 3, 5 y 10 tanto en progresión como en regresión.', '48 tarjetas  naranjas con diferentes combinados de números de 2 cifras. \r\nIdeal para plastificar y reutilizar. \r\nPerfecto para trabajar en estaciones de aprendizaje, rincones o refuerzo.', 1.75, 2, 1, 'serascydescprim.webp', 'activo', 5, '2026-04-10 11:12:40', 0, NULL, 1, NULL),
(16, 'Descomposición números del 1 al 99', 'Este recurso permite al alumnado trabajar la descomposición de números hasta el 99 de forma visual y manipulativa. A través de tarjetas con representaciones de decenas y unidades, el alumnado deberá identificar el número que representan entre tres opciones. Una sola opción es correcta, por lo que también se estimula la atención y discriminación visual.', '36 tarjetas con descomposición visual (decenas y unidades).', 2.25, 2, 1, 'desc1_99prim.webp', 'activo', 5, '2026-04-10 11:14:26', 0, NULL, 0, NULL),
(17, 'Escribir números del 1 al 99 (letra)', 'Esta actividad está diseñada para reforzar la lectura y escritura de números del 1 al 99, favoreciendo la correcta correspondencia entre cifras y palabras. \r\nEl alumnado debe observar el número presentado en cada tarjeta y escribirlo correctamente en letra, prestando atención a la ortografía y a la estructura del número.', '30 tarjetas con números del 1 al 99. Espacio en cada tarjeta para escribir el número en letra. \r\nFormato ideal para plastificar y escribir con rotulador borrable. \r\nNúmeros variados, incluyendo casos con ceros y combinaciones diversas.', 1.75, 2, 1, 'escrnumletraprim.web', 'activo', 5, '2026-04-10 11:15:32', 0, 'escribirelnumero1_99.mp4', 0, NULL),
(18, 'JUEGO, ¿QUIÉN TIENE...?', 'Este juego de dinámica oral está diseñado para reforzar de forma lúdica y colectiva el conocimiento de los números del 1 al 99. A través de pistas encadenadas, el alumnado deberá identificar, reconocer y relacionar en diferentes formas: cifras, letras, descomposición y relaciones numéricas. \r\nCada alumno recibe varias tarjetas en la que cuenta con dos partes: una afirmación de YO TENGO... (UN NÚMERO EN CIFRAS), y en otra una pregunta ¿QUIÉN TIENE.... (UNA PISTA QUE DESCRIBE EL OTRO NÚMERO). \r\nEl juego se desarrolla en cadena; cuando un alumno escucha la pista que coincide con su número, lee su tarjeta en voz alta y lanza la siguiente pregunta.', '32 tarjetas diseñadas para ser impresas y recortadas.', 2.50, 2, 1, 'juegoquientieneprim.webp', 'activo', 5, '2026-04-10 11:18:18', 0, NULL, 0, NULL),
(19, 'Problemas con ruleta', 'Esta actividad consiste en un juego matemático manipulativo en el que el alumnado resuelve problemas a través de una ruleta. La propuesta se organiza mediante tarjetas identificadas con letras del abecedario, de manera que cada letra corresponde a un problema diferente. \r\nPara comenzar, el alumnado debe girar la ruleta. la letra en la que se detiene indica qué tarjeta debe coger. Una vez seleccionada, deberá leer el problema y resolverlo.', '27 tarjetas con problemas  de sumas y restas sin llevar. \r\nIdeal para usar en rincones, estaciones de aprendizaje, trabajo en pequeño grupo o como actividad de refuerzo.', 2.50, 2, 1, 'problconruletaprim.webp', 'activo', 5, '2026-04-10 11:18:18', 0, NULL, 0, NULL),
(20, 'Resolvemos problemas', 'Este recurso incluye problemas matemáticos graduados para trabajar la comprensión, el razonamiento y resolución de problemas de forma clara y motivadora. \r\nLos problemas están organizándose en dos niveles de dificultad, lo que permite adaptarlos fácilmente al ritmo y necesidades del alumnado.', '20 Problemas Nivel 1 (Inicial): ideales para empezar a trabajar problemas sencillos, son sumas y restas de una cifra.\r\n0 Problemas Nivel 2 (Avanzado): para reforzar el razonamiento y trabajar con mayor dificultad, son problemas de 2 cifras de sumas y restas sin llevar. ', 2.76, 2, 1, 'resolvprobprim.webp', 'activo', 5, '2026-04-10 11:19:32', 0, NULL, 0, NULL),
(21, 'Juego de resolución de problemas', 'Este material propone una forma divertida y colaborativa de trabajar la resolución de problemas matemáticos. Ideal para trabajar en parejas o pequeños grupos. El alumnado debe ponerse de acuerdo en formar un problema completo, seleccionando las tarjetas (enunciado, pregunta, operación y resultado) que mejor encajen entre sí. \r\n\r\nPermite reforzar la comprensión, el razonamiento lógico-matemático y la estructuración de problemas.', 'Plantilla base dividida por colores:\r\n\r\n-azul: enunciado\r\n\r\n-Rojo: Pregunta\r\n\r\n-Naranja: Operación\r\n\r\n-Verde: Resultado\r\n\r\n18Tarjetas recortables de enunciados.\r\n\r\n18 tarjetas recortables de preguntas.\r\n\r\n18 tarjetas recortables de operaciones.\r\n\r\n18 tarjetas recortables de resultado.', 2.76, 2, 1, 'juresprobprim.webp', 'activo', 5, '2026-04-10 11:32:24', 0, 'juegoresolproblemas.mp4', 0, NULL),
(22, 'Problemas sencillos, visuales y manipulativos', 'Este recurso está diseñado para trabajar la resolución de problemas matemáticos de forma adaptada, visual y manipulativa. Cada problema viene con los datos clave resaltado en color, lo que facilita la identificación de la información importante. Además, se acompaña de imágenes con velcro para que los alumnos puedan representar de forma manipulativa el problema (quitar o añadir elementos) favoreciendo la comprensión y participación activa.', '10 problemas visuales con números en clave en color. \r\n\r\n20 problemas visuales para que puedas formar un problema diferente con los mismos elementos.\r\n\r\nPlantilla de resolución paso a paso.\r\n\r\nIlustraciones para usar con velcro (para ayudar al alumnado a quitar o poner objetos).', 2.50, 2, 1, 'probsenvismanprim.webp', 'activo', 5, '2026-04-10 11:32:24', 0, NULL, 0, NULL),
(23, 'Sumas y restas (Con dedos)', 'Este recurso está diseñado para reforzar las sumas y las restas sin llevar de una forma manipulativa, visual y accesible. A través de tarjetas con representaciones de dedos, el alumno contará, sumará o restará, desarrollando el cálculo mental de manera lúdica.', '36 tarjetas de sumas y restas con ayuda\r\n\r\n36 tarjetas de sumas y restas sin ayuda (solo apoyo de dedos)\r\n\r\n18 tarjetas de sumas llevando con dedo.', 2.50, 2, 1, 'sumyrescondedosprim.webp', 'activo', 5, '2026-04-10 11:34:31', 0, NULL, 0, NULL),
(24, 'Sumas y restas con recta numérica', 'Este recurso está diseñado para trabajar operaciones básicas de sumas y restas mediante el apoyo visual de una recta numérica. Favorece el razonamiento lógico y la comprensión del cálculo como desplazamiento en la línea numérica. El material incluye tanto sumas como restas, con y sin llevadas, así como operaciones de dos cifras permitiendo su uso progresivo y adaptado al nivel del alumnado.', '20 tarjetas de sumas con y sin llevar y restas sin llevar \r\n\r\n20 tarjetas de sumas y restas sin llevar.', 1.50, 2, 1, 'sumyresrectaprim.webp', 'activo', 5, '2026-04-10 11:34:31', 0, NULL, 0, NULL),
(25, 'Sumas, decenas y unidades', 'Este recurso está diseñado para que el alumnado practique las sumas de números de dos cifras de forma razonada, visual y manipulativa, reforzando la comprensión del valor posicional y el uso correcto de las sumas en primero de primaria. \r\nA través de tarjetas, el alumnado resolverá sumas descomponiendo los números en decenas y unidades, favoreciendo un aprendizaje más significativo y comprensivo.', '28 tarjetas de sumas de dos cifras. \r\nTarjetas pensadas para trabajar la descomposición D-U. \r\nDiseño claro y reutilizable, ideal para plastificar. \r\nTarjetas autocorregibles para fomentar autonomía en el alumnado.', 2.75, 2, 1, 'sumasdecun.webp', 'activo', 5, '2026-04-10 11:43:53', 0, 'sumasdecenasyunidades.mp4', 0, NULL),
(26, 'Tarjetas de Decenas y unidades', 'Este recurso está diseñado para trabajar de forma manipulativa y visual el valor posicional en los números de dos cifras. \r\nA través de tarjetas estructuradas, el alumnado observa la representación de decenas y unidades y debe completar diferentes apartados que refuerzan la comprensión del número. \r\nSe trabaja la identificación de decenas y unidades, descomposición del número en cifra, anterior y posterior, y escritura del número en cifra.', '30 tarjetas de descomposición. \r\nActividad perfecta para trabajar en estaciones de aprendizaje, rincones matemáticos o refuerzo. \r\nIdeal para recortar, plastificar y reutilizar.', 2.75, 2, 1, 'decyunprim.webp', 'activo', 5, '2026-04-10 11:46:37', 0, NULL, 0, NULL),
(27, 'Descomposición de decenas y unidades', 'Este recurso permite al alumnado recomponer y representar números de hasta cuatro cifras a partir del uso del ábaco. Se trabajan diferentes niveles y formatos para identificar, asociar y construir números, reforzando así el valor posicional de cada cifra.', '4 tarjetas con representaciones en ábaco. El alumnado deberá elegir entre tres opciones cuál es el número correcto. \r\n24 tarjetas con bolitas en el ábaco. El alumnado debe escribir el número que representa. \r\n24 tarjetas con el número dado. El alumnado debe formar ese número colocando las bolitas correspondientes que indica en el ábaco.', 2.50, 2, 1, 'descdecyunprim.webp', 'activo', 5, '2026-04-10 11:48:56', 0, NULL, 0, NULL),
(28, 'Sumas con regleta', 'Este recurso está diseñado para trabajar las sumas sencillas a través de las regletas Cuisenaire, favoreciendo el aprendizaje visual, manipulativo y activo. Las regletas permiten al alumnado representar cantidades, comparar longitudes y realizar sumas de forma clara y comprensible.', '24 operaciones de sumas utilizando la regleta\r\n\r\nPlantillas para colocar regletas y completar la suma.', 1.50, 2, 1, 'sumyresregprim.webp', 'activo', 5, '2026-04-10 11:51:30', 0, NULL, 0, NULL),
(29, 'Sumas y restas con policubos', 'Este recurso manipulativo está diseñado para trabajar las sumas y restas de forma visual y tangible mediante policubos. A través de tarjetas estructuradas, el alumno podrá representar cantidades, realizar operaciones y afianzar conceptos básicos del cálculo.\"', '4 plantillas de sumas y restas con policubos.\r\n\r\n36 tarjetas de sumas con policubos.\r\n\r\n30 tarjetas de restas con policubos.', 1.75, 2, 1, 'sumyrespolicprim.webp', 'activo', 5, '2026-04-10 11:53:26', 0, NULL, 0, NULL),
(30, 'Sumas con unidades', 'Este recurso está pensado para trabajar las sumas sencillas mediante la asociación visual de unidades. A través de tarjetas manipulativas, el alumnado contará elementos representados, sumará las cantidades y colocará el resultado de forma autónoma y divertida. Está diseñado para usarse de forma plastificada y con velcro, fomentando la interacción táctil y visual.', '6 plantillas de sumas con recuadros en blanco para colocar el número de unidades.\r\n\r\nResultados para recortar y colocar en el velcro.', 1.50, 2, 1, 'sumconudsprim.webp', 'activo', 5, '2026-04-10 11:53:26', 0, NULL, 0, NULL),
(31, 'Tarjeta de sumas', 'Este recurso está compuesto por un total de 48 tarjetas de sumas divididas en dos niveles de dificultad. Ideal para trabajar el cálculo mental y la discriminación de resultados de forma visual y manipulativa. Cada tarjeta presenta una operación y dos posibles respuestas, de las cuales solo una es la correcta, fomentando la atención, el razonamiento lógico y la autonomía del alumnado.', '24 tarjetas de Nivel 1: sumas sencillas con números pequeños, con apoyo visual y respuestas muy accesibles.\n\n24 tarjetas de Nivel 2: sumas más complejas, con números de dos cifras y distractores más ajustados.\n\nListo para plastificar y reutilizar', 1.50, 2, 1, 'sumasprim.webp', 'activo', 5, '2026-04-10 11:54:40', 0, NULL, 0, NULL),
(32, 'Restas sin llevar dos cifras', 'Este recurso está pensando para trabajar las restas sin llevar de dos cifras de forma visual y manipulativa. A través de plantillas con colores diferenciados para decenas (barras rojas) y unidades (cuadrados azules), el alumnado podrá comprender el proceso de la resta colocando y quitando elementos de forma tangible. Esta actividad es ideal para interiorizar el concepto de la resta sin errores de comprensión.', '4 hojas de plantillas con operaciones de restas sin llevar de dos cifras.\r\n\r\nColores diferenciados para decenas y unidades.\r\n\r\nCasillas para colocar y quitar barras y cuadros.\r\n\r\nPlantillas imprimibles reutilizables con velcro.\r\n\r\nBarras rojas(decenas) y cuadrados azules(unidades).', 1.75, 2, 1, 'resdoscifprim.webp', 'activo', 5, '2026-04-10 11:56:52', 0, NULL, 0, NULL),
(33, 'Sumas y restas con objetos', 'Este recurso está pensado para trabajar las sumas y restas de forma visual y manipulativa a través del uso de objetos. Ideal para el alumnado que necesita un enfoque más concreto de cálculo, permitiendo reforzar el concepto de cantidad y el sentido numérico mediante la acción de añadir o quitar objetos.', 'Plantilla de sumas y restas listas para plastificar.\r\n\r\nObjetos recortables con imágenes (hasta el número 20).\r\n\r\nOperaciones abiertas para que el alumno o docente cree sumas y restas.\r\n\r\nIdeal para trabajar con velcro: reutilizable y manipulativo.', 1.25, 2, 1, 'sumyresconobjsprim.webp', 'activo', 5, '2026-04-10 11:56:52', 0, NULL, 0, NULL),
(34, 'Dossier conceptos básicos', 'Este material está diseñado para trabajar los conceptos básicos espaciales y cuantitativos, desde un enfoque manipulativo, visual y adaptado. Ideal para introducir o reforzar contenidos esenciales en el desarrollo cognitivo temprano.', 'Lleno/vacío\r\n\r\nGrande/pequeño\r\n\r\nMuchos/pocos\r\n\r\nAncho/estrecho\r\n\r\nDentro/fuera\r\n\r\nCerca/lejos', 1.50, 2, 1, 'concbasprim.webp', 'activo', 5, '2026-04-10 11:57:53', 0, NULL, 0, NULL),
(35, 'Figuras geométricas', 'Este recurso está diseñado para trabajar la identificación y discriminación visual de figuras geométricas a través de imágenes reales y cotidianas. El alumnado deberá observar una imagen y seleccionar entre tres opciones cuál es la figura geométrica que más se le parece. Una forma divertida y visual de aprender geometría.', '44 tarjetas con imágenes reales de objetos.\r\n\r\nEn cada tarjeta aparecen tres opciones de figuras geométricas para elegir una que es correcta.\r\n\r\nFiguras trabajadas: círculo, cuadrado, triángulo, rectángulo, pentágono, hexágono y octógono.', 1.25, 2, 1, 'figgeoprim.webp', 'activo', 5, '2026-04-10 11:59:36', 0, NULL, 0, NULL),
(36, 'Lectura y escritura Dinero (euros y céntimos)', 'Este material está diseñado para trabajar de forma progresiva y manipulativa la lectura y escritura de monedas y billetes en euros y céntimos. A través de tres niveles de dificultad, el alumnado podrá avanzar desde el reconocimiento visual hasta la asociación escrita con apoyos de imágenes.', 'Nivel naranja: Asociación de la imagen del billete o moneda con su palabra correspondiente. \r\n\r\nNivel amarillo: Asociación con pista escrita..El alumnado debe decidir si se refiere a euros o a céntimos.\r\n\r\nNivel verde: Plantilla vacía donde el alumnado debe colocar la imagen del dinero y escribir su valor.', 1.00, 2, 1, 'escrdinprim.webp', 'activo', 5, '2026-04-10 11:59:36', 0, NULL, 0, NULL),
(37, 'Centenas, decenas y unidades', 'Este material está diseñado para reforzar la comprensión del valor posicional en números de tres cifras. A través de tarjetas visuales, el alumnado debe clasificar cada número en la plantilla correspondiente, reconociendo y ubicando las centenas, decenas y unidades.', '30 tarjetas  con descomposición de números de 3 cifras.\r\n\r\nPlantilla para clasificar el número por posiciones (C-D-U)\r\n\r\nIlustraciones de cuadrados, barras, representando las centenas, decenas y unidades.', 1.50, 2, 2, 'cendecunseg.webp', 'activo', 5, '2026-04-10 12:01:32', 0, NULL, 0, NULL),
(38, 'Sumas con llevadas', 'Este recurso consiste en tarjetas de sumas con llevadas donde se emplean colores diferenciados para destacar la decena y unidad, facilitando la comprensión del algoritmo y del proceso de llevar. Es una herramienta muy útil para alumnos que necesitan apoyo visual o manipulación para consolidar el concepto.', '24 Tarjetas de sumas con llevadas.\r\n\r\nCódigo de colores para diferenciar decenas y unidades.\r\n\r\nPlantilla de apoyo para colocar las operaciones.\r\n\r\nIdeal para plastificar y trabajar con rotulador o velcro.\r\n\r\nOperaciones de dos cifras.', 1.75, 2, 2, 'sumconllevseg.webp', 'activo', 5, '2026-04-10 12:01:32', 0, NULL, 0, NULL),
(39, 'Sumas con llevadas 3 cifras', 'Este recurso consiste en tarjetas de sumas con llevadas donde se emplean colores diferenciados para destacar la centena, decena y unidad, facilitando la comprensión del algoritmo y del proceso de llevar. Es una herramienta muy útil para alumnos que necesitan apoyo visual o manipulación para consolidar el concepto.', '24 Tarjetas de sumas con llevadas de 3 cifras.\r\n\r\nCódigo de colores para diferenciar centenas, decenas y unidades.\r\n\r\nPlantilla de apoyo para colocar las operaciones.\r\n\r\nIdeal para plastificar y trabajar con rotulador o velcro.\r\n\r\nOperaciones de tres cifras.', 1.75, 2, 2, 'sum3cifseg.webp', 'activo', 5, '2026-04-10 12:02:20', 0, NULL, 1, NULL),
(40, 'Restas con llevadas 3 cifras', 'Este recurso consiste en tarjetas de sumas con llevadas donde se emplean colores diferenciados para destacar la centena, decena y unidad, facilitando la comprensión del algoritmo y del proceso de llevar. Es una herramienta muy útil para alumnos que necesitan apoyo visual o manipulación para consolidar el concepto.', '24 Tarjetas de sumas con llevadas de 3 cifras.\r\n\r\nCódigo de colores para diferenciar centenas, decenas y unidades.\r\n\r\nPlantilla de apoyo para colocar las operaciones.\r\n\r\nIdeal para plastificar y trabajar con rotulador o velcro.\r\n\r\nOperaciones de tres cifras.', 1.75, 2, 2, 'resconllev3cifseg.webp', 'activo', 5, '2026-04-10 12:04:46', 0, NULL, 0, NULL),
(41, 'Juego de ressolución de problemas', 'Este material propone una forma divertida y colaborativa de trabajar la resolución de problemas matemáticos. Ideal para trabajar en parejas o pequeños grupos. El alumnado debe ponerse de acuerdo en formar un problema completo, seleccionando las tarjetas (enunciado, pregunta, operación y resultado) que mejor encajen entre sí. \r\n\r\nPermite reforzar la comprensión, el razonamiento lógico-matemático y la estructuración de problemas.', 'Plantilla base dividida por colores:\r\n\r\n-azul: enunciado\r\n\r\n-Rojo: Pregunta\r\n\r\n-Naranja: Operación\r\n\r\n-Verde: Resultado\r\n\r\n20 Tarjetas recortables de enunciados.\r\n\r\n20 tarjetas recortables de preguntas.\r\n\r\n20 tarjetas recortables de operaciones.\r\n\r\n20 tarjetas recortables de resultado.', 2.75, 2, 2, 'juegprobseg.webp', 'activo', 5, '2026-04-10 12:04:46', 0, NULL, 0, NULL),
(42, 'Resolvemos problemas', 'Este recurso incluye problemas matemáticos graduados para trabajar la comprensión, el razonamiento y resolución de problemas de forma clara y motivadora.\r\n\r\nLos problemas están organizándose en dos niveles de dificultad, lo que permite adaptarlos fácilmente al ritmo y necesidades del alumnado. ', '20 Problemas Nivel 1 (Inicial): ideales para empezar a trabajar problemas sencillos, son sumas y restas sin llevar.\r\n\r\n20 Problemas Nivel 2 (Avanzado): para reforzar el razonamiento y trabajar con mayor dificultad, son problemas de 2-3 cifras de sumas y restas con llevadas.\",2.75,,,resprobseg.webp\r\n\r\n20 Problemas Nivel 2 (Avanzado): para reforzar el razonamiento y trabajar con mayor dificultad, son problemas de 2-3 cifras de sumas y restas con llevadas.', 2.76, 2, 2, 'resprobseg.webp', 'activo', 5, '2026-04-10 12:05:58', 0, NULL, 0, NULL),
(43, 'Problemas de estadística', 'Este recurso está diseñado para que el alumnado trabaje la interpretación de gráficos de barras a través de problemas contextualizados y adaptados a segundo de primaria.\r\n\r\nEl alumnado debe observar detenidamente los gráficos y analizar la información y después responder a las preguntas con los datos representados.', '26 problemas de estadística diferentes.\r\n\r\nDiseño claro y reutilizable, ideal para plastificar.\r\n\r\nPerfecto para trabajar en estaciones de aprendizaje, refuerzo, rincones matemáticos.', 3.25, 2, 2, 'probestadseg.webp', 'activo', 5, '2026-04-10 12:16:42', 0, NULL, 0, NULL),
(44, 'Plantilla con números', 'Esta actividad está diseñada para que el alumnado trabaje de forma manipulativa y visual la comprensión de los números de tres cifras (del 100 al 999). A través de una plantilla con tarjetas para cada número, el alumnado completará diferentes representaciones y descomposiciones del número propuesto. Es ideal para reforzar la numeración, el valor posicional y la representación gráfica de las centenas, decenas y unidades.', 'Inlcluye 24 números seleccionados, tarjetas individuales, plantilla general reutilizable para que se puedan colocar los números uno a uno.\r\n\r\nCada número se presenta en un conjunto de tarjetas con las siguientes secciones a completar por el alumnado:\r\n\r\nNúmero en cifras,\r\n\r\nNúmero en palabras\r\n\r\nDescomposición de números (expresando en número como suma de centenas, decenas y unidades)\r\n\r\nRepresentación gráfica: símbolo visual que representan las centenas decenas y unidades.\r\n\r\nDescomposición en letra\r\n\r\nRepresentación en ábaco.', 2.76, 2, 1, 'plannumseg.webp', 'activo', 5, '2026-04-10 12:21:30', 0, NULL, 4, NULL),
(47, 'Valor posicional y nuúmeros hasta el 999', 'Esta actividad está diseñada para que el alumnado comprenda y consolide el valor posicional de los números de dos cifras de forma visual, manipulativa y significativa.\r\n\r\nA través de una plantilla estructurada, el alumnado trabaja cada número desde diferentes representaciones:\r\n\r\nRepresentación de centenas, decenas y unidades.\r\n\r\nIdentificación de la descomposición en forma de suma.\r\n\r\nEscritura del número en cifras.\r\n\r\nEste enfoque permite reforzar la comprensión profunda del número favoreciendo la conexión entre cantidad, símbolo y palabra escrita.', 'Incluye 30 números seleccionados hasta el 999 con sus diferentes representaciones de centena, decenas y unidades, su escritura, y su descomposición.\r\n\r\nPlantilla lista para imprimir y plastificar.', 2.50, 2, 2, 'valpos999seg.webp', 'activo', 5, '2026-04-10 12:26:33', 0, NULL, 0, NULL),
(48, 'Tarjetas de centenas,decenas y unidades', 'Este recurso está diseñado para trabajar de forma manipulativa y visual el valor posicional en los números de tres cifras.\r\n\r\nA través de tarjetas estructuradas, el alumnado observa la representación de centenas, decenas y unidades y debe completar diferentes apartados que refuerzan la comprensión del número.\r\n\r\nSe trabaja la identificación de centenas, decenas y unidades, descomposición del número en cifra, anterior y posterior, y escritura del número en cifra', '30 tarjetas de descomposición.\r\n\r\nActividad perfecta para trabajar en estaciones de aprendizaje, rincones matemáticos o refuerzo.\r\n\r\nIdeal para recortar, plastificar y reutilizar.', 2.75, 2, 2, 'tarcendecunseg.webp', 'activo', 5, '2026-04-10 12:26:33', 0, NULL, 0, NULL),
(49, 'Sumas, centenas, decenas y unidades', 'Este recurso está diseñado para que el alumnado practique las sumas de números de tres cifras de forma razonada, visual y manipulativa, reforzando la comprensión del valor posicional y el uso correcto de las sumas en primero de primaria.\r\n\r\nA través de tarjetas, el alumnado resolverá sumas descomponiendo los números en centenas, decenas y unidades, favoreciendo un aprendizaje más significativo y comprensivo.', '28 tarjetas de sumas de tres cifras con y sin llevar.\r\n\r\nTarjetas pensadas para trabajar la descomposición C-D-U.\r\n\r\nDiseño claro y reutilizable, ideal para plastificar.\r\n\r\nTarjetas autocorregibles para fomentar autonomía en el alumnado.', 2.75, 2, 2, 'sumcendecunseg.webp', 'activo', 5, '2026-04-10 12:33:12', 0, NULL, 0, NULL),
(50, 'Restas centenas, decenas y unidades', 'Este recurso está diseñado para que el alumnado practique las restas de números de tres cifras de forma razonada, visual y manipulativa, reforzando la comprensión del valor posicional y el uso correcto de las restas en segundo de primaria.\r\n\r\nA través de tarjetas, el alumnado resolverá restas descomponiendo los números en centenas, decenas y unidades, favoreciendo un aprendizaje más significativo y comprensivo.', '28 tarjetas de restas de tres cifras con y sin llevar.\r\n\r\nTarjetas pensadas para trabajar la descomposición C-D-U.\r\n\r\nDiseño claro y reutilizable, ideal para plastificar.\r\n\r\nTarjetas autocorregibles para fomentar autonomía en el alumnado.', 2.75, 2, 2, 'rescendecunseg.webp', 'activo', 5, '2026-04-10 12:33:12', 0, NULL, 0, NULL),
(51, 'Escribir el número (letra) 2º', 'Esta actividad está diseñada para reforzar la lectura y escritura de números del 100 al 999, favoreciendo la correcta correspondencia entre cifras y palabras.\r\n\r\nEl alumnado debe observar el número presentado en cada tarjeta y escribirlo correctamente en letra, prestando atención a la ortografía y a la estructura del número.', '30 tarjetas con números del 100 al 999.\r\n\r\nEspacio en cada tarjeta para escribir el número en letra.\r\n\r\nFormato ideal para plastificar y escribir con rotulador borrable.\r\n\r\nNúmeros variados, incluyendo casos con ceros y combinaciones diversas.', 1.75, 2, 2, 'escnumletseg.webp', 'activo', 5, '2026-04-10 12:35:09', 0, NULL, 0, NULL),
(52, 'Juego ¿Quién tiene…? -2º', 'Este juego de dinámica oral está diseñado para reforzar de forma lúdica y colectiva el conocimiento de los números del 100 al 999. A través de pistas encadenadas, el alumnado deberá identificar, reconocer y relacionar en diferentes formas: cifras, letras, descomposición y relaciones numéricas.\r\n\r\nCada alumno recibe varias tarjetas en la que cuenta con dos partes: una afirmación de YO TENGO... (UN NÚMERO EN CIFRAS), y en otra una pregunta ¿QUIÉN TIENE.... (UNA PISTA QUE DESCRIBE EL OTRO NÚMERO).\r\n\r\nEl juego se desarrolla en cadena; cuando un alumno escucha la pista que coincide con su número, lee su tarjeta en voz alta y lanza la siguiente pregunta.', '32 tarjetas diseñadas para ser impresas y recortadas.', 2.50, 2, 2, 'juegoquienseg.webp', 'activo', 5, '2026-04-10 12:35:09', 0, NULL, 0, NULL),
(53, 'Pienso el número', 'Esta actividad está diseñada para reforzar la comprensión del valor posicional (centenas, decenas y unidades) y la lectura y escritura de números hasta el 999, a través de un enfoque manipulativo y visual.\r\n\r\nEl alumnado debe leer la descripción del número y averiguar de qué número se trata, colocando la respuesta correcta.', '24 tarjetas con descripciones numéricas (centenas, decenas y unidades).\r\n\r\n24 números sueltos para colocarlo como solución.\r\n\r\nFormato ideal para plastificar y utilizar con velcro.\r\n\r\nTarjetas graduadas con diferentes niveles de dificultad', 1.75, 2, 2, 'piensoelnumseg.webp', 'activo', 5, '2026-04-10 12:36:11', 0, NULL, 0, NULL),
(54, 'Descomposición de números hasta el 999', 'Este recurso permite al alumnado trabajar la descomposición de números hasta el 999 de forma visual y manipulativa. A través de tarjetas con representaciones de centenas, decenas y unidades, el alumnado deberá identificar el número que representan entre tres opciones. Una sola opción es correcta, por lo que también se estimula la atención y discriminación visual.', '36 tarjetas con descomposición visual (centenas, decenas y unidades).', 2.25, 2, 2, 'descomnumseg.webp', 'activo', 5, '2026-04-10 12:38:21', 0, NULL, 2, NULL),
(55, 'Dominó, sumas y restas con y sin llevadas', 'Este recurso consiste en un juego tipo dominó con operaciones básicas de suma y resta. Cada pieza contiene una operación en un extremo y un resultado en el otro. El alumnado debe resolver la operación para poder emparejarla con el resultado correspondiente. Incluye dos niveles de dificultad, diferenciados por color para facilitar su uso y adaptación al nivel de cada alumno.', '24 tarjetas dominó de 3-4 cifras de sumas y restas sin llevar \r\n\r\n24 tarjetas dominó de 3-4 cifras de sumas y restas llevando.', 1.75, 2, 2, 'dominosegsumyres.webp', 'activo', 5, '2026-04-10 12:38:21', 0, NULL, 0, NULL),
(56, 'Series ascendentes y descendentes', 'Este recurso está diseñado para reforzar el reconocimiento de patrones numéricos mediante series ascendentes y descendentes. Se proponen diferentes niveles con saltos de 2, 3, 5 y 10 tanto en progresión como en regresión', '48 tarjetas  naranjas con diferentes combinados de números de 2 cifras (2 de primaria).\r\n\r\nIdeal para plastificar y reutilizar.\r\n\r\nPerfecto para trabajar en estaciones de aprendizaje, rincones o refuerzo.', 1.75, 2, 2, 'seriesasdesseg.webp', 'activo', 5, '2026-04-10 12:40:52', 0, NULL, 0, NULL),
(57, 'Numero anterior y posterior', 'Este recurso está diseñado para trabajar el concepto del número anterior y posterior de forma visual, estructurada y progresiva. A través de tarjetas con diferentes niveles de dificultad y códigos de color por curso, el alumnado podrá identificar qué número va antes y cuál va después en una secuencia.', '28 tarjetas  naranjas con diferentes combinados de números de 2 cifras (2º de primaria)\r\n\r\nIdeal para plastificar y reutilizar.\r\n\r\nPerfecto para trabajar en estaciones de aprendizaje, rincones, trabajo individual o refuerzo.', 1.75, 2, 2, 'numantyposseg.webp', 'activo', 5, '2026-04-10 12:40:52', 0, NULL, 0, NULL),
(58, 'De menor a mayor y de Mayor a menor - 2º', 'Este recurso está diseñado para trabajar de forma manipulativa y visual el orden numérico. Mediante tarjetas con series numéricas, el alumnado debe organizarlas según el criterio indicado: de menor a mayor o de mayor a menor.', '18 tarjetas con diferentes combinados de números de 2 cifras', 1.75, 2, 2, 'maymenmenmayseg.webp', 'activo', 5, '2026-04-10 12:47:16', 0, NULL, 1, NULL),
(59, 'Relación de números. Escritura y descomposición', 'Este recurso permite trabajar la relación entre el número en su forma numérica, su forma escrita en palabras y su descomposición en centenas, decenas y unidades. A través de tarjetas, el alumnado debe emparejar correctamente las tres representaciones de cada número, desarrollando la comprensión global del valor posicional y la lectoescritura numérica.', '16 tarjetas  de números de dos cifras con la escritura en palabras de cada número y la descomposición correspondiente (C-D-U).', 2.25, 2, 1, 'escridesseg.webp', 'activo', 5, '2026-04-10 12:47:16', 0, NULL, 0, NULL),
(60, 'Problemas con ruleta - 2º', 'Esta actividad consiste en un juego matemático manipulativo en el que el alumnado resuelve problemas a través de una ruleta. La propuesta se organiza mediante tarjetas identificadas con letras del abecedario, de manera que cada letra corresponde a un problema diferente.\r\n\r\nPara comenzar, el alumnado debe girar la ruleta. la letra en la que se detiene indica qué tarjeta debe coger. Una vez seleccionada, deberá leer el problema y resolverlo.', '27 tarjetas con problemas  de sumas y restas con y sin llevar.\r\n\r\nIdeal para usar en rincones, estaciones de aprendizaje, trabajo en pequeño grupo o como actividad de refuerzo.', 2.50, 2, 2, 'probrulseg.webp', 'activo', 5, '2026-04-10 12:49:00', 0, NULL, 0, NULL),
(61, '¿Es correcta la operación?', 'Esta actividad se basa en un conjunto de 30 tarjetas en las que aparecen operaciones de sumas y restas acompañadas de un resultado. El objetivo del alumnado es revisar cada operación, realizarla de nuevo y comprobar si el resultado mostrado es correcto o incorrecto.\r\n\r\nEl alumnado deberá resolver la operación por sí mismo, utilizando cálculo mental, papel y lápiz o material manipulativo. Una vez obtenida la solución, la comparará con el resultado que aparece en la tarjeta y decidirá si está bien o contiene un error.', '30 tarjetas con operaciones sumas y restas.\r\n\r\nEspacio en cada tarjeta para rodear la opción correcta.\r\n\r\nFormato ideal para plastificar y reutilizar.\r\n\r\nPerfecto para el trabajo autónomo, rincones matemáticos, estaciones de aprendizaje o refuerzo.', 1.50, 2, 1, 'escorrseg.webp', 'activo', 5, '2026-04-10 12:49:00', 0, NULL, 0, NULL),
(62, 'Descomposición de centenas decenas y unidades.', 'Este recurso permite al alumnado recomponer y representar números de hasta cuatro cifras a partir del uso del ábaco. Se trabajan diferentes niveles y formatos para identificar, asociar y construir números, reforzando así el valor posicional de cada cifra.', '18 tarjetas con representaciones en ábaco. El alumnado deberá elegir entre tres opciones cuál es el número correcto.\r\n\r\n18 tarjetas con bolitas en el ábaco. El alumnado debe escribir el número que representa.\r\n\r\n18 tarjetas con el número dado. El alumnado debe formar ese número colocando las bolitas correspondientes que indica en el ábaco.', 2.50, 2, 2, 'descendecunseg.webp', 'activo', 5, '2026-04-10 12:50:56', 0, NULL, 1, NULL),
(63, 'Tarjetas de calculo. Averigua las cifras que faltan', 'Actividad manipulativa de cálculo mental y razonamiento matemático para 2º de primaria en la que el alumnado deberá descubrir qué cifra falta en operaciones de suma y resta.\r\n\r\nA través de tarjetas visuales y dinámicas, los niños y niñas trabajan el valor posicional, la lógica matemática y la atención al detalle mientras resuelven pequeños retos numéricos.', '48 tarjetas de operaciones:\r\n\r\nNIVEL 1 (8 sumas y 8 restas sencillas, una sola cifra oculta).\r\n\r\nNIVEL 2 (8 sumas y 8 restas que siguen siendo accesibles pero que requieren mayor atención).\r\n\r\nNIVEL 3 (8 sumas y 8 restas con llevadas más complejas)\r\n\r\nIncluye plantilla autocorregible para que sean más autónomos.\r\n\r\nEs ideal para trabajar operaciones en pequeño grupo, estaciones de aprendizaje, o actividad de refuerzo y ampliación.', 2.75, 2, 2, 'avlascifseg.webp', 'activo', 5, '2026-04-10 12:50:56', 0, NULL, 0, NULL),
(64, 'Calculo 2º', 'Esta actividad consiste en una ruleta de sumas pensadas para primero de primaria, con el objetivo de reforzar el cálculo mental y escrito de forma visual y motivadora.\r\n\r\nEl alumnado debe observar la ruleta, resolver cada suma y completar el resultado en el hueco correspondiente.', '24 ruletas de sumas con 8 operaciones cada una.\r\n\r\nUn número en el centrom que será el número fijo de la suma.\r\n\r\nUn espacio en blanco en cada operación para que el alumnado escriba el resultado.\r\n\r\nPor detrás, la misma ruleta autocorregible.', 2.25, 2, 2, 'calculoseg.webp', 'activo', 5, '2026-04-10 12:52:59', 0, NULL, 0, NULL),
(65, 'Tarjetas mayor, menor e Igual - 2º', 'Este recurso ofrece una forma visual, divertida y muy práctica para que el alumnado aprenda a comparar números utilizando los símbolos de mayor, menor e igual. \r\n\r\nDeben observar dos cantidades y colocar el símbolo que corresponda para indicar si el número es mayor, menor o igual.', '30 tarjetas para identificar si el número es mayor, menor e igual.', 1.75, 2, 2, 'tarmaymenigseg.webp', 'activo', 5, '2026-04-10 12:52:59', 0, NULL, 3, 'recursos/productos/65/tarjetas-mayor-menor-e-igual-2o-1779111817.pdf'),
(66, 'Tarjetas ordinales del 1º al 29º', 'Esta actividad está diseñada para que el alumnado trabaje de forma visual, manipulativa y escrita los números ordinales del 1º al 29º, afianzando la relación entre la posición, el ordinal en número y el ordinal en letra.\r\n\r\nA través de tarjetas, el alumnado deberá observar una situación o consigna y escribir el número ordinal correspondiente tanto en su forma numérica como en su forma escrita.', 'Tarjetas individuales con consignas relacionadas con números ordinales.\r\n\r\nEspacios diferenciados para que el alumnado escriba (el  ordinal en número 1º,2º,3º ...) y el ordinal en letra (primero, segundo, tercero...)', 1.50, 2, 2, 'tarnumord1_29seg.webp', 'activo', 5, '2026-04-10 12:57:49', 0, NULL, 0, NULL),
(67, 'Puzzle números ordinales', 'Esta actividad está diseñada para que el alumnado trabaje de forma visual, manipulativa los números ordinales del 1º al 29º, relacionando el ordinal  escrito en letra con su representación en número.\r\n\r\nEl alumnado deberá unir correctamente las piezas del puzzle, encajando la parte que contiene el ordinal en letra con la pieza correspondiente del ordinal en número (1º,2º,3º...), favoreciendo así la comprensión y afianzamiento de este contenido matemático.', 'Piezas de puzzle con: números ordinales en letra y números ordinales en número.\r\n\r\nMaterial pensado para ser plastificado y reutilizado.', 1.50, 2, 2, 'puznumordseg.webp', 'activo', 5, '2026-04-10 12:57:49', 0, 'puzlenumordinales.mp4', 7, NULL),
(68, 'Unidades de millar, centenas, decenas y unidades ', 'Este recurso está diseñado para trabajar la descomposición numérica hasta las unidades de millar, reforzando el valor posicional de cada cifra del número. El alumnado debe clasificar correctamente en una plantilla las tarjetas con números representado en forma de cubo, barras, cuadrados que simbolizan los millares, centenas, decenas y unidades.', '42 tarjetas  con descomposición de números de 4 cifras.\r\n\r\nPlantilla para clasificar el número por posiciones (UM-C-D-U)\r\n\r\nIlustraciones de cubos, barras, cuadrados representando las unidades de millar, centenas, decenas y unidades.', 2.25, 2, 3, 'unmilcendecunter.webp', 'activo', 5, '2026-04-10 13:01:35', 0, NULL, 5, NULL),
(69, 'Juego, ¿Quién tiene multiplicaciones?', 'sta actividad está diseñada para reforzar el cálculo mental, la comprensión de la multiplicación y la agilidad matemática, a través del juego \"¿Quién tiene...?\".\r\n\r\nEl alumnado debe estar atento, resolver una multiplicación y reconocer cuándo tiene el resultado correcto para continuar la cadena del juego.', '32 tarjetas del juego \"¿Quién tiene....?\".\r\n\r\nCada tarjeta presenta una frase inicial  \"Yo tengo....(un número) y una pregunta \"¿Quién tiene...? (una multiplicación)\r\n\r\nMultiplicaciones adaptadas al nivel (tablas iniciales)\r\n\r\nDiseño claro y visual para facilitar el seguimiento del juego.\r\n\r\nFormato ideal para plastificar y trabajar de manera manipultativa en estaciones de aprendizaje.', 2.50, 2, 3, 'juequitimultter.webp', 'activo', 5, '2026-04-10 13:01:35', 0, NULL, 11, NULL),
(77, 'prueba', 'lo que seA', 'ÑLO', 1.00, 1, 1, 'default.png', 'activo', 5, '2026-05-28 16:27:47', 0, NULL, 3, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_clicks_metricas`
--

CREATE TABLE `productos_clicks_metricas` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_clicks_metricas`
--

INSERT INTO `productos_clicks_metricas` (`id`, `producto_id`, `usuario_id`, `fecha`) VALUES
(1, 12, NULL, '2026-05-20 10:09:47'),
(2, 2, NULL, '2026-05-20 10:09:51'),
(3, 12, NULL, '2026-05-20 10:09:58'),
(4, 54, NULL, '2026-05-20 10:10:17'),
(5, 2, 5, '2026-05-20 12:27:00'),
(6, 15, 5, '2026-05-20 12:29:15'),
(7, 2, 4, '2026-05-20 12:44:42'),
(8, 6, 5, '2026-05-20 15:15:17'),
(9, 9, 5, '2026-05-20 15:17:02'),
(10, 9, 5, '2026-05-20 15:43:34'),
(11, 9, 5, '2026-05-20 15:43:37'),
(12, 10, 5, '2026-05-20 16:04:04'),
(13, 10, 5, '2026-05-20 16:04:05'),
(14, 12, 5, '2026-05-20 16:17:34'),
(15, 5, 4, '2026-05-21 12:16:40'),
(16, 4, 4, '2026-05-21 12:19:07'),
(17, 4, 4, '2026-05-21 12:20:24'),
(18, 2, 5, '2026-05-21 15:56:46'),
(19, 2, NULL, '2026-05-21 15:57:01'),
(20, 2, 7, '2026-05-22 12:46:25'),
(21, 2, 7, '2026-05-22 12:46:40'),
(22, 5, 7, '2026-05-22 12:46:48'),
(23, 6, 7, '2026-05-22 12:47:42'),
(24, 6, 7, '2026-05-22 12:49:25'),
(25, 6, 7, '2026-05-22 12:55:19'),
(26, 2, 7, '2026-05-22 12:55:35'),
(27, 6, 7, '2026-05-22 12:55:47'),
(28, 6, 7, '2026-05-22 12:55:54'),
(29, 6, 7, '2026-05-22 12:59:03'),
(30, 6, 7, '2026-05-22 13:04:40'),
(31, 6, 7, '2026-05-22 13:04:53'),
(32, 5, 7, '2026-05-22 13:05:23'),
(33, 6, 7, '2026-05-22 13:09:40'),
(34, 6, NULL, '2026-05-25 11:07:13'),
(35, 9, NULL, '2026-05-25 11:07:18'),
(36, 9, NULL, '2026-05-25 11:28:41'),
(37, 12, NULL, '2026-05-25 11:28:48'),
(38, 13, NULL, '2026-05-25 11:28:54'),
(39, 13, NULL, '2026-05-25 12:03:30'),
(40, 65, 4, '2026-05-25 12:54:13'),
(41, 58, 4, '2026-05-25 12:54:48'),
(42, 14, 4, '2026-05-25 12:55:10'),
(43, 2, 4, '2026-05-25 12:56:24'),
(44, 2, 4, '2026-05-25 13:06:39'),
(45, 2, 4, '2026-05-25 13:06:49'),
(46, 2, 4, '2026-05-25 13:06:57'),
(47, 2, 4, '2026-05-25 13:08:26'),
(48, 2, 4, '2026-05-25 13:09:20'),
(49, 2, 4, '2026-05-25 13:09:33'),
(50, 4, 4, '2026-05-25 13:12:11'),
(51, 4, 4, '2026-05-25 13:25:10'),
(52, 4, 4, '2026-05-25 13:26:59'),
(53, 4, 4, '2026-05-25 13:29:26'),
(54, 4, 4, '2026-05-25 13:29:33'),
(55, 4, 4, '2026-05-25 13:31:18'),
(56, 4, 4, '2026-05-25 13:31:29'),
(57, 4, 4, '2026-05-25 13:31:33'),
(58, 4, 4, '2026-05-25 13:31:54'),
(59, 4, 4, '2026-05-25 13:32:05'),
(60, 4, 4, '2026-05-25 13:32:10'),
(61, 9, 4, '2026-05-25 13:32:26'),
(62, 2, 4, '2026-05-25 13:49:59'),
(63, 2, 4, '2026-05-25 13:50:07'),
(64, 2, 4, '2026-05-25 13:55:57'),
(65, 9, 4, '2026-05-25 13:56:35'),
(66, 7, 4, '2026-05-26 19:12:09'),
(67, 10, 4, '2026-05-26 19:12:13'),
(68, 9, 4, '2026-05-26 19:12:17'),
(69, 7, 4, '2026-05-26 19:12:32'),
(70, 7, 4, '2026-05-26 19:24:29'),
(71, 2, 4, '2026-05-26 19:24:37'),
(72, 8, 4, '2026-05-26 19:24:44'),
(73, 7, 4, '2026-05-26 19:24:58'),
(74, 65, 4, '2026-05-28 13:10:04'),
(75, 6, 4, '2026-05-28 13:10:23'),
(76, 2, 5, '2026-05-28 16:05:04'),
(77, 3, 5, '2026-05-28 16:06:55'),
(78, 2, 5, '2026-05-28 16:07:08'),
(79, 2, 5, '2026-05-28 16:07:48'),
(80, 2, 5, '2026-05-28 16:08:22'),
(81, 2, 5, '2026-05-28 16:08:28'),
(82, 4, 5, '2026-05-28 16:09:02'),
(83, 4, 4, '2026-05-28 16:13:10'),
(84, 4, 4, '2026-05-28 16:18:07'),
(85, 6, 4, '2026-05-28 16:19:41'),
(86, 2, 5, '2026-05-28 16:42:46'),
(87, 2, 7, '2026-05-28 16:44:14'),
(88, 2, 6, '2026-05-28 18:01:27'),
(89, 2, 6, '2026-05-28 18:08:53'),
(90, 2, 6, '2026-05-28 18:09:06'),
(91, 6, 9, '2026-05-28 18:09:44'),
(92, 2, 9, '2026-05-28 18:10:03'),
(93, 2, 4, '2026-05-28 18:19:48'),
(94, 6, 4, '2026-05-28 18:59:46'),
(95, 65, 4, '2026-05-28 19:19:21'),
(96, 68, 4, '2026-05-29 09:35:15'),
(97, 39, 4, '2026-05-29 09:35:29'),
(98, 4, 4, '2026-05-29 09:35:32'),
(99, 5, 4, '2026-05-29 09:35:35'),
(100, 9, 4, '2026-05-29 09:35:39'),
(101, 54, 4, '2026-05-29 09:35:49'),
(102, 2, 6, '2026-05-29 11:56:40'),
(103, 2, 6, '2026-05-29 12:08:56'),
(104, 2, 9, '2026-05-29 12:24:12'),
(105, 2, 9, '2026-05-29 12:28:54'),
(106, 67, 9, '2026-05-29 12:29:25'),
(107, 67, 9, '2026-05-29 12:30:13'),
(108, 67, 9, '2026-05-29 12:31:03'),
(109, 3, 9, '2026-05-29 12:31:04'),
(110, 67, 9, '2026-05-29 12:31:08'),
(111, 3, 9, '2026-05-29 12:31:09'),
(112, 67, 9, '2026-05-29 12:31:13'),
(113, 3, 9, '2026-05-29 12:31:14'),
(114, 67, 9, '2026-05-29 12:31:17'),
(115, 67, 9, '2026-05-29 12:31:51'),
(116, 2, 9, '2026-05-29 12:31:53'),
(117, 3, 9, '2026-05-29 12:31:58'),
(118, 2, 9, '2026-05-29 12:32:00'),
(119, 3, 9, '2026-05-29 12:32:02'),
(120, 2, 9, '2026-05-29 12:32:03'),
(121, 3, 9, '2026-05-29 12:32:05'),
(122, 2, 9, '2026-05-29 12:32:20'),
(123, 3, 9, '2026-05-29 12:32:28'),
(124, 2, 9, '2026-05-29 12:32:31'),
(125, 3, 9, '2026-05-29 12:32:31'),
(126, 5, 9, '2026-05-29 12:32:34'),
(127, 4, 9, '2026-05-29 12:32:38'),
(128, 5, 9, '2026-05-29 12:32:40'),
(129, 77, 9, '2026-05-29 12:33:12'),
(130, 77, 9, '2026-05-29 12:33:19'),
(131, 69, 9, '2026-05-29 12:33:30'),
(132, 3, 9, '2026-05-29 12:33:33'),
(133, 4, 9, '2026-05-29 12:33:38'),
(134, 3, 9, '2026-05-29 12:33:41'),
(135, 4, 9, '2026-05-29 12:33:43'),
(136, 4, 9, '2026-05-29 12:39:07'),
(137, 2, 9, '2026-05-29 12:39:11'),
(138, 4, 9, '2026-05-29 12:39:13'),
(139, 4, 9, '2026-05-29 12:39:15'),
(140, 2, 9, '2026-05-29 12:39:17'),
(141, 2, 9, '2026-05-29 12:39:30'),
(142, 4, 9, '2026-05-29 12:39:33'),
(143, 2, 9, '2026-05-29 12:39:35'),
(144, 4, 9, '2026-05-29 12:39:37'),
(145, 2, 9, '2026-05-29 12:39:39'),
(146, 69, 9, '2026-05-29 12:42:01'),
(147, 2, 9, '2026-05-29 12:42:03'),
(148, 2, 9, '2026-05-29 12:42:08'),
(149, 4, 9, '2026-05-29 12:42:11'),
(150, 6, 9, '2026-05-29 12:42:15'),
(151, 62, 9, '2026-05-29 12:42:30'),
(152, 5, 9, '2026-05-29 12:42:34'),
(153, 5, 9, '2026-05-29 12:42:38'),
(154, 6, 9, '2026-05-29 12:42:42'),
(155, 6, 9, '2026-05-29 12:43:51'),
(156, 4, 9, '2026-05-29 12:44:01'),
(157, 6, 9, '2026-05-29 12:44:06'),
(158, 3, 9, '2026-05-29 12:44:08'),
(159, 4, 9, '2026-05-29 12:44:13'),
(160, 4, 9, '2026-05-29 12:44:29'),
(161, 3, 9, '2026-05-29 12:44:30'),
(162, 6, 9, '2026-05-29 12:44:30'),
(163, 3, 9, '2026-05-29 12:44:32'),
(164, 69, 9, '2026-05-29 12:55:01'),
(165, 2, 9, '2026-05-29 12:55:05'),
(166, 69, 9, '2026-05-29 12:55:07'),
(167, 2, 9, '2026-05-29 12:55:09'),
(168, 3, 9, '2026-05-29 12:55:12'),
(169, 2, 9, '2026-05-29 12:55:14'),
(170, 3, 9, '2026-05-29 12:55:16'),
(171, 6, 9, '2026-05-29 12:55:23'),
(172, 5, 9, '2026-05-29 12:55:27'),
(173, 4, 9, '2026-05-29 12:55:30'),
(174, 3, 9, '2026-05-29 12:55:33'),
(175, 2, 9, '2026-05-29 12:55:36'),
(176, 3, 9, '2026-05-29 12:55:40'),
(177, 5, 9, '2026-05-29 12:55:44'),
(178, 4, 9, '2026-05-29 12:55:48'),
(179, 5, 9, '2026-05-29 12:55:52'),
(180, 4, 9, '2026-05-29 12:55:55'),
(181, 6, 9, '2026-05-29 12:56:00'),
(182, 3, 9, '2026-05-29 12:56:03'),
(183, 2, 9, '2026-05-29 12:56:08'),
(184, 3, 9, '2026-05-29 12:56:13'),
(185, 3, 9, '2026-05-29 12:57:01'),
(186, 2, 9, '2026-05-29 12:57:04'),
(187, 4, 9, '2026-05-29 12:57:08'),
(188, 2, 9, '2026-05-29 12:57:14'),
(189, 4, 9, '2026-05-29 12:57:16'),
(190, 5, 9, '2026-05-29 12:57:17'),
(191, 4, 9, '2026-05-29 12:57:21'),
(192, 6, 9, '2026-05-29 12:57:23'),
(193, 3, 9, '2026-05-29 12:57:54'),
(194, 4, 9, '2026-05-29 12:58:00'),
(195, 4, 9, '2026-05-29 12:58:10'),
(196, 3, 9, '2026-05-29 12:58:11'),
(197, 3, 9, '2026-05-29 12:59:11'),
(198, 6, 9, '2026-05-29 12:59:53'),
(199, 4, 9, '2026-05-29 13:07:02'),
(200, 2, 9, '2026-05-29 13:21:48'),
(201, 69, NULL, '2026-05-29 13:23:35'),
(202, 77, 4, '2026-05-29 13:29:08'),
(203, 69, 4, '2026-05-29 13:29:16'),
(204, 69, 4, '2026-05-29 13:32:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_archivos`
--

CREATE TABLE `producto_archivos` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `archivo_url` varchar(255) NOT NULL,
  `archivo_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_gratuitos`
--

CREATE TABLE `recursos_gratuitos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `url_drive` text NOT NULL,
  `formato` varchar(50) DEFAULT 'Drive',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `clicks` int(11) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `descargas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recursos_gratuitos`
--

INSERT INTO `recursos_gratuitos` (`id`, `titulo`, `categoria_id`, `imagen`, `url_drive`, `formato`, `estado`, `clicks`, `fecha_creacion`, `descargas`) VALUES
(1, 'Horarios visuales', 1, 'horarios.png', 'https://drive.google.com/file/d/1zAjyHFC5F0gJPcJcLFAczc8AkZpfy07q/view', 'pdf', 'activo', 0, '2026-05-15 12:52:30', 0),
(2, 'Secuencias visuales ', 2, 'secuencias.png', 'https://drive.google.com/file/d/1QAxUrzoOIkbBoVRJjA6bnCtZtC8AYE5T/view', 'pdf', 'activo', 0, '2026-05-15 12:52:30', 0),
(3, 'Paneles visuales de actividades', 3, 'panelesv.png', 'https://drive.google.com/file/d/177_CvXUM7tCPNi5DQKUSrP_8Mnz5OanC/view', 'pdf', 'activo', 0, '2026-05-15 12:56:21', 0),
(4, 'Llavero conductas y anticipacion', 4, 'llaveroconductas.png', 'https://drive.google.com/file/d/1Ttt6mcll6wg4MjKwO9n4qtrQ_3L9e05L/view', 'pdf', 'activo', 0, '2026-05-15 12:56:21', 0),
(5, 'Pasos al realizar tareas', 5, 'pasoapasoact.png', 'https://drive.google.com/file/d/1-tpc7Ld5QB3nzXDugf4tc2W0tfpt7s15/view', 'pdf', 'activo', 0, '2026-05-15 12:58:02', 0),
(6, 'Apoyo visual en mesa', 6, 'apovisualmesa.png', 'https://drive.google.com/file/d/1SvGslp_n1g5N9beWv2itpV28djkcUP_8/view', 'Drive', 'activo', 0, '2026-05-15 12:58:02', 0),
(7, 'Palabra secreta', 7, 'palabrasecreta.png', 'https://drive.google.com/file/d/1pm8VH77-1UMCOEms_lGIuAyE1mg0wYbb/view', 'pdf', 'activo', 0, '2026-05-15 12:59:46', 0),
(8, 'Puzzle Silábico', 7, 'puzzlesilabico.png', 'https://drive.google.com/file/d/13dkRwvgQ52CgTIYdxSt3FP5-DlXmttHZ/view', 'pdf', 'activo', 0, '2026-05-15 12:59:46', 0),
(9, 'Bingo fonético', 7, 'bingofonetico.png', 'https://drive.google.com/file/d/184R4G5EHmxl7-pet0D1yi87dYurqTZLN/view', 'pdf', 'activo', 0, '2026-05-15 13:01:22', 0),
(10, 'Ruleta de sonidos', 7, 'ruletasonidos.png', 'https://drive.google.com/file/d/1gObS5BMIyblX_yQ2qGuqDXp2qdAjxDgz/view', 'Drive', 'activo', 0, '2026-05-15 13:01:22', 0),
(11, 'Lectoescritura de las partes de la casa', 7, 'lectoescritura.png', 'https://drive.google.com/file/d/1sHTuxUQJBLuR_UBQFafPVtXcf4qVFbCn/view', 'Drive', 'activo', 0, '2026-05-15 13:02:57', 0),
(12, 'Escritura de palabras partes del cuerpo', 7, 'escritura', 'https://drive.google.com/file/d/1RNQllNbGEC1tStYJVM5kSyT4G5LNRsuc/view', 'pdf', 'activo', 0, '2026-05-15 13:02:57', 0),
(13, 'Antónimos', 7, 'antonimos.png', 'https://drive.google.com/file/d/1FaSbKkYOl47UK2vyUflIG-OvRnMWGLmB/view', 'Drive', 'activo', 0, '2026-05-15 13:04:58', 0),
(14, 'Memory Imagen-Palabra', 8, 'memory-imagen-palabra.png', 'https://drive.google.com/file/d/1-oCbftFBISFWyWS46kBhDEB3h_lPeQF_/view', 'Drive', 'activo', 0, '2026-05-15 13:04:58', 0),
(15, 'Memory planetas', 8, 'memoryplanetas.png', 'https://drive.google.com/file/d/18haIcj-kak_FujcK7p6LZS3Tf1uPfrV8/view', 'Drive', 'activo', 0, '2026-05-15 13:06:33', 0),
(16, 'Si yo tengo...¿Quién tiene?', 8, 'siyotengo.png', 'https://drive.google.com/file/d/1wIQvdmycF1p0rhJZEJBha5Fv59v1A69G/view', 'pdf', 'activo', 0, '2026-05-15 13:06:33', 0),
(17, 'Calendario adviento', 8, 'calendarioadviento.png', 'https://drive.google.com/file/d/1QeSRn2WeqS2lyc_nn9alkVhFRChaz-2e/view', 'Drive', 'activo', 0, '2026-05-15 13:08:23', 0),
(18, 'Doble de Navidad', 8, 'doblenavidad.png', 'https://drive.google.com/file/d/1vjDWk40eWej5YbcdX7aR8k3anr4C9mRJ/view', 'Drive', 'activo', 0, '2026-05-15 13:08:23', 0),
(19, 'Bingo y ruleta de navidad', 8, 'bingonavideño.png', 'https://drive.google.com/file/d/1BRzKqDbdg3RLicGdQCXtH3Cq7Iyk9Dcz/view', 'pdf', 'activo', 0, '2026-05-15 13:10:45', 0),
(20, 'Tarjetas lectoescritura de Navidad', 8, 'talectoescrituranavidad.png', 'https://drive.google.com/file/d/1MOAujl20MChYuhukOWl7LyeKBDazPhDe/view', 'Drive', 'activo', 1, '2026-05-15 13:10:45', 0),
(21, 'Series logicas Navidad', 8, 'serieslogicasnavidad.png', 'https://drive.google.com/file/d/1VZSrrei5OpCdZt-Gauf_y5LQ3Vd5A1K8/view', 'Drive', 'activo', 0, '2026-05-15 13:12:31', 0),
(22, 'Descripción de los planetas', 9, 'planetas.png', 'https://drive.google.com/file/d/1EHzUgFVrxkN0m0J867C6qZf91rSGBYu5/view', 'Drive', 'activo', 0, '2026-05-15 13:12:31', 0),
(23, 'Descripción de animales', 9, 'animales.png', 'https://drive.google.com/file/d/1sz99RFb21Zrq0jeGUDENQPk-L6lC6lPl/view', 'Drive', 'activo', 0, '2026-05-15 13:13:37', 0),
(24, 'Descripción de paisajes', 9, 'paisajes.png', 'https://drive.google.com/file/d/1DP-rSx63RITBn6mJxG0eb8l1_X4SBpeT/view', 'pdf', 'activo', 0, '2026-05-15 13:13:37', 0),
(25, 'Dado, \"Mi cuerpo por dentro y por fuera\"', 9, 'cuerpo.png', 'https://drive.google.com/file/d/17SwtCCuXa7c_YYyw-0ZV1jbL79gWAsM2/view', 'Drive', 'activo', 0, '2026-05-15 13:15:19', 0),
(26, 'Recetario', 9, 'recetario.png', 'https://drive.google.com/file/d/1ag_05AaV_Lp53zo4qjyuU6jPto4AHXG0/view', 'pdf', 'activo', 0, '2026-05-15 13:15:19', 0),
(27, 'Asociar número - Cantidad prendas de vestir', 9, 'asociarnumero.png', 'https://drive.google.com/file/d/1-tzwc7Jt6UCfhf3aWwq2Pre5wm0hOnsc/view', 'Drive', 'activo', 0, '2026-05-15 13:16:51', 0),
(28, 'Conteo otoño', 9, 'conteootoño.png', 'https://drive.google.com/file/d/1zHDgdUeAn5RViCCWyS8VbAPQQh3PiWch/view', 'Drive', 'activo', 0, '2026-05-15 13:16:51', 0),
(29, 'Series lógicas \"Primavera\"', 9, 'seriesprimavera.png', 'https://drive.google.com/file/d/1wwyfPrJi5r8x4u08ala1CHywjWKWH2-X/view', 'Drive', 'activo', 0, '2026-05-15 13:20:53', 0),
(30, 'Restas sencillas', 10, 'restassen.png', 'https://drive.google.com/file/d/1CO9ud79SE14JiD7PV1c1vNwzI3YP6YaV/view', 'Drive', 'activo', 0, '2026-05-15 13:20:53', 0),
(31, 'Plantilla figutas geométricas', 10, 'figuras.png', 'https://drive.google.com/file/d/1Fwcv_4HWMJE9QFYeDVAkfs25ThuAa1d2/view', 'pdf', 'activo', 1, '2026-05-15 13:22:31', 0),
(32, 'Encuentra la forma geométrica', 10, 'formacorrecta.png', 'https://drive.google.com/file/d/1F_juSlUVA7Ruk36n80X_TIQffK8BXRds/view', 'pdf', 'activo', 0, '2026-05-15 13:22:31', 0),
(33, 'Doble de números', 10, 'dobledenumeros.png', 'https://drive.google.com/file/d/1bnU9i3dcr3cm4TQC-tEIxv0UW2Y8UtcO/view', 'pdf', 'activo', 1, '2026-05-15 13:23:58', 0),
(34, 'Memory. Números del 1 al 10', 10, 'memorynumeros.png', 'https://drive.google.com/file/d/18-ppXFx2_9X-tJzIHzCEms_LRKIrdFEZ/view', 'pdf', 'activo', 1, '2026-05-15 13:23:58', 0),
(35, 'Recta numérica', 10, 'rectanumerica.png', 'https://drive.google.com/file/d/1qm30HIlYQFMINYJQ-144OTxD_2Z3Bgzl/view', 'pdf', 'activo', 0, '2026-05-15 13:25:41', 0),
(36, 'Apoyo visual de números en mesa', 10, 'apoyovisualnumerosyseries.png', 'https://drive.google.com/file/d/1CizfWrG_wDBSQkr7N5WW5eHkyDLzrE7h/view', 'pdf', 'activo', 0, '2026-05-15 13:25:41', 0),
(37, 'Numero aventurero', 10, 'numeroaventurero.png', 'https://drive.google.com/file/d/1-BmS-84pnqOhF6ykUghvclL-MKMDFkWb/view', 'pdf', 'activo', 0, '2026-05-15 13:27:15', 0),
(38, 'Juegos de mesa. \"Sumas y restas\" diferentes niveles', 10, 'juegomesasumasyrestas.png', 'https://drive.google.com/file/d/14rAPIpyVB8CA1xBSijtBgTSyT0OWZ8gb/view', 'pdf', 'activo', 0, '2026-05-15 13:27:15', 0),
(39, 'Sumas sencillas', 10, 'sumassencillas.png', 'https://drive.google.com/file/d/1yinglPAOXrd43-jC7jz3d45upjJnMvsx/view', 'pdf', 'activo', 0, '2026-05-15 13:29:53', 0),
(40, '¿Qué números faltan?', 10, 'quenumfalta.png', 'https://drive.google.com/file/d/1F_0T0JBJMmzfN9V1FDiUzjuYJD8jyumh/view', 'pdf', 'activo', 0, '2026-05-15 13:29:53', 0),
(41, 'Plantillas, \"Número anterior y posterior\"', 10, 'numantypostplantilla.png', 'https://drive.google.com/file/d/1l3Z2hfA9ZW2ArFtOh6I1PFKxHsDB7dXH/view', 'pdf', 'activo', 0, '2026-05-15 13:32:05', 0),
(42, 'Memory, \"Centenas, decenas y unidades\"', 10, 'memorycentenadecyun.png', 'https://drive.google.com/file/d/15DreZ8-27w5TjqMgMpMz3Y05u_t1xOiY/view', 'PDF', 'activo', 1, '2026-05-15 13:32:05', 0),
(43, 'Memory. \"Decenas y unidades\"', 10, 'memorydecyun.png', 'https://drive.google.com/file/d/1wG_wRT6Q23_DDoj1tU202wi9p83H5oGR/view', 'pdf', 'activo', 0, '2026-05-15 13:36:37', 0),
(44, 'Descomposición de números. \"Abeja\"', 10, 'descomposicionnumabejas.png', 'https://drive.google.com/file/d/1T6E7v3YmdhdXyQJ8Mk7RAPMGYjhHWpv1/view', 'pdf', 'activo', 0, '2026-05-15 13:36:37', 0),
(45, 'Memorym \"Multiplicaciones\"', 10, 'memorymultiplicaciones.png', 'https://drive.google.com/file/d/1z8rsUqWvBcA_t1OBqqBEQjA-82qKZHHn/view', 'pdf', 'activo', 2, '2026-05-15 13:37:59', 0),
(46, 'Secuencias temporales', 10, 'secuenciastemporales.png', 'https://drive.google.com/file/d/13wneVTWMaV1EY86pltSHIeYpHMH4TNNg/view', 'Drive', 'activo', 0, '2026-05-15 13:37:59', 0),
(47, '¿Kilogramos o gramos?', 10, 'kgog.png', 'https://drive.google.com/file/d/1cW9gHQamj1PGz8XsFcihm_ZDFwc6gDMq/view', 'pdf', 'activo', 1, '2026-05-15 13:39:53', 0),
(48, 'Lista de la compra', 10, 'listadelacompra.png', 'https://drive.google.com/file/d/1nkwBxmEbZ4eYckQRclDlDz9q99x54cXY/view', 'Drive', 'activo', 5, '2026-05-15 13:39:53', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_gratuitos_metricas`
--

CREATE TABLE `recursos_gratuitos_metricas` (
  `id` int(11) NOT NULL,
  `recurso_id` int(11) NOT NULL,
  `tipo` enum('click','descarga') NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recursos_gratuitos_metricas`
--

INSERT INTO `recursos_gratuitos_metricas` (`id`, `recurso_id`, `tipo`, `fecha`) VALUES
(8, 31, 'click', '2026-05-20 10:09:28'),
(7, 33, 'click', '2026-05-20 10:09:24'),
(9, 34, 'click', '2026-05-20 10:09:31'),
(2, 42, 'click', '2026-05-19 15:09:35'),
(1, 45, 'click', '2026-05-19 15:09:30'),
(3, 48, 'click', '2026-05-19 15:09:38'),
(4, 48, 'click', '2026-05-19 16:15:30'),
(5, 48, 'click', '2026-05-19 16:15:34'),
(6, 48, 'click', '2026-05-19 16:15:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reseñas`
--

CREATE TABLE `reseñas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `puntuacion` int(11) DEFAULT NULL CHECK (`puntuacion` between 1 and 5),
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('visible','oculta','denunciada') DEFAULT 'visible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reseñas`
--

INSERT INTO `reseñas` (`id`, `usuario_id`, `producto_id`, `comentario`, `puntuacion`, `fecha`, `estado`) VALUES
(2, 4, 2, 'dfdsf', 5, '2026-05-14 14:35:55', 'denunciada'),
(3, 9, 2, 'DTRTE', 5, '2026-05-29 10:24:08', 'visible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(3, 'cliente'),
(2, 'profesor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte_mensajes`
--

CREATE TABLE `soporte_mensajes` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `remitente` enum('usuario','admin') NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `remitente_nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `soporte_mensajes`
--

INSERT INTO `soporte_mensajes` (`id`, `ticket_id`, `remitente`, `mensaje`, `fecha`, `remitente_nombre`) VALUES
(1, 1, 'usuario', 'Esto es una prueba', '2026-05-04 14:15:10', NULL),
(2, 2, 'usuario', 'sdfsdf', '2026-05-12 14:17:21', NULL),
(3, 2, 'admin', 'rdt', '2026-05-12 15:03:36', 'Jennifer'),
(4, 2, 'usuario', 'dgfd', '2026-05-13 11:21:31', 'Raul'),
(5, 2, 'usuario', 'fsdf', '2026-05-14 07:51:08', 'Raul'),
(6, 3, 'usuario', 'xcb', '2026-05-14 09:59:48', NULL),
(7, 4, 'usuario', 'FHGF', '2026-05-14 10:20:32', NULL),
(8, 5, 'usuario', 'BJHG', '2026-05-14 14:26:58', NULL),
(9, 5, 'admin', 'vcb', '2026-05-21 10:36:40', 'Jennifer'),
(10, 6, 'usuario', 'SDFASDFASDFSD', '2026-05-22 11:36:16', NULL),
(11, 7, 'usuario', 'Hola, se puede adaptar a niños mayores?', '2026-05-28 14:06:45', NULL),
(12, 8, 'usuario', 'hola quiero algo similar para autismo', '2026-05-29 11:34:18', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte_tickets`
--

CREATE TABLE `soporte_tickets` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `estado` enum('abierto','respondido','cerrado') DEFAULT 'abierto',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `soporte_tickets`
--

INSERT INTO `soporte_tickets` (`id`, `usuario_id`, `asunto`, `estado`, `fecha`) VALUES
(1, 4, 'Hola Raquel', 'cerrado', '2026-05-04 14:15:10'),
(2, 4, 'dfsd', 'cerrado', '2026-05-12 14:17:21'),
(3, 4, 'Consulta sobre: RESTAS DECENAS Y UNIDADES 1º', 'cerrado', '2026-05-14 09:59:48'),
(4, 4, 'FH', 'cerrado', '2026-05-14 10:20:32'),
(5, 5, 'Consulta sobre: Unidades de millar, centenas, decenas y unidades ', 'respondido', '2026-05-14 14:26:58'),
(6, 7, 'FSDFSADFSADF', 'abierto', '2026-05-22 11:36:16'),
(7, 5, 'Consulta sobre: Detective matemático 1º', 'abierto', '2026-05-28 14:06:45'),
(8, 4, 'Consulta sobre: Juego, ¿Quién tiene multiplicaciones?', 'abierto', '2026-05-29 11:34:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencias`
--

CREATE TABLE `sugerencias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `leida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sugerencias`
--

INSERT INTO `sugerencias` (`id`, `usuario_id`, `mensaje`, `fecha`, `leida`) VALUES
(1, 4, 'gfhgf', '2026-05-14 13:07:48', 0),
(2, 4, 'fsdfsadf', '2026-05-25 14:23:43', 0),
(3, 9, 'fgfdgfsd', '2026-05-29 14:21:12', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `auth_provider` varchar(30) NOT NULL DEFAULT 'local',
  `avatar` varchar(500) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `puede_resenar` tinyint(1) NOT NULL DEFAULT 1,
  `localidad` varchar(15) DEFAULT NULL,
  `cp` char(5) DEFAULT NULL,
  `apellidos` varchar(30) DEFAULT NULL,
  `acceso_actual` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `ultimo_ip` varchar(45) DEFAULT NULL,
  `password_changed_at` datetime DEFAULT NULL,
  `session_version` int(11) NOT NULL DEFAULT 1,
  `requiere_cambio_password` tinyint(1) NOT NULL DEFAULT 0,
  `acceso_actual_ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `google_id`, `auth_provider`, `avatar`, `password_hash`, `rol_id`, `activo`, `fecha_registro`, `puede_resenar`, `localidad`, `cp`, `apellidos`, `acceso_actual`, `ultimo_acceso`, `ultimo_ip`, `password_changed_at`, `session_version`, `requiere_cambio_password`, `acceso_actual_ip`) VALUES
(4, 'Raul', 'raulanta83@gmail.com', NULL, 'local', NULL, '$2y$10$k30/KwS4pqIsT3p56fN9HelwVgniYXYQBdg546Rw.5eKCOYNRtaGe', 3, 1, '2026-04-30 16:55:12', 0, NULL, NULL, NULL, '2026-05-29 13:37:03', '2026-05-29 13:25:35', '::1', NULL, 5, 1, '::1'),
(5, 'Jennifer', 'jenfol92@gmail.com', NULL, 'local', NULL, '$2y$10$PsnjwrgOU84cvhkW7Q4n.uJ5M6NH1iLWscsagTyl3y4w/rc6WgJZC', 1, 1, '2026-05-04 16:30:43', 1, NULL, NULL, NULL, '2026-05-28 16:39:52', '2026-05-28 16:24:58', '::1', NULL, 1, 0, '::1'),
(6, 'Raquel ', 'unrinconmaravillosodept@gmail.com', '101984639793956238688', 'google', 'https://lh3.googleusercontent.com/a/ACg8ocK6N6IXScga02OhcgsNhZLPO-QyNfCB7lJb39SHoGAmuj6r4EY=s96-c', '$2y$10$M4o4R4HbuMTzhfO/XU3UxedwnGNE/XJm5ebHqNR/K4Es5hlUCcuWS', 1, 1, '2026-05-04 16:42:26', 1, NULL, NULL, NULL, '2026-05-29 13:38:23', '2026-05-29 09:36:55', '::1', NULL, 1, 0, '::1'),
(7, 'jenn', 'jenfolalc@alu.edu.gva.es', NULL, 'local', NULL, '$2y$10$RcXuQh7dp6LN1iBv8UusVOemD5qbiFCTiiNmIDF/hfsFANhnwKjyC', 3, 1, '2026-05-22 11:36:24', 1, 'crevillente', '03330', 'f', '2026-05-28 16:43:52', NULL, NULL, NULL, 1, 0, '::1'),
(8, 'Raquel', 'unmundomaravillosodept@gmail.com', NULL, 'local', NULL, '$2y$10$NHIcLPY/x3ihNSpJQvqk5uX0AQStVLhV.DrlcIRPOUMprPDneAdVW', 1, 1, '2026-05-22 16:28:34', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL),
(9, 'Pepito', 'formaciondeocioytiempolibre@gmail.com', NULL, 'local', NULL, '$2y$10$/Dq45Be0uK0Zvg034BcbK..OvU/UDNLrZJyLZK3vSfABujkIrQUPa', 3, 1, '2026-05-28 16:39:08', 1, 'crevillente', '03330', 'lopez', '2026-05-29 14:18:26', '2026-05-29 12:24:00', '::1', NULL, 1, 0, '::1');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas_seguridad`
--
ALTER TABLE `alertas_seguridad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `idx_carrito_usuario` (`usuario_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `categorias_gratuitas`
--
ALTER TABLE `categorias_gratuitas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contacto_mensajes`
--
ALTER TABLE `contacto_mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `descargas`
--
ALTER TABLE `descargas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_descarga` (`token_descarga`),
  ADD KEY `idx_descargas_usuario` (`usuario_id`),
  ADD KEY `idx_descargas_producto` (`producto_id`),
  ADD KEY `idx_token_descarga` (`token_descarga`),
  ADD KEY `idx_descargas_pedido_producto` (`pedido_id`,`producto_id`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `idx_detalle_pedido` (`pedido_id`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `ips_sospechosas`
--
ALTER TABLE `ips_sospechosas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_password_resets_usuario` (`usuario_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedidos_usuario` (`usuario_id`),
  ADD KEY `idx_pedidos_estado` (`estado`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_productos_categoria` (`categoria_id`),
  ADD KEY `idx_productos_nivel` (`nivel_id`);

--
-- Indices de la tabla `productos_clicks_metricas`
--
ALTER TABLE `productos_clicks_metricas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_producto_fecha` (`producto_id`,`fecha`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indices de la tabla `producto_archivos`
--
ALTER TABLE `producto_archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `recursos_gratuitos`
--
ALTER TABLE `recursos_gratuitos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `recursos_gratuitos_metricas`
--
ALTER TABLE `recursos_gratuitos_metricas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recurso_tipo_fecha` (`recurso_id`,`tipo`,`fecha`);

--
-- Indices de la tabla `reseñas`
--
ALTER TABLE `reseñas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `soporte_mensajes`
--
ALTER TABLE `soporte_mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `soporte_tickets`
--
ALTER TABLE `soporte_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uq_usuarios_google_id` (`google_id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas_seguridad`
--
ALTER TABLE `alertas_seguridad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `categorias_gratuitas`
--
ALTER TABLE `categorias_gratuitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `contacto_mensajes`
--
ALTER TABLE `contacto_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `descargas`
--
ALTER TABLE `descargas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `ips_sospechosas`
--
ALTER TABLE `ips_sospechosas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `productos_clicks_metricas`
--
ALTER TABLE `productos_clicks_metricas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT de la tabla `producto_archivos`
--
ALTER TABLE `producto_archivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recursos_gratuitos`
--
ALTER TABLE `recursos_gratuitos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `recursos_gratuitos_metricas`
--
ALTER TABLE `recursos_gratuitos_metricas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `reseñas`
--
ALTER TABLE `reseñas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `soporte_mensajes`
--
ALTER TABLE `soporte_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `soporte_tickets`
--
ALTER TABLE `soporte_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `descargas`
--
ALTER TABLE `descargas`
  ADD CONSTRAINT `descargas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `descargas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`);

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`id`);

--
-- Filtros para la tabla `productos_clicks_metricas`
--
ALTER TABLE `productos_clicks_metricas`
  ADD CONSTRAINT `fk_productos_clicks_metricas_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `producto_archivos`
--
ALTER TABLE `producto_archivos`
  ADD CONSTRAINT `producto_archivos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recursos_gratuitos`
--
ALTER TABLE `recursos_gratuitos`
  ADD CONSTRAINT `recursos_gratuitos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_gratuitas` (`id`);

--
-- Filtros para la tabla `recursos_gratuitos_metricas`
--
ALTER TABLE `recursos_gratuitos_metricas`
  ADD CONSTRAINT `fk_metricas_recurso_gratuito` FOREIGN KEY (`recurso_id`) REFERENCES `recursos_gratuitos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reseñas`
--
ALTER TABLE `reseñas`
  ADD CONSTRAINT `reseñas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reseñas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
