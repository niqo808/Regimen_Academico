-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2025 at 11:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistema_escuela`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumnos`
--

CREATE TABLE `alumnos` (
  `DNI_Alumno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `alumnos`
--

INSERT INTO `alumnos` (`DNI_Alumno`) VALUES
(45123456),
(45123457),
(45123458);

-- --------------------------------------------------------

--
-- Table structure for table `auditoria`
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
-- Dumping data for table `auditoria`
--

INSERT INTO `auditoria` (`ID`, `Tabla`, `Accion`, `DNI_Usuario`, `DNI_Tutor`, `ID_Materia`, `ID_Curso`, `Fecha`, `Usuario`) VALUES
(1, 'Usuarios', 'DELETE', 47567419, NULL, NULL, NULL, '2025-09-11', 'root@localhost'),
(2, 'Usuarios', 'UPDATE', 39287654, NULL, NULL, NULL, '2025-10-13', 'root@localhost'),
(3, 'Usuarios', 'INSERT', 45123456, NULL, NULL, NULL, '2025-10-13', 'root@localhost'),
(4, 'Cursos', 'INSERT', NULL, NULL, NULL, 1, '2025-10-13', 'root@localhost'),
(5, 'Materias', 'INSERT', NULL, NULL, 1, NULL, '2025-10-13', 'root@localhost'),
(6, 'Materias', 'INSERT', NULL, NULL, 2, NULL, '2025-10-13', 'root@localhost'),
(7, 'Materias', 'INSERT', NULL, NULL, 3, NULL, '2025-10-13', 'root@localhost'),
(8, 'Usuarios', 'UPDATE', 45123456, NULL, NULL, NULL, '2025-10-13', 'root@localhost'),
(9, 'Usuarios', 'INSERT', 45123457, NULL, NULL, NULL, '2025-10-15', 'root@localhost'),
(10, 'Usuarios', 'INSERT', 45123458, NULL, NULL, NULL, '2025-10-15', 'root@localhost'),
(11, 'Cursos', 'INSERT', NULL, NULL, NULL, 2, '2025-10-15', 'root@localhost'),
(12, 'Cursos', 'INSERT', NULL, NULL, NULL, 3, '2025-10-15', 'root@localhost'),
(13, 'Usuarios', 'UPDATE', 40891234, NULL, NULL, NULL, '2025-10-15', 'root@localhost'),
(14, 'Cursos', 'DELETE', NULL, NULL, NULL, 2, '2025-10-19', 'root@localhost'),
(15, 'Cursos', 'DELETE', NULL, NULL, NULL, 3, '2025-10-19', 'root@localhost');

-- --------------------------------------------------------

--
-- Table structure for table `cursos`
--

CREATE TABLE `cursos` (
  `ID` int(11) NOT NULL,
  `DNI_Preceptor` int(11) NOT NULL,
  `Turno` enum('Mañana','Tarde','Noche') NOT NULL,
  `Grupo` varchar(255) NOT NULL,
  `Anio` int(4) NOT NULL,
  `Division` varchar(10) NOT NULL,
  `Especialidad` varchar(255) NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `cursos`
--

INSERT INTO `cursos` (`ID`, `DNI_Preceptor`, `Turno`, `Grupo`, `Anio`, `Division`, `Especialidad`, `Estado`) VALUES
(1, 40891234, 'Mañana', 'A', 5, '1ra', 'Informática', 1);

--
-- Triggers `cursos`
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
-- Table structure for table `curso_alumno`
--

CREATE TABLE `curso_alumno` (
  `ID` int(11) NOT NULL,
  `ID_Curso` int(11) NOT NULL,
  `DNI_Alumno` int(11) NOT NULL,
  `Fecha_Inscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `curso_alumno`
--

INSERT INTO `curso_alumno` (`ID`, `ID_Curso`, `DNI_Alumno`, `Fecha_Inscripcion`, `Estado`) VALUES
(1, 1, 45123456, '2025-10-19 21:30:41', 1),
(2, 1, 45123457, '2025-10-19 21:30:41', 1),
(3, 1, 45123458, '2025-10-19 21:30:41', 1);

--
-- Triggers `curso_alumno`
--
DELIMITER $$
CREATE TRIGGER `aud_curso_alumno_delete` AFTER DELETE ON `curso_alumno` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Curso, DNI_Usuario, Fecha, Usuario
    ) VALUES (
        'Curso_Alumno', 'DELETE', OLD.ID_Curso, OLD.DNI_Alumno, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `aud_curso_alumno_insert` AFTER INSERT ON `curso_alumno` FOR EACH ROW BEGIN
    INSERT INTO Auditoria (
        Tabla, Accion, ID_Curso, DNI_Usuario, Fecha, Usuario
    ) VALUES (
        'Curso_Alumno', 'INSERT', NEW.ID_Curso, NEW.DNI_Alumno, CURRENT_DATE, CURRENT_USER()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inasistencias`
--

CREATE TABLE `inasistencias` (
  `ID` int(11) NOT NULL,
  `DNI_Alumno` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Tipo` enum('Falta','Tarde','Falta Justificada') NOT NULL DEFAULT 'Falta',
  `Observaciones` text DEFAULT NULL,
  `Fecha_Carga` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `inasistencias`
--

INSERT INTO `inasistencias` (`ID`, `DNI_Alumno`, `Fecha`, `Tipo`, `Observaciones`, `Fecha_Carga`) VALUES
(1, 45123456, '2025-10-01', 'Falta', 'Sin justificativo', '2025-10-13 19:29:35'),
(2, 45123456, '2025-10-05', 'Tarde', 'Llegó 15 minutos tarde', '2025-10-13 19:29:35'),
(3, 45123456, '2025-10-10', 'Falta Justificada', 'Certificado médico presentado', '2025-10-13 19:29:35'),
(4, 45123456, '2025-10-13', 'Falta', NULL, '2025-10-14 01:31:22'),
(5, 45123457, '2025-10-01', 'Tarde', 'Llegó 10 minutos tarde', '2025-10-15 03:14:32'),
(6, 45123457, '2025-10-05', 'Falta', NULL, '2025-10-15 03:14:32'),
(7, 45123458, '2025-10-02', 'Falta', NULL, '2025-10-15 03:14:32'),
(8, 45123458, '2025-10-08', 'Tarde', NULL, '2025-10-15 03:14:32'),
(9, 45123458, '2025-10-12', 'Falta', NULL, '2025-10-15 03:14:32'),
(10, 45123457, '2025-10-19', 'Tarde', NULL, '2025-10-19 20:32:03'),
(11, 45123458, '2025-10-19', 'Falta', NULL, '2025-10-19 20:32:03');

-- --------------------------------------------------------

--
-- Table structure for table `materias`
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
-- Dumping data for table `materias`
--

INSERT INTO `materias` (`ID`, `ID_Curso`, `DNI_Profesor`, `Nombre`, `Horarios`, `Estado`) VALUES
(1, 1, 39287654, 'Matemática', 'Lunes y Miércoles 8:00-10:00', 1),
(2, 1, 39287654, 'Programación', 'Martes y Jueves 10:00-12:00', 1),
(3, 1, 39287654, 'Base de Datos', 'Viernes 8:00-12:00', 1);

--
-- Triggers `materias`
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
-- Table structure for table `notas`
--

CREATE TABLE `notas` (
  `dni_alumno` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `primerInforme` int(11) DEFAULT NULL,
  `primerCuatri` int(11) DEFAULT NULL,
  `segundoInforme` int(11) DEFAULT NULL,
  `segundoCuatri` int(11) DEFAULT NULL,
  `notaFinal` int(11) DEFAULT NULL,
  `Estado_Aprobacion` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `DNI_Preceptor_Aprobador` int(11) DEFAULT NULL,
  `Fecha_Aprobacion` datetime DEFAULT NULL,
  `Observaciones_Preceptor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `notas`
--

INSERT INTO `notas` (`dni_alumno`, `id_materia`, `primerInforme`, `primerCuatri`, `segundoInforme`, `segundoCuatri`, `notaFinal`, `Estado_Aprobacion`, `DNI_Preceptor_Aprobador`, `Fecha_Aprobacion`, `Observaciones_Preceptor`) VALUES
(45123456, 1, 8, 7, 9, 8, 10, 'Aprobado', 40891234, '2025-10-15 00:26:45', NULL),
(45123456, 2, 10, 10, 10, 4, 10, 'Aprobado', 40891234, '2025-10-15 00:26:45', NULL),
(45123456, 3, 7, 7, 8, 7, 7, 'Aprobado', 40891234, '2025-10-15 00:26:45', NULL),
(45123457, 1, 9, 9, 10, 9, 9, 'Pendiente', NULL, NULL, NULL),
(45123457, 2, 8, 8, 9, 8, 8, 'Aprobado', 40891234, '2025-10-15 00:26:45', NULL),
(45123457, 3, 10, 10, 10, 10, 10, 'Aprobado', 40891234, '2025-10-15 00:26:45', NULL),
(45123458, 1, 6, 6, 7, 6, 6, 'Aprobado', 40891234, '2025-10-15 00:26:45', NULL),
(45123458, 2, 5, 5, 6, 5, 5, 'Pendiente', NULL, NULL, NULL),
(45123458, 3, 7, 7, 8, 7, 7, 'Aprobado', 40891234, '2025-10-15 00:26:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `preceptor`
--

CREATE TABLE `preceptor` (
  `DNI_Preceptor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `preceptor`
--

INSERT INTO `preceptor` (`DNI_Preceptor`) VALUES
(40891234);

-- --------------------------------------------------------

--
-- Table structure for table `profesores`
--

CREATE TABLE `profesores` (
  `DNI_Profesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `profesores`
--

INSERT INTO `profesores` (`DNI_Profesor`) VALUES
(39287654);

-- --------------------------------------------------------

--
-- Table structure for table `tutores`
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
-- Triggers `tutores`
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
-- Table structure for table `tutor_alumno`
--

CREATE TABLE `tutor_alumno` (
  `DNI_A` int(11) DEFAULT NULL,
  `DNI_T` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
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
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`DNI`, `Primer_nombre`, `Segundo_nombre`, `Apellido`, `Email`, `Password_Usuario`, `Nacionalidad`, `Localidad`, `Calle`, `Altura`, `Fecha_Nacimiento`, `Telefono`, `Rol`, `Estado`, `Fecha_Creacion`, `Usuario`) VALUES
(39287654, 'Carlos', 'Eduardo', 'Ramírez', 'c.ramirez@docente.edu', '$2y$10$MzwUkVumdGaZTDnaYNzSc.t1bi5.52qKomRDA.KIshI7K6gD.MnIO', 'Argentina', 'La Plata', 'Calle 12', 876, '1979-11-03', '11 9988 7766', 'Profesor', 1, '2025-09-11 15:26:34', 'root@localhost'),
(40891234, 'Lucía', 'Marina', 'Fernández', 'lucia.fernandez@educa.ar', '$2y$10$jxbX4FVW9bFf0cux8Eo4c.lVgxYPlTxN0IXxssmspzx9FV/23TwcC', 'Argentina', 'Morón', 'Av. Rivadavia', 12345, '1985-06-14', '11 4567 8910', 'Preceptor', 1, '2025-09-11 15:26:27', 'root@localhost'),
(45123456, 'Nicolas', 'Fernando', 'Ferreira', 'nico.ferre@alumno.edu', '$2y$10$D2sub00bm1HMKwLkOE9eQeoFRMBFBEU2AkRsHdxwDWOuHxwFIt08i', 'Argentina', 'San Miguel', 'Belgrano', 1234, '2007-03-15', '11 1234 5678', 'Alumno', 1, '2025-10-13 19:29:06', 'root@localhost'),
(45123457, 'María', 'Luz', 'González', 'maria.gonzalez@alumno.edu', NULL, 'Argentina', 'San Miguel', 'San Martín', 567, '2007-05-20', '11 2345 6789', 'Alumno', 1, '2025-10-15 03:14:32', 'root@localhost'),
(45123458, 'Pedro', 'José', 'Martínez', 'pedro.martinez@alumno.edu', NULL, 'Argentina', 'San Miguel', 'Mitre', 890, '2007-08-15', '11 3456 7890', 'Alumno', 1, '2025-10-15 03:14:32', 'root@localhost');

--
-- Triggers `usuarios`
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
-- Indexes for dumped tables
--

--
-- Indexes for table `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`DNI_Alumno`);

--
-- Indexes for table `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `DNI_Preceptor` (`DNI_Preceptor`);

--
-- Indexes for table `curso_alumno`
--
ALTER TABLE `curso_alumno`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `curso_alumno_unique` (`ID_Curso`,`DNI_Alumno`),
  ADD KEY `ID_Curso` (`ID_Curso`),
  ADD KEY `DNI_Alumno` (`DNI_Alumno`);

--
-- Indexes for table `inasistencias`
--
ALTER TABLE `inasistencias`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `DNI_Alumno` (`DNI_Alumno`);

--
-- Indexes for table `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_Curso` (`ID_Curso`),
  ADD KEY `DNI_Profesor` (`DNI_Profesor`);

--
-- Indexes for table `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`dni_alumno`,`id_materia`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indexes for table `preceptor`
--
ALTER TABLE `preceptor`
  ADD PRIMARY KEY (`DNI_Preceptor`);

--
-- Indexes for table `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`DNI_Profesor`);

--
-- Indexes for table `tutores`
--
ALTER TABLE `tutores`
  ADD PRIMARY KEY (`DNI`);

--
-- Indexes for table `tutor_alumno`
--
ALTER TABLE `tutor_alumno`
  ADD UNIQUE KEY `DNI_A` (`DNI_A`),
  ADD UNIQUE KEY `DNI_T` (`DNI_T`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`DNI`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `curso_alumno`
--
ALTER TABLE `curso_alumno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inasistencias`
--
ALTER TABLE `inasistencias`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `materias`
--
ALTER TABLE `materias`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`DNI_Alumno`) REFERENCES `usuarios` (`DNI`) ON DELETE CASCADE;

--
-- Constraints for table `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`DNI_Preceptor`) REFERENCES `preceptor` (`DNI_Preceptor`);

--
-- Constraints for table `curso_alumno`
--
ALTER TABLE `curso_alumno`
  ADD CONSTRAINT `curso_alumno_ibfk_1` FOREIGN KEY (`ID_Curso`) REFERENCES `cursos` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `curso_alumno_ibfk_2` FOREIGN KEY (`DNI_Alumno`) REFERENCES `alumnos` (`DNI_Alumno`) ON DELETE CASCADE;

--
-- Constraints for table `inasistencias`
--
ALTER TABLE `inasistencias`
  ADD CONSTRAINT `inasistencias_ibfk_1` FOREIGN KEY (`DNI_Alumno`) REFERENCES `alumnos` (`DNI_Alumno`);

--
-- Constraints for table `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`ID_Curso`) REFERENCES `cursos` (`ID`),
  ADD CONSTRAINT `materias_ibfk_2` FOREIGN KEY (`DNI_Profesor`) REFERENCES `profesores` (`DNI_Profesor`);

--
-- Constraints for table `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`dni_alumno`) REFERENCES `alumnos` (`DNI_Alumno`),
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`ID`);

--
-- Constraints for table `preceptor`
--
ALTER TABLE `preceptor`
  ADD CONSTRAINT `preceptor_ibfk_1` FOREIGN KEY (`DNI_Preceptor`) REFERENCES `usuarios` (`DNI`) ON DELETE CASCADE;

--
-- Constraints for table `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`DNI_Profesor`) REFERENCES `usuarios` (`DNI`) ON DELETE CASCADE;

--
-- Constraints for table `tutor_alumno`
--
ALTER TABLE `tutor_alumno`
  ADD CONSTRAINT `tutor_alumno_ibfk_1` FOREIGN KEY (`DNI_A`) REFERENCES `alumnos` (`DNI_Alumno`),
  ADD CONSTRAINT `tutor_alumno_ibfk_2` FOREIGN KEY (`DNI_T`) REFERENCES `tutores` (`DNI`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
