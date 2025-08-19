/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Tabla: roles
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Tabla: usuarios
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `rol_id` int NOT NULL,
  `confirmado` tinyint(1) DEFAULT '0',
  `token` varchar(13) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4;

-- Tabla: membresias
CREATE TABLE `membresias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text,
  `beneficios` text,
  `descuento` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Tabla: clientes
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

-- Tabla: colaboradores
CREATE TABLE `colaboradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `colaboradores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- Tabla: familiares
CREATE TABLE `familiares` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `parentesco` varchar(50) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `familiares_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- Tabla: citas
CREATE TABLE `citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `colaborador_id` int DEFAULT NULL,
  `familiar_id` int DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `colaborador_id` (`colaborador_id`),
  KEY `familiar_id` (`familiar_id`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`familiar_id`) REFERENCES `familiares` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4;

-- Tabla: citas_cancelaciones
CREATE TABLE `citas_cancelaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cita_id` int NOT NULL,
  `motivo` text NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `citas_cancelaciones_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4;

-- Tabla: citas_servicios
CREATE TABLE `citas_servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cita_id` int NOT NULL,
  `servicio_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cita_id` (`cita_id`),
  KEY `servicio_id` (`servicio_id`),
  CONSTRAINT `citas_servicios_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_servicios_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4;

-- Tabla: clientes_membresias
CREATE TABLE `clientes_membresias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `membresia_id` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `membresia_id` (`membresia_id`),
  CONSTRAINT `clientes_membresias_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clientes_membresias_ibfk_2` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Tabla: historiales_tratamientos
CREATE TABLE `historiales_tratamientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `colaborador_id` int DEFAULT NULL,
  `servicio_id` int DEFAULT NULL,
  `fecha` date NOT NULL,
  `notas` text,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `colaborador_id` (`colaborador_id`),
  KEY `servicio_id` (`servicio_id`),
  CONSTRAINT `historiales_tratamientos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `historiales_tratamientos_ibfk_2` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `historiales_tratamientos_ibfk_3` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4;

-- Tabla: proveedores
CREATE TABLE `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- Tabla: inventario
CREATE TABLE `inventario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `proveedor_id` int DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- Tabla: recordatorios
CREATE TABLE `recordatorios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `cita_id` int NOT NULL,
  `fecha` date NOT NULL,
  `enviado` tinyint(1) DEFAULT '0',
  `medio` enum('email','sms') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `recordatorios_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recordatorios_ibfk_2` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

-- Tabla: servicios
CREATE TABLE `servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- Inserciones de datos
INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(3, 'cliente'),
(2, 'terapeuta');

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `rol_id`, `confirmado`, `token`) VALUES
(1, 'Alexander', 'Castañeda', 'ingalexander@gmail.com', '$2y$10$9r1u4GSoGkvrgWB0TCG4qeNQSEz8OB.cxjy01UMYOrJ347BLoJH8.', '3152230758', 1, 1, NULL),
(2, 'Yuliana', 'Vera', 'yuliana.vera@utp.edu.co', '$2y$10$j2Y5V3Kwm2N.hVwRQd2hXOTrMgZkZKKmDCxIoKIcsqFdZvQHIJgNi', '3105660000', 2, 1, NULL),
(4, 'Alexander', 'Yepez', 'ingalexanderyepez@gmail.com', '$2y$10$DPrvya6/iLPfG9z4LF1ykuTZe4NxHjIOYTVH5Apdz/IZZDQKatnX2', '3113460775', 3, 1, ''),
(5, 'Karla', 'Giraldo', 'terapeuta@spa.com', '$2y$10$x4on58qSQQ.RTtZc26JsEOn/BA60zGEC75fu.MgFVi5Nxnu8HMM2C', '3206495589', 2, 1, ''),
(7, 'Andrés', 'Salgado', 'andres.salgado@utp.edu.co', '$2y$10$BJG35pR1d8t3/dbF1BuxteRysotk3m88Ml6Ricz.vLskmrBii4EZ.', '1234567890', 3, 1, NULL),
(11, 'Sebastian', 'Bermudez', 'sebastian.bermudez@utp.edu.co', '$2y$10$xa2RByDd.53QJT9sm16v/eV8/lhkPuB9RktVjzkfpHe4Mon0luAee', '1234567890', 3, 1, ''),
(17, 'Amada', 'Pérez', 'terapeuta3@spa.com', '$2y$10$HqIE9OKKnOi99w7HxUupCuJc/cd8udIWaEOiIybldnSVVs5SpnsRy', '3152230758', 2, 1, NULL),
(18, 'Karen', 'Bermúdez', 'terapeuta2@spa.com', '$2y$10$.q11VkQPSQ5m10RrntZ9PebNcd1mmY5lHS8P4I0GX3p/EW51ZdR.e', '3152230757', 2, 1, NULL),
(23, 'Marta', 'Restrepo', 'marta.restrepo@utp.edu.co', '$2y$10$SRENPtX021UrX.TgjVtC4.54.3pbyJKv6NhyZODFz35ucD1jvUp.C', '3151237894', 2, 1, NULL),
(24, 'Aldair', 'Lasso', 'aldair.lasso@utp.edu.co', '$2y$10$173TpQgQGzJwesDkci21SeFxavcTqgVaIDp0GBE3sMxhM6yR85uBm', '3147536250', 3, 1, NULL),
(25, 'Jose', 'Florez', 'josew.florez@aerocivil.gov.co', '$2y$10$cnpB0ZmMnFUHe5W9ezDJ.eAOZ9TOrokEL8/5ls3uIH49QftRA927C', '3118767274', 3, 1, NULL),
(26, 'Andres Felipe', 'Ramirez', 'andresramirez@utp.edu.co', '$2y$10$920285iCrLN8cvkOWUYojO/ZxS1bByUz.3BqHWpmoU/2g7Sl8aw5q', '3001234567', 3, 1, NULL),
(27, 'Pepito', 'Perez', 'pepito.perez@utp.edu.co', '$2y$10$d6VZeVpmLJgMEdiFSVnxr.sZQ8s.uDlRywkVmuE5ciJC.uKyf4j5O', '3001112233', 3, 1, NULL);

INSERT INTO `membresias` (`id`, `nombre`, `precio`, `descripcion`, `beneficios`, `descuento`) VALUES
(4, 'Membresía Gold', '60000.00', 'Una opción intermedia para quienes buscan relajación regular con beneficios adicionales, ideal para clientes frecuentes.', '- 3 sesiones de masajes o tratamientos básicos al mes.\r\n- 10% de descuento en sesiones adicionales.\r\n- Acceso a talleres de bienestar mensuales.', '10.00'),
(5, 'Membresía Platinum', '100000.00', 'Una membresía de alto nivel para clientes que desean disfrutar de beneficios especiales y relajación frecuente con un toque de exclusividad.', '- 5 sesiones de masajes o tratamientos faciales al mes.\r\n- 1 sesión gratuita con un terapeuta premium cada mes.\r\n- Descuento en la programación de citas para familiares.\r\n- Acceso a eventos de bienestar seleccionados.', '20.00'),
(6, 'Membresía Diamante', '150000.00', 'La membresía más exclusiva de Luminous Spa, diseñada para quienes buscan una experiencia de lujo total. Incluye acceso ilimitado a todos los servicios y beneficios premium.', '- Acceso ilimitado a todos los servicios del spa (masajes, tratamientos faciales, sauna, etc.).\r\n- 3 sesiones gratuitas al mes con terapeutas premium.\r\n- Prioridad en la programación de citas.\r\n- Acceso exclusivo a eventos y talleres de bienestar.\r\n- Productos de spa de cortesía cada mes (valor de $20,000 COP).', '30.00');

INSERT INTO `clientes` (`id`, `usuario_id`, `telefono`, `direccion`) VALUES
(1, 1, '3152230758', 'Coodelmar 4'),
(2, 2, '3105669793', 'Calle falsa 123'),
(4, 4, '3113460775', 'Calle falsa 234'),
(5, 5, '3206495589', 'Calle falsa 345'),
(7, 7, '1234567890', 'Calle falsa 456'),
(11, 11, '1234567890', 'Calle falsa 567'),
(16, 24, '3147536250', 'Calle falsa 678'),
(17, 25, '3118767274', NULL),
(18, 26, '3001234567', NULL),
(19, 27, '3001112233', NULL);

INSERT INTO `colaboradores` (`id`, `usuario_id`, `especialidad`) VALUES
(1, 2, 'Masajista'),
(2, 5, 'Esteticista'),
(8, 17, 'Esteticista'),
(9, 18, 'Pedicurista'),
(10, 23, 'Manicurista');

INSERT INTO `familiares` (`id`, `cliente_id`, `nombre`, `apellido`, `parentesco`, `fecha_nacimiento`, `telefono`) VALUES
(1, 4, 'Lusiana', 'Cardona', 'Prima', '2010-10-10', ''),
(2, 4, 'Lizeth', 'Cardona', 'Prima', '2010-10-10', ''),
(3, 4, 'Rafael Augusto', 'Cardona', 'Primo', '2005-10-10', ''),
(7, 11, 'Nicol', 'Kidman', 'Tía', '2000-10-10', ''),
(8, 7, 'Sebastian', 'Bermúdez', 'El primo super programador', '2005-10-10', '3006439106');

INSERT INTO `citas` (`id`, `cliente_id`, `colaborador_id`, `familiar_id`, `fecha`, `hora`, `estado`) VALUES
(1, 4, 1, 1, '2025-06-20', '10:00:00', '2'),
(28, 4, 2, NULL, '2025-06-02', '14:00:00', '0'),
(30, 4, 2, 3, '2025-06-06', '13:00:00', '0'),
(31, 7, 1, NULL, '2025-06-05', '15:00:00', '0'),
(32, 4, 2, 1, '2025-06-05', '11:00:00', '2'),
(33, 4, 1, NULL, '2025-06-06', '12:30:00', '0'),
(35, 7, 1, 8, '2025-06-10', '11:30:00', '0'),
(37, 7, 1, NULL, '2025-06-10', '13:45:00', '0'),
(38, 7, 8, NULL, '2025-06-10', '15:30:00', '2'),
(40, 19, 1, NULL, '2025-06-10', '13:00:00', '0'),
(41, 17, 1, NULL, '2025-06-24', '15:00:00', '0');

INSERT INTO `citas_cancelaciones` (`id`, `cita_id`, `motivo`, `fecha`) VALUES
(36, 31, 'El Andrés no llegó a la cita!', '2025-05-30'),
(37, 1, 'El cliente no pudo asistir!', '2025-05-31'),
(38, 32, 'El cliente no pudo asistir!', '2025-05-31');

INSERT INTO `citas_servicios` (`id`, `cita_id`, `servicio_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(53, 28, 3),
(54, 28, 4),
(57, 30, 9),
(58, 30, 8),
(59, 31, 7),
(60, 31, 4),
(61, 31, 1),
(62, 32, 9),
(63, 32, 5),
(64, 32, 6),
(65, 33, 6),
(66, 33, 5),
(67, 33, 3),
(71, 35, 1),
(72, 35, 4),
(76, 37, 13),
(77, 37, 10),
(78, 38, 9),
(79, 38, 4),
(83, 40, 5),
(84, 40, 8),
(85, 40, 10),
(86, 41, 8),
(87, 41, 9),
(88, 41, 10);

INSERT INTO `clientes_membresias` (`id`, `cliente_id`, `membresia_id`, `fecha_inicio`, `fecha_fin`) VALUES
(4, 7, 4, '2025-06-03', '2025-07-03'),
(5, 4, 5, '2025-06-04', '2025-08-04'),
(6, 11, 6, '2025-06-04', '2025-09-04'),
(7, 17, 5, '2025-06-04', '2025-10-04'),
(8, 16, 5, '2025-06-04', '2025-07-04'),
(9, 18, 6, '2025-06-04', '2025-11-04');

INSERT INTO `historiales_tratamientos` (`id`, `cliente_id`, `colaborador_id`, `servicio_id`, `fecha`, `notas`) VALUES
(6, 4, 1, 1, '2025-05-30', 'Exfoliación adicional gratuita.'),
(7, 4, 1, 6, '2025-05-30', 'Se debe programar varias sesiones de pedicure por daño extenso severo de onicomicosis.'),
(8, 4, 1, 6, '2025-05-30', 'Aplicación chocoterapia.'),
(9, 7, 1, 7, '2025-05-30', 'Recomendación ir a donde dermatólogo para recomendaciones sobre piel delicada.'),
(10, 4, 1, 8, '2025-05-30', 'Posible caso de no tolerancia a los rayos UV. Se recomienda revisión con dearmatólogo.'),
(11, 7, 1, 1, '2025-05-31', 'Mal olor de pies, pecueca terrible!'),
(28, 4, 2, 3, '2025-05-31', 'Masaje con final feliz!'),
(29, 7, 1, 13, '2025-06-01', 'Se realiza exfoliación completa y se le recomienda sesión para humectación profunda.');

INSERT INTO `inventario` (`id`, `producto`, `descripcion`, `precio`, `cantidad`, `proveedor_id`, `fecha_ingreso`) VALUES
(1, 'Aceite esencial de lavanda', 'Aceite relajante usado en aromaterapia', '25000.00', 20, 1, '2025-05-30'),
(2, 'Toallas faciales premium', 'Toallas ultra suaves para tratamientos faciales', '8000.00', 50, 3, '2025-05-29'),
(3, 'Mascarilla de arcilla verde', 'Mascarilla purificante para piel grasa', '12000.00', 30, 2, '2025-05-28'),
(4, 'Gel conductor para ultrasonido', 'Gel neutro para equipos de estética', '10000.00', 25, 4, '2025-03-27'),
(5, 'Loción hidratante post-tratamiento', 'Loción para restaurar la humedad de la piel', '15000.00', 15, 1, '2025-04-30'),
(8, 'Velas aromáticas spa', 'Velas ambientadoras con fragancia de eucalipto', '6000.00', 40, 7, '2025-04-04');

INSERT INTO `proveedores` (`id`, `nombre`, `contacto`, `telefono`, `email`, `direccion`) VALUES
(1, 'Belleza Total SAS', 'Laura Gómez Romero', '3124567890', 'contacto@bellezatotal.com', 'Cra 15 #45-67, Bogotá'),
(2, 'Salud Natural LTDA', 'Carlos Pérez', '3012345678', 'ventas@saludnatural.com', 'Av. Siempre Viva 123, Medellín'),
(3, 'Distribuciones Cosméticos SA', 'Paula Ríos', '3112233445', 'info@cosmeticosdistribuciones.com', 'Calle 10 #20-30, Cali'),
(4, 'Suministros SpaZen', 'Ana Beltrán', '3159876543', 'servicio@spazen.co', 'Transv 23 #67-12, Bucaramanga'),
(5, 'Bioestética Proveedores', 'Julián Morales', '3109988776', 'bio@estetica.com', 'Carrera 50 #22-11, Barranquilla'),
(7, 'Bella Proveedores', 'Nestor Marín', '3109981234', 'ventas@bella.com', 'Carrera 8 # 22-11');

INSERT INTO `recordatorios` (`id`, `cliente_id`, `cita_id`, `fecha`, `enviado`, `medio`) VALUES
(6, 4, 1, '2025-06-06', 1, 'email'),
(7, 7, 35, '2025-06-06', 1, 'email'),
(8, 17, 41, '2025-06-06', 0, 'email'),
(11, 17, 41, '2025-06-05', 1, 'email'),
(12, 4, 33, '2025-06-05', 1, 'email');

INSERT INTO `servicios` (`id`, `nombre`, `precio`, `descripcion`, `activo`) VALUES
(1, 'Masaje Relajante', '90000.00', 'Masaje de relajación profunda con aceites esenciales.', 1),
(2, 'Masaje Descontracturante', '130000.00', 'Masaje enfocado en aliviar tensiones musculares.', 1),
(3, 'Tratamiento Facial Hidratante', '90000.00', 'Limpieza facial profunda con mascarilla hidratante.', 1),
(4, 'Tratamiento Antiedad', '150000.00', 'Tratamiento facial con colágeno y ácido hialurónico.', 1),
(5, 'Terapia de Piedras Calientes', '130000.00', 'Masaje con piedras volcánicas para relajación.', 1),
(6, 'Manicura Spa', '50000.00', 'Manicura completa con exfoliación y esmalte.', 1),
(7, 'Pedicura Spa', '60000.00', 'Pedicura completa con masaje y exfoliación.', 1),
(8, 'Exfoliación Corporal', '100000.00', 'Exfoliación con sales minerales para piel suave.', 1),
(9, 'Depilación con Cera', '80000.00', 'Depilación con cera caliente o fría en zonas a elegir.', 1),
(10, 'Aromaterapia', '110000.00', 'Terapia con aceites esenciales para el bienestar general.', 1),
(13, 'Exfoliación Corporal con Sales del Himalaya', '60000.00', 'Renueva tu piel eliminando células muertas con una exfoliación suave a base de sales minerales naturales.', 1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;