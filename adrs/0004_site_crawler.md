# Site Crawler

## Motivation

We might want to crawl the html elements from a website, or a json within the head > script tags of the html.

## Decision

We have two different SiteCrawlers:

- The `HtmlSiteCrawler`: receives a list of `Crawler interfaces`, and with them it can easily crawl the html body using
  the `DomCrawler`.
- The `RootAppJsonCrawler`: which receives a Closure from which you can retrieve to any data that you might be
  interested in from the `root.App.main`. You can see the structure of that json in this
  example [snapshot](../data/RootAppMainJsonExample.json).

## Consequences

You might want to crawl just an individual site/url by using the `HtmlSiteCrawler`. Or if you find the correct data in
the root.App.main json, then you might want to use the `RootAppJsonCrawler` instead. Or both.
