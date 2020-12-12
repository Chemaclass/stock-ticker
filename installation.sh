
set -u

echo 'Installing the Finance Yahoo use case example ...'

composerJson='{
    "name": "your-username/finance-yahoo-use-case-example",
    "require": {
        "php": ">=7.4",
        "chemaclass/finance-yahoo": "dev-master",
        "symfony/google-mailer": "^5.2",
        "vlucas/phpdotenv": "^5.2"
    },
    "minimum-stability": "dev"
}'
echo "$composerJson" > composer.json

curl -s https://raw.githubusercontent.com/Chemaclass/FinanceYahoo/master/docker-compose.yml > docker-compose.yml
mkdir -p devops/dev
curl -s https://raw.githubusercontent.com/Chemaclass/FinanceYahoo/master/devops/dev/php.dockerfile > devops/dev/php.dockerfile

sed -i '' 's/finance_yahoo/example_finance_yahoo/g' docker-compose.yml

docker-compose up --build --remove-orphans -d
docker-compose exec -u dev example_finance_yahoo composer install

cp -r ./vendor/chemaclass/finance-yahoo/example .
cp example/.env.dist example/.env

# Executing the crawling script to ensure everything was fine
docker-compose exec -u dev example_finance_yahoo example/crawl.php
echo 'Installation successfully'
echo 'Do not forget to fill the .env file!'
