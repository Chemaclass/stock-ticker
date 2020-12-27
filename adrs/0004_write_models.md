# Write Models

## Motivation

I want to be able to map the key-value data collected from the crawlers into some dtos.

## Decision

Create some logic to be able to extend easily the creation of any write model fromArray and back toArray.
Always having good and strict typing between the data itself. 

## Consequences

The crawled data becomes easier to type strictly, so you get all benefits from the types system.
