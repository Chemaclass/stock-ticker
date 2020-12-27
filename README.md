# StockTicker

[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/StockTicker/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/StockTicker/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/StockTicker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/StockTicker/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/StockTicker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/StockTicker/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

This is an API to get a notification (via email and/or slack) with the latest news related to the 
Stock Symbol that you are interested in based on a personal lookup that you can define yourself.

## Commands

- [Crawl](src/StockTicker/Infrastructure/Command/CrawlCommand.php): Crawl multiple websites and group their info per stock. 
  - `php bin/console crawl DIS TSLA --maxNews=8 `
- [Notify](src/StockTicker/Infrastructure/Command/NotifyCommand.php): Crawl and notify via different channels according to your criteria.
  - `php bin/console notify DIS TSLA --maxNews=5 --sleepingTime=10`

## Contribute

### Set up the project

Set up the container and install the composer dependencies:

```bash
docker-compose up -d
docker-compose exec stock_ticker composer install
```

You can go even go inside the docker container:

```bash
docker exec -ti -u dev stock_ticker bash
```

----------

More info about this scaffolding -> https://github.com/Chemaclass/PhpScaffolding
