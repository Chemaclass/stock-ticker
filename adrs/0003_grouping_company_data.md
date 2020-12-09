# Grouping Company Data

## Motivation

The Company data might have been in different URLs. For example, see the
ADR [0002_crawler_structure](0002_crawler_structure.md), there is a directory per section, and that makes sense also
because each section has its own URL.

For example:

- For the summary: `https://finance.yahoo.com/quote/%s/summary`
- For the analysis: `https://finance.yahoo.com/quote/%s/analysis`
- etc

## Decision

Unify all crawled data from the different sites in the same "Company object" (independently of the URLs which was used
to collect that data), group by the Ticker Symbol.

## Consequences

The expected result of the `crawlStock()` is a one-level list of key-values.
