
set -u

echo 'Installing a StockTicker use case example ...'

projectName=${1:-StockTickerExample}
mkdir "$projectName"
cd "$projectName" || exit

composerJson='{
    "require": {
        "php": ">=7.4",
        "chemaclass/stock-ticker": "dev-master",
        "symfony/google-mailer": "^5.2",
        "vlucas/phpdotenv": "^5.2"
    },
    "minimum-stability": "dev"
}'
echo "$composerJson" > composer.json

curl -s https://raw.githubusercontent.com/Chemaclass/StockTicker/master/docker-compose.yml > docker-compose.yml
mkdir -p devops/dev
curl -s https://raw.githubusercontent.com/Chemaclass/StockTicker/master/devops/dev/php.dockerfile > devops/dev/php.dockerfile

sed -i '' 's/stock_ticker/stock_ticker_example/g' docker-compose.yml

docker-compose up --build --remove-orphans -d
docker-compose exec -u dev stock_ticker_example composer install

cp -r ./vendor/chemaclass/stock-ticker/example .
cp example/.env.dist example/.env

# Executing the crawling script to ensure everything was fine
docker-compose exec -u dev stock_ticker_example example/crawl.php
echo 'Installation successfully. Do not forget to fill the .env file!'
