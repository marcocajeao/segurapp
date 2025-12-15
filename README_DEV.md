# SegurApp – Documentación Técnica

Este documento está orientado a desarrolladores que necesiten entender la arquitectura, el modelo de datos y cómo levantar el proyecto localmente.

---

## Stack tecnológico

Backend
- PHP 8.3
- Laravel 12
- MySQL 8.0
- Laravel Sanctum

Frontend
- React
- Bootstrap 5

Infraestructura
- Docker
- Nginx

---

## Arquitectura general

- Arquitectura multi-barrio.
- Todas las entidades principales tienen neighborhood_id.
- Autenticación stateless mediante tokens.
- Controllers livianos.
- Lógica de negocio centralizada en servicios.

Estructura clave:
- app/Services/Payments
- app/Services/Guard

---

## Modelo de entidades (ER)

Diagrama lógico (Mermaid):

erDiagram
  NEIGHBORHOODS ||--o{ USERS : has
  NEIGHBORHOODS ||--o{ BENEFICIARIES : has
  NEIGHBORHOODS ||--o{ PROPERTIES : has
  NEIGHBORHOODS ||--o{ PAYMENTS : has

  BENEFICIARIES ||--o{ PROPERTIES : owns
  PROPERTIES ||--o{ PAYMENTS : receives

  USERS }o--o{ ROLES : has

---

## Entidades principales

Neighborhood
- id
- name
- default_due_day
- active

User
- id
- neighborhood_id
- name
- email
- password

Beneficiary
- id
- neighborhood_id
- first_name
- last_name
- active

Property
- id
- neighborhood_id
- beneficiary_id
- street
- number
- qr_token
- active
- created_at

Payment
- id
- neighborhood_id
- property_id
- period (YYYY-MM-01)
- amount
- method
- status
- paid_at

---

## Servicio clave: PaymentStatusService

Responsabilidades:
- Determinar desde qué mes debe pagar una propiedad.
- Determinar hasta qué mes debe estar pago según el vencimiento.
- Verificar si todos los períodos exigibles tienen pagos aprobados.
- Definir el estado final (al día / deudor).

Toda la lógica de negocio crítica vive en este servicio.

---

## Cómo levantar el proyecto

### Requisitos
- Docker
- Docker Compose

### Levantar contenedores

Desde la raíz del proyecto:

docker compose up -d --build

Servicios:
- Backend: http://localhost:8080
- Frontend: http://localhost:3000
- MySQL: puerto 3307

---

### Setup inicial

Entrar al contenedor PHP:

docker compose exec php bash

Migrar base de datos:

php artisan migrate

Seed de roles:

php artisan db:seed --class=RolesSeeder

---

## Endpoints principales

Autenticación:
- POST /api/auth/login
- GET /api/auth/me
- POST /api/auth/logout

Guardia:
- GET /api/guard/check/{qr_token}

---

## Buenas prácticas aplicadas

- Clean Code
- SRP
- Domain Services
- Controllers delgados
- Queries basadas en rangos de fechas (Carbon)
- Diseño preparado para escalar

---

## Próximos pasos técnicos

- Integración Mercado Pago
- Dashboard administrativo
- Métricas y reportes
- Tests automatizados
