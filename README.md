# 📰 Hacker News Scraper API

API en PHP que realiza *web scraping* a [Hacker News](https://news.ycombinator.com) y devuelve artículos en formato JSON. 
Incluye cacheo para evitar múltiples requests innecesarios y cuenta con tests automatizados utilizando PHPUnit.

</br>



## 🚀 Instalación

### Requisitos
- Docker

### Clona el repositorio

```bash
git clone https://github.com/Nadialagosaez/apiScrap.git
cd apiScrap
```

- src/: Contiene el código fuente de la API.

- tests/: Contiene las pruebas automatizadas.

- docker-compose.yaml: Configuración de Docker.

- composer.json: Dependencias de PHP.


### Construye y ejecuta los contenedores del Docker:

```bash
docker-compose updocker-compose up --build
```

</br>

## 🧪 Testing

### Correr los tests

Con el contenedor levantado, ejecutá:

```bash
docker-compose exec -it app php /var/www/html/vendor/bin/phpunit --bootstrap /var/www/html/bootstrap.php
```
</br>

## 🔍 Endpoints

Obtener artículos (paginado)

/1 → Trae la primera página de artículos (30 noticias)

/2 → Trae las primeras 2 páginas (60 noticias)

... hasta el número deseado



Ejemplo:
```bash
curl http://localhost:3000/2 | jq
```

Respuesta:
```bash
[
  {
    "title": "Some article",
    "points": "124",
    "sent_by": "someuser",
    "published": "2 hours ago",
    "comments": "45 comments"
  },
  ...
]
```

</br>

## 💾 Cacheo

Los resultados se almacenan temporalmente en un archivo local (/tmp/cache.json) para evitar múltiples llamadas a Hacker News si los datos ya fueron consultados recientemente.
El tiempo de cacheo está definido por TIMECACHE = 100000 (en segundos).
Si el archivo cache existe y no ha expirado, se usa directamente.

</br>

## 🛠️ Tecnologías usadas

- PHP 8.2
- DOMDocument / XPath
- Docker
- PHPUnit


