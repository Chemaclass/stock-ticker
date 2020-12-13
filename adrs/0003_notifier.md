# Notifier

## Motivation

I want to set up a personal lookup (policy conditions) based on the crawled data, and send custom notifications
according to them.

## Decision

Created a new Module called `Notifier` which will be responsible for:

- Defining some already prepared channels (such as Email and Slack)
- Create a `PolicyGroup` logic in which you are able to define a group of conditions per Ticker

See the [example/notify.php](../example/notify.php) file to see a fully working example.

```php
$policy = new NotifierPolicy([
    'AMZN' => new PolicyGroup([
        'high trend to buy' => fn (Company $c): bool => $c->info('trend')['buy'] > 25,
    ])
]);

$facade = new TickerNewsFacade(
    new TickerNewsFactory(
        HttpClient::create(),
        new EmailChannel(/**/),
        new SlackChannel(/**/),
        // ... Any list of ChannelInterface
    )
);

$crawlResult = $facade->crawl([SiteCrawlerInterface], ['AMZN','GOOG']);
// And after crawling the site/s we just need to pass the policy with the crawled result 
$notifyResult = $facade->notify($policy, $crawlResult);
```

## Consequences

You can define multiple policy conditions for the same Ticker Symbol in order to trigger a notification.
