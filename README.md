# symfonyAPI

#docker install <br/>
sudo apt update -y<br/>
sudo apt install -y docker.io<br/>
sudo systemctl enable docker <br/>
sudo systemctl start docker <br/>
sudo usermod -aG docker $USER <br/>

sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose <br/>
<br/>sudo chmod +x /usr/local/bin/docker-compose <br/>

sudo docker-compose up --build

<hr>

php bin/console doctrine:migrations:generate <br/>
php bin/console doctrine:database:create --env=test < br/>
<br/>
php bin/console doctrine:migrations:migrate --env=test <br/>
<br/>
php bin/console doctrine:schema:update --force --env=test <br/>
php bin/console doctrine:migrations:status --env=test <br/>

Example Unittests <br/>
php bin/phpunit --filter testCreateTransactionSuccess

<hr>
example API:<br>
POST /api/transaction<br/> 

<br/>
{
  "ledgerID": "84188d62-da2a-4335-bc40-7194242510d7",
  "type": "credit",
  "amount": "100",
  "currency": "3",
  "transactionID": "3fa85f64-5717-4562-b3fc-test"
}
<hr>
# GET /ledgers

/api/ledgers/?page=1
<hr>
POST:
/ledgers
{
"amount": "1.00",
"currency": "2",
"value": "content",
"firstName":"Ivan",
"lastName":"Ivanov"
}
<hr>
GET
/balances <br>
example:
/api/balances/84188d62-da2a-4335-bc40-7194242510d7

<hr>
for Unit testing run: <br>
php bin/phpunit <br>
OR< br/>

/bin/php /var/www/symfonyAPI/vendor/phpunit/phpunit/phpunit --no-configuration --filter App\\Tests\\Entity\\LedgersTest --test-suffix LedgersTest.php /var/www/symfonyAPI/tests/Entity --teamcity --cache-result-file=/var/www/symfonyAPI/.phpunit.result.cache
