# Site Crawler

## Motivation

We might want to crawl the html elements from a website, or a json within the head > script tags of the html.

## Decision

We have two different SiteCrawlers:

- The `HtmlSiteCrawler`: receives a list of `CrawlerInterfaces`, and with them it can easily crawl the html body using
  the `DomCrawler` (from `Symfony`, for example)
- The `RootAppJsonCrawler`: which receives a Closure from which you can retrieve to any data that you might be
  interested in from the `root.App.main`.

## Consequences

Crawling each individual site, and within each individual element, gets super easy. This applies to the
whole `root.App.main` json that it's just dumped on the head of the page.
