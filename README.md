# SegurApp – Gestión de Pagos de Seguridad Barrial

SegurApp es un sistema web diseñado para administrar y controlar los pagos de seguridad de barrios cerrados o zonas organizadas.
Permite validar en tiempo real si una vivienda tiene el servicio activo mediante un QR fijo en la puerta, escaneado por el personal de guardia.

El proyecto está pensado para ser simple, claro y escalable, evitando complejidades innecesarias en la gestión de pagos mensuales.

---

## ¿Qué problema resuelve?

En muchos barrios:
- No hay certeza clara de quién pagó y quién no.
- El control se hace de forma manual.
- El guardia no tiene una herramienta objetiva para validar el servicio.

SegurApp centraliza toda esta información y la vuelve accesible en segundos.

---

## Conceptos principales

### Barrio
Unidad principal del sistema.
Cada barrio:
- Tiene su propio administrador.
- Define su día de vencimiento.
- Utiliza su propia cuenta de Mercado Pago.

### Beneficiario (Vecino)
Persona que recibe el servicio de seguridad.
Puede tener una o más propiedades dentro del barrio.

### Propiedad
Es lo que el guardia valida.
Cada propiedad:
- Tiene un QR único y estático.
- Está asociada a un beneficiario.
- Tiene una fecha de alta.

### Pago
Representa un pago mensual del servicio.
- Asociado a una propiedad.
- Identificado por un período mensual.
- Puede ser manual o automático.

---

## Lógica de pagos

- No existen cuotas generadas automáticamente.
- Los meses son implícitos (calendario).
- Desde el mes de alta de la propiedad, todos los meses deben estar pagos.
- El vencimiento es configurable (por defecto día 5).
- A partir del día siguiente al vencimiento:
  - Si el mes no está pago, la propiedad es deudora.

---

## Flujo del guardia (QR)

1. El guardia escanea el QR de la vivienda.
2. Ingresa al sistema con su usuario.
3. El sistema devuelve el estado del servicio:
   - PAGADO
   - DEUDOR
   - NO BENEFICIARIO

El guardia no ve montos ni historial, solo el estado.

---

## Estado actual

- Sistema funcional.
- Flujo de guardia operativo.
- Lógica de pagos validada.
- Listo para continuar con integraciones y reportes.
