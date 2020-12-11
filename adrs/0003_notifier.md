# Notifier

## Motivation

I want to settle-up some threshold/policy conditions based on the crawled data, and send some custom notifications
according to these conditions.

## Decision

Create a new Module called `Notifier` which will be responsible for

- Defining some already prepared channels (such as Email or Slack)
- Create a `PolicyGroup` logic in which you are able to define a group of policy conditions per Ticker

See the [example/notify.php](../example/notify.php) file to see a fully working example.

```php
$policy = new NotifierPolicy([
    'AMZN' => new PolicyGroup([
        'high trend to buy' => fn (Company $c): bool => $c->info('trend')->get('buy') > 25,
    ])
]);

$facade = new FinanceYahooFacade(
    new FinanceYahooFactory(
        HttpClient::create(),
        new EmailChannel(/**/),
        new SlackChannel(/**/),
        // ... Actually any list of ChannelInterface
    )
);

$crawlResult = $facade->crawl([SiteCrawlerInterface], ['AMZN','GOOG']);
// And after crawling the site/s we just need to pass the policy with the crawled result 
$notifyResult = $facade->notify($policy, $crawlResult);
```

## Consequences

You can define multiple policies for the same Ticker
