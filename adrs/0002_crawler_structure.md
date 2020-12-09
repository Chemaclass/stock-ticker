# Crawler structure

## Motivation

We need an easy way to extend new crawler selectors independently form the URL; 
which might be different depending on the context of the crawled data. 

## Decision

The Company Module has a sub-module called Crawler. 
There, you can find a directory per section:

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

And inside each section you can find the selectors that you might be interested on.

## Consequences

It becomes easier to find where does a crawl-selector belongs to in order to keep an easy structure.
