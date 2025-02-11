# symfonyAPI

<hr>
example:<br>
POST /transaction<br/> 

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

api/ledgers/?page=1
<hr>
POST:
/ledgers

{
"amount": "2",
"currency": "2",
"value": "content",
"firstName":"Sergiy",
"lastName":"Chaialovskiy"
}
<hr>
GET
/balances <br>
example:
balances/8b05c363-79a2-4a12-bc5f-e2104852cb54
