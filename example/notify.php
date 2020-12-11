#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\FinanceYahoo\Domain\Notifier\Channel\EmailChannel;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;

require_once __DIR__ . '/autoload.php';

print 'Sending notifications...' . PHP_EOL;

$facade = createFacade(
    new EmailChannel(
        $_ENV['TO_ADDRESS'],
        new Mailer(new GmailSmtpTransport(
            $_ENV['MAILER_USERNAME'],
            $_ENV['MAILER_PASSWORD']
        ))
    )
);

$result = sendNotifications($facade, [
    // You can define multiple policies for the same Ticker
    'AMZN' => new PolicyGroup([
        'high trend to buy' => static fn (Company $c): bool => $c->get('trend')->asArray()['buy'] > 25,
        'high trend to sell' => static fn (Company $c): bool => $c->get('trend')->asArray()['sell'] > 20,
    ]),
    // And combine them however you want
    'GOOG' => new PolicyGroup([
        'strongBuy higher than strongSell' => static function (Company $c): bool {
            $strongBuy = $c->get('trend')->asArray()['strongBuy'];
            $strongSell = $c->get('trend')->asArray()['strongSell'];

            return $strongBuy > $strongSell;
        },
    ]),
]);

dump($result);

print 'Done.' . PHP_EOL;
