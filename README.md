# Régimen Académico 🎓

Sistema web de gestión escolar para instituciones educativas. Permite administrar alumnos, docentes, cursos, notas, asistencias y reportes desde un entorno centralizado con roles diferenciados.

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/es/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/es/docs/Web/CSS)

---

## Información del proyecto

**Régimen Académico** es un sistema web desarrollado con PHP y MySQL que digitaliza la gestión interna de una institución educativa. Contempla múltiples roles de usuario (director, preceptor, profesor y alumno), cada uno con su propio dashboard y funcionalidades específicas.

- **Tipo:** Aplicación web fullstack (PHP + MySQL)
- **Contexto:** Proyecto académico desarrollado durante la Tecnicatura en Informática (EESTN°2)
- **Estado:** Funcional — proyecto finalizado

---

## Tecnologías utilizadas

| Tecnología | Propósito |
|---|---|
| **PHP** | Lógica del servidor, autenticación y procesamiento de datos |
| **MySQL** | Base de datos relacional para toda la persistencia del sistema |
| **HTML5 + CSS3** | Interfaz de usuario y estilos |
| **JavaScript** | Interacciones del lado del cliente |

---

## Roles del sistema

El sistema implementa cuatro roles con permisos diferenciados:

| Rol | Acceso |
|---|---|
| **Director** | Dashboard general, gestión de cursos, reportes, boletines de todos los alumnos |
| **Preceptor** | Revisión de notas, gestión de asistencias por curso |
| **Profesor** | Carga de notas, visualización de sus materias y cursos asignados |
| **Alumno** | Consulta de notas, materias e inasistencias propias |

---

## Funcionalidades

- Autenticación con sesiones PHP y control de acceso por rol
- Registro de usuarios con verificación de DNI duplicado
- Gestión de cursos, materias y asignaciones de docentes
- Carga y aprobación de notas por parciales
- Control de asistencias por curso con historial
- Generación de boletines individuales por alumno
- Importación masiva de alumnos mediante archivos CSV
- Reportes para el director con vista general del establecimiento
- Perfil editable con cambio de contraseña

---

## Cómo ejecutar el proyecto

### Requisitos previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor local: XAMPP, WAMP o equivalente

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/niqo808/Regimen_Academico

# 2. Mover la carpeta al directorio de tu servidor local
# Por ejemplo, en XAMPP:
mv Regimen_Academico /xampp/htdocs/
```

```sql
-- 3. Importar la base de datos en phpMyAdmin o desde consola
mysql -u root -p < sistema_escuela.sql
```

```php
// 4. Configurar la conexión en conexion/conexion.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistema_escuela";
```

## Acceder desde el navegador
http://localhost/Regimen_Academico/index.php

---

## Estructura del proyecto

```
Regimen_Academico/
├── conexion/                   # Configuración de conexión a la base de datos
├── imagenes/                   # Recursos visuales
├── public/                     # Archivos estáticos públicos
├── style/                      # Hojas de estilo CSS
├── sistema_escuela.sql         # Script de creación de la base de datos
├── index.php                   # Punto de entrada
├── login.php                   # Autenticación de usuarios
├── dashboard_director.php      # Panel del director
├── gestionar_notas.php         # Módulo de notas
├── gestionar_asistencias.php   # Módulo de asistencias
├── generar_boletin.php         # Generación de boletines
├── importar_alumnos.php        # Importación masiva por CSV
└── ...
```

---

## Próximas mejoras

- [ ] Migración del frontend a un framework moderno (React o Vue)
- [ ] API REST para desacoplar backend y frontend
- [ ] Notificaciones por email al registrar notas o inasistencias
- [ ] Exportación de boletines en PDF
- [ ] Panel de estadísticas con gráficos interactivos

---

## Autor

**Nicolás Ferreira**

Técnico en Informática · Estudiante de Licenciatura en Informática (UNAHUR)

- GitHub: [@niqo808](https://github.com/niqo808)
- Repositorio: [Regimen_Academico](https://github.com/niqo808/Regimen_Academico)

---

> Proyecto académico desarrollado como sistema de gestión escolar integral con PHP y MySQL.
