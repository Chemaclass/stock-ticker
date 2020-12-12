# Finance Yahoo

[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

This is an API to get a report/notification (via email and/or slack) of the latest news (from finance.yahoo.com) related
to the Tinker (Stock Symbol) that you are interested in based on a personal lookup that you can define yourself
independently of each Tinker.

## Example

See a full & working example for 
- Just [Crawling](example/crawl.php)
- Crawling + [Notifying](example/notify.php)

```php
$facade = createFacade(
    createEmailChannel(),
    createSlackChannel(),
);

$result = sendNotifications($facade, [
    // You can define multiple policies for the same Ticker
    // As a function or a callable class
    'AMZN' => new PolicyGroup([
        'high trend to buy' => fn (Company $c): bool => $c->info('trend')->get('0')['buy'] > 25,
        'buy higher than sell' => new BuyIsHigherThanSell(),
    ]),
    // And combine them however you want
    'GOOG' => new PolicyGroup([
        'strongBuy higher than strongSell' => function (Company $c): bool {
            $strongBuy = $c->info('trend')->get('0')['strongBuy'];
            $strongSell = $c->info('trend')->get('0')['strongSell'];

            return $strongBuy > $strongSell;
        },
    ]),
]);
//[
//  "AMZN" => [
//    0 => "high trend to buy"
//    1 => "buy higher than sell"
//  ]
//  "GOOG" => [
//    0 => "strongBuy higher than strongSell"
//  ]
//]


```

## Set up the project

Set up the container and install the composer dependencies:

```bash
docker-compose up -d
docker-compose exec finance_yahoo composer install
docker-compose exec finance_yahoo example/crawl.php
```

You can go even go inside the docker container:

```bash
docker exec -ti -u dev finance_yahoo bash
```

### Some composer scripts

```bash
composer test-all     # run test-quality and test-unit
composer test-quality # run psalm
composer test-unit    # run phpunit

composer psalm  # run Psalm coverage
```

## Substantial changes

Substantial changes are architecture decisions, documentation restructuring, breaking changes, etc. Not Bug Reports, Bug
Fixes, Tests, etc.

### How to contribute a substantial change

In order to make a substantial change it is a good practice to discuss the idea before implementing it.

- An [ADR](https://github.com/joelparkerhenderson/architecture_decision_record) can be proposed with an issue.
- The issue is the place to discuss everything.
- The result of the issue can be an ADR file (under the [adrs](./adrs) directory), but also just as CS Fixer rule to
  check then during CI.

----------

More info about this scaffolding -> https://github.com/Chemaclass/PhpScaffolding
