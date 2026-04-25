# dh-portero-php

Cliente PHP para comunicación entre servicios de DiarioHilario.

## Instalación

```bash
composer require dh/portero-php
```

## Uso

### UserClient

Obtiene el nombre de un usuario a partir de su UUID, consultando la API de `dhcore_symfony_x1`. Los resultados se cachean 1 hora para evitar llamadas repetidas.

```php
use Dh\Portero\UserClient;

$name = $userClient->getUserName('9bc44228-6342-4f6c-893e-67b3e62c11a3');
// "cointreau17"
```

Devuelve `null` si el usuario no existe o si el servicio no está disponible.

## Configuración en Symfony

Registra el servicio en `config/services.yaml`:

```yaml
Dh\Portero\UserClient:
    arguments:
        $baseUrl: '%env(DH_CORE_API_URL)%'
```

Añade la variable de entorno en `.env`:

```dotenv
# Local:      https://api.diariohilario.local  (puerto 443 interno Docker)
# Producción: https://api.diariohilario.com
DH_CORE_API_URL=https://api.diariohilario.local
```

## Instalación en local (Docker)

La librería se monta como volumen en el contenedor:

```yaml
# docker-compose.yml
volumes:
  - /home/alberto/Proyectos/dh-portero-php:/dh-portero-php
```

Y se referencia como path repository en `composer.json`:

```json
{
    "type": "path",
    "url": "/dh-portero-php",
    "options": { "symlink": true }
}
```

## Instalación en producción (Railway)

Se usa el repositorio VCS de GitHub:

```json
{
    "type": "vcs",
    "url": "https://github.com/cointreau17/dh-portero-php"
}
```

## Endpoint requerido en dhcore

La librería llama a `GET /user/{uuid}/name` en dhcore, que devuelve:

```json
{ "uuid": "...", "name": "..." }
```
