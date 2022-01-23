# WeasyPrint experiments

* <https://github.com/xpublisher/weasyprint-rest>
* <https://weasyprint.org/>

```sh
docker-compose up --detach
```

Check that the `phpfpm` container can talk to the `weasyprint-rest` container:

```sh
docker-compose exec phpfpm curl http://weasyprint-rest:5000/api/v1.0/health
```

Print HTML:

```sh
docker-compose exec phpfpm curl --form 'html=@test/test.html' --form 'template=default' http://weasyprint-rest:5000/api/v1.0/print --output test/test.pdf
open test/test.pdf
```

[Skim](https://skim-app.sourceforge.io/) support automatic reload of PDF files
which is handy during development and debugging of templates.

You may have to restart the `weasyprint-rest` container after adding a new template:

```sh
docker-compose restart weasyprint-rest
```

## Symfony

```sh
composer installation
bin/console app:print:test --help
```

## Print templates

```sh
cd weasyprint-rest/
yarn install
yarn watch
```

## Deployment

```sh
docker-compose --env-file .env.docker.local -f docker-compose.server.yml up --detach
docker-compose --env-file .env.docker.local -f docker-compose.server.yml exec phpfpm composer install --no-dev --classmap-authoritative

# Build Symfony assets
docker-compose --env-file .env.docker.local -f docker-compose.server.yml run --rm node yarn --cwd=/app install
docker-compose --env-file .env.docker.local -f docker-compose.server.yml run --rm node yarn --cwd=/app build

# Build weasyprint-rest template assets
docker-compose --env-file .env.docker.local -f docker-compose.server.yml run --rm node yarn --cwd=/app/weasyprint-rest install
docker-compose --env-file .env.docker.local -f docker-compose.server.yml run --rm node yarn --cwd=/app/weasyprint-rest build
```
