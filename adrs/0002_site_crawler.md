# Site Crawler

## Motivation

We want to be able to retrieve any value from the json with all info from the head > script tags of the html.

## Decision

The `CompanyCrawler` is the responsible to orchestrate and combine all possible `SiteCrawlerInterfaces` that will fetch
all important information from different domains/websites.

The resulting from the `CompanyCrawler` is a `CrawlResult`, which creates a new `Company` object in order to full-fill
the wanted information.

In order to be able to crawl a new Website you just need to create a class which should implement
the `SiteCrawlerInterface`
and pass it to the `TickerNewsFacade::crawlStock()` method.

### Example 1: FinanceYahooSiteCrawler

The `FinanceYahooSiteCrawler` receives a list of `JsonExtractorInterfaces` from which you can retrieve any data that you
might be interested in from the `root.App.main`. You can see the structure of that json in this
example [snapshot](../data/RootAppMainJsonExample.json), or even in live viewing the source html code from any quote,
for example: view-source:https://finance.yahoo.com/quote/AMZN

There you just need to look for the string `root.App.main =` and copy-paste that JSON value in an editor. It looks like
this:

> root.App.main = {"context":{"dispatcher":{"stores":{"PageStore":{"currentPageName":"quote","currentEvent":{"eventNa...

I like to use, for example, https://jsoneditoronline.org/ because it can build a nice tree where you can search and find
easily the exact location from a specific value.

See the [example/crawl.php](../example/crawl.php) file to see a fully working example.

```php
$financeYahoo = new FinanceYahooSiteCrawler([
    'name' => new CompanyName(),
    'price' => new RegularMarketPrice(),
    'change' => new RegularMarketChange(),
    'changePercent' => new RegularMarketChangePercent(),
    'trend' => new RecommendationTrend(),
    'news' => new News(new DateTimeZone('Europe/Berlin')),
]);

$crawlResult = $facade->crawl([$financeYahoo], ['AMZN','GOOG']); 
```

### Example 2: BarronsSiteCrawler

They have all information about their news in their html, with a simple Crawling is suffient to gather all of it.

```php
$barrons = new BarronsSiteCrawler([
    'news' => new HtmlCrawler\News(new DateTimeZone('Europe/Berlin')),
]);

$crawlResult = $facade->crawl([$barrons], ['AMZN','GOOG']); 
```

## Consequences

You can easily implement your own "site crawlers" inside the `Domain/Crawler/Site` directory.
