# Crawler structure

## Motivation

We need an easy way to extend new crawler selectors independently form the URL; which might be different depending on
the context of the crawled data.

## Decision

The `Crawler` Module has a sub-module called `HtmlCrawler`. 
That directory is the place where you would find: 

- Summary
- Company Outlook
- Chart
- Conversations
- Statistics
- Historical Data
- Profile
- Financials
- Analysis
- Options
- Holders
- Sustainability

Inside each section you can find the selectors that you might be interested on.

## Consequences

It becomes easier to find where does a crawl-selector belongs to in order to keep an easy structure.
