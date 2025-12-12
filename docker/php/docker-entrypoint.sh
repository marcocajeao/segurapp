#!/bin/bash
set -e

# Si estamos ejecutando como root (durante el inicio), corregir permisos
if [ "$(id -u)" = "0" ]; then
    if [ -d /var/www/html ]; then
        chown -R 1000:1000 /var/www/html 2>/dev/null || true
        chmod -R 775 /var/www/html 2>/dev/null || true
    fi
    # Cambiar a usuario appuser antes de ejecutar php-fpm
    exec gosu appuser "$@"
else
    # Si ya somos appuser, ejecutar directamente
    exec "$@"
fi

