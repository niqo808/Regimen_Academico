-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-09-2025 a las 18:53:20
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
-- Base de datos: `sistema_escuela`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `DNI_Alumno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `ID` int(11) NOT NULL,
  `Tabla` varchar(50) NOT NULL,
  `Accion` varchar(20) NOT NULL,
  `DNI_Usuario` int(11) DEFAULT NULL,
  `DNI_Tutor` int(11) DEFAULT NULL,
  `ID_Materia` int(11) DEFAULT NULL,
  `ID_Curso` int(11) DEFAULT NULL,
  `Fecha` date NOT NULL DEFAULT curdate(),
  `Usuario` text NOT NULL DEFAULT current_user()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`ID`, `Tabla`, `Accion`, `DNI_Usuario`, `DNI_Tutor`, `ID_Materia`, `ID_Curso`, `Fecha`, `Usuario`) VALUES
(1, 'Usuarios', 'DELETE', 47567419, NULL, NULL, NULL, '2025-09-11', 'root@localhost');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `ID` int(11) NOT NULL,
  `DNI_Preceptor` int(11) NOT NULL,
  `DNI_Alumno` int(11) NOT NULL,
  `Turno` enum('Mañana','Tarde','Noche') NOT NULL,
  `Grupo` varchar(255) NOT NULL,
  `Anio` int(4) NOT NULL,
  `Division` varchar(10) NOT NULL,
  `Especialidad` varchar(255) NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Disparadores `cursos`
--
DELIMITER $$
CREATE TRIGGER `aud_cursos_delete` AFTER DELETE ON `cursos` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Curso, Fecha, Usuario
    ) VALUES (
        'Cursos', 'DELETE', OLD.ID, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_cursos_insert` AFTER INSERT ON `cursos` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Curso, Fecha, Usuario
    ) VALUES (
        'Cursos', 'INSERT', NEW.ID, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_cursos_update` AFTER UPDATE ON `cursos` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Curso, Fecha, Usuario
    ) VALUES (
        'Cursos', 'UPDATE', NEW.ID, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `ID` int(11) NOT NULL,
  `ID_Curso` int(11) NOT NULL,
  `DNI_Profesor` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Horarios` varchar(255) NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Disparadores `materias`
--
DELIMITER $$
CREATE TRIGGER `aud_materias_delete` AFTER DELETE ON `materias` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Materia, Fecha, Usuario
    ) VALUES (
        'Materias', 'DELETE', OLD.ID, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_materias_insert` AFTER INSERT ON `materias` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Materia, Fecha, Usuario
    ) VALUES (
        'Materias', 'INSERT', NEW.ID, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_materias_update` AFTER UPDATE ON `materias` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Materia, Fecha, Usuario
    ) VALUES (
        'Materias', 'UPDATE', NEW.ID, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `dni_alumno` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `primerInforme` int(11) DEFAULT NULL,
  `primerCuatri` int(11) DEFAULT NULL,
  `segundoInforme` int(11) DEFAULT NULL,
  `segundoCuatri` int(11) DEFAULT NULL,
  `notaFinal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preceptor`
--

CREATE TABLE `preceptor` (
  `DNI_Preceptor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `preceptor`
--

INSERT INTO `preceptor` (`DNI_Preceptor`) VALUES
(40891234);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `DNI_Profesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`DNI_Profesor`) VALUES
(39287654);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutores`
--

CREATE TABLE `tutores` (
  `DNI` int(11) NOT NULL,
  `Primer_Nombre` varchar(255) NOT NULL,
  `Segundo_Nombre` varchar(255) NOT NULL,
  `Apellido` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Nacionalidad` varchar(255) NOT NULL,
  `Localidad` varchar(255) NOT NULL,
  `Calle` varchar(255) NOT NULL,
  `Altura` int(10) NOT NULL,
  `Fecha_nacimiento` date NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Disparadores `tutores`
--
DELIMITER $$
CREATE TRIGGER `aud_tutores_delete` AFTER DELETE ON `tutores` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, DNI_Tutor, Fecha, Usuario
    ) VALUES (
        'Tutores', 'DELETE', OLD.DNI, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_tutores_insert` AFTER INSERT ON `tutores` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, DNI_Tutor, Fecha, Usuario
    ) VALUES (
        'Tutores', 'INSERT', NEW.DNI, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_tutores_update` AFTER UPDATE ON `tutores` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, DNI_Tutor, Fecha, Usuario
    ) VALUES (
        'Tutores', 'UPDATE', NEW.DNI, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutor_alumno`
--

CREATE TABLE `tutor_alumno` (
  `DNI_A` int(11) DEFAULT NULL,
  `DNI_T` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `DNI` int(11) NOT NULL,
  `Primer_nombre` varchar(255) NOT NULL,
  `Segundo_nombre` varchar(255) NOT NULL,
  `Apellido` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password_Usuario` varchar(255) DEFAULT NULL,
  `Nacionalidad` varchar(255) NOT NULL,
  `Localidad` varchar(255) NOT NULL,
  `Calle` varchar(255) NOT NULL,
  `Altura` int(11) NOT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `Telefono` varchar(255) NOT NULL,
  `Rol` enum('Director','Preceptor','Profesor','Alumno') NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1,
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Usuario` varchar(255) DEFAULT current_user()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`DNI`, `Primer_nombre`, `Segundo_nombre`, `Apellido`, `Email`, `Password_Usuario`, `Nacionalidad`, `Localidad`, `Calle`, `Altura`, `Fecha_Nacimiento`, `Telefono`, `Rol`, `Estado`, `Fecha_Creacion`, `Usuario`) VALUES
(39287654, 'Carlos', 'Eduardo', 'Ramírez', 'c.ramirez@docente.edu', NULL, 'Argentina', 'La Plata', 'Calle 12', 876, '1979-11-03', '11 9988 7766', 'Profesor', 1, '2025-09-11 15:26:34', 'root@localhost'),
(40891234, 'Lucía', 'Marina', 'Fernández', 'lucia.fernandez@educa.ar', NULL, 'Argentina', 'Morón', 'Av. Rivadavia', 12345, '1985-06-14', '11 4567 8910', 'Preceptor', 1, '2025-09-11 15:26:27', 'root@localhost');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `aud_usuarios_delete` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, DNI_Usuario, Fecha, Usuario
    ) VALUES (
        'Usuarios', 'DELETE', OLD.DNI, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_usuarios_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, DNI_Usuario, Fecha, Usuario
    ) VALUES (
        'Usuarios', 'INSERT', NEW.DNI, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_usuarios_update` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, DNI_Usuario, Fecha, Usuario
    ) VALUES (
        'Usuarios', 'UPDATE', NEW.DNI, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insertar_DNI_en_tablas` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    IF NEW.Rol = 'Alumno' THEN
        INSERT IGNORE INTO Alumnos (DNI_Alumno)
        VALUES (NEW.DNI);
    ELSEIF NEW.Rol = 'Preceptor' THEN
        INSERT IGNORE INTO preceptor (DNI_Preceptor)
        VALUES (NEW.DNI);
    ELSEIF NEW.Rol = 'Profesor'THEN
        INSERT IGNORE INTO profesores (DNI_Profesor)
        VALUES (NEW.DNI);
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`DNI_Alumno`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `DNI_Preceptor` (`DNI_Preceptor`),
  ADD KEY `DNI_Alumno` (`DNI_Alumno`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_Curso` (`ID_Curso`),
  ADD KEY `DNI_Profesor` (`DNI_Profesor`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`dni_alumno`,`id_materia`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `preceptor`
--
ALTER TABLE `preceptor`
  ADD PRIMARY KEY (`DNI_Preceptor`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`DNI_Profesor`);

--
-- Indices de la tabla `tutores`
--
ALTER TABLE `tutores`
  ADD PRIMARY KEY (`DNI`);

--
-- Indices de la tabla `tutor_alumno`
--
ALTER TABLE `tutor_alumno`
  ADD UNIQUE KEY `DNI_A` (`DNI_A`),
  ADD UNIQUE KEY `DNI_T` (`DNI_T`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`DNI`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`DNI_Alumno`) REFERENCES `usuarios` (`DNI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`DNI_Preceptor`) REFERENCES `preceptor` (`DNI_Preceptor`),
  ADD CONSTRAINT `cursos_ibfk_2` FOREIGN KEY (`DNI_Alumno`) REFERENCES `alumnos` (`DNI_Alumno`);

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`ID_Curso`) REFERENCES `cursos` (`ID`),
  ADD CONSTRAINT `materias_ibfk_2` FOREIGN KEY (`DNI_Profesor`) REFERENCES `profesores` (`DNI_Profesor`);

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`dni_alumno`) REFERENCES `alumnos` (`DNI_Alumno`),
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`ID`);

--
-- Filtros para la tabla `preceptor`
--
ALTER TABLE `preceptor`
  ADD CONSTRAINT `preceptor_ibfk_1` FOREIGN KEY (`DNI_Preceptor`) REFERENCES `usuarios` (`DNI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`DNI_Profesor`) REFERENCES `usuarios` (`DNI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tutor_alumno`
--
ALTER TABLE `tutor_alumno`
  ADD CONSTRAINT `tutor_alumno_ibfk_1` FOREIGN KEY (`DNI_A`) REFERENCES `alumnos` (`DNI_Alumno`),
  ADD CONSTRAINT `tutor_alumno_ibfk_2` FOREIGN KEY (`DNI_T`) REFERENCES `tutores` (`DNI`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
