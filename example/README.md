# EXAMPLE

In this directory you can find three different examples of how to use this api:

### Crawling

[See the working example](crawl.php)

It just crawls the website, collect the data and renders into your output terminal.

### Notifying

[See the working example](notify.php)

It crawls the data, and it sends a notification via two channels (slack + email).

### Looping

[See the working example](loop-notify.php)

It shows how can you combine these two (crawling + notifying) in an infinite loop, in order to decide (via custom policy
condition) when to trigger a notification.

## Real use case

What about getting a notification everytime there are news that are new for you?

Easy. Create an infinite loop and use `FoundMoreNews` as Policy Condition for a particular Stock:

```php
$facade = createFacade(
    createEmailChannel(),
    createSlackChannel(),
);

while (true) {
    $result = sendNotifications($facade, [
        'AMZN' => new PolicyGroup([new FoundMoreNews()]),
        'GOOG' => new PolicyGroup([new FoundMoreNews()]),
        // ...
    ]);
    sleep(60);
}
```

#### Working with the example scripts

In order to make the example scripts work, you need to create a `.env` file as:

- `cp example/.env.dist example/.env`

More info about it in the example's [readme](example/README.md).
