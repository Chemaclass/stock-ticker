# EXAMPLE

In this directory you can find three different examples of how to use this api:

### Crawling

[See the working example](crawl.php)

It just crawls the website, collect the data and renders into your output terminal.

### Notifying

It crawls the data, and it sends a notification via two channels (slack + email).

### Looping

[See the working example](notify.php)

It shows how can you combine these two (crawling + notifying) in an infinite loop.

## Real use case

What about getting a notification everytime there are news that are new for you?

Easy. Create an infinite loop and use `FoundMoreNews` as Policy Condition for a particular Stock:

```php
$channels = [
    createEmailChannel(),
    createSlackChannel(),
];

$groupedPolicy = [
    'AMZN' => new PolicyGroup([new FoundMoreNews()]),
    'GOOG' => new PolicyGroup([new FoundMoreNews()]),
    // ...
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

More info about it in the example's [readme](example/README.md).
