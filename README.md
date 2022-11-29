# Stock Ticker

[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/stock-ticker/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/stock-ticker/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/stock-ticker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/stock-ticker/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/stock-ticker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/stock-ticker/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

This is an API to get a notification (via email and/or slack) with the latest news related to the 
Stock Symbol that you are interested in based on a personal lookup that you can define yourself.

### Set up the project

#### Define your env variables
```bash
cp .env.dist .env
```

#### Install dependencies
```bash
composer install
# Or using docker
docker-compose up -d
docker-compose exec stock_ticker composer install
```

#### Execute the commands

```bash
php bin/console crawl DIS TSLA
# Or using docker
docker exec -ti -u dev stock_ticker php bin/console crawl DIS TSLA
```

### Commands

- [Crawl](src/StockTicker/Infrastructure/Command/CrawlCommand.php): It crawls multiple websites and group their info per stock.
  - `php bin/console crawl DIS TSLA --maxNews=8`
  - Options
    - `maxNews`: Max number of news to fetch per crawled site

- [Notify](src/StockTicker/Infrastructure/Command/NotifyCommand.php): It crawls and notifies via different channels.
  - `php bin/console notify DIS TSLA --maxNews=5 --channels=email,slack --sleepingTime=10`
  - Options
    - `maxNews`: Max number of news to fetch per crawled site
    - `maxRepetitions`: Max number repetitions for the loop
    - `channels`: Channels to notify separated by a comma. For example `email` and `slack`
    - `sleepingTime`: Sleeping time in seconds
