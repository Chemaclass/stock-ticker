# EXAMPLE

In this directory you can find two different examples of how to use this package:

### Crawling

[See the working example](crawl.php)

It just crawls the website, collect the data and renders into your output terminal.

### Notifying

[See the working example](notify.php)

It crawls the data, and it sends a notification via some channels (slack, email, ...).

## Real use case

What about getting a notification every time there is news that you are interested in?

Easy. Create an infinite loop and use `FoundMoreNews` as condition for a particular Stock.

```php
$groupedPolicy = [
    'AMZN' => new PolicyGroup([new FoundMoreNews()]),
    'GOOG' => new PolicyGroup([new FoundMoreNews()]),
    // ...
];

$channels = [
    EmailChannel::class,
    SlackChannel::class,
];

while (true) {
    $symbols = implode(', ', array_keys($groupedPolicy));
    printfln('Looking for news in %s ...', $symbols);

    $result = sendNotifications($channels, $groupedPolicy);

    printNotifyResult($result);
    sleep(60);
}

```

#### Working with the example scripts

In order to make the example scripts work, you need to create a `.env` file as:

- `cp example/.env.dist example/.env`
