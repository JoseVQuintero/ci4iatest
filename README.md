# CI4 + IA - Sistema de Productos y Usuarios

Aplicacion de gestion construida con **CodeIgniter 4** para administrar productos, categorias, usuarios, roles dinamicos y visibilidad de modulos por usuario.

## Produccion

- URL: https://ci4ia-7148b88079cd.herokuapp.com

## Stack

- Backend: CodeIgniter 4.4.8 (PHP)
- Frontend: AdminLTE 3, Bootstrap 4, jQuery, DataTables
- Base de datos: MySQL

## Modulos principales

- Autenticacion (registro/login/logout + OAuth Google/GitHub)
- Dashboard
- Productos (CRUD + imagen + categorias)
- Usuarios (CRUD)
- Roles dinamicos (CRUD)
- Permisos por modulo:
  - Por rol (`role_modules`)
  - Por usuario (`user_modules`)

## Metricas

### Modulo de Productos (historico)

| Metrica | Valor |
|---|---|
| Fecha de implementacion base | 23-feb-2026 |
| Tiempo total estimado | ~4 horas |
| Iteraciones principales | 7 |
| Caracteristicas implementadas | 12+ |
| Archivos creados/modificados | 20+ |
| LOC estimadas | ~2,500+ |

### Modulo de Usuarios/Roles/Permisos (sesion 26-feb-2026)

| Metrica | Valor |
|---|---|
| Fecha | 26-feb-2026 |
| Nuevas migraciones | 3 (`000008`, `000009`, `000010`) |
| Nuevos controladores | 2 (`User`, `Role`) |
| Nuevos filtros | 2 (`RoleFilter`, `ModuleFilter`) |
| Nueva libreria de acceso | 1 (`ModuleAccess`) |
| Nuevas vistas | 6 (usuarios + roles) |
| Configuracion de menu dinamico | Completada |
| Control de acceso por ruta/modulo | Completado |

## Instalacion rapida

1. Clonar e instalar dependencias:

```bash
git clone https://github.com/tuusuario/ci4withia.git
cd ci4withia
composer install
```

2. Configurar entorno:

```bash
cp env .env
```

3. Ajustar `.env` (baseURL y conexion DB), luego ejecutar:

```bash
php spark migrate
php spark db:seed UserSeeder
php spark serve --port=8082
```

4. Abrir: `http://localhost:8082`

## Credenciales demo

- Usuario: `admin@example.com`
- Contrasena: `password123`

## Rutas clave

- Auth: `/login`, `/register`, `/logout`
- Dashboard: `/dashboard`
- Productos: `/products`
- Usuarios: `/users`
- Roles: `/roles`

## Esquema de permisos

- `users.role`: rol asignado al usuario.
- `roles`: catalogo dinamico de roles.
- `role_modules`: modulos permitidos por rol.
- `user_modules`: visibilidad final por usuario (override sobre su rol).
- Filtro `module:*`: protege rutas segun permisos efectivos.

## Notas tecnicas

- `setAutoRoute(false)` habilitado para evitar bypass de filtros.
- Menu lateral renderizado dinamicamente segun modulos visibles del usuario.
- En movil, tabla de productos ajustada para mantenerse dentro del contenedor.

## Licencia

MIT.
