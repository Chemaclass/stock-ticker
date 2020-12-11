# Finance Yahoo

[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

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
    'AMZN' => new PolicyGroup([
        'high trend to buy' => fn (Company $c): bool => $c->info('trend')->get('buy') > 25,
        'some trend to sell' => fn (Company $c): bool => $c->info('trend')->get('sell') > 0,
    ]),
    // And combine them however you want
    'GOOG' => new PolicyGroup([
        'strongBuy higher than strongSell' => function (Company $c): bool {
            $strongBuy = $c->info('trend')->get('strongBuy');
            $strongSell = $c->info('trend')->get('strongSell');

            return $strongBuy > $strongSell;
        },
    ]),
]);
//[
//  "AMZN" => [
//    0 => "high trend to buy"
//    1 => "some trend to sell"
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

----------

More info about this scaffolding -> https://github.com/Chemaclass/PhpScaffolding
