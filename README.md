# Finance-Yahoo

[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

## Installing dependencies

To set up the container and install the composer dependencies:

```bash
docker-compose up -d
docker-compose exec fin_yahoo composer install
```

You can go inside the docker container:

```bash
docker exec -ti -u dev fin_yahoo bash
```

## Some composer scripts

```bash
composer test-all     # run test-quality and test-unit
composer test-quality # run psalm
composer test-unit    # run phpunit

composer psalm  # run Psalm coverage
```

----------

More info about this scaffolding -> https://github.com/Chemaclass/PhpScaffolding
