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
    new FinanceYahooFactory(HttpClient::create())
);

$siteCrawler = new RootAppJsonCrawler(
    new NameJsonExtractor(),
    new PriceJsonExtractor(),
    new RecommendationTrendJsonExtractor(),
    new NewsJsonExtractor(),
);

$companies = $facade->crawlStock($siteCrawler);
//[
//  "AMZN" => Company {
//    -summary: [
//      "name" => ExtractedFromJson {
//        -data: ["Amazon.com, Inc."]
//      }
//      "price" => ExtractedFromJson {
//        -data: ["3,048.00"]
//      }
//      "recommendationTrend" => ExtractedFromJson {
//        -data: [
//          "period" => "0m"
//          "strongBuy" => 15
//          "buy" => 28
//          "hold" => 3
//          "sell" => 1
//          "strongSell" => 0
//        ]
//      }
//      "news" => ExtractedFromJson {
//        -data: [
//          0 => "Facebook sued by FTC, 48 attorneys general alleging it operates an illegal monopoly"
//          1 => "COVID-19 pandemic has forever changed the pet products business: Chewy CEO"
//          2 => "Where to Invest $10,000 Right Now"
//          ...
//        ]
//      }
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
