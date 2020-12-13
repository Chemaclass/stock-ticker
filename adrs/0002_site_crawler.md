# Site Crawler

## Motivation

We want to be able to retrieve any value from the json with all info from the head > script tags of the html.

## Decision

The `RootJsonSiteCrawler`: which receives a list of `JsonExtractorInterfaces` from which you can retrieve any data that
you might be interested in from the `root.App.main`. You can see the structure of that json in this
example [snapshot](../data/RootAppMainJsonExample.json), or even in live viewing the source html code from any quote,
for example: view-source:https://finance.yahoo.com/quote/AMZN

There you just need to look for the string `root.App.main =` and copy-paste that JSON value in an editor. It looks like
this:

> root.App.main = {"context":{"dispatcher":{"stores":{"PageStore":{"currentPageName":"quote","currentEvent":{"eventNa...

I like to use, for example, https://jsoneditoronline.org/ because it can build a nice tree where you can search and find
easily the exact location from a specific value.

See the [example/crawl.php](../example/crawl.php) file to see a fully working example.

```php
$siteCrawler = new RootJsonSiteCrawler([
    'name' => new CompanyName(),
    'price' => new RegularMarketPrice(),
    'change' => new RegularMarketChange(),
    'changePercent' => new RegularMarketChangePercent(),
    'trend' => new RecommendationTrend(),
    'news' => new News(new DateTimeZone('Europe/Berlin'), 3),
]);

$crawlResult = $TickerNewsFacade->crawl([$siteCrawler], ['AMZN','GOOG']); 
```

## Consequences

You can easily extract all interesting data from the `root.App.main`.
