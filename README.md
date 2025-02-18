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
"ledgerID": "1dbbfb73-7d64-47b0-809b-727360446371",
"type": "debit",
"amount": "200",
"currency": "2",
"transactionID": "3fa85f64-5717-4562-b3fc-2c963f66afa6"
}
<hr>
# GET /ledgers

/api/ledgers/?page=1
<hr>
POST:
/ledgers
{
"amount": "2",
"currency": "2",
"value": "content",
"firstName":"Ivan",
"lastName":"Ivanov"
}
<hr>
GET
/balances <br>
example:
/api/balances/8b05c363-79a2-4a12-bc5f-e2104852cb54
