# Finance Yahoo

[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

## Example

See a full & working example for 
- [crawling](example/crawl.php)
- [notifying](example/notify.php)

```php
$facade = createFacade(
    new EmailChannel(
        $_ENV['TO_ADDRESS'],
        new Mailer(new GmailSmtpTransport(
            $_ENV['MAILER_USERNAME'],
            $_ENV['MAILER_PASSWORD']
        ))
    )
);

$result = sendNotifications($facade, [
    // You can define multiple policies for the same Ticker
    'AMZN' => new PolicyGroup([
        'high trend to buy' => static fn (Company $c): bool => $c->get('trend')->asArray()['buy'] > 25,
        'high trend to sell' => static fn (Company $c): bool => $c->get('trend')->asArray()['sell'] > 20,
    ]),
    // And combine them however you want
    'GOOG' => new PolicyGroup([
        'strongBuy higher than strongSell' => static function (Company $c): bool {
            $strongBuy = $c->get('trend')->asArray()['strongBuy'];
            $strongSell = $c->get('trend')->asArray()['strongSell'];

            return $strongBuy > $strongSell;
        },
    ]),
]);
//NotifyResult {
//  -result: [
//    "AMZN" => "high trend to buy"
//    "GOOG" => "strongBuy higher than strongSell"
//  ]
//}

```

## Set up the project

Set up the container and install the composer dependencies:

```bash
docker-compose up -d
docker-compose exec finance_yahoo composer install
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

## Roadmap

This Project is currently in progress. To be done:

- Be able to define a threshold at which you might want to get notify via email or slack, for example.  
- ...

----------

More info about this scaffolding -> https://github.com/Chemaclass/PhpScaffolding
