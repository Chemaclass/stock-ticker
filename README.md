# Finance Yahoo

[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/FinanceYahoo/?branch=master)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

## Example

See full & working example [here](example/console).

```php
$facade = new FinanceYahooFacade(
    new FinanceYahooConfig('["AMZN"]'),
    new CompanyCrawlerFactory(HttpClient::create())
);

$jsonExtractor = new RootAppJsonCrawler(
    fn (array $json): array => [
        'name' => $json['context']['dispatcher']['stores']['QuoteSummaryStore']['price']['shortName'],
        'price' => $json['context']['dispatcher']['stores']['QuoteSummaryStore']['financialData']['targetLowPrice']['fmt'],
        'recommendationTrend' => $json['context']['dispatcher']['stores']['QuoteSummaryStore']['recommendationTrend']['trend']['0'],
    ]
);

$companies = $facade->crawlStock($jsonExtractor);

//[
//  "AMZN" => Chemaclass\FinanceYahoo\ReadModel\Company {
//    -summary: array:3 [
//      "name" => "Amazon.com, Inc."
//      "price" => "3,048.00"
//      "recommendationTrend" => array:6 [
//        "period" => "0m"
//        "strongBuy" => 15
//        "buy" => 28
//        "hold" => 3
//        "sell" => 1
//        "strongSell" => 0
//      ]
//    ]
//  }
//]

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
