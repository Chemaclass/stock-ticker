
set -u

echo 'Installing TickerNews use case example ...'

composerJson='{
    "name": "your-username/ticker-news-use-case-example",
    "require": {
        "php": ">=7.4",
        "chemaclass/ticker-news": "dev-master",
        "symfony/google-mailer": "^5.2",
        "vlucas/phpdotenv": "^5.2"
    },
    "minimum-stability": "dev"
}'
echo "$composerJson" > composer.json

curl -s https://raw.githubusercontent.com/Chemaclass/TickerNews/master/docker-compose.yml > docker-compose.yml
mkdir -p devops/dev
curl -s https://raw.githubusercontent.com/Chemaclass/TickerNews/master/devops/dev/php.dockerfile > devops/dev/php.dockerfile

sed -i '' 's/ticker_news/example_ticker_news/g' docker-compose.yml

docker-compose up --build --remove-orphans -d
docker-compose exec -u dev example_ticker_news composer install

cp -r ./vendor/chemaclass/ticker-news/example .
cp example/.env.dist example/.env

# Executing the crawling script to ensure everything was fine
docker-compose exec -u dev example_ticker_news example/crawl.php
echo 'Installation successfully. Do not forget to fill the .env file!'
