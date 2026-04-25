# dh-portero-php

Librería PHP para comunicación entre servicios de DiarioHilario.

## Stack

- PHP 8.2+
- Symfony Contracts (HttpClient + Cache)

## Estructura

```
src/
└── UserClient.php    # Cliente HTTP con caché para consultar usuarios por UUID
```

## Cómo funciona

`UserClient::getUserName(string $uuid): ?string`

- Llama a `GET /user/{uuid}/name` en la API de dhcore (`dhcore_symfony_x1`)
- Cachea el resultado 1 hora (60 segundos si hay error)
- Devuelve `null` si el usuario no existe o el servicio no responde

## Proyectos que usan esta librería

- **billboard_symfony** — incluye `createdByName` en el endpoint `/movies/latest`

## Endpoint requerido en dhcore

Controlador: `src/Controller/UserByUuidGetController.php`
Ruta: `GET /user/{uuid}/name`
Auth: no requerida

## Instalación local

Montado como volumen Docker en billboard_symfony (`/dh-portero-php`) y referenciado como path repository en `composer.json`.

## Publicación

Repo GitHub: https://github.com/cointreau17/dh-portero-php
