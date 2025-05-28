# Inventory+

**Inventory+** es un sistema de gestión de inventario enfocado en tiendas de componentes gamer y tecnología. Desarrollado en PHP orientado a programacion orientada a objetos
con base de datos MySQL y utilizando HTML, CSS y JavaScript, permite controlar productos, usuarios y movimientos dentro del inventario, con una interfaz sencilla y funcional.

## 🎯 Objetivo

Brindar una herramienta eficiente para el registro, control y seguimiento de artículos tecnológicos como procesadores, discos duros, tarjetas gráficas, RAM, fuentes,
gabinetes y más. Diseñado para ser intuitivo y adaptable a pequeñas o medianas tiendas.

---

## 🧩 Características principales

- 📦 **Gestión de productos**: Agregar, editar, Eliminar (Desactivado en el codigo por problemas de legalidad y registro) y listar artículos.
- 👥 **Gestión de usuarios**: Registro y control de usuarios con roles (Administrador o Usuario).
- 🔄 **Registro de movimientos**: Toda entrada o salida de productos queda registrada con fecha, hora, usuario y cantidades.
- 🔐 **Control de acceso**: Ingreso con usuario y contraseña; restricciones según el rol.
- 🗂️ **Historial y trazabilidad**: No se eliminan registros críticos, permitiendo rastrear acciones realizadas.
- 🎨 **Interfaz gráfica personalizada**: Visual con colores llamativos, publicidad, e identidad visual.

---

## 🗃️ Estructura de la base de datos

### Base de datos: `inventario`

#### Tabla: `productos`
| Campo              | Tipo         | Descripción                        |
|-------------------|--------------|------------------------------------|
| id                | INT (PK)     | ID autoincremental                 |
| nombre            | VARCHAR      | Nombre del producto                |
| descripcion       | TEXT         | Descripción breve                  |
| precio            | DECIMAL      | Precio unitario                    |
| cantidad          | INT          | Cantidad disponible                |
| fecha_creacion    | DATETIME     | Fecha de creación                  |
| fecha_actualizacion | DATETIME   | Última actualización               |
| activo            | BOOLEAN      | Estado del producto (activo/inactivo) |

#### Tabla: `usuarios`
| Campo     | Tipo     | Descripción              |
|-----------|----------|--------------------------|
| CC        | VARCHAR  | Cédula del usuario (PK)  |
| UserDB    | VARCHAR  | Nombre de usuario        |
| PassDB    | VARCHAR  | Contraseña               |
| RoleDB    | VARCHAR  | Rol (Administrador/Usuario) |
| NameDB    | VARCHAR  | Nombre completo          |

#### Tabla: `movimientos`
| Campo    | Tipo     | Descripción                 |
|----------|----------|-----------------------------|
| id       | INT (PK) | ID autoincremental          |
| fecha    | DATE     | Fecha del movimiento        |
| hora     | TIME     | Hora del movimiento         |
| usuario  | VARCHAR  | Usuario que realizó la acción |
| accion   | VARCHAR  | Acción (Entrada/Salida)     |

#### Tabla: `detalles_movimiento`
| Campo        | Tipo     | Descripción                               |
|--------------|----------|-------------------------------------------|
| id           | INT (PK) | ID del detalle                            |
| id_movimiento| INT      | FK a `movimientos.id`                     |
| id_articulo  | INT      | FK a `productos.id`                       |
| cantidad     | INT      | Cantidad involucrada                      |
