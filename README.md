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

## Symfony

```sh
composer installation
bin/console app:print:test --help
```
