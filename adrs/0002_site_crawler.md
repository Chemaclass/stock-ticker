# Site Crawler

## Motivation

We want to be able to retrieve any value from the json with all info from the head > script tags of the html.

## Decision

The `RootAppJsonCrawler`: which receives a Closure from which you can retrieve any data that you might be interested in
from the `root.App.main`. You can see the structure of that json in this
example [snapshot](../data/RootAppMainJsonExample.json).

## Consequences

You can easily extract all interesting data from the `root.App.main` using the `RootAppJsonCrawler`.
